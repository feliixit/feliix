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
    $email->setFrom("feliix.it@gmail.com", "Feliix.System");
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

function send_review_mail_adm($s_date, $e_date, $adm_id, $emp_id){
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

    
    $admin = GetNotifiers($adm_id);
    $admin_name = '';
    $admin_email = '';
    foreach($admin as &$list)
    {
        $mail->AddAddress($list["email"], $list["username"]);
        $admin_name = $list["username"];
        $admin_email = $list["email"];
    }

    $emp = GetNotifiers($emp_id);
    $emp_name = '';
    $emp_title = '';
    $emp_department = '';
    foreach($emp as &$list)
    {
        $emp_name = $list["username"];
        $emp_title = $list["title"];
        $emp_department = $list["department"];
    }

    $title = "[Notification] " . $emp_name . "'s Performance Review Form over " . $s_date . " ~ " . $e_date . " is Open";

    $mail->IsHTML(true);

    $mail->SetFrom("feliix.it@gmail.com", "Feliix.System");
    $mail->AddReplyTo("feliix.it@gmail.com", "Feliix.System");

    $mail->Subject = $title;
    $content =  "<p>Dear " . $admin_name . ",</p>";
    $content = $content . "<p>" . $emp_name . "'s Performance Review Form over " . $s_date . " ~ " . $e_date . " is open now. As evaluation on your subordinate, you can record your scores and comments against each performance criterion. We encourage you to give your inputs on a regular basis as this will help in clear discussion between you and your subordinate.</p>";
    $content = $content . "<p> </p>";
    $content = $content . "<p>Following are the details:</p>";
    $content = $content . "<p> </p>";
    $content = $content . "<p>Employee Name:" . $emp_name . "</p>";
    $content = $content . "<p>Employee Position:" . $emp_title . "</p>";
    $content = $content . "<p>Employee Department:" . $emp_department . "</p>";
    $content = $content . "<p>Supervisor:" . $admin_name . "</p>";
    $content = $content . "<p>Review Period:" . $s_date . " ~ " . $e_date . "</p>";
    
    $content = $content . "<p> </p>";

    $content = $content . "<p>Please log on to Feliix >> Performance Evaluation >> Tab Performance Review to start the evaluation.</p>";
    $content = $content . "<p>URL: " . $conf::$mail_ip . "</p>";


    $mail->MsgHTML($content);
    if($mail->Send()) {
        logMail($admin_email, $content);
        return true;
    } else {
        logMail($admin_email, $mail->ErrorInfo);
        return false;
    }
}

function send_review_mail($s_date, $e_date, $adm_id, $emp_id){
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

    
    $admin = GetNotifiers($adm_id);
    $admin_name = '';
    $admin_email = '';
    foreach($admin as &$list)
    {
        $admin_name = $list["username"];
        $admin_email = $list["email"];
    }

    $emp = GetNotifiers($emp_id);
    $emp_name = '';
    $emp_email = '';
    $emp_title = '';
    $emp_department = '';
    foreach($emp as &$list)
    {
        $mail->AddAddress($list["email"], $list["username"]);
        $emp_name = $list["username"];
        $emp_title = $list["title"];
        $emp_department = $list["department"];
        $emp_email = $list["email"];
    }

    $title = "[Notification] Your Performance Review Form over " . $s_date . " ~ " . $e_date . " is Open";

    $mail->IsHTML(true);

    $mail->SetFrom("feliix.it@gmail.com", "Feliix.System");
    $mail->AddReplyTo("feliix.it@gmail.com", "Feliix.System");

    $mail->Subject = $title;
    $content =  "<p>Dear " . $emp_name . ",</p>";
    $content = $content . "<p>Your Performance Review Form over " . $s_date . " ~ " . $e_date . " is open now. As self-evaluation, you can record your scores and comments against each performance criterion. We encourage you to give your inputs on a regular basis as this will help in clear discussion between you and your supervisor.</p>";
    $content = $content . "<p> </p>";
    $content = $content . "<p>Following are the details:</p>";
    $content = $content . "<p> </p>";
    $content = $content . "<p>Employee Name:" . $emp_name . "</p>";
    $content = $content . "<p>Employee Position:" . $emp_title . "</p>";
    $content = $content . "<p>Employee Department:" . $emp_department . "</p>";
    $content = $content . "<p>Supervisor:" . $admin_name . "</p>";
    $content = $content . "<p>Review Period:" . $s_date . " ~ " . $e_date . "</p>";
    
    $content = $content . "<p> </p>";

    $content = $content . "<p>Please log on to Feliix >> Performance Evaluation >> Tab Performance Review to start the evaluation.</p>";
    $content = $content . "<p>URL: " . $conf::$mail_ip . "</p>";


    $mail->MsgHTML($content);
    if($mail->Send()) {
        logMail($emp_email, $content);
        return true;
    } else {
        logMail($emp_email, $mail->ErrorInfo);
        return false;
    }
}


