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

function logMail($email, $content){

    $database = new Database();
    $db = $database->getConnection();

    $query = "INSERT INTO mail_log
                (`approve`, `content`) 
                VALUES (:approve, :content)";

        // prepare the query
        $stmt = $db->prepare($query);

        $content_b = addslashes($content);

            $stmt->bindParam(':approve', $email);
            $stmt->bindParam(':content', $content_b);
            

        try {
            // execute the query, also check if query was successful
            if ($stmt->execute()) {
                $last_id = $db->lastInsertId();
            }
            else
            {
                $arr = $stmt->errorInfo();
                error_log($arr[2]);
            }
        }
        catch (Exception $e)
        {
            error_log($e->getMessage());
        }


        return $last_id;
}

function sendGridMail($name, $email1,  $leaver, $projectname, $remark)
{
    $conf = new Conf();
    $email = new \SendGrid\Mail\Mail();
    $email->setFrom("feliix.it@gmail.com", "feliix.it");
    $email->setSubject("Downpayment Proof Submitted by " . $leaver . "(" . $projectname . ")" );
    $email->addTo($email1, $name);

    $baseURL = "https://storage.cloud.google.com/feliiximg/";

    $content =  "<p>Dear " . $name . ",</p>";
    $content = $content . "<p>" . $leaver . " has applied for downpayment proof, Following are the details:</p>";
    $content = $content . "<p>" . $remark . "</p>";

    $content = $content . "<p> </p>";
    $content = $content . "<p>Please log on to Feliix >> Admin Section >> Verify and Review to review the downpayment proof.</p>";
    $content = $content . "<p>URL: " . $conf::$mail_ip . "</p>";

    $email->addContent(
        "text/html", $content
    );

    $sendgrid = new \SendGrid($conf::$mail_key);
    try {
        $response = $sendgrid->send($email1);
        //print $response->statusCode() . "\n";
        //print_r($response->headers());
        //print $response->body() . "\n";

        logMail($email1, $content);
        return true;

    } catch (Exception $e) {
        logMail($email1, $e->ErrorInfo);
        return false;
    }
}


function send_check_notify_mail($name, $email1,  $leaver, $projectname, $remark, $subtime, $reason, $status)
{
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
    $mail->AddAddress($email1, $name);

    $mail->SetFrom("feliix.it@gmail.com", "feliix.it");
    $mail->AddReplyTo("feliix.it@gmail.com", "feliix.it");
    // $mail->AddCC("tryhelpbuy@gmail.com", "tryhelpbuy");
    $mail->Subject = "Checked: " . $status . " for Downpayment Proof submitted by " . $leaver . "(" . $projectname . ")";
    $content =  "<p>Dear " . $name . ",</p>";
    $content = $content . "<p>" . $leaver . " has checked downpayment proof, Following are the details:</p>";
    $content = $content . "<p>Status Checked:" . $status . "</p>";
    $content = $content . "<p> </p>";
    /*
    $content = $content . "<p>Project Name:" . $projectname . "</p>";
    $content = $content . "<p>Submission Time:" . $subtime . "</p>";
    $content = $content . "<p>Submitter:" . $leaver . "</p>";
    $content = $content . "<p>Checked:" . $status . "</p>";
    $content = $content . "<p>Remark:" . $remark . "</p>";
    */

    if($reason != "")
        $content = $content . "<p>Additional Remark:" . $reason . "</p>";

    $content = $content . "<p> </p>";


    $mail->MsgHTML($content);
    if($mail->Send()) {
        logMail($email1, $content);
        return true;
//        echo "Error while sending Email.";
//        var_dump($mail);
    } else {
        logMail($email1, $mail->ErrorInfo);
        return false;
//        echo "Email sent successfully";
    }

}


function send_pay_notify_mail($name, $email1,  $leaver, $projectname, $remark, $subtime)
{
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
    $mail->AddAddress($email1, $name);

    $mail->SetFrom("feliix.it@gmail.com", "feliix.it");
    $mail->AddReplyTo("feliix.it@gmail.com", "feliix.it");
    // $mail->AddCC("tryhelpbuy@gmail.com", "tryhelpbuy");
    $mail->Subject = "Downpayment Proof Submitted by " . $leaver . "(" . $projectname . ")";
    $content =  "<p>Dear " . $name . ",</p>";
    $content = $content . "<p>" . $leaver . " has applied for downpayment proof, Following are the details:</p>";
    $content = $content . "<p> </p>";

    $content = $content . "<p>Project Name:" . $projectname . "</p>";
    $content = $content . "<p>Submission Time:" . $subtime . "</p>";
    $content = $content . "<p>Submitter:" . $leaver . "</p>";
    $content = $content . "<p>Remark:" . $remark . "</p>";

    $content = $content . "<p> </p>";
    $content = $content . "<p>Please log on to Feliix >> Admin Section >> Verify and Review to review the downpayment proof.</p>";
    $content = $content . "<p>URL: " . $conf::$mail_ip . "</p>";

    $mail->MsgHTML($content);
    if($mail->Send()) {
        logMail($email1, $content);
        return true;
//        echo "Error while sending Email.";
//        var_dump($mail);
    } else {
        logMail($email1, $mail->ErrorInfo);
        return false;
//        echo "Email sent successfully";
    }

}

function sendMail($name, $email1, $appove_hash, $reject_hash, $leave_info, $leaver, $department, $app_time, $leave_type, $start_time, $end_time, $leave_length, $reason, $imgurl) {
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
    $mail->AddAddress($email1, $name);
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
    if($mail->Send()) {
        logMail($email1, $content);
        return true;
//        echo "Error while sending Email.";
//        var_dump($mail);
    } else {
        logMail($email1, $mail->ErrorInfo);
        return false;
//        echo "Email sent successfully";
    }
}

?>