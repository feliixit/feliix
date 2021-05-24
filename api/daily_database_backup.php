<?php
 error_reporting(0);

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
require '../PHPMailer-master/src/Exception.php';
require '../PHPMailer-master/src/PHPMailer.php';
require '../PHPMailer-master/src/SMTP.php';
require '../vendor/autoload.php';

include_once 'config/core.php';
include_once 'config/conf.php';
include_once 'config/database.php';

date_default_timezone_set('Asia/Taipei');
$date = date("Y-m-d");

daily_database_backup_mail($date);

function daily_database_backup_mail($my_day)
{

    $title = "Daily database backup." . $my_day . " ";
   
    $conf = new Conf();

    $mail = new PHPMailer();
    $mail->IsSMTP();
    $mail->Mailer = "smtp";
    $mail->CharSet = 'UTF-8';
    $mail->Encoding = 'base64';

    $mail->SMTPDebug  = 0;
    $mail->SMTPAuth   = true;
    $mail->SMTPSecure = "ssl";
    $mail->Port       = 465;
    $mail->SMTPKeepAlive = true;
    $mail->Host       = $conf::$mail_host;
    $mail->Username   = $conf::$mail_username;
    $mail->Password   = $conf::$mail_password;

    $mail->addAttachment("/home/feliix_it/mysql/".$my_day."-feliix.gz");
    $mail->addAttachment("/home/feliix_it/mysql/".$my_day."-ludb.gz");

    $mail->IsHTML(true);

    $mail->AddAddress('dereckyin@gmail.com', 'dereckyin');

    $mail->SetFrom("feliix.it@gmail.com", "Feliix.System");
    $mail->AddReplyTo("feliix.it@gmail.com", "Feliix.System");
    
    $mail->Subject = $title;
   
    $content = "<p>Date backup:" . $my_day . "</p>";
 

    $mail->MsgHTML($content);
    if($mail->Send()) {
        
        return true;
    } else {
      
        return false;
    }

}