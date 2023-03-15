<?php

namespace App\Http\Controllers;

use App\Library\PaymentService;
use App\Models\Setting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Laravel\Lumen\Routing\Controller;

class UssdController extends Controller
{

    protected $paymentService;
    protected $setting;

    public function __construct()
    {
        $this->paymentService = new  PaymentService();
        $this->setting = Setting::first();
    }

    public function json($errcode, $message, $data = null)
    {
        return response()->json(array('errcode' => $errcode, 'message' => $message, 'data' => $data), $errcode);
    }

    public function clientInfo(Request $request)
    {
        $name = $request->name;
        $menus[] = "Monsieur/Madame $name vous souhaitez payer 10 FCFA pour l'assurance habitation \n 1. Confirmer \n 2. Annuler";
        return $this->json(200, 'Client enregistré', ['menus' => $menus]);
    }

    public function payment(Request $request)
    {
        $phone = $request->msisdn;
        if (substr($phone, 0, strlen("237")) == "237") {
            $phone = substr($phone, strlen("237"));
        }
        $check_number = $this->check($phone);
        $payment_ref = "chanas-" . Str::uuid()->toString();
        $response = $this->paymentService->mobilePay(1, $phone, 10, $payment_ref);
        Log::info($response);
        if ($response && array_key_exists("paymentId", $response)) {

            if ($check_number['code'] == "*126#") {
                return response()->json(array('errcode' => 200, 'message' => 'Le payement de votre assurance habitations ' . ' a ete initié. Tapez *126#'));
            } elseif ($check_number['code'] == '#150*50#') {
                return response()->json(array('errcode' => 200, 'message' => 'Le payement de votre assurance habitations' . ' a ete initié. Tapez le #150*50#'));
            } else {
                return response()->json(array('errcode' => 200, 'message' => 'Le payement de votre assurance habitations' . ' a ete initié. Tapez le  #237*885#'));
            }
        } else {
            return response()->json(array('errcode' => 404, 'message' => 'Impossible de trouver ce que vous chercher'), 404);
        }
    }


    public function check($number)
    {
        $number = str_replace(" ", "", $number);
        $regexs = [
            [
                'regex' => '/^(237)?((65[0-4])|(67[0-9])|(68[0-4]))[0-9]{6}$/',
                'code' => '*126#',
            ],
            [
                'regex' => '/^(237)?((65[5-9])|(69[0-9])|(68[6-9]))[0-9]{6}$/',
                'code' => '#150*50#',
            ],
            [
                'regex' => '/^(237)?((24[0-9])|(23[0-9]))[0-9]{6}$/',
                'code' => '#237*885#',
            ]
        ];
        foreach ($regexs as $regex) {
            if (preg_match_all($regex['regex'], $number)) {
                return $regex;
            }
        }
        return null;
    }
}
