<?php

namespace App\Http\Controllers;

use App\Library\PaymentService;
use App\Library\SMSAPIService;
use App\Models\Setting;
use Barryvdh\DomPDF\Facade\Pdf;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Laravel\Lumen\Routing\Controller;
use SimpleSoftwareIO\QrCode\Facades\QrCode;

class UssdController extends Controller
{

    protected $paymentService;
    protected $setting;
    protected $smsService;

    public function __construct()
    {
        $this->paymentService = new  PaymentService();
        $this->setting = Setting::first();
        $this->smsService = new SMSAPIService();
    }

    public function json($errcode, $message, $data = null)
    {
        return response()->json(array('errcode' => $errcode, 'message' => $message, 'data' => $data), $errcode);
    }

    public function clientInfo(Request $request)
    {
        $name = $request->name;
        $phone = $request->msisdn;
        if (substr($phone, 0, strlen("237")) == "237") {
            $phone = substr($phone, strlen("237"));
        }


        $menus[] = "Monsieur/Madame $name vous souhaitez payer 10 FCFA pour l'assurance habitation \n 1. Confirmer \n 2. Annuler";
        return $this->json(200, 'Client enregistré', ['menus' => $menus]);
    }

    public function payment(Request $request)
    {
        $phone = $request->msisdn;
        Log::info('data send buy USSD: '.json_encode($request->all()));

        $name = $request->name;
        $phone = $request->msisdn;
        if (substr($phone, 0, strlen("237")) == "237") {
            $phone = substr($phone, strlen("237"));
        }
        $email = $request->email;
        $estimate_value = $request->estimate_value;
        $nb_piece = $request->nb_piece;

        $check_number = $this->check($phone);
        $payment_ref = "chanas-" . $this->generateRandomString();
        DB::select('call sp_client_create(?,?,?,?,?,?)', array($name, $email, $phone, $estimate_value, $nb_piece, 1));

        $client = DB::select('call sp_client_find(?)', [$phone])[0];
        $response = $this->paymentService->mobilePay(1, $phone, 10, $payment_ref);
        Log::info($response);
        if ($response && array_key_exists("paymentId", $response)) {
            $result = DB::select('call sp_client_update(?,?,?,?)', [$client->id, $response["paymentId"], 'PENDING', $payment_ref]);

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

    public function notify(Request $request)
    {
        $payment_ref = $request->get('payment_ref');
        $order_id = $request->get('order_id');
        $status = $request->get('status');


        $client = DB::select('call sp_client_update_status(?,?,?,?)',[checkstatus($status),$payment_ref,$order_id,date_to_utc(date("Y-m-d H:i:s"))])[0];
        if (checkstatus($status) !== paysuccess() or checkstatus($status) === paysuccess())
            {
                $invoice_url = short_link("".url("/api/v1/invoice")."/".$order_id);

                $custom_message = "Cher(e) Monsieur/Madame le paiement de vitre assurance HABITATION a réussi cliquer sur le lien pour télécharger votre facture. $invoice_url";

                $this->smsService->sendsms($this->setting->sms_api_token, $client->phone, $custom_message, $this->setting->sender);
            }
    }



    public function invoice($payment_ref){

        try {
            Log::info($payment_ref);
            $payments =  DB::select('call sp_client_find_payment(?)', array($payment_ref));
            $invoice["services"] = array();
            $invoice["total"] = 0;
            if ($payments != null){
                $invoice["name"] = $payments[0]->name;
                $invoice["phone"] = $payments[0]->phone;
                $invoice["Transaction_id"] = $payments[0]->payment_id;
                $invoice['date'] = date_to_utc($payments[0]->payment_date);
                foreach ($payments as $payment){
                    $invoice["total"] = $invoice["total"] + $payment->amount;
                    $service['name'] = "ASSURANCE HABITATION";
                    $service['amount'] = $payment->amount;
                    $invoice["services"][] = $service;
                }
                $name = "qrcode-$payment_ref.svg";
                $file_path = "../public/public/uploads/".$name;
                if (!file_exists($file_path)){
                    try{
                        QrCode::size(100)->format('svg')->generate($payments[0]->payment_id, $file_path);
                    }catch (Exception $e){
                        Log::info($e->getMessage());
                    }
                }
                $pdf = PDF::loadView("invoice", compact('invoice', 'name'));
                return $pdf->download('invoice.pdf');
            }
        }
        catch (Exception $e){
            Log::info($e->getMessage());
        }
        abort(404);
        return null;
    }
    public function generateRandomString($length = 10)
    {
        $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $charactersLength = strlen($characters);
        $randomString = '';
        for ($i = 0; $i < $length; $i++) {
            $randomString .= $characters[random_int(0, $charactersLength - 1)];
        }
        return $randomString;
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