function send_check_notify_mail_new($name, $email1, $projectname, $remark, $subtime, $reason, $status, $category, $kind)
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

    if($kind == 0)
        $payment = "DownPayment";
    
    if($kind == 1)
        $payment = "Payment";

    if($category == '1')
        $mail->AddAddress('johmar@feliix.com', 'Johmar Maximo');

    if($category == '2')
        $mail->AddAddress('nestor@feliix.com', 'Nestor Rosales');

    $mail->AddCC('kuan@feliix.com', 'Kuan');
    $mail->AddCC('kristel@feliix.com', 'Kristel Tan');
    $mail->AddCC('glen@feliix.com', 'Glendon Wendell Co');
    $mail->AddCC('wren@feliix.com', 'Thalassa Wren Benzon');
    $mail->AddCC('dennis@feliix.com', 'Dennis Lin');

    $mail->SetFrom("feliix.it@gmail.com", "Feliix.System");
    $mail->AddReplyTo("feliix.it@gmail.com", "Feliix.System");
    // $mail->AddCC("tryhelpbuy@gmail.com", "tryhelpbuy");
    $mail->Subject = "Checked: " . $status . " for " . $payment . " Proof submitted by " . $name . "(" . $projectname . ")";
    $content =  "<p>Dear " . $name . ",</p>";
    $content = $content . "<p>Glen has checked " . $payment . " proof, Following are the details:</p>";

    $content = $content . "<p>Project Name:" . $projectname . "</p>";
    $content = $content . "<p>Submission Time:" . $subtime . "</p>";
    $content = $content . "<p>Submitter:" . $name . "</p>";
    $content = $content . "<p>Remark:" . $remark . "</p>";


    $content = $content . "<p>Status: Checked: " . $status . "</p>";
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

    $content = $content . "<p>Please log on to Feliix >> Admin Section >> Verify and Review to view the downpayment proof.</p>";
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

function send_check_notify_mail($name, $email1, $projectname, $remark, $subtime, $reason, $status, $category)
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


    if($category == '1')
        $mail->AddAddress('johmar@feliix.com', 'Johmar Maximo');

    if($category == '2')
        $mail->AddAddress('nestor@feliix.com', 'Nestor Rosales');

    $mail->AddCC('kuan@feliix.com', 'Kuan');
    $mail->AddCC('kristel@feliix.com', 'Kristel Tan');
    $mail->AddCC('glen@feliix.com', 'Glendon Wendell Co');
    $mail->AddCC('wren@feliix.com', 'Thalassa Wren Benzon');
    $mail->AddCC('dennis@feliix.com', 'Dennis Lin');

    $mail->SetFrom("feliix.it@gmail.com", "Feliix.System");
    $mail->AddReplyTo("feliix.it@gmail.com", "Feliix.System");
    // $mail->AddCC("tryhelpbuy@gmail.com", "tryhelpbuy");
    $mail->Subject = "Checked: " . $status . " for Downpayment Proof submitted by " . $name . "(" . $projectname . ")";
    $content =  "<p>Dear " . $name . ",</p>";
    $content = $content . "<p>Glen has checked downpayment proof, Following are the details:</p>";

    $content = $content . "<p>Project Name:" . $projectname . "</p>";
    $content = $content . "<p>Submission Time:" . $subtime . "</p>";
    $content = $content . "<p>Submitter:" . $name . "</p>";
    $content = $content . "<p>Remark:" . $remark . "</p>";


    $content = $content . "<p>Status: Checked: " . $status . "</p>";
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

    $content = $content . "<p>Please log on to Feliix >> Admin Section >> Verify and Review to view the downpayment proof.</p>";
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

function send_pay_notify_mail_new($name, $email1,  $leaver, $projectname, $remark, $subtime, $category, $kind)
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
    $mail->AddAddress('glen@feliix.com', 'Glendon Wendell Co');

    if($category == '1')
        $mail->AddAddress('johmar@feliix.com', 'Johmar Maximo');

    if($category == '2')
        $mail->AddAddress('nestor@feliix.com', 'Nestor Rosales');

    $pay = "Payment";
    if($kind == 0)
        $pay = "Downpayment";

    $mail->AddCC('kuan@feliix.com', 'Kuan');
    $mail->AddCC('kristel@feliix.com', 'Kristel Tan');
    $mail->AddCC($email1, $name);
    $mail->AddCC('wren@feliix.com', 'Thalassa Wren Benzon');
    $mail->AddCC('dennis@feliix.com', 'Dennis Lin');

    $mail->SetFrom("feliix.it@gmail.com", "Feliix.System");
    $mail->AddReplyTo("feliix.it@gmail.com", "Feliix.System");
    // $mail->AddCC("tryhelpbuy@gmail.com", "tryhelpbuy");
    $mail->Subject = "Payment Proof Submitted by " . $leaver . "(" . $projectname . ")";
    $content =  "<p>Dear Glen,</p>";
    $content = $content . "<p>" . $leaver . " has submitted payment proof, Following are the details:</p>";
    $content = $content . "<p> </p>";

    $content = $content . "<p>Project Name:" . $projectname . "</p>";
    $content = $content . "<p>Submission Time:" . $subtime . "</p>";
    $content = $content . "<p>Submitter:" . $leaver . "</p>";
    $content = $content . "<p>Type:" . $pay . "</p>";
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

function send_pay_notify_mail($name, $email1,  $leaver, $projectname, $remark, $subtime, $category)
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
    $mail->AddAddress('glen@feliix.com', 'Glendon Wendell Co');

    if($category == '1')
        $mail->AddAddress('johmar@feliix.com', 'Johmar Maximo');

    if($category == '2')
        $mail->AddAddress('nestor@feliix.com', 'Nestor Rosales');

    $mail->AddCC('kuan@feliix.com', 'Kuan');
    $mail->AddCC('kristel@feliix.com', 'Kristel Tan');
    $mail->AddCC($email1, $name);
    $mail->AddCC('wren@feliix.com', 'Thalassa Wren Benzon');
    $mail->AddCC('dennis@feliix.com', 'Dennis Lin');

    $mail->SetFrom("feliix.it@gmail.com", "Feliix.System");
    $mail->AddReplyTo("feliix.it@gmail.com", "Feliix.System");
    // $mail->AddCC("tryhelpbuy@gmail.com", "tryhelpbuy");
    $mail->Subject = "Downpayment Proof Submitted by " . $leaver . "(" . $projectname . ")";
    $content =  "<p>Dear Glen,</p>";
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
    $mail->SetFrom("feliix.it@gmail.com", "Feliix.System");
    $mail->AddReplyTo("feliix.it@gmail.com", "Feliix.System");
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

