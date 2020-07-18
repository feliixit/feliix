<?php
 error_reporting(0);

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
require '../PHPMailer-master/src/Exception.php';
require '../PHPMailer-master/src/PHPMailer.php';
require '../PHPMailer-master/src/SMTP.php';


function sendMail($name, $email, $appove_hash, $reject_hash, $leave_info) {
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
    $mail->Host       = "smtp.gmail.com";
    $mail->Username   = "feliix.it@gmail.com";
    $mail->Password   = "+886feliix";

    $mail->IsHTML(true);
    $mail->AddAddress($email, $name);
    //$mail->Subject = "=?utf-8?B?" . base64_encode("信件標題") . "?=";
    $mail->SetFrom("feliix.it@gmail.com", "feliix.it");
    $mail->AddReplyTo("sfeliix.it@gmail.com", "feliix.it");
    // $mail->AddCC("tryhelpbuy@gmail.com", "tryhelpbuy");
    $mail->Subject = "Need approval";
    $content =  "<p>Greetings! Pls review Leave Application:</p>";
    $content = "<p>" . $leave_info . "</p>";
   $content = $content . "<p><a href='" . $appove_hash . "'>Accept</a></p>";
    $content = $content . "<p><a href='" . $reject_hash . "''>reject</a></p>";
    $content = $content . "<p> </p>";
    $content = $content . "<p> </p>";
    $content = $content . "<p>-SERVICTORY</p>";

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