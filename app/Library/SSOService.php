<?php


namespace App\Library;


use App\Library\Repository\SSOServiceInterface;
use App\Models\Setting;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;

class SSOService implements SSOServiceInterface
{

    protected $settings;

    public function __construct()
    {
        $this->settings = Setting::first();
    }

    public function authentification($datas)
    {
        $alls = [
            'grant_type' => $this->settings->grant_type,
            'client_id' => $this->settings->client_id,
            'client_secret' => $this->settings->client_secret,
            'scope' => $this->settings->scope,
            'username' => $datas['email'],
            'password' => $datas['password']
        ];

        $url = $this->settings->api_sso_url."oauth/token";
        $body = json_encode($alls);

        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Accept:application/json',
            'Content-Type:application/json']);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $body);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 10);

        //needed for https
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);

        $response = curl_exec($ch);
        curl_close($ch);
        $result = json_decode($response, true);

        return $result;
    }

    public function find_by_user_id($sso_user_id,$acces)
    {
        $alls = [
            'sso_user_id' => $sso_user_id,
            'client_id' => $this->settings->client_id,
        ];
        $url = $this->settings->api_sso_url."api/find-user";
        $body = json_encode($alls);

        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Accept:application/json',
            "Authorization: Bearer $acces",
            'Content-Type:application/json']);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $body);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 300);

        //needed for https
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);

        $response = curl_exec($ch);
        curl_close($ch);
        $result = json_decode($response, true);

        return $result;
    }

    public function logout()
    {
        $url = $this->settings->api_sso_url."api/logout-user";

        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Accept:application/json',
            "Authorization: Bearer ".Auth::user()->access_token,
            'Content-Type:application/json']);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 30);

        //needed for https
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);

        $response = curl_exec($ch);
        curl_close($ch);
        $result = json_decode($response, true);

        return $result;
    }

    public function update_lang($lang, $access_token)
    {
        $alls = [
            'lang' => $lang,
        ];
        $url = $this->settings->api_sso_url."api/update-user-lang";
        $body = json_encode($alls);

        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Accept:application/json',
            "Authorization: Bearer ".$access_token,
            'Content-Type:application/json']);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $body);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 30);

        //needed for https
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);

        $response = curl_exec($ch);
        curl_close($ch);
        $result = json_decode($response, true);

        return $result;
    }

    public function update_user_org($data, $access_token)
    {
        $url = $this->settings->api_sso_url."api/update-user-org";

        return json_decode(
            Http::withHeaders([
                'Accept' => 'application/json',
                "Authorization" => "Bearer " . $access_token,
                'Content-Type' => "application/json"
            ])->post($url, $data), true
        );
    }


    public function getAllOrgsUser($access_token, $sso_user_id)
    {
        $url = $this->settings->api_sso_url."api/get-user-orgs/".$sso_user_id;
        $data['with_default'] = 1;
        return json_decode(
            Http::withHeaders([
                'Accept' => 'application/json',
                "Authorization" => "Bearer ".$access_token,
                'Content-Type' => "application/json"
            ])->get($url, $data),true
        );
    }

    public function findUserByRole($user, $role)
    {
        $url = $this->settings->api_sso_url."api/get-client-users";
        $data['client_id'] = $this->settings->client_id;
        $data['mcr_org_id'] = 1;
        $data['role_name'] = $role;
        return json_decode(
            Http::withHeaders([
                'Accept' => 'application/json',
                "Authorization" => "Bearer ".$user->access_token,
                'Content-Type' => "application/json"
            ])->get($url, $data),true
        );
    }

    public function rolesUserInApp($access_token, $sso_user_id, $mcr_org_id)
    {
        $url = $this->settings->api_sso_url."api/get-user-app-roles/".$mcr_org_id."/".$sso_user_id."/".$this->settings->client_id;

        return json_decode(
            Http::withHeaders([
                'Accept' => 'application/json',
                "Authorization" => "Bearer $access_token",
                'Content-Type' => "application/json"
            ])->get($url),true
        );
    }

}