function send_meeting_notify_mail($name, $email1, $subject, $creator, $attendee, $start_time, $end_time, $detail)
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

    $mail->SetFrom("feliix.it@gmail.com", "Feliix.System");
    $mail->AddReplyTo("feliix.it@gmail.com", "Feliix.System");
    // $mail->AddCC("tryhelpbuy@gmail.com", "tryhelpbuy");
    $mail->Subject = "Notification: New Meeting from " . $creator;
    $content =  "<p>Dear " . $name . ",</p>";
    $content = $content . "<p>" . $creator . " created a meeting. Following are the details:</p>";
    $content = $content . "<p>Subject:" . $subject . "</p>";
    $content = $content . "<p>Creator:" . $creator . "</p>";
    $content = $content . "<p>Attendee:" . $attendee . "</p>";
    $content = $content . "<p>Time:" . $start_time . " - " . $end_time . "</p>";
    $content = $content . "<p>Content:" . $detail . "</p>";
    /*
    $content = $content . "<p>Project Name:" . $projectname . "</p>";
    $content = $content . "<p>Submission Time:" . $subtime . "</p>";
    $content = $content . "<p>Submitter:" . $leaver . "</p>";
    $content = $content . "<p>Checked:" . $status . "</p>";
    $content = $content . "<p>Remark:" . $remark . "</p>";
    */

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


function send_meeting_modified_mail($name, $email1, $subject, $creator, $attendee, $start_time, $end_time, $detail)
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

    $mail->SetFrom("feliix.it@gmail.com", "Feliix.System");
    $mail->AddReplyTo("feliix.it@gmail.com", "Feliix.System");
    // $mail->AddCC("tryhelpbuy@gmail.com", "tryhelpbuy");
    $mail->Subject = "Notification: Meeting Info Changed by " . $creator;
    $content =  "<p>Dear " . $name . ",</p>";
    $content = $content . "<p>" . $creator . " changed the original info of the meeting. Following are the details after change:</p>";
    $content = $content . "<p>Subject:" . $subject . "</p>";
    $content = $content . "<p>Creator:" . $creator . "</p>";
    $content = $content . "<p>Attendee:" . $attendee . "</p>";
    $content = $content . "<p>Time:" . $start_time . " - " . $end_time . "</p>";
    $content = $content . "<p>Content:" . $detail . "</p>";
    /*
    $content = $content . "<p>Project Name:" . $projectname . "</p>";
    $content = $content . "<p>Submission Time:" . $subtime . "</p>";
    $content = $content . "<p>Submitter:" . $leaver . "</p>";
    $content = $content . "<p>Checked:" . $status . "</p>";
    $content = $content . "<p>Remark:" . $remark . "</p>";
    */

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


function send_meeting_delete_mail($name, $email1, $subject, $creator, $attendee, $start_time, $end_time, $detail)
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

    $mail->SetFrom("feliix.it@gmail.com", "Feliix.System");
    $mail->AddReplyTo("feliix.it@gmail.com", "Feliix.System");
    // $mail->AddCC("tryhelpbuy@gmail.com", "tryhelpbuy");
    $mail->Subject = "Notification: Meeting Deleted by " . $creator;
    $content =  "<p>Dear " . $name . ",</p>";
    $content = $content . "<p>" . $creator . " deleted the meeting. Following are the details before deletion:</p>";
    $content = $content . "<p>Subject:" . $subject . "</p>";
    $content = $content . "<p>Creator:" . $creator . "</p>";
    $content = $content . "<p>Attendee:" . $attendee . "</p>";
    $content = $content . "<p>Time:" . $start_time . " - " . $end_time . "</p>";
    $content = $content . "<p>Content:" . $detail . "</p>";
    /*
    $content = $content . "<p>Project Name:" . $projectname . "</p>";
    $content = $content . "<p>Submission Time:" . $subtime . "</p>";
    $content = $content . "<p>Submitter:" . $leaver . "</p>";
    $content = $content . "<p>Checked:" . $status . "</p>";
    $content = $content . "<p>Remark:" . $remark . "</p>";
    */

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


function void_expense_mail($request_no, $applicant, $user_name, $user_email, $department, $ap_time, $project_name1, $project_name, $date_request, $total_amount, $reason, $request_type)
{
    $title = "";
    $action = "";
    $tab = "";

    switch ($request_type) {
        
        case "Void":
            $title = "Expense Application with Request No." . $request_no . " from " . $user_name . " was Voided";
            $action = "Releaser";
            $conten1 = "<p>Your expense application was voided by " . $action . ". Following are the details:</p>";
            $tab = "<p>Please log on to Feliix >> Payment Request/Claim >> Expense Apply/Liquidate >> Tab Records to view the expense application or re-submit.</p>";
            break;
        default:
            return;
            break;

    }

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

    $mail->AddAddress($user_email, $user_name);

    $notifior = array();
    $notifior = GetPettyVoidNotifiers();
    foreach($notifior as &$list)
    {
        $mail->AddCC($list["email"], $list["username"]);
    }
    
    $mail->SetFrom("feliix.it@gmail.com", "Feliix.System");
    $mail->AddReplyTo("feliix.it@gmail.com", "Feliix.System");
    
    $mail->Subject = $title;
    $content =  "<p>Dear " . $user_name . ",</p>";
    $content = $content . $conten1;
    $content = $content . "<p>Request No.:" . $request_no . "</p>";
    $content = $content . "<p>Applicant:" . $applicant . "</p>";
    $content = $content . "<p>Department:" . $department . "</p>";
    $content = $content . "<p>Application Time:" . $ap_time . "</p>";
    $content = $content . "<p>Project Name:" . $project_name1 . "</p>";
    $content = $content . "<p>Reason:" . $project_name . "</p>";
    $content = $content . "<p>Date Needed:" . $date_request . "</p>";
    $content = $content . "<p>Total Amount Requested:" . $total_amount . "</p>";
    $content = $content . "<p>Voiding Reason:" . $reason . "</p>";
    $content = $content . "<p> </p>";
    $content = $content . $tab;
    $content = $content . "<p>URL: https://feliix.myvnc.com/</p>";

    $mail->MsgHTML($content);
    if($mail->Send()) {
        logMail($user_email, $content);
        return true;
    } else {
        logMail($user_email, $mail->ErrorInfo);
        return false;
    }

}

