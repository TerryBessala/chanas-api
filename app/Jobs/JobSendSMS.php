<?php

namespace App\Jobs;

use App\Library\SMSAPIService;
use App\Models\Campaign;
use App\Models\Message;
use Exception;
use Illuminate\Support\Facades\Log;

class JobSendSMS extends Job
{
    protected $user;
    protected $campaign;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($user, Campaign $campaign)
    {
        //
        //$this->connection = 'sqs';
        $this->user = $user;
        $this->campaign = $campaign;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        try{
            $this->sendMessage($this->user, $this->campaign);
        }catch (Exception $e){
            $this->campaign->sms_is_working = 0;
            $this->campaign->save();
            Log::error("Send Bulk SMS Job Fail = ".json_encode($e->getMessage()));

        }
    }

    /**
     * The job failed to process.
     *
     * @param  Exception  $exception
     * @return void
     */
    public function failed(Exception $exception)
    {
        $this->campaign->sms_is_working = 0;
        $this->campaign->save();
        // Send user notification of failure, etc...
        Log::error("Send Bulk SMS Job Fail = ".json_encode($exception->getMessage()));

    }

    public function sendMessage($user,$campaign){

        if ($campaign->enabled == 1) {
            $smsService = new  SMSAPIService();
            Message::whereNull('messageid')
                ->whereNull('smsclientid')
                ->where('campaign_id', '=', $campaign->id)
                ->limit(50)
                ->chunk(50, function ($messages) use ($user, $smsService, $campaign) {
                    try {
                        //Log::info("messages = ". json_encode($messages));
                        $request = [
                            "api_key" => $user->api_key,
                            "sms" => []
                        ];
                        foreach ($messages as $message) {
                            $smselt = [
                                "idsmsskysoft" => $message->id,
                                "campaignid" => $message->campaign_id,
                                "mobileno" => str_replace("+", "", $message->number),
                                "senderid" => $campaign->sender,
                                "message" => $message->message,
                                "scheduletime" => null
                            ];
                            array_push($request["sms"], $smselt);
                        }
                        Log::info("request = ". json_encode($request));
                        $json = $smsService->sendBulkMessage($request);
                        Log::info("response = ". json_encode($json));
                        if ($json != null) {
                            $smsarr = json_decode($json);
                            if (property_exists($smsarr, 'responsecode')) {
                                if ($smsarr->responsecode == 1) {
                                    foreach ($smsarr->sms as $sms) {
                                        if ($sms != null) {
                                            $this->update($sms);
                                        }
                                    }
                                }
                            }
                        }
                    } catch (\Exception $exception) {
                        Log::error('Error send bulk SMS = ' . $exception->getMessage());
                    }
                });
            if ($campaign->sms_sent == $campaign->nb_contact) {
                $campaign->is_completed = 1;
                $campaign->save();
            }
            $campaign->sms_is_working = 0;
            $campaign->save();
        }else{
            Log::info("not enabled = ". $campaign->name);
        }

    }

    public function update($sms){
        if ($sms->messageid != null && $sms->smsclientid != null) {
            $value = array(
                "messageid" => $sms->messageid,
                "smsclientid" => $sms->smsclientid
            );
            try{
                Message::where("id",$sms->idsmsskysoft)->update($value);
            }catch (Exception $e){
            }
        }
    }
}
