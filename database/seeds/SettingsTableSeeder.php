<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class SettingsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('settings')->insert([
            'client_id' => 13,
            'client_secret' => 'dgF0wMuGFaAureptRvJu0Q13Dsrh4ui4L4N58IJm',
            'grant_type' => 'password',
            'scope' => 'openid email profile roles key',
            'api_sms_url' => 'https://smsvas.com/bulk/public/index.php/api/v1/',
            'api_sso_url' => 'https://sso.nexah.net/',
            'api_org_url' => 'https://ms-organization.nexah.net/api/v1/',
            'app_web_url' => 'https://sales.nexah.net',
            'username' => 'info@nexah.net',
            'password' => 'Nex@hu5r',
            'gen_key' => 'ZeBkwLlaWC3N2G7fMCWGrQ==',
            'url_shortener_api_url' => 'https://nxh.cm/api/v1/',
            'url_shortener_api_key' => '779b41bee3b9d13bfcf60b3f3966e92654971a046c48b797ee69844c569e1ab44ee1c3e30e478195',
        ]);

    }
}