function reject_expense_mail($request_no, $user_name, $requestor, $requestor_email, $department, $ap_time, $project_name1, $project_name, $date_request, $total_amount, $reason, $request_type)
{
    $title = "";
    $action = "";
    $tab = "";

    switch ($request_type) {
        case "Checking Reject":
            $title = "Expense Application with Request No." . $request_no . " was Rejected";
            $action = "Checker";
            $conten1 = "<p>Your expense application was rejected by " . $action . ". Following are the details:</p>";
            $tab = "<p>Please log on to Feliix >> Payment Request/Claim >> Expense Apply/Liquidate >> Tab Records to view the expense application or re-submit.</p>";
            break;
        case "OP Review Reject To User":
            $title = "Expense Application with Request No." . $request_no . " was Rejected";
            $action = "OP";
            $conten1 = "<p>Your expense application was rejected by " . $action . ". Following are the details:</p>";
            $tab = "<p>Please log on to Feliix >> Payment Request/Claim >> Expense Apply/Liquidate >> Tab Records to view the expense application or re-submit.</p>";
            break;
        case "OP Review Reject To Checker":
            $title = "Expense Application for Re-Check: Request No." . $request_no . " from " . $user_name;
            $action = "OP";
            $conten1 = "<p>An expense application was rejected by " . $action . " and is waiting for you to re-check. Following are the details:</p>";
            $tab = "<p>Please log on to Feliix >> Admin Section >> Expense Review >> Tab Check to view the expense application.</p>";
            break;
        case "MD Review Reject To User":
            $title = "Expense Application with Request No." . $request_no . " was Rejected";
            $action = "MD";
            $conten1 = "<p>Your expense application was rejected by " . $action . ". Following are the details:</p>";
            $tab = "<p>Please log on to Feliix >> Payment Request/Claim >> Expense Apply/Liquidate >> Tab Records to view the expense application or re-submit.</p>";
            break;
        case "MD Review Reject To Checker":
            $title = "Expense Application for Re-Check: Request No." . $request_no . " from " . $user_name;
            $action = "MD";
            $conten1 = "<p>An expense application was rejected by " . $action . " and is waiting for you to re-check. Following are the details:</p>";
            $tab = "<p>Please log on to Feliix >> Admin Section >> Expense Review >> Tab Check to view the expense application.</p>";
            break;
        default:
            return;
            break;
     
    }

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

    $mail->AddAddress($requestor_email, $requestor);

    $mail->SetFrom("feliix.it@gmail.com", "Feliix.System");
    $mail->AddReplyTo("feliix.it@gmail.com", "Feliix.System");
    
    $mail->Subject = $title;
    $content =  "<p>Dear " . $requestor . ",</p>";
    $content = $content . $conten1;
    $content = $content . "<p>Request No.:" . $request_no . "</p>";
    $content = $content . "<p>Applicant:" . $user_name . "</p>";
    $content = $content . "<p>Department:" . $department . "</p>";
    $content = $content . "<p>Application Time:" . $ap_time . "</p>";
    $content = $content . "<p>Project Name:" . $project_name1 . "</p>";
    $content = $content . "<p>Reason:" . $project_name . "</p>";
    $content = $content . "<p>Date Needed:" . $date_request . "</p>";
    $content = $content . "<p>Total Amount Requested:" . $total_amount . "</p>";
    $content = $content . "<p>Rejection Reason:" . $reason . "</p>";
    $content = $content . "<p> </p>";
    $content = $content . $tab;
    $content = $content . "<p>URL: https://feliix.myvnc.com/</p>";

    $mail->MsgHTML($content);
    if($mail->Send()) {
        logMail($requestor_email, $content);
        return true;
    } else {
        logMail($requestor_email, $mail->ErrorInfo);
        return false;
    }

}

