<?php

namespace App\Http\Controllers;

use App\Library\Repository\OrganizationServiceInterface;
use App\Library\Repository\SSOServiceInterface;
use App\Traits\JResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Laravel\Lumen\Routing\Controller as BaseController;

class OrganizationController extends BaseController
{
    use JResponse;

    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Display the specified resource.
     *
     * @param $id
     * @param OrganizationServiceInterface $organizationService
     * @return JsonResponse
     */
    public function show($id, OrganizationServiceInterface $organizationService)
    {
        $data = $organizationService->find($id);
        return $this->json(200,'',$data['data']);
    }

    public function updateUserOrg(Request $request, SSOServiceInterface $SSOService, OrganizationServiceInterface $organizationService)
    {
        $result = [];
        $validator = Validator::make($request->all(), [
            'org_id' => ['required', 'integer'],
        ]);
        if ($validator->fails()) {
            $result = validateWith(
                $validator, $request
            );
        } else {
            $mcr_org_id = $request->get('org_id');
            $data = $SSOService->update_user_org(array('mcr_org_id'=>$mcr_org_id), Auth::user()->access_token);
            if ($data['errcode'] == 200) {
                $fr = "Chargement des données de l'organisation";
                $en = "Loading the organization's data";
                $current_org = $organizationService->find($data['data']['curr_org_id'])['data'];
                return $this->json($data['errcode'], response_msg_translate($fr, $en), $current_org);
            }
            else {
                $fr = "Une erreur est survenue recharger et réessayer";
                $en = "An error occurred reload and try again";
                return $this->json($data['errcode'], response_msg_translate($fr, $en));
            }
        }
        return response()->json($result);
    }


    public function allOrganizationsUser(SSOServiceInterface $SSOService)
    {
        $data = $SSOService->getAllOrgsUser(Auth::user()->access_token, Auth::user()->sso_user_id)['data'];
        return $this->json(200, "", $data);
    }
}
