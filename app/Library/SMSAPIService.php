<?php
/**
 * Created by PhpStorm.
 * User: eddy
 * Date: 13/12/18
 * Time: 10:47
 */

namespace App\Library;

use App\Models\Setting;
use GuzzleHttp\Client;
use Illuminate\Support\Facades\Log;


class SMSAPIService
{

    private $setting;

    public function __construct()
    {
        $this->setting = Setting::first();
    }

    public function sendsms($token, $numbers ,$message, $sender)
    {
        $client = new Client();
        $form_params = [
            'token' => $token,
            'mobiles' => $numbers,
            'sms' => $message,
            'senderid' => $sender];

        $response = $client->post($this->setting->api_sms_url."sendsms", [
            'headers' => ['Accept' => 'application/json'],
            'form_params' => $form_params
        ]);

        $result = $response->getBody()->getContents();
        Log::info($result);
        return $result;
    }
}