function send_liquidate_mail($request_no,  
                            $applicant, 
                            $requestor, 
                            $requestor_email, 
                            $department, 
                            $ap_time, 
                            $project_name1, 
                            $project_name, 
                            $date_request, 
                            $total_amount, 
                            $request_type,
                            $date_release,
                            $date_liquidate,
                            $liquidate_amount,
                            $remarks
                            )
{
    $title = "";
    $action = "";
    $tab = "";

    switch ($request_type) {
        case "Liquidated":
            $title = "Expense Application for Verify: Request No." . $request_no . " from " . $applicant;
            $action = "verify";
            $tab = "Verify";
            break;
        default:
            return;
            break;
    }

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

    $mail->AddAddress($requestor_email, $requestor);

    $mail->SetFrom("feliix.it@gmail.com", "Feliix.System");
    $mail->AddReplyTo("feliix.it@gmail.com", "Feliix.System");
    
    $mail->Subject = $title;
    $content =  "<p>Dear " . $requestor . ",</p>";
    $content = $content . "<p>An expense application is waiting for you to " . $action . ". Following are the details:</p>";
    $content = $content . "<p>Request No.:" . $request_no . "</p>";
    $content = $content . "<p>Applicant:" . $applicant . "</p>";
    $content = $content . "<p>Department:" . $department . "</p>";
    $content = $content . "<p>Application Time:" . $ap_time . "</p>";
    $content = $content . "<p>Project Name:" . $project_name1 . "</p>";
    $content = $content . "<p>Reason:" . $project_name . "</p>";
    $content = $content . "<p>Date Needed:" . $date_request . "</p>";
    $content = $content . "<p>Total Amount Requested:" . $total_amount . "</p>";
    $content = $content . "<p>Date Released:" . $date_release . "</p>";
    $content = $content . "<p>Date Liquidated:" . $date_liquidate . "</p>";
    $content = $content . "<p>Amount Liquidated:" . $liquidate_amount . "</p>";
    $content = $content . "<p>Remarks:" . $remarks . "</p>";
    $content = $content . "<p> </p>";
    $content = $content . "<p>Please log on to Feliix >> Admin Section >> Expense Review >> Tab " . $tab . " to view the expense application.</p>";
    $content = $content . "<p>URL: https://feliix.myvnc.com/</p>";

    $mail->MsgHTML($content);
    if($mail->Send()) {
        logMail($requestor_email, $content);
        return true;
    } else {
        logMail($requestor_email, $mail->ErrorInfo);
        return false;
    }
}

function send_expense_mail($request_no,  $applicant, $requestor, $requestor_email, $department, $ap_time, $project_name1, $project_name, $date_request, $total_amount, $request_type)
{
    $title = "";
    $action = "";
    $tab = "";

    switch ($request_type) {
        case "Apply":
            $title = "Expense Application for Check: Request No." . $request_no . " from " . $applicant;
            $action = "check";
            $tab = "Check ";
            break;
        case "Send To OP":
            $title = "Expense Application for Approve: Request No." . $request_no . " from " . $applicant;
            $action = "approve";
            $tab = "Review";
            break;
        case "Send To MD":
            $title = "Expense Application for approve: Request No." . $request_no . " from " . $applicant;
            $action = "approve";
            $tab = "Review";
            break;
        case "OP Send To MD":
            $title = "Expense Application for approve: Request No." . $request_no . " from " . $applicant;
            $action = "approve";
            $tab = "Review";
            break;
        case "Approve_MD":
            $title = "Expense Application for approve: Request No." . $request_no . " from " . $applicant;
            $action = "approve";
            $tab = "Review";
            break;
        case "MD Send To Releaser":
            $title = "Expense Application for Release: Request No." . $request_no . " from " . $applicant;
            $action = "release";
            $tab = "Release";
            break;
        default:
            return;
            break;
    }

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

    $mail->AddAddress($requestor_email, $requestor);

    $mail->SetFrom("feliix.it@gmail.com", "Feliix.System");
    $mail->AddReplyTo("feliix.it@gmail.com", "Feliix.System");
    
    $mail->Subject = $title;
    $content =  "<p>Dear " . $requestor . ",</p>";
    $content = $content . "<p>An expense application is waiting for you to " . $action . ". Following are the details:</p>";
    $content = $content . "<p>Request No.:" . $request_no . "</p>";
    $content = $content . "<p>Applicant:" . $applicant . "</p>";
    $content = $content . "<p>Department:" . $department . "</p>";
    $content = $content . "<p>Application Time:" . $ap_time . "</p>";
    $content = $content . "<p>Project Name:" . $project_name1 . "</p>";
    $content = $content . "<p>Reason:" . $project_name . "</p>";
    $content = $content . "<p>Date Needed:" . $date_request . "</p>";
    $content = $content . "<p>Total Amount Requested:" . $total_amount . "</p>";
    $content = $content . "<p> </p>";
    $content = $content . "<p>Please log on to Feliix >> Admin Section >> Expense Review >> Tab " . $tab . " to view the expense application.</p>";
    $content = $content . "<p>URL: https://feliix.myvnc.com/</p>";

    $mail->MsgHTML($content);
    if($mail->Send()) {
        logMail($requestor_email, $content);
        return true;
    } else {
        logMail($requestor_email, $mail->ErrorInfo);
        return false;
    }
}


function batch_liquidate_notify_mail($request_no, $user_name, $user_email, $department, $ap_time, $project_name1, $project_name, $date_request, $total_amount, $reason, $date_release)
{

    $title = "Expense Application with Request No." . $request_no . " Needs Liquidation";
   
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

    $mail->AddAddress($user_email, $user_name);

    $mail->SetFrom("feliix.it@gmail.com", "Feliix.System");
    $mail->AddReplyTo("feliix.it@gmail.com", "Feliix.System");
    
    $mail->Subject = $title;
    $content =  "<p>Dear " . $user_name . ",</p>";
    $content = $content . "<p>An expense application is waiting for you to liquidate. Following are the details:</p>";
    $content = $content . "<p>Request No.:" . $request_no . "</p>";
    $content = $content . "<p>Applicant:" . $user_name . "</p>";
    $content = $content . "<p>Department:" . $department . "</p>";
    $content = $content . "<p>Application Time:" . $ap_time . "</p>";
    $content = $content . "<p>Project Name:" . $project_name1 . "</p>";
    $content = $content . "<p>Reason:" . $project_name . "</p>";
    $content = $content . "<p>Date Needed:" . $date_request . "</p>";
    $content = $content . "<p>Total Amount Requested:" . $total_amount . "</p>";
    $content = $content . "<p>Date Released:" . $date_release . "</p>";
    $content = $content . "<p> </p>";
    $content = $content . "<p>Please log on to Feliix >> Payment Request/Claim >> Expense Apply/Liquidate >> Tab Liquidate to view the expense application.</p>";
    $content = $content . "<p>URL: https://feliix.myvnc.com/</p>";

    $mail->MsgHTML($content);
    if($mail->Send()) {
        logMail($user_email, $content);
        return true;
    } else {
        logMail($user_email, $mail->ErrorInfo);
        return false;
    }

}


