<?php


namespace App\Http\Controllers\Api;


use App\Jobs\TaskMailErrorJob;
use App\Library\MailService;
use App\Library\Repository\OrganizationServiceInterface;
use App\Library\Repository\SSOServiceInterface;
use App\Library\SMSAPIService;
use App\User;
use App\Utils\JResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Queue;
use Illuminate\Support\Facades\Validator;
use Laravel\Lumen\Routing\Controller as BaseController;
use Lcobucci\JWT\Parser;

class AuthApiController extends BaseController
{
    use JResponse;

    protected $sso_service = null;
    protected $smsApiService = null;
    protected $smsService = null;


    public function __construct(SSOServiceInterface $sso_service)
    {
        $this->sso_service = $sso_service;
        $this->smsApiService = new  SMSAPIService();
    }

    /**
     * Get a validator for an incoming registration request.
     *
     * @param array $data
     * @return \Illuminate\Contracts\Validation\Validator
     */
    protected function validateLogin(array $data)
    {
        return Validator::make($data, [
            'code' => ['required', 'string'],
        ]);
    }

    public function validateWith($validator, Request $request = null)
    {
        $request = $request ?: request();
        if (is_array($validator)) {
            $validator = $this->getValidationFactory()->make($request->all(), $validator);
        }
        return $validator->validate();
    }

    /** Connexion via API
     * @param Request $request
     * @param OrganizationServiceInterface $organizationService
     * @return JsonResponse
     */

    public function login(Request $request, OrganizationServiceInterface $organizationService)
    {
        $result = [];
        $validator = $this->validateLogin($request->all());
        if ($validator->fails()) {
            $result = $this->validateWith(
                $validator, $request
            );
            $fr = "Error has occurred";
            $en = "Authentication successful";
            return $this->json(500, response_msg_translate($fr, $en) , $result);
        } else {
            $secret = $request->get('code');
            $base_decode_secret = urlsafe_b64decode($secret);
            $tab_credentials = explode(':', $base_decode_secret);
            if (count($tab_credentials) == 2){
                $email = decrypter($tab_credentials[0]);
                $password = decrypter($tab_credentials[1]);
                $fr = "Authentification réussie";
                $en = "Authentication successful";
                return $this->loginResponse($email, $password, response_msg_translate($fr, $en), $secret, $organizationService);
            }else{
                $headers = $request->get('headers');
                $ip = $request->get('ip');
                $canal = "WEB";
                if (empty($headers) && empty($ip)){
                    $headers = $request->headers->all();
                    $ip = $request->ip();
                    $canal = "API";
                }
                $array = array(
                  'SERVER_PROTOCOL' => $request->server->get('SERVER_PROTOCOL'),
                  'HEADERS' => $headers,
                  'IP' => $ip,
                  'CANAL' => $canal,
                  'APPLICATION' => "MOBILE ADS"
                );
                $data = array(
                    'email' => "ronaldo.mine@nexah.net",
                    'subject' => "Tentatives de connexion ",
                    'data' => $array
                );
                $mailservice = new MailService();
                Queue::push(new TaskMailErrorJob($mailservice, $data));
                return $this->json(500, 'Erreur');
            }
        }
    }

    function loginResponse($email, $password, $message_success, $secret, OrganizationServiceInterface $organizationService)
    {
        $credentials = array('email' => $email, 'password' => $password);
        $resultSSO = $this->sso_service->authentification($credentials);
        dd($resultSSO);
        if (array_key_exists('id_token', $resultSSO) && $resultSSO['id_token'] != null) {
            $id_token = $resultSSO['id_token'];
            $access_token = $resultSSO['access_token'];
            $type_token = $resultSSO['token_type'];
            $ttl_token = $resultSSO['expires_in'];
            $parse = (new Parser())->parse((string)$id_token);
            $claims = $parse->getClaims();
            $user = User::where('sso_user_id', $claims['id'])->first();
            if ($user == null) {
                $user = User::create([
                    'sso_user_id' => $claims['id']
                ]);
            }
            $user->api_token = $claims['key'];
            $user->access_token = $access_token;
            $user->save();
            $user = infos_user(User::find($user->id), $user->access_token);
            $orgs = $claims['roles'];
            $current_org = $organizationService->find($user->curr_org_id)['data'];
            return $this->json(200, $message_success, array('user' => $user, 'current_org' => $current_org, 'orgs' => $orgs, 'secret' => $secret));
        } else {
            $fr = "Les informations de connexion sont érronées";
            $en = "Login information is incorrect";
            return $this->json(203, response_msg_translate($fr, $en));
        }
    }


    /**
     * @param Request $request
     * @param OrganizationServiceInterface $organizationService
     * @param SSOServiceInterface $SSOService
     * @return JsonResponse
     */

    public function check_key(Request $request, OrganizationServiceInterface $organizationService, SSOServiceInterface $SSOService)
    {
        $api_token = $request->get('api_token');
        $lang = $request->get('lang');
        if (!empty($api_token)) {
            $user = User::where('api_token', $api_token)->first();
            if ($user) {
                $user = infos_user(User::find($user->id), $user->access_token);
                $have_access = $SSOService->rolesUserInApp($user->access_token, $user->sso_user_id, $user->curr_org_id);
                if ($have_access != null && $have_access['errcode'] == 200 && count($have_access['data']) > 0){
                    if (!empty($lang)) {
                        $SSOService->update_lang($lang, $user->access_token);
                    }
                    $fr = "Utilisateur authentifié";
                    $en = "Authenticated user";
                    $current_org = $organizationService->find($user->curr_org_id)['data'];
                    return $this->json(200, response_msg_translate($fr, $en), array('user' => $user, 'current_org' => $current_org));
                }else{
                    $fr = "Vous n'êtes pas autorisés à accéder à cette ressourse";
                    $en = "You are not authorized to access this resource.";
                    return $this->json(403, response_msg_translate($fr, $en));
                }
            } else {
                $fr = "Votre session a expiré, vous devez vous reconnecter";
                $en = "Your session has expired, you need to log in again.";
                return $this->json(404, response_msg_translate($fr, $en));
            }
        } else {
            $fr = "Vous n'êtes pas authentifié";
            $en = "You are not authenticated.";
            return $this->json(401, response_msg_translate($fr, $en));
        }

    }

    public function logout(SSOServiceInterface $SSOService)
    {
        $result = $SSOService->logout();
        $fr = "Déconnexion de l'utilisateur";
        $en = "User Logout";
        return $this->json(200, response_msg_translate($fr, $en), $result);
    }

}
