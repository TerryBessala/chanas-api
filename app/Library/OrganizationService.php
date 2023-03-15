<?php


namespace App\Library;


use App\Library\Repository\OrganizationServiceInterface;
use App\Models\Setting;
use Illuminate\Support\Facades\Http;

class OrganizationService implements OrganizationServiceInterface
{

    protected $settings;

    public function __construct()
    {
        $this->settings = Setting::first();
    }

    public function find($id)
    {
        $url = $this->settings->api_org_url."organisations/".$id;
        return json_decode(
            Http::withHeaders([
                'Accept' => 'application/json',
                'Content-Type' => 'application/json'
            ])->get($url),true
        );
    }

    public function create($data)
    {
        $url = $this->settings->api_org_url."organisations";
        return json_decode(
            Http::withHeaders([
                'Accept' => 'application/json',
                'Content-Type' => 'application/json'
            ])->post($url, $data),true
        );
    }

    public function update($id,$data)
    {
        $url = $this->settings->api_org_url."organisations/".$id;
        return json_decode(
            Http::withHeaders([
                'Accept' => 'application/json',
                'Content-Type' => 'application/json'
            ])->put($url, $data),true
        );
    }
}
