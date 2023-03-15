<?php
/**
 * Created by PhpStorm.
 * User: eddy
 * Date: 13/12/18
 * Time: 10:47
 */

namespace App\Library;

use App\Library\Repository\MailServiceInterface;
use App\Mail\AffectTask;
use App\Mail\EndTask;
use App\Mail\LinkResetPassword;
use App\Mail\RemimberTask;
use App\Mail\RemimberWeekTask;
use App\Mail\ReportTask;
use App\Mail\ResetPassword;
use App\Mail\SendCredentials;
use App\Mail\SendErrors;
use App\Mail\UnAffectTask;
use Illuminate\Support\Facades\Mail;

class MailService implements MailServiceInterface
{

}
