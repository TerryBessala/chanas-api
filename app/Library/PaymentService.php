<?php
/**
 * Created by PhpStorm.
 * User: eddy
 * Date: 13/12/18
 * Time: 10:47
 */

namespace App\Library;

use App\Models\OrgSetting;
use App\Models\Setting;
use App\Models\ContribuableTaxe;
use Faker\Provider\ar_SA\Payment;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class PaymentService
{
    public function __construct()
    {
        $this->setting = Setting::first();
    }

    public function json($errcode, $message, $data = null)
    {
        return response()->json(array('errcode' => $errcode, 'message' => $message, 'data' => $data), $errcode);
    }

    public function generatePayLink($baseLink, $taxe)
    {
        $key = crypter($taxe->org_id . ":" . $taxe->id);
        Log::info($key);
        return short_link($baseLink . $key);

    }

    public function send($orgSetting, $mobile_no, $taxe,$total,$name)
    {
        $smsApiService = new SMSAPIService();
        $lien = $this->generatePayLink($this->setting->pay_base_link, $taxe);
        $bulletin_emission=short_link( url('/api/v1/imputation/')."/".$taxe->contribuable_id);
        $custom_message = str_replace(array('$PAYLINK', '$TOTAL','$NAME','$BULLETIN'),array($lien,$total,$name,$bulletin_emission), $orgSetting->sms);
        Log::info($custom_message);
        $json = $smsApiService->sendsms($orgSetting->sms_api_token, $mobile_no, $custom_message, $orgSetting->sender_id);
        return [
            'messageid' => $json['sms'][0]['messageid']
        ];
    }
    public function mobilePay ($orgId, $mobileno, $amount, $payment_ref)
    {
        $orgSetting = Setting::first();
        Log::info($orgSetting);
        $url = "https://ms-payment-preprod.nexah.net/api/v1/place-deposit";
        $data1 = array(
            "api_key" => $orgSetting->pay_api_key,
            "service_key" => $orgSetting->pay_service_key,
            "payment_ref" => $payment_ref,
            "number" => (string)$mobileno,
            "transactional" => "yes",
            "amount" => (string)$amount
        );
        $result = json_decode(
            Http::withHeaders([
                'Accept' => 'application/json',
                'Content-type' => 'application/json',
            ])->post("https://ms-payment-preprod.nexah.net/api/v1/place-deposit", $data1),true);
        Log::info(json_encode($result));
        return $result;
    }
    public function sendsms($token, $numbers, $message, $sender)
    {
        $form_params = [
            'token' => $token,
            'mobiles' => $numbers,
            'sms' => $message,
            'senderid' => $sender
        ];
        $result = json_decode(
            Http::withHeaders([
                'Accept' => 'application/json',
                'Content-type' => 'application/json',
            ])->post("https://smsvas.com/bulk/public/index.php/api/v1/send", $form_params),true);
        Log::info(json_encode($result));
        return $result;
    }

    public function getPaidTaxes($taxes)
    {
        $list = "";
        for ($i=0; $i < count($taxes); $i++){
            if ($i==0){
                $list = $taxes[$i]["name"] . ":" . $taxes[$i]["prix"];
            }else{
                $list .= ";".$taxes[$i]["name"]. ":" . $taxes[$i]["prix"];
            }
        }
        return $list;
    }
    public function getPaidTaxesI($taxes)
    {
        $list = "";
        for ($i=0; $i < count($taxes); $i++){
            if ($i==0){
                $list = $taxes[$i]["name"] . ":" . $taxes[$i]["montant"];
            }else{
                $list .= ";".$taxes[$i]["name"]. ":" . $taxes[$i]["montant"];
            }
        }
        return $list;
    }

    public function getTaxes($taxes){
        $list = "";
        for ($i=0; $i < count($taxes); $i++){
            if ($i==0){
                $list = $taxes[$i]["name"] . ":" . $taxes[$i]["montant"];
            }else{
                $list .= ";".$taxes[$i]["name"] . ":" . $taxes[$i]["montant"];
            }
        }
        return $list;
    }
    public function getContribuableTaxes($contribuableId, $groupId){

        $list = "";
        $contribuableTaxes = DB::select(' call sp_contribuabletaxes_list(?,?)', [$contribuableId, $groupId]);

        for ($i=0; $i < count($contribuableTaxes); $i++){
            if ($i==0){
                $list =$contribuableTaxes[$i]->name . ":" . $contribuableTaxes[$i]->montant;
            }else{
                $list .= ";".$contribuableTaxes[$i]->name . ":" .$contribuableTaxes[$i]->montant;
            }
        }
        return $list;
    }

    public function createTaxes($contribuable_id,$group,array $taxes,$quantity=1){
        foreach ($taxes as $key => $value){
            if ($value > 0) {
                $taxeId = DB::select('call sp_tax_check_name(?)', array($key));
                $trimester_id= DB::select('call sp_show_trimester(?)',[date('Y-m-d')])[0]->id;
                $exam_id= DB::select('call sp_show_exam(?)',[date('Y')])[0]->id;
                if ($taxeId != null) {
                    $contribuableTax= DB::select('call sp_check_contribuable_tax(?,?,?,?)',[$taxeId[0]->id,$contribuable_id,$trimester_id,$exam_id]);
                    if ($contribuableTax==null)
                    {
                        ContribuableTaxe::create([
                            "group_id" => $group->id,
                            "contribuable_id" => $contribuable_id,
                            "trimester_id"=> $trimester_id,
                            "exam_id"=>$exam_id,
                            "taxe_id" => $taxeId[0]->id,
                            "amount" => $value,
                            "quantity"=> $quantity,
                            "amount_to_pay"=>$value*$quantity,
                            "org_id" => $group->org_id,
                            "sso_user_id" => $group->sso_user_id
                        ]);
                    }
                }
            }
        }
    }
}
