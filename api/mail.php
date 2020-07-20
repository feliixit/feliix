<?php
 error_reporting(0);

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
require '../PHPMailer-master/src/Exception.php';
require '../PHPMailer-master/src/PHPMailer.php';
require '../PHPMailer-master/src/SMTP.php';

include_once 'config/core.php';
include_once 'config/conf.php';

function sendMail($name, $email, $appove_hash, $reject_hash, $leave_info, $leaver, $department, $app_time, $leave_type, $start_time, $end_time, $leave_length, $reason, $imgurl) {
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

    $mail->IsHTML(true);
    $mail->AddAddress($email, $name);
    //$mail->Subject = "=?utf-8?B?" . base64_encode("信件標題") . "?=";
    $mail->SetFrom("feliix.it@gmail.com", "feliix.it");
    $mail->AddReplyTo("feliix.it@gmail.com", "feliix.it");
    // $mail->AddCC("tryhelpbuy@gmail.com", "tryhelpbuy");
    $mail->Subject = "Leave Application from " . $leaver;
    $content =  "<p>Dear " . $name . ",</p>";
    $content = $content . "<p>" . $leaver . " has applied for Leave, Following are the details:</p>";
    $content = $content . "<p> </p>";
    $content = $content . "<p>Applicant:" . $leaver . "</p>";
    $content = $content . "<p>Department:" . $department . "</p>";
    $content = $content . "<p>Applying Time:" . $app_time . "</p>";
    $content = $content . "<p>Leave Type:" . $leave_type . "</p>";
    $content = $content . "<p>Starting Time:" . $start_time . "</p>";
    $content = $content . "<p>Ending Time:" . $end_time . "</p>";
    $content = $content . "<p>Leave Length:" . $leave_length . "</p>";
    $content = $content . "<p>Reason:" . $reason . "</p>";
    if($imgurl != "")
        $content = $content . "<a href='" . $conf::$mail_ip . "/img/" . $imgurl . "'>Certificate of Diagnosis</a>";
    $content = $content . "<p> </p>";
    $content = $content . "<p>Please log on to Feliix >> Admin Section >> Verify and Review to review the leave application.</p>";
    $content = $content . "<p>URL: " . $conf::$mail_ip . "</p>";

    $mail->MsgHTML($content);
    if(!$mail->Send()) {
        return true;
//        echo "Error while sending Email.";
//        var_dump($mail);
    } else {
        return false;
//        echo "Email sent successfully";
    }
}

?>