function task_notify01($old_status, $task_status, $project_name, $task_name, $stage, $stages_status, $create_id, $assignee, $collaborator, $due_date, $detail, $stage_id)
{

    $tab = '<p>Status of task "' . $task_name . '" changed from ' . $old_status . ' to ' . $task_status . '. Following are the details:</p>';

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

    $notifior = array();

    $creators = "";
    $collaborators = "";
    $assignees = "";

    $notifior = GetNotifiers($assignee);
    foreach($notifior as &$list)
    {
        $mail->AddAddress($list["email"], $list["username"]);
        $assignees = $assignees . $list["username"] . ", ";
    }

    $notifior = GetNotifiers($collaborator);
    foreach($notifior as &$list)
    {
        $mail->AddAddress($list["email"], $list["username"]);
        $collaborators = $collaborators . $list["username"] . ", ";
    }

    $notifior = GetNotifiers($create_id);
    foreach($notifior as &$list)
    {
        $mail->AddCC($list["email"], $list["username"]);
        $creators = $creators . $list["username"] . ", ";
    }

    $creators = rtrim($creators, ", ");
    $assignees = rtrim($assignees, ", ");
    $collaborators = rtrim($collaborators, ", ");
    
    $mail->SetFrom("feliix.it@gmail.com", "Feliix.System");
    $mail->AddReplyTo("feliix.it@gmail.com", "Feliix.System");

    $title = "[Task Notification] " . $project_name . " - " . $task_name . " ";
    
    $mail->Subject = $title;
    $content =  "<p>Dear all,</p>";
    $content = $content . $tab;
    $content = $content . "<p>Project Name:" . $project_name . "</p>";
    $content = $content . "<p>Stage:" . $stage . "</p>";
    $content = $content . "<p>Task:" . $task_name . "</p>";
    $content = $content . "<p>Task Status:" . $old_status . ' => ' . $task_status . "</p>";
    $content = $content . "<p>Creator:" . $creators . "</p>";
    $content = $content . "<p>Assignee:" . $assignees . "</p>";
    $content = $content . "<p>Collaborator:" . $collaborators . "</p>";
    $content = $content . "<p>Due Date:" . $due_date . "</p>";
    $content = $content . "<p>Description:" . $detail . "</p>";
    $content = $content . "<p> </p>";
    $content = $content . "<p>Please click this link to view the target webpage: </p>";
    $content = $content . "<p>https://feliix.myvnc.com/project03_other?sid=" . $stage_id . "</p>";

    $mail->MsgHTML($content);
    if($mail->Send()) {
        logMail($creators, $content);
        return true;
    } else {
        logMail($creators, $mail->ErrorInfo);
        return false;
    }

}

function task_notify02($old_status, $task_status, $project_name, $task_name, $stage, $stages_status, $create_id, $assignee, $collaborator, $due_date, $detail, $stage_id)
{

    $tab = "<p>A task was revised and needs you to follow. Below is the details:</p>";

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

    $notifior = array();

    $creators = "";
    $collaborators = "";
    $assignees = "";

    $notifior = GetNotifiers($assignee);
    foreach($notifior as &$list)
    {
        $mail->AddAddress($list["email"], $list["username"]);
        $assignees = $assignees . $list["username"] . ", ";
    }

    $notifior = GetNotifiers($collaborator);
    foreach($notifior as &$list)
    {
        $mail->AddAddress($list["email"], $list["username"]);
        $collaborators = $collaborators . $list["username"] . ", ";
    }

    $notifior = GetNotifiers($create_id);
    foreach($notifior as &$list)
    {
        $mail->AddCC($list["email"], $list["username"]);
        $creators = $creators . $list["username"] . ", ";
    }

    $creators = rtrim($creators, ", ");
    $assignees = rtrim($assignees, ", ");
    $collaborators = rtrim($collaborators, ", ");
    
    $mail->SetFrom("feliix.it@gmail.com", "Feliix.System");
    $mail->AddReplyTo("feliix.it@gmail.com", "Feliix.System");

    $title = "[Task Notification] " . $project_name . " - " . $task_name . " ";
    
    $mail->Subject = $title;
    $content =  "<p>Dear all,</p>";
    $content = $content . $tab;
    $content = $content . "<p>Project Name:" . $project_name . "</p>";
    $content = $content . "<p>Stage:" . $stages_status . "</p>";
    $content = $content . "<p>Task:" . $task_name . "</p>";

    if($old_status != $task_status)
        $content = $content . "<p>Task Status:" . $old_status . ' => ' . $task_status . "</p>";
    else
        $content = $content . "<p>Task Status:" . $task_status . "</p>";

    $content = $content . "<p>Creator:" . $creators . "</p>";
    $content = $content . "<p>Assignee:" . $assignees . "</p>";
    $content = $content . "<p>Collaborator:" . $collaborators . "</p>";
    $content = $content . "<p>Due Date:" . $due_date . "</p>";
    $content = $content . "<p>Description:" . $detail . "</p>";
    $content = $content . "<p> </p>";
    $content = $content . "<p>Please click this link to view the target webpage: </p>";
    $content = $content . "<p>https://feliix.myvnc.com/project03_other?sid=" . $stage_id . "</p>";

    $mail->MsgHTML($content);
    if($mail->Send()) {
        logMail($creators, $content);
        return true;
    } else {
        logMail($creators, $mail->ErrorInfo);
        return false;
    }

}

