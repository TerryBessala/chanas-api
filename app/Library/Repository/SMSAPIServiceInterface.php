<?php
/**
 * Created by PhpStorm.
 * User: eddy
 * Date: 13/12/18
 * Time: 10:57
 */

namespace App\Library\Repository;


interface SMSAPIServiceInterface
{
    public function sendsms($username, $password, $numbers, $message, $sender, $scheduletime, $stackid, $campaignid);
    public function smsbalance($username, $password);
    public function smscredit($username, $password);

    public function ocmcredit();
    public function mtncredit();
}
