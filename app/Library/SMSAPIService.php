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


class SMSAPIService
{

    private $setting;

    public function __construct()
    {
        $this->setting = Setting::first();
    }

    public function sendsms($numbers, $message, $sender)
    {
        $client = new Client();
        $form_params = [
            'user' => $this->setting->username,
            'password' => $this->setting->password,
            'mobiles' => $numbers,
            'sms' => $message,
            'senderid' => $sender];

        $response = $client->post($this->setting->api_sms_url."sendsms", [
            'headers' => ['Accept' => 'application/json'],
            'form_params' => $form_params
        ]);
        $result = $response->getBody()->getContents();
        return $result;
    }
}
