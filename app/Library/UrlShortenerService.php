<?php


namespace App\Library;


use App\Models\Setting;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class UrlShortenerService
{

    protected $setting;

    public function __construct()
    {
        $this->setting = Setting::first();
    }


    public function createLink($link)
    {
        $data = array(
            'api_key' => $this->setting->url_shortener_api_key,
            'long_url' => $link,
            'tag' => 'taxespay');

        return json_decode(
            Http::withHeaders([
                'Accept' => 'application/json'
            ])->post($this->setting->url_shortener_api_url."create-link",
                $data),true);
    }
}