function GetNotifiers($id)
{
    $database = new Database();
    $db = $database->getConnection();

    $sql = "SELECT user.id, username, email, title, department FROM user 
    LEFT JOIN user_department ON user.apartment_id = user_department.id LEFT JOIN user_title ON user.title_id = user_title.id
        WHERE user.id in (" . $id . ")";

    $merged_results = array();

    $stmt = $db->prepare($sql);
    $stmt->execute();

    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        $merged_results[] = $row;
    }

    return $merged_results;
}

function GetPettyVoidNotifiers()
{
    $database = new Database();
    $db = $database->getConnection();

    $sql = "SELECT username, email FROM expense_flow ap
    LEFT JOIN user u ON ap.uid = u.id 
    WHERE flow in (1, 2, 3)";

    $merged_results = array();

    $stmt = $db->prepare($sql);
    $stmt->execute();

    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        $merged_results[] = $row;
    }

    return $merged_results;
}


function project01_notify_mail($request_type, $project_name, $username, $created_at, $category, $client_type, $priority, $project_status, $estimate_close_prob, $project_id, $project_creator_id)
{
    $tab = "";

    switch ($request_type) {
        case "01":
            $title = 'Project "'. $project_name .'" was created by ' . $username;
            $tab = '<p>A new project named "'. $project_name .'" was created by ' . $username . '. Following are the details:</p>';
            break;
        default:
            $title = 'Project "'. $project_name .'" was created by ' . $username;
            $tab = '<p>A new project named "'. $project_name .'" was created by ' . $username . '. Following are the details:</p>';
    }

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

    $notifior = array();

    $notifior = GetNotifiers($project_creator_id);
    foreach($notifior as &$list)
    {
        $mail->AddCC($list["email"], $list["username"]);
    }

    $notifior = GetProject01NotifiersByCatagory($category);
    foreach($notifior as &$list)
    {
        $mail->AddAddress($list["email"], $list["username"]);
    }

    $mail->SetFrom("feliix.it@gmail.com", "Feliix.System");
    $mail->AddReplyTo("feliix.it@gmail.com", "Feliix.System");
    
    $mail->Subject = $title;
    $content =  "<p>Dear all,</p>";
    $content = $content . $tab;
    $content = $content . "<p>Project Name:" . $project_name . "</p>";
    $content = $content . "<p>Project Category:" . $category . "</p>";
    $content = $content . "<p>Client Type:" . $client_type . "</p>";
    $content = $content . "<p>Priority:" . $priority . "</p>";
    $content = $content . "<p>Project Status:" . $project_status . "</p>";
    $content = $content . "<p>Estimated Closing Prob.:" . $estimate_close_prob . "</p>";
    $content = $content . "<p>Project Creator:" . $username . " at " . $created_at . "</p>";

    $content = $content . "<p> </p>";
    $content = $content . "<p>Please click this link to view the target webpage: </p>";
    $content = $content . "<p>https://feliix.myvnc.com/project02?p=" . $project_id . "</p>";

    $mail->MsgHTML($content);
    if($mail->Send()) {
        logMail($username, $content);
        return true;
    } else {
        logMail($username, $mail->ErrorInfo);
        return false;
    }

}

function project02_stage_notify_mail($stage_name, $project_name, $username, $created_at, $stage_status, $project_id, $project_creator_id, $category)
{

    $title = 'Stage "'. $stage_name .'" was created in Project "' . $project_name . '" ';
    $tab = '<p>Stage "'. $stage_name .'" was created in Project "' . $project_name . '" by ' . $username . '. Following are the details:</p>';
    
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

    $notifior = array();

    $notifior = GetNotifiers($project_creator_id);
    foreach($notifior as &$list)
    {
        $mail->AddCC($list["email"], $list["username"]);
    }

    $notifior = GetProject01NotifiersByCatagory($category);
    foreach($notifior as &$list)
    {
        $mail->AddAddress($list["email"], $list["username"]);
    }

    $mail->SetFrom("feliix.it@gmail.com", "Feliix.System");
    $mail->AddReplyTo("feliix.it@gmail.com", "Feliix.System");
    
    $mail->Subject = $title;
    $content =  "<p>Dear all,</p>";
    $content = $content . $tab;
    $content = $content . "<p>Project Name:" . $project_name . "</p>";
    $content = $content . "<p>Stage:" . $stage_name . "</p>";
    $content = $content . "<p>Status of Stage:" . $stage_status . "</p>";
    $content = $content . "<p>Stage Creator:" . $username . " at " . $created_at . "</p>";

    $content = $content . "<p> </p>";
    $content = $content . "<p>Please click this link to view the target webpage: </p>";
    $content = $content . "<p>https://feliix.myvnc.com/project02?p=" . $project_id . "</p>";

    $mail->MsgHTML($content);
    if($mail->Send()) {
        logMail($username, $content);
        return true;
    } else {
        logMail($username, $mail->ErrorInfo);
        return false;
    }

}

