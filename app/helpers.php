<?php

/*
 * Global helpers file with misc functions.
 */

use App\Library\SSOService;
use App\Library\UrlShortenerService;
use App\Models\Credentials;
use App\Models\Setting;
use Carbon\Carbon;
use Illuminate\Contracts\Container\BindingResolutionException;
use Illuminate\Contracts\Validation\Factory;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

if (!function_exists('response_msg_translate')) {
    /**
     * Helper to grab the application name.
     *
     * @param $fr
     * @param $en
     * @return mixed
     */
    function response_msg_translate($fr, $en)
    {
        return array(
            'fr' => $fr,
            'en' => $en
        );
    }
}
if (!function_exists('short_message')) {
    /**
     * Helper to grab the application name.
     *
     * @param array $array_key
     * @param array $array_values
     * @param $message
     * @param $task_name
     * @return mixed
     */
    function short_message(array $array_key, array $array_values, $message, $task_name = null)
    {
        $message_original = str_replace($array_key, $array_values, $message);
        $msg_length = 160 - strlen($message_original);
        if ($task_name) {
            $short_task_name = cut_string($task_name, $msg_length);
            $array_values[count($array_values) - 1] = $short_task_name;
        }
        return str_replace($array_key, $array_values, $message);
    }
}

function short_link($link)
{
    $urlShortener = new UrlShortenerService();
    return $urlShortener->createLink($link)['data']['short_url'];
}

if (!function_exists('cut_string')) {
    /**
     * Helper to grab the application name.
     *
     * @param $text
     * @param $max
     * @return mixed
     */
    function cut_string($text, $max)
    {
        if (strlen($text) <= $max)
            return $text;
        return substr($text, 0, $max - 3) . '...';
    }
}

if (!function_exists('crypter')) {

    /** Crypter un texte
     * @param $Texte
     * @return mixed
     */

    function crypter($Texte)
    {
        srand((double)microtime() * 1000000);
        $CleDEncryptage = md5(rand(0, 32000));
        $Compteur = 0;
        $VariableTemp = "";
        for ($Ctr = 0; $Ctr < strlen($Texte); $Ctr++) {
            if ($Compteur == strlen($CleDEncryptage))
                $Compteur = 0;
            $VariableTemp .= substr($CleDEncryptage, $Compteur, 1) . (substr($Texte, $Ctr, 1) ^ substr($CleDEncryptage, $Compteur, 1));
            $Compteur++;
        }
        return urlsafe_b64encode(generation_cle($VariableTemp, get_gen_key()));
    }

    function urlsafe_b64encode($string)
    {
        $data = base64_encode($string);
        $data = str_replace(array('+', '/', '='), array('&', '_', '-'), $data);
        return $data;
    }

}

if (!function_exists('decrypter')) {

    /** Décrypter un texte
     * @param $Texte
     * @return string
     */
    function decrypter($Texte)
    {
        $Texte = generation_cle(urlsafe_b64decode($Texte), get_gen_key());
        $VariableTemp = "";
        for ($Ctr = 0; $Ctr < strlen($Texte); $Ctr++) {
            $md5 = substr($Texte, $Ctr, 1);
            $Ctr++;
            $VariableTemp .= (substr($Texte, $Ctr, 1) ^ $md5);
        }
        return $VariableTemp;
        //return urlsafe_b64decode($Texte);
    }

    function urlsafe_b64decode($string)
    {
        $data = str_replace(array('&', '_', '-'), array('+', '/', '='), $string);
        $mod4 = strlen($data) % 4;
        if ($mod4) {
            $data .= substr('====', $mod4);
        }
        return base64_decode($data);
    }
}

if (!function_exists('generation_cle')) {

    /** Générer la clé la clé de cryptage et de décryptage
     * @param $Texte
     * @param $CleDEncryptage
     * @return string
     */
    function generation_cle($Texte, $CleDEncryptage)
    {
        $CleDEncryptage = md5($CleDEncryptage);
        $Compteur = 0;
        $VariableTemp = "";
        for ($Ctr = 0; $Ctr < strlen($Texte); $Ctr++) {
            if ($Compteur == strlen($CleDEncryptage))
                $Compteur = 0;
            $VariableTemp .= substr($Texte, $Ctr, 1) ^ substr($CleDEncryptage, $Compteur, 1);
            $Compteur++;
        }
        return $VariableTemp;
    }

}

if (!function_exists('get_gen_key')) {

    /** Retiré le token en session
     * @return mixed
     */
    function get_gen_key()
    {
        return md5(Setting::first()->gen_key);
    }

}

if (!function_exists('generate_target')) {

    /** Générer une cible unique
     * @param $id
     * @param int $nbre
     * @return string
     */
    function generate_target($id, $nbre = 2)
    {
        $str = Str::random($nbre);
        return str_replace('-', '', str_shuffle($id . $str));
    }

}


if (!function_exists('validateWith')) {

    /** Générer une cible unique
     * @param $validator
     * @param Request|null $request
     * @return array|Exception|ValidationException
     */
    function validateWith($validator, Request $request = null)
    {
        try {
            $request = $request ?: request();
            if (is_array($validator)) {
                $validator = getValidationFactory()->make($request->all(), $validator);
            }
            return $validator->validate();
        } catch (ValidationException $e) {
            return $e;
        }
    }

    /**
     * Get a validation factory instance.
     *
     * @return Factory
     */
    function getValidationFactory()
    {
        return app(Factory::class);
    }
}
if (!function_exists('get_users')) {
    /**
     * Helper to grab the application name.
     *
     * @param array $users
     * @return mixed
     */
    function get_users($users)
    {
        $results = array();
        if (!empty($users)) {
            foreach ($users as $user) {
                $current_user = infos_user($user, Auth::user()->access_token);
                if ($current_user) {
                    array_push($results, $current_user);
                }
            }
        }
        return $results;
    }
}
if (!function_exists('infos_user')) {
    /**
     * Helper to grab the application name.
     *
     * @param $user
     * @param $access_token
     * @return mixed
     */
    function infos_user($user, $access_token)
    {
        $SSOService = new SSOService();
        $data = $SSOService->find_by_user_id($user->sso_user_id,$access_token);
        if ($data != null && $data['errcode'] == 200){
            $current_user = (Object) $data['data']['user'];
            $current_user->roles = implode(',',$data['data']['roles']);
            $current_user->api_token = $user->api_token;
            $current_user->access_token = $user->access_token;
            $current_user->tel = $current_user->telephone;
            $current_user->sso_user_id = $current_user->id;
            $current_user->id = $user->id;
            return $current_user;
        }
        return null;
    }
}
if (!function_exists('credentials')) {
    /**
     * Get a an encrypted value.
     *
     * @param string $key
     * @param null $default
     * @return mixed
     */
    function credentials(string $key, $default = null)
    {
        $filename = app()->basePath('config') . ('credentials.php.enc' ? DIRECTORY_SEPARATOR . 'credentials.php.enc' : 'credentials.php.enc');
        try {
            $credentials = app(Credentials::class);
            $credentials->load($filename);
            return $credentials->get($key, $default);
        } catch (ReflectionException | BindingResolutionException $e) {
            return Credentials::CONFIG_PREFIX . $key;
        }

    }
}

if (!function_exists('default_org_id')) {

    /** Rediriger vers ToDo à la connexion
     * @return string
     */
    function default_org_id()
    {
        return 7;
    }
}

if (!function_exists('public_path')) {
    /**
     * Get the path to the public folder.
     *
     * @param string $path
     * @return string
     */
    function public_path($path = '')
    {
        return env('PUBLIC_PATH', base_path('storage/app')) . ($path ? '/' . $path : $path);
    }
}

if (!function_exists('date_to_utc')) {
    /**
     * Get the path to the public folder.
     *
     * @param string $date
     * @return mixed
     */
    function date_to_utc($date)
    {
        return date("Y-m-d H:i:s", strtotime('-1 hour', strtotime($date)));
    }
}
if (! function_exists('statusAppointment')) {
    /**
     * Retourne les tâches en cours d'un compartiment
     *
     * @param $appointment
     */
    function statusAppointment($appointment)
    {
        $now = Carbon::parse(Carbon::now()->format("Y-m-d"));
        $end = Carbon::parse($appointment->rdv_date)->format("Y-m-d");
        $diff = $now->diffInDays($end,false);
        if ($appointment->comment != null) {
            return 1;
        }
        else if ($appointment->comment == null && $diff >= 0){
            return 2;
        }
        return 3;
    }
}

if (!function_exists('paginate_array')) {

    /** Paginer un tableau
     * @param $items
     * @param int $perPage
     * @param null $page
     * @param array $options
     * @return mixed
     */
    function paginate_array($items, $perPage = 10, $page = null, $options = [])
    {
        $page = $page ?: (Paginator::resolveCurrentPage() ?: 1);
        $items = $items instanceof Collection ? $items : Collection::make($items);
        return new LengthAwarePaginator($items->forPage($page, $perPage), $items->count(), $perPage, $page, $options);
    }
}