function project03_stage_client_task_notify_mail($project_name, $username, $created_at, $project_creator_id, $message, $category, $stage_id)
{
    $title = '[Message Notification] New message in client stage of project "' . $project_name . '" ';
    $tab = '<p>A new message in client stage of project "' . $project_name . '" was created by ' . $username . '. Following are the details:</p>';
    
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

    $notifior = array();

    $notifior = GetNotifiers($project_creator_id);
    foreach($notifior as &$list)
    {
        $mail->AddCC($list["email"], $list["username"]);
    }

    $notifior = GetProjectNotifiersByCatagory($category);
    foreach($notifior as &$list)
    {
        $mail->AddAddress($list["email"], $list["username"]);
    }

    $mail->SetFrom("feliix.it@gmail.com", "Feliix.System");
    $mail->AddReplyTo("feliix.it@gmail.com", "Feliix.System");
    
    $mail->Subject = $title;
    $content =  "<p>Dear all,</p>";
    $content = $content . $tab;
    $content = $content . "<p>Project Name:" . $project_name . "</p>";
    $content = $content . "<p>Message Creator:" . $username . " at " . $created_at . "</p>";
    $content = $content . "<p>Content:" . $message . "</p>";

    $content = $content . "<p> </p>";
    $content = $content . "<p>Please click this link to view the target webpage: </p>";
    $content = $content . "<p>https://feliix.myvnc.com/project03_client?sid=" . $stage_id . "</p>";

    $mail->MsgHTML($content);
    if($mail->Send()) {
        logMail($username, $content);
        return true;
    } else {
        logMail($username, $mail->ErrorInfo);
        return false;
    }
}

function project02_status_change_notify_mail($project_name, $project_category, $username, $created_at, $client_type, $priority, $estimate_close_prob, $project_status, $pre_status, $project_id, $create_id)
{

    $title = 'Status of project "' . $project_name . '" changed to "' . $project_status . '" ';
    $tab = '<p>Status of project "' . $project_name . '" changed to "' . $project_status . '". Following are the details:</p>';
    
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

    $notifior = array();

    $notifior = GetNotifiers($create_id);
    foreach($notifior as &$list)
    {
        $mail->AddCC($list["email"], $list["username"]);
    }

    $notifior = GetProject01NotifiersByCatagory($project_category);
    foreach($notifior as &$list)
    {
        $mail->AddAddress($list["email"], $list["username"]);
    }

    $mail->SetFrom("feliix.it@gmail.com", "Feliix.System");
    $mail->AddReplyTo("feliix.it@gmail.com", "Feliix.System");
    
    $mail->Subject = $title;
    $content =  "<p>Dear all,</p>";
    $content = $content . $tab;
    $content = $content . "<p>Project Name:" . $project_name . "</p>";
    $content = $content . "<p>Project Category:" . $project_category . "</p>";
    $content = $content . "<p>Client Type:" . $client_type . "</p>";
    $content = $content . "<p>Priority:" . $priority . "</p>";
    $content = $content . "<p>Project Status:" . $pre_status . " => " . $project_status.  "</p>";
    $content = $content . "<p>Estimated Closing Prob.:" . $estimate_close_prob . "</p>";
    $content = $content . "<p>Project Creator:" . $username . " at " . $created_at . "</p>";

    $content = $content . "<p> </p>";
    $content = $content . "<p>Please click this link to view the target webpage: </p>";
    $content = $content . "<p>https://feliix.myvnc.com/project02?p=" . $project_id . "</p>";

    $mail->MsgHTML($content);
    if($mail->Send()) {
        logMail($username, $content);
        return true;
    } else {
        logMail($username, $mail->ErrorInfo);
        return false;
    }

}

function GetProject01NotifiersByCatagory($catagory)
{
    $database = new Database();
    $db = $database->getConnection();

    if($catagory == 'Office Systems')
    {
        $sql = "
            SELECT username, email, title 
            FROM user u
            LEFT JOIN user_title ut
            ON u.title_id = ut.id 
            WHERE title IN(
                'Assistant Sales Manager',
                'Sales Manager',
                'Office Systems Manager',
                'Office Systems Assistant Manager',
                'Operations Manager',
                'Managing Director') ";
    }
    else
    {
        $sql = "
            SELECT username, email, title 
            FROM user u
            LEFT JOIN user_title ut
            ON u.title_id = ut.id 
            WHERE title IN(
                'Assistant Sales Manager',
                'Sales Manager',
                'Lighting Manager',
                'Lighting Assistant Manager',
                'Operations Manager',
                'Managing Director') ";
    }

    $merged_results = array();

    $stmt = $db->prepare($sql);
    $stmt->execute();

    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        $merged_results[] = $row;
    }

    return $merged_results;
}

function GetProjectNotifiersByCatagory($catagory)
{
    $database = new Database();
    $db = $database->getConnection();

    if($catagory == 'Office Systems')
    {
        $sql = "
            SELECT username, email, title 
            FROM user u
            LEFT JOIN user_title ut
            ON u.title_id = ut.id 
            WHERE title IN(
                'Office Systems Manager',
                'Managing Director') ";
    }
    else
    {
        $sql = "
            SELECT username, email, title 
            FROM user u
            LEFT JOIN user_title ut
            ON u.title_id = ut.id 
            WHERE title IN(
                'Lighting Manager',
                'Managing Director') ";
    }

    $merged_results = array();

    $stmt = $db->prepare($sql);
    $stmt->execute();

    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        $merged_results[] = $row;
    }

    return $merged_results;
}

function GetProjectNotifiers()
{
    $database = new Database();
    $db = $database->getConnection();

    $sql = "
        SELECT username, email, title 
        FROM user u
        LEFT JOIN user_title ut
        ON u.title_id = ut.id 
        WHERE title IN(
            'Jr. Account Executive',
            'Account Executive',
            'Sr. Account Executive',
            'Assistant Sales Manager',
            'Sales Manager',
            'Lighting Manager',
            'Lighting Assistant Manager',
            'Office Systems Manager',
            'Office Systems Assistant Manager',
            'Operations Manager',
            'Managing Director') ";

    $merged_results = array();

    $stmt = $db->prepare($sql);
    $stmt->execute();

    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        $merged_results[] = $row;
    }

    return $merged_results;
}

?>