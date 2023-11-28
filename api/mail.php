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
        $content_b = substr($content_b, 0, 2000);

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

function batch_performance_evaluate_adm_notify_mail($s_date, $e_date, $dead_date, $adm_id, $emp_id){
    $conf = new Conf();

    $mail = new PHPMailer();
    $mail->IsSMTP();
    $mail->Mailer = "smtp";
    $mail->CharSet = 'UTF-8';
    $mail->Encoding = 'base64';

    // $mail->SMTPDebug  = 0;
    // $mail->SMTPAuth   = true;
    // $mail->SMTPSecure = "ssl";
    // $mail->Port       = 465;
    // $mail->SMTPKeepAlive = true;
    // $mail->Host       = $conf::$mail_host;
    // $mail->Username   = $conf::$mail_username;
    // $mail->Password   = $conf::$mail_password;

    $mail = SetupMail($mail, $conf);

    $admin = GetNotifiers($adm_id);
    $admin_name = '';
    $admin_email = '';
    $admin_title = '';
    $admin_department = '';
    foreach($admin as &$list)
    {
        $admin_name = $list["username"];
        $admin_email = $list["email"];
        $admin_title = $list["title"];
        $admin_department = $list["department"];
    }

    $emp = GetNotifiers($emp_id);
    $emp_name = '';
    $emp_email = '';
    $emp_title = '';
    $emp_department = '';
    foreach($emp as &$list)
    {
        $emp_name = $list["username"];
        $emp_email = $list["email"];
        $emp_title = $list["title"];
        $emp_department = $list["department"];
    }

    $mail->AddAddress($admin_email, $admin_name);

    $title = "[Notification] Please Fill out Your Performance Review Form ASAP";

    $mail->IsHTML(true);

    $mail->SetFrom("feliix.it@gmail.com", "Feliix.System");
    $mail->AddReplyTo("feliix.it@gmail.com", "Feliix.System");

    $mail->Subject = $title;
    $content =  "<p>Dear " . $admin_name . ",</p>";
    $content = $content . "<p>Deadline of the below performance review item was " . $dead_date . ". Please fill out your performance review form as soon as possible. Following are the details:</p>";
    $content = $content . "<p>Employee Name: " . $emp_name . "</p>";
    $content = $content . "<p>Employee Position: " . $emp_title . "</p>";
    $content = $content . "<p>Employee Department: " . $emp_department . "</p>";
    $content = $content . "<p>Supervisor: " . $admin_name . "</p>";
    $content = $content . "<p>Review Period: " . $s_date . " ~ " . $e_date . "</p>";
    
    $content = $content . "<p> </p>";

    $content = $content . "<p>Please log on to Feliix >> Performance Evaluation >> Tab Performance Review to fill out the performance review form.</p>";
    $content = $content . "<p>URL: " . $conf::$mail_ip . "</p>";


    $mail->MsgHTML($content);
    if($mail->Send()) {
        logMail($admin_email, $content);
        return true;
    } else {
        logMail($admin_email, $mail->ErrorInfo . $content);
        return false;
    }
}

function batch_performance_evaluate_adm_notify_mail_single($s_date, $dead_date, $adm_id, $emp_id){
    $conf = new Conf();

    $mail = new PHPMailer();
    $mail->IsSMTP();
    $mail->Mailer = "smtp";
    $mail->CharSet = 'UTF-8';
    $mail->Encoding = 'base64';

    // $mail->SMTPDebug  = 0;
    // $mail->SMTPAuth   = true;
    // $mail->SMTPSecure = "ssl";
    // $mail->Port       = 465;
    // $mail->SMTPKeepAlive = true;
    // $mail->Host       = $conf::$mail_host;
    // $mail->Username   = $conf::$mail_username;
    // $mail->Password   = $conf::$mail_password;

    $mail = SetupMail($mail, $conf);

    
    $admin = GetNotifiers($adm_id);
    $admin_name = '';
    $admin_email = '';
    $admin_title = '';
    $admin_department = '';
    foreach($admin as &$list)
    {
        $admin_name = $list["username"];
        $admin_email = $list["email"];
        $admin_title = $list["title"];
        $admin_department = $list["department"];
    }

    $emp = GetNotifiers($emp_id);
    $emp_name = '';
    $emp_email = '';
    $emp_title = '';
    $emp_department = '';
    foreach($emp as &$list)
    {
        $emp_name = $list["username"];
        $emp_email = $list["email"];
        $emp_title = $list["title"];
        $emp_department = $list["department"];
    }

    $mail->AddAddress($admin_email, $admin_name);

    $title = "[Notification] Please Fill out Your Performance Review Form ASAP";

    $mail->IsHTML(true);

    $mail->SetFrom("feliix.it@gmail.com", "Feliix.System");
    $mail->AddReplyTo("feliix.it@gmail.com", "Feliix.System");

    $mail->Subject = $title;
    $content =  "<p>Dear " . $admin_name . ",</p>";
    $content = $content . "<p>Deadline of the below performance review item was " . $dead_date . ". Please fill out your performance review form as soon as possible. Following are the details:</p>";
    $content = $content . "<p>Employee Name: " . $emp_name . "</p>";
    $content = $content . "<p>Employee Position: " . $emp_title . "</p>";
    $content = $content . "<p>Employee Department: " . $emp_department . "</p>";
    $content = $content . "<p>Supervisor: " . $admin_name . "</p>";
    $content = $content . "<p>Review Period: " . $s_date . "</p>";
    
    $content = $content . "<p> </p>";

    $content = $content . "<p>Please log on to Feliix >> Performance Evaluation >> Tab Performance Review to fill out the performance review form.</p>";
    $content = $content . "<p>URL: " . $conf::$mail_ip . "</p>";


    $mail->MsgHTML($content);
    if($mail->Send()) {
        logMail($admin_email, $content);
        return true;
    } else {
        logMail($admin_email, $mail->ErrorInfo . $content);
        return false;
    }
}

function batch_performance_evaluate_emp_notify_mail($s_date, $e_date, $dead_date, $adm_id, $emp_id){
    $conf = new Conf();

    $mail = new PHPMailer();
    $mail->IsSMTP();
    $mail->Mailer = "smtp";
    $mail->CharSet = 'UTF-8';
    $mail->Encoding = 'base64';

    // $mail->SMTPDebug  = 0;
    // $mail->SMTPAuth   = true;
    // $mail->SMTPSecure = "ssl";
    // $mail->Port       = 465;
    // $mail->SMTPKeepAlive = true;
    // $mail->Host       = $conf::$mail_host;
    // $mail->Username   = $conf::$mail_username;
    // $mail->Password   = $conf::$mail_password;

    $mail = SetupMail($mail, $conf);

    
    $admin = GetNotifiers($adm_id);
    $admin_name = '';
    $admin_email = '';
    $admin_title = '';
    $admin_department = '';
    foreach($admin as &$list)
    {
        $admin_name = $list["username"];
        $admin_email = $list["email"];
        $admin_title = $list["title"];
        $admin_department = $list["department"];
    }

    $emp = GetNotifiers($emp_id);
    $emp_name = '';
    $emp_email = '';
    $emp_title = '';
    $emp_department = '';
    foreach($emp as &$list)
    {
        $emp_name = $list["username"];
        $emp_email = $list["email"];
        $emp_title = $list["title"];
        $emp_department = $list["department"];
    }

    $mail->AddAddress($emp_email, $emp_name);

    $title = "[Notification] Please Fill out Your Performance Review Form ASAP";

    $mail->IsHTML(true);

    $mail->SetFrom("feliix.it@gmail.com", "Feliix.System");
    $mail->AddReplyTo("feliix.it@gmail.com", "Feliix.System");

    $mail->Subject = $title;
    $content =  "<p>Dear " . $emp_name . ",</p>";
    $content = $content . "<p>Deadline of the below performance review item was " . $dead_date . ". Please fill out your performance review form as soon as possible. Following are the details:</p>";
    $content = $content . "<p>Employee Name: " . $emp_name . "</p>";
    $content = $content . "<p>Employee Position: " . $emp_title . "</p>";
    $content = $content . "<p>Employee Department: " . $emp_department . "</p>";
    $content = $content . "<p>Supervisor: " . $admin_name . "</p>";
    $content = $content . "<p>Review Period: " . $s_date . " ~ " . $e_date . "</p>";
    
    $content = $content . "<p> </p>";

    $content = $content . "<p>Please log on to Feliix >> Performance Evaluation >> Tab Performance Review to fill out the performance review form.</p>";
    $content = $content . "<p>URL: " . $conf::$mail_ip . "</p>";


    $mail->MsgHTML($content);
    if($mail->Send()) {
        logMail($admin_email, $content);
        return true;
    } else {
        logMail($admin_email, $mail->ErrorInfo . $content);
        return false;
    }
}

function batch_performance_evaluate_emp_notify_mail_single($s_date, $dead_date, $adm_id, $emp_id){
    $conf = new Conf();

    $mail = new PHPMailer();
    $mail->IsSMTP();
    $mail->Mailer = "smtp";
    $mail->CharSet = 'UTF-8';
    $mail->Encoding = 'base64';

    // $mail->SMTPDebug  = 0;
    // $mail->SMTPAuth   = true;
    // $mail->SMTPSecure = "ssl";
    // $mail->Port       = 465;
    // $mail->SMTPKeepAlive = true;
    // $mail->Host       = $conf::$mail_host;
    // $mail->Username   = $conf::$mail_username;
    // $mail->Password   = $conf::$mail_password;

    $mail = SetupMail($mail, $conf);

    
    $admin = GetNotifiers($adm_id);
    $admin_name = '';
    $admin_email = '';
    $admin_title = '';
    $admin_department = '';
    foreach($admin as &$list)
    {
        $admin_name = $list["username"];
        $admin_email = $list["email"];
        $admin_title = $list["title"];
        $admin_department = $list["department"];
    }

    $emp = GetNotifiers($emp_id);
    $emp_name = '';
    $emp_email = '';
    $emp_title = '';
    $emp_department = '';
    foreach($emp as &$list)
    {
        $emp_name = $list["username"];
        $emp_email = $list["email"];
        $emp_title = $list["title"];
        $emp_department = $list["department"];
    }

    $mail->AddAddress($emp_email, $emp_name);

    $title = "[Notification] Please Fill out Your Performance Review Form ASAP";

    $mail->IsHTML(true);

    $mail->SetFrom("feliix.it@gmail.com", "Feliix.System");
    $mail->AddReplyTo("feliix.it@gmail.com", "Feliix.System");

    $mail->Subject = $title;
    $content =  "<p>Dear " . $emp_name . ",</p>";
    $content = $content . "<p>Deadline of the below performance review item was " . $dead_date . ". Please fill out your performance review form as soon as possible. Following are the details:</p>";
    $content = $content . "<p>Employee Name: " . $emp_name . "</p>";
    $content = $content . "<p>Employee Position: " . $emp_title . "</p>";
    $content = $content . "<p>Employee Department: " . $emp_department . "</p>";
    $content = $content . "<p>Supervisor: " . $admin_name . "</p>";
    $content = $content . "<p>Review Period: " . $s_date  . "</p>";
    
    $content = $content . "<p> </p>";

    $content = $content . "<p>Please log on to Feliix >> Performance Evaluation >> Tab Performance Review to fill out the performance review form.</p>";
    $content = $content . "<p>URL: " . $conf::$mail_ip . "</p>";


    $mail->MsgHTML($content);
    if($mail->Send()) {
        logMail($admin_email, $content);
        return true;
    } else {
        logMail($admin_email, $mail->ErrorInfo . $content);
        return false;
    }
}

function batch_performance_review_notify_mail($_name, $_email, $s_date, $e_date)
{
    $conf = new Conf();

    $mail = new PHPMailer();
    $mail->IsSMTP();
    $mail->Mailer = "smtp";
    $mail->CharSet = 'UTF-8';
    $mail->Encoding = 'base64';

    // $mail->SMTPDebug  = 0;
    // $mail->SMTPAuth   = true;
    // $mail->SMTPSecure = "ssl";
    // $mail->Port       = 465;
    // $mail->SMTPKeepAlive = true;
    // $mail->Host       = $conf::$mail_host;
    // $mail->Username   = $conf::$mail_username;
    // $mail->Password   = $conf::$mail_password;

    $mail = SetupMail($mail, $conf);
 
    $mail->AddAddress($_email, $_name);
    

    $title = "[Notification] It's Time to Create Performance Review Item for Subordinates";

    $mail->IsHTML(true);

    $mail->SetFrom("feliix.it@gmail.com", "Feliix.System");
    $mail->AddReplyTo("feliix.it@gmail.com", "Feliix.System");

    $mail->Subject = $title;
    $content =  "<p>Dear " . $_name . ",</p>";
    $content = $content . "<p>It's time to create performance review item for your subordinates. The review period of the performance review item this time is " . $s_date . " ~ " . $e_date . ".</p>";
    $content = $content . "<p> </p>";

    $content = $content . "<p>Please log on to Feliix >> Performance Evaluation >> Tab Performance Review to create the performance review item.</p>";
    $content = $content . "<p>URL: " . $conf::$mail_ip . "</p>";


    $mail->MsgHTML($content);
    if($mail->Send()) {
        logMail($_email, $content);
        return true;
    } else {
        logMail($_email, $mail->ErrorInfo . $content);
        return false;
    }
}


function send_review_mail_adm($s_date, $e_date, $adm_id, $emp_id, $dead_date){
    $conf = new Conf();

    $mail = new PHPMailer();
    $mail->IsSMTP();
    $mail->Mailer = "smtp";
    $mail->CharSet = 'UTF-8';
    $mail->Encoding = 'base64';

    // $mail->SMTPDebug  = 0;
    // $mail->SMTPAuth   = true;
    // $mail->SMTPSecure = "ssl";
    // $mail->Port       = 465;
    // $mail->SMTPKeepAlive = true;
    // $mail->Host       = $conf::$mail_host;
    // $mail->Username   = $conf::$mail_username;
    // $mail->Password   = $conf::$mail_password;

    $mail = SetupMail($mail, $conf);
    
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
    $content = $content . "<p>" . $emp_name . "'s Performance Review Form over " . $s_date . " ~ " . $e_date . " is open now and the deadline is " . $dead_date . ". As evaluation on your subordinate, you can record your scores and comments against each performance criterion. We encourage you to give your inputs on a regular basis as this will help in clear discussion between you and your subordinate.</p>";
    $content = $content . "<p> </p>";
    $content = $content . "<p>Following are the details:</p>";
    $content = $content . "<p> </p>";
    $content = $content . "<p>Employee Name: " . $emp_name . "</p>";
    $content = $content . "<p>Employee Position: " . $emp_title . "</p>";
    $content = $content . "<p>Employee Department: " . $emp_department . "</p>";
    $content = $content . "<p>Supervisor: " . $admin_name . "</p>";
    $content = $content . "<p>Review Period: " . $s_date . " ~ " . $e_date . "</p>";
    
    $content = $content . "<p> </p>";

    $content = $content . "<p>Please log on to Feliix >> Performance Evaluation >> Tab Performance Review to start the evaluation.</p>";
    $content = $content . "<p>URL: " . $conf::$mail_ip . "</p>";


    $mail->MsgHTML($content);
    if($mail->Send()) {
        logMail($admin_email, $content);
        return true;
    } else {
        logMail($admin_email, $mail->ErrorInfo . $content);
        return false;
    }
}


function send_review_mail_adm_single($s_date, $adm_id, $emp_id, $dead_date){
    $conf = new Conf();

    $mail = new PHPMailer();
    $mail->IsSMTP();
    $mail->Mailer = "smtp";
    $mail->CharSet = 'UTF-8';
    $mail->Encoding = 'base64';

    // $mail->SMTPDebug  = 0;
    // $mail->SMTPAuth   = true;
    // $mail->SMTPSecure = "ssl";
    // $mail->Port       = 465;
    // $mail->SMTPKeepAlive = true;
    // $mail->Host       = $conf::$mail_host;
    // $mail->Username   = $conf::$mail_username;
    // $mail->Password   = $conf::$mail_password;

    $mail = SetupMail($mail, $conf);
    
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

    $title = "[Notification] " . $emp_name . "'s Performance Review Form over " . $s_date . " is Open";

    $mail->IsHTML(true);

    $mail->SetFrom("feliix.it@gmail.com", "Feliix.System");
    $mail->AddReplyTo("feliix.it@gmail.com", "Feliix.System");

    $mail->Subject = $title;
    $content =  "<p>Dear " . $admin_name . ",</p>";
    $content = $content . "<p>" . $emp_name . "'s Performance Review Form over " . $s_date .  " is open now and the deadline is " . $dead_date . ". As evaluation on your subordinate, you can record your scores and comments against each performance criterion. We encourage you to give your inputs on a regular basis as this will help in clear discussion between you and your subordinate.</p>";
    $content = $content . "<p> </p>";
    $content = $content . "<p>Following are the details:</p>";
    $content = $content . "<p> </p>";
    $content = $content . "<p>Employee Name: " . $emp_name . "</p>";
    $content = $content . "<p>Employee Position: " . $emp_title . "</p>";
    $content = $content . "<p>Employee Department: " . $emp_department . "</p>";
    $content = $content . "<p>Supervisor: " . $admin_name . "</p>";
    $content = $content . "<p>Review Period: " . $s_date .  "</p>";
    
    $content = $content . "<p> </p>";

    $content = $content . "<p>Please log on to Feliix >> Performance Evaluation >> Tab Performance Review to start the evaluation.</p>";
    $content = $content . "<p>URL: " . $conf::$mail_ip . "</p>";


    $mail->MsgHTML($content);
    if($mail->Send()) {
        logMail($admin_email, $content);
        return true;
    } else {
        logMail($admin_email, $mail->ErrorInfo . $content);
        return false;
    }
}

function send_review_mail($s_date, $e_date, $adm_id, $emp_id, $dead_date){
    $conf = new Conf();

    $mail = new PHPMailer();
    $mail->IsSMTP();
    $mail->Mailer = "smtp";
    $mail->CharSet = 'UTF-8';
    $mail->Encoding = 'base64';

    // $mail->SMTPDebug  = 0;
    // $mail->SMTPAuth   = true;
    // $mail->SMTPSecure = "ssl";
    // $mail->Port       = 465;
    // $mail->SMTPKeepAlive = true;
    // $mail->Host       = $conf::$mail_host;
    // $mail->Username   = $conf::$mail_username;
    // $mail->Password   = $conf::$mail_password;

    $mail = SetupMail($mail, $conf);

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
    $content = $content . "<p>Your Performance Review Form over " . $s_date . " ~ " . $e_date . " is open now and the deadline is " . $dead_date . ". As self-evaluation, you can record your scores and comments against each performance criterion. We encourage you to give your inputs on a regular basis as this will help in clear discussion between you and your supervisor.</p>";
    $content = $content . "<p> </p>";
    $content = $content . "<p>Following are the details:</p>";
    $content = $content . "<p> </p>";
    $content = $content . "<p>Employee Name: " . $emp_name . "</p>";
    $content = $content . "<p>Employee Position: " . $emp_title . "</p>";
    $content = $content . "<p>Employee Department: " . $emp_department . "</p>";
    $content = $content . "<p>Supervisor: " . $admin_name . "</p>";
    $content = $content . "<p>Review Period: " . $s_date . " ~ " . $e_date . "</p>";
    
    $content = $content . "<p> </p>";

    $content = $content . "<p>Please log on to Feliix >> Performance Evaluation >> Tab Performance Review to start the evaluation.</p>";
    $content = $content . "<p>URL: " . $conf::$mail_ip . "</p>";


    $mail->MsgHTML($content);
    if($mail->Send()) {
        logMail($emp_email, $content);
        return true;
    } else {
        logMail($emp_email, $mail->ErrorInfo . $content);
        return false;
    }
}

function send_review_mail_single($s_date, $adm_id, $emp_id, $dead_date){
    $conf = new Conf();

    $mail = new PHPMailer();
    $mail->IsSMTP();
    $mail->Mailer = "smtp";
    $mail->CharSet = 'UTF-8';
    $mail->Encoding = 'base64';

    // $mail->SMTPDebug  = 0;
    // $mail->SMTPAuth   = true;
    // $mail->SMTPSecure = "ssl";
    // $mail->Port       = 465;
    // $mail->SMTPKeepAlive = true;
    // $mail->Host       = $conf::$mail_host;
    // $mail->Username   = $conf::$mail_username;
    // $mail->Password   = $conf::$mail_password;

    $mail = SetupMail($mail, $conf);
    
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

    $title = "[Notification] Your Performance Review Form over " . $s_date  . " is Open";

    $mail->IsHTML(true);

    $mail->SetFrom("feliix.it@gmail.com", "Feliix.System");
    $mail->AddReplyTo("feliix.it@gmail.com", "Feliix.System");

    $mail->Subject = $title;
    $content =  "<p>Dear " . $emp_name . ",</p>";
    $content = $content . "<p>Your Performance Review Form over " . $s_date  . " is open now and the deadline is " . $dead_date . ". As self-evaluation, you can record your scores and comments against each performance criterion. We encourage you to give your inputs on a regular basis as this will help in clear discussion between you and your supervisor.</p>";
    $content = $content . "<p> </p>";
    $content = $content . "<p>Following are the details:</p>";
    $content = $content . "<p> </p>";
    $content = $content . "<p>Employee Name: " . $emp_name . "</p>";
    $content = $content . "<p>Employee Position: " . $emp_title . "</p>";
    $content = $content . "<p>Employee Department: " . $emp_department . "</p>";
    $content = $content . "<p>Supervisor: " . $admin_name . "</p>";
    $content = $content . "<p>Review Period: " . $s_date  . "</p>";
    
    $content = $content . "<p> </p>";

    $content = $content . "<p>Please log on to Feliix >> Performance Evaluation >> Tab Performance Review to start the evaluation.</p>";
    $content = $content . "<p>URL: " . $conf::$mail_ip . "</p>";


    $mail->MsgHTML($content);
    if($mail->Send()) {
        logMail($emp_email, $content);
        return true;
    } else {
        logMail($emp_email, $mail->ErrorInfo . $content);
        return false;
    }
}

function send_check_notify_mail_new($name, $email1, $projectname, $remark, $subtime, $reason, $status, $category, $kind, $amount, $receive_date, $send_mail, $payment_method, $bank_name, $check_number, $bank_account, $invoice, $special, $final_amount)
{
    $conf = new Conf();

    $mail = new PHPMailer();
    $mail->IsSMTP();
    $mail->Mailer = "smtp";
    $mail->CharSet = 'UTF-8';
    $mail->Encoding = 'base64';

    // $mail->SMTPDebug  = 0;
    // $mail->SMTPAuth   = true;
    // $mail->SMTPSecure = "ssl";
    // $mail->Port       = 465;
    // $mail->SMTPKeepAlive = true;
    // $mail->Host       = $conf::$mail_host;
    // $mail->Username   = $conf::$mail_username;
    // $mail->Password   = $conf::$mail_password;

    $mail = SetupMail($mail, $conf);

    $mail->IsHTML(true);
    $mail->AddAddress($email1, $name);

    if($kind == 0)
        $payment = "Down Payment";

    if($kind == 1)
        $payment = "Full Payment";

    if($kind == 2)
        $payment = "2307";

    if($category == '1')
        $mail->AddAddress('johmar@feliix.com', 'Johmar Maximo');

    if($category == '2')
        $mail->AddAddress('nestor@feliix.com', 'Nestor Rosales');

    $mail->AddCC('kuan@feliix.com', 'Kuan');
    $mail->AddCC('kristel@feliix.com', 'Kristel Tan');
    $mail->AddCC('glen@feliix.com', 'Glendon Wendell Co');
    $mail->AddCC('ariel@feliix.com', 'Ariel Lin');
    //$mail->AddCC('wren@feliix.com', 'Thalassa Wren Benzon');
    //if($kind == 0)
    //    $mail->AddCC('argel.feliix@gmail.com', 'Argel Argana');

    //if($kind == 1 && $send_mail == 'true')

    //if($kind == 0 || $kind == 1)
        //$mail->AddCC('edneil@feliix.com', 'Edneil Fernandez');

    $mail->AddCC('dennis@feliix.com', 'Dennis Lin');

    $mail->SetFrom("feliix.it@gmail.com", "Feliix.System");
    $mail->AddReplyTo("feliix.it@gmail.com", "Feliix.System");
    // $mail->AddCC("tryhelpbuy@gmail.com", "tryhelpbuy");

    if($status == 'True'){
        $mail->Subject = "[PAYMENT CONFIRMED] Checked: " . $status . " for " . $payment . " Proof submitted by " . $name . "(" . $projectname . ")";

        if($category == '2' && ($kind == 0 || $kind == 1)){
            $mail->AddAddress('aiza@feliix.com', 'Aiza Eisma');
            $mail->AddCC('cristina@feliix.com', 'Cristina Matining');
            $mail->AddCC('alleah.feliix@gmail.com', 'Alleah Belmonte');
        }
    }
    if($status == 'False')
        $mail->Subject = "Checked: " . $status . " for " . $payment . " Proof submitted by " . $name . "(" . $projectname . ")";


    $content = '<!DOCTYPE html>
                <html lang="en">
                <head>
                    <meta http-equiv="content-type" content="text/html; charset=utf-8"/>
                </head>
                <body>

                <div style="width: 766px; padding: 25px 70px 20px 70px; border: 2px solid rgb(230,230,230); color: black;">

                    <table style="width: 100%;">
                        <tbody>
                        <tr>
                            <td style="font-size: 20px; padding: 20px 0 20px 5px;">';
    $content = $content . "Dear " . $name . ",";
    $content = $content . '</td>
                        </tr>
                        <tr>
                            <td style="font-size: 20px; padding: 0 0 20px 5px; text-align: justify;">';


    // 判斷 Project Type 和 Proof Kind 和 Project Final Amount 來決定 稱呼者名稱
    // Project Type = Normal
    if($special == "")
	$content = $content . "Glen has checked " . $payment . " proof, Please check details below:";

    // Project Type = X-Deal
    if($special == "s")
        $content = $content . "Boss has checked " . $payment . " proof, Please check details below:";

    // Project Type = No DP and Kind = 0 and Amount <= 10萬
    if($special == "sn" && $kind == 0 && $final_amount <= 100000)
        $content = $content . "Kristel has checked " . $payment . " proof, Please check details below:";

    // Project Type = No DP and Kind = 0 and Amount > 10萬
    if($special == "sn" && $kind == 0 && $final_amount > 100000)
        $content = $content . "Boss has checked " . $payment . " proof, Please check details below:";

    // Project Type = No DP and Kind = 1 or 2 and Amount <= 10萬
    if($special == "sn" && ($kind == 1 || $kind == 2) && $final_amount <= 100000)
        $content = $content . "Glen has checked " . $payment . " proof, Please check details below:";

    // Project Type = No DP and Kind = 1 or 2 and Amount > 10萬
    if($special == "sn" && ($kind == 1 || $kind == 2) && $final_amount > 100000)
        $content = $content . "Glen has checked " . $payment . " proof, Please check details below:";


    $content = $content . '</td>
                        </tr>
                        </tbody>
                    </table>
                    <table style="width: 100%">
                        <tbody>
                        <tr>
                            <td style="background-color: #FDB72F44; border: 2px solid #FFFFFF; padding: 8px; font-size: 14px; width: 280px; font-weight: 600;">
                                <eng style="font-size: 16px;">
                                    Project Name
                                </eng>
                            </td>
                            <td style="background-color: #FDB72F44; border: 2px solid #FFFFFF; padding: 8px; width: 440px; font-size: 16px;">';
    $content = $content . " " . $projectname . " ";
    $content = $content . '</td>
                        </tr>
                        <tr>
                            <td style="background-color: #FDB72F44; border: 2px solid #FFFFFF; padding: 8px; font-size: 14px; width: 280px; font-weight: 600;">
                                <eng style="font-size: 16px;">
                                    Project Category
                                </eng>
                            </td>
                        <td style="background-color: #FDB72F44; border: 2px solid #FFFFFF; padding: 8px; width: 440px; font-size: 16px;">';
    if($category == '1')
        $content = $content . " " . "Office Systems" . " ";
    else
        $content = $content . " " . "Lighting" . " ";
    //$content = $content . " " . $category == '1' ? "Office Systems" : "Lighting" . " ";
    $content = $content . '</td>
                        </tr>
                        <tr>
                            <td style="background-color: #FDB72F44; border: 2px solid #FFFFFF; padding: 8px; font-size: 14px; width: 280px; font-weight: 600;">
                                <eng style="font-size: 16px;">
                                    Submission Time
                                </eng>
                            </td>
                            <td style="background-color: #FDB72F44; border: 2px solid #FFFFFF; padding: 8px; width: 440px; font-size: 16px;">';
    $content = $content . " " . $subtime . " ";
    $content = $content . '</td>
                        </tr>
                        <tr>
                            <td style="background-color: #FDB72F44; border: 2px solid #FFFFFF; padding: 8px; font-size: 14px; width: 280px; font-weight: 600;">
                                <eng style="font-size: 16px;">
                                    Submitter
                                </eng>
                            </td>
                            <td style="background-color: #FDB72F44; border: 2px solid #FFFFFF; padding: 8px; width: 440px; font-size: 16px;">';
    $content = $content . " " . $name . " ";
    $content = $content . '</td>
                        </tr>
                        <tr>
                            <td style="background-color: #FDB72F44; border: 2px solid #FFFFFF; padding: 8px; font-size: 14px; width: 280px; font-weight: 600;">
                                <eng style="font-size: 16px;">
                                    Type
                                </eng>
                            </td>
                            <td style="background-color: #FDB72F44; border: 2px solid #FFFFFF; padding: 8px; width: 440px; font-size: 16px;">';
    $content = $content . " " . $payment . " ";

    $content = $content . '</td>
                        </tr>
                        <tr>
                            <td style="background-color: #FDB72F44; border: 2px solid #FFFFFF; padding: 8px; font-size: 14px; width: 280px; font-weight: 600;">
                                <eng style="font-size: 16px;">
                                    Remarks by Submitter
                                </eng>
                            </td>
                            <td style="background-color: #FDB72F44; border: 2px solid #FFFFFF; padding: 8px; width: 440px; font-size: 16px;">';
    $content = $content . " " . $remark . " ";

    $content = $content . '</td>
                        </tr>
                        <tr>
                            <td style="background-color: #FDB72F44; border: 2px solid #FFFFFF; padding: 8px; font-size: 14px; width: 280px; font-weight: 600;">
                                <eng style="font-size: 16px;">
                                    Status
                                </eng>
                            </td>
                            <td style="background-color: #FDB72F44; border: 2px solid #FFFFFF; padding: 8px; width: 440px; font-size: 16px;">';
    $content = $content . " Checked: " . $status . " ";

    $content = $content . '</td>
                        </tr>
                        <tr>
                            <td style="background-color: #FDB72F44; border: 2px solid #FFFFFF; padding: 8px; font-size: 14px; width: 280px; font-weight: 600;">
                                <eng style="font-size: 16px;">
                                    Date of Receiving Payment
                                </eng>
                            </td>
                            <td style="background-color: #FDB72F44; border: 2px solid #FFFFFF; padding: 8px; width: 440px; font-size: 16px;">';
    $content = $content . " " . $receive_date . " ";

    $content = $content . '</td>
                        </tr>
                        <tr>
                            <td style="background-color: #FDB72F44; border: 2px solid #FFFFFF; padding: 8px; font-size: 14px; width: 280px; font-weight: 600;">
                                <eng style="font-size: 16px;">
                                    Amount of Receiving Payment
                                </eng>
                            </td>
                            <td style="background-color: #FDB72F44; border: 2px solid #FFFFFF; padding: 8px; width: 440px; font-size: 16px;">';
    $content = $content . " " . number_format($amount) . " ";

    $content = $content . '</td>
                        </tr>
                        <tr>
                            <td style="background-color: #FDB72F44; border: 2px solid #FFFFFF; padding: 8px; font-size: 14px; width: 280px; font-weight: 600;">
                                <eng style="font-size: 16px;">
                                    Invoice Number
                                </eng>
                            </td>
                            <td style="background-color: #FDB72F44; border: 2px solid #FFFFFF; padding: 8px; width: 440px; font-size: 16px;">';
    $content = $content . " " . $invoice . " ";

    if($kind != 2)
    {
        if($payment_method == 'check')
        {
            $content = $content . '</td>
                                </tr>
                                <tr>
                                    <td style="background-color: #FDB72F44; border: 2px solid #FFFFFF; padding: 8px; font-size: 14px; width: 280px; font-weight: 600;">
                                        <eng style="font-size: 16px;">
                                            Payment Method
                                        </eng>
                                    </td>
                                    <td style="background-color: #FDB72F44; border: 2px solid #FFFFFF; padding: 8px; width: 440px; font-size: 16px;">';
            $content = $content . " Check ";

            $content = $content . '</td>
                                </tr>
                                <tr>
                                    <td style="background-color: #FDB72F44; border: 2px solid #FFFFFF; padding: 8px; font-size: 14px; width: 280px; font-weight: 600;">
                                        <eng style="font-size: 16px;">
                                            Bank Name
                                        </eng>
                                    </td>
                                    <td style="background-color: #FDB72F44; border: 2px solid #FFFFFF; padding: 8px; width: 440px; font-size: 16px;">';
            $content = $content . " " . $bank_name . " ";

            $content = $content . '</td>
                                </tr>
                                <tr>
                                    <td style="background-color: #FDB72F44; border: 2px solid #FFFFFF; padding: 8px; font-size: 14px; width: 280px; font-weight: 600;">
                                        <eng style="font-size: 16px;">
                                            Check Number
                                        </eng>
                                    </td>
                                    <td style="background-color: #FDB72F44; border: 2px solid #FFFFFF; padding: 8px; width: 440px; font-size: 16px;">';
            $content = $content . " " . $check_number . " ";
        }

        if($payment_method == 'deposit')
        {
            $content = $content . '</td>
                                </tr>
                                <tr>
                                    <td style="background-color: #FDB72F44; border: 2px solid #FFFFFF; padding: 8px; font-size: 14px; width: 280px; font-weight: 600;">
                                        <eng style="font-size: 16px;">
                                            Payment Method
                                        </eng>
                                    </td>
                                    <td style="background-color: #FDB72F44; border: 2px solid #FFFFFF; padding: 8px; width: 440px; font-size: 16px;">';
            $content = $content . " Deposit ";

            $content = $content . '</td>
                                </tr>
                                <tr>
                                    <td style="background-color: #FDB72F44; border: 2px solid #FFFFFF; padding: 8px; font-size: 14px; width: 280px; font-weight: 600;">
                                        <eng style="font-size: 16px;">
                                            Bank Account
                                        </eng>
                                    </td>
                                    <td style="background-color: #FDB72F44; border: 2px solid #FFFFFF; padding: 8px; width: 440px; font-size: 16px;">';
            $content = $content . " " . $bank_account . " ";
        }

        if($payment_method == 'cash')
        {
            $content = $content . '</td>
                                </tr>
                                <tr>
                                    <td style="background-color: #FDB72F44; border: 2px solid #FFFFFF; padding: 8px; font-size: 14px; width: 280px; font-weight: 600;">
                                        <eng style="font-size: 16px;">
                                            Payment Method
                                        </eng>
                                    </td>
                                    <td style="background-color: #FDB72F44; border: 2px solid #FFFFFF; padding: 8px; width: 440px; font-size: 16px;">';
            $content = $content . " Cash ";

        }
    }

    $content = $content . '</td>
                        </tr>
                        <tr>
                            <td style="background-color: #FDB72F44; border: 2px solid #FFFFFF; padding: 8px; font-size: 14px; width: 280px; font-weight: 600;">
                                <eng style="font-size: 16px;">
                                    Remarks by Checker
                                </eng>
                            </td>
                            <td style="background-color: #FDB72F44; border: 2px solid #FFFFFF; padding: 8px; width: 440px; font-size: 16px;">';
    $content = $content . " " . $reason . " ";

    $content = $content . '</td>
                        </tr>
                        </tbody>
                    </table>
                    <hr style="margin-top: 45px;">
                    <table style="width: 100%;">
                        <tbody>
                        <tr>
                            <td style="font-size: 16px; padding: 5px 0 0 5px; line-height: 1.5;">
                            Please log on to Feliix >> Admin Section >> Verify and Review to view the payment proof.<br>';
    $content = $content . 'URL:  <a href="' . $conf::$mail_ip . '">' . $conf::$mail_ip . '</a> ';
    $content = $content . '</td>
                        </tr>
                        </tbody>
                    </table>
                    </div>
                    </body>
                    </html>';
/*
    $content = $content . "<p>Remark: " . $remark . "</p>";


    $content = $content . "<p>Status: Checked: " . $status . "</p>";

    $content = $content . "<p>Date of Receiving Payment: " . $receive_date . "</p>";

    $content = $content . "<p>Amount of Receiving Payment: " . number_format($amount) . "</p>";

    $content = $content . "<p>Project Name: " . $projectname . "</p>";
    $content = $content . "<p>Submission Time: " . $subtime . "</p>";
    $content = $content . "<p>Submitter: " . $leaver . "</p>";
    $content = $content . "<p>Checked: " . $status . "</p>";
    $content = $content . "<p>Remark: " . $remark . "</p>";


    if($reason != "")
        $content = $content . "<p>Additional Remark: " . $reason . "</p>";

    $content = $content . "<p> </p>";

    $content = $content . "<p>Please log on to Feliix >> Admin Section >> Verify and Review to view the downpayment proof.</p>";
    $content = $content . "<p>URL: " . $conf::$mail_ip . "</p>";
*/

    $mail->MsgHTML($content);
    if($mail->Send()) {
        logMail($email1, $content);
        return true;
//        echo "Error while sending Email.";
//        var_dump($mail);
    } else {
        logMail($email1, $mail->ErrorInfo . $content);
        return false;
//        echo "Email sent successfully";
    }

}

// 2023/07/10 因為目前無任何地方會呼叫此函數，因此將其註解起來
// function send_check_notify_mail($name, $email1, $projectname, $remark, $subtime, $reason, $status, $category)
// {
//     $conf = new Conf();

//     $mail = new PHPMailer();
//     $mail->IsSMTP();
//     $mail->Mailer = "smtp";
//     $mail->CharSet = 'UTF-8';
//     $mail->Encoding = 'base64';

//     // $mail->SMTPDebug  = 0;
//     // $mail->SMTPAuth   = true;
//     // $mail->SMTPSecure = "ssl";
//     // $mail->Port       = 465;
//     // $mail->SMTPKeepAlive = true;
//     // $mail->Host       = $conf::$mail_host;
//     // $mail->Username   = $conf::$mail_username;
//     // $mail->Password   = $conf::$mail_password;

//     $mail = SetupMail($mail, $conf);

//     $mail->IsHTML(true);
//     $mail->AddAddress($email1, $name);


//     if($category == '1')
//         $mail->AddAddress('johmar@feliix.com', 'Johmar Maximo');

//     if($category == '2')
//         $mail->AddAddress('nestor@feliix.com', 'Nestor Rosales');

//     $mail->AddCC('kuan@feliix.com', 'Kuan');
//     $mail->AddCC('kristel@feliix.com', 'Kristel Tan');
//     $mail->AddCC('glen@feliix.com', 'Glendon Wendell Co');
//     $mail->AddCC('ariel@feliix.com', 'Ariel Lin');
//     //$mail->AddCC('wren@feliix.com', 'Thalassa Wren Benzon');
//     //$mail->AddCC('edneil@feliix.com', 'Edneil Fernandez');

//     $mail->AddCC('dennis@feliix.com', 'Dennis Lin');

//     $mail->SetFrom("feliix.it@gmail.com", "Feliix.System");
//     $mail->AddReplyTo("feliix.it@gmail.com", "Feliix.System");
//     // $mail->AddCC("tryhelpbuy@gmail.com", "tryhelpbuy");


//     if($status == 'True'){
//         $mail->Subject = "[PAYMENT CONFIRMED] Checked: " . $status . " for Downpayment Proof submitted by " . $name . "(" . $projectname . ")";

//         if($category == '2'){
//             $mail->AddAddress('aiza@feliix.com', 'Aiza Eisma');
//             $mail->AddCC('cristina@feliix.com', 'Cristina Matining');
//             $mail->AddCC('alleah.feliix@gmail.com', 'Alleah Belmonte');
//         }

//     }
//     if($status == 'False')
//         $mail->Subject = "Checked: " . $status . " for Downpayment Proof submitted by " . $name . "(" . $projectname . ")";

//     $content =  "<p>Dear " . $name . ",</p>";
//     $content = $content . "<p>Glen has checked downpayment proof, Following are the details:</p>";

//     $content = $content . "<p>Project Name: " . $projectname . "</p>";
//     $content = $content . "<p>Submission Time: " . $subtime . "</p>";
//     $content = $content . "<p>Submitter: " . $name . "</p>";
//     $content = $content . "<p>Remark: " . $remark . "</p>";


//     $content = $content . "<p>Status: Checked: " . $status . "</p>";
//     /*
//     $content = $content . "<p>Project Name: " . $projectname . "</p>";
//     $content = $content . "<p>Submission Time: " . $subtime . "</p>";
//     $content = $content . "<p>Submitter: " . $leaver . "</p>";
//     $content = $content . "<p>Checked: " . $status . "</p>";
//     $content = $content . "<p>Remark: " . $remark . "</p>";
//     */

//     if($reason != "")
//         $content = $content . "<p>Additional Remark: " . $reason . "</p>";

//     $content = $content . "<p> </p>";

//     $content = $content . "<p>Please log on to Feliix >> Admin Section >> Verify and Review to view the downpayment proof.</p>";
//     $content = $content . "<p>URL: " . $conf::$mail_ip . "</p>";


//     $mail->MsgHTML($content);
//     if($mail->Send()) {
//         logMail($email1, $content);
//         return true;
// //        echo "Error while sending Email.";
// //        var_dump($mail);
//     } else {
//         logMail($email1, $mail->ErrorInfo . $content);
//         return false;
// //        echo "Email sent successfully";
//     }

// }

function send_pay_notify_mail_new($name, $email1,  $leaver, $projectname, $remark, $subtime, $category, $kind, $special, $final_amount)
{
    $conf = new Conf();

    $mail = new PHPMailer();
    $mail->IsSMTP();
    $mail->Mailer = "smtp";
    $mail->CharSet = 'UTF-8';
    $mail->Encoding = 'base64';

    // $mail->SMTPDebug  = 0;
    // $mail->SMTPAuth   = true;
    // $mail->SMTPSecure = "ssl";
    // $mail->Port       = 465;
    // $mail->SMTPKeepAlive = true;
    // $mail->Host       = $conf::$mail_host;
    // $mail->Username   = $conf::$mail_username;
    // $mail->Password   = $conf::$mail_password;

    $mail = SetupMail($mail, $conf);

    $mail->IsHTML(true);

    // 判斷 Project Type 和 Proof Kind 和 Project Final Amount 來決定 收件者和cc收件者
    // Project Type = Normal
    if($special == ""){
        $mail->AddAddress('glen@feliix.com', 'Glendon Wendell Co');
	$mail->AddCC('kuan@feliix.com', 'Kuan');
	$mail->AddCC('kristel@feliix.com', 'Kristel Tan');
    }

    // Project Type = X-Deal
    if($special == "s"){
        $mail->AddAddress('kuan@feliix.com', 'Kuan');
	$mail->AddCC('kristel@feliix.com', 'Kristel Tan');
	$mail->AddCC('glen@feliix.com', 'Glendon Wendell Co');
    }

    // Project Type = No DP and Kind = 0 and Amount <= 10萬
    if($special == "sn" && $kind == 0 && $final_amount <= 100000){
        $mail->AddAddress('kristel@feliix.com', 'Kristel Tan');
	$mail->AddCC('kuan@feliix.com', 'Kuan');
	$mail->AddCC('glen@feliix.com', 'Glendon Wendell Co');
    }

    // Project Type = No DP and Kind = 0 and Amount > 10萬
    if($special == "sn" && $kind == 0 && $final_amount > 100000){
        $mail->AddAddress('kuan@feliix.com', 'Kuan');
	$mail->AddCC('kristel@feliix.com', 'Kristel Tan');
	$mail->AddCC('glen@feliix.com', 'Glendon Wendell Co');
    }

    // Project Type = No DP and Kind = 1 or 2 and Amount <= 10萬
        if($special == "sn" && ($kind == 1 || $kind == 2) && $final_amount <= 100000){
        $mail->AddAddress('glen@feliix.com', 'Glendon Wendell Co');
	$mail->AddCC('kuan@feliix.com', 'Kuan');
	$mail->AddCC('kristel@feliix.com', 'Kristel Tan');
    }

    // Project Type = No DP and Kind = 1 or 2 and Amount > 10萬
    if($special == "sn" && ($kind == 1 || $kind == 2) && $final_amount > 100000){
        $mail->AddAddress('glen@feliix.com', 'Glendon Wendell Co');
	$mail->AddCC('kuan@feliix.com', 'Kuan');
	$mail->AddCC('kristel@feliix.com', 'Kristel Tan');
    }


    if($category == '1')
        $mail->AddAddress('johmar@feliix.com', 'Johmar Maximo');

    if($category == '2')
        $mail->AddAddress('nestor@feliix.com', 'Nestor Rosales');

    $pay = "Full Payment";
    if($kind == 0)
        $pay = "Down Payment";

    if($kind == 2)
        $pay = "2307";


    $mail->AddCC($email1, $name);
    //$mail->AddCC('wren@feliix.com', 'Thalassa Wren Benzon');

    //if($kind == 0 || $kind == 1)
        //$mail->AddCC('edneil@feliix.com', 'Edneil Fernandez');
    //if($category == '2' && ($kind == 0 || $kind == 1))
        //$mail->AddCC('aiza@feliix.com', 'Aiza Eisma');

    $mail->AddCC('dennis@feliix.com', 'Dennis Lin');

    $mail->SetFrom("feliix.it@gmail.com", "Feliix.System");
    $mail->AddReplyTo("feliix.it@gmail.com", "Feliix.System");
    // $mail->AddCC("tryhelpbuy@gmail.com", "tryhelpbuy");
    $mail->Subject = "Payment Proof Submitted by " . $leaver . "(" . $projectname . ")";

    $content = '<!DOCTYPE html>
                <html lang="en">
                <head>
                    <meta http-equiv="content-type" content="text/html; charset=utf-8"/>
                </head>
                <body>
                <div style="width: 766px; padding: 25px 70px 20px 70px; border: 2px solid rgb(230,230,230); color: black;">
                    <table style="width: 100%;">
                        <tbody>
                        <tr>
                            <td style="font-size: 20px; padding: 20px 0 20px 5px;"> ';

   
    // 判斷 Project Type 和 Proof Kind 和 Project Final Amount 來決定 稱呼者名稱
    // Project Type = Normal
    if($special == "")
	$content = $content . ' Dear Glendon, ';

    // Project Type = X-Deal
    if($special == "s")
        $content = $content . ' Dear Boss, ';

    // Project Type = No DP and Kind = 0 and Amount <= 10萬
    if($special == "sn" && $kind == 0 && $final_amount <= 100000)
        $content = $content . ' Dear Kristel, ';

    // Project Type = No DP and Kind = 0 and Amount > 10萬
    if($special == "sn" && $kind == 0 && $final_amount > 100000)
        $content = $content . ' Dear Boss, ';

    // Project Type = No DP and Kind = 1 or 2 and Amount <= 10萬
        if($special == "sn" && ($kind == 1 || $kind == 2) && $final_amount <= 100000)
        $content = $content . ' Dear Glendon, ';

    // Project Type = No DP and Kind = 1 or 2 and Amount > 10萬
    if($special == "sn" && ($kind == 1 || $kind == 2) && $final_amount > 100000)
        $content = $content . ' Dear Glendon, ';


    $content = $content . '
                            </td>
                        </tr>
                        <tr>
                            <td style="font-size: 20px; padding: 0 0 20px 5px; text-align: justify;">';
    $content = $content . " " . $leaver . " has submitted payment proof. Please check details below:";
    $content = $content . '</td>
                        </tr>
                        </tbody>
                    </table>
                    <table style="width: 100%">
                        <tbody>
                        <tr>
                            <td style="background-color: #C3F69D99; border: 2px solid #FFFFFF; padding: 8px; font-size: 14px; width: 280px; font-weight: 600;">
                                <eng style="font-size: 16px;">
                                    Project Name
                                </eng>
                            </td>
                            <td style="background-color: #C3F69D99; border: 2px solid #FFFFFF; padding: 8px; width: 440px; font-size: 16px;">';
    $content = $content . " " . $projectname . " ";
    $content = $content . '</td>
                        </tr>
                        <tr>
                            <td style="background-color: #C3F69D99; border: 2px solid #FFFFFF; padding: 8px; font-size: 14px; width: 280px; font-weight: 600;">
                                <eng style="font-size: 16px;">
                                    Project Category
                                </eng>
                            </td>
                        <td style="background-color: #C3F69D99; border: 2px solid #FFFFFF; padding: 8px; width: 440px; font-size: 16px;">';
    //$content = $content . " " . $category == '1' ? "Office Systems" : "Lighting" . " ";
    if($category == '1')
        $content = $content . " " . "Office Systems" . " ";
    else
        $content = $content . " " . "Lighting" . " ";
    $content = $content . '</td>
                        </tr>
                        <tr>
                            <td style="background-color: #C3F69D99; border: 2px solid #FFFFFF; padding: 8px; font-size: 14px; width: 280px; font-weight: 600;">
                                <eng style="font-size: 16px;">
                                    Submission Time
                                </eng>
                            </td>
                        <td style="background-color: #C3F69D99; border: 2px solid #FFFFFF; padding: 8px; width: 440px; font-size: 16px;">';
    $content = $content . " " . $subtime . " ";
    $content = $content . '</td>
                        </tr>
                        <tr>
                            <td style="background-color: #C3F69D99; border: 2px solid #FFFFFF; padding: 8px; font-size: 14px; width: 280px; font-weight: 600;">
                                <eng style="font-size: 16px;">
                                    Submitter
                                </eng>
                            </td>
                            <td style="background-color: #C3F69D99; border: 2px solid #FFFFFF; padding: 8px; width: 440px; font-size: 16px;">';
    $content = $content . " " . $leaver . " ";
    $content = $content . '</td>
                        </tr>
                        <tr>
                            <td style="background-color: #C3F69D99; border: 2px solid #FFFFFF; padding: 8px; font-size: 14px; width: 280px; font-weight: 600;">
                                <eng style="font-size: 16px;">
                                    Type
                                </eng>
                            </td>
                            <td style="background-color: #C3F69D99; border: 2px solid #FFFFFF; padding: 8px; width: 440px; font-size: 16px;">';
    $content = $content . " " . $pay . " ";
    $content = $content . '</td>
                        </tr>

                        <tr>
                            <td style="background-color: #C3F69D99; border: 2px solid #FFFFFF; padding: 8px; font-size: 14px; width: 280px; font-weight: 600;">
                                <eng style="font-size: 16px;">
                                    Remarks
                                </eng>
                            </td>
                            <td style="background-color: #C3F69D99; border: 2px solid #FFFFFF; padding: 8px; width: 440px; font-size: 16px;">';
    $content = $content . " " . $remark . " ";

    $content = $content . '</td>
                        </tr>
                        </tbody>
                    </table>
                    <hr style="margin-top: 45px;">
                    <table style="width: 100%;">
                        <tbody>
                        <tr>
                            <td style="font-size: 16px; padding: 5px 0 0 5px; line-height: 1.5;">
                                Please log on to Feliix >> Admin Section >> Verify and Review to review the downpayment proof.<br>';
    $content = $content . 'URL:  <a href="' . $conf::$mail_ip . '">' . $conf::$mail_ip . '</a> ';
    $content = $content . '</td>
                        </tr>
                        </tbody>
                    </table>
                    </div>
                    </body>
                    </html>';

    $mail->MsgHTML($content);
    if($mail->Send()) {
        logMail($email1, $content);
        return true;
//        echo "Error while sending Email.";
//        var_dump($mail);
    } else {
        logMail($email1, $mail->ErrorInfo . $content);
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

    // $mail->SMTPDebug  = 0;
    // $mail->SMTPAuth   = true;
    // $mail->SMTPSecure = "ssl";
    // $mail->Port       = 465;
    // $mail->SMTPKeepAlive = true;
    // $mail->Host       = $conf::$mail_host;
    // $mail->Username   = $conf::$mail_username;
    // $mail->Password   = $conf::$mail_password;

    $mail = SetupMail($mail, $conf);

    $mail->IsHTML(true);
    $mail->AddAddress('glen@feliix.com', 'Glendon Wendell Co');

    if($category == '1')
        $mail->AddAddress('johmar@feliix.com', 'Johmar Maximo');

    if($category == '2')
        $mail->AddAddress('nestor@feliix.com', 'Nestor Rosales');

    $mail->AddCC('kuan@feliix.com', 'Kuan');
    $mail->AddCC('kristel@feliix.com', 'Kristel Tan');
    $mail->AddCC($email1, $name);

    $mail->AddCC('dennis@feliix.com', 'Dennis Lin');

    $mail->SetFrom("feliix.it@gmail.com", "Feliix.System");
    $mail->AddReplyTo("feliix.it@gmail.com", "Feliix.System");
    // $mail->AddCC("tryhelpbuy@gmail.com", "tryhelpbuy");
    $mail->Subject = "Downpayment Proof Submitted by " . $leaver . "(" . $projectname . ")";
    $content =  "<p>Dear Glen,</p>";
    $content = $content . "<p>" . $leaver . " has applied for downpayment proof, Following are the details:</p>";
    $content = $content . "<p> </p>";

    $content = $content . "<p>Project Name: " . $projectname . "</p>";
    $content = $content . "<p>Submission Time: " . $subtime . "</p>";
    $content = $content . "<p>Submitter: " . $leaver . "</p>";
    $content = $content . "<p>Remark: " . $remark . "</p>";

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
        logMail($email1, $mail->ErrorInfo . $content);
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

    // $mail->SMTPDebug  = 0;
    // $mail->SMTPAuth   = true;
    // $mail->SMTPSecure = "ssl";
    // $mail->Port       = 465;
    // $mail->SMTPKeepAlive = true;
    // $mail->Host       = $conf::$mail_host;
    // $mail->Username   = $conf::$mail_username;
    // $mail->Password   = $conf::$mail_password;

    $mail = SetupMail($mail, $conf);

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
    $content = $content . "<p>Applicant: " . $leaver . "</p>";
    $content = $content . "<p>Department: " . $department . "</p>";
    $content = $content . "<p>Applying Time: " . $app_time . "</p>";
    $content = $content . "<p>Leave Type: " . $leave_type . "</p>";
    $content = $content . "<p>Starting Time: " . $start_time . "</p>";
    $content = $content . "<p>Ending Time: " . $end_time . "</p>";
    $content = $content . "<p>Leave Length: " . $leave_length . "</p>";
    $content = $content . "<p>Reason: " . $reason . "</p>";
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
        logMail($email1, $mail->ErrorInfo . $content);
        return false;
//        echo "Email sent successfully";
    }
}

function send_schedule_notify_mail($last_id, $project, $creator, $_date, $_time, $sales_executive, $project_in_charge, $relevants, $att)
{
    $conf = new Conf();

    $mail = new PHPMailer();
    $mail->IsSMTP();
    $mail->Mailer = "smtp";
    $mail->CharSet = 'UTF-8';
    $mail->Encoding = 'base64';

    // $mail->SMTPDebug  = 0;
    // $mail->SMTPAuth   = true;
    // $mail->SMTPSecure = "ssl";
    // $mail->Port       = 465;
    // $mail->SMTPKeepAlive = true;
    // $mail->Host       = $conf::$mail_host;
    // $mail->Username   = $conf::$mail_username;
    // $mail->Password   = $conf::$mail_password;

    $mail = SetupMail($mail, $conf);

    $mail->IsHTML(true);

    $mail->SetFrom("feliix.it@gmail.com", "Feliix.System");
    $mail->AddReplyTo("feliix.it@gmail.com", "Feliix.System");

    $notifior = array();
    $notifior = GetNotifiersByName($relevants);
    foreach($notifior as &$list)
    {
        $mail->AddAddress($list["email"], $list["username"]);
    }

    $notifior = GetNotifiersByName($creator);
    foreach($notifior as &$list)
    {
        $mail->AddCC($list["email"], $list["username"]);
    }

    $mail->addAttachment($att);

    $mail->Subject = "[Schedule Notification] " . $project . " was created";
    $content =  "<p>Dear all,</p>";
    $content = $content . "<p>A new schedule was created and needs you to follow. Below is the details:</p>";
    $content = $content . "<p>Project: " . $project . "</p>";
    $content = $content . "<p>Creator: " . $creator . "</p>";
    $content = $content . "<p>Date: " . $_date . "</p>";
    $content = $content . "<p>Time: " . $_time .  "</p>";
    $content = $content . "<p>Sales Executive: " . $sales_executive . "</p>";
    $content = $content . "<p>Project-in-charge: " . $project_in_charge . "</p>";
    $content = $content . "<p>Relevant Persons: " . $relevants . "</p>";

    $content = $content . "<p> </p>";
    $content = $content . "<p>Click this link to view the target webpage: </p>";
    $content = $content . "<p>https://feliix.myvnc.com/schedule_calendar</p>";

    $mail->MsgHTML($content);
    if($mail->Send()) {
        logMail($creator, $content);
        return true;
//        echo "Error while sending Email.";
//        var_dump($mail);
    } else {
        logMail($creator, $mail->ErrorInfo . $content);
        return false;
//        echo "Email sent successfully";
    }

}

function send_schedule_edit_mail($last_id, $project, $creator, $_date, $_time, $sales_executive, $project_in_charge, $relevants, $updated_by, $att)
{
    $conf = new Conf();

    $mail = new PHPMailer();
    $mail->IsSMTP();
    $mail->Mailer = "smtp";
    $mail->CharSet = 'UTF-8';
    $mail->Encoding = 'base64';

    // $mail->SMTPDebug  = 0;
    // $mail->SMTPAuth   = true;
    // $mail->SMTPSecure = "ssl";
    // $mail->Port       = 465;
    // $mail->SMTPKeepAlive = true;
    // $mail->Host       = $conf::$mail_host;
    // $mail->Username   = $conf::$mail_username;
    // $mail->Password   = $conf::$mail_password;

    $mail = SetupMail($mail, $conf);

    $mail->IsHTML(true);

    $mail->SetFrom("feliix.it@gmail.com", "Feliix.System");
    $mail->AddReplyTo("feliix.it@gmail.com", "Feliix.System");

    $notifior = array();
    $notifior = GetNotifiersByName($relevants);
    foreach($notifior as &$list)
    {
        $mail->AddAddress($list["email"], $list["username"]);
    }

    $notifior = GetNotifiersByName($updated_by);
    foreach($notifior as &$list)
    {
        $mail->AddCC($list["email"], $list["username"]);
    }

    $notifior = GetNotifiersByName($creator);
    foreach($notifior as &$list)
    {
        $mail->AddCC($list["email"], $list["username"]);
    }

    $mail->addAttachment($att);

    $mail->Subject = "[Schedule Notification] " . $project . " was revised";
    $content =  "<p>Dear all,</p>";
    $content = $content . "<p>A schedule was revised and needs you to follow. Below is the details:</p>";
    $content = $content . "<p>Project: " . $project . "</p>";
    $content = $content . "<p>Creator: " . $creator . "</p>";
    $content = $content . "<p>Reviser: " . $updated_by . "</p>";
    $content = $content . "<p>Date: " . $_date . "</p>";
    $content = $content . "<p>Time: " . $_time .  "</p>";
    $content = $content . "<p>Sales Executive: " . $sales_executive . "</p>";
    $content = $content . "<p>Project-in-charge: " . $project_in_charge . "</p>";
    $content = $content . "<p>Relevant Persons: " . $relevants . "</p>";

    $content = $content . "<p> </p>";
    $content = $content . "<p>Click this link to view the target webpage: </p>";
    $content = $content . "<p>https://feliix.myvnc.com/schedule_calendar</p>";

    $mail->MsgHTML($content);
    if($mail->Send()) {
        logMail($creator, $content);
        return true;
//        echo "Error while sending Email.";
//        var_dump($mail);
    } else {
        logMail($creator, $mail->ErrorInfo . $content);
        return false;
//        echo "Email sent successfully";
    }

}

function send_schedule_edit_goodby_mail($last_id, $project, $creator, $_date, $_time, $sales_executive, $project_in_charge, $relevants, $updated_by, $att)
{
    $conf = new Conf();

    $mail = new PHPMailer();
    $mail->IsSMTP();
    $mail->Mailer = "smtp";
    $mail->CharSet = 'UTF-8';
    $mail->Encoding = 'base64';

    // $mail->SMTPDebug  = 0;
    // $mail->SMTPAuth   = true;
    // $mail->SMTPSecure = "ssl";
    // $mail->Port       = 465;
    // $mail->SMTPKeepAlive = true;
    // $mail->Host       = $conf::$mail_host;
    // $mail->Username   = $conf::$mail_username;
    // $mail->Password   = $conf::$mail_password;

    $mail = SetupMail($mail, $conf);

    $mail->IsHTML(true);

    $mail->SetFrom("feliix.it@gmail.com", "Feliix.System");
    $mail->AddReplyTo("feliix.it@gmail.com", "Feliix.System");

    $notifior = array();
    $notifior = GetNotifiersByName($relevants);
    foreach($notifior as &$list)
    {
        $mail->AddAddress($list["email"], $list["username"]);
    }

    $mail->addAttachment($att);

    $mail->Subject = "[Schedule Notification] " . $project . " was revised";
    $content =  "<p>Dear all,</p>";
    $content = $content . "<p>You were removed from relevant persons of Schedule " . $project . ". Below is the details of Schedule " . $project . ":</p>";
    $content = $content . "<p>Project: " . $project . "</p>";
    $content = $content . "<p>Creator: " . $creator . "</p>";
    $content = $content . "<p>Reviser: " . $updated_by . "</p>";
    $content = $content . "<p>Date: " . $_date . "</p>";
    $content = $content . "<p>Time: " . $_time .  "</p>";
    $content = $content . "<p>Sales Executive: " . $sales_executive . "</p>";
    $content = $content . "<p>Project-in-charge: " . $project_in_charge . "</p>";
    $content = $content . "<p>Relevant Persons: " . $relevants . "</p>";

    $content = $content . "<p> </p>";
    $content = $content . "<p>Click this link to view the target webpage: </p>";
    $content = $content . "<p>https://feliix.myvnc.com/schedule_calendar</p>";

    $mail->MsgHTML($content);
    if($mail->Send()) {
        logMail($creator, $content);
        return true;
//        echo "Error while sending Email.";
//        var_dump($mail);
    } else {
        logMail($creator, $mail->ErrorInfo . $content);
        return false;
//        echo "Email sent successfully";
    }

}

function send_schedule_del_mail($last_id, $project, $creator, $_date, $_time, $sales_executive, $project_in_charge, $relevants, $updated_by, $att)
{
    $conf = new Conf();

    $mail = new PHPMailer();
    $mail->IsSMTP();
    $mail->Mailer = "smtp";
    $mail->CharSet = 'UTF-8';
    $mail->Encoding = 'base64';

    // $mail->SMTPDebug  = 0;
    // $mail->SMTPAuth   = true;
    // $mail->SMTPSecure = "ssl";
    // $mail->Port       = 465;
    // $mail->SMTPKeepAlive = true;
    // $mail->Host       = $conf::$mail_host;
    // $mail->Username   = $conf::$mail_username;
    // $mail->Password   = $conf::$mail_password;

    $mail = SetupMail($mail, $conf);

    $mail->IsHTML(true);

    $mail->SetFrom("feliix.it@gmail.com", "Feliix.System");
    $mail->AddReplyTo("feliix.it@gmail.com", "Feliix.System");

    $notifior = array();
    $notifior = GetNotifiersByName($relevants);
    foreach($notifior as &$list)
    {
        $mail->AddAddress($list["email"], $list["username"]);
    }

    $notifior = GetNotifiersByName($updated_by);
    foreach($notifior as &$list)
    {
        $mail->AddCC($list["email"], $list["username"]);
    }

    $notifior = GetNotifiersByName($creator);
    foreach($notifior as &$list)
    {
        $mail->AddCC($list["email"], $list["username"]);
    }

    $mail->addAttachment($att);

    $mail->Subject = "[Schedule Notification] " . $project . " was deleted";
    $content =  "<p>Dear all,</p>";
    $content = $content . "<p>A existing task was deleted. Below is the details:</p>";
    $content = $content . "<p>Project: " . $project . "</p>";
    $content = $content . "<p>Creator: " . $creator . "</p>";
    $content = $content . "<p>Eraser: " . $updated_by . "</p>";
    $content = $content . "<p>Date: " . $_date . "</p>";
    $content = $content . "<p>Time: " . $_time .  "</p>";
    $content = $content . "<p>Sales Executive: " . $sales_executive . "</p>";
    $content = $content . "<p>Project-in-charge: " . $project_in_charge . "</p>";
    $content = $content . "<p>Relevant Persons: " . $relevants . "</p>";

    $content = $content . "<p></p>";

    $mail->MsgHTML($content);
    if($mail->Send()) {
        logMail($creator, $content);
        return true;
//        echo "Error while sending Email.";
//        var_dump($mail);
    } else {
        logMail($creator, $mail->ErrorInfo . $content);
        return false;
//        echo "Email sent successfully";
    }

}

function send_meeting_notify_mail($name, $email1, $subject, $creator, $attendee, $start_time, $end_time, $detail, $location)
{
    $conf = new Conf();

    $mail = new PHPMailer();
    $mail->IsSMTP();
    $mail->Mailer = "smtp";
    $mail->CharSet = 'UTF-8';
    $mail->Encoding = 'base64';

    // $mail->SMTPDebug  = 0;
    // $mail->SMTPAuth   = true;
    // $mail->SMTPSecure = "ssl";
    // $mail->Port       = 465;
    // $mail->SMTPKeepAlive = true;
    // $mail->Host       = $conf::$mail_host;
    // $mail->Username   = $conf::$mail_username;
    // $mail->Password   = $conf::$mail_password;

    $mail = SetupMail($mail, $conf);

    $mail->IsHTML(true);
    $mail->AddAddress($email1, $name);

    $mail->SetFrom("feliix.it@gmail.com", "Feliix.System");
    $mail->AddReplyTo("feliix.it@gmail.com", "Feliix.System");
    // $mail->AddCC("tryhelpbuy@gmail.com", "tryhelpbuy");
    $mail->Subject = "Notification: New Meeting from " . $creator;
    $content =  "<p>Dear " . $name . ",</p>";
    $content = $content . "<p>" . $creator . " created a meeting. Following are the details:</p>";
    $content = $content . "<p>Subject: " . $subject . "</p>";
    $content = $content . "<p>Creator: " . $creator . "</p>";
    $content = $content . "<p>Attendee: " . $attendee . "</p>";
    $content = $content . "<p>Time: " . $start_time . " - " . $end_time . "</p>";
    $content = $content . "<p>Location: " . $location . "</p>";
    $content = $content . "<p>Content: " . $detail . "</p>";
    /*
    $content = $content . "<p>Project Name: " . $projectname . "</p>";
    $content = $content . "<p>Submission Time: " . $subtime . "</p>";
    $content = $content . "<p>Submitter: " . $leaver . "</p>";
    $content = $content . "<p>Checked: " . $status . "</p>";
    $content = $content . "<p>Remark: " . $remark . "</p>";
    */

    $content = $content . "<p> </p>";


    $mail->MsgHTML($content);
    if($mail->Send()) {
        logMail($email1, $content);
        return true;
//        echo "Error while sending Email.";
//        var_dump($mail);
    } else {
        logMail($email1, $mail->ErrorInfo . $content);
        return false;
//        echo "Email sent successfully";
    }

}


function send_meeting_modified_mail($name, $email1, $subject, $creator, $attendee, $start_time, $end_time, $detail, $location)
{
    $conf = new Conf();

    $mail = new PHPMailer();
    $mail->IsSMTP();
    $mail->Mailer = "smtp";
    $mail->CharSet = 'UTF-8';
    $mail->Encoding = 'base64';

    // $mail->SMTPDebug  = 0;
    // $mail->SMTPAuth   = true;
    // $mail->SMTPSecure = "ssl";
    // $mail->Port       = 465;
    // $mail->SMTPKeepAlive = true;
    // $mail->Host       = $conf::$mail_host;
    // $mail->Username   = $conf::$mail_username;
    // $mail->Password   = $conf::$mail_password;

    $mail = SetupMail($mail, $conf);

    $mail->IsHTML(true);
    $mail->AddAddress($email1, $name);

    $mail->SetFrom("feliix.it@gmail.com", "Feliix.System");
    $mail->AddReplyTo("feliix.it@gmail.com", "Feliix.System");
    // $mail->AddCC("tryhelpbuy@gmail.com", "tryhelpbuy");
    $mail->Subject = "Notification: Meeting Info Changed by " . $creator;
    $content =  "<p>Dear " . $name . ",</p>";
    $content = $content . "<p>" . $creator . " changed the original info of the meeting. Following are the details after change:</p>";
    $content = $content . "<p>Subject: " . $subject . "</p>";
    $content = $content . "<p>Creator: " . $creator . "</p>";
    $content = $content . "<p>Attendee: " . $attendee . "</p>";
    $content = $content . "<p>Time: " . $start_time . " - " . $end_time . "</p>";
    $content = $content . "<p>Location: " . $location . "</p>";
    $content = $content . "<p>Content: " . $detail . "</p>";
    /*
    $content = $content . "<p>Project Name: " . $projectname . "</p>";
    $content = $content . "<p>Submission Time: " . $subtime . "</p>";
    $content = $content . "<p>Submitter: " . $leaver . "</p>";
    $content = $content . "<p>Checked: " . $status . "</p>";
    $content = $content . "<p>Remark: " . $remark . "</p>";
    */

    $content = $content . "<p> </p>";


    $mail->MsgHTML($content);
    if($mail->Send()) {
        logMail($email1, $content);
        return true;
//        echo "Error while sending Email.";
//        var_dump($mail);
    } else {
        logMail($email1, $mail->ErrorInfo . $content);
        return false;
//        echo "Email sent successfully";
    }

}


function send_meeting_delete_mail($name, $email1, $subject, $creator, $attendee, $start_time, $end_time, $detail, $location)
{
    $conf = new Conf();

    $mail = new PHPMailer();
    $mail->IsSMTP();
    $mail->Mailer = "smtp";
    $mail->CharSet = 'UTF-8';
    $mail->Encoding = 'base64';

    // $mail->SMTPDebug  = 0;
    // $mail->SMTPAuth   = true;
    // $mail->SMTPSecure = "ssl";
    // $mail->Port       = 465;
    // $mail->SMTPKeepAlive = true;
    // $mail->Host       = $conf::$mail_host;
    // $mail->Username   = $conf::$mail_username;
    // $mail->Password   = $conf::$mail_password;

    $mail = SetupMail($mail, $conf);

    $mail->IsHTML(true);
    $mail->AddAddress($email1, $name);

    $mail->SetFrom("feliix.it@gmail.com", "Feliix.System");
    $mail->AddReplyTo("feliix.it@gmail.com", "Feliix.System");
    // $mail->AddCC("tryhelpbuy@gmail.com", "tryhelpbuy");
    $mail->Subject = "Notification: Meeting Deleted by " . $creator;
    $content =  "<p>Dear " . $name . ",</p>";
    $content = $content . "<p>" . $creator . " deleted the meeting. Following are the details before deletion:</p>";
    $content = $content . "<p>Subject: " . $subject . "</p>";
    $content = $content . "<p>Creator: " . $creator . "</p>";
    $content = $content . "<p>Attendee: " . $attendee . "</p>";
    $content = $content . "<p>Time: " . $start_time . " - " . $end_time . "</p>";
    $content = $content . "<p>Location: " . $location . "</p>";
    $content = $content . "<p>Content: " . $detail . "</p>";
    /*
    $content = $content . "<p>Project Name: " . $projectname . "</p>";
    $content = $content . "<p>Submission Time: " . $subtime . "</p>";
    $content = $content . "<p>Submitter: " . $leaver . "</p>";
    $content = $content . "<p>Checked: " . $status . "</p>";
    $content = $content . "<p>Remark: " . $remark . "</p>";
    */

    $content = $content . "<p> </p>";


    $mail->MsgHTML($content);
    if($mail->Send()) {
        logMail($email1, $content);
        return true;
//        echo "Error while sending Email.";
//        var_dump($mail);
    } else {
        logMail($email1, $mail->ErrorInfo . $content);
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

    // $mail->SMTPDebug  = 0;
    // $mail->SMTPAuth   = true;
    // $mail->SMTPSecure = "ssl";
    // $mail->Port       = 465;
    // $mail->SMTPKeepAlive = true;
    // $mail->Host       = $conf::$mail_host;
    // $mail->Username   = $conf::$mail_username;
    // $mail->Password   = $conf::$mail_password;

    $mail = SetupMail($mail, $conf);

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
    $content = $content . "<p>Request No.: " . $request_no . "</p>";
    $content = $content . "<p>Applicant: " . $applicant . "</p>";
    $content = $content . "<p>Department: " . $department . "</p>";
    $content = $content . "<p>Application Time: " . $ap_time . "</p>";
    $content = $content . "<p>Project Name: " . $project_name1 . "</p>";
    $content = $content . "<p>Reason: " . $project_name . "</p>";
    $content = $content . "<p>Date Needed: " . $date_request . "</p>";
    $content = $content . "<p>Total Amount Requested: " . $total_amount . "</p>";
    $content = $content . "<p>Voiding Reason: " . $reason . "</p>";
    $content = $content . "<p> </p>";
    $content = $content . $tab;
    $content = $content . "<p>URL: https://feliix.myvnc.com/</p>";

    $mail->MsgHTML($content);
    if($mail->Send()) {
        logMail($user_email, $content);
        return true;
    } else {
        logMail($user_email, $mail->ErrorInfo . $content);
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

    // $mail->SMTPDebug  = 0;
    // $mail->SMTPAuth   = true;
    // $mail->SMTPSecure = "ssl";
    // $mail->Port       = 465;
    // $mail->SMTPKeepAlive = true;
    // $mail->Host       = $conf::$mail_host;
    // $mail->Username   = $conf::$mail_username;
    // $mail->Password   = $conf::$mail_password;

    $mail = SetupMail($mail, $conf);

    $mail->IsHTML(true);

    $mail->AddAddress($requestor_email, $requestor);

    $mail->SetFrom("feliix.it@gmail.com", "Feliix.System");
    $mail->AddReplyTo("feliix.it@gmail.com", "Feliix.System");
    
    $mail->Subject = $title;
    $content =  "<p>Dear " . $requestor . ",</p>";
    $content = $content . $conten1;
    $content = $content . "<p>Request No.: " . $request_no . "</p>";
    $content = $content . "<p>Applicant: " . $user_name . "</p>";
    $content = $content . "<p>Department: " . $department . "</p>";
    $content = $content . "<p>Application Time: " . $ap_time . "</p>";
    $content = $content . "<p>Project Name: " . $project_name1 . "</p>";
    $content = $content . "<p>Reason: " . $project_name . "</p>";
    $content = $content . "<p>Date Needed: " . $date_request . "</p>";
    $content = $content . "<p>Total Amount Requested: " . $total_amount . "</p>";
    $content = $content . "<p>Rejection Reason: " . $reason . "</p>";
    $content = $content . "<p> </p>";
    $content = $content . $tab;
    $content = $content . "<p>URL: https://feliix.myvnc.com/</p>";

    $mail->MsgHTML($content);
    if($mail->Send()) {
        logMail($requestor_email, $content);
        return true;
    } else {
        logMail($requestor_email, $mail->ErrorInfo . $content);
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

    // $mail->SMTPDebug  = 0;
    // $mail->SMTPAuth   = true;
    // $mail->SMTPSecure = "ssl";
    // $mail->Port       = 465;
    // $mail->SMTPKeepAlive = true;
    // $mail->Host       = $conf::$mail_host;
    // $mail->Username   = $conf::$mail_username;
    // $mail->Password   = $conf::$mail_password;

    $mail = SetupMail($mail, $conf);

    $mail->IsHTML(true);

    $mail->AddAddress($requestor_email, $requestor);

    $mail->SetFrom("feliix.it@gmail.com", "Feliix.System");
    $mail->AddReplyTo("feliix.it@gmail.com", "Feliix.System");
    
    $mail->Subject = $title;
    $content =  "<p>Dear " . $requestor . ",</p>";
    $content = $content . "<p>An expense application is waiting for you to " . $action . ". Following are the details:</p>";
    $content = $content . "<p>Request No.: " . $request_no . "</p>";
    $content = $content . "<p>Applicant: " . $applicant . "</p>";
    $content = $content . "<p>Department: " . $department . "</p>";
    $content = $content . "<p>Application Time: " . $ap_time . "</p>";
    $content = $content . "<p>Project Name: " . $project_name1 . "</p>";
    $content = $content . "<p>Reason: " . $project_name . "</p>";
    $content = $content . "<p>Date Needed: " . $date_request . "</p>";
    $content = $content . "<p>Total Amount Requested: " . $total_amount . "</p>";
    $content = $content . "<p>Date Released: " . $date_release . "</p>";
    $content = $content . "<p>Date Liquidated: " . $date_liquidate . "</p>";
    $content = $content . "<p>Amount Liquidated: " . $liquidate_amount . "</p>";
    $content = $content . "<p>Remarks: " . $remarks . "</p>";
    $content = $content . "<p> </p>";
    $content = $content . "<p>Please log on to Feliix >> Admin Section >> Expense Review >> Tab " . $tab . " to view the expense application.</p>";
    $content = $content . "<p>URL: https://feliix.myvnc.com/</p>";

    $mail->MsgHTML($content);
    if($mail->Send()) {
        logMail($requestor_email, $content);
        return true;
    } else {
        logMail($requestor_email, $mail->ErrorInfo . $content);
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

    // $mail->SMTPDebug  = 0;
    // $mail->SMTPAuth   = true;
    // $mail->SMTPSecure = "ssl";
    // $mail->Port       = 465;
    // $mail->SMTPKeepAlive = true;
    // $mail->Host       = $conf::$mail_host;
    // $mail->Username   = $conf::$mail_username;
    // $mail->Password   = $conf::$mail_password;

    $mail = SetupMail($mail, $conf);

    $mail->IsHTML(true);

    $mail->AddAddress($requestor_email, $requestor);

    $mail->SetFrom("feliix.it@gmail.com", "Feliix.System");
    $mail->AddReplyTo("feliix.it@gmail.com", "Feliix.System");
    
    $mail->Subject = $title;
    $content =  "<p>Dear " . $requestor . ",</p>";
    $content = $content . "<p>An expense application is waiting for you to " . $action . ". Following are the details:</p>";
    $content = $content . "<p>Request No.: " . $request_no . "</p>";
    $content = $content . "<p>Applicant: " . $applicant . "</p>";
    $content = $content . "<p>Department: " . $department . "</p>";
    $content = $content . "<p>Application Time: " . $ap_time . "</p>";
    $content = $content . "<p>Project Name: " . $project_name1 . "</p>";
    $content = $content . "<p>Reason: " . $project_name . "</p>";
    $content = $content . "<p>Date Needed: " . $date_request . "</p>";
    $content = $content . "<p>Total Amount Requested: " . $total_amount . "</p>";
    $content = $content . "<p> </p>";
    $content = $content . "<p>Please log on to Feliix >> Admin Section >> Expense Review >> Tab " . $tab . " to view the expense application.</p>";
    $content = $content . "<p>URL: https://feliix.myvnc.com/</p>";

    $mail->MsgHTML($content);
    if($mail->Send()) {
        logMail($requestor_email, $content);
        return true;
    } else {
        logMail($requestor_email, $mail->ErrorInfo . $content);
        return false;
    }
}


function batch_liquidate_notify_mail($request_no, $user_name, $user_email, $department, $ap_time, $project_name1, $project_name, $date_request, $total_amount, $reason, $date_release, $checker)
{

    $title = "Expense Application with Request No." . $request_no . " Needs Liquidation";
   
    $conf = new Conf();

    $mail = new PHPMailer();
    $mail->IsSMTP();
    $mail->Mailer = "smtp";
    $mail->CharSet = 'UTF-8';
    $mail->Encoding = 'base64';

    // $mail->SMTPDebug  = 0;
    // $mail->SMTPAuth   = true;
    // $mail->SMTPSecure = "ssl";
    // $mail->Port       = 465;
    // $mail->SMTPKeepAlive = true;
    // $mail->Host       = $conf::$mail_host;
    // $mail->Username   = $conf::$mail_username;
    // $mail->Password   = $conf::$mail_password;

    $mail = SetupMail($mail, $conf);

    $mail->IsHTML(true);

    $mail->AddAddress($user_email, $user_name);

    $notifior = GetExpenseFlowVerifiers();
    foreach($notifior as &$list)
    {
        // verifier
        $mail->AddCC($list["email"], $list["username"]);
    }

    $mail->SetFrom("feliix.it@gmail.com", "Feliix.System");
    $mail->AddReplyTo("feliix.it@gmail.com", "Feliix.System");
    
    $mail->Subject = $title;
    $content =  "<p>Dear " . $user_name . ",</p>";
    $content = $content . "<p>An expense application is waiting for you to liquidate. Following are the details:</p>";
    $content = $content . "<p>Request No.: " . $request_no . "</p>";
    $content = $content . "<p>Applicant: " . $user_name . "</p>";
    $content = $content . "<p>Department: " . $department . "</p>";
    $content = $content . "<p>Application Time: " . $ap_time . "</p>";
    $content = $content . "<p>Project Name: " . $project_name1 . "</p>";
    $content = $content . "<p>Reason: " . $project_name . "</p>";
    $content = $content . "<p>Date Needed: " . $date_request . "</p>";
    $content = $content . "<p>Total Amount Requested: " . $total_amount . "</p>";
    $content = $content . "<p>Date Released: " . $date_release . "</p>";
    $content = $content . "<p> </p>";
    $content = $content . "<p>Please log on to Feliix >> Payment Request/Claim >> Expense Apply/Liquidate >> Tab Liquidate to view the expense application.</p>";
    $content = $content . "<p>URL: https://feliix.myvnc.com/</p>";

    $mail->MsgHTML($content);
    if($mail->Send()) {
        logMail($user_email, $content);
        return true;
    } else {
        logMail($user_email, $mail->ErrorInfo . $content);
        return false;
    }

}

function task_notify_admin_c($request_type,  $project_name, $task_name, $task_status, $create_id, $assignee, $collaborator, $due_date, $detail, $stage_id, $revise_id, $erase_id, $created_at)
{
    $tab = "";



    $conf = new Conf();

    $mail = new PHPMailer();
    $mail->IsSMTP();
    $mail->Mailer = "smtp";
    $mail->CharSet = 'UTF-8';
    $mail->Encoding = 'base64';

    // $mail->SMTPDebug  = 0;
    // $mail->SMTPAuth   = true;
    // $mail->SMTPSecure = "ssl";
    // $mail->Port       = 465;
    // $mail->SMTPKeepAlive = true;
    // $mail->Host       = $conf::$mail_host;
    // $mail->Username   = $conf::$mail_username;
    // $mail->Password   = $conf::$mail_password;

    $mail = SetupMail($mail, $conf);

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

    $_revisor = "";
    if($revise_id != 0)
    {
        $revisor = GetNotifiers($revise_id);
        foreach($revisor as &$list)
        {
            $_revisor = $list["username"];
        }
    }

    $_erasor = "";
    if($erase_id != 0)
    {
        $erasor = GetNotifiers($erase_id);
        foreach($erasor as &$list)
        {
            $_erasor = $list["username"];
        }
    }

    $creators = rtrim($creators, ", ");
    $assignees = rtrim($assignees, ", ");
    $collaborators = rtrim($collaborators, ", ");

    $mail->SetFrom("feliix.it@gmail.com", "Feliix.System");
    $mail->AddReplyTo("feliix.it@gmail.com", "Feliix.System");


    switch ($request_type) {
        case "create":
            $tab = "<p>A new task was created and needs you to follow. Below is the details:</p>";
            $title = "[" . $assignees . "][Task Notification] Task " . $task_name . " was created";
            break;
        case "edit":
            $tab = "<p>A task was revised and needs you to follow. Below is the details:</p>";
            $title = "[" . $assignees . "][Task Notification] Task " . $task_name . " was revised";
            break;
        case "del":
            $tab = "<p>A existing task was deleted. Below is the details:</p>";
            $title = "[" . $assignees . "][Task Notification] Task " . $task_name . " was deleted";
            break;
        case "notify":
            $tab = "<p>Just a quick reminder that the due date of Task " . $task_name . " is " . $due_date . ". Below is the details:</p>";
            $title = "[" . $assignees . "][Task Reminder: Due Date is Near] Task " . $task_name . " ";
            break;
        default:
            return;
            break;
    }


    $mail->Subject = $title;
    $content =  "<p>Dear all,</p>";
    $content = $content . $tab;
    $content = $content . "<p>Project Name: " . $project_name . "</p>";
    $content = $content . "<p>Task: " . $task_name . "</p>";
    $content = $content . "<p>Task Status: " . $task_status . "</p>";
    $content = $content . "<p>Creator: " . $creators . "</p>";

    if($_revisor != "")
        $content = $content . "<p>Reviser: " . $_revisor . "</p>";

    if($_erasor != "")
        $content = $content . "<p>Eraser: " . $_erasor . "</p>";

    $content = $content . "<p>Assignee: " . $assignees . "</p>";
    $content = $content . "<p>Collaborator: " . $collaborators . "</p>";
    $content = $content . "<p>Created at: " . $created_at . "</p>";
    $content = $content . "<p>Due Date: " . $due_date . "</p>";
    $content = $content . "<p>Description: " . $detail . "</p>";
    $content = $content . "<p> </p>";
    $content = $content . "<p>Click this link to view the target webpage: </p>";
    $content = $content . "<p>https://feliix.myvnc.com/project03_client_v2?sid=" . $stage_id . "</p>";

    $mail->MsgHTML($content);
    if($mail->Send()) {
        logMail($creators, $content);
        return true;
    } else {
        logMail($creators, $mail->ErrorInfo . $content);
        return false;
    }

}

function task_notify_admin_d($request_type, $task_status, $task_name, $stages_status, $create_id, $assignee, $collaborator, $due_date, $detail, $stage_id, $revise_id, $erase_id, $created_at)
{
    $tab = "";



    $conf = new Conf();

    $mail = new PHPMailer();
    $mail->IsSMTP();
    $mail->Mailer = "smtp";
    $mail->CharSet = 'UTF-8';
    $mail->Encoding = 'base64';

    // $mail->SMTPDebug  = 0;
    // $mail->SMTPAuth   = true;
    // $mail->SMTPSecure = "ssl";
    // $mail->Port       = 465;
    // $mail->SMTPKeepAlive = true;
    // $mail->Host       = $conf::$mail_host;
    // $mail->Username   = $conf::$mail_username;
    // $mail->Password   = $conf::$mail_password;

    $mail = SetupMail($mail, $conf);

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

    $_revisor = "";
    if($revise_id != 0)
    {
        $revisor = GetNotifiers($revise_id);
        foreach($revisor as &$list)
        {
            $_revisor = $list["username"];
        }
    }

    $_erasor = "";
    if($erase_id != 0)
    {
        $erasor = GetNotifiers($erase_id);
        foreach($erasor as &$list)
        {
            $_erasor = $list["username"];
        }
    }

    $creators = rtrim($creators, ", ");
    $assignees = rtrim($assignees, ", ");
    $collaborators = rtrim($collaborators, ", ");

    $mail->SetFrom("feliix.it@gmail.com", "Feliix.System");
    $mail->AddReplyTo("feliix.it@gmail.com", "Feliix.System");

    switch ($request_type) {
        case "create":
            $tab = "<p>A new task was created and needs you to follow. Below is the details:</p>";
            $title = "[" . $assignees . "][Task Notification] Task " . $task_name . " was created";
            break;
        case "edit":
            $tab = "<p>A task was revised and needs you to follow. Below is the details:</p>";
            $title = "[" . $assignees . "][Task Notification] Task " . $task_name . " was revised";
            break;
        case "del":
            $tab = "<p>A existing task was deleted. Below is the details:</p>";
            $title = "[" . $assignees . "][Task Notification] Task " . $task_name . " was deleted";
            break;
        case "notify":
            $tab = "<p>Just a quick reminder that the due date of Task " . $task_name . " is " . $due_date . ". Below is the details:</p>";
            $title = "[" . $assignees . "][Task Reminder: Due Date is Near] Task " . $task_name . " ";
            break;
        default:
            return;
            break;
    }

    $mail->Subject = $title;
    $content =  "<p>Dear all,</p>";
    $content = $content . $tab;
    $content = $content . "<p>Task: " . $task_name . "</p>";
    $content = $content . "<p>Task Status: " . $task_status . "</p>";
    $content = $content . "<p>Creator: " . $creators . "</p>";

    if($_revisor != "")
        $content = $content . "<p>Reviser: " . $_revisor . "</p>";

    if($_erasor != "")
        $content = $content . "<p>Eraser: " . $_erasor . "</p>";

    $content = $content . "<p>Assignee: " . $assignees . "</p>";
    $content = $content . "<p>Collaborator: " . $collaborators . "</p>";
    $content = $content . "<p>Created at: " . $created_at . "</p>";
    $content = $content . "<p>Due Date: " . $due_date . "</p>";
    $content = $content . "<p>Description: " . $detail . "</p>";
    $content = $content . "<p> </p>";
    $content = $content . "<p>Click this link to view the target webpage: </p>";
    $content = $content . "<p>https://feliix.myvnc.com/task_management_DS?sid=" . $stage_id . "</p>";

    $mail->MsgHTML($content);
    if($mail->Send()) {
        logMail($creators, $content);
        return true;
    } else {
        logMail($creators, $mail->ErrorInfo . $content);
        return false;
    }

}

function task_notify_admin_sl($request_type, $task_status, $task_name, $stages_status, $create_id, $assignee, $collaborator, $due_date, $detail, $stage_id, $revise_id, $erase_id, $created_at)
{
    $tab = "";



    $conf = new Conf();

    $mail = new PHPMailer();
    $mail->IsSMTP();
    $mail->Mailer = "smtp";
    $mail->CharSet = 'UTF-8';
    $mail->Encoding = 'base64';

    // $mail->SMTPDebug  = 0;
    // $mail->SMTPAuth   = true;
    // $mail->SMTPSecure = "ssl";
    // $mail->Port       = 465;
    // $mail->SMTPKeepAlive = true;
    // $mail->Host       = $conf::$mail_host;
    // $mail->Username   = $conf::$mail_username;
    // $mail->Password   = $conf::$mail_password;

    $mail = SetupMail($mail, $conf);

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

    $_revisor = "";
    if($revise_id != 0)
    {
        $revisor = GetNotifiers($revise_id);
        foreach($revisor as &$list)
        {
            $_revisor = $list["username"];
        }
    }

    $_erasor = "";
    if($erase_id != 0)
    {
        $erasor = GetNotifiers($erase_id);
        foreach($erasor as &$list)
        {
            $_erasor = $list["username"];
        }
    }

    $creators = rtrim($creators, ", ");
    $assignees = rtrim($assignees, ", ");
    $collaborators = rtrim($collaborators, ", ");

    $mail->SetFrom("feliix.it@gmail.com", "Feliix.System");
    $mail->AddReplyTo("feliix.it@gmail.com", "Feliix.System");

    switch ($request_type) {
        case "create":
            $tab = "<p>A new task was created and needs you to follow. Below is the details:</p>";
            $title = "[" . $assignees . "][Task Notification] Task " . $task_name . " was created";
            break;
        case "edit":
            $tab = "<p>A task was revised and needs you to follow. Below is the details:</p>";
            $title = "[" . $assignees . "][Task Notification] Task " . $task_name . " was revised";
            break;
        case "del":
            $tab = "<p>A existing task was deleted. Below is the details:</p>";
            $title = "[" . $assignees . "][Task Notification] Task " . $task_name . " was deleted";
            break;
        case "notify":
            $tab = "<p>Just a quick reminder that the due date of Task " . $task_name . " is " . $due_date . ". Below is the details:</p>";
            $title = "[" . $assignees . "][Task Reminder: Due Date is Near] Task " . $task_name . " ";
            break;
        default:
            return;
            break;
    }

    $mail->Subject = $title;
    $content =  "<p>Dear all,</p>";
    $content = $content . $tab;
    $content = $content . "<p>Task: " . $task_name . "</p>";
    $content = $content . "<p>Task Status: " . $task_status . "</p>";
    $content = $content . "<p>Creator: " . $creators . "</p>";

    if($_revisor != "")
        $content = $content . "<p>Reviser: " . $_revisor . "</p>";

    if($_erasor != "")
        $content = $content . "<p>Eraser: " . $_erasor . "</p>";

    $content = $content . "<p>Assignee: " . $assignees . "</p>";
    $content = $content . "<p>Collaborator: " . $collaborators . "</p>";
    $content = $content . "<p>Created at: " . $created_at . "</p>";
    $content = $content . "<p>Due Date: " . $due_date . "</p>";
    $content = $content . "<p>Description: " . $detail . "</p>";
    $content = $content . "<p> </p>";
    $content = $content . "<p>Click this link to view the target webpage: </p>";
    $content = $content . "<p>https://feliix.myvnc.com/task_management_SLS?sid=" . $stage_id . "</p>";

    $mail->MsgHTML($content);
    if($mail->Send()) {
        logMail($creators, $content);
        return true;
    } else {
        logMail($creators, $mail->ErrorInfo . $content);
        return false;
    }

}

function GetSvcMangerLeaveNotifiers()
{
    $database = new Database();
    $db = $database->getConnection();

    $sql = "SELECT user.id, username, email, title, department FROM user 
    LEFT JOIN user_department ON user.apartment_id = user_department.id LEFT JOIN user_title ON user.title_id = user_title.id
        WHERE user.title_id in (10, 15) and user.status = 1";

    $merged_results = array();

    $stmt = $db->prepare($sql);
    $stmt->execute();

    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        $merged_results[] = $row;
    }

    return $merged_results;
}

function task_notify_admin_sv($request_type, $task_status, $task_name, $stages_status, $create_id, $assignee, $collaborator, $due_date, $detail, $stage_id, $revise_id, $erase_id, $created_at)
{
    $tab = "";



    $conf = new Conf();

    $mail = new PHPMailer();
    $mail->IsSMTP();
    $mail->Mailer = "smtp";
    $mail->CharSet = 'UTF-8';
    $mail->Encoding = 'base64';

    // $mail->SMTPDebug  = 0;
    // $mail->SMTPAuth   = true;
    // $mail->SMTPSecure = "ssl";
    // $mail->Port       = 465;
    // $mail->SMTPKeepAlive = true;
    // $mail->Host       = $conf::$mail_host;
    // $mail->Username   = $conf::$mail_username;
    // $mail->Password   = $conf::$mail_password;

    $mail = SetupMail($mail, $conf);

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

    // 20220321 for service leave
    $notifior = GetSvcMangerLeaveNotifiers();
    foreach($notifior as &$list)
    {
        $mail->AddCC($list["email"], $list["username"]);
        $creators = $creators . $list["username"] . ", ";
    }

    $_revisor = "";
    if($revise_id != 0)
    {
        $revisor = GetNotifiers($revise_id);
        foreach($revisor as &$list)
        {
            $_revisor = $list["username"];
        }
    }

    $_erasor = "";
    if($erase_id != 0)
    {
        $erasor = GetNotifiers($erase_id);
        foreach($erasor as &$list)
        {
            $_erasor = $list["username"];
        }
    }

    $creators = rtrim($creators, ", ");
    $assignees = rtrim($assignees, ", ");
    $collaborators = rtrim($collaborators, ", ");

    $mail->SetFrom("feliix.it@gmail.com", "Feliix.System");
    $mail->AddReplyTo("feliix.it@gmail.com", "Feliix.System");

    switch ($request_type) {
        case "create":
            $tab = "<p>A new task was created and needs you to follow. Below is the details:</p>";
            $title = "[" . $assignees . "][Task Notification] Task " . $task_name . " was created";
            break;
        case "edit":
            $tab = "<p>A task was revised and needs you to follow. Below is the details:</p>";
            $title = "[" . $assignees . "][Task Notification] Task " . $task_name . " was revised";
            break;
        case "del":
            $tab = "<p>A existing task was deleted. Below is the details:</p>";
            $title = "[" . $assignees . "][Task Notification] Task " . $task_name . " was deleted";
            break;
        case "notify":
            $tab = "<p>Just a quick reminder that the due date of Task " . $task_name . " is " . $due_date . ". Below is the details:</p>";
            $title = "[" . $assignees . "][Task Reminder: Due Date is Near] Task " . $task_name . " ";
            break;
        default:
            return;
            break;
    }

    $mail->Subject = $title;
    $content =  "<p>Dear all,</p>";
    $content = $content . $tab;
    $content = $content . "<p>Task: " . $task_name . "</p>";
    $content = $content . "<p>Task Status: " . $task_status . "</p>";
    $content = $content . "<p>Creator: " . $creators . "</p>";

    if($_revisor != "")
        $content = $content . "<p>Reviser: " . $_revisor . "</p>";

    if($_erasor != "")
        $content = $content . "<p>Eraser: " . $_erasor . "</p>";

    $content = $content . "<p>Assignee: " . $assignees . "</p>";
    $content = $content . "<p>Collaborator: " . $collaborators . "</p>";
    $content = $content . "<p>Created at: " . $created_at . "</p>";
    $content = $content . "<p>Due Date: " . $due_date . "</p>";
    $content = $content . "<p>Description: " . $detail . "</p>";
    $content = $content . "<p> </p>";
    $content = $content . "<p>Click this link to view the target webpage: </p>";
    $content = $content . "<p>https://feliix.myvnc.com/task_management_SVC?sid=" . $stage_id . "</p>";

    $mail->MsgHTML($content);
    if($mail->Send()) {
        logMail($creators, $content);
        return true;
    } else {
        logMail($creators, $mail->ErrorInfo . $content);
        return false;
    }

}

function task_notify_admin_o($request_type, $task_status, $task_name, $stages_status, $create_id, $assignee, $collaborator, $due_date, $detail, $stage_id, $revise_id, $erase_id, $created_at)
{
    $tab = "";



    $conf = new Conf();

    $mail = new PHPMailer();
    $mail->IsSMTP();
    $mail->Mailer = "smtp";
    $mail->CharSet = 'UTF-8';
    $mail->Encoding = 'base64';

    // $mail->SMTPDebug  = 0;
    // $mail->SMTPAuth   = true;
    // $mail->SMTPSecure = "ssl";
    // $mail->Port       = 465;
    // $mail->SMTPKeepAlive = true;
    // $mail->Host       = $conf::$mail_host;
    // $mail->Username   = $conf::$mail_username;
    // $mail->Password   = $conf::$mail_password;

    $mail = SetupMail($mail, $conf);

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

    $_revisor = "";
    if($revise_id != 0)
    {
        $revisor = GetNotifiers($revise_id);
        foreach($revisor as &$list)
        {
            $_revisor = $list["username"];
        }
    }

    $_erasor = "";
    if($erase_id != 0)
    {
        $erasor = GetNotifiers($erase_id);
        foreach($erasor as &$list)
        {
            $_erasor = $list["username"];
        }
    }

    $creators = rtrim($creators, ", ");
    $assignees = rtrim($assignees, ", ");
    $collaborators = rtrim($collaborators, ", ");

    $mail->SetFrom("feliix.it@gmail.com", "Feliix.System");
    $mail->AddReplyTo("feliix.it@gmail.com", "Feliix.System");

    switch ($request_type) {
        case "create":
            $tab = "<p>A new task was created and needs you to follow. Below is the details:</p>";
            $title = "[" . $assignees . "][Task Notification] Task " . $task_name . " was created";
            break;
        case "edit":
            $tab = "<p>A task was revised and needs you to follow. Below is the details:</p>";
            $title = "[" . $assignees . "][Task Notification] Task " . $task_name . " was revised";
            break;
        case "del":
            $tab = "<p>A existing task was deleted. Below is the details:</p>";
            $title = "[" . $assignees . "][Task Notification] Task " . $task_name . " was deleted";
            break;
        case "notify":
            $tab = "<p>Just a quick reminder that the due date of Task " . $task_name . " is " . $due_date . ". Below is the details:</p>";
            $title = "[" . $assignees . "][Task Reminder: Due Date is Near] Task " . $task_name . " ";
            break;
        default:
            return;
            break;
    }

    $mail->Subject = $title;
    $content =  "<p>Dear all,</p>";
    $content = $content . $tab;
    $content = $content . "<p>Task: " . $task_name . "</p>";
    $content = $content . "<p>Task Status: " . $task_status . "</p>";
    $content = $content . "<p>Creator: " . $creators . "</p>";

    if($_revisor != "")
        $content = $content . "<p>Reviser: " . $_revisor . "</p>";

    if($_erasor != "")
        $content = $content . "<p>Eraser: " . $_erasor . "</p>";

    $content = $content . "<p>Assignee: " . $assignees . "</p>";
    $content = $content . "<p>Collaborator: " . $collaborators . "</p>";
    $content = $content . "<p>Created at: " . $created_at . "</p>";
    $content = $content . "<p>Due Date: " . $due_date . "</p>";
    $content = $content . "<p>Description: " . $detail . "</p>";
    $content = $content . "<p> </p>";
    $content = $content . "<p>Click this link to view the target webpage: </p>";
    $content = $content . "<p>https://feliix.myvnc.com/task_management_OS?sid=" . $stage_id . "</p>";

    $mail->MsgHTML($content);
    if($mail->Send()) {
        logMail($creators, $content);
        return true;
    } else {
        logMail($creators, $mail->ErrorInfo . $content);
        return false;
    }

}

function task_notify_admin_l($request_type, $task_status, $task_name, $stages_status, $create_id, $assignee, $collaborator, $due_date, $detail, $stage_id, $revise_id, $erase_id, $created_at)
{
    $tab = "";



    $conf = new Conf();

    $mail = new PHPMailer();
    $mail->IsSMTP();
    $mail->Mailer = "smtp";
    $mail->CharSet = 'UTF-8';
    $mail->Encoding = 'base64';

    // $mail->SMTPDebug  = 0;
    // $mail->SMTPAuth   = true;
    // $mail->SMTPSecure = "ssl";
    // $mail->Port       = 465;
    // $mail->SMTPKeepAlive = true;
    // $mail->Host       = $conf::$mail_host;
    // $mail->Username   = $conf::$mail_username;
    // $mail->Password   = $conf::$mail_password;

    $mail = SetupMail($mail, $conf);

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

    $_revisor = "";
    if($revise_id != 0)
    {
        $revisor = GetNotifiers($revise_id);
        foreach($revisor as &$list)
        {
            $_revisor = $list["username"];
        }
    }

    $_erasor = "";
    if($erase_id != 0)
    {
        $erasor = GetNotifiers($erase_id);
        foreach($erasor as &$list)
        {
            $_erasor = $list["username"];
        }
    }

    $creators = rtrim($creators, ", ");
    $assignees = rtrim($assignees, ", ");
    $collaborators = rtrim($collaborators, ", ");

    $mail->SetFrom("feliix.it@gmail.com", "Feliix.System");
    $mail->AddReplyTo("feliix.it@gmail.com", "Feliix.System");

    switch ($request_type) {
        case "create":
            $tab = "<p>A new task was created and needs you to follow. Below is the details:</p>";
            $title = "[" . $assignees . "][Task Notification] Task " . $task_name . " was created";
            break;
        case "edit":
            $tab = "<p>A task was revised and needs you to follow. Below is the details:</p>";
            $title = "[" . $assignees . "][Task Notification] Task " . $task_name . " was revised";
            break;
        case "del":
            $tab = "<p>A existing task was deleted. Below is the details:</p>";
            $title = "[" . $assignees . "][Task Notification] Task " . $task_name . " was deleted";
            break;
        case "notify":
            $tab = "<p>Just a quick reminder that the due date of Task " . $task_name . " is " . $due_date . ". Below is the details:</p>";
            $title = "[" . $assignees . "][Task Reminder: Due Date is Near] Task " . $task_name . " ";
            break;
        default:
            return;
            break;
    }

    $mail->Subject = $title;
    $content =  "<p>Dear all,</p>";
    $content = $content . $tab;
    $content = $content . "<p>Task: " . $task_name . "</p>";
    $content = $content . "<p>Task Status: " . $task_status . "</p>";
    $content = $content . "<p>Creator: " . $creators . "</p>";

    if($_revisor != "")
        $content = $content . "<p>Reviser: " . $_revisor . "</p>";

    if($_erasor != "")
        $content = $content . "<p>Eraser: " . $_erasor . "</p>";

    $content = $content . "<p>Assignee: " . $assignees . "</p>";
    $content = $content . "<p>Collaborator: " . $collaborators . "</p>";
    $content = $content . "<p>Created at: " . $created_at . "</p>";
    $content = $content . "<p>Due Date: " . $due_date . "</p>";
    $content = $content . "<p>Description: " . $detail . "</p>";
    $content = $content . "<p> </p>";
    $content = $content . "<p>Click this link to view the target webpage: </p>";
    $content = $content . "<p>https://feliix.myvnc.com/task_management_LT?sid=" . $stage_id . "</p>";

    $mail->MsgHTML($content);
    if($mail->Send()) {
        logMail($creators, $content);
        return true;
    } else {
        logMail($creators, $mail->ErrorInfo . $content);
        return false;
    }

}

function stage_close_notify($project_creator_id, $project_id, $project_name, $stage_name, $modify_name, $stage_creator_name, $stage_create_at, $title, $cc_to)
{
    $conf = new Conf();

    $mail = new PHPMailer();
    $mail->IsSMTP();
    $mail->Mailer = "smtp";
    $mail->CharSet = 'UTF-8';
    $mail->Encoding = 'base64';

    // $mail->SMTPDebug  = 0;
    // $mail->SMTPAuth   = true;
    // $mail->SMTPSecure = "ssl";
    // $mail->Port       = 465;
    // $mail->SMTPKeepAlive = true;
    // $mail->Host       = $conf::$mail_host;
    // $mail->Username   = $conf::$mail_username;
    // $mail->Password   = $conf::$mail_password;
    
    $mail = SetupMail($mail, $conf);

    $mail->IsHTML(true);

    $notifior = array();

    $cc = "";
    $creator = "";

    $notifior = GetNotifiers($project_creator_id);
    foreach($notifior as &$list)
    {
        $mail->AddAddress($list["email"], $list["username"]);
        $creator = $creator . $list["username"] . ", ";
    }

    $notifior = GetNotifiers($cc_to);
    foreach($notifior as &$list)
    {
        $mail->AddAddress($list["email"], $list["username"]);
        $cc = $cc . $list["username"] . ", ";
    }
    
    // 當專案中的Order Stage的狀態變為Close時(function stage_close_notify)，系統發出的通知信中需要額外加入「職位為 Service Manager」和「職位為 Warehouse in charge」的人員
    if($stage_name == "Order")
    {
        $notifior = GetChargeNotifiersByTitle('Warehouse in charge');
        foreach($notifior as &$list)
        {
            $mail->AddAddress($list["email"], $list["username"]);
        }

        $category_id = GetProjectCategoryByProjectId($project_id);
        if($category_id == "2")
        {
            $notifior = GetChargeNotifiersByTitle('Engineering Manager');
            foreach($notifior as &$list)
            {
                $mail->AddAddress($list["email"], $list["username"]);
            }
        }
        
    }
    

    $mail->SetFrom("feliix.it@gmail.com", "Feliix.System");
    $mail->AddReplyTo("feliix.it@gmail.com", "Feliix.System");

    $mail->Subject = $title;
    $content =  "<p>Dear all,</p>";
    $content = $content . '<p>Stage "' . $stage_name . '" was closed in Project "' . $project_name . '" by ' . $modify_name . '. Following are the details:</p>';
    $content = $content . "<p>Project Name: " . $project_name . "</p>";
    $content = $content . "<p>Stage: " . $stage_name . "</p>";
    $content = $content . "<p>Status of Stage: Close</p>";
    $content = $content . "<p>Stage Creator: " . $stage_creator_name . " at " . $stage_create_at . "</p>";
    $content = $content . "<p> </p>";
    $content = $content . "<p>Click this link to view the target webpage: </p>";
    $content = $content . "<p>https://feliix.myvnc.com/project02?p=" . $project_id . "</p>";

    $mail->MsgHTML($content);
    if($mail->Send()) {
        logMail($creator, $content);
        return true;
    } else {
        logMail($creator, $mail->ErrorInfo . $title . $cc .  $content);
        return false;
    }

}

function stage_order_close_notify($project_creator_id, $project_id, $project_name, $stage_name, $modify_name, $stage_creator_name, $stage_create_at, $title, $cc_to)
{
    $conf = new Conf();

    $mail = new PHPMailer();
    $mail->IsSMTP();
    $mail->Mailer = "smtp";
    $mail->CharSet = 'UTF-8';
    $mail->Encoding = 'base64';

    // $mail->SMTPDebug  = 0;
    // $mail->SMTPAuth   = true;
    // $mail->SMTPSecure = "ssl";
    // $mail->Port       = 465;
    // $mail->SMTPKeepAlive = true;
    // $mail->Host       = $conf::$mail_host;
    // $mail->Username   = $conf::$mail_username;
    // $mail->Password   = $conf::$mail_password;

    $mail = SetupMail($mail, $conf);
    
    $mail->IsHTML(true);

    $notifior = array();

    $cc = "";
    $creator = "";


    // 當專案中的Order Stage的狀態變為Close時(function stage_close_notify)，系統發出的通知信中需要額外加入「職位為 Service Manager」和「職位為 Warehouse in charge」的人員
    if($stage_name == "Order")
    {
        $notifior = GetChargeNotifiersByTitle('Warehouse in charge');
        foreach($notifior as &$list)
        {
            $mail->AddAddress($list["email"], $list["username"]);
        }

        $category_id = GetProjectCategoryByProjectId($project_id);
        if($category_id == "2")
        {
            $notifior = GetChargeNotifiersByTitle('Engineering Manager');
            foreach($notifior as &$list)
            {
                $mail->AddAddress($list["email"], $list["username"]);
            }
        }
        
    }
    

    $mail->SetFrom("feliix.it@gmail.com", "Feliix.System");
    $mail->AddReplyTo("feliix.it@gmail.com", "Feliix.System");

    $mail->Subject = $title;
    $content =  "<p>Dear all,</p>";
    $content = $content . '<p>Stage "' . $stage_name . '" was closed in Project "' . $project_name . '" by ' . $modify_name . '. Following are the details:</p>';
    $content = $content . "<p>Project Name: " . $project_name . "</p>";
    $content = $content . "<p>Stage: " . $stage_name . "</p>";
    $content = $content . "<p>Status of Stage: Close</p>";
    $content = $content . "<p>Stage Creator: " . $stage_creator_name . " at " . $stage_create_at . "</p>";
    $content = $content . "<p> </p>";
    $content = $content . "<p>Click this link to view the target webpage: </p>";
    $content = $content . "<p>https://feliix.myvnc.com/project02?p=" . $project_id . "</p>";

    $mail->MsgHTML($content);
    if($mail->Send()) {
        logMail($creator, $content);
        return true;
    } else {
        logMail($creator, $mail->ErrorInfo . $title . $cc .  $content);
        return false;
    }

}


function task_notify_admin($request_type, $task_status, $task_name, $stages_status, $create_id, $assignee, $collaborator, $due_date, $detail, $stage_id, $revise_id, $erase_id, $created_at)
{
    $tab = "";



    $conf = new Conf();

    $mail = new PHPMailer();
    $mail->IsSMTP();
    $mail->Mailer = "smtp";
    $mail->CharSet = 'UTF-8';
    $mail->Encoding = 'base64';

    // $mail->SMTPDebug  = 0;
    // $mail->SMTPAuth   = true;
    // $mail->SMTPSecure = "ssl";
    // $mail->Port       = 465;
    // $mail->SMTPKeepAlive = true;
    // $mail->Host       = $conf::$mail_host;
    // $mail->Username   = $conf::$mail_username;
    // $mail->Password   = $conf::$mail_password;

    $mail = SetupMail($mail, $conf);

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

    $_revisor = "";
    if($revise_id != 0)
    {
        $revisor = GetNotifiers($revise_id);
        foreach($revisor as &$list)
        {
            $_revisor = $list["username"];
        }
    }

    $_erasor = "";
    if($erase_id != 0)
    {
        $erasor = GetNotifiers($erase_id);
        foreach($erasor as &$list)
        {
            $_erasor = $list["username"];
        }
    }

    $creators = rtrim($creators, ", ");
    $assignees = rtrim($assignees, ", ");
    $collaborators = rtrim($collaborators, ", ");

    $mail->SetFrom("feliix.it@gmail.com", "Feliix.System");
    $mail->AddReplyTo("feliix.it@gmail.com", "Feliix.System");

    switch ($request_type) {
        case "create":
            $tab = "<p>A new task was created and needs you to follow. Below is the details:</p>";
            $title = "[" . $assignees . "][Task Notification] Task " . $task_name . " was created";
            break;
        case "edit":
            $tab = "<p>A task was revised and needs you to follow. Below is the details:</p>";
            $title = "[" . $assignees . "][Task Notification] Task " . $task_name . " was revised";
            break;
        case "del":
            $tab = "<p>A existing task was deleted. Below is the details:</p>";
            $title = "[" . $assignees . "][Task Notification] Task " . $task_name . " was deleted";
            break;
        case "notify":
            $tab = "<p>Just a quick reminder that the due date of Task " . $task_name . " is " . $due_date . ". Below is the details:</p>";
            $title = "[" . $assignees . "][Task Reminder: Due Date is Near] Task " . $task_name . " ";
            break;
        default:
            return;
            break;
    }

    $mail->Subject = $title;
    $content =  "<p>Dear all,</p>";
    $content = $content . $tab;
    $content = $content . "<p>Task: " . $task_name . "</p>";
    $content = $content . "<p>Task Status: " . $task_status . "</p>";
    $content = $content . "<p>Creator: " . $creators . "</p>";

    if($_revisor != "")
        $content = $content . "<p>Reviser: " . $_revisor . "</p>";

    if($_erasor != "")
        $content = $content . "<p>Eraser: " . $_erasor . "</p>";

    $content = $content . "<p>Assignee: " . $assignees . "</p>";
    $content = $content . "<p>Collaborator: " . $collaborators . "</p>";
    $content = $content . "<p>Created at: " . $created_at . "</p>";
    $content = $content . "<p>Due Date: " . $due_date . "</p>";
    $content = $content . "<p>Description: " . $detail . "</p>";
    $content = $content . "<p> </p>";
    $content = $content . "<p>Click this link to view the target webpage: </p>";
    $content = $content . "<p>https://feliix.myvnc.com/task_management_AD?sid=" . $stage_id . "</p>";

    $mail->MsgHTML($content);
    if($mail->Send()) {
        logMail($creators, $content);
        return true;
    } else {
        logMail($creators, $mail->ErrorInfo . $content);
        return false;
    }

}

function task_notify($request_type, $project_name, $task_name, $stages_status, $create_id, $assignee, $collaborator, $due_date, $detail, $stage_id, $created_at)
{
    $tab = "";

    switch ($request_type) {
        case "create":
            $tab = "<p>A new task was created and needs you to follow. Below is the details:</p>";
            break;
        case "edit":
            $tab = "<p>A task was revised and needs you to follow. Below is the details:</p>";
            break;
        case "del":
            $tab = "<p>A existing task was deleted. Below is the details:</p>";
            break;
        case "notify":
            $tab = "<p>Just a quick reminder that the due date of Task " . $task_name . " is " . $due_date . ". Below is the details:</p>";
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


    // $mail->SMTPDebug  = 0;
    // $mail->SMTPAuth   = true;
    // $mail->SMTPSecure = "ssl";
    // $mail->Port       = 465;
    // $mail->SMTPKeepAlive = true;
    // $mail->Host       = $conf::$mail_host;
    // $mail->Username   = $conf::$mail_username;
    // $mail->Password   = $conf::$mail_password;

    $mail = SetupMail($mail, $conf);


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

    // 在Order Stage中，當使用者XXXX Task，系統發出的通知信中需要額外加入「職位為 Service Manager」和「職位為 Warehouse in charge」的人員
    if($stages_status == "Order")
    {
        $notifior = GetChargeNotifiersByTitle('Warehouse in charge');
        foreach($notifior as &$list)
        {
            $mail->AddAddress($list["email"], $list["username"]);
        }

        $category_id = GetProjectCategoryByStageId($stage_id);
        if($category_id == "2")
        {
            $notifior = GetChargeNotifiersByTitle('Engineering Manager');
            foreach($notifior as &$list)
            {
                $mail->AddAddress($list["email"], $list["username"]);
            }
        }

    }

    $creators = rtrim($creators, ", ");
    $assignees = rtrim($assignees, ", ");
    $collaborators = rtrim($collaborators, ", ");

    $mail->SetFrom("feliix.it@gmail.com", "Feliix.System");
    $mail->AddReplyTo("feliix.it@gmail.com", "Feliix.System");

    $title = "[" . $assignees . "][Task Notification] " . $project_name . " - " . $task_name . " ";

    if($request_type == "notify")
        $title = "[" . $assignees . "][Task Reminder: Due Date is Near] " . $project_name . " - " . $task_name . " ";

    $mail->Subject = $title;
    $content =  "<p>Dear all,</p>";
    $content = $content . $tab;
    $content = $content . "<p>Project Name: " . $project_name . "</p>";
    $content = $content . "<p>Stage: " . $stages_status . "</p>";
    $content = $content . "<p>Task: " . $task_name . "</p>";
    $content = $content . "<p>Creator: " . $creators . "</p>";
    $content = $content . "<p>Assignee: " . $assignees . "</p>";
    $content = $content . "<p>Collaborator: " . $collaborators . "</p>";
    $content = $content . "<p>Created at: " . $created_at . "</p>";
    $content = $content . "<p>Due Date: " . $due_date . "</p>";
    $content = $content . "<p>Description: " . $detail . "</p>";
    $content = $content . "<p> </p>";
    $content = $content . "<p>Please click this link to view the target webpage: </p>";
    $content = $content . "<p>https://feliix.myvnc.com/project03_other?sid=" . $stage_id . "</p>";

    $mail->MsgHTML($content);
    if($mail->Send()) {
        logMail($creators, $content);
        return true;
    } else {
        logMail($creators, $mail->ErrorInfo . $content);
        return false;
    }

}


function task_notify_r($request_type, $project_name, $task_name, $stages_status, $create_id, $assignee, $collaborator, $due_date, $detail, $stage_id, $created_at)
{
    $tab = "";

    switch ($request_type) {
        case "create":
            $tab = "<p>A new message was created and needs you to follow. Below is the details:</p>";
            break;
        case "edit":
            $tab = "<p>A message was revised and needs you to follow. Below is the details:</p>";
            break;
        case "del":
            $tab = "<p>A existing message was deleted. Below is the details:</p>";
            break;
        case "notify":
            $tab = "<p>Just a quick reminder that the due date of Message " . $task_name . " is " . $due_date . ". Below is the details:</p>";
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

    
    // $mail->SMTPDebug  = 0;
    // $mail->SMTPAuth   = true;
    // $mail->SMTPSecure = "ssl";
    // $mail->Port       = 465;
    // $mail->SMTPKeepAlive = true;
    // $mail->Host       = $conf::$mail_host;
    // $mail->Username   = $conf::$mail_username;
    // $mail->Password   = $conf::$mail_password;

    $mail = SetupMail($mail, $conf);
    

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

    // 在Order Stage中，當使用者XXXX Task，系統發出的通知信中需要額外加入「職位為 Service Manager」和「職位為 Warehouse in charge」的人員
    if($stages_status == "Order")
    {
        $notifior = GetChargeNotifiersByTitle('Warehouse in charge');
        foreach($notifior as &$list)
        {
            $mail->AddAddress($list["email"], $list["username"]);
        }

        $category_id = GetProjectCategoryByStageId($stage_id);
        if($category_id == "2")
        {
            $notifior = GetChargeNotifiersByTitle('Engineering Manager');
            foreach($notifior as &$list)
            {
                $mail->AddAddress($list["email"], $list["username"]);
            }
        }
        
    }

    $creators = rtrim($creators, ", ");
    $assignees = rtrim($assignees, ", ");
    $collaborators = rtrim($collaborators, ", ");
    
    $mail->SetFrom("feliix.it@gmail.com", "Feliix.System");
    $mail->AddReplyTo("feliix.it@gmail.com", "Feliix.System");

    $title = "[Message Notification] " . $project_name . " - " . $task_name . " ";

    if($request_type == "notify")
        $title = "[Message Reminder: Due Date is Near] " . $project_name . " - " . $task_name . " ";
    
    $mail->Subject = $title;
    $content =  "<p>Dear all,</p>";
    $content = $content . $tab;
    $content = $content . "<p>Project Name: " . $project_name . "</p>";
    $content = $content . "<p>Stage: " . $stages_status . "</p>";
    $content = $content . "<p>Message Title: " . $task_name . "</p>";
    $content = $content . "<p>Creator: " . $creators . "</p>";
    $content = $content . "<p>Assignee: " . $assignees . "</p>";
    //$content = $content . "<p>Collaborator: " . $collaborators . "</p>";
    $content = $content . "<p>Created at: " . $created_at . "</p>";
    //$content = $content . "<p>Due Date: " . $due_date . "</p>";
    $content = $content . "<p>Description: " . $detail . "</p>";
    $content = $content . "<p> </p>";
    $content = $content . "<p>Please click this link to view the target webpage: </p>";
    $content = $content . "<p>https://feliix.myvnc.com/project03_other?sid=" . $stage_id . "</p>";

    $mail->MsgHTML($content);
    if($mail->Send()) {
        logMail($creators, $content);
        return true;
    } else {
        logMail($creators, $mail->ErrorInfo . $content);
        return false;
    }

}


function task_notify_order($request_type, $project_name, $task_name, $stages_status, $create_id, $assignee, $collaborator, $due_date, $detail, $stage_id, $created_at, $order_type, $order_name)
{
    $tab = "";

    switch ($request_type) {
        case "create":
            $tab = "<p>A new order task was created and needs you to follow. Below is the details:</p>";
            break;
        case "edit":
            $tab = "<p>A order task was revised and needs you to follow. Below is the details:</p>";
            break;
        case "del":
            $tab = "<p>A existing order task was deleted. Below is the details:</p>";
            break;
        case "notify":
            $tab = "<p>Just a quick reminder that the due date of Order Task " . $task_name . " is " . $due_date . ". Below is the details:</p>";
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


    // $mail->SMTPDebug  = 0;
    // $mail->SMTPAuth   = true;
    // $mail->SMTPSecure = "ssl";
    // $mail->Port       = 465;
    // $mail->SMTPKeepAlive = true;
    // $mail->Host       = $conf::$mail_host;
    // $mail->Username   = $conf::$mail_username;
    // $mail->Password   = $conf::$mail_password;

    $mail = SetupMail($mail, $conf);


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

    // 在Order Stage中，當使用者XXXX Task，系統發出的通知信中需要額外加入「職位為 Service Manager」和「職位為 Warehouse in charge」的人員
    if($stages_status == "Order")
    {
        $notifior = GetChargeNotifiersByTitle('Warehouse in charge');
        foreach($notifior as &$list)
        {
            $mail->AddAddress($list["email"], $list["username"]);
        }

        $catagory_id = GetProjectCategoryByStageId($stage_id);
        if($catagory_id == "2")
        {
            $notifior = GetChargeNotifiersByTitle('Engineering Manager');
            foreach($notifior as &$list)
            {
                $mail->AddAddress($list["email"], $list["username"]);
            }
        }


    }

    $creators = rtrim($creators, ", ");
    $assignees = rtrim($assignees, ", ");
    $collaborators = rtrim($collaborators, ", ");

    $mail->SetFrom("feliix.it@gmail.com", "Feliix.System");
    $mail->AddReplyTo("feliix.it@gmail.com", "Feliix.System");

    //如果是訂單任務，執行下一行
    $title = "[" . $assignees . "][Order Task Notification] " . $project_name . " - " . $task_name . " ";

    //如果是訂單任務，執行下兩行
    if($request_type == "notify")
        $title = "[" . $assignees . "][Order Task Reminder: Due Date is Near] " . $project_name . " - " . $task_name . " ";

    $mail->Subject = $title;
    $content =  "<p>Dear all,</p>";
    $content = $content . $tab;
    $content = $content . "<p>Project Name: " . $project_name . "</p>";
    $content = $content . "<p>Stage: " . $stages_status . "</p>";

    //如果是訂單任務，執行下三行
    $content = $content . "<p>Order Task: " . $task_name . "</p>";
    $content = $content . "<p>Order Type: " . $order_type . "</p>";
    $content = $content . "<p>Order Name: " . $order_name . "</p>";

    $content = $content . "<p>Creator: " . $creators . "</p>";
    $content = $content . "<p>Assignee: " . $assignees . "</p>";
    $content = $content . "<p>Collaborator: " . $collaborators . "</p>";
    $content = $content . "<p>Created at: " . $created_at . "</p>";
    $content = $content . "<p>Due Date: " . $due_date . "</p>";
    $content = $content . "<p>Description: " . $detail . "</p>";
    $content = $content . "<p> </p>";
    $content = $content . "<p>Please click this link to view the target webpage: </p>";
    $content = $content . "<p>https://feliix.myvnc.com/project03_other?sid=" . $stage_id . "</p>";

    $mail->MsgHTML($content);
    if($mail->Send()) {
        logMail($creators, $content);
        return true;
    } else {
        logMail($creators, $mail->ErrorInfo . $content);
        return false;
    }

}



function task_notify_inquiry($request_type, $project_name, $task_name, $stages_status, $create_id, $assignee, $collaborator, $due_date, $detail, $stage_id, $created_at, $order_type, $order_name, $serial_name)
{
    $tab = "";

    switch ($request_type) {
        case "create":
            $tab = "<p>A new inquiry task was created and needs you to follow. Below is the details:</p>";
            break;
        case "edit":
            $tab = "<p>A inquiry task was revised and needs you to follow. Below is the details:</p>";
            break;
        case "del":
            $tab = "<p>A existing inquiry task was deleted. Below is the details:</p>";
            break;
        case "notify":
            $tab = "<p>Just a quick reminder that the due date of inquiry Task " . $task_name . " is " . $due_date . ". Below is the details:</p>";
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


    // $mail->SMTPDebug  = 0;
    // $mail->SMTPAuth   = true;
    // $mail->SMTPSecure = "ssl";
    // $mail->Port       = 465;
    // $mail->SMTPKeepAlive = true;
    // $mail->Host       = $conf::$mail_host;
    // $mail->Username   = $conf::$mail_username;
    // $mail->Password   = $conf::$mail_password;

    $mail = SetupMail($mail, $conf);


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

    // 在Order Stage中，當使用者XXXX Task，系統發出的通知信中需要額外加入「職位為 Service Manager」和「職位為 Warehouse in charge」的人員
    if($stages_status == "Order")
    {
        $notifior = GetChargeNotifiersByTitle('Warehouse in charge');
        foreach($notifior as &$list)
        {
            $mail->AddAddress($list["email"], $list["username"]);
        }

        $catagory_id = GetProjectCategoryByStageId($stage_id);
        if($catagory_id == "2")
        {
            $notifior = GetChargeNotifiersByTitle('Engineering Manager');
            foreach($notifior as &$list)
            {
                $mail->AddAddress($list["email"], $list["username"]);
            }
        }

    }

    $creators = rtrim($creators, ", ");
    $assignees = rtrim($assignees, ", ");
    $collaborators = rtrim($collaborators, ", ");

    $mail->SetFrom("feliix.it@gmail.com", "Feliix.System");
    $mail->AddReplyTo("feliix.it@gmail.com", "Feliix.System");

    //如果是訂單任務，執行下一行
    $title = "[" . $assignees . "][Inquiry Task Notification] " . $project_name . " - " . $task_name . " ";

    //如果是訂單任務，執行下兩行
    if($request_type == "notify")
        $title = "[" . $assignees . "][Inquiry Task Reminder: Due Date is Near] " . $project_name . " - " . $task_name . " ";

    $mail->Subject = $title;
    $content =  "<p>Dear all,</p>";
    $content = $content . $tab;
    $content = $content . "<p>Project Name: " . $project_name . "</p>";
    $content = $content . "<p>Stage: " . $stages_status . "</p>";

    //如果是訂單任務，執行下三行
    $content = $content . "<p>Inquiry Task: " . $task_name . "</p>";
    // $content = $content . "<p>Order Type: " . $order_type . "</p>";
    $content = $content . "<p>Inquiry Name: " . $serial_name . " "  . $order_name . "</p>";

    $content = $content . "<p>Creator: " . $creators . "</p>";
    $content = $content . "<p>Assignee: " . $assignees . "</p>";
    $content = $content . "<p>Collaborator: " . $collaborators . "</p>";
    $content = $content . "<p>Created at: " . $created_at . "</p>";
    $content = $content . "<p>Due Date: " . $due_date . "</p>";
    $content = $content . "<p>Description: " . $detail . "</p>";
    $content = $content . "<p> </p>";
    $content = $content . "<p>Please click this link to view the target webpage: </p>";
    $content = $content . "<p>https://feliix.myvnc.com/project03_other?sid=" . $stage_id . "</p>";

    $mail->MsgHTML($content);
    if($mail->Send()) {
        logMail($creators, $content);
        return true;
    } else {
        logMail($creators, $mail->ErrorInfo . $content);
        return false;
    }

}




function task_notify_type_order($request_type, $project_name, $task_name, $stages_status, $create_id, $assignee, $collaborator, $due_date, $detail, $stage_id, $created_at, $order_type, $order_name, $task_type)
{
    $tab = "";

    switch ($request_type) {
        case "create":
            $tab = "<p>A new order task was created and needs you to follow. Below is the details:</p>";
            break;
        case "edit":
            $tab = "<p>A order task was revised and needs you to follow. Below is the details:</p>";
            break;
        case "del":
            $tab = "<p>A existing order task was deleted. Below is the details:</p>";
            break;
        case "notify":
            $tab = "<p>Just a quick reminder that the due date of Order Task " . $task_name . " is " . $due_date . ". Below is the details:</p>";
            break;
        default:
            return;
            break;
    }

    $task_department = "";
    if($task_type == 'LT')
        $task_department = "Lighting";
    if($task_type == 'OS')
        $task_department = "Office Systems";
    if($task_type == 'SLS')
        $task_department = "Sales";
    if($task_type == 'SVC')
        $task_department = "Engineering Department";

    $conf = new Conf();

    $mail = new PHPMailer();
    $mail->IsSMTP();
    $mail->Mailer = "smtp";
    $mail->CharSet = 'UTF-8';
    $mail->Encoding = 'base64';


    // $mail->SMTPDebug  = 0;
    // $mail->SMTPAuth   = true;
    // $mail->SMTPSecure = "ssl";
    // $mail->Port       = 465;
    // $mail->SMTPKeepAlive = true;
    // $mail->Host       = $conf::$mail_host;
    // $mail->Username   = $conf::$mail_username;
    // $mail->Password   = $conf::$mail_password;

    $mail = SetupMail($mail, $conf);


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

    // 在Order Stage中，當使用者XXXX Task，系統發出的通知信中需要額外加入「職位為 Service Manager」和「職位為 Warehouse in charge」的人員
    if($stages_status == "Order")
    {
        $notifior = GetChargeNotifiersByTitle('Warehouse in charge');
        foreach($notifior as &$list)
        {
            $mail->AddAddress($list["email"], $list["username"]);
        }

        $catagory_id = GetProjectCategoryByStageId($stage_id);
        if($catagory_id == "2")
        {
            $notifior = GetChargeNotifiersByTitle('Engineering Manager');
            foreach($notifior as &$list)
            {
                $mail->AddAddress($list["email"], $list["username"]);
            }
        }

    }

    $creators = rtrim($creators, ", ");
    $assignees = rtrim($assignees, ", ");
    $collaborators = rtrim($collaborators, ", ");

    $mail->SetFrom("feliix.it@gmail.com", "Feliix.System");
    $mail->AddReplyTo("feliix.it@gmail.com", "Feliix.System");

    //如果是訂單任務，執行下一行
    $title = "[" . $assignees . "][Order Task Notification] " . $project_name . " - " . $task_name . " ";

    //如果是訂單任務，執行下兩行
    if($request_type == "notify")
        $title = "[" . $assignees . "][Order Task Reminder: Due Date is Near] " . $project_name . " - " . $task_name . " ";

    $mail->Subject = $title;
    $content =  "<p>Dear all,</p>";
    $content = $content . $tab;
    $content = $content . "<p>Task Management of " . $task_department . " Department</p>";
    //$content = $content . "<p>Stage: " . $stages_status . "</p>";

    //如果是訂單任務，執行下三行
    $content = $content . "<p>Order Task: " . $task_name . "</p>";
    $content = $content . "<p>Order Type: " . $order_type . "</p>";
    $content = $content . "<p>Order Name: " . $order_name . "</p>";

    $content = $content . "<p>Creator: " . $creators . "</p>";
    $content = $content . "<p>Assignee: " . $assignees . "</p>";
    $content = $content . "<p>Collaborator: " . $collaborators . "</p>";
    $content = $content . "<p>Created at: " . $created_at . "</p>";
    $content = $content . "<p>Due Date: " . $due_date . "</p>";
    $content = $content . "<p>Description: " . $detail . "</p>";
    $content = $content . "<p> </p>";
    $content = $content . "<p>Please click this link to view the target webpage: </p>";
    $content = $content . "<p>https://feliix.myvnc.com/task_management_" . $task_type . "?sid=" . $stage_id . "</p>";

    $mail->MsgHTML($content);
    if($mail->Send()) {
        logMail($creators, $content);
        return true;
    } else {
        logMail($creators, $mail->ErrorInfo . $content);
        return false;
    }

}

function batch_date_start_company_notify_mail($user_array)
{
    $tab = "<p>Seniority of employee(s) changed. You may need to change the corresponding yearly leave credits for the employee(s). Below is the details of the afftected employee(s) and their seniority change:</p>";

    $conf = new Conf();

    $mail = new PHPMailer();
    $mail->IsSMTP();
    $mail->Mailer = "smtp";
    $mail->CharSet = 'UTF-8';
    $mail->Encoding = 'base64';

    // $mail->SMTPDebug  = 0;
    // $mail->SMTPAuth   = true;
    // $mail->SMTPSecure = "ssl";
    // $mail->Port       = 465;
    // $mail->SMTPKeepAlive = true;
    // $mail->Host       = $conf::$mail_host;
    // $mail->Username   = $conf::$mail_username;
    // $mail->Password   = $conf::$mail_password;

    $mail = SetupMail($mail, $conf);

    $mail->IsHTML(true);

    $mail->AddAddress("dennis@feliix.com", "Dennis");

    $mail->SetFrom("feliix.it@gmail.com", "Feliix.System");
    $mail->AddReplyTo("feliix.it@gmail.com", "Feliix.System");

    $title = "[Seniority Change Notification by System]";

    $mail->Subject = $title;
    $content =  "<p>Dear Sir and Maddam,</p>";
    $content = $content . $tab;
    $content = $content . "<p> </p>";

    foreach ($user_array as $user)
    {
        $content = $content . "<p>Employee Name: " . $user["username"] . "</p>";
        $content = $content . "<p>Seniority Change: " . $user["seniority_old"] . " -> " . $user["seniority_new"] . " </p>";
        $content = $content . "<p> </p>";
    }
    
    $content = $content . "<p>Please log on to Feliix >> System Section >> User to adjust yearly leave credits.";
    $content = $content . "<p>URL: " . $conf::$mail_ip . "</p>";

    $mail->MsgHTML($content);
    if($mail->Send()) {
        logMail("", $content);
        return true;
    } else {
        logMail("", $mail->ErrorInfo . $content);
        return false;
    }
}

function task_notify_type_inquiry($request_type, $project_name, $task_name, $stages_status, $create_id, $assignee, $collaborator, $due_date, $detail, $stage_id, $created_at, $order_type, $order_name, $task_type)
{
    $tab = "";

    switch ($request_type) {
        case "create":
            $tab = "<p>A new inquiry task was created and needs you to follow. Below is the details:</p>";
            break;
        case "edit":
            $tab = "<p>A inquiry task was revised and needs you to follow. Below is the details:</p>";
            break;
        case "del":
            $tab = "<p>A existing inquiry task was deleted. Below is the details:</p>";
            break;
        case "notify":
            $tab = "<p>Just a quick reminder that the due date of Inquiry Task " . $task_name . " is " . $due_date . ". Below is the details:</p>";
            break;
        default:
            return;
            break;
    }

    $task_department = "";
    if($task_type == 'LT')
        $task_department = "Lighting";
    if($task_type == 'OS')
        $task_department = "Office Systems";
    if($task_type == 'SLS')
        $task_department = "Sales";
    if($task_type == 'SVC')
        $task_department = "Engineering Department";

    $conf = new Conf();

    $mail = new PHPMailer();
    $mail->IsSMTP();
    $mail->Mailer = "smtp";
    $mail->CharSet = 'UTF-8';
    $mail->Encoding = 'base64';


    // $mail->SMTPDebug  = 0;
    // $mail->SMTPAuth   = true;
    // $mail->SMTPSecure = "ssl";
    // $mail->Port       = 465;
    // $mail->SMTPKeepAlive = true;
    // $mail->Host       = $conf::$mail_host;
    // $mail->Username   = $conf::$mail_username;
    // $mail->Password   = $conf::$mail_password;

    $mail = SetupMail($mail, $conf);


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

    // 在Order Stage中，當使用者XXXX Task，系統發出的通知信中需要額外加入「職位為 Service Manager」和「職位為 Warehouse in charge」的人員
    if($stages_status == "Order")
    {
        $notifior = GetChargeNotifiersByTitle('Warehouse in charge');
        foreach($notifior as &$list)
        {
            $mail->AddAddress($list["email"], $list["username"]);
        }

        $catagory_id = GetProjectCategoryByStageId($stage_id);
        if($catagory_id == "2")
        {
            $notifior = GetChargeNotifiersByTitle('Engineering Manager');
            foreach($notifior as &$list)
            {
                $mail->AddAddress($list["email"], $list["username"]);
            }
        }

    }

    $creators = rtrim($creators, ", ");
    $assignees = rtrim($assignees, ", ");
    $collaborators = rtrim($collaborators, ", ");

    $mail->SetFrom("feliix.it@gmail.com", "Feliix.System");
    $mail->AddReplyTo("feliix.it@gmail.com", "Feliix.System");

    //如果是訂單任務，執行下一行
    $title = "[" . $assignees . "][Inquiry Task Notification] " . $project_name . " - " . $task_name . " ";

    //如果是訂單任務，執行下兩行
    if($request_type == "notify")
        $title = "[" . $assignees . "][Inquiry Task Reminder: Due Date is Near] " . $project_name . " - " . $task_name . " ";

    $mail->Subject = $title;
    $content =  "<p>Dear all,</p>";
    $content = $content . $tab;
    $content = $content . "<p>Task Management of " . $task_department . " Department</p>";
    //$content = $content . "<p>Stage: " . $stages_status . "</p>";

    //如果是訂單任務，執行下三行
    $content = $content . "<p>Inquiry Task: " . $task_name . "</p>";
    //$content = $content . "<p>Inquiry Type: " . $order_type . "</p>";
    $content = $content . "<p>Inquiry Name: " . $order_name . "</p>";

    $content = $content . "<p>Creator: " . $creators . "</p>";
    $content = $content . "<p>Assignee: " . $assignees . "</p>";
    $content = $content . "<p>Collaborator: " . $collaborators . "</p>";
    $content = $content . "<p>Created at: " . $created_at . "</p>";
    $content = $content . "<p>Due Date: " . $due_date . "</p>";
    $content = $content . "<p>Description: " . $detail . "</p>";
    $content = $content . "<p> </p>";
    $content = $content . "<p>Please click this link to view the target webpage: </p>";
    $content = $content . "<p>https://feliix.myvnc.com/task_management_" . $task_type . "?sid=" . $stage_id . "</p>";

    $mail->MsgHTML($content);
    if($mail->Send()) {
        logMail($creators, $content);
        return true;
    } else {
        logMail($creators, $mail->ErrorInfo . $content);
        return false;
    }

}



function message_notify_dept($request_type, $project_name, $task_name, $stages, $create_id, $assignee, $collaborator, $due_date, $detail, $stage_id, $msg, $username, $created_at, $c_id, $dept)
{
    $tab = "";
    $department = "";
    $uri = "";

    switch ($dept) {
        case "LT":
            $department = "Lighting";
            $uri = "task_management_LT";
            break;
        case "OS":
            $department = "Office Systems";
            $uri = "task_management_OS";
            break;
        case "AD":
            $department = "Admin";
            $uri = "task_management_AD";
            break;
        case "DS":
            $department = "Design";
            $uri = "task_management_DS";
            break;
        case "SLS":
            $department = "Sales";
            $uri = "task_management_SLS";
            break;
        case "ENG":
            $department = "Engineering";
            $uri = "task_management_SVC";
            break;
        case "SVC":
            $department = "Engineering";
            $uri = "task_management_SVC";
            break;
        case "C":
            $department = "Client Stage";
            $uri = "project03_client_v2";
            break;
    }

    switch ($request_type) {
        case "create":
            $tab = '<p>A new message in Task "' . $task_name . '" of ' . $department . ' Department was created by "' . $username . '". Following are the details:</p>';
            $title = '[Message Notification] New message in Task "' . $task_name . '" of ' . $department . ' Department Task Management';
            break;
        case "edit":
            $tab = "<p>A task was revised and needs you to follow. Below is the details:</p>";
            break;
        case "del":
            $tab = '<p>A message in Task "' . $task_name . '" of ' . $department . ' Department was deleted by "' . $username . '". Following are the details:</p>';
            $title = '[Message Notification] Message was deleted in Task "' . $task_name . '" of ' . $department . ' Department Task Management';
            break;
        default:
            return;
            break;
    }

    if($dept == 'C')
    {
        switch ($request_type) {
            case "create":
                $tab = '<p>A new message in Task "' . $task_name . '" was created by "' . $username . '". Following are the details:</p>';
                $title = '[Message Notification] New message in Task "' . $task_name;
                break;
            case "edit":
                $tab = "<p>A task was revised and needs you to follow. Below is the details:</p>";
                break;
            case "del":
                $tab = '<p>A message in Task "' . $task_name . '" was deleted by "' . $username . '". Following are the details:</p>';
                $title = '[Message Notification] Message was deleted in Task "' . $task_name;
                break;
            default:
                return;
                break;
        }
    }

    $conf = new Conf();

    $mail = new PHPMailer();
    $mail->IsSMTP();
    $mail->Mailer = "smtp";
    $mail->CharSet = 'UTF-8';
    $mail->Encoding = 'base64';

    // $mail->SMTPDebug  = 0;
    // $mail->SMTPAuth   = true;
    // $mail->SMTPSecure = "ssl";
    // $mail->Port       = 465;
    // $mail->SMTPKeepAlive = true;
    // $mail->Host       = $conf::$mail_host;
    // $mail->Username   = $conf::$mail_username;
    // $mail->Password   = $conf::$mail_password;

    $mail = SetupMail($mail, $conf);

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
        $mail->AddAddress($list["email"], $list["username"]);
        $creators = $creators . $list["username"] . ", ";
    }

    $notifior = GetNotifiers($c_id);
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

    $mail->Subject = $title;
    $content =  "<p>Dear all,</p>";
    $content = $content . $tab;

    if($dept == 'C')
        $content = $content . "<p>Project Name: " . $project_name . "</p>";

    $content = $content . "<p>Task Name: " . $task_name . "</p>";
  
    if($request_type == "create")
    {
        $content = $content . "<p>Message Creator: " . $username . " at " . $created_at . "</p>";
        $content = $content . "<p>Content: " . $msg . "</p>";
    }

    if($request_type == "del")
    {
        $content = $content . "<p>Message Eraser: " . $username . " at " . $created_at . "</p>";
        $content = $content . "<p>Content: " . $msg . "</p>";
    }
    // $content = $content . "<p>Assignee: " . $assignees . "</p>";
    // $content = $content . "<p>Collaborator: " . $collaborators . "</p>";
    // $content = $content . "<p>Due Date: " . $due_date . "</p>";
    // $content = $content . "<p>Description: " . $detail . "</p>";
    $content = $content . "<p> </p>";
    $content = $content . "<p>Please click this link to view the target webpage: </p>";
    $content = $content . "<p>https://feliix.myvnc.com/" . $uri . "?sid=" . $stage_id . "</p>";

    $mail->MsgHTML($content);
    if($mail->Send()) {
        logMail($creators, $content);
        return true;
    } else {
        logMail($creators, $mail->ErrorInfo . $content);
        return false;
    }

}



function message_notify_r($request_type, $project_name, $task_name, $stages, $create_id, $assignee, $collaborator, $due_date, $detail, $stage_id, $msg, $username, $created_at, $c_id)
{
    $tab = "";

    switch ($request_type) {
        case "create":
            $tab = '<p>A new message in "' . $stages . '" stage of project "' . $project_name . '" was created by "' . $username . '". Following are the details:</p>';
            $title = '[Message Notification] New message in "' . $stages . '" stage of project "' . $project_name . '"';
            break;
        case "edit":
            $tab = "<p>A message was revised and needs you to follow. Below is the details:</p>";
            break;
        case "del":
            $tab = '<p>A message in "' . $stages . '" stage of project "' . $project_name . '" was deleted by "' . $username . '". Following are the details:</p>';
            $title = '[Message Notification] Message was deleted in "' . $stages . '" stage of project "' . $project_name . '"';
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

    // $mail->SMTPDebug  = 0;
    // $mail->SMTPAuth   = true;
    // $mail->SMTPSecure = "ssl";
    // $mail->Port       = 465;
    // $mail->SMTPKeepAlive = true;
    // $mail->Host       = $conf::$mail_host;
    // $mail->Username   = $conf::$mail_username;
    // $mail->Password   = $conf::$mail_password;

    $mail = SetupMail($mail, $conf);

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
        $mail->AddAddress($list["email"], $list["username"]);
        $creators = $creators . $list["username"] . ", ";
    }

    $notifior = GetNotifiers($c_id);
    foreach($notifior as &$list)
    {
        $mail->AddCC($list["email"], $list["username"]);
        $creators = $creators . $list["username"] . ", ";
    }

    // 在Order Stage中，當使用者留言或是回復留言時，系統發出的通知信中需要額外加入「職位為 Service Manager」和「職位為 Warehouse in charge」的人員
    if($stages == "Order")
    {
        $notifior = GetChargeNotifiersByTitle('Warehouse in charge');
        foreach($notifior as &$list)
        {
            $mail->AddAddress($list["email"], $list["username"]);
        }

        $catagory_id = GetProjectCategoryByStageId($stage_id);
        if($catagory_id == "2")
        {
            $notifior = GetChargeNotifiersByTitle('Engineering Manager');
            foreach($notifior as &$list)
            {
                $mail->AddAddress($list["email"], $list["username"]);
            }
        }
        
    }

    $creators = rtrim($creators, ", ");
    $assignees = rtrim($assignees, ", ");
    $collaborators = rtrim($collaborators, ", ");
    
    $mail->SetFrom("feliix.it@gmail.com", "Feliix.System");
    $mail->AddReplyTo("feliix.it@gmail.com", "Feliix.System");

    $mail->Subject = $title;
    $content =  "<p>Dear all,</p>";
    $content = $content . $tab;
    $content = $content . "<p>Project Name: " . $project_name . "</p>";
    $content = $content . "<p>Stage: " . $stages . "</p>";
    $content = $content . "<p>Message Title: " . $task_name . "</p>";
    if($request_type == "create")
    {
        $content = $content . "<p>Creator: " . $username . " at " . $created_at . "</p>";
        $content = $content . "<p>Content: " . $msg . "</p>";
    }

    if($request_type == "del")
    {
        $content = $content . "<p>Message Eraser: " . $username . " at " . $created_at . "</p>";
        $content = $content . "<p>Content: " . $msg . "</p>";
    }
    // $content = $content . "<p>Assignee: " . $assignees . "</p>";
    // $content = $content . "<p>Collaborator: " . $collaborators . "</p>";
    // $content = $content . "<p>Due Date: " . $due_date . "</p>";
    // $content = $content . "<p>Description: " . $detail . "</p>";
    $content = $content . "<p> </p>";
    $content = $content . "<p>Please click this link to view the target webpage: </p>";
    $content = $content . "<p>https://feliix.myvnc.com/project03_other?sid=" . $stage_id . "</p>";

    $mail->MsgHTML($content);
    if($mail->Send()) {
        logMail($creators, $content);
        return true;
    } else {
        logMail($creators, $mail->ErrorInfo . $content);
        return false;
    }

}


function message_notify($request_type, $project_name, $task_name, $stages, $create_id, $assignee, $collaborator, $due_date, $detail, $stage_id, $msg, $username, $created_at, $c_id)
{
    $tab = "";

    switch ($request_type) {
        case "create":
            $tab = '<p>A new message in "' . $stages . '" stage of project "' . $project_name . '" was created by "' . $username . '". Following are the details:</p>';
            $title = '[Message Notification] New message in "' . $stages . '" stage of project "' . $project_name . '"';
            break;
        case "edit":
            $tab = "<p>A task was revised and needs you to follow. Below is the details:</p>";
            break;
        case "del":
            $tab = '<p>A message in "' . $stages . '" stage of project "' . $project_name . '" was deleted by "' . $username . '". Following are the details:</p>';
            $title = '[Message Notification] Message was deleted in "' . $stages . '" stage of project "' . $project_name . '"';
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

    // $mail->SMTPDebug  = 0;
    // $mail->SMTPAuth   = true;
    // $mail->SMTPSecure = "ssl";
    // $mail->Port       = 465;
    // $mail->SMTPKeepAlive = true;
    // $mail->Host       = $conf::$mail_host;
    // $mail->Username   = $conf::$mail_username;
    // $mail->Password   = $conf::$mail_password;

    $mail = SetupMail($mail, $conf);

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
        $mail->AddAddress($list["email"], $list["username"]);
        $creators = $creators . $list["username"] . ", ";
    }

    $notifior = GetNotifiers($c_id);
    foreach($notifior as &$list)
    {
        $mail->AddCC($list["email"], $list["username"]);
        $creators = $creators . $list["username"] . ", ";
    }

    // 在Order Stage中，當使用者留言或是回復留言時，系統發出的通知信中需要額外加入「職位為 Service Manager」和「職位為 Warehouse in charge」的人員
    if($stages == "Order")
    {
        $notifior = GetChargeNotifiersByTitle('Warehouse in charge');
        foreach($notifior as &$list)
        {
            $mail->AddAddress($list["email"], $list["username"]);
        }

        $catagory_id = GetProjectCategoryByStageId($stage_id);
        if($catagory_id == "2")
        {
            $notifior = GetChargeNotifiersByTitle('Engineering Manager');
            foreach($notifior as &$list)
            {
                $mail->AddAddress($list["email"], $list["username"]);
            }
        }
        
    }

    $creators = rtrim($creators, ", ");
    $assignees = rtrim($assignees, ", ");
    $collaborators = rtrim($collaborators, ", ");
    
    $mail->SetFrom("feliix.it@gmail.com", "Feliix.System");
    $mail->AddReplyTo("feliix.it@gmail.com", "Feliix.System");

    $mail->Subject = $title;
    $content =  "<p>Dear all,</p>";
    $content = $content . $tab;
    $content = $content . "<p>Project Name: " . $project_name . "</p>";
    $content = $content . "<p>Stage: " . $stages . "</p>";
    $content = $content . "<p>Task Name: " . $task_name . "</p>";
    if($request_type == "create")
    {
        $content = $content . "<p>Creator: " . $username . " at " . $created_at . "</p>";
        $content = $content . "<p>Content: " . $msg . "</p>";
    }

    if($request_type == "del")
    {
        $content = $content . "<p>Message Eraser: " . $username . " at " . $created_at . "</p>";
        $content = $content . "<p>Content: " . $msg . "</p>";
    }
    // $content = $content . "<p>Assignee: " . $assignees . "</p>";
    // $content = $content . "<p>Collaborator: " . $collaborators . "</p>";
    // $content = $content . "<p>Due Date: " . $due_date . "</p>";
    // $content = $content . "<p>Description: " . $detail . "</p>";
    $content = $content . "<p> </p>";
    $content = $content . "<p>Please click this link to view the target webpage: </p>";
    $content = $content . "<p>https://feliix.myvnc.com/project03_other?sid=" . $stage_id . "</p>";

    $mail->MsgHTML($content);
    if($mail->Send()) {
        logMail($creators, $content);
        return true;
    } else {
        logMail($creators, $mail->ErrorInfo . $content);
        return false;
    }

}


function task_notify01_admin($old_status, $task_status, $revisor, $task_name, $create_id, $assignee, $collaborator, $due_date, $detail, $stage_id, $category, $project_name)
{

    $tab = '<p>Status of task "' . $task_name . '" changed from ' . $old_status . ' to ' . $task_status . '. Following are the details:</p>';

    $conf = new Conf();

    $mail = new PHPMailer();
    $mail->IsSMTP();
    $mail->Mailer = "smtp";
    $mail->CharSet = 'UTF-8';
    $mail->Encoding = 'base64';

    // $mail->SMTPDebug  = 0;
    // $mail->SMTPAuth   = true;
    // $mail->SMTPSecure = "ssl";
    // $mail->Port       = 465;
    // $mail->SMTPKeepAlive = true;
    // $mail->Host       = $conf::$mail_host;
    // $mail->Username   = $conf::$mail_username;
    // $mail->Password   = $conf::$mail_password;

    $mail = SetupMail($mail, $conf);

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

    $title = "[" . $assignees . "][Task Notification] Status of " . $task_name . " changed from " . $old_status . ' to ' . $task_status;

    $mail->Subject = $title;
    $content =  "<p>Dear all,</p>";
    $content = $content . $tab;

    if($category == 'C' || $category == '')
        $content = $content . "<p>Project Name: " . $project_name . "</p>";

    $content = $content . "<p>Task: " . $task_name . "</p>";
    $content = $content . "<p>Task Status: " . $old_status . ' => ' . $task_status . "</p>";
    $content = $content . "<p>Creator: " . $creators . "</p>";
    $content = $content . "<p>Reviser: " . $revisor . "</p>";
    $content = $content . "<p>Assignee: " . $assignees . "</p>";
    $content = $content . "<p>Collaborator: " . $collaborators . "</p>";
    $content = $content . "<p>Due Date: " . $due_date . "</p>";
    $content = $content . "<p>Description: " . $detail . "</p>";
    $content = $content . "<p> </p>";
    $content = $content . "<p>Please click this link to view the target webpage: </p>";

    if($category == 'AD')
        $content = $content . "<p>https://feliix.myvnc.com/task_management_AD?sid=" . $stage_id . "</p>";
    else if($category == 'DS')
        $content = $content . "<p>https://feliix.myvnc.com/task_management_DS?sid=" . $stage_id . "</p>";
    else if($category == 'LT_T')
        $content = $content . "<p>https://feliix.myvnc.com/task_management_LT?sid=" . $stage_id . "</p>";
    else if($category == 'OS_T')
        $content = $content . "<p>https://feliix.myvnc.com/task_management_OS?sid=" . $stage_id . "</p>";
    else if($category == 'SL')
        $content = $content . "<p>https://feliix.myvnc.com/task_management_SLS?sid=" . $stage_id . "</p>";
    else if($category == 'SV')
        $content = $content . "<p>https://feliix.myvnc.com/task_management_SVC?sid=" . $stage_id . "</p>";
    else if($category == 'C')
        $content = $content . "<p>https://feliix.myvnc.com/project03_client_v2?sid=" . $stage_id . "</p>";
    else
        $content = $content . "<p>https://feliix.myvnc.com/project03_other?sid=" . $stage_id . "</p>";

    $mail->MsgHTML($content);
    if($mail->Send()) {
        logMail($creators, $content);
        return true;
    } else {
        logMail($creators, $mail->ErrorInfo . $content);
        return false;
    }

}


function task_notify01_admin_sl($old_status, $task_status, $revisor, $task_name, $create_id, $assignee, $collaborator, $due_date, $detail, $stage_id)
{

    $tab = '<p>Status of task "' . $task_name . '" changed from ' . $old_status . ' to ' . $task_status . '. Following are the details:</p>';

    $conf = new Conf();

    $mail = new PHPMailer();
    $mail->IsSMTP();
    $mail->Mailer = "smtp";
    $mail->CharSet = 'UTF-8';
    $mail->Encoding = 'base64';

    // $mail->SMTPDebug  = 0;
    // $mail->SMTPAuth   = true;
    // $mail->SMTPSecure = "ssl";
    // $mail->Port       = 465;
    // $mail->SMTPKeepAlive = true;
    // $mail->Host       = $conf::$mail_host;
    // $mail->Username   = $conf::$mail_username;
    // $mail->Password   = $conf::$mail_password;

    $mail = SetupMail($mail, $conf);

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

    $title = "[" . $assignees . "][Task Notification] Status of " . $task_name . " changed from " . $old_status . ' to ' . $task_status;

    $mail->Subject = $title;
    $content =  "<p>Dear all,</p>";
    $content = $content . $tab;
    $content = $content . "<p>Task: " . $task_name . "</p>";
    $content = $content . "<p>Task Status: " . $old_status . ' => ' . $task_status . "</p>";
    $content = $content . "<p>Creator: " . $creators . "</p>";
    $content = $content . "<p>Reviser: " . $revisor . "</p>";
    $content = $content . "<p>Assignee: " . $assignees . "</p>";
    $content = $content . "<p>Collaborator: " . $collaborators . "</p>";
    $content = $content . "<p>Due Date: " . $due_date . "</p>";
    $content = $content . "<p>Description: " . $detail . "</p>";
    $content = $content . "<p> </p>";
    $content = $content . "<p>Please click this link to view the target webpage: </p>";
    $content = $content . "<p>https://feliix.myvnc.com/task_management_SLS?sid=" . $stage_id . "</p>";

    $mail->MsgHTML($content);
    if($mail->Send()) {
        logMail($creators, $content);
        return true;
    } else {
        logMail($creators, $mail->ErrorInfo . $content);
        return false;
    }
}

function task_notify01_admin_sv($old_status, $task_status, $revisor, $task_name, $create_id, $assignee, $collaborator, $due_date, $detail, $stage_id)
{

    $tab = '<p>Status of task "' . $task_name . '" changed from ' . $old_status . ' to ' . $task_status . '. Following are the details:</p>';

    $conf = new Conf();

    $mail = new PHPMailer();
    $mail->IsSMTP();
    $mail->Mailer = "smtp";
    $mail->CharSet = 'UTF-8';
    $mail->Encoding = 'base64';

    // $mail->SMTPDebug  = 0;
    // $mail->SMTPAuth   = true;
    // $mail->SMTPSecure = "ssl";
    // $mail->Port       = 465;
    // $mail->SMTPKeepAlive = true;
    // $mail->Host       = $conf::$mail_host;
    // $mail->Username   = $conf::$mail_username;
    // $mail->Password   = $conf::$mail_password;

    $mail = SetupMail($mail, $conf);

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

    $title = "[" . $assignees . "][Task Notification] Status of " . $task_name . " changed from " . $old_status . ' to ' . $task_status;

    $mail->Subject = $title;
    $content =  "<p>Dear all,</p>";
    $content = $content . $tab;
    $content = $content . "<p>Task: " . $task_name . "</p>";
    $content = $content . "<p>Task Status: " . $old_status . ' => ' . $task_status . "</p>";
    $content = $content . "<p>Creator: " . $creators . "</p>";
    $content = $content . "<p>Reviser: " . $revisor . "</p>";
    $content = $content . "<p>Assignee: " . $assignees . "</p>";
    $content = $content . "<p>Collaborator: " . $collaborators . "</p>";
    $content = $content . "<p>Due Date: " . $due_date . "</p>";
    $content = $content . "<p>Description: " . $detail . "</p>";
    $content = $content . "<p> </p>";
    $content = $content . "<p>Please click this link to view the target webpage: </p>";
    $content = $content . "<p>https://feliix.myvnc.com/task_management_SVC?sid=" . $stage_id . "</p>";

    $mail->MsgHTML($content);
    if($mail->Send()) {
        logMail($creators, $content);
        return true;
    } else {
        logMail($creators, $mail->ErrorInfo . $content);
        return false;
    }

}

function task_notify01_admin_d($old_status, $task_status, $revisor, $task_name, $create_id, $assignee, $collaborator, $due_date, $detail, $stage_id)
{

    $tab = '<p>Status of task "' . $task_name . '" changed from ' . $old_status . ' to ' . $task_status . '. Following are the details:</p>';

    $conf = new Conf();

    $mail = new PHPMailer();
    $mail->IsSMTP();
    $mail->Mailer = "smtp";
    $mail->CharSet = 'UTF-8';
    $mail->Encoding = 'base64';

    // $mail->SMTPDebug  = 0;
    // $mail->SMTPAuth   = true;
    // $mail->SMTPSecure = "ssl";
    // $mail->Port       = 465;
    // $mail->SMTPKeepAlive = true;
    // $mail->Host       = $conf::$mail_host;
    // $mail->Username   = $conf::$mail_username;
    // $mail->Password   = $conf::$mail_password;

    $mail = SetupMail($mail, $conf);

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

    $title = "[" . $assignees . "][Task Notification] Status of " . $task_name . " changed from " . $old_status . ' to ' . $task_status;

    $mail->Subject = $title;
    $content =  "<p>Dear all,</p>";
    $content = $content . $tab;
    $content = $content . "<p>Task: " . $task_name . "</p>";
    $content = $content . "<p>Task Status: " . $old_status . ' => ' . $task_status . "</p>";
    $content = $content . "<p>Creator: " . $creators . "</p>";
    $content = $content . "<p>Reviser: " . $revisor . "</p>";
    $content = $content . "<p>Assignee: " . $assignees . "</p>";
    $content = $content . "<p>Collaborator: " . $collaborators . "</p>";
    $content = $content . "<p>Due Date: " . $due_date . "</p>";
    $content = $content . "<p>Description: " . $detail . "</p>";
    $content = $content . "<p> </p>";
    $content = $content . "<p>Please click this link to view the target webpage: </p>";
    $content = $content . "<p>https://feliix.myvnc.com/task_management_DS?sid=" . $stage_id . "</p>";

    $mail->MsgHTML($content);
    if($mail->Send()) {
        logMail($creators, $content);
        return true;
    } else {
        logMail($creators, $mail->ErrorInfo . $content);
        return false;
    }

}

function task_notify01_admin_o($old_status, $task_status, $revisor, $task_name, $create_id, $assignee, $collaborator, $due_date, $detail, $stage_id)
{

    $tab = '<p>Status of task "' . $task_name . '" changed from ' . $old_status . ' to ' . $task_status . '. Following are the details:</p>';

    $conf = new Conf();

    $mail = new PHPMailer();
    $mail->IsSMTP();
    $mail->Mailer = "smtp";
    $mail->CharSet = 'UTF-8';
    $mail->Encoding = 'base64';

    // $mail->SMTPDebug  = 0;
    // $mail->SMTPAuth   = true;
    // $mail->SMTPSecure = "ssl";
    // $mail->Port       = 465;
    // $mail->SMTPKeepAlive = true;
    // $mail->Host       = $conf::$mail_host;
    // $mail->Username   = $conf::$mail_username;
    // $mail->Password   = $conf::$mail_password;

    $mail = SetupMail($mail, $conf);

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

    $title = "[" . $assignees . "][Task Notification] Status of " . $task_name . " changed from " . $old_status . ' to ' . $task_status;

    $mail->Subject = $title;
    $content =  "<p>Dear all,</p>";
    $content = $content . $tab;
    $content = $content . "<p>Task: " . $task_name . "</p>";
    $content = $content . "<p>Task Status: " . $old_status . ' => ' . $task_status . "</p>";
    $content = $content . "<p>Creator: " . $creators . "</p>";
    $content = $content . "<p>Reviser: " . $revisor . "</p>";
    $content = $content . "<p>Assignee: " . $assignees . "</p>";
    $content = $content . "<p>Collaborator: " . $collaborators . "</p>";
    $content = $content . "<p>Due Date: " . $due_date . "</p>";
    $content = $content . "<p>Description: " . $detail . "</p>";
    $content = $content . "<p> </p>";
    $content = $content . "<p>Please click this link to view the target webpage: </p>";
    $content = $content . "<p>https://feliix.myvnc.com/task_management_OS?sid=" . $stage_id . "</p>";

    $mail->MsgHTML($content);
    if($mail->Send()) {
        logMail($creators, $content);
        return true;
    } else {
        logMail($creators, $mail->ErrorInfo . $content);
        return false;
    }

}

function task_notify01_admin_l($old_status, $task_status, $revisor, $task_name, $create_id, $assignee, $collaborator, $due_date, $detail, $stage_id)
{

    $tab = '<p>Status of task "' . $task_name . '" changed from ' . $old_status . ' to ' . $task_status . '. Following are the details:</p>';

    $conf = new Conf();

    $mail = new PHPMailer();
    $mail->IsSMTP();
    $mail->Mailer = "smtp";
    $mail->CharSet = 'UTF-8';
    $mail->Encoding = 'base64';

    // $mail->SMTPDebug  = 0;
    // $mail->SMTPAuth   = true;
    // $mail->SMTPSecure = "ssl";
    // $mail->Port       = 465;
    // $mail->SMTPKeepAlive = true;
    // $mail->Host       = $conf::$mail_host;
    // $mail->Username   = $conf::$mail_username;
    // $mail->Password   = $conf::$mail_password;

    $mail = SetupMail($mail, $conf);

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

    $title = "[" . $assignees . "][Task Notification] Status of " . $task_name . " changed from " . $old_status . ' to ' . $task_status;

    $mail->Subject = $title;
    $content =  "<p>Dear all,</p>";
    $content = $content . $tab;
    $content = $content . "<p>Task: " . $task_name . "</p>";
    $content = $content . "<p>Task Status: " . $old_status . ' => ' . $task_status . "</p>";
    $content = $content . "<p>Creator: " . $creators . "</p>";
    $content = $content . "<p>Reviser: " . $revisor . "</p>";
    $content = $content . "<p>Assignee: " . $assignees . "</p>";
    $content = $content . "<p>Collaborator: " . $collaborators . "</p>";
    $content = $content . "<p>Due Date: " . $due_date . "</p>";
    $content = $content . "<p>Description: " . $detail . "</p>";
    $content = $content . "<p> </p>";
    $content = $content . "<p>Please click this link to view the target webpage: </p>";
    $content = $content . "<p>https://feliix.myvnc.com/task_management_LT?sid=" . $stage_id . "</p>";

    $mail->MsgHTML($content);
    if($mail->Send()) {
        logMail($creators, $content);
        return true;
    } else {
        logMail($creators, $mail->ErrorInfo . $content);
        return false;
    }

}

function task_notify01($old_status, $task_status, $project_name, $task_name, $stage, $stages_status, $create_id, $assignee, $collaborator, $due_date, $detail, $stage_id)
{

    //如果是普通任務，執行下一行
    $tab = '<p>Status of task "' . $task_name . '" changed from ' . $old_status . ' to ' . $task_status . '. Following are the details:</p>';
    //如果是訂單任務，執行下一行
    //$tab = '<p>Status of order task "' . $task_name . '" changed from ' . $old_status . ' to ' . $task_status . '. Following are the details:</p>';

    $conf = new Conf();

    $mail = new PHPMailer();
    $mail->IsSMTP();
    $mail->Mailer = "smtp";
    $mail->CharSet = 'UTF-8';
    $mail->Encoding = 'base64';

    // $mail->SMTPDebug  = 0;
    // $mail->SMTPAuth   = true;
    // $mail->SMTPSecure = "ssl";
    // $mail->Port       = 465;
    // $mail->SMTPKeepAlive = true;
    // $mail->Host       = $conf::$mail_host;
    // $mail->Username   = $conf::$mail_username;
    // $mail->Password   = $conf::$mail_password;

    $mail = SetupMail($mail, $conf);

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


// 在Order Stage中，當使用者XXXX Task，系統發出的通知信中需要額外加入「職位為 Service Manager」和「職位為 Warehouse in charge」的人員
if($stage == "Order")
{
    $notifior = GetChargeNotifiersByTitle('Warehouse in charge');
    foreach($notifior as &$list)
    {
        $mail->AddAddress($list["email"], $list["username"]);
    }

    $catagory_id = GetProjectCategoryByStageId($stage_id);
    if($catagory_id == "2")
    {
        $notifior = GetChargeNotifiersByTitle('Engineering Manager');
        foreach($notifior as &$list)
        {
            $mail->AddAddress($list["email"], $list["username"]);
        }
    }

}

    $creators = rtrim($creators, ", ");
    $assignees = rtrim($assignees, ", ");
    $collaborators = rtrim($collaborators, ", ");

    $mail->SetFrom("feliix.it@gmail.com", "Feliix.System");
    $mail->AddReplyTo("feliix.it@gmail.com", "Feliix.System");

    //如果是普通任務，執行下一行
    $title = "[" . $assignees . "][Task Notification] " . $project_name . " - " . $task_name . " ";
    //如果是訂單任務，執行下一行
    //$title = "[" . $assignees . "][Order Task Notification] " . $project_name . " - " . $task_name . " ";

    $mail->Subject = $title;
    $content =  "<p>Dear all,</p>";
    $content = $content . $tab;
    $content = $content . "<p>Project Name: " . $project_name . "</p>";
    $content = $content . "<p>Stage: " . $stage . "</p>";

    //如果是普通任務，執行下一行
    $content = $content . "<p>Task: " . $task_name . "</p>";
    //如果是訂單任務，執行下一行
    //$content = $content . "<p>Order Task: " . $task_name . "</p>";


    $content = $content . "<p>Task Status: " . $old_status . ' => ' . $task_status . "</p>";

    //如果是訂單任務，執行下兩行
    //$content = $content . "<p>Order Type: " . $order_type . "</p>";
    //$content = $content . "<p>Order Name: " . $order_name . "</p>";

    $content = $content . "<p>Creator: " . $creators . "</p>";
    $content = $content . "<p>Assignee: " . $assignees . "</p>";
    $content = $content . "<p>Collaborator: " . $collaborators . "</p>";
    $content = $content . "<p>Due Date: " . $due_date . "</p>";
    $content = $content . "<p>Description: " . $detail . "</p>";
    $content = $content . "<p> </p>";
    $content = $content . "<p>Please click this link to view the target webpage: </p>";
    $content = $content . "<p>https://feliix.myvnc.com/project03_other?sid=" . $stage_id . "</p>";

    $mail->MsgHTML($content);
    if($mail->Send()) {
        logMail($creators, $content);
        return true;
    } else {
        logMail($creators, $mail->ErrorInfo . $content);
        return false;
    }

}


function task_notify01_r($old_status, $task_status, $project_name, $task_name, $stage, $stages_status, $create_id, $assignee, $collaborator, $due_date, $detail, $stage_id)
{

    //如果是普通任務，執行下一行
    $tab = '<p>Status of task "' . $task_name . '" changed from ' . $old_status . ' to ' . $task_status . '. Following are the details:</p>';
    //如果是訂單任務，執行下一行
    //$tab = '<p>Status of order task "' . $task_name . '" changed from ' . $old_status . ' to ' . $task_status . '. Following are the details:</p>';

    $conf = new Conf();

    $mail = new PHPMailer();
    $mail->IsSMTP();
    $mail->Mailer = "smtp";
    $mail->CharSet = 'UTF-8';
    $mail->Encoding = 'base64';

    // $mail->SMTPDebug  = 0;
    // $mail->SMTPAuth   = true;
    // $mail->SMTPSecure = "ssl";
    // $mail->Port       = 465;
    // $mail->SMTPKeepAlive = true;
    // $mail->Host       = $conf::$mail_host;
    // $mail->Username   = $conf::$mail_username;
    // $mail->Password   = $conf::$mail_password;

    $mail = SetupMail($mail, $conf);

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

    
// 在Order Stage中，當使用者XXXX Task，系統發出的通知信中需要額外加入「職位為 Service Manager」和「職位為 Warehouse in charge」的人員
if($stage == "Order")
{
    $notifior = GetChargeNotifiersByTitle('Warehouse in charge');
    foreach($notifior as &$list)
    {
        $mail->AddAddress($list["email"], $list["username"]);
    }

    $catagory_id = GetProjectCategoryByStageId($stage_id);
    if($catagory_id == "2")
    {
        $notifior = GetChargeNotifiersByTitle('Engineering Manager');
        foreach($notifior as &$list)
        {
            $mail->AddAddress($list["email"], $list["username"]);
        }
    }
    
}

    $creators = rtrim($creators, ", ");
    $assignees = rtrim($assignees, ", ");
    $collaborators = rtrim($collaborators, ", ");
    
    $mail->SetFrom("feliix.it@gmail.com", "Feliix.System");
    $mail->AddReplyTo("feliix.it@gmail.com", "Feliix.System");

    //如果是普通任務，執行下一行
    $title = "[Message Notification] " . $project_name . " - " . $task_name . " ";
    //如果是訂單任務，執行下一行
    //$title = "[Order Task Notification] " . $project_name . " - " . $task_name . " ";
    
    $mail->Subject = $title;
    $content =  "<p>Dear all,</p>";
    $content = $content . $tab;
    $content = $content . "<p>Project Name: " . $project_name . "</p>";
    $content = $content . "<p>Stage: " . $stage . "</p>";

    //如果是普通任務，執行下一行
    $content = $content . "<p>Message Title: " . $task_name . "</p>";
 

    //如果是訂單任務，執行下兩行
    //$content = $content . "<p>Order Type: " . $order_type . "</p>";
    //$content = $content . "<p>Order Name: " . $order_name . "</p>";

    $content = $content . "<p>Creator: " . $creators . "</p>";
    $content = $content . "<p>Assignee: " . $assignees . "</p>";
    //$content = $content . "<p>Collaborator: " . $collaborators . "</p>";

    $content = $content . "<p>Description: " . $detail . "</p>";
    $content = $content . "<p> </p>";
    $content = $content . "<p>Please click this link to view the target webpage: </p>";
    $content = $content . "<p>https://feliix.myvnc.com/project03_other?sid=" . $stage_id . "</p>";

    $mail->MsgHTML($content);
    if($mail->Send()) {
        logMail($creators, $content);
        return true;
    } else {
        logMail($creators, $mail->ErrorInfo . $content);
        return false;
    }

}



function task_notify01_order($old_status, $task_status, $project_name, $task_name, $stage, $stages_status, $create_id, $assignee, $collaborator, $due_date, $detail, $stage_id, $order_type, $order_name)
{

    //如果是普通任務，執行下一行
    //$tab = '<p>Status of task "' . $task_name . '" changed from ' . $old_status . ' to ' . $task_status . '. Following are the details:</p>';
    //如果是訂單任務，執行下一行
    $tab = '<p>Status of order task "' . $task_name . '" changed from ' . $old_status . ' to ' . $task_status . '. Following are the details:</p>';

    $conf = new Conf();

    $mail = new PHPMailer();
    $mail->IsSMTP();
    $mail->Mailer = "smtp";
    $mail->CharSet = 'UTF-8';
    $mail->Encoding = 'base64';

    // $mail->SMTPDebug  = 0;
    // $mail->SMTPAuth   = true;
    // $mail->SMTPSecure = "ssl";
    // $mail->Port       = 465;
    // $mail->SMTPKeepAlive = true;
    // $mail->Host       = $conf::$mail_host;
    // $mail->Username   = $conf::$mail_username;
    // $mail->Password   = $conf::$mail_password;

    $mail = SetupMail($mail, $conf);

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


// 在Order Stage中，當使用者XXXX Task，系統發出的通知信中需要額外加入「職位為 Service Manager」和「職位為 Warehouse in charge」的人員
if($stage == "Order")
{
    $notifior = GetChargeNotifiersByTitle('Warehouse in charge');
    foreach($notifior as &$list)
    {
        $mail->AddAddress($list["email"], $list["username"]);
    }

    $catagory_id = GetProjectCategoryByStageId($stage_id);
    if($catagory_id == "2")
    {
        $notifior = GetChargeNotifiersByTitle('Engineering Manager');
        foreach($notifior as &$list)
        {
            $mail->AddAddress($list["email"], $list["username"]);
        }
    }

}

    $creators = rtrim($creators, ", ");
    $assignees = rtrim($assignees, ", ");
    $collaborators = rtrim($collaborators, ", ");

    $mail->SetFrom("feliix.it@gmail.com", "Feliix.System");
    $mail->AddReplyTo("feliix.it@gmail.com", "Feliix.System");

    //如果是普通任務，執行下一行
    //$title = "[" . $assignees . "][Task Notification] " . $project_name . " - " . $task_name . " ";
    //如果是訂單任務，執行下一行
    $title = "[" . $assignees . "][Order Task Notification] " . $project_name . " - " . $task_name . " ";

    $mail->Subject = $title;
    $content =  "<p>Dear all,</p>";
    $content = $content . $tab;
    $content = $content . "<p>Project Name: " . $project_name . "</p>";
    $content = $content . "<p>Stage: " . $stage . "</p>";

    //如果是普通任務，執行下一行
    //$content = $content . "<p>Task: " . $task_name . "</p>";
    //如果是訂單任務，執行下一行
    $content = $content . "<p>Order Task: " . $task_name . "</p>";


    $content = $content . "<p>Task Status: " . $old_status . ' => ' . $task_status . "</p>";

    //如果是訂單任務，執行下兩行
    $content = $content . "<p>Order Type: " . $order_type . "</p>";
    $content = $content . "<p>Order Name: " . $order_name . "</p>";

    $content = $content . "<p>Creator: " . $creators . "</p>";
    $content = $content . "<p>Assignee: " . $assignees . "</p>";
    $content = $content . "<p>Collaborator: " . $collaborators . "</p>";
    $content = $content . "<p>Due Date: " . $due_date . "</p>";
    $content = $content . "<p>Description: " . $detail . "</p>";
    $content = $content . "<p> </p>";
    $content = $content . "<p>Please click this link to view the target webpage: </p>";
    $content = $content . "<p>https://feliix.myvnc.com/project03_other?sid=" . $stage_id . "</p>";

    $mail->MsgHTML($content);
    if($mail->Send()) {
        logMail($creators, $content);
        return true;
    } else {
        logMail($creators, $mail->ErrorInfo . $content);
        return false;
    }

}

function task_notify01_inquiry($old_status, $task_status, $project_name, $task_name, $stage, $stages_status, $create_id, $assignee, $collaborator, $due_date, $detail, $stage_id, $order_type, $order_name)
{

    //如果是普通任務，執行下一行
    //$tab = '<p>Status of task "' . $task_name . '" changed from ' . $old_status . ' to ' . $task_status . '. Following are the details:</p>';
    //如果是訂單任務，執行下一行
    $tab = '<p>Status of inquiry task "' . $task_name . '" changed from ' . $old_status . ' to ' . $task_status . '. Following are the details:</p>';

    $conf = new Conf();

    $mail = new PHPMailer();
    $mail->IsSMTP();
    $mail->Mailer = "smtp";
    $mail->CharSet = 'UTF-8';
    $mail->Encoding = 'base64';

    // $mail->SMTPDebug  = 0;
    // $mail->SMTPAuth   = true;
    // $mail->SMTPSecure = "ssl";
    // $mail->Port       = 465;
    // $mail->SMTPKeepAlive = true;
    // $mail->Host       = $conf::$mail_host;
    // $mail->Username   = $conf::$mail_username;
    // $mail->Password   = $conf::$mail_password;

    $mail = SetupMail($mail, $conf);

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


// 在Order Stage中，當使用者XXXX Task，系統發出的通知信中需要額外加入「職位為 Service Manager」和「職位為 Warehouse in charge」的人員
if($stage == "Order")
{
    $notifior = GetChargeNotifiersByTitle('Warehouse in charge');
    foreach($notifior as &$list)
    {
        $mail->AddAddress($list["email"], $list["username"]);
    }

    $catagory_id = GetProjectCategoryByStageId($stage_id);
    if($catagory_id == "2")
    {
        $notifior = GetChargeNotifiersByTitle('Engineering Manager');
        foreach($notifior as &$list)
        {
            $mail->AddAddress($list["email"], $list["username"]);
        }
    }

}

    $creators = rtrim($creators, ", ");
    $assignees = rtrim($assignees, ", ");
    $collaborators = rtrim($collaborators, ", ");

    $mail->SetFrom("feliix.it@gmail.com", "Feliix.System");
    $mail->AddReplyTo("feliix.it@gmail.com", "Feliix.System");

    //如果是普通任務，執行下一行
    //$title = "[" . $assignees . "][Task Notification] " . $project_name . " - " . $task_name . " ";
    //如果是訂單任務，執行下一行
    $title = "[" . $assignees . "][Inquiry Task Notification] " . $project_name . " - " . $task_name . " ";

    $mail->Subject = $title;
    $content =  "<p>Dear all,</p>";
    $content = $content . $tab;
    $content = $content . "<p>Project Name: " . $project_name . "</p>";
    $content = $content . "<p>Stage: " . $stage . "</p>";

    //如果是普通任務，執行下一行
    //$content = $content . "<p>Task: " . $task_name . "</p>";
    //如果是訂單任務，執行下一行
    $content = $content . "<p>Inquiry Task: " . $task_name . "</p>";


    $content = $content . "<p>Task Status: " . $old_status . ' => ' . $task_status . "</p>";

    //如果是訂單任務，執行下兩行
    //$content = $content . "<p>Order Type: " . $order_type . "</p>";
    $content = $content . "<p>Inquiry Name: " . $order_name . "</p>";

    $content = $content . "<p>Creator: " . $creators . "</p>";
    $content = $content . "<p>Assignee: " . $assignees . "</p>";
    $content = $content . "<p>Collaborator: " . $collaborators . "</p>";
    $content = $content . "<p>Due Date: " . $due_date . "</p>";
    $content = $content . "<p>Description: " . $detail . "</p>";
    $content = $content . "<p> </p>";
    $content = $content . "<p>Please click this link to view the target webpage: </p>";
    $content = $content . "<p>https://feliix.myvnc.com/project03_other?sid=" . $stage_id . "</p>";

    $mail->MsgHTML($content);
    if($mail->Send()) {
        logMail($creators, $content);
        return true;
    } else {
        logMail($creators, $mail->ErrorInfo . $content);
        return false;
    }

}


function task_notify01_type_order($old_status, $task_status, $project_name, $task_name, $stage, $stages_status, $create_id, $assignee, $collaborator, $due_date, $detail, $stage_id, $order_type, $order_name, $task_type)
{

    //如果是普通任務，執行下一行
    //$tab = '<p>Status of task "' . $task_name . '" changed from ' . $old_status . ' to ' . $task_status . '. Following are the details:</p>';
    //如果是訂單任務，執行下一行
    $tab = '<p>Status of order task "' . $task_name . '" changed from ' . $old_status . ' to ' . $task_status . '. Following are the details:</p>';

    $conf = new Conf();

    $mail = new PHPMailer();
    $mail->IsSMTP();
    $mail->Mailer = "smtp";
    $mail->CharSet = 'UTF-8';
    $mail->Encoding = 'base64';

    // $mail->SMTPDebug  = 0;
    // $mail->SMTPAuth   = true;
    // $mail->SMTPSecure = "ssl";
    // $mail->Port       = 465;
    // $mail->SMTPKeepAlive = true;
    // $mail->Host       = $conf::$mail_host;
    // $mail->Username   = $conf::$mail_username;
    // $mail->Password   = $conf::$mail_password;

    $mail = SetupMail($mail, $conf);

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


// 在Order Stage中，當使用者XXXX Task，系統發出的通知信中需要額外加入「職位為 Service Manager」和「職位為 Warehouse in charge」的人員
if($stage == "Order")
{
    $notifior = GetChargeNotifiersByTitle('Warehouse in charge');
    foreach($notifior as &$list)
    {
        $mail->AddAddress($list["email"], $list["username"]);
    }

    $catagory_id = GetProjectCategoryByStageId($stage_id);
    if($catagory_id == "2")
    {
        $notifior = GetChargeNotifiersByTitle('Engineering Manager');
        foreach($notifior as &$list)
        {
            $mail->AddAddress($list["email"], $list["username"]);
        }
    }

}

$task_department = "";
if($task_type == 'LT')
    $task_department = "Lighting";
if($task_type == 'OS')
    $task_department = "Office Systems";
if($task_type == 'SLS')
    $task_department = "Sales";
if($task_type == 'SVC')
    $task_department = "Engineering Department";

    $creators = rtrim($creators, ", ");
    $assignees = rtrim($assignees, ", ");
    $collaborators = rtrim($collaborators, ", ");

    $mail->SetFrom("feliix.it@gmail.com", "Feliix.System");
    $mail->AddReplyTo("feliix.it@gmail.com", "Feliix.System");

    //如果是普通任務，執行下一行
    //$title = "[" . $assignees . "][Task Notification] " . $project_name . " - " . $task_name . " ";
    //如果是訂單任務，執行下一行
    $title = "[" . $assignees . "][Order Task Notification] " . $project_name . " - " . $task_name . " ";

    $mail->Subject = $title;
    $content =  "<p>Dear all,</p>";
    $content = $content . $tab;
    $content = $content . "<p>Task Management of " . $task_department . " Department</p>";

    //如果是普通任務，執行下一行
    //$content = $content . "<p>Task: " . $task_name . "</p>";
    //如果是訂單任務，執行下一行
    $content = $content . "<p>Order Task: " . $task_name . "</p>";


    $content = $content . "<p>Task Status: " . $old_status . ' => ' . $task_status . "</p>";

    //如果是訂單任務，執行下兩行
    $content = $content . "<p>Order Type: " . $order_type . "</p>";
    $content = $content . "<p>Order Name: " . $order_name . "</p>";

    $content = $content . "<p>Creator: " . $creators . "</p>";
    $content = $content . "<p>Assignee: " . $assignees . "</p>";
    $content = $content . "<p>Collaborator: " . $collaborators . "</p>";
    $content = $content . "<p>Due Date: " . $due_date . "</p>";
    $content = $content . "<p>Description: " . $detail . "</p>";
    $content = $content . "<p> </p>";
    $content = $content . "<p>Please click this link to view the target webpage: </p>";
    $content = $content . "<p>https://feliix.myvnc.com/task_management_" . $task_type . "?sid=" . $stage_id . "</p>";

    $mail->MsgHTML($content);
    if($mail->Send()) {
        logMail($creators, $content);
        return true;
    } else {
        logMail($creators, $mail->ErrorInfo . $content);
        return false;
    }

}


function task_notify01_type_inquiry($old_status, $task_status, $project_name, $task_name, $stage, $stages_status, $create_id, $assignee, $collaborator, $due_date, $detail, $stage_id, $order_type, $order_name, $task_type)
{

    //如果是普通任務，執行下一行
    //$tab = '<p>Status of task "' . $task_name . '" changed from ' . $old_status . ' to ' . $task_status . '. Following are the details:</p>';
    //如果是訂單任務，執行下一行
    $tab = '<p>Status of inquiry task "' . $task_name . '" changed from ' . $old_status . ' to ' . $task_status . '. Following are the details:</p>';

    $conf = new Conf();

    $mail = new PHPMailer();
    $mail->IsSMTP();
    $mail->Mailer = "smtp";
    $mail->CharSet = 'UTF-8';
    $mail->Encoding = 'base64';

    // $mail->SMTPDebug  = 0;
    // $mail->SMTPAuth   = true;
    // $mail->SMTPSecure = "ssl";
    // $mail->Port       = 465;
    // $mail->SMTPKeepAlive = true;
    // $mail->Host       = $conf::$mail_host;
    // $mail->Username   = $conf::$mail_username;
    // $mail->Password   = $conf::$mail_password;

    $mail = SetupMail($mail, $conf);

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


// 在Order Stage中，當使用者XXXX Task，系統發出的通知信中需要額外加入「職位為 Service Manager」和「職位為 Warehouse in charge」的人員
if($stage == "Order")
{
    $notifior = GetChargeNotifiersByTitle('Warehouse in charge');
    foreach($notifior as &$list)
    {
        $mail->AddAddress($list["email"], $list["username"]);
    }

    $catagory_id = GetProjectCategoryByStageId($stage_id);
    if($catagory_id == "2")
    {
        $notifior = GetChargeNotifiersByTitle('Engineering Manager');
        foreach($notifior as &$list)
        {
            $mail->AddAddress($list["email"], $list["username"]);
        }
    }

}

$task_department = "";
if($task_type == 'LT')
    $task_department = "Lighting";
if($task_type == 'OS')
    $task_department = "Office Systems";
if($task_type == 'SLS')
    $task_department = "Sales";
if($task_type == 'SVC')
    $task_department = "Engineering Department";

    $creators = rtrim($creators, ", ");
    $assignees = rtrim($assignees, ", ");
    $collaborators = rtrim($collaborators, ", ");

    $mail->SetFrom("feliix.it@gmail.com", "Feliix.System");
    $mail->AddReplyTo("feliix.it@gmail.com", "Feliix.System");

    //如果是普通任務，執行下一行
    //$title = "[" . $assignees . "][Task Notification] " . $project_name . " - " . $task_name . " ";
    //如果是訂單任務，執行下一行
    $title = "[" . $assignees . "][Inquiry Task Notification] " . $project_name . " - " . $task_name . " ";

    $mail->Subject = $title;
    $content =  "<p>Dear all,</p>";
    $content = $content . $tab;
    $content = $content . "<p>Task Management of " . $task_department . " Department</p>";

    //如果是普通任務，執行下一行
    //$content = $content . "<p>Task: " . $task_name . "</p>";
    //如果是訂單任務，執行下一行
    $content = $content . "<p>Inquiry Task: " . $task_name . "</p>";


    $content = $content . "<p>Task Status: " . $old_status . ' => ' . $task_status . "</p>";

    //如果是訂單任務，執行下兩行
    //$content = $content . "<p>Order Type: " . $order_type . "</p>";
    $content = $content . "<p>Inquiry Name: " . $order_name . "</p>";

    $content = $content . "<p>Creator: " . $creators . "</p>";
    $content = $content . "<p>Assignee: " . $assignees . "</p>";
    $content = $content . "<p>Collaborator: " . $collaborators . "</p>";
    $content = $content . "<p>Due Date: " . $due_date . "</p>";
    $content = $content . "<p>Description: " . $detail . "</p>";
    $content = $content . "<p> </p>";
    $content = $content . "<p>Please click this link to view the target webpage: </p>";
    $content = $content . "<p>https://feliix.myvnc.com/task_management_" . $task_type . "?sid=" . $stage_id . "</p>";

    $mail->MsgHTML($content);
    if($mail->Send()) {
        logMail($creators, $content);
        return true;
    } else {
        logMail($creators, $mail->ErrorInfo . $content);
        return false;
    }

}


function task_notify02($old_status, $task_status, $project_name, $task_name, $stage, $stages_status, $create_id, $assignee, $collaborator, $due_date, $detail, $stage_id)
{

    //$tab = "<p>A task was revised and needs you to follow. Below is the details:</p>";

    //如果是普通任務，執行下一行
    $tab = '<p>A task was revised and needs you to follow. Below is the details:</p>';
    //如果是訂單任務，執行下一行
    //$tab = '<p>A Order task was revised and needs you to follow. Below is the details:</p>';

    $conf = new Conf();

    $mail = new PHPMailer();
    $mail->IsSMTP();
    $mail->Mailer = "smtp";
    $mail->CharSet = 'UTF-8';
    $mail->Encoding = 'base64';

    // $mail->SMTPDebug  = 0;
    // $mail->SMTPAuth   = true;
    // $mail->SMTPSecure = "ssl";
    // $mail->Port       = 465;
    // $mail->SMTPKeepAlive = true;
    // $mail->Host       = $conf::$mail_host;
    // $mail->Username   = $conf::$mail_username;
    // $mail->Password   = $conf::$mail_password;

    $mail = SetupMail($mail, $conf);


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

    // 在Order Stage中，當使用者XXXX Task，系統發出的通知信中需要額外加入「職位為 Service Manager」和「職位為 Warehouse in charge」的人員
    if($stage == "Order")
    {
        $notifior = GetChargeNotifiersByTitle('Warehouse in charge');
        foreach($notifior as &$list)
        {
            $mail->AddAddress($list["email"], $list["username"]);
        }

        $catagory_id = GetProjectCategoryByStageId($stage_id);
        if($catagory_id == "2")
        {
            $notifior = GetChargeNotifiersByTitle('Engineering Manager');
            foreach($notifior as &$list)
            {
                $mail->AddAddress($list["email"], $list["username"]);
            }
        }

    }

    $creators = rtrim($creators, ", ");
    $assignees = rtrim($assignees, ", ");
    $collaborators = rtrim($collaborators, ", ");

    $mail->SetFrom("feliix.it@gmail.com", "Feliix.System");
    $mail->AddReplyTo("feliix.it@gmail.com", "Feliix.System");

    //$title = "[Task Notification] " . $project_name . " - " . $task_name . " ";

    //如果是普通任務，執行下一行
    $title = "[" . $assignees . "][Task Notification] " . $project_name . " - " . $task_name . " ";
    //如果是訂單任務，執行下一行
    //$title = "[" . $assignees . "][Order Task Notification] " . $project_name . " - " . $task_name . " ";


    $mail->Subject = $title;
    $content =  "<p>Dear all,</p>";
    $content = $content . $tab;
    $content = $content . "<p>Project Name: " . $project_name . "</p>";
    $content = $content . "<p>Stage: " . $stage . "</p>";
    //$content = $content . "<p>Task: " . $task_name . "</p>";

    //如果是普通任務，執行下一行
    $content = $content . "<p>Task: " . $task_name . "</p>";
    //如果是訂單任務，執行下一行
    //$content = $content . "<p>Order Task: " . $task_name . "</p>";

    if($old_status != $task_status)
        $content = $content . "<p>Task Status: " . $old_status . ' => ' . $task_status . "</p>";
    else
        $content = $content . "<p>Task Status: " . $task_status . "</p>";

    //如果是訂單任務，執行下兩行
    //$content = $content . "<p>Order Type: " . $order_type . "</p>";
    //$content = $content . "<p>Order Name: " . $order_name . "</p>";

    $content = $content . "<p>Creator: " . $creators . "</p>";
    $content = $content . "<p>Assignee: " . $assignees . "</p>";
    $content = $content . "<p>Collaborator: " . $collaborators . "</p>";
    $content = $content . "<p>Due Date: " . $due_date . "</p>";
    $content = $content . "<p>Description: " . $detail . "</p>";
    $content = $content . "<p> </p>";
    $content = $content . "<p>Please click this link to view the target webpage: </p>";
    $content = $content . "<p>https://feliix.myvnc.com/project03_other?sid=" . $stage_id . "</p>";

    $mail->MsgHTML($content);
    if($mail->Send()) {
        logMail($creators, $content);
        return true;
    } else {
        logMail($creators, $mail->ErrorInfo . $content);
        return false;
    }

}


function task_notify02_r($old_status, $task_status, $project_name, $task_name, $stage, $stages_status, $create_id, $created_at, $assignee, $collaborator, $due_date, $detail, $stage_id)
{

    //$tab = "<p>A task was revised and needs you to follow. Below is the details:</p>";

    //如果是普通任務，執行下一行
    $tab = '<p>A message was revised and needs you to follow. Below is the details:</p>';
    //如果是訂單任務，執行下一行
    //$tab = '<p>A Order task was revised and needs you to follow. Below is the details:</p>';

    $conf = new Conf();

    $mail = new PHPMailer();
    $mail->IsSMTP();
    $mail->Mailer = "smtp";
    $mail->CharSet = 'UTF-8';
    $mail->Encoding = 'base64';

    // $mail->SMTPDebug  = 0;
    // $mail->SMTPAuth   = true;
    // $mail->SMTPSecure = "ssl";
    // $mail->Port       = 465;
    // $mail->SMTPKeepAlive = true;
    // $mail->Host       = $conf::$mail_host;
    // $mail->Username   = $conf::$mail_username;
    // $mail->Password   = $conf::$mail_password;

    $mail = SetupMail($mail, $conf);


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

    // 在Order Stage中，當使用者XXXX Task，系統發出的通知信中需要額外加入「職位為 Service Manager」和「職位為 Warehouse in charge」的人員
    if($stage == "Order")
    {
        $notifior = GetChargeNotifiersByTitle('Warehouse in charge');
        foreach($notifior as &$list)
        {
            $mail->AddAddress($list["email"], $list["username"]);
        }

        $catagory_id = GetProjectCategoryByStageId($stage_id);
        if($catagory_id == "2")
        {
            $notifior = GetChargeNotifiersByTitle('Engineering Manager');
            foreach($notifior as &$list)
            {
                $mail->AddAddress($list["email"], $list["username"]);
            }
        }

    }

    $creators = rtrim($creators, ", ");
    $assignees = rtrim($assignees, ", ");
    $collaborators = rtrim($collaborators, ", ");

    $mail->SetFrom("feliix.it@gmail.com", "Feliix.System");
    $mail->AddReplyTo("feliix.it@gmail.com", "Feliix.System");

    //$title = "[" . $assignees . "][Task Notification] " . $project_name . " - " . $task_name . " ";

    //如果是普通任務，執行下一行
    $title = "[" . $assignees . "][Message Notification] " . $project_name . " - " . $task_name . " ";
    //如果是訂單任務，執行下一行
    //$title = "[" . $assignees . "][Order Task Notification] " . $project_name . " - " . $task_name . " ";


    $mail->Subject = $title;
    $content =  "<p>Dear all,</p>";
    $content = $content . $tab;
    $content = $content . "<p>Project Name: " . $project_name . "</p>";
    $content = $content . "<p>Stage: " . $stage . "</p>";
    $content = $content . "<p>Message Title: " . $task_name . "</p>";

    //如果是普通任務，執行下一行
    //$content = $content . "<p>Task: " . $task_name . "</p>";
    //如果是訂單任務，執行下一行
    //$content = $content . "<p>Order Task: " . $task_name . "</p>";

    // if($old_status != $task_status)
    //     $content = $content . "<p>Task Status: " . $old_status . ' => ' . $task_status . "</p>";
    // else
    //     $content = $content . "<p>Task Status: " . $task_status . "</p>";

    //如果是訂單任務，執行下兩行
    //$content = $content . "<p>Order Type: " . $order_type . "</p>";
    //$content = $content . "<p>Order Name: " . $order_name . "</p>";

    $content = $content . "<p>Creator: " . $creators . "</p>";
    $content = $content . "<p>Assignee: " . $assignees . "</p>";
    //$content = $content . "<p>Collaborator: " . $collaborators . "</p>";
    $content = $content . "<p>Created at: " . $created_at . "</p>";
    $content = $content . "<p>Description: " . $detail . "</p>";
    $content = $content . "<p> </p>";
    $content = $content . "<p>Please click this link to view the target webpage: </p>";
    $content = $content . "<p>https://feliix.myvnc.com/project03_other?sid=" . $stage_id . "</p>";

    $mail->MsgHTML($content);
    if($mail->Send()) {
        logMail($creators, $content);
        return true;
    } else {
        logMail($creators, $mail->ErrorInfo . $content);
        return false;
    }

}

function task_notify02_order($old_status, $task_status, $project_name, $task_name, $stage, $stages_status, $create_id, $assignee, $collaborator, $due_date, $detail, $stage_id, $order_type, $order_name)
{

    //$tab = "<p>A task was revised and needs you to follow. Below is the details:</p>";

    //如果是普通任務，執行下一行
    //$tab = '<p>A task was revised and needs you to follow. Below is the details:</p>';
    //如果是訂單任務，執行下一行
    $tab = '<p>A order task was revised and needs you to follow. Below is the details:</p>';

    $conf = new Conf();

    $mail = new PHPMailer();
    $mail->IsSMTP();
    $mail->Mailer = "smtp";
    $mail->CharSet = 'UTF-8';
    $mail->Encoding = 'base64';

    // $mail->SMTPDebug  = 0;
    // $mail->SMTPAuth   = true;
    // $mail->SMTPSecure = "ssl";
    // $mail->Port       = 465;
    // $mail->SMTPKeepAlive = true;
    // $mail->Host       = $conf::$mail_host;
    // $mail->Username   = $conf::$mail_username;
    // $mail->Password   = $conf::$mail_password;

    $mail = SetupMail($mail, $conf);


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

    // 在Order Stage中，當使用者XXXX Task，系統發出的通知信中需要額外加入「職位為 Service Manager」和「職位為 Warehouse in charge」的人員
    if($stage == "Order")
    {
        $notifior = GetChargeNotifiersByTitle('Warehouse in charge');
        foreach($notifior as &$list)
        {
            $mail->AddAddress($list["email"], $list["username"]);
        }

        $catagory_id = GetProjectCategoryByStageId($stage_id);
        if($catagory_id == "2")
        {
            $notifior = GetChargeNotifiersByTitle('Engineering Manager');
            foreach($notifior as &$list)
            {
                $mail->AddAddress($list["email"], $list["username"]);
            }
        }

    }

    $creators = rtrim($creators, ", ");
    $assignees = rtrim($assignees, ", ");
    $collaborators = rtrim($collaborators, ", ");

    $mail->SetFrom("feliix.it@gmail.com", "Feliix.System");
    $mail->AddReplyTo("feliix.it@gmail.com", "Feliix.System");

    //$title = "[" . $assignees . "][Task Notification] " . $project_name . " - " . $task_name . " ";

    //如果是普通任務，執行下一行
    //$title = "[" . $assignees . "][Task Notification] " . $project_name . " - " . $task_name . " ";
    //如果是訂單任務，執行下一行
    $title = "[" . $assignees . "][Order Task Notification] " . $project_name . " - " . $task_name . " ";


    $mail->Subject = $title;
    $content =  "<p>Dear all,</p>";
    $content = $content . $tab;
    $content = $content . "<p>Project Name: " . $project_name . "</p>";
    $content = $content . "<p>Stage: " . $stage . "</p>";
    //$content = $content . "<p>Task: " . $task_name . "</p>";

    //如果是普通任務，執行下一行
    //$content = $content . "<p>Task: " . $task_name . "</p>";
    //如果是訂單任務，執行下一行
    $content = $content . "<p>Order Task: " . $task_name . "</p>";

    if($old_status != $task_status)
        $content = $content . "<p>Task Status: " . $old_status . ' => ' . $task_status . "</p>";
    else
        $content = $content . "<p>Task Status: " . $task_status . "</p>";

    //如果是訂單任務，執行下兩行
    $content = $content . "<p>Order Type: " . $order_type . "</p>";
    $content = $content . "<p>Order Name: " . $order_name . "</p>";

    $content = $content . "<p>Creator: " . $creators . "</p>";
    $content = $content . "<p>Assignee: " . $assignees . "</p>";
    $content = $content . "<p>Collaborator: " . $collaborators . "</p>";
    $content = $content . "<p>Due Date: " . $due_date . "</p>";
    $content = $content . "<p>Description: " . $detail . "</p>";
    $content = $content . "<p> </p>";
    $content = $content . "<p>Please click this link to view the target webpage: </p>";
    $content = $content . "<p>https://feliix.myvnc.com/project03_other?sid=" . $stage_id . "</p>";

    $mail->MsgHTML($content);
    if($mail->Send()) {
        logMail($creators, $content);
        return true;
    } else {
        logMail($creators, $mail->ErrorInfo . $content);
        return false;
    }

}


function task_notify02_inquiry($old_status, $task_status, $project_name, $task_name, $stage, $stages_status, $create_id, $assignee, $collaborator, $due_date, $detail, $stage_id, $order_type, $order_name)
{

    //$tab = "<p>A task was revised and needs you to follow. Below is the details:</p>";

    //如果是普通任務，執行下一行
    //$tab = '<p>A task was revised and needs you to follow. Below is the details:</p>';
    //如果是訂單任務，執行下一行
    $tab = '<p>A inquiry task was revised and needs you to follow. Below is the details:</p>';

    $conf = new Conf();

    $mail = new PHPMailer();
    $mail->IsSMTP();
    $mail->Mailer = "smtp";
    $mail->CharSet = 'UTF-8';
    $mail->Encoding = 'base64';

    // $mail->SMTPDebug  = 0;
    // $mail->SMTPAuth   = true;
    // $mail->SMTPSecure = "ssl";
    // $mail->Port       = 465;
    // $mail->SMTPKeepAlive = true;
    // $mail->Host       = $conf::$mail_host;
    // $mail->Username   = $conf::$mail_username;
    // $mail->Password   = $conf::$mail_password;

    $mail = SetupMail($mail, $conf);


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

    // 在Order Stage中，當使用者XXXX Task，系統發出的通知信中需要額外加入「職位為 Service Manager」和「職位為 Warehouse in charge」的人員
    if($stage == "Order")
    {
        $notifior = GetChargeNotifiersByTitle('Warehouse in charge');
        foreach($notifior as &$list)
        {
            $mail->AddAddress($list["email"], $list["username"]);
        }

        $catagory_id = GetProjectCategoryByStageId($stage_id);
        if($catagory_id == "2")
        {
            $notifior = GetChargeNotifiersByTitle('Engineering Manager');
            foreach($notifior as &$list)
            {
                $mail->AddAddress($list["email"], $list["username"]);
            }
        }

    }

    $creators = rtrim($creators, ", ");
    $assignees = rtrim($assignees, ", ");
    $collaborators = rtrim($collaborators, ", ");

    $mail->SetFrom("feliix.it@gmail.com", "Feliix.System");
    $mail->AddReplyTo("feliix.it@gmail.com", "Feliix.System");

    //$title = "[" . $assignees . "][Task Notification] " . $project_name . " - " . $task_name . " ";

    //如果是普通任務，執行下一行
    //$title = "[" . $assignees . "][Task Notification] " . $project_name . " - " . $task_name . " ";
    //如果是訂單任務，執行下一行
    $title = "[" . $assignees . "][Inquiry Task Notification] " . $project_name . " - " . $task_name . " ";


    $mail->Subject = $title;
    $content =  "<p>Dear all,</p>";
    $content = $content . $tab;
    $content = $content . "<p>Project Name: " . $project_name . "</p>";
    $content = $content . "<p>Stage: " . $stage . "</p>";
    //$content = $content . "<p>Task: " . $task_name . "</p>";

    //如果是普通任務，執行下一行
    //$content = $content . "<p>Task: " . $task_name . "</p>";
    //如果是訂單任務，執行下一行
    $content = $content . "<p>Inquiry Task: " . $task_name . "</p>";

    if($old_status != $task_status)
        $content = $content . "<p>Task Status: " . $old_status . ' => ' . $task_status . "</p>";
    else
        $content = $content . "<p>Task Status: " . $task_status . "</p>";

    //如果是訂單任務，執行下兩行
    //$content = $content . "<p>Order Type: " . $order_type . "</p>";
    $content = $content . "<p>Inquiry Name: " . $order_name . "</p>";

    $content = $content . "<p>Creator: " . $creators . "</p>";
    $content = $content . "<p>Assignee: " . $assignees . "</p>";
    $content = $content . "<p>Collaborator: " . $collaborators . "</p>";
    $content = $content . "<p>Due Date: " . $due_date . "</p>";
    $content = $content . "<p>Description: " . $detail . "</p>";
    $content = $content . "<p> </p>";
    $content = $content . "<p>Please click this link to view the target webpage: </p>";
    $content = $content . "<p>https://feliix.myvnc.com/project03_other?sid=" . $stage_id . "</p>";

    $mail->MsgHTML($content);
    if($mail->Send()) {
        logMail($creators, $content);
        return true;
    } else {
        logMail($creators, $mail->ErrorInfo . $content);
        return false;
    }

}

function task_notify02_type_order($old_status, $task_status, $project_name, $task_name, $stage, $stages_status, $create_id, $assignee, $collaborator, $due_date, $detail, $stage_id, $order_type, $order_name, $task_type)
{

    //$tab = "<p>A task was revised and needs you to follow. Below is the details:</p>";

    //如果是普通任務，執行下一行
    //$tab = '<p>A task was revised and needs you to follow. Below is the details:</p>';
    //如果是訂單任務，執行下一行
    $tab = '<p>A order task was revised and needs you to follow. Below is the details:</p>';

    $conf = new Conf();

    $mail = new PHPMailer();
    $mail->IsSMTP();
    $mail->Mailer = "smtp";
    $mail->CharSet = 'UTF-8';
    $mail->Encoding = 'base64';

    // $mail->SMTPDebug  = 0;
    // $mail->SMTPAuth   = true;
    // $mail->SMTPSecure = "ssl";
    // $mail->Port       = 465;
    // $mail->SMTPKeepAlive = true;
    // $mail->Host       = $conf::$mail_host;
    // $mail->Username   = $conf::$mail_username;
    // $mail->Password   = $conf::$mail_password;

    $mail = SetupMail($mail, $conf);


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

    // 在Order Stage中，當使用者XXXX Task，系統發出的通知信中需要額外加入「職位為 Service Manager」和「職位為 Warehouse in charge」的人員
    if($stage == "Order")
    {
        $notifior = GetChargeNotifiersByTitle('Warehouse in charge');
        foreach($notifior as &$list)
        {
            $mail->AddAddress($list["email"], $list["username"]);
        }

        $catagory_id = GetProjectCategoryByStageId($stage_id);
        if($catagory_id == "2")
        {
            $notifior = GetChargeNotifiersByTitle('Engineering Manager');
            foreach($notifior as &$list)
            {
                $mail->AddAddress($list["email"], $list["username"]);
            }
        }

    }

    $task_department = "";
    if($task_type == 'LT')
        $task_department = "Lighting";
    if($task_type == 'OS')
        $task_department = "Office Systems";
    if($task_type == 'SLS')
        $task_department = "Sales";
    if($task_type == 'SVC')
        $task_department = "Engineering Department";

    $creators = rtrim($creators, ", ");
    $assignees = rtrim($assignees, ", ");
    $collaborators = rtrim($collaborators, ", ");

    $mail->SetFrom("feliix.it@gmail.com", "Feliix.System");
    $mail->AddReplyTo("feliix.it@gmail.com", "Feliix.System");

    //$title = "[" . $assignees . "][Task Notification] " . $project_name . " - " . $task_name . " ";

    //如果是普通任務，執行下一行
    //$title = "[" . $assignees . "][Task Notification] " . $project_name . " - " . $task_name . " ";
    //如果是訂單任務，執行下一行
    $title = "[" . $assignees . "][Order Task Notification] " . $project_name . " - " . $task_name . " ";


    $mail->Subject = $title;
    $content =  "<p>Dear all,</p>";
    $content = $content . $tab;
    $content = $content . "<p>Task Management of " . $task_department . " Department</p>";
    //$content = $content . "<p>Task: " . $task_name . "</p>";

    //如果是普通任務，執行下一行
    //$content = $content . "<p>Task: " . $task_name . "</p>";
    //如果是訂單任務，執行下一行
    $content = $content . "<p>Order Task: " . $task_name . "</p>";

    if($old_status != $task_status)
        $content = $content . "<p>Task Status: " . $old_status . ' => ' . $task_status . "</p>";
    else
        $content = $content . "<p>Task Status: " . $task_status . "</p>";

    //如果是訂單任務，執行下兩行
    $content = $content . "<p>Order Type: " . $order_type . "</p>";
    $content = $content . "<p>Order Name: " . $order_name . "</p>";

    $content = $content . "<p>Creator: " . $creators . "</p>";
    $content = $content . "<p>Assignee: " . $assignees . "</p>";
    $content = $content . "<p>Collaborator: " . $collaborators . "</p>";
    $content = $content . "<p>Due Date: " . $due_date . "</p>";
    $content = $content . "<p>Description: " . $detail . "</p>";
    $content = $content . "<p> </p>";
    $content = $content . "<p>Please click this link to view the target webpage: </p>";
    $content = $content . "<p>https://feliix.myvnc.com/task_management_" . $task_type . "?sid=" . $stage_id . "</p>";

    $mail->MsgHTML($content);
    if($mail->Send()) {
        logMail($creators, $content);
        return true;
    } else {
        logMail($creators, $mail->ErrorInfo . $content);
        return false;
    }

}


function task_notify02_type_inquiry($old_status, $task_status, $project_name, $task_name, $stage, $stages_status, $create_id, $assignee, $collaborator, $due_date, $detail, $stage_id, $order_type, $order_name, $task_type)
{

    //$tab = "<p>A task was revised and needs you to follow. Below is the details:</p>";

    //如果是普通任務，執行下一行
    //$tab = '<p>A task was revised and needs you to follow. Below is the details:</p>';
    //如果是訂單任務，執行下一行
    $tab = '<p>A inquiry task was revised and needs you to follow. Below is the details:</p>';

    $conf = new Conf();

    $mail = new PHPMailer();
    $mail->IsSMTP();
    $mail->Mailer = "smtp";
    $mail->CharSet = 'UTF-8';
    $mail->Encoding = 'base64';

    // $mail->SMTPDebug  = 0;
    // $mail->SMTPAuth   = true;
    // $mail->SMTPSecure = "ssl";
    // $mail->Port       = 465;
    // $mail->SMTPKeepAlive = true;
    // $mail->Host       = $conf::$mail_host;
    // $mail->Username   = $conf::$mail_username;
    // $mail->Password   = $conf::$mail_password;

    $mail = SetupMail($mail, $conf);


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

    // 在Order Stage中，當使用者XXXX Task，系統發出的通知信中需要額外加入「職位為 Service Manager」和「職位為 Warehouse in charge」的人員
    if($stage == "Order")
    {
        $notifior = GetChargeNotifiersByTitle('Warehouse in charge');
        foreach($notifior as &$list)
        {
            $mail->AddAddress($list["email"], $list["username"]);
        }

        $catagory_id = GetProjectCategoryByStageId($stage_id);
        if($catagory_id == "2")
        {
            $notifior = GetChargeNotifiersByTitle('Engineering Manager');
            foreach($notifior as &$list)
            {
                $mail->AddAddress($list["email"], $list["username"]);
            }
        }

    }

    $task_department = "";
    if($task_type == 'LT')
        $task_department = "Lighting";
    if($task_type == 'OS')
        $task_department = "Office Systems";
    if($task_type == 'SLS')
        $task_department = "Sales";
    if($task_type == 'SVC')
        $task_department = "Engineering Department";

    $creators = rtrim($creators, ", ");
    $assignees = rtrim($assignees, ", ");
    $collaborators = rtrim($collaborators, ", ");

    $mail->SetFrom("feliix.it@gmail.com", "Feliix.System");
    $mail->AddReplyTo("feliix.it@gmail.com", "Feliix.System");

    //$title = "[" . $assignees . "][Task Notification] " . $project_name . " - " . $task_name . " ";

    //如果是普通任務，執行下一行
    //$title = "[" . $assignees . "][Task Notification] " . $project_name . " - " . $task_name . " ";
    //如果是訂單任務，執行下一行
    $title = "[" . $assignees . "][Inquiry Task Notification] " . $project_name . " - " . $task_name . " ";


    $mail->Subject = $title;
    $content =  "<p>Dear all,</p>";
    $content = $content . $tab;
    $content = $content . "<p>Task Management of " . $task_department . " Department</p>";
    //$content = $content . "<p>Task: " . $task_name . "</p>";

    //如果是普通任務，執行下一行
    //$content = $content . "<p>Task: " . $task_name . "</p>";
    //如果是訂單任務，執行下一行
    $content = $content . "<p>Inquiry Task: " . $task_name . "</p>";

    if($old_status != $task_status)
        $content = $content . "<p>Task Status: " . $old_status . ' => ' . $task_status . "</p>";
    else
        $content = $content . "<p>Task Status: " . $task_status . "</p>";

    //如果是訂單任務，執行下兩行
    //$content = $content . "<p>Order Type: " . $order_type . "</p>";
    $content = $content . "<p>Inquiry Name: " . $order_name . "</p>";

    $content = $content . "<p>Creator: " . $creators . "</p>";
    $content = $content . "<p>Assignee: " . $assignees . "</p>";
    $content = $content . "<p>Collaborator: " . $collaborators . "</p>";
    $content = $content . "<p>Due Date: " . $due_date . "</p>";
    $content = $content . "<p>Description: " . $detail . "</p>";
    $content = $content . "<p> </p>";
    $content = $content . "<p>Please click this link to view the target webpage: </p>";
    $content = $content . "<p>https://feliix.myvnc.com/task_management_" . $task_type . "?sid=" . $stage_id . "</p>";

    $mail->MsgHTML($content);
    if($mail->Send()) {
        logMail($creators, $content);
        return true;
    } else {
        logMail($creators, $mail->ErrorInfo . $content);
        return false;
    }

}

function GetCarCheckers()
{
    $database = new Database_Sea();
    $db = $database->getConnection();

    $names = [];
    $result = "";

    // get car_access1, car_access2 split by comma
    $sql = "select car_access1, car_access2  from access_control ";
    $rs = array();

    $stmt = $db->prepare($sql);
    $stmt->execute();

    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        $rs[] = $row;
    }

    foreach($rs as &$list)
    {
        $arr = explode(',', $list['car_access1']);
        $result = "'" . implode ( "', '", $arr ) . "'";

        $arr = explode(',', $list['car_access2']);
        $result .= ",'" . implode ( "', '", $arr ) . "'";

    }

    if($result != "")
    {
        $sql = "SELECT user.id, username, email FROM user 
            WHERE user.username in (" . $result . ") and user.status = 1";
    
        $merged_results = array();
    
        $stmt = $db->prepare($sql);
        $stmt->execute();
    
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $merged_results[] = $row;
        }
    }

    return $merged_results;
}

function GetCarChecker1()
{
    $database = new Database_Sea();
    $db = $database->getConnection();

    $names = [];
    $result = "";

    // get car_access1, car_access2 split by comma
    $sql = "select car_access1  from access_control ";
    $rs = array();

    $stmt = $db->prepare($sql);
    $stmt->execute();

    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        $rs[] = $row;
    }

    foreach($rs as &$list)
    {
        $arr = explode(',', $list['car_access1']);
        $result = "'" . implode ( "', '", $arr ) . "'";
    }


    if($result != "")
    {
        $sql = "SELECT user.id, username, email FROM user 
            WHERE user.username in (" . $result . ") and user.status = 1";
    
        $merged_results = array();
    
        $stmt = $db->prepare($sql);
        $stmt->execute();
    
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $merged_results[] = $row;
        }
    }

    return $merged_results;
}

function GetNotifiersByName($names)
{
    $database = new Database();
    $db = $database->getConnection();

    $myArray = explode(',', $names);
    $result = "'" . implode ( "', '", $myArray ) . "'";

    $sql = "SELECT user.id, username, email, title, department FROM user 
    LEFT JOIN user_department ON user.apartment_id = user_department.id LEFT JOIN user_title ON user.title_id = user_title.id
        WHERE user.username in (" . $result . ") and user.status = 1";

    $merged_results = array();

    $stmt = $db->prepare($sql);
    $stmt->execute();

    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        $merged_results[] = $row;
    }

    return $merged_results;
}

function GetNotifiers($id)
{
    $database = new Database();
    $db = $database->getConnection();

    $sql = "SELECT user.id, username, email, title, department FROM user 
    LEFT JOIN user_department ON user.apartment_id = user_department.id LEFT JOIN user_title ON user.title_id = user_title.id
        WHERE user.id in (" . $id . ")  and user.status = 1";

    $merged_results = array();

    $stmt = $db->prepare($sql);
    $stmt->execute();

    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        $merged_results[] = $row;
    }

    return $merged_results;
}

function GetExpenseFlowVerifiers()
{
    $database = new Database();
    $db = $database->getConnection();

    $sql = "SELECT user.id, username, email, title, department, flow 
            FROM user 
            LEFT JOIN user_department ON user.apartment_id = user_department.id 
            LEFT JOIN user_title ON user.title_id = user_title.id
            LEFT JOIN expense_flow ON user.id = expense_flow.uid
            WHERE user.status = 1 and flow = 7";

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
    WHERE flow in (1, 2, 3) and u.status = 1";

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

    // $mail->SMTPDebug  = 0;
    // $mail->SMTPAuth   = true;
    // $mail->SMTPSecure = "ssl";
    // $mail->Port       = 465;
    // $mail->SMTPKeepAlive = true;
    // $mail->Host       = $conf::$mail_host;
    // $mail->Username   = $conf::$mail_username;
    // $mail->Password   = $conf::$mail_password;

    $mail = SetupMail($mail, $conf);

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

    // 當使用者在project01.php透過「+按鈕」新建立一個專案且該專案的狀態(status)是 Approved 時，系統寄發的通知信的收件人中，需要再新增 Service Manager (也就是 Edneil Fernandez，edneil@feliix.com)
    if($estimate_close_prob == "80" || $estimate_close_prob == "90" || $estimate_close_prob == "100" || $project_status == "Approved" || $project_status == "Completed")
    {
        
        $catagory_id = GetProjectCategoryByProjectId($project_id);
        if($catagory_id == "2")
        {
            $notifior = GetProjectServiceNotifiers();
            foreach($notifior as &$list)
            {
                $mail->AddAddress($list["email"], $list["username"]);
            }
        }
        
    }

    $mail->SetFrom("feliix.it@gmail.com", "Feliix.System");
    $mail->AddReplyTo("feliix.it@gmail.com", "Feliix.System");
    
    $mail->Subject = $title;
    $content =  "<p>Dear all,</p>";
    $content = $content . $tab;
    $content = $content . "<p>Project Name: " . $project_name . "</p>";
    $content = $content . "<p>Project Category: " . $category . "</p>";
    $content = $content . "<p>Client Type: " . $client_type . "</p>";
    $content = $content . "<p>Priority: " . $priority . "</p>";
    $content = $content . "<p>Project Status: " . $project_status . "</p>";
    $content = $content . "<p>Estimated Closing Prob.: " . $estimate_close_prob . "</p>";
    $content = $content . "<p>Project Creator: " . $username . " at " . $created_at . "</p>";

    $content = $content . "<p> </p>";
    $content = $content . "<p>Please click this link to view the target webpage: </p>";
    $content = $content . "<p>https://feliix.myvnc.com/project02?p=" . $project_id . "</p>";

    $mail->MsgHTML($content);
    if($mail->Send()) {
        logMail($username, $content);
        return true;
    } else {
        logMail($username, $mail->ErrorInfo . $content);
        return false;
    }

}

function project02_stage_notify_mail($stage_name, $project_name, $username, $created_at, $stage_status, $project_id, $stage_creator_id, $category, $main_id)
{

    $title = 'Stage "'. $stage_name .'" was created in Project "' . $project_name . '" ';
    $tab = '<p>Stage "'. $stage_name .'" was created in Project "' . $project_name . '" by ' . $username . '. Following are the details:</p>';
    
    $conf = new Conf();

    $mail = new PHPMailer();
    $mail->IsSMTP();
    $mail->Mailer = "smtp";
    $mail->CharSet = 'UTF-8';
    $mail->Encoding = 'base64';

    
    // $mail->SMTPDebug  = 0;
    // $mail->SMTPAuth   = true;
    // $mail->SMTPSecure = "ssl";
    // $mail->Port       = 465;
    // $mail->SMTPKeepAlive = true;
    // $mail->Host       = $conf::$mail_host;
    // $mail->Username   = $conf::$mail_username;
    // $mail->Password   = $conf::$mail_password;

    $mail = SetupMail($mail, $conf);

    $mail->IsHTML(true);

    $notifior = array();

    // 如果是新建立的stage，則需要cc給stage的創建者
    $notifior = GetNotifiers($stage_creator_id);
    foreach($notifior as &$list)
    {
        $mail->AddCC($list["email"], $list["username"]);
    }

    // 也需要cc給project的創建者
    $notifior = GetNotifiers($main_id);
    foreach($notifior as &$list)
    {
        $mail->AddCC($list["email"], $list["username"]);
    }

    // Order Stage被建立時(function project02_stage_notify_mail)，系統發出的通知信中需要額外加入「職位為 Warehouse in charge」的人員
    if($stage_name == "Order")
    {
        $notifior = GetChargeNotifiersByTitle('Warehouse in charge');
        foreach($notifior as &$list)
        {
            $mail->AddAddress($list["email"], $list["username"]);
        }
    }

    $notifior = GetProject01NotifiersByCatagory($category);
    foreach($notifior as &$list)
    {
        $mail->AddAddress($list["email"], $list["username"]);
    }

    if($stage_name == 'A Meeting / Close Deal' || $stage_name == 'Order')
    {
        $category_id = GetProjectCategoryByProjectId($project_id);
        if($category_id == "2")
        {
            $notifior = GetProjectServiceNotifiers();
            foreach($notifior as &$list)
            {
                $mail->AddAddress($list["email"], $list["username"]);
            }
        }
        
    }


    $mail->SetFrom("feliix.it@gmail.com", "Feliix.System");
    $mail->AddReplyTo("feliix.it@gmail.com", "Feliix.System");
    
    $mail->Subject = $title;
    $content =  "<p>Dear all,</p>";
    $content = $content . $tab;
    $content = $content . "<p>Project Name: " . $project_name . "</p>";
    $content = $content . "<p>Stage: " . $stage_name . "</p>";
    $content = $content . "<p>Status of Stage: " . $stage_status . "</p>";
    $content = $content . "<p>Stage Creator: " . $username . " at " . $created_at . "</p>";

    $content = $content . "<p> </p>";
    $content = $content . "<p>Please click this link to view the target webpage: </p>";
    $content = $content . "<p>https://feliix.myvnc.com/project02?p=" . $project_id . "</p>";

    $mail->MsgHTML($content);
    if($mail->Send()) {
        logMail($username, $content);
        return true;
    } else {
        logMail($username, $mail->ErrorInfo . $content);
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

    // $mail->SMTPDebug  = 0;
    // $mail->SMTPAuth   = true;
    // $mail->SMTPSecure = "ssl";
    // $mail->Port       = 465;
    // $mail->SMTPKeepAlive = true;
    // $mail->Host       = $conf::$mail_host;
    // $mail->Username   = $conf::$mail_username;
    // $mail->Password   = $conf::$mail_password;

    $mail = SetupMail($mail, $conf);

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
    $content = $content . "<p>Project Name: " . $project_name . "</p>";
    $content = $content . "<p>Message Creator: " . $username . " at " . $created_at . "</p>";
    $content = $content . "<p>Content: " . $message . "</p>";

    $content = $content . "<p> </p>";
    $content = $content . "<p>Please click this link to view the target webpage: </p>";
    $content = $content . "<p>https://feliix.myvnc.com/project03_client?sid=" . $stage_id . "</p>";

    $mail->MsgHTML($content);
    if($mail->Send()) {
        logMail($username, $content);
        return true;
    } else {
        logMail($username, $mail->ErrorInfo . $content);
        return false;
    }
}

function project02_status_change_notify_mail($project_name, $project_category, $username, $created_at, $client_type, $priority, $estimate_close_prob, $project_status, $pre_status, $project_id, $create_id, $project_status_edit, $reason)
{

    $title = 'Status of project "' . $project_name . '" changed to "' . $project_status . '" ';
    $tab = '<p>Status of project "' . $project_name . '" changed to "' . $project_status . '". Following are the details:</p>';
    
    $conf = new Conf();

    $mail = new PHPMailer();
    $mail->IsSMTP();
    $mail->Mailer = "smtp";
    $mail->CharSet = 'UTF-8';
    $mail->Encoding = 'base64';

    // $mail->SMTPDebug  = 0;
    // $mail->SMTPAuth   = true;
    // $mail->SMTPSecure = "ssl";
    // $mail->Port       = 465;
    // $mail->SMTPKeepAlive = true;
    // $mail->Host       = $conf::$mail_host;
    // $mail->Username   = $conf::$mail_username;
    // $mail->Password   = $conf::$mail_password;
    $mail = SetupMail($mail, $conf);

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

    // 當使用者在project01.php透過「+按鈕」新建立一個專案且該專案的狀態(status)是 Approved 時，系統寄發的通知信的收件人中，需要再新增 Service Manager (也就是 Edneil Fernandez，edneil@feliix.com)
    if($project_status == "Approved" || $project_status == "Completed")
    {
        $catagory_id = GetProjectCategoryByProjectId($project_id);
        if($catagory_id == "2")
        {
            $notifior = GetProjectServiceNotifiers();
            foreach($notifior as &$list)
            {
                $mail->AddAddress($list["email"], $list["username"]);
            }
        }
        
    }

    $mail->SetFrom("feliix.it@gmail.com", "Feliix.System");
    $mail->AddReplyTo("feliix.it@gmail.com", "Feliix.System");
    
    $mail->Subject = $title;
    $content =  "<p>Dear all,</p>";
    $content = $content . $tab;
    $content = $content . "<p>Project Name: " . $project_name . "</p>";
    $content = $content . "<p>Project Category: " . $project_category . "</p>";
    $content = $content . "<p>Client Type: " . $client_type . "</p>";
    $content = $content . "<p>Priority: " . $priority . "</p>";
    $content = $content . "<p>Project Status: " . $pre_status . " => " . $project_status.  "</p>";

    // 當專案的狀態(status) 變成是 Disapproved 時，系統寄發的通知信內容中，需要新增一個 理由 欄位，欄位裡面會抓 使用者在project02.php中變更狀態時填寫的理由
    if($project_status == "Disapproved")
    {
        $content = $content . "<p>Reason: " . $reason . "</p>";
    }

    $content = $content . "<p>Estimated Closing Prob.: " . $estimate_close_prob . "</p>";
    $content = $content . "<p>Project Creator: " . $username . " at " . $created_at . "</p>";

    $content = $content . "<p> </p>";
    $content = $content . "<p>Please click this link to view the target webpage: </p>";
    $content = $content . "<p>https://feliix.myvnc.com/project02?p=" . $project_id . "</p>";

    $mail->MsgHTML($content);
    if($mail->Send()) {
        logMail($username, $content);
        return true;
    } else {
        logMail($username, $mail->ErrorInfo . $content);
        return false;
    }

}


function send_salary_slip(  $start_date, 
                            $end_date, 
                            $employee_id, 
                            $user_id
                            )
{
    $title = "[Notification] Salary Slip for " . $start_date . " to " . $end_date . " Needs Confirmation";

    $conf = new Conf();

    $mail = new PHPMailer();
    $mail->IsSMTP();
    $mail->Mailer = "smtp";
    $mail->CharSet = 'UTF-8';
    $mail->Encoding = 'base64';

    // $mail->SMTPDebug  = 0;
    // $mail->SMTPAuth   = true;
    // $mail->SMTPSecure = "ssl";
    // $mail->Port       = 465;
    // $mail->SMTPKeepAlive = true;
    // $mail->Host       = $conf::$mail_host;
    // $mail->Username   = $conf::$mail_username;
    // $mail->Password   = $conf::$mail_password;

    $mail = SetupMail($mail, $conf);

    $mail->IsHTML(true);

    $employee = GetNotifiers($employee_id);
    $emp_name = "";
    $sumitor = "";
    foreach($employee as &$list)
    {
        $emp_name = $list["username"];
        $mail->AddAddress($list["email"], $list["username"]);
    }

    $notifior = GetNotifiers($user_id);
    foreach($notifior as &$list)
    {
        $sumitor = $list["username"];
        $mail->AddCC($list["email"], $list["username"]);
    }

    $mail->SetFrom("feliix.it@gmail.com", "Feliix.System");
    $mail->AddReplyTo("feliix.it@gmail.com", "Feliix.System");
    
    $mail->Subject = $title;
    $content =  "<p>Dear " . $emp_name . ",</p>";
    $content = $content . "<p> Your salary slip for " . $start_date . " to " . $end_date . " was submitted by " . $sumitor . " and needs you to confirm.</p>";
    $content = $content . "<p> </p>";
    $content = $content . "<p>Please log on to Feliix >> Payment Request/Claim and Salary Slip >> Salary Slip to review your salary slip details and confirm.</p>";
    $content = $content . "<p>URL: " . $conf::$mail_ip . "</p>";

    $mail->MsgHTML($content);
    if($mail->Send()) {
        logMail($emp_name, $content);
        return true;
    } else {
        logMail($emp_name, $mail->ErrorInfo . $content);
        return false;
    }
}

function send_salary_slip_withdraw(  
                            $start_date, 
                            $end_date, 
                            $employee_id, 
                            $user_id
                            )
{
 
    $conf = new Conf();

    $mail = new PHPMailer();
    $mail->IsSMTP();
    $mail->Mailer = "smtp";
    $mail->CharSet = 'UTF-8';
    $mail->Encoding = 'base64';

    // $mail->SMTPDebug  = 0;
    // $mail->SMTPAuth   = true;
    // $mail->SMTPSecure = "ssl";
    // $mail->Port       = 465;
    // $mail->SMTPKeepAlive = true;
    // $mail->Host       = $conf::$mail_host;
    // $mail->Username   = $conf::$mail_username;
    // $mail->Password   = $conf::$mail_password;

    $mail = SetupMail($mail, $conf);

    $mail->IsHTML(true);

    $employee = GetNotifiers($employee_id);
    $emp_name = "";
    $sumitor = "";
    foreach($employee as &$list)
    {
        $emp_name = $list["username"];
        $mail->AddAddress($list["email"], $list["username"]);
    }

    $notifior = GetNotifiers($user_id);
    foreach($notifior as &$list)
    {
        $sumitor = $list["username"];
        $mail->AddCC($list["email"], $list["username"]);
    }

    $title = "[Notification] Salary Slip for " . $start_date . " to " . $end_date . " was Withdrawn by " . $sumitor;

    $mail->SetFrom("feliix.it@gmail.com", "Feliix.System");
    $mail->AddReplyTo("feliix.it@gmail.com", "Feliix.System");
    
    $mail->Subject = $title;
    $content =  "<p>Dear " . $emp_name . ",</p>";
    $content = $content . "<p> Your salary slip for " . $start_date . " to " . $end_date . " was withdrawn by " . $sumitor . ". As a result, this salary slip will disappear from the table of salary slip, which is normal.</p>";

    $mail->MsgHTML($content);
    if($mail->Send()) {
        logMail($emp_name, $content);
        return true;
    } else {
        logMail($emp_name, $mail->ErrorInfo . $content);
        return false;
    }
}

function send_salary_slip_confirm(  
                            $start_date, 
                            $end_date, 
                            $employee_id, 
                            $user_id,
                            $remark
                            )
{
 
    $conf = new Conf();

    $mail = new PHPMailer();
    $mail->IsSMTP();
    $mail->Mailer = "smtp";
    $mail->CharSet = 'UTF-8';
    $mail->Encoding = 'base64';

    // $mail->SMTPDebug  = 0;
    // $mail->SMTPAuth   = true;
    // $mail->SMTPSecure = "ssl";
    // $mail->Port       = 465;
    // $mail->SMTPKeepAlive = true;
    // $mail->Host       = $conf::$mail_host;
    // $mail->Username   = $conf::$mail_username;
    // $mail->Password   = $conf::$mail_password;

    $mail = SetupMail($mail, $conf);

    $mail->IsHTML(true);

    $employee = GetNotifiers($employee_id);
    $emp_name = "";
    $sumitor = "";
    foreach($employee as &$list)
    {
        $emp_name = $list["username"];
        $mail->AddCC($list["email"], $list["username"]);
    }

    $notifior = GetNotifiers($user_id);
    foreach($notifior as &$list)
    {
        $sumitor = $list["username"];
        $mail->AddAddress($list["email"], $list["username"]);
    }

    $title = "[Notification] " . $emp_name . "'s Salary Slip for " . $start_date . " to " . $end_date . " was Confirmed";

    $mail->SetFrom("feliix.it@gmail.com", "Feliix.System");
    $mail->AddReplyTo("feliix.it@gmail.com", "Feliix.System");
    
    $mail->Subject = $title;

    $content =  "<p>Dear " . $sumitor . ",</p>";
    $content = $content . "<p>" . $emp_name . "'s salary slip for " . $start_date . " to " . $end_date . " was confirmed.</p>";
    $content = $content . "<p> </p>";
  
        $content = $content . "<p>Remarks from Employee: " . $remark . "</p>";
    $content = $content . "<p> </p>";
    $content = $content . "<p>Please log on to Feliix >> Payment Request/Claim and Salary Slip >> Salary Slip Management to view the salary slip.</p>";
    $content = $content . "<p>URL: " . $conf::$mail_ip . "</p>";


    $mail->MsgHTML($content);
    if($mail->Send()) {
        logMail($emp_name, $content);
        return true;
    } else {
        logMail($emp_name, $mail->ErrorInfo . $content);
        return false;
    }
}

function send_salary_slip_reject(  
    $start_date, 
    $end_date, 
    $employee_id, 
    $user_id,
    $remark
    )
{

$conf = new Conf();

$mail = new PHPMailer();
$mail->IsSMTP();
$mail->Mailer = "smtp";
$mail->CharSet = 'UTF-8';
$mail->Encoding = 'base64';

// $mail->SMTPDebug  = 0;
// $mail->SMTPAuth   = true;
// $mail->SMTPSecure = "ssl";
// $mail->Port       = 465;
// $mail->SMTPKeepAlive = true;
// $mail->Host       = $conf::$mail_host;
// $mail->Username   = $conf::$mail_username;
// $mail->Password   = $conf::$mail_password;

$mail = SetupMail($mail, $conf);

$mail->IsHTML(true);

$employee = GetNotifiers($employee_id);
$emp_name = "";
$sumitor = "";
foreach($employee as &$list)
{
$emp_name = $list["username"];
$mail->AddCC($list["email"], $list["username"]);
}

$notifior = GetNotifiers($user_id);
foreach($notifior as &$list)
{
$sumitor = $list["username"];
$mail->AddAddress($list["email"], $list["username"]);
}

$title = "[Notification] " . $emp_name . "'s Salary Slip for " . $start_date . " to " . $end_date . " was Rejected";

$mail->SetFrom("feliix.it@gmail.com", "Feliix.System");
$mail->AddReplyTo("feliix.it@gmail.com", "Feliix.System");

$mail->Subject = $title;

$content =  "<p>Dear " . $sumitor . ",</p>";
$content = $content . "<p>" . $emp_name . "'s salary slip for " . $start_date . " to " . $end_date . " was rejected.</p>";
$content = $content . "<p> </p>";

    $content = $content . "<p>Remarks from Employee: " . $remark . "</p>";
$content = $content . "<p> </p>";
$content = $content . "<p>Please log on to Feliix >> Payment Request/Claim and Salary Slip >> Salary Slip Management to view the salary slip.</p>";
$content = $content . "<p>URL: " . $conf::$mail_ip . "</p>";


$mail->MsgHTML($content);
if($mail->Send()) {
logMail($emp_name, $content);
return true;
} else {
logMail($emp_name, $mail->ErrorInfo . $content);
return false;
}
}

function send_salary_slip_delete(  
    $start_date, 
    $end_date, 
    $employee_id, 
    $user_id
    )
{

$conf = new Conf();

$mail = new PHPMailer();
$mail->IsSMTP();
$mail->Mailer = "smtp";
$mail->CharSet = 'UTF-8';
$mail->Encoding = 'base64';

// $mail->SMTPDebug  = 0;
// $mail->SMTPAuth   = true;
// $mail->SMTPSecure = "ssl";
// $mail->Port       = 465;
// $mail->SMTPKeepAlive = true;
// $mail->Host       = $conf::$mail_host;
// $mail->Username   = $conf::$mail_username;
// $mail->Password   = $conf::$mail_password;

$mail = SetupMail($mail, $conf);

$mail->IsHTML(true);

$employee = GetNotifiers($employee_id);
$emp_name = "";
$sumitor = "";
foreach($employee as &$list)
{
$emp_name = $list["username"];
$mail->AddAddress($list["email"], $list["username"]);
}

$notifior = GetNotifiers($user_id);
foreach($notifior as &$list)
{
$sumitor = $list["username"];
$mail->AddCC($list["email"], $list["username"]);
}

$title = "[Notification] Salary Slip for " . $start_date . " to " . $end_date . " was Deleted by " . $sumitor;

$mail->SetFrom("feliix.it@gmail.com", "Feliix.System");
$mail->AddReplyTo("feliix.it@gmail.com", "Feliix.System");

$mail->Subject = $title;
$content =  "<p>Dear " . $emp_name . ",</p>";
$content = $content . "<p> Your salary slip for " . $start_date . " to " . $end_date . " was deleted by " . $sumitor . ". As a result, this salary slip will disappear from the table of salary slip, which is normal.</p>";
$mail->MsgHTML($content);
if($mail->Send()) {
logMail($emp_name, $content);
return true;
} else {
logMail($emp_name, $mail->ErrorInfo . $content);
return false;
}
}

function send_pay_reminder_mail_new($name, $email1,  $leaver, $projectname, $remark, $subtime, $category, $kind, $special, $final_amount)
{
    $conf = new Conf();

    $mail = new PHPMailer();
    $mail->IsSMTP();
    $mail->Mailer = "smtp";
    $mail->CharSet = 'UTF-8';
    $mail->Encoding = 'base64';

    // $mail->SMTPDebug  = 0;
    // $mail->SMTPAuth   = true;
    // $mail->SMTPSecure = "ssl";
    // $mail->Port       = 465;
    // $mail->SMTPKeepAlive = true;
    // $mail->Host       = $conf::$mail_host;
    // $mail->Username   = $conf::$mail_username;
    // $mail->Password   = $conf::$mail_password;

    $mail = SetupMail($mail, $conf);

    $mail->IsHTML(true);
    if($special == "")
        $mail->AddAddress('glen@feliix.com', 'Glendon Wendell Co');
    if($special == "s")
        $mail->AddAddress('kuan@feliix.com', 'Kuan');

    $pay = "Full Payment";
    if($kind == 0)
        $pay = "Down Payment";

    if($kind == 2)
        $pay = "2307";

    if($special == "")
    {
        $mail->AddCC('kristel@feliix.com', 'Kristel Tan');
        $mail->AddCC($email1, $name);
        $mail->AddCC('dennis@feliix.com', 'Dennis Lin');
    }

    $mail->SetFrom("feliix.it@gmail.com", "Feliix.System");
    $mail->AddReplyTo("feliix.it@gmail.com", "Feliix.System");
    // $mail->AddCC("tryhelpbuy@gmail.com", "tryhelpbuy");
    $mail->Subject = "Payment Proof Submitted by " . $leaver . "(" . $projectname . ")";

    $content = '<!DOCTYPE html>
                <html lang="en">
                <head>
                    <meta http-equiv="content-type" content="text/html; charset=utf-8"/>
                </head>
                <body>
                <div style="width: 766px; padding: 25px 70px 20px 70px; border: 2px solid rgb(230,230,230); color: black;">
                    <table style="width: 100%;">
                        <tbody>
                        <tr>
                            <td style="font-size: 20px; padding: 20px 0 20px 5px;">';


    // 判斷 Project Type 和 Proof Kind 和 Project Final Amount 來決定 稱呼者名稱
    // Project Type = Normal
    if($special == "")
	$content = $content . ' Dear Glendon, ';

    // Project Type = X-Deal
    if($special == "s")
        $content = $content . ' Dear Boss, ';

    // Project Type = No DP and Kind = 0 and Amount <= 10萬
    if($special == "sn" && $kind == 0 && $final_amount <= 100000)
        $content = $content . ' Dear Kristel, ';

    // Project Type = No DP and Kind = 0 and Amount > 10萬
    if($special == "sn" && $kind == 0 && $final_amount > 100000)
        $content = $content . ' Dear Boss, ';

    // Project Type = No DP and Kind = 1 or 2 and Amount <= 10萬
        if($special == "sn" && ($kind == 1 || $kind == 2) && $final_amount <= 100000)
        $content = $content . ' Dear Glendon, ';

    // Project Type = No DP and Kind = 1 or 2 and Amount > 10萬
    if($special == "sn" && ($kind == 1 || $kind == 2) && $final_amount > 100000)
        $content = $content . ' Dear Glendon, ';


    $content = $content . ' </td>
                        </tr>
                        <tr>
                            <td style="font-size: 20px; padding: 0 0 20px 5px; text-align: justify;">';
    $content = $content . " Just a quick reminder that " . $leaver . " has submitted payment proof. Please check details below:";
    $content = $content . '</td>
                        </tr>
                        </tbody>
                    </table>
                    <table style="width: 100%">
                        <tbody>
                        <tr>
                            <td style="background-color: #C3F69D99; border: 2px solid #FFFFFF; padding: 8px; font-size: 14px; width: 280px; font-weight: 600;">
                                <eng style="font-size: 16px;">
                                    Project Name
                                </eng>
                            </td>
                            <td style="background-color: #C3F69D99; border: 2px solid #FFFFFF; padding: 8px; width: 440px; font-size: 16px;">';
    $content = $content . " " . $projectname . " ";
    $content = $content . '</td>
                        </tr>
                        <tr>
                            <td style="background-color: #C3F69D99; border: 2px solid #FFFFFF; padding: 8px; font-size: 14px; width: 280px; font-weight: 600;">
                                <eng style="font-size: 16px;">
                                    Project Category
                                </eng>
                            </td>
                        <td style="background-color: #C3F69D99; border: 2px solid #FFFFFF; padding: 8px; width: 440px; font-size: 16px;">';
    //$content = $content . " " . $category == '1' ? "Office Systems" : "Lighting" . " ";
    if($category == '1')
        $content = $content . " " . "Office Systems" . " ";
    else
        $content = $content . " " . "Lighting" . " ";
    $content = $content . '</td>
                        </tr>
                        <tr>
                            <td style="background-color: #C3F69D99; border: 2px solid #FFFFFF; padding: 8px; font-size: 14px; width: 280px; font-weight: 600;">
                                <eng style="font-size: 16px;">
                                    Submission Time
                                </eng>
                            </td>
                        <td style="background-color: #C3F69D99; border: 2px solid #FFFFFF; padding: 8px; width: 440px; font-size: 16px;">';
    $content = $content . " " . $subtime . " ";
    $content = $content . '</td>
                        </tr>
                        <tr>
                            <td style="background-color: #C3F69D99; border: 2px solid #FFFFFF; padding: 8px; font-size: 14px; width: 280px; font-weight: 600;">
                                <eng style="font-size: 16px;">
                                    Submitter
                                </eng>
                            </td>
                            <td style="background-color: #C3F69D99; border: 2px solid #FFFFFF; padding: 8px; width: 440px; font-size: 16px;">';
    $content = $content . " " . $leaver . " ";
    $content = $content . '</td>
                        </tr>
                        <tr>
                            <td style="background-color: #C3F69D99; border: 2px solid #FFFFFF; padding: 8px; font-size: 14px; width: 280px; font-weight: 600;">
                                <eng style="font-size: 16px;">
                                    Type
                                </eng>
                            </td>
                            <td style="background-color: #C3F69D99; border: 2px solid #FFFFFF; padding: 8px; width: 440px; font-size: 16px;">';
    $content = $content . " " . $pay . " ";
    $content = $content . '</td>
                        </tr>
                        <tr>
                            <td style="background-color: #C3F69D99; border: 2px solid #FFFFFF; padding: 8px; font-size: 14px; width: 280px; font-weight: 600;">
                                <eng style="font-size: 16px;">
                                    Remarks
                                </eng>
                            </td>
                            <td style="background-color: #C3F69D99; border: 2px solid #FFFFFF; padding: 8px; width: 440px; font-size: 16px;">';
    $content = $content . " " . $remark . " ";

    $content = $content . '</td>
                        </tr>
                        </tbody>
                    </table>
                    <hr style="margin-top: 45px;">
                    <table style="width: 100%;">
                        <tbody>
                        <tr>
                            <td style="font-size: 16px; padding: 5px 0 0 5px; line-height: 1.5;">
                                Please log on to Feliix >> Admin Section >> Verify and Review to review the downpayment proof.<br>';
    $content = $content . 'URL:  <a href="' . $conf::$mail_ip . '">' . $conf::$mail_ip . '</a> ';
    $content = $content . '</td>
                        </tr>
                        </tbody>
                    </table>
                    </div>
                    </body>
                    </html>';

    $mail->MsgHTML($content);
    if($mail->Send()) {
        logMail($email1, $content);
        return true;
//        echo "Error while sending Email.";
//        var_dump($mail);
    } else {
        logMail($email1, $mail->ErrorInfo . $content);
        return false;
//        echo "Email sent successfully";
    }

}

function order_notification($name, $access,  $access_cc, $project_name, $serial_name, $order_name, $order_type, $remark, $action, $items, $od_id)
{
    $conf = new Conf();

    $mail = new PHPMailer();
    $mail->IsSMTP();
    $mail->Mailer = "smtp";
    $mail->CharSet = 'UTF-8';
    $mail->Encoding = 'base64';

    // $mail->SMTPDebug  = 0;
    // $mail->SMTPAuth   = true;
    // $mail->SMTPSecure = "ssl";
    // $mail->Port       = 465;
    // $mail->SMTPKeepAlive = true;
    // $mail->Host       = $conf::$mail_host;
    // $mail->Username   = $conf::$mail_username;
    // $mail->Password   = $conf::$mail_password;

    $mail = SetupMail($mail, $conf);


    $mail->IsHTML(true);

    $receiver = "";
    $cc = "";

    $_list = explode(",", $access);
    foreach($_list as &$c_list)
    {
        $notifior = GetAccessNotifiers($c_list, $serial_name);
        foreach($notifior as &$list)
        {
            if($action == 'finish_notes')
            {
                if($list["username"] == 'Nestor Rosales')
                {
                    $receiver .= "Aiza Eisma" . ", ";
                    $mail->AddAddress("aiza@feliix.com", "Aiza Eisma");
                }
                else{
                    if($list["username"] == 'Cristina Matining'){
                        $receiver .= "Alleah Belmonte" . ", ";
                        $mail->AddAddress("alleah.feliix@gmail.com", "Alleah Belmonte");
                    }
                    else{
                        $receiver .= $list["username"] . ", ";
                        $mail->AddAddress($list["email"], $list["username"]);
                    }
                }
            }
            else{
                if($action == 'reject')
                {
                    if($list["username"] == 'Nestor Rosales')
                    {
                        $receiver .= "Aiza Eisma" . ", ";
                        $mail->AddAddress("aiza@feliix.com", "Aiza Eisma");
                        $receiver .= $list["username"] . ", ";
                        $mail->AddAddress($list["email"], $list["username"]);
                    }
                    else{
                        if($list["username"] == 'Cristina Matining'){
                            $receiver .= "Alleah Belmonte" . ", ";
                            $mail->AddAddress("alleah.feliix@gmail.com", "Alleah Belmonte");
                            $receiver .= $list["username"] . ", ";
                            $mail->AddAddress($list["email"], $list["username"]);
                        }
                        else{
                            $receiver .= $list["username"] . ", ";
                            $mail->AddAddress($list["email"], $list["username"]);
                        }
                    }
                }
                else{
                    $receiver .= $list["username"] . ", ";
                    $mail->AddAddress($list["email"], $list["username"]);
                }
            }
        }
    }

    $receiver = rtrim($receiver, ", ");

    // explore cc into array
    $cc_list = explode(",", $access_cc);
    foreach($cc_list as &$c_list)
    {
        $notifior = GetAccessNotifiers($c_list, $serial_name);
        foreach($notifior as &$list)
        {
            if($action == 'send_note' || $action == 'withdraw_note_tw')
            {
                if($list["username"] == 'Nestor Rosales')
                {
                    $cc .= "Aiza Eisma" . ", ";
                    $mail->AddCC("aiza@feliix.com", "Aiza Eisma");
                }
                else{
                    if($list["username"] == 'Cristina Matining'){
                        $cc .= "Alleah Belmonte" . ", ";;
                        $mail->AddCC("alleah.feliix@gmail.com", "Alleah Belmonte");
                    }
                    else{
                        $cc .= $list["username"];
                        $mail->AddCC($list["email"], $list["username"]);
                    }
                }
            }
            else{
                if($action == 'approval' || $action == 'approved' || $action == 'withdraw')
                {
                    if($list["username"] == 'Nestor Rosales')
                    {
                        $cc .= "Aiza Eisma" . ", ";
                        $mail->AddCC("aiza@feliix.com", "Aiza Eisma");
                        $cc .= $list["username"];
                        $mail->AddCC($list["email"], $list["username"]);
                    }
                    else{
                        if($list["username"] == 'Cristina Matining'){
                            $cc .= "Alleah Belmonte";
                            $mail->AddCC("alleah.feliix@gmail.com", "Alleah Belmonte");
                            $cc .= $list["username"];
                            $mail->AddCC($list["email"], $list["username"]);
                        }
                        else{
                            $cc .= $list["username"];
                            $mail->AddCC($list["email"], $list["username"]);
                        }
                    }
                }
                else{
                    $cc .= $list["username"];
                    $mail->AddCC($list["email"], $list["username"]);
                }
            }
        }
    }


    $mail->SetFrom("feliix.it@gmail.com", "Feliix.System");
    $mail->AddReplyTo("feliix.it@gmail.com", "Feliix.System");

    $mail->Subject = "";

    $header = "";
    $url = "";

    // preliminary
    if($action == 'send_note')
    {
        $mail->Subject = 'Items of "' . $order_type . ': ' . $serial_name . '" need your feedback';
        $header = 'Items of "' . $order_type . ': ' . $serial_name . '" need your feedback. Please check details below:';
        $url = "https://feliix.myvnc.com/order_taiwan_p1?id=" . $od_id;
    }

    if($action == 'withdraw_note_tw')
    {
        $mail->Subject = 'Request for feedback was withdrawn on items of "' . $order_type . ': ' . $serial_name . '" ';
        $header = 'The request for your feedback was withdrawn on items of  "' . $order_type . ': ' . $serial_name . '". Please check details below:';
        $url = "https://feliix.myvnc.com/order_taiwan_p1?id=" . $od_id;
    }

    if($action == 'approval')
    {
        $mail->Subject = 'Items of "' . $order_type . ': ' . $serial_name . '" need your approval';
        $header = 'Items of "' . $order_type . ': ' . $serial_name . '" need your approval. Please check details below:';
        $url = "https://feliix.myvnc.com/order_taiwan_p2?id=" . $od_id;
    }

    if($action == 'finish_notes')
    {
        $mail->Subject = 'Taiwan office already provided feedback for items of "' . $order_type . ': ' . $serial_name . '"';
        $header = 'Taiwan office already provided feedback for items of "' . $order_type . ': ' . $serial_name . '". Please check details below:';
        $url = "https://feliix.myvnc.com/order_taiwan_p1?id=" . $od_id;
    }

    // for approve
    if($action == 'approved')
    {
        $mail->Subject = 'Items of "' . $order_type . ': ' . $serial_name . '" were approved';
        $header = 'Items of "' . $order_type . ': ' . $serial_name . '" were already approved and need you to follow. Please check details below:';
        $url = "https://feliix.myvnc.com/order_taiwan_p3?id=" . $od_id;
    }

    if($action == 'reject')
    {
        $mail->Subject = 'Items of "' . $order_type . ': ' . $serial_name . '" were rejected';
        $header = 'Items of "' . $order_type . ': ' . $serial_name . '" were already rejected and need you to follow. Please check details below:';
        $url = "https://feliix.myvnc.com/order_taiwan_p1?id=" . $od_id;
    }

    if($action == 'withdraw')
    {
        $mail->Subject = 'Request for approval was withdrawn on items of "' . $order_type . ': ' . $serial_name . '" ';
        $header = 'The request for your approval was withdrawn on items of  "' . $order_type . ': ' . $serial_name . '". Please check details below:';
        $url = "https://feliix.myvnc.com/order_taiwan_p1?id=" . $od_id;
    }

    // APPROVED
    if($action == 'ordered')
    {
        $receiver = "All";
        $mail->Subject = 'Items of "' . $order_type . ': ' . $serial_name . '" are ordered';
        $header = 'Items of "' . $order_type . ': ' . $serial_name . '" are ordered. Please check details below:';
        $url = "https://feliix.myvnc.com/order_taiwan_p3?id=" . $od_id;
    }

    // Canceled
    if($action == 'canceled')
    {
        $receiver = "All";
        $mail->Subject = 'Items of "' . $order_type . ': ' . $serial_name . '" are canceled';
        $header = 'Items of "' . $order_type . ': ' . $serial_name . '" are canceled. Please check details below:';
        $url = "https://feliix.myvnc.com/order_taiwan_p3?id=" . $od_id;
    }

    if($action == 'ship_info')
    {
        // $receiver = "All";
        $mail->Subject = 'Shipping info for items of "' . $order_type . ': ' . $serial_name . '" is updated';
        $header = 'Shipping info for items of "' . $order_type . ': ' . $serial_name . '" is updated. Please check details below:';
        $url = "https://feliix.myvnc.com/order_taiwan_p3?id=" . $od_id;
    }

    if($action == 'ware_info')
    {
        // $receiver = "All";
        $mail->Subject = 'Warehouse info for items of "' . $order_type . ': ' . $serial_name . '" is updated';
        $header = 'Warehouse info for items of "' . $order_type . ': ' . $serial_name . '" is updated. Please check details below:';
        $url = "https://feliix.myvnc.com/order_taiwan_p3?id=" . $od_id;
    }

    if($action == 'batch')
    {
        $name = "System";
        $mail->Subject = 'Shipping info for items of "' . $order_type . ': ' . $serial_name . '" is updated';
        $header = 'Shipping info for items of "' . $order_type . ': ' . $serial_name . '" is updated. Please check details below:';
        $url = "https://feliix.myvnc.com/order_taiwan_p3?id=" . $od_id;

    }

    $content = '<!DOCTYPE html>
    <html lang="en">
    <head>
        <meta http-equiv="content-type" content="text/html; charset=utf-8"/>
    </head>
    <body>

    <div style="width: 766px; padding: 25px 70px 20px 70px; border: 2px solid rgb(230,230,230); color: black;">

        <table style="width: 100%;">
            <tbody>
            <tr>
                <td style="font-size: 16px; padding: 10px 0 10px 5px;">';
                    $content = $content . "Dear " . $receiver . ",";
                    $content = $content . '
                </td>
            </tr>
            <tr>
                <td style="font-size: 16px; padding: 0 0 20px 5px; text-align: justify;">';
                    $content = $content . $header;
                    $content = $content . '
                </td>
            </tr>

            <tr>
                <td style="font-size: 15px; padding: 0 0 5px 15px; text-align: justify; line-height: 1.8;">';
                    $content = $content . 'Order Type: ' . $order_type . '<br>';
                    $content = $content . 'Order Name: ' . $serial_name . ' ' . $order_name . '<br>';
                    $content = $content . 'Related Project: ' . $project_name . '<br>';
                    $content = $content . 'Submission Time: ' . date('Y/m/d h:i:s a', time()) . '<br>';
                    $content = $content . 'Submitter: ' . $name . '<br>';
                    $content = $content . 'Comment: ' . $remark . '';
                    $content = $content . '
                </td>
            </tr>
            </tbody>
        </table>
        <table style="margin-left: 15px; width: 96%;">
            <tbody>
            <tr>
                <td colspan="2"
                    style="background-color: #DFEAEA; border: 2px solid #94BABB; border-bottom: 1px solid #94BABB; padding: 8px; font-size: 14px; font-weight: 600; text-align: center; border-top-left-radius: 9px; border-top-right-radius: 9px;">
                    Affected Items
                </td>
            </tr>
            <tr>
                <td style="border-left: 2px solid #94BABB; border-bottom: 1px solid #94BABB; padding: 8px; font-size: 14px; font-weight: 600; text-align: center; width: 280px;">
                    #
                </td>

                <td style="border-left: 1px solid #94BABB; border-bottom: 1px solid #94BABB; border-right: 2px solid #94BABB; padding: 8px; font-size: 14px; font-weight: 600; text-align: center; width: 440px;">
                    Product Code
                </td>
            </tr>
            ';

            /* 除了最後一個產品的其他產品 */
            $i = 0;
            for($i=0; $i<count($items)-1; $i++)
            {
                $content = $content . '
                <tr>
                    <td style="border-left: 2px solid #94BABB; border-bottom: 1px solid #94BABB; padding: 8px; font-size: 14px; text-align: center; width: 280px;">
                        ';
                        $content = $content . $items[$i]['serial_number'] . '';
                        $content = $content . '
                    </td>
                    <td style="border-left: 1px solid #94BABB; border-bottom: 1px solid #94BABB; border-right: 2px solid #94BABB; padding: 8px; font-size: 14px; text-align: center; width: 440px;">
                        ';
                        $content = $content . $items[$i]['code'] . '';
                        $content = $content . '
                    </td>
                </tr>
                ';
            }

            /* 最後一個產品 */
            $content = $content . '
            <tr>
                <td style="border-left: 2px solid #94BABB; border-bottom: 2px solid #94BABB; padding: 8px; font-size: 14px; text-align: center; width: 280px; border-bottom-left-radius: 9px;">
                    ';
                    $content = $content . $items[$i]['serial_number']  . '';
                    $content = $content . '
                </td>
                <td style="border-left: 1px solid #94BABB; border-bottom: 2px solid #94BABB; border-right: 2px solid #94BABB; padding: 8px; font-size: 14px; text-align: center; width: 440px; border-bottom-right-radius: 9px;">
                    ';
                    $content = $content . $items[$i]['code'] . '';
                    $content = $content . '
                </td>
            </tr>
            ';


        $content = $content . '
            </tbody>
                </table>
                <hr style="margin-top: 45px;">
                <table style="width: 100%;">
                    <tbody>
                    <tr>
                        <td style="font-size: 16px; padding: 5px 0 0 5px; line-height: 1.5;">
                            Please click this link to view the target webpage: ';
                            $content = $content . '<a href="' . $url . '">' . $url . '</a> ';
                            $content = $content . '
                        </td>
                    </tr>
                    </tbody>
                </table>
            </div>
            </body>
            </html>';

    $mail->MsgHTML($content);
    if($mail->Send()) {
        logMail($receiver, $content);
        return true;
//        echo "Error while sending Email.";
//        var_dump($mail);
    } else {
        logMail($receiver, $mail->ErrorInfo . $content);
        return false;
//        echo "Email sent successfully";
    }

}

function mockup_notification($name, $access,  $access_cc, $project_name, $serial_name, $order_name, $order_type, $remark, $action, $items, $od_id)
{
    $conf = new Conf();

    $mail = new PHPMailer();
    $mail->IsSMTP();
    $mail->Mailer = "smtp";
    $mail->CharSet = 'UTF-8';
    $mail->Encoding = 'base64';

    // $mail->SMTPDebug  = 0;
    // $mail->SMTPAuth   = true;
    // $mail->SMTPSecure = "ssl";
    // $mail->Port       = 465;
    // $mail->SMTPKeepAlive = true;
    // $mail->Host       = $conf::$mail_host;
    // $mail->Username   = $conf::$mail_username;
    // $mail->Password   = $conf::$mail_password;

    $mail = SetupMail($mail, $conf);


    $mail->IsHTML(true);
    
    $receiver = "";
    $cc = "";

    $_list = explode(",", $access);
    foreach($_list as &$c_list)
    {
        $notifior = GetAccessNotifiers($c_list, $serial_name);
        foreach($notifior as &$list)
        {
            $receiver .= $list["username"] . ", ";
            $mail->AddAddress($list["email"], $list["username"]);
        }
    }

    $receiver = rtrim($receiver, ", ");

    // explore cc into array
    $cc_list = explode(",", $access_cc);
    foreach($cc_list as &$c_list)
    {
        $notifior = GetAccessNotifiers($c_list, $serial_name);
        foreach($notifior as &$list)
        {
            $cc = $list["username"];
            $mail->AddCC($list["email"], $list["username"]);
        }
    }
    

    $mail->SetFrom("feliix.it@gmail.com", "Feliix.System");
    $mail->AddReplyTo("feliix.it@gmail.com", "Feliix.System");

    $mail->Subject = "";

    $header = "";
    $url = "";

    // preliminary
    if($action == 'send_note')
    {
        $mail->Subject = 'Items of "' . $order_type . ': ' . $serial_name . '" need your feedback';
        $header = 'Items of "' . $order_type . ': ' . $serial_name . '" need your feedback. Please check details below:';
        $url = "https://feliix.myvnc.com/order_taiwan_mockup_p1?id=" . $od_id;
    }
        
    if($action == 'withdraw_note_tw')
    {
        $mail->Subject = 'Request for feedback was withdrawn on items of "' . $order_type . ': ' . $serial_name . '" ';
        $header = 'The request for your feedback was withdrawn on items of  "' . $order_type . ': ' . $serial_name . '". Please check details below:';
        $url = "https://feliix.myvnc.com/order_taiwan_mockup_p1?id=" . $od_id;
    }

    if($action == 'approval')
    {
        $mail->Subject = 'Items of "' . $order_type . ': ' . $serial_name . '" need your approval';
        $header = 'Items of "' . $order_type . ': ' . $serial_name . '" need your approval. Please check details below:';
        $url = "https://feliix.myvnc.com/order_taiwan_mockup_p2?id=" . $od_id;
    }

    if($action == 'finish_notes')
    {
        $mail->Subject = 'Taiwan office already provided feedback for items of "' . $order_type . ': ' . $serial_name . '"';
        $header = 'Taiwan office already provided feedback for items of "' . $order_type . ': ' . $serial_name . '". Please check details below:';
        $url = "https://feliix.myvnc.com/order_taiwan_mockup_p1?id=" . $od_id;
    }

    // for approve
    if($action == 'approved')
    {
        $mail->Subject = 'Items of "' . $order_type . ': ' . $serial_name . '" were approved';
        $header = 'Items of "' . $order_type . ': ' . $serial_name . '" were already approved and need you to follow. Please check details below:';
        $url = "https://feliix.myvnc.com/order_taiwan_mockup_p3?id=" . $od_id;
    }

    if($action == 'reject')
    {
        $mail->Subject = 'Items of "' . $order_type . ': ' . $serial_name . '" were rejected';
        $header = 'Items of "' . $order_type . ': ' . $serial_name . '" were already rejected and need you to follow. Please check details below:';
        $url = "https://feliix.myvnc.com/order_taiwan_mockup_p1?id=" . $od_id;
    }
    
    if($action == 'withdraw')
    {
        $mail->Subject = 'Request for approval was withdrawn on items of "' . $order_type . ': ' . $serial_name . '" ';
        $header = 'The request for your approval was withdrawn on items of  "' . $order_type . ': ' . $serial_name . '". Please check details below:';
        $url = "https://feliix.myvnc.com/order_taiwan_mockup_p1?id=" . $od_id;
    }

    // APPROVED
    if($action == 'ordered')
    {
        $receiver = "All";
        $mail->Subject = 'Items of "' . $order_type . ': ' . $serial_name . '" are ordered';
        $header = 'Items of "' . $order_type . ': ' . $serial_name . '" are ordered. Please check details below:';
        $url = "https://feliix.myvnc.com/order_taiwan_mockup_p3?id=" . $od_id;
    }

    // Canceled
    if($action == 'canceled')
    {
        $receiver = "All";
        $mail->Subject = 'Items of "' . $order_type . ': ' . $serial_name . '" are canceled';
        $header = 'Items of "' . $order_type . ': ' . $serial_name . '" are canceled. Please check details below:';
        $url = "https://feliix.myvnc.com/order_taiwan_mockup_p3?id=" . $od_id;
    }

    if($action == 'ship_info')
    {
        // $receiver = "All";
        $mail->Subject = 'Shipping info for items of "' . $order_type . ': ' . $serial_name . '" is updated';
        $header = 'Shipping info for items of "' . $order_type . ': ' . $serial_name . '" is updated. Please check details below:';
        $url = "https://feliix.myvnc.com/order_taiwan_mockup_p3?id=" . $od_id;
    }

    if($action == 'ware_info')
    {
        // $receiver = "All";
        $mail->Subject = 'Warehouse info for items of "' . $order_type . ': ' . $serial_name . '" is updated';
        $header = 'Warehouse info for items of "' . $order_type . ': ' . $serial_name . '" is updated. Please check details below:';
        $url = "https://feliix.myvnc.com/order_taiwan_mockup_p3?id=" . $od_id;
    }

    if($action == 'batch')
    {
        $name = "System";
        $mail->Subject = 'Shipping info for items of "' . $order_type . ': ' . $serial_name . '" is updated';
        $header = 'Shipping info for items of "' . $order_type . ': ' . $serial_name . '" is updated. Please check details below:';
        $url = "https://feliix.myvnc.com/order_taiwan_mockup_p3?id=" . $od_id;
        
    }

    $content = '<!DOCTYPE html>
    <html lang="en">
    <head>
        <meta http-equiv="content-type" content="text/html; charset=utf-8"/>
    </head>
    <body>

    <div style="width: 766px; padding: 25px 70px 20px 70px; border: 2px solid rgb(230,230,230); color: black;">

        <table style="width: 100%;">
            <tbody>
            <tr>
                <td style="font-size: 16px; padding: 10px 0 10px 5px;">';
                    $content = $content . "Dear " . $receiver . ",";
                    $content = $content . '
                </td>
            </tr>
            <tr>
                <td style="font-size: 16px; padding: 0 0 20px 5px; text-align: justify;">';
                    $content = $content . $header;
                    $content = $content . '
                </td>
            </tr>

            <tr>
                <td style="font-size: 15px; padding: 0 0 5px 15px; text-align: justify; line-height: 1.8;">';
                    $content = $content . 'Order Type: ' . $order_type . '<br>';
                    $content = $content . 'Order Name: ' . $serial_name . ' ' . $order_name . '<br>';
                    $content = $content . 'Related Project: ' . $project_name . '<br>';
                    $content = $content . 'Submission Time: ' . date('Y/m/d h:i:s a', time()) . '<br>';
                    $content = $content . 'Submitter: ' . $name . '<br>';
                    $content = $content . 'Comment: ' . $remark . '';
                    $content = $content . '
                </td>
            </tr>
            </tbody>
        </table>
        <table style="margin-left: 15px; width: 96%;">
            <tbody>
            <tr>
                <td colspan="2"
                    style="background-color: #DFEAEA; border: 2px solid #94BABB; border-bottom: 1px solid #94BABB; padding: 8px; font-size: 14px; font-weight: 600; text-align: center; border-top-left-radius: 9px; border-top-right-radius: 9px;">
                    Affected Items
                </td>
            </tr>
            <tr>
                <td style="border-left: 2px solid #94BABB; border-bottom: 1px solid #94BABB; padding: 8px; font-size: 14px; font-weight: 600; text-align: center; width: 280px;">
                    #
                </td>

                <td style="border-left: 1px solid #94BABB; border-bottom: 1px solid #94BABB; border-right: 2px solid #94BABB; padding: 8px; font-size: 14px; font-weight: 600; text-align: center; width: 440px;">
                    Product Code
                </td>
            </tr>
            ';

            /* 除了最後一個產品的其他產品 */
            $i = 0;
            for($i=0; $i<count($items)-1; $i++)
            {
                $content = $content . '
                <tr>
                    <td style="border-left: 2px solid #94BABB; border-bottom: 1px solid #94BABB; padding: 8px; font-size: 14px; text-align: center; width: 280px;">
                        ';
                        $content = $content . $items[$i]['serial_number'] . '';
                        $content = $content . '
                    </td>
                    <td style="border-left: 1px solid #94BABB; border-bottom: 1px solid #94BABB; border-right: 2px solid #94BABB; padding: 8px; font-size: 14px; text-align: center; width: 440px;">
                        ';
                        $content = $content . $items[$i]['code'] . '';
                        $content = $content . '
                    </td>
                </tr>
                ';
            }

            /* 最後一個產品 */
            $content = $content . '
            <tr>
                <td style="border-left: 2px solid #94BABB; border-bottom: 2px solid #94BABB; padding: 8px; font-size: 14px; text-align: center; width: 280px; border-bottom-left-radius: 9px;">
                    ';
                    $content = $content . $items[$i]['serial_number']  . '';
                    $content = $content . '
                </td>
                <td style="border-left: 1px solid #94BABB; border-bottom: 2px solid #94BABB; border-right: 2px solid #94BABB; padding: 8px; font-size: 14px; text-align: center; width: 440px; border-bottom-right-radius: 9px;">
                    ';
                    $content = $content . $items[$i]['code'] . '';
                    $content = $content . '
                </td>
            </tr>
            ';


        $content = $content . '
            </tbody>
                </table>
                <hr style="margin-top: 45px;">
                <table style="width: 100%;">
                    <tbody>
                    <tr>
                        <td style="font-size: 16px; padding: 5px 0 0 5px; line-height: 1.5;">
                            Please click this link to view the target webpage: ';
                            $content = $content . '<a href="' . $url . '">' . $url . '</a> ';
                            $content = $content . '
                        </td>
                    </tr>
                    </tbody>
                </table>
            </div>
            </body>
            </html>';

    $mail->MsgHTML($content);
    if($mail->Send()) {
        logMail($receiver, $content);
        return true;
//        echo "Error while sending Email.";
//        var_dump($mail);
    } else {
        logMail($receiver, $mail->ErrorInfo . $content);
        return false;
//        echo "Email sent successfully";
    }

}


function inquiry_partial_complete_notification($name, $access,  $access_cc, $project_name, $serial_name, $order_name, $order_type, $remark, $action, $items, $od_id, $task_type)
{
    $conf = new Conf();

    $mail = new PHPMailer();
    $mail->IsSMTP();
    $mail->Mailer = "smtp";
    $mail->CharSet = 'UTF-8';
    $mail->Encoding = 'base64';

    // $mail->SMTPDebug  = 0;
    // $mail->SMTPAuth   = true;
    // $mail->SMTPSecure = "ssl";
    // $mail->Port       = 465;
    // $mail->SMTPKeepAlive = true;
    // $mail->Host       = $conf::$mail_host;
    // $mail->Username   = $conf::$mail_username;
    // $mail->Password   = $conf::$mail_password;

    $mail = SetupMail($mail, $conf);


    $mail->IsHTML(true);
    
    $receiver = "";
    $cc = "";

    $_list = explode(",", $access);
    foreach($_list as &$c_list)
    {
        $notifior = GetNotifiers($c_list, $serial_name);
        foreach($notifior as &$list)
        {
            $receiver .= $list["username"] . ", ";
            $mail->AddAddress($list["email"], $list["username"]);
        }
    }

    $receiver = rtrim($receiver, ", ");

    // explore cc into array
    $cc_list = explode(",", $access_cc);
    foreach($cc_list as &$c_list)
    {
        $notifior = GetNotifiers($c_list, $serial_name);
        foreach($notifior as &$list)
        {
            $cc = $list["username"];
            $mail->AddCC($list["email"], $list["username"]);
        }
    }
    

    $mail->SetFrom("feliix.it@gmail.com", "Feliix.System");
    $mail->AddReplyTo("feliix.it@gmail.com", "Feliix.System");

    $mail->Subject = "";

    $header = "";
    $url = "";

    
    $mail->Subject = 'Taiwan office first provided partial feedback for Inquiry "' . $serial_name . '".';
    $header = 'Taiwan office first provided partial feedback for Inquiry "' . $serial_name . '" . Taiwan office needs more time to collect the supplier\'s response for the rest of the current inquiry. Below is the details of the current inquiry:';
    $url = "https://feliix.myvnc.com/inquiry_taiwan?id=" . $od_id;



    $content = '<!DOCTYPE html>
    <html lang="en">
    <head>
        <meta http-equiv="content-type" content="text/html; charset=utf-8"/>
    </head>
    <body>

    <div style="width: 766px; padding: 25px 70px 20px 70px; border: 2px solid rgb(230,230,230); color: black;">

        <table style="width: 100%;">
            <tbody>
            <tr>
                <td style="font-size: 16px; padding: 10px 0 10px 5px;">';
                    $content = $content . "Dear " . $receiver . ",";
                    $content = $content . '
                </td>
            </tr>
            <tr>
                <td style="font-size: 16px; padding: 0 0 20px 5px; text-align: justify;">';
                    $content = $content . $header;
                    $content = $content . '
                </td>
            </tr>

            <tr>
                <td style="font-size: 15px; padding: 0 0 5px 15px; text-align: justify; line-height: 1.8;">';
                    $content = $content . 'Inquiry Name: ' . $serial_name . ' ' . $order_name . '<br>';

                    if($task_type == "")
                        $content = $content . 'Related Project: ' . $project_name . '<br>';
                    else
                        $content = $content . 'Task Management of ' . $task_type . ' Department<br>';

                    $content = $content . 'Submission Time: ' . date('Y/m/d h:i:s a', time()) . '<br>';
                    $content = $content . 'Submitter: ' . $name . '<br>';
                    $content = $content . 'Comment: ' . $remark . '';
                    $content = $content . '
                </td>
            </tr>
            </tbody>
        </table>
       
            ';


        $content = $content . '
            
                <hr style="margin-top: 45px;">
                <table style="width: 100%;">
                    <tbody>
                    <tr>
                        <td style="font-size: 16px; padding: 5px 0 0 5px; line-height: 1.5;">
                            Please click this link to view the target webpage: ';
                            $content = $content . '<a href="' . $url . '">' . $url . '</a> ';
                            $content = $content . '
                        </td>
                    </tr>
                    </tbody>
                </table>
            </div>
            </body>
            </html>';

    $mail->MsgHTML($content);
    if($mail->Send()) {
        logMail($receiver, $content);
        return true;
//        echo "Error while sending Email.";
//        var_dump($mail);
    } else {
        logMail($receiver, $mail->ErrorInfo . $content);
        return false;
//        echo "Email sent successfully";
    }

}

function inquiry_notification($name, $access,  $access_cc, $project_name, $serial_name, $order_name, $order_type, $remark, $action, $items, $od_id, $task_type)
{
    $conf = new Conf();

    $mail = new PHPMailer();
    $mail->IsSMTP();
    $mail->Mailer = "smtp";
    $mail->CharSet = 'UTF-8';
    $mail->Encoding = 'base64';

    // $mail->SMTPDebug  = 0;
    // $mail->SMTPAuth   = true;
    // $mail->SMTPSecure = "ssl";
    // $mail->Port       = 465;
    // $mail->SMTPKeepAlive = true;
    // $mail->Host       = $conf::$mail_host;
    // $mail->Username   = $conf::$mail_username;
    // $mail->Password   = $conf::$mail_password;

    $mail = SetupMail($mail, $conf);


    $mail->IsHTML(true);
    
    $receiver = "";
    $cc = "";

    $_list = explode(",", $access);
    foreach($_list as &$c_list)
    {
        $notifior = GetNotifiers($c_list, $serial_name);
        foreach($notifior as &$list)
        {
            $receiver .= $list["username"] . ", ";
            $mail->AddAddress($list["email"], $list["username"]);
        }
    }

    $receiver = rtrim($receiver, ", ");

    // explore cc into array
    $cc_list = explode(",", $access_cc);
    foreach($cc_list as &$c_list)
    {
        $notifior = GetNotifiers($c_list, $serial_name);
        foreach($notifior as &$list)
        {
            $cc = $list["username"];
            $mail->AddCC($list["email"], $list["username"]);
        }
    }
    

    $mail->SetFrom("feliix.it@gmail.com", "Feliix.System");
    $mail->AddReplyTo("feliix.it@gmail.com", "Feliix.System");

    $mail->Subject = "";

    $header = "";
    $url = "";

    // preliminary
    if($action == 'send_note')
    {
        $receiver = "Ariel Lin";
        $mail->Subject = '[Inquiry Notification] Inquiry "' . $serial_name . '" needs your feedback';
        $header = 'Inquiry "' . $serial_name . '" needs your feedback. Please check details below:';
        $url = "https://feliix.myvnc.com/inquiry_taiwan?id=" . $od_id;
    }
        
    if($action == 'withdraw_note_tw')
    {
        $receiver = "Ariel Lin";
        $mail->Subject = '[Inquiry Notification] Request for Inquiry "' . $serial_name . '" was withdrawn';
        $header = 'The request for inquiry "' . $serial_name . '" was withdrawn. Please check details below:';
        $url = "https://feliix.myvnc.com/inquiry_taiwan?id=" . $od_id;
    }

    if($action == 'finish_notes')
    {
        
        $mail->Subject = '[Inquiry Notification] Taiwan office already provided feedback for Inquiry "' . $serial_name . '".';
        $header = 'Taiwan office already provided feedback for Inquiry "' . $serial_name . '". Please check details below:';
        $url = "https://feliix.myvnc.com/inquiry_taiwan?id=" . $od_id;
    }


    $content = '<!DOCTYPE html>
    <html lang="en">
    <head>
        <meta http-equiv="content-type" content="text/html; charset=utf-8"/>
    </head>
    <body>

    <div style="width: 766px; padding: 25px 70px 20px 70px; border: 2px solid rgb(230,230,230); color: black;">

        <table style="width: 100%;">
            <tbody>
            <tr>
                <td style="font-size: 16px; padding: 10px 0 10px 5px;">';
                    $content = $content . "Dear " . $receiver . ",";
                    $content = $content . '
                </td>
            </tr>
            <tr>
                <td style="font-size: 16px; padding: 0 0 20px 5px; text-align: justify;">';
                    $content = $content . $header;
                    $content = $content . '
                </td>
            </tr>

            <tr>
                <td style="font-size: 15px; padding: 0 0 5px 15px; text-align: justify; line-height: 1.8;">';
                    $content = $content . 'Inquiry Name: ' . $serial_name . ' ' . $order_name . '<br>';

                    if($task_type == "")
                        $content = $content . 'Related Project: ' . $project_name . '<br>';
                    else
                        $content = $content . 'Task Management of ' . $task_type . ' Department<br>';

                    $content = $content . 'Submission Time: ' . date('Y/m/d h:i:s a', time()) . '<br>';
                    $content = $content . 'Submitter: ' . $name . '<br>';
                    $content = $content . 'Comment: ' . $remark . '';
                    $content = $content . '
                </td>
            </tr>
            </tbody>
        </table>
       
            ';


        $content = $content . '
            
                <hr style="margin-top: 45px;">
                <table style="width: 100%;">
                    <tbody>
                    <tr>
                        <td style="font-size: 16px; padding: 5px 0 0 5px; line-height: 1.5;">
                            Please click this link to view the target webpage: ';
                            $content = $content . '<a href="' . $url . '">' . $url . '</a> ';
                            $content = $content . '
                        </td>
                    </tr>
                    </tbody>
                </table>
            </div>
            </body>
            </html>';

    $mail->MsgHTML($content);
    if($mail->Send()) {
        logMail($receiver, $content);
        return true;
//        echo "Error while sending Email.";
//        var_dump($mail);
    } else {
        logMail($receiver, $mail->ErrorInfo . $content);
        return false;
//        echo "Email sent successfully";
    }

}

function knowledge_add_notification($name, $name_at, $access, $access_cc, $title, $creator, $created_at, $category, $view_type, $duration, $watch, $od_id, $action)
{
    $conf = new Conf();

    $mail = new PHPMailer();
    $mail->IsSMTP();
    $mail->Mailer = "smtp";
    $mail->CharSet = 'UTF-8';
    $mail->Encoding = 'base64';

    // $mail->SMTPDebug  = 0;
    // $mail->SMTPAuth   = true;
    // $mail->SMTPSecure = "ssl";
    // $mail->Port       = 465;
    // $mail->SMTPKeepAlive = true;
    // $mail->Host       = $conf::$mail_host;
    // $mail->Username   = $conf::$mail_username;
    // $mail->Password   = $conf::$mail_password;

    $mail = SetupMail($mail, $conf);


    $mail->IsHTML(true);
    
    $receiver = "";
    $cc = "";

    $_list = explode(",", $access);
    foreach($_list as &$c_list)
    {
        $notifior = GetNotifiers($c_list, $serial_name);
        foreach($notifior as &$list)
        {
            $receiver .= $list["username"] . ", ";
            $mail->AddAddress($list["email"], $list["username"]);
        }
    }

    $receiver = rtrim($receiver, ", ");

    // explore cc into array
    $cc_list = explode(",", $access_cc);
    foreach($cc_list as &$c_list)
    {
        $notifior = GetNotifiers($c_list, $serial_name);
        foreach($notifior as &$list)
        {
            $cc = $list["username"];
            $mail->AddCC($list["email"], $list["username"]);
        }
    }
    

    $mail->SetFrom("feliix.it@gmail.com", "Feliix.System");
    $mail->AddReplyTo("feliix.it@gmail.com", "Feliix.System");

    $mail->Subject = "";

    $header = "";
    $reviser = "";
    $deletor = "";
    $url = "";

    // preliminary
    if($action == 'add')
    {
        $mail->Subject = '[Knowledge Notification] ' . $name . ' created new knowledge in Knowledge List';
        $header = "<p>" . $name . ' created new knowledge in Knowledge List. Below is the details of the knowledge:</p>';
        $url = "https://feliix.myvnc.com/knowledge_display";
    }

    if($action == 'edit')
    {
        $mail->Subject = '[Knowledge Notification] ' . $name . ' revised existing knowledge in Knowledge List';
        $header = "<p>" . $name . ' revised existing knowledge in Knowledge List. Below is the details of the knowledge:</p>';

        $reviser = $name;

        $url = "https://feliix.myvnc.com/knowledge_display";
    }
    
    if($action == 'del')
    {
        $mail->Subject = '[Knowledge Notification] ' . $name . ' deleted existing knowledge in Knowledge List';
        $header = "<p>" . $name . ' deleted existing knowledge in Knowledge List. Below is the details of the knowledge:</p>';

        $deletor = $name;

        $url = "https://feliix.myvnc.com/knowledge_display";
    }
    
    $content =  "<p>Dear all,</p>";
    $content = $content . $header;
    $content = $content . "<p> </p>";

    $content = $content . "<p>Title: " . $title . "</p>";
    $content = $content . "<p>Creator: " . $creator . " at " . $created_at . "</p>";
    if($reviser != "")
        $content = $content . "<p>Reviser: " . $reviser . " at " . $name_at . "</p>";
    if($deletor != "")
        $content = $content . "<p>Deleter: " . $deletor . " at " . $name_at . "</p>";
    $content = $content . "<p>Category: " . $category . "</p>";
    $content = $content . "<p>Type: " . $view_type . "</p>";
    $content = $content . "<p>Duration: " . $duration . " " . $watch . "</p>";	

    $content = $content . "<p> </p>";
    if($action == "add")
    {
        $content = $content . "<p>Please log on to Feliix >> Knowledge Library >> Knowledge List to view new Knowledge.</p>";
        $content = $content . "<p>URL: " . $url . "</p>";
    }

    if($action == "edit")
    {
        $content = $content . "<p>Please log on to Feliix >> Knowledge Library >> Knowledge List to view revised Knowledge.</p>";
        $content = $content . "<p>URL: " .$url . "</p>";
    }

    $mail->MsgHTML($content);
    if($mail->Send()) {
        logMail($receiver, $content);
        return true;
//        echo "Error while sending Email.";
//        var_dump($mail);
    } else {
        logMail($receiver, $mail->ErrorInfo . $content);
        return false;
//        echo "Email sent successfully";
    }

}


function batch_voting_system_notify_mail($access, $access_cc, $topic_name, $creator, $created_at, $vote_start, $vote_end, $vote_rule, $od_id, $action)
{
    $conf = new Conf();

    $mail = new PHPMailer();
    $mail->IsSMTP();
    $mail->Mailer = "smtp";
    $mail->CharSet = 'UTF-8';
    $mail->Encoding = 'base64';

    // $mail->SMTPDebug  = 0;
    // $mail->SMTPAuth   = true;
    // $mail->SMTPSecure = "ssl";
    // $mail->Port       = 465;
    // $mail->SMTPKeepAlive = true;
    // $mail->Host       = $conf::$mail_host;
    // $mail->Username   = $conf::$mail_username;
    // $mail->Password   = $conf::$mail_password;

    $mail = SetupMail($mail, $conf);


    $mail->IsHTML(true);
    
    $receiver = "";
    $cc = "";

    $_list = explode(",", $access);
    foreach($_list as &$c_list)
    {
        $notifior = GetNotifiers($c_list, $serial_name);
        foreach($notifior as &$list)
        {
            $receiver .= $list["username"] . ", ";
            $mail->AddAddress($list["email"], $list["username"]);
        }
    }

    $receiver = rtrim($receiver, ", ");

    // explore cc into array
    $cc_list = explode(",", $access_cc);
    foreach($cc_list as &$c_list)
    {
        $notifior = GetNotifiers($c_list, $serial_name);
        foreach($notifior as &$list)
        {
            $cc = $list["username"];
            $mail->AddCC($list["email"], $list["username"]);
        }
    }
    

    $mail->SetFrom("feliix.it@gmail.com", "Feliix.System");
    $mail->AddReplyTo("feliix.it@gmail.com", "Feliix.System");

    $mail->Subject = "";

    $header = "";
    $url = "";

    if($action == 'start')
    {
        $mail->Subject = '[Vote Notification] New voting topic starts to vote';
        $header = "<p>A new voting topic starts to vote. Below is the details of the voting topic:</p>";
        $url = "https://feliix.myvnc.com/voting_system";
        
    }

    if($action == 'end')
    {
        $mail->Subject = '[Vote Notification] Result of one voting topic already released';
        $header = "<p>Result of the below voting topic already released. Below is the details of the voting topic:</p>";
        $url = "https://feliix.myvnc.com/voting_system";
    }
    
    if($action == 'mgt')
    {
        $mail->Subject = '[Vote Notification] Result of one voting topic you created already released';
        $header = "<p>Result of one voting topic you created already released. Below is the details of the voting topic:</p>";
        $url = "https://feliix.myvnc.com/voting_topic_mgt";
    }
    
    if($action == 'mgt')
        $content =  "<p>Dear " . $creator . ",</p>";
    else
        $content =  "<p>Dear all,</p>";

    $content = $content . $header;
    $content = $content . "<p> </p>";

    $content = $content . "<p>Topic Name: " . $topic_name . "</p>";
    $content = $content . "<p>Creator: " . $creator . " at " . $created_at . "</p>";
    $content = $content . "<p>Voting Time: " . $vote_start . " ~ " . $vote_end . "</p>";
    $content = $content . "<p>Voting Rule: " . $vote_rule . "</p>";   

    $content = $content . "<p> </p>";
    if($action == "start")
    {
        $content = $content . "<p>Please log on to Feliix >> Let's Vote >> Voting System to start your vote.</p>";
        $content = $content . "<p>URL: " . $url . "</p>";
    }

    if($action == "end")
    {
        $content = $content . "<p>Please log on to Feliix >> Let's Vote >> Voting System to view the result.</p>";
        $content = $content . "<p>URL: " . $url . "</p>";
    }

    if($action == "mgt")
    {
        $content = $content . "<p>Please log on to Feliix >> Let's Vote >> Voting Topic Management to view the result.</p>";
        $content = $content . "<p>URL: " . $url . "</p>";
    }

    $mail->MsgHTML($content);
    if($mail->Send()) {
        logMail($receiver, $content);
        return true;
//        echo "Error while sending Email.";
//        var_dump($mail);
    } else {
        logMail($receiver, $mail->ErrorInfo . $content);
        return false;
//        echo "Email sent successfully";
    }

}


function order_type_notification($name, $access,  $access_cc, $project_name, $serial_name, $order_name, $order_type, $remark, $action, $items, $od_id, $type)
{
    $conf = new Conf();

    $mail = new PHPMailer();
    $mail->IsSMTP();
    $mail->Mailer = "smtp";
    $mail->CharSet = 'UTF-8';
    $mail->Encoding = 'base64';

    // $mail->SMTPDebug  = 0;
    // $mail->SMTPAuth   = true;
    // $mail->SMTPSecure = "ssl";
    // $mail->Port       = 465;
    // $mail->SMTPKeepAlive = true;
    // $mail->Host       = $conf::$mail_host;
    // $mail->Username   = $conf::$mail_username;
    // $mail->Password   = $conf::$mail_password;

    $mail = SetupMail($mail, $conf);


    $mail->IsHTML(true);
    
    $receiver = "";
    $cc = "";

    $_list = explode(",", $access);
    foreach($_list as &$c_list)
    {
        $notifior = GetAccessNotifiers($c_list, $serial_name);
        foreach($notifior as &$list)
        {
            $receiver .= $list["username"] . ", ";
            $mail->AddAddress($list["email"], $list["username"]);
        }
    }

    $receiver = rtrim($receiver, ", ");

    // explore cc into array
    $cc_list = explode(",", $access_cc);
    foreach($cc_list as &$c_list)
    {
        $notifior = GetAccessNotifiers($c_list, $serial_name);
        foreach($notifior as &$list)
        {
            $cc = $list["username"];
            $mail->AddCC($list["email"], $list["username"]);
        }
    }
    

    $mail->SetFrom("feliix.it@gmail.com", "Feliix.System");
    $mail->AddReplyTo("feliix.it@gmail.com", "Feliix.System");

    $mail->Subject = "";

    $header = "";
    $url = "";

    // preliminary
    if($action == 'send_note')
    {
        $mail->Subject = 'Items of "' . $order_type . ': ' . $serial_name . '" need your feedback';
        $header = 'Items of "' . $order_type . ': ' . $serial_name . '" need your feedback. Please check details below:';
        $url = "https://feliix.myvnc.com/order_taiwan_" . $type . "_p1?id=" . $od_id;
    }
        
    if($action == 'withdraw_note_tw')
    {
        $mail->Subject = 'Request for feedback was withdrawn on items of "' . $order_type . ': ' . $serial_name . '" ';
        $header = 'The request for your feedback was withdrawn on items of  "' . $order_type . ': ' . $serial_name . '". Please check details below:';
        $url = "https://feliix.myvnc.com/order_taiwan_" . $type . "_p1?id=" . $od_id;
    }

    if($action == 'approval')
    {
        $mail->Subject = 'Items of "' . $order_type . ': ' . $serial_name . '" need your approval';
        $header = 'Items of "' . $order_type . ': ' . $serial_name . '" need your approval. Please check details below:';
        $url = "https://feliix.myvnc.com/order_taiwan_" . $type . "_p2?id=" . $od_id;
    }

    if($action == 'finish_notes')
    {
        $mail->Subject = 'Taiwan office already provided feedback for items of "' . $order_type . ': ' . $serial_name . '"';
        $header = 'Taiwan office already provided feedback for items of "' . $order_type . ': ' . $serial_name . '". Please check details below:';
        $url = "https://feliix.myvnc.com/order_taiwan_" . $type . "_p1?id=" . $od_id;
    }

    // for approve
    if($action == 'approved')
    {
        $mail->Subject = 'Items of "' . $order_type . ': ' . $serial_name . '" were approved';
        $header = 'Items of "' . $order_type . ': ' . $serial_name . '" were already approved and need you to follow. Please check details below:';
        $url = "https://feliix.myvnc.com/order_taiwan_" . $type . "_p3?id=" . $od_id;
    }

    if($action == 'reject')
    {
        $mail->Subject = 'Items of "' . $order_type . ': ' . $serial_name . '" were rejected';
        $header = 'Items of "' . $order_type . ': ' . $serial_name . '" were already rejected and need you to follow. Please check details below:';
        $url = "https://feliix.myvnc.com/order_taiwan_" . $type . "_p1?id=" . $od_id;
    }
    
    if($action == 'withdraw')
    {
        $mail->Subject = 'Request for approval was withdrawn on items of "' . $order_type . ': ' . $serial_name . '" ';
        $header = 'The request for your approval was withdrawn on items of  "' . $order_type . ': ' . $serial_name . '". Please check details below:';
        $url = "https://feliix.myvnc.com/order_taiwan_" . $type . "_p1?id=" . $od_id;
    }

    // APPROVED
    if($action == 'ordered')
    {
        $receiver = "All";
        $mail->Subject = 'Items of "' . $order_type . ': ' . $serial_name . '" are ordered';
        $header = 'Items of "' . $order_type . ': ' . $serial_name . '" are ordered. Please check details below:';
        $url = "https://feliix.myvnc.com/order_taiwan_" . $type . "_p3?id=" . $od_id;
    }

    // Canceled
    if($action == 'canceled')
    {
        $receiver = "All";
        $mail->Subject = 'Items of "' . $order_type . ': ' . $serial_name . '" are canceled';
        $header = 'Items of "' . $order_type . ': ' . $serial_name . '" are canceled. Please check details below:';
        $url = "https://feliix.myvnc.com/order_taiwan_" . $type . "_p3?id=" . $od_id;
    }

    if($action == 'ship_info')
    {
        // $receiver = "All";
        $mail->Subject = 'Shipping info for items of "' . $order_type . ': ' . $serial_name . '" is updated';
        $header = 'Shipping info for items of "' . $order_type . ': ' . $serial_name . '" is updated. Please check details below:';
        $url = "https://feliix.myvnc.com/order_taiwan_" . $type . "_p3?id=" . $od_id;
    }

    if($action == 'ware_info')
    {
        // $receiver = "All";
        $mail->Subject = 'Warehouse info for items of "' . $order_type . ': ' . $serial_name . '" is updated';
        $header = 'Warehouse info for items of "' . $order_type . ': ' . $serial_name . '" is updated. Please check details below:';
        $url = "https://feliix.myvnc.com/order_taiwan_" . $type . "_p3?id=" . $od_id;
    }

    if($action == 'batch')
    {
        $name = "System";
        $mail->Subject = 'Shipping info for items of "' . $order_type . ': ' . $serial_name . '" is updated';
        $header = 'Shipping info for items of "' . $order_type . ': ' . $serial_name . '" is updated. Please check details below:';
        $url = "https://feliix.myvnc.com/order_taiwan_" . $type . "_p3?id=" . $od_id;
        
    }

    $content = '<!DOCTYPE html>
    <html lang="en">
    <head>
        <meta http-equiv="content-type" content="text/html; charset=utf-8"/>
    </head>
    <body>

    <div style="width: 766px; padding: 25px 70px 20px 70px; border: 2px solid rgb(230,230,230); color: black;">

        <table style="width: 100%;">
            <tbody>
            <tr>
                <td style="font-size: 16px; padding: 10px 0 10px 5px;">';
                    $content = $content . "Dear " . $receiver . ",";
                    $content = $content . '
                </td>
            </tr>
            <tr>
                <td style="font-size: 16px; padding: 0 0 20px 5px; text-align: justify;">';
                    $content = $content . $header;
                    $content = $content . '
                </td>
            </tr>

            <tr>
                <td style="font-size: 15px; padding: 0 0 5px 15px; text-align: justify; line-height: 1.8;">';
                    $content = $content . 'Order Type: ' . $order_type . '<br>';
                    $content = $content . 'Order Name: ' . $serial_name . ' ' . $order_name . '<br>';
                    $content = $content . 'Related Task: ' . $project_name . '<br>';
                    $content = $content . 'Submission Time: ' . date('Y/m/d h:i:s a', time()) . '<br>';
                    $content = $content . 'Submitter: ' . $name . '<br>';
                    $content = $content . 'Comment: ' . $remark . '';
                    $content = $content . '
                </td>
            </tr>
            </tbody>
        </table>
        <table style="margin-left: 15px; width: 96%;">
            <tbody>
            <tr>
                <td colspan="2"
                    style="background-color: #DFEAEA; border: 2px solid #94BABB; border-bottom: 1px solid #94BABB; padding: 8px; font-size: 14px; font-weight: 600; text-align: center; border-top-left-radius: 9px; border-top-right-radius: 9px;">
                    Affected Items
                </td>
            </tr>
            <tr>
                <td style="border-left: 2px solid #94BABB; border-bottom: 1px solid #94BABB; padding: 8px; font-size: 14px; font-weight: 600; text-align: center; width: 280px;">
                    #
                </td>

                <td style="border-left: 1px solid #94BABB; border-bottom: 1px solid #94BABB; border-right: 2px solid #94BABB; padding: 8px; font-size: 14px; font-weight: 600; text-align: center; width: 440px;">
                    Product Code
                </td>
            </tr>
            ';

            /* 除了最後一個產品的其他產品 */
            $i = 0;
            for($i=0; $i<count($items)-1; $i++)
            {
                $content = $content . '
                <tr>
                    <td style="border-left: 2px solid #94BABB; border-bottom: 1px solid #94BABB; padding: 8px; font-size: 14px; text-align: center; width: 280px;">
                        ';
                        $content = $content . $items[$i]['serial_number'] . '';
                        $content = $content . '
                    </td>
                    <td style="border-left: 1px solid #94BABB; border-bottom: 1px solid #94BABB; border-right: 2px solid #94BABB; padding: 8px; font-size: 14px; text-align: center; width: 440px;">
                        ';
                        $content = $content . $items[$i]['code'] . '';
                        $content = $content . '
                    </td>
                </tr>
                ';
            }

            /* 最後一個產品 */
            $content = $content . '
            <tr>
                <td style="border-left: 2px solid #94BABB; border-bottom: 2px solid #94BABB; padding: 8px; font-size: 14px; text-align: center; width: 280px; border-bottom-left-radius: 9px;">
                    ';
                    $content = $content . $items[$i]['serial_number']  . '';
                    $content = $content . '
                </td>
                <td style="border-left: 1px solid #94BABB; border-bottom: 2px solid #94BABB; border-right: 2px solid #94BABB; padding: 8px; font-size: 14px; text-align: center; width: 440px; border-bottom-right-radius: 9px;">
                    ';
                    $content = $content . $items[$i]['code'] . '';
                    $content = $content . '
                </td>
            </tr>
            ';


        $content = $content . '
            </tbody>
                </table>
                <hr style="margin-top: 45px;">
                <table style="width: 100%;">
                    <tbody>
                    <tr>
                        <td style="font-size: 16px; padding: 5px 0 0 5px; line-height: 1.5;">
                            Please click this link to view the target webpage: ';
                            $content = $content . '<a href="' . $url . '">' . $url . '</a> ';
                            $content = $content . '
                        </td>
                    </tr>
                    </tbody>
                </table>
            </div>
            </body>
            </html>';

    $mail->MsgHTML($content);
    if($mail->Send()) {
        logMail($receiver, $content);
        return true;
//        echo "Error while sending Email.";
//        var_dump($mail);
    } else {
        logMail($receiver, $mail->ErrorInfo . $content);
        return false;
//        echo "Email sent successfully";
    }

}


function order_sample_notification($name, $access,  $access_cc, $project_name, $serial_name, $order_name, $order_type, $remark, $action, $items, $od_id, $type)
{
    $conf = new Conf();

    $mail = new PHPMailer();
    $mail->IsSMTP();
    $mail->Mailer = "smtp";
    $mail->CharSet = 'UTF-8';
    $mail->Encoding = 'base64';

    // $mail->SMTPDebug  = 0;
    // $mail->SMTPAuth   = true;
    // $mail->SMTPSecure = "ssl";
    // $mail->Port       = 465;
    // $mail->SMTPKeepAlive = true;
    // $mail->Host       = $conf::$mail_host;
    // $mail->Username   = $conf::$mail_username;
    // $mail->Password   = $conf::$mail_password;

    $mail = SetupMail($mail, $conf);


    $mail->IsHTML(true);
    
    $receiver = "";
    $cc = "";

    $_list = explode(",", $access);
    foreach($_list as &$c_list)
    {
        $notifior = GetAccessNotifiers($c_list, $serial_name);
        foreach($notifior as &$list)
        {
            $receiver .= $list["username"] . ", ";
            $mail->AddAddress($list["email"], $list["username"]);
        }
    }

    $receiver = rtrim($receiver, ", ");

    // explore cc into array
    $cc_list = explode(",", $access_cc);
    foreach($cc_list as &$c_list)
    {
        $notifior = GetAccessNotifiers($c_list, $serial_name);
        foreach($notifior as &$list)
        {
            $cc = $list["username"];
            $mail->AddCC($list["email"], $list["username"]);
        }
    }
    

    $mail->SetFrom("feliix.it@gmail.com", "Feliix.System");
    $mail->AddReplyTo("feliix.it@gmail.com", "Feliix.System");

    $mail->Subject = "";

    $header = "";
    $url = "";

    // preliminary
    if($action == 'send_note')
    {
        $mail->Subject = 'Items of "' . $order_type . ': ' . $serial_name . '" need your feedback';
        $header = 'Items of "' . $order_type . ': ' . $serial_name . '" need your feedback. Please check details below:';
        $url = "https://feliix.myvnc.com/order_taiwan_" . $type . "_p1?id=" . $od_id;
    }
        
    if($action == 'withdraw_note_tw')
    {
        $mail->Subject = 'Request for feedback was withdrawn on items of "' . $order_type . ': ' . $serial_name . '" ';
        $header = 'The request for your feedback was withdrawn on items of  "' . $order_type . ': ' . $serial_name . '". Please check details below:';
        $url = "https://feliix.myvnc.com/order_taiwan_" . $type . "_p1?id=" . $od_id;
    }

    if($action == 'approval')
    {
        $mail->Subject = 'Items of "' . $order_type . ': ' . $serial_name . '" need your approval';
        $header = 'Items of "' . $order_type . ': ' . $serial_name . '" need your approval. Please check details below:';
        $url = "https://feliix.myvnc.com/order_taiwan_" . $type . "_p2?id=" . $od_id;
    }

    if($action == 'finish_notes')
    {
        $mail->Subject = 'Taiwan office already provided feedback for items of "' . $order_type . ': ' . $serial_name . '"';
        $header = 'Taiwan office already provided feedback for items of "' . $order_type . ': ' . $serial_name . '". Please check details below:';
        $url = "https://feliix.myvnc.com/order_taiwan_" . $type . "_p1?id=" . $od_id;
    }

    // for approve
    if($action == 'approved')
    {
        $mail->Subject = 'Items of "' . $order_type . ': ' . $serial_name . '" were approved';
        $header = 'Items of "' . $order_type . ': ' . $serial_name . '" were already approved and need you to follow. Please check details below:';
        $url = "https://feliix.myvnc.com/order_taiwan_" . $type . "_p3?id=" . $od_id;
    }

    if($action == 'reject')
    {
        $mail->Subject = 'Items of "' . $order_type . ': ' . $serial_name . '" were rejected';
        $header = 'Items of "' . $order_type . ': ' . $serial_name . '" were already rejected and need you to follow. Please check details below:';
        $url = "https://feliix.myvnc.com/order_taiwan_" . $type . "_p1?id=" . $od_id;
    }
    
    if($action == 'withdraw')
    {
        $mail->Subject = 'Request for approval was withdrawn on items of "' . $order_type . ': ' . $serial_name . '" ';
        $header = 'The request for your approval was withdrawn on items of  "' . $order_type . ': ' . $serial_name . '". Please check details below:';
        $url = "https://feliix.myvnc.com/order_taiwan_" . $type . "_p1?id=" . $od_id;
    }

    // APPROVED
    if($action == 'ordered')
    {
        $receiver = "All";
        $mail->Subject = 'Items of "' . $order_type . ': ' . $serial_name . '" are ordered';
        $header = 'Items of "' . $order_type . ': ' . $serial_name . '" are ordered. Please check details below:';
        $url = "https://feliix.myvnc.com/order_taiwan_" . $type . "_p3?id=" . $od_id;
    }

    // Canceled
    if($action == 'canceled')
    {
        $receiver = "All";
        $mail->Subject = 'Items of "' . $order_type . ': ' . $serial_name . '" are canceled';
        $header = 'Items of "' . $order_type . ': ' . $serial_name . '" are canceled. Please check details below:';
        $url = "https://feliix.myvnc.com/order_taiwan_" . $type . "_p3?id=" . $od_id;
    }

    if($action == 'ship_info')
    {
        // $receiver = "All";
        $mail->Subject = 'Shipping info for items of "' . $order_type . ': ' . $serial_name . '" is updated';
        $header = 'Shipping info for items of "' . $order_type . ': ' . $serial_name . '" is updated. Please check details below:';
        $url = "https://feliix.myvnc.com/order_taiwan_" . $type . "_p3?id=" . $od_id;
    }

    if($action == 'ware_info')
    {
        // $receiver = "All";
        $mail->Subject = 'Warehouse info for items of "' . $order_type . ': ' . $serial_name . '" is updated';
        $header = 'Warehouse info for items of "' . $order_type . ': ' . $serial_name . '" is updated. Please check details below:';
        $url = "https://feliix.myvnc.com/order_taiwan_" . $type . "_p3?id=" . $od_id;
    }

    if($action == 'batch')
    {
        $name = "System";
        $mail->Subject = 'Shipping info for items of "' . $order_type . ': ' . $serial_name . '" is updated';
        $header = 'Shipping info for items of "' . $order_type . ': ' . $serial_name . '" is updated. Please check details below:';
        $url = "https://feliix.myvnc.com/order_taiwan_" . $type . "_p3?id=" . $od_id;
        
    }

    $content = '<!DOCTYPE html>
    <html lang="en">
    <head>
        <meta http-equiv="content-type" content="text/html; charset=utf-8"/>
    </head>
    <body>

    <div style="width: 766px; padding: 25px 70px 20px 70px; border: 2px solid rgb(230,230,230); color: black;">

        <table style="width: 100%;">
            <tbody>
            <tr>
                <td style="font-size: 16px; padding: 10px 0 10px 5px;">';
                    $content = $content . "Dear " . $receiver . ",";
                    $content = $content . '
                </td>
            </tr>
            <tr>
                <td style="font-size: 16px; padding: 0 0 20px 5px; text-align: justify;">';
                    $content = $content . $header;
                    $content = $content . '
                </td>
            </tr>

            <tr>
                <td style="font-size: 15px; padding: 0 0 5px 15px; text-align: justify; line-height: 1.8;">';
                    $content = $content . 'Order Type: ' . $order_type . '<br>';
                    $content = $content . 'Order Name: ' . $serial_name . ' ' . $order_name . '<br>';
                    $content = $content . 'Related Task: ' . $project_name . '<br>';
                    $content = $content . 'Submission Time: ' . date('Y/m/d h:i:s a', time()) . '<br>';
                    $content = $content . 'Submitter: ' . $name . '<br>';
                    $content = $content . 'Comment: ' . $remark . '';
                    $content = $content . '
                </td>
            </tr>
            </tbody>
        </table>
        <table style="margin-left: 15px; width: 96%;">
            <tbody>
            <tr>
                <td colspan="2"
                    style="background-color: #DFEAEA; border: 2px solid #94BABB; border-bottom: 1px solid #94BABB; padding: 8px; font-size: 14px; font-weight: 600; text-align: center; border-top-left-radius: 9px; border-top-right-radius: 9px;">
                    Affected Items
                </td>
            </tr>
            <tr>
                <td style="border-left: 2px solid #94BABB; border-bottom: 1px solid #94BABB; padding: 8px; font-size: 14px; font-weight: 600; text-align: center; width: 280px;">
                    #
                </td>

                <td style="border-left: 1px solid #94BABB; border-bottom: 1px solid #94BABB; border-right: 2px solid #94BABB; padding: 8px; font-size: 14px; font-weight: 600; text-align: center; width: 440px;">
                    Product Code
                </td>
            </tr>
            ';

            /* 除了最後一個產品的其他產品 */
            $i = 0;
            for($i=0; $i<count($items)-1; $i++)
            {
                $content = $content . '
                <tr>
                    <td style="border-left: 2px solid #94BABB; border-bottom: 1px solid #94BABB; padding: 8px; font-size: 14px; text-align: center; width: 280px;">
                        ';
                        $content = $content . $items[$i]['serial_number'] . '';
                        $content = $content . '
                    </td>
                    <td style="border-left: 1px solid #94BABB; border-bottom: 1px solid #94BABB; border-right: 2px solid #94BABB; padding: 8px; font-size: 14px; text-align: center; width: 440px;">
                        ';
                        $content = $content . $items[$i]['code'] . '';
                        $content = $content . '
                    </td>
                </tr>
                ';
            }

            /* 最後一個產品 */
            $content = $content . '
            <tr>
                <td style="border-left: 2px solid #94BABB; border-bottom: 2px solid #94BABB; padding: 8px; font-size: 14px; text-align: center; width: 280px; border-bottom-left-radius: 9px;">
                    ';
                    $content = $content . $items[$i]['serial_number']  . '';
                    $content = $content . '
                </td>
                <td style="border-left: 1px solid #94BABB; border-bottom: 2px solid #94BABB; border-right: 2px solid #94BABB; padding: 8px; font-size: 14px; text-align: center; width: 440px; border-bottom-right-radius: 9px;">
                    ';
                    $content = $content . $items[$i]['code'] . '';
                    $content = $content . '
                </td>
            </tr>
            ';


        $content = $content . '
            </tbody>
                </table>
                <hr style="margin-top: 45px;">
                <table style="width: 100%;">
                    <tbody>
                    <tr>
                        <td style="font-size: 16px; padding: 5px 0 0 5px; line-height: 1.5;">
                            Please click this link to view the target webpage: ';
                            $content = $content . '<a href="' . $url . '">' . $url . '</a> ';
                            $content = $content . '
                        </td>
                    </tr>
                    </tbody>
                </table>
            </div>
            </body>
            </html>';

    $mail->MsgHTML($content);
    if($mail->Send()) {
        logMail($receiver, $content);
        return true;
//        echo "Error while sending Email.";
//        var_dump($mail);
    } else {
        logMail($receiver, $mail->ErrorInfo . $content);
        return false;
//        echo "Email sent successfully";
    }

}

function order_notification02($name, $access,  $access_cc, $project_name, $serial_name, $order_name, $order_type, $remark, $action, $items, $od_id)
{
    $conf = new Conf();

    $mail = new PHPMailer();
    $mail->IsSMTP();
    $mail->Mailer = "smtp";
    $mail->CharSet = 'UTF-8';
    $mail->Encoding = 'base64';

    // $mail->SMTPDebug  = 0;
    // $mail->SMTPAuth   = true;
    // $mail->SMTPSecure = "ssl";
    // $mail->Port       = 465;
    // $mail->SMTPKeepAlive = true;
    // $mail->Host       = $conf::$mail_host;
    // $mail->Username   = $conf::$mail_username;
    // $mail->Password   = $conf::$mail_password;

    $mail = SetupMail($mail, $conf);


    $mail->IsHTML(true);
    
    $receiver = "";
    $cc = "";
    $assignee = [];

    
    $_list = explode(",", $access);
    foreach($_list as &$c_list)
    {
        $notifior = GetAccessNotifiers($c_list, $serial_name);
        foreach($notifior as &$list)
        {
            $receiver .= $list["username"] . ", ";
            $mail->AddAddress($list["email"], $list["username"]);
        }
    }
    
    $receiver = rtrim($receiver, ", ");

    // explore cc into array
    $cc_list = explode(",", $access_cc);
    foreach($cc_list as &$c_list)
    {
        $notifior = GetAccessNotifiers($c_list, $serial_name);
        foreach($notifior as &$list)
        {
            $cc = $list["username"];
            $mail->AddCC($list["email"], $list["username"]);
        }
    }
    

    $mail->SetFrom("feliix.it@gmail.com", "Feliix.System");
    $mail->AddReplyTo("feliix.it@gmail.com", "Feliix.System");

    $mail->Subject = "";

    $header = 'You are assigned to test items of  "' . $order_type . ': ' . $serial_name . '". Please check details';
    $url = "";

    if($action == 'assing_test')
    {
        foreach($items as &$item)
        {
            $assignee[] = $item['test'];
        }

        $receiver = implode("','", $assignee);

        $notifior = GetAccessNotifiersByName($receiver, $serial_name);
        foreach($notifior as &$list)
        {
            $mail->AddAddress($list["email"], $list["username"]);
        }

        $notifior = GetAccessNotifiersByName($access_cc, $serial_name);
        foreach($notifior as &$list)
        {
            $mail->AddCC($list["email"], $list["username"]);
        }

        $receiver = "All";

        $mail->Subject = 'You are assigned to test items of "' . $order_type . ': ' . $serial_name . '" ';
        $header = 'You are assigned to test items of "' . $order_type . ': ' . $serial_name . '". Please check details below:';
        $url = "https://feliix.myvnc.com/order_taiwan_p3?id=" . $od_id;
        
    }

    if($action == 'edit_test')
    {
        foreach($items as &$item)
        {
            $assignee[] = $item['test'];
        }

        $notifior = GetAccessNotifiersByName($name, $serial_name);
        foreach($notifior as &$list)
        {
            $receiver = $list["username"];
            $mail->AddCC($list["email"], $list["username"]);
        }

        $receiver = "";
        // access5
        $_list = explode(",", $access);
        foreach($_list as &$c_list)
        {
            $notifior = GetAccessNotifiers($c_list, $serial_name);
            foreach($notifior as &$list)
            {
                $receiver .= $list["username"] . ", ";
            }
        }
        
        $receiver = rtrim($receiver, ", ");

        $mail->Subject = 'Testing info for items of "' . $order_type . ': ' . $serial_name . '" is updated';
        $header = $name . ' updated the testing info for items of "' . $order_type . ': ' . $serial_name . '". Please check details below:';
        $url = "https://feliix.myvnc.com/order_taiwan_p3?id=" . $od_id;
        
    }

    if($action == 'assign_delivery')
    {
        foreach($items as &$item)
        {
            $assignee[] = $item['delivery'];
        }

        $receiver = implode("','", $assignee);

        $notifior = GetAccessNotifiersByName($receiver, $serial_name);
        foreach($notifior as &$list)
        {
            $receiver = $list["username"];
            $mail->AddAddress($list["email"], $list["username"]);
        }

        $notifior = GetAccessNotifiersByName($access_cc, $serial_name);
        foreach($notifior as &$list)
        {
            $mail->AddCC($list["email"], $list["username"]);
        }


        $receiver = "All";

        $mail->Subject = 'You are assigned to deliver items of "' . $order_type . ': ' . $serial_name . '" ';
        $header = 'You are assigned to deliver items of "' . $order_type . ': ' . $serial_name . '". Please check details below:';
        $url = "https://feliix.myvnc.com/order_taiwan_p3?id=" . $od_id;
        
    }

    if($action == 'edit_delivery')
    {
        foreach($items as &$item)
        {
            $assignee[] = $item['delivery'];
        }

        $notifior = GetAccessNotifiersByName($name, $serial_name);
        foreach($notifior as &$list)
        {
            $receiver = $list["username"];
            $mail->AddCC($list["email"], $list["username"]);
        }

        $receiver = "";

        // access5
        $_list = explode(",", $access);
        foreach($_list as &$c_list)
        {
            $notifior = GetAccessNotifiers($c_list, $serial_name);
            foreach($notifior as &$list)
            {
                $receiver .= $list["username"] . ", ";
            }
        }

        $receiver = rtrim($receiver, ", ");

        $mail->Subject = 'Delivery info for items of "' . $order_type . ': ' . $serial_name . '" is updated';
        $header = $name . ' updated the delivery info for items of "' . $order_type . ': ' . $serial_name . '". Please check details below:';
        $url = "https://feliix.myvnc.com/order_taiwan_p3?id=" . $od_id;
        
    }



    $content = '<!DOCTYPE html>
    <html lang="en">
    <head>
        <meta http-equiv="content-type" content="text/html; charset=utf-8"/>
    </head>
    <body>

    <div style="width: 766px; padding: 25px 70px 20px 70px; border: 2px solid rgb(230,230,230); color: black;">

        <table style="width: 100%;">
            <tbody>
            <tr>
                <td style="font-size: 16px; padding: 10px 0 10px 5px;">';
                    $content = $content . "Dear " . $receiver . ",";
                    $content = $content . '
                </td>
            </tr>
            <tr>
                <td style="font-size: 16px; padding: 0 0 20px 5px; text-align: justify;">';
                    $content = $content . $header;
                    $content = $content . '
                </td>
            </tr>

            <tr>
                <td style="font-size: 15px; padding: 0 0 5px 15px; text-align: justify; line-height: 1.8;">';
                    $content = $content . 'Order Type: ' . $order_type . '<br>';
                    $content = $content . 'Order Name: ' . $serial_name . ' ' . $order_name . '<br>';
                    $content = $content . 'Related Project: ' . $project_name . '<br>';
                    $content = $content . 'Submission Time: ' . date('Y/m/d h:i:s a', time()) . '<br>';
                    $content = $content . 'Submitter: ' . $name . '<br>';
                    $content = $content . 'Comment: ' . $remark . '';
                    $content = $content . '
                </td>
            </tr>
            </tbody>
        </table>
        <table style="margin-left: 15px; width: 96%;">
            <tbody>
            <tr>
                <td colspan="2"
                    style="background-color: #DFEAEA; border: 2px solid #94BABB; border-bottom: 1px solid #94BABB; padding: 8px; font-size: 14px; font-weight: 600; text-align: center; border-top-left-radius: 9px; border-top-right-radius: 9px;">
                    Affected Items
                </td>
            </tr>
            <tr>
                <td style="border-left: 2px solid #94BABB; border-bottom: 1px solid #94BABB; padding: 8px; font-size: 14px; font-weight: 600; text-align: center; width: 280px;">
                    #
                </td>

                <td style="border-left: 1px solid #94BABB; border-bottom: 1px solid #94BABB; border-right: 2px solid #94BABB; padding: 8px; font-size: 14px; font-weight: 600; text-align: center; width: 440px;">
                    Product Code
                </td>
            </tr>
            ';

            /* 除了最後一個產品的其他產品 */
            $i = 0;
            for($i=0; $i<count($items)-1; $i++)
            {
                $content = $content . '
                <tr>
                    <td style="border-left: 2px solid #94BABB; border-bottom: 1px solid #94BABB; padding: 8px; font-size: 14px; text-align: center; width: 280px;">
                        ';
                        $content = $content . $items[$i]['serial_number'] . '';
                        $content = $content . '
                    </td>
                    <td style="border-left: 1px solid #94BABB; border-bottom: 1px solid #94BABB; border-right: 2px solid #94BABB; padding: 8px; font-size: 14px; text-align: center; width: 440px;">
                        ';
                        $content = $content . $items[$i]['code'] . '';
                        $content = $content . '
                    </td>
                </tr>
                ';
            }

            /* 最後一個產品 */
            $content = $content . '
            <tr>
                <td style="border-left: 2px solid #94BABB; border-bottom: 2px solid #94BABB; padding: 8px; font-size: 14px; text-align: center; width: 280px; border-bottom-left-radius: 9px;">
                    ';
                    $content = $content . $items[$i]['serial_number'] . '';
                    $content = $content . '
                </td>
                <td style="border-left: 1px solid #94BABB; border-bottom: 2px solid #94BABB; border-right: 2px solid #94BABB; padding: 8px; font-size: 14px; text-align: center; width: 440px;">
                    ';
                    $content = $content . $items[$i]['code'] . '';
                    $content = $content . '
                </td>
            </tr>
            ';


        $content = $content . '
            </tbody>
                </table>
                <hr style="margin-top: 45px;">
                <table style="width: 100%;">
                    <tbody>
                    <tr>
                        <td style="font-size: 16px; padding: 5px 0 0 5px; line-height: 1.5;">
                            Please click this link to view the target webpage: ';
                            $content = $content . '<a href="' . $url . '">' . $url . '</a> ';
                            $content = $content . '
                        </td>
                    </tr>
                    </tbody>
                </table>
            </div>
            </body>
            </html>';

    $mail->MsgHTML($content);
    if($mail->Send()) {
        logMail($receiver, $content);
        return true;
//        echo "Error while sending Email.";
//        var_dump($mail);
    } else {
        logMail($receiver, $mail->ErrorInfo . $content);
        return false;
//        echo "Email sent successfully";
    }

}


function mockup_notification02($name, $access,  $access_cc, $project_name, $serial_name, $order_name, $order_type, $remark, $action, $items, $od_id)
{
    $conf = new Conf();

    $mail = new PHPMailer();
    $mail->IsSMTP();
    $mail->Mailer = "smtp";
    $mail->CharSet = 'UTF-8';
    $mail->Encoding = 'base64';

    // $mail->SMTPDebug  = 0;
    // $mail->SMTPAuth   = true;
    // $mail->SMTPSecure = "ssl";
    // $mail->Port       = 465;
    // $mail->SMTPKeepAlive = true;
    // $mail->Host       = $conf::$mail_host;
    // $mail->Username   = $conf::$mail_username;
    // $mail->Password   = $conf::$mail_password;

    $mail = SetupMail($mail, $conf);


    $mail->IsHTML(true);
    
    $receiver = "";
    $cc = "";
    $assignee = [];

    
    $_list = explode(",", $access);
    foreach($_list as &$c_list)
    {
        $notifior = GetAccessNotifiers($c_list, $serial_name);
        foreach($notifior as &$list)
        {
            $receiver .= $list["username"] . ", ";
            $mail->AddAddress($list["email"], $list["username"]);
        }
    }
    
    $receiver = rtrim($receiver, ", ");

    // explore cc into array
    $cc_list = explode(",", $access_cc);
    foreach($cc_list as &$c_list)
    {
        $notifior = GetAccessNotifiers($c_list, $serial_name);
        foreach($notifior as &$list)
        {
            $cc = $list["username"];
            $mail->AddCC($list["email"], $list["username"]);
        }
    }
    

    $mail->SetFrom("feliix.it@gmail.com", "Feliix.System");
    $mail->AddReplyTo("feliix.it@gmail.com", "Feliix.System");

    $mail->Subject = "";

    $header = 'You are assigned to test items of  "' . $order_type . ': ' . $serial_name . '". Please check details';
    $url = "";

    if($action == 'assing_test')
    {
        foreach($items as &$item)
        {
            $assignee[] = $item['test'];
        }

        $receiver = implode("','", $assignee);

        $notifior = GetAccessNotifiersByName($receiver, $serial_name);
        foreach($notifior as &$list)
        {
            $mail->AddAddress($list["email"], $list["username"]);
        }

        $notifior = GetAccessNotifiersByName($access_cc, $serial_name);
        foreach($notifior as &$list)
        {
            $mail->AddCC($list["email"], $list["username"]);
        }

        $receiver = "All";

        $mail->Subject = 'You are assigned to test items of "' . $order_type . ': ' . $serial_name . '" ';
        $header = 'You are assigned to test items of "' . $order_type . ': ' . $serial_name . '". Please check details below:';
        $url = "https://feliix.myvnc.com/order_taiwan_mockup_p3?id=" . $od_id;
        
    }

    if($action == 'edit_test')
    {
        foreach($items as &$item)
        {
            $assignee[] = $item['test'];
        }

        $notifior = GetAccessNotifiersByName($name, $serial_name);
        foreach($notifior as &$list)
        {
            $receiver = $list["username"];
            $mail->AddCC($list["email"], $list["username"]);
        }

        $receiver = "";
        // access5
        $_list = explode(",", $access);
        foreach($_list as &$c_list)
        {
            $notifior = GetAccessNotifiers($c_list, $serial_name);
            foreach($notifior as &$list)
            {
                $receiver .= $list["username"] . ", ";
            }
        }
        
        $receiver = rtrim($receiver, ", ");

        $mail->Subject = 'Testing info for items of "' . $order_type . ': ' . $serial_name . '" is updated';
        $header = $name . ' updated the testing info for items of "' . $order_type . ': ' . $serial_name . '". Please check details below:';
        $url = "https://feliix.myvnc.com/order_taiwan_mockup_p3?id=" . $od_id;
        
    }

    if($action == 'assign_delivery')
    {
        foreach($items as &$item)
        {
            $assignee[] = $item['delivery'];
        }

        $receiver = implode("','", $assignee);

        $notifior = GetAccessNotifiersByName($receiver, $serial_name);
        foreach($notifior as &$list)
        {
            $receiver = $list["username"];
            $mail->AddAddress($list["email"], $list["username"]);
        }

        $notifior = GetAccessNotifiersByName($access_cc, $serial_name);
        foreach($notifior as &$list)
        {
            $mail->AddCC($list["email"], $list["username"]);
        }


        $receiver = "All";

        $mail->Subject = 'You are assigned to deliver items of "' . $order_type . ': ' . $serial_name . '" ';
        $header = 'You are assigned to deliver items of "' . $order_type . ': ' . $serial_name . '". Please check details below:';
        $url = "https://feliix.myvnc.com/order_taiwan_mockup_p3?id=" . $od_id;
        
    }

    if($action == 'edit_delivery')
    {
        foreach($items as &$item)
        {
            $assignee[] = $item['delivery'];
        }

        $notifior = GetAccessNotifiersByName($name, $serial_name);
        foreach($notifior as &$list)
        {
            $receiver = $list["username"];
            $mail->AddCC($list["email"], $list["username"]);
        }

        $receiver = "";

        // access5
        $_list = explode(",", $access);
        foreach($_list as &$c_list)
        {
            $notifior = GetAccessNotifiers($c_list, $serial_name);
            foreach($notifior as &$list)
            {
                $receiver .= $list["username"] . ", ";
            }
        }

        $receiver = rtrim($receiver, ", ");

        $mail->Subject = 'Delivery info for items of "' . $order_type . ': ' . $serial_name . '" is updated';
        $header = $name . ' updated the delivery info for items of "' . $order_type . ': ' . $serial_name . '". Please check details below:';
        $url = "https://feliix.myvnc.com/order_taiwan_mockup_p3?id=" . $od_id;
        
    }



    $content = '<!DOCTYPE html>
    <html lang="en">
    <head>
        <meta http-equiv="content-type" content="text/html; charset=utf-8"/>
    </head>
    <body>

    <div style="width: 766px; padding: 25px 70px 20px 70px; border: 2px solid rgb(230,230,230); color: black;">

        <table style="width: 100%;">
            <tbody>
            <tr>
                <td style="font-size: 16px; padding: 10px 0 10px 5px;">';
                    $content = $content . "Dear " . $receiver . ",";
                    $content = $content . '
                </td>
            </tr>
            <tr>
                <td style="font-size: 16px; padding: 0 0 20px 5px; text-align: justify;">';
                    $content = $content . $header;
                    $content = $content . '
                </td>
            </tr>

            <tr>
                <td style="font-size: 15px; padding: 0 0 5px 15px; text-align: justify; line-height: 1.8;">';
                    $content = $content . 'Order Type: ' . $order_type . '<br>';
                    $content = $content . 'Order Name: ' . $serial_name . ' ' . $order_name . '<br>';
                    $content = $content . 'Related Project: ' . $project_name . '<br>';
                    $content = $content . 'Submission Time: ' . date('Y/m/d h:i:s a', time()) . '<br>';
                    $content = $content . 'Submitter: ' . $name . '<br>';
                    $content = $content . 'Comment: ' . $remark . '';
                    $content = $content . '
                </td>
            </tr>
            </tbody>
        </table>
        <table style="margin-left: 15px; width: 96%;">
            <tbody>
            <tr>
                <td colspan="2"
                    style="background-color: #DFEAEA; border: 2px solid #94BABB; border-bottom: 1px solid #94BABB; padding: 8px; font-size: 14px; font-weight: 600; text-align: center; border-top-left-radius: 9px; border-top-right-radius: 9px;">
                    Affected Items
                </td>
            </tr>
            <tr>
                <td style="border-left: 2px solid #94BABB; border-bottom: 1px solid #94BABB; padding: 8px; font-size: 14px; font-weight: 600; text-align: center; width: 280px;">
                    #
                </td>

                <td style="border-left: 1px solid #94BABB; border-bottom: 1px solid #94BABB; border-right: 2px solid #94BABB; padding: 8px; font-size: 14px; font-weight: 600; text-align: center; width: 440px;">
                    Product Code
                </td>
            </tr>
            ';

            /* 除了最後一個產品的其他產品 */
            $i = 0;
            for($i=0; $i<count($items)-1; $i++)
            {
                $content = $content . '
                <tr>
                    <td style="border-left: 2px solid #94BABB; border-bottom: 1px solid #94BABB; padding: 8px; font-size: 14px; text-align: center; width: 280px;">
                        ';
                        $content = $content . $items[$i]['serial_number'] . '';
                        $content = $content . '
                    </td>
                    <td style="border-left: 1px solid #94BABB; border-bottom: 1px solid #94BABB; border-right: 2px solid #94BABB; padding: 8px; font-size: 14px; text-align: center; width: 440px;">
                        ';
                        $content = $content . $items[$i]['code'] . '';
                        $content = $content . '
                    </td>
                </tr>
                ';
            }

            /* 最後一個產品 */
            $content = $content . '
            <tr>
                <td style="border-left: 2px solid #94BABB; border-bottom: 2px solid #94BABB; padding: 8px; font-size: 14px; text-align: center; width: 280px; border-bottom-left-radius: 9px;">
                    ';
                    $content = $content . $items[$i]['serial_number'] . '';
                    $content = $content . '
                </td>
                <td style="border-left: 1px solid #94BABB; border-bottom: 2px solid #94BABB; border-right: 2px solid #94BABB; padding: 8px; font-size: 14px; text-align: center; width: 440px;">
                    ';
                    $content = $content . $items[$i]['code'] . '';
                    $content = $content . '
                </td>
            </tr>
            ';


        $content = $content . '
            </tbody>
                </table>
                <hr style="margin-top: 45px;">
                <table style="width: 100%;">
                    <tbody>
                    <tr>
                        <td style="font-size: 16px; padding: 5px 0 0 5px; line-height: 1.5;">
                            Please click this link to view the target webpage: ';
                            $content = $content . '<a href="' . $url . '">' . $url . '</a> ';
                            $content = $content . '
                        </td>
                    </tr>
                    </tbody>
                </table>
            </div>
            </body>
            </html>';

    $mail->MsgHTML($content);
    if($mail->Send()) {
        logMail($receiver, $content);
        return true;
//        echo "Error while sending Email.";
//        var_dump($mail);
    } else {
        logMail($receiver, $mail->ErrorInfo . $content);
        return false;
//        echo "Email sent successfully";
    }

}

function order_sample_notification02($name, $access,  $access_cc, $project_name, $serial_name, $order_name, $order_type, $remark, $action, $items, $od_id, $type)
{
    $conf = new Conf();

    $mail = new PHPMailer();
    $mail->IsSMTP();
    $mail->Mailer = "smtp";
    $mail->CharSet = 'UTF-8';
    $mail->Encoding = 'base64';

    // $mail->SMTPDebug  = 0;
    // $mail->SMTPAuth   = true;
    // $mail->SMTPSecure = "ssl";
    // $mail->Port       = 465;
    // $mail->SMTPKeepAlive = true;
    // $mail->Host       = $conf::$mail_host;
    // $mail->Username   = $conf::$mail_username;
    // $mail->Password   = $conf::$mail_password;

    $mail = SetupMail($mail, $conf);


    $mail->IsHTML(true);
    
    $receiver = "";
    $cc = "";
    $assignee = [];

    
    $_list = explode(",", $access);
    foreach($_list as &$c_list)
    {
        $notifior = GetAccessNotifiers($c_list, $serial_name);
        foreach($notifior as &$list)
        {
            $receiver .= $list["username"] . ", ";
            $mail->AddAddress($list["email"], $list["username"]);
        }
    }
    
    $receiver = rtrim($receiver, ", ");

    // explore cc into array
    $cc_list = explode(",", $access_cc);
    foreach($cc_list as &$c_list)
    {
        $notifior = GetAccessNotifiers($c_list, $serial_name);
        foreach($notifior as &$list)
        {
            $cc = $list["username"];
            $mail->AddCC($list["email"], $list["username"]);
        }
    }
    

    $mail->SetFrom("feliix.it@gmail.com", "Feliix.System");
    $mail->AddReplyTo("feliix.it@gmail.com", "Feliix.System");

    $mail->Subject = "";

    $header = 'You are assigned to test items of  "' . $order_type . ': ' . $serial_name . '". Please check details';
    $url = "";

    if($action == 'assing_test')
    {
        foreach($items as &$item)
        {
            $assignee[] = $item['test'];
        }

        $receiver = implode("','", $assignee);

        $notifior = GetAccessNotifiersByName($receiver, $serial_name);
        foreach($notifior as &$list)
        {
            $mail->AddAddress($list["email"], $list["username"]);
        }

        $notifior = GetAccessNotifiersByName($access_cc, $serial_name);
        foreach($notifior as &$list)
        {
            $mail->AddCC($list["email"], $list["username"]);
        }

        $receiver = "All";

        $mail->Subject = 'You are assigned to test items of "' . $order_type . ': ' . $serial_name . '" ';
        $header = 'You are assigned to test items of "' . $order_type . ': ' . $serial_name . '". Please check details below:';
        $url = "https://feliix.myvnc.com/order_taiwan_" . $type . "_p3?id=" . $od_id;
        
    }

    if($action == 'edit_test')
    {
        foreach($items as &$item)
        {
            $assignee[] = $item['test'];
        }

        $notifior = GetAccessNotifiersByName($name, $serial_name);
        foreach($notifior as &$list)
        {
            $receiver = $list["username"];
            $mail->AddCC($list["email"], $list["username"]);
        }

        $receiver = "";
        // access5
        $_list = explode(",", $access);
        foreach($_list as &$c_list)
        {
            $notifior = GetAccessNotifiers($c_list, $serial_name);
            foreach($notifior as &$list)
            {
                $receiver .= $list["username"] . ", ";
            }
        }
        
        $receiver = rtrim($receiver, ", ");

        $mail->Subject = 'Testing info for items of "' . $order_type . ': ' . $serial_name . '" is updated';
        $header = $name . ' updated the testing info for items of "' . $order_type . ': ' . $serial_name . '". Please check details below:';
        $url = "https://feliix.myvnc.com/order_taiwan_" . $type . "_p3?id=" . $od_id;
        
    }

    if($action == 'assign_delivery')
    {
        foreach($items as &$item)
        {
            $assignee[] = $item['delivery'];
        }

        $receiver = implode("','", $assignee);

        $notifior = GetAccessNotifiersByName($receiver, $serial_name);
        foreach($notifior as &$list)
        {
            $receiver = $list["username"];
            $mail->AddAddress($list["email"], $list["username"]);
        }

        $notifior = GetAccessNotifiersByName($access_cc, $serial_name);
        foreach($notifior as &$list)
        {
            $mail->AddCC($list["email"], $list["username"]);
        }


        $receiver = "All";

        $mail->Subject = 'You are assigned to deliver items of "' . $order_type . ': ' . $serial_name . '" ';
        $header = 'You are assigned to deliver items of "' . $order_type . ': ' . $serial_name . '". Please check details below:';
        $url = "https://feliix.myvnc.com/order_taiwan_" . $type . "_p3?id=" . $od_id;
        
    }

    if($action == 'edit_delivery')
    {
        foreach($items as &$item)
        {
            $assignee[] = $item['delivery'];
        }

        $notifior = GetAccessNotifiersByName($name, $serial_name);
        foreach($notifior as &$list)
        {
            $receiver = $list["username"];
            $mail->AddCC($list["email"], $list["username"]);
        }

        $receiver = "All";

        // access5
        $_list = explode(",", $access);
        foreach($_list as &$c_list)
        {
            $notifior = GetAccessNotifiers($c_list, $serial_name);
            foreach($notifior as &$list)
            {
                $receiver .= $list["username"] . ", ";
            }
        }

        $receiver = rtrim($receiver, ", ");

        $mail->Subject = 'Delivery info for items of "' . $order_type . ': ' . $serial_name . '" is updated';
        $header = $name . ' updated the delivery info for items of "' . $order_type . ': ' . $serial_name . '". Please check details below:';
        $url = "https://feliix.myvnc.com/order_taiwan_" . $type . "_p3?id=" . $od_id;
        
    }



    $content = '<!DOCTYPE html>
    <html lang="en">
    <head>
        <meta http-equiv="content-type" content="text/html; charset=utf-8"/>
    </head>
    <body>

    <div style="width: 766px; padding: 25px 70px 20px 70px; border: 2px solid rgb(230,230,230); color: black;">

        <table style="width: 100%;">
            <tbody>
            <tr>
                <td style="font-size: 16px; padding: 10px 0 10px 5px;">';
                    $content = $content . "Dear " . $receiver . ",";
                    $content = $content . '
                </td>
            </tr>
            <tr>
                <td style="font-size: 16px; padding: 0 0 20px 5px; text-align: justify;">';
                    $content = $content . $header;
                    $content = $content . '
                </td>
            </tr>

            <tr>
                <td style="font-size: 15px; padding: 0 0 5px 15px; text-align: justify; line-height: 1.8;">';
                    $content = $content . 'Order Type: ' . $order_type . '<br>';
                    $content = $content . 'Order Name: ' . $serial_name . ' ' . $order_name . '<br>';
                    $content = $content . 'Related Project: ' . $project_name . '<br>';
                    $content = $content . 'Submission Time: ' . date('Y/m/d h:i:s a', time()) . '<br>';
                    $content = $content . 'Submitter: ' . $name . '<br>';
                    $content = $content . 'Comment: ' . $remark . '';
                    $content = $content . '
                </td>
            </tr>
            </tbody>
        </table>
        <table style="margin-left: 15px; width: 96%;">
            <tbody>
            <tr>
                <td colspan="2"
                    style="background-color: #DFEAEA; border: 2px solid #94BABB; border-bottom: 1px solid #94BABB; padding: 8px; font-size: 14px; font-weight: 600; text-align: center; border-top-left-radius: 9px; border-top-right-radius: 9px;">
                    Affected Items
                </td>
            </tr>
            <tr>
                <td style="border-left: 2px solid #94BABB; border-bottom: 1px solid #94BABB; padding: 8px; font-size: 14px; font-weight: 600; text-align: center; width: 280px;">
                    #
                </td>

                <td style="border-left: 1px solid #94BABB; border-bottom: 1px solid #94BABB; border-right: 2px solid #94BABB; padding: 8px; font-size: 14px; font-weight: 600; text-align: center; width: 440px;">
                    Product Code
                </td>
            </tr>
            ';

            /* 除了最後一個產品的其他產品 */
            $i = 0;
            for($i=0; $i<count($items)-1; $i++)
            {
                $content = $content . '
                <tr>
                    <td style="border-left: 2px solid #94BABB; border-bottom: 1px solid #94BABB; padding: 8px; font-size: 14px; text-align: center; width: 280px;">
                        ';
                        $content = $content . $items[$i]['serial_number'] . '';
                        $content = $content . '
                    </td>
                    <td style="border-left: 1px solid #94BABB; border-bottom: 1px solid #94BABB; border-right: 2px solid #94BABB; padding: 8px; font-size: 14px; text-align: center; width: 440px;">
                        ';
                        $content = $content . $items[$i]['code'] . '';
                        $content = $content . '
                    </td>
                </tr>
                ';
            }

            /* 最後一個產品 */
            $content = $content . '
            <tr>
                <td style="border-left: 2px solid #94BABB; border-bottom: 2px solid #94BABB; padding: 8px; font-size: 14px; text-align: center; width: 280px; border-bottom-left-radius: 9px;">
                    ';
                    $content = $content . $items[$i]['serial_number'] . '';
                    $content = $content . '
                </td>
                <td style="border-left: 1px solid #94BABB; border-bottom: 2px solid #94BABB; border-right: 2px solid #94BABB; padding: 8px; font-size: 14px; text-align: center; width: 440px; border-bottom-right-radius: 9px;">
                    ';
                    $content = $content . $items[$i]['code'] . '';
                    $content = $content . '
                </td>
            </tr>
            ';


        $content = $content . '
            </tbody>
                </table>
                <hr style="margin-top: 45px;">
                <table style="width: 100%;">
                    <tbody>
                    <tr>
                        <td style="font-size: 16px; padding: 5px 0 0 5px; line-height: 1.5;">
                            Please click this link to view the target webpage: ';
                            $content = $content . '<a href="' . $url . '">' . $url . '</a> ';
                            $content = $content . '
                        </td>
                    </tr>
                    </tbody>
                </table>
            </div>
            </body>
            </html>';

    $mail->MsgHTML($content);
    if($mail->Send()) {
        logMail($receiver, $content);
        return true;
//        echo "Error while sending Email.";
//        var_dump($mail);
    } else {
        logMail($receiver, $mail->ErrorInfo . $content);
        return false;
//        echo "Email sent successfully";
    }

}


function order_sample_delievery_notification($name, $access,  $access_cc, $project_name, $serial_name, $order_name, $order_type, $remark, $action, $items, $od_id, $type)
{
    $conf = new Conf();

    $mail = new PHPMailer();
    $mail->IsSMTP();
    $mail->Mailer = "smtp";
    $mail->CharSet = 'UTF-8';
    $mail->Encoding = 'base64';

    // $mail->SMTPDebug  = 0;
    // $mail->SMTPAuth   = true;
    // $mail->SMTPSecure = "ssl";
    // $mail->Port       = 465;
    // $mail->SMTPKeepAlive = true;
    // $mail->Host       = $conf::$mail_host;
    // $mail->Username   = $conf::$mail_username;
    // $mail->Password   = $conf::$mail_password;

    $mail = SetupMail($mail, $conf);


    $mail->IsHTML(true);
    
    $receiver = "";
    $cc = "";
    $assignee = [];


    $mail->SetFrom("feliix.it@gmail.com", "Feliix.System");
    $mail->AddReplyTo("feliix.it@gmail.com", "Feliix.System");

    $mail->Subject = "";

    $header = '';
    $url = "";


    if($action == 'edit_delivery')
    {
        // 收件人：角色4, 部門為 Admin 的所有人員
        
        $_list = explode(",", $access);
        foreach($_list as &$c_list)
        {
            $notifior = GetAccessNotifiers($c_list, $serial_name);
            foreach($notifior as &$list)
            {
                $receiver .= $list["username"] . ", ";
                $mail->AddAddress($list["email"], $list["username"]);
            }
        }
        $receiver = rtrim($receiver, ", ");

        $notifior = GetAccessNotifiersByDepartment("Admin");
        foreach($notifior as &$list)
        {
            if( $list["username"] == 'Gina Donato' || $list["username"] == 'Ronnie Fernando Dela Cruz'){
                $receiver .= $list["username"] . ", ";
                $mail->AddAddress($list["email"], $list["username"]);
            }
        }

        // CC收件人：執行動作的人, 角色1, 角色3, 職位為Sales Manager的使用者, 角色5
        $cc_list = explode(",", $access_cc);
        foreach($cc_list as &$c_list)
        {
            $notifior = GetAccessNotifiers($c_list, $serial_name);
            foreach($notifior as &$list)
            {
                $cc = $list["username"];
                $mail->AddCC($list["email"], $list["username"]);
            }
        }

        $notifior = GetChargeNotifiersByTitle('Sales Manager');
        foreach($notifior as &$list)
        {
            $mail->AddCC($list["email"], $list["username"]);
        }
    
        $notifior = GetAccessNotifiersByName($name, "");
        foreach($notifior as &$list)
        {
            $receiver = $list["username"];
            $mail->AddCC($list["email"], $list["username"]);
        }

        $receiver = "all";

        $mail->Subject = 'Delivery info for items of "' . $order_type . ': ' . $serial_name . '" is updated';
        $header = $name . ' updated the delivery info for items of "' . $order_type . ': ' . $serial_name . '". Please check details below:';
        $url = "https://feliix.myvnc.com/order_taiwan_" . $type . "_p3?id=" . $od_id;
        
    }



    $content = '<!DOCTYPE html>
    <html lang="en">
    <head>
        <meta http-equiv="content-type" content="text/html; charset=utf-8"/>
    </head>
    <body>

    <div style="width: 766px; padding: 25px 70px 20px 70px; border: 2px solid rgb(230,230,230); color: black;">

        <table style="width: 100%;">
            <tbody>
            <tr>
                <td style="font-size: 16px; padding: 10px 0 10px 5px;">';
                    $content = $content . "Dear " . $receiver . ",";
                    $content = $content . '
                </td>
            </tr>
            <tr>
                <td style="font-size: 16px; padding: 0 0 20px 5px; text-align: justify;">';
                    $content = $content . $header;
                    $content = $content . '
                </td>
            </tr>

            <tr>
                <td style="font-size: 15px; padding: 0 0 5px 15px; text-align: justify; line-height: 1.8;">';
                    $content = $content . 'Order Type: ' . $order_type . '<br>';
                    $content = $content . 'Order Name: ' . $serial_name . ' ' . $order_name . '<br>';
                    $content = $content . 'Related Project: ' . $project_name . '<br>';
                    $content = $content . 'Submission Time: ' . date('Y/m/d h:i:s a', time()) . '<br>';
                    $content = $content . 'Submitter: ' . $name . '<br>';
                    $content = $content . 'Comment: ' . $remark . '';
                    $content = $content . '
                </td>
            </tr>
            </tbody>
        </table>
        <table style="margin-left: 15px; width: 96%;">
            <tbody>
            <tr>
                <td colspan="2"
                    style="background-color: #DFEAEA; border: 2px solid #94BABB; border-bottom: 1px solid #94BABB; padding: 8px; font-size: 14px; font-weight: 600; text-align: center; border-top-left-radius: 9px; border-top-right-radius: 9px;">
                    Affected Items
                </td>
            </tr>
            <tr>
                <td style="border-left: 2px solid #94BABB; border-bottom: 1px solid #94BABB; padding: 8px; font-size: 14px; font-weight: 600; text-align: center; width: 280px;">
                    #
                </td>

                <td style="border-left: 1px solid #94BABB; border-bottom: 1px solid #94BABB; border-right: 2px solid #94BABB; padding: 8px; font-size: 14px; font-weight: 600; text-align: center; width: 440px;">
                    Product Code
                </td>
            </tr>
            ';

            /* 除了最後一個產品的其他產品 */
            $i = 0;
            for($i=0; $i<count($items)-1; $i++)
            {
                $content = $content . '
                <tr>
                    <td style="border-left: 2px solid #94BABB; border-bottom: 1px solid #94BABB; padding: 8px; font-size: 14px; text-align: center; width: 280px;">
                        ';
                        $content = $content . $items[$i]['serial_number'] . '';
                        $content = $content . '
                    </td>
                    <td style="border-left: 1px solid #94BABB; border-bottom: 1px solid #94BABB; border-right: 2px solid #94BABB; padding: 8px; font-size: 14px; text-align: center; width: 440px;">
                        ';
                        $content = $content . $items[$i]['code'] . '';
                        $content = $content . '
                    </td>
                </tr>
                ';
            }

            /* 最後一個產品 */
            $content = $content . '
            <tr>
                <td style="border-left: 2px solid #94BABB; border-bottom: 2px solid #94BABB; padding: 8px; font-size: 14px; text-align: center; width: 280px; border-bottom-left-radius: 9px;">
                    ';
                    $content = $content . $items[$i]['serial_number'] . '';
                    $content = $content . '
                </td>
                <td style="border-left: 1px solid #94BABB; border-bottom: 2px solid #94BABB; border-right: 2px solid #94BABB; padding: 8px; font-size: 14px; text-align: center; width: 440px; border-bottom-right-radius: 9px;">
                    ';
                    $content = $content . $items[$i]['code'] . '';
                    $content = $content . '
                </td>
            </tr>
            ';


        $content = $content . '
            </tbody>
                </table>
                <hr style="margin-top: 45px;">
                <table style="width: 100%;">
                    <tbody>
                    <tr>
                        <td style="font-size: 16px; padding: 5px 0 0 5px; line-height: 1.5;">
                            Please click this link to view the target webpage: ';
                            $content = $content . '<a href="' . $url . '">' . $url . '</a> ';
                            $content = $content . '
                        </td>
                    </tr>
                    </tbody>
                </table>
            </div>
            </body>
            </html>';

    $mail->MsgHTML($content);
    if($mail->Send()) {
        logMail($receiver, $content);
        return true;
//        echo "Error while sending Email.";
//        var_dump($mail);
    } else {
        logMail($receiver, $mail->ErrorInfo . $content);
        return false;
//        echo "Email sent successfully";
    }

}


function order_stock_delievery_notification($name, $access,  $access_cc, $project_name, $serial_name, $order_name, $order_type, $remark, $action, $items, $od_id, $type)
{
    $conf = new Conf();

    $mail = new PHPMailer();
    $mail->IsSMTP();
    $mail->Mailer = "smtp";
    $mail->CharSet = 'UTF-8';
    $mail->Encoding = 'base64';

    // $mail->SMTPDebug  = 0;
    // $mail->SMTPAuth   = true;
    // $mail->SMTPSecure = "ssl";
    // $mail->Port       = 465;
    // $mail->SMTPKeepAlive = true;
    // $mail->Host       = $conf::$mail_host;
    // $mail->Username   = $conf::$mail_username;
    // $mail->Password   = $conf::$mail_password;

    $mail = SetupMail($mail, $conf);


    $mail->IsHTML(true);
    
    $receiver = "";
    $cc = "";
    $assignee = [];


    $mail->SetFrom("feliix.it@gmail.com", "Feliix.System");
    $mail->AddReplyTo("feliix.it@gmail.com", "Feliix.System");

    $mail->Subject = "";

    $header = '';
    $url = "";


    if($action == 'edit_delivery')
    {
        //收件人：角色1, 角色3, 職位為Sales Manager的使用者, 角色4, 角色5
        $_list = explode(",", $access);
        foreach($_list as &$c_list)
        {
            $notifior = GetAccessNotifiers($c_list, $serial_name);
            foreach($notifior as &$list)
            {
                $receiver .= $list["username"] . ", ";
                $mail->AddAddress($list["email"], $list["username"]);
            }
        }
        $receiver = rtrim($receiver, ", ");

        $notifior = GetChargeNotifiersByTitle('Sales Manager');
        foreach($notifior as &$list)
        {
            $mail->AddAddress($list["email"], $list["username"]);
        }

        // CC收件人：執行動作的人
        $notifior = GetAccessNotifiersByName($name, "");
        foreach($notifior as &$list)
        {
            $receiver = $list["username"];
            $mail->AddCC($list["email"], $list["username"]);
        }

        $receiver = "all";

        $mail->Subject = 'Inventory info for items of "' . $order_type . ': ' . $serial_name . '" is updated';
        $header = $name . ' updated the inventory info for items of "' . $order_type . ': ' . $serial_name . '". Please check details below:';
        $url = "https://feliix.myvnc.com/order_taiwan_" . $type . "_p3?id=" . $od_id;
        
    }



    $content = '<!DOCTYPE html>
    <html lang="en">
    <head>
        <meta http-equiv="content-type" content="text/html; charset=utf-8"/>
    </head>
    <body>

    <div style="width: 766px; padding: 25px 70px 20px 70px; border: 2px solid rgb(230,230,230); color: black;">

        <table style="width: 100%;">
            <tbody>
            <tr>
                <td style="font-size: 16px; padding: 10px 0 10px 5px;">';
                    $content = $content . "Dear " . $receiver . ",";
                    $content = $content . '
                </td>
            </tr>
            <tr>
                <td style="font-size: 16px; padding: 0 0 20px 5px; text-align: justify;">';
                    $content = $content . $header;
                    $content = $content . '
                </td>
            </tr>

            <tr>
                <td style="font-size: 15px; padding: 0 0 5px 15px; text-align: justify; line-height: 1.8;">';
                    $content = $content . 'Order Type: ' . $order_type . '<br>';
                    $content = $content . 'Order Name: ' . $serial_name . ' ' . $order_name . '<br>';
                    $content = $content . 'Related Project: ' . $project_name . '<br>';
                    $content = $content . 'Submission Time: ' . date('Y/m/d h:i:s a', time()) . '<br>';
                    $content = $content . 'Submitter: ' . $name . '<br>';
                    $content = $content . 'Comment: ' . $remark . '';
                    $content = $content . '
                </td>
            </tr>
            </tbody>
        </table>
        <table style="margin-left: 15px; width: 96%;">
            <tbody>
            <tr>
                <td colspan="2"
                    style="background-color: #DFEAEA; border: 2px solid #94BABB; border-bottom: 1px solid #94BABB; padding: 8px; font-size: 14px; font-weight: 600; text-align: center; border-top-left-radius: 9px; border-top-right-radius: 9px;">
                    Affected Items
                </td>
            </tr>
            <tr>
                <td style="border-left: 2px solid #94BABB; border-bottom: 1px solid #94BABB; padding: 8px; font-size: 14px; font-weight: 600; text-align: center; width: 280px;">
                    #
                </td>

                <td style="border-left: 1px solid #94BABB; border-bottom: 1px solid #94BABB; border-right: 2px solid #94BABB; padding: 8px; font-size: 14px; font-weight: 600; text-align: center; width: 440px;">
                    Product Code
                </td>
            </tr>
            ';

            /* 除了最後一個產品的其他產品 */
            $i = 0;
            for($i=0; $i<count($items)-1; $i++)
            {
                $content = $content . '
                <tr>
                    <td style="border-left: 2px solid #94BABB; border-bottom: 1px solid #94BABB; padding: 8px; font-size: 14px; text-align: center; width: 280px;">
                        ';
                        $content = $content . $items[$i]['serial_number'] . '';
                        $content = $content . '
                    </td>
                    <td style="border-left: 1px solid #94BABB; border-bottom: 1px solid #94BABB; border-right: 2px solid #94BABB; padding: 8px; font-size: 14px; text-align: center; width: 440px;">
                        ';
                        $content = $content . $items[$i]['code'] . '';
                        $content = $content . '
                    </td>
                </tr>
                ';
            }

            /* 最後一個產品 */
            $content = $content . '
            <tr>
                <td style="border-left: 2px solid #94BABB; border-bottom: 2px solid #94BABB; padding: 8px; font-size: 14px; text-align: center; width: 280px; border-bottom-left-radius: 9px;">
                    ';
                    $content = $content . $items[$i]['serial_number'] . '';
                    $content = $content . '
                </td>
                <td style="border-left: 1px solid #94BABB; border-bottom: 2px solid #94BABB; border-right: 2px solid #94BABB; padding: 8px; font-size: 14px; text-align: center; width: 440px; border-bottom-right-radius: 9px;">
                    ';
                    $content = $content . $items[$i]['code'] . '';
                    $content = $content . '
                </td>
            </tr>
            ';


        $content = $content . '
            </tbody>
                </table>
                <hr style="margin-top: 45px;">
                <table style="width: 100%;">
                    <tbody>
                    <tr>
                        <td style="font-size: 16px; padding: 5px 0 0 5px; line-height: 1.5;">
                            Please click this link to view the target webpage: ';
                            $content = $content . '<a href="' . $url . '">' . $url . '</a> ';
                            $content = $content . '
                        </td>
                    </tr>
                    </tbody>
                </table>
            </div>
            </body>
            </html>';

    $mail->MsgHTML($content);
    if($mail->Send()) {
        logMail($receiver, $content);
        return true;
//        echo "Error while sending Email.";
//        var_dump($mail);
    } else {
        logMail($receiver, $mail->ErrorInfo . $content);
        return false;
//        echo "Email sent successfully";
    }

}

function order_notification03($name, $access,  $access_cc, $project_name, $serial_name, $order_name, $order_type, $remark, $action, $items, $od_id)
{
    $conf = new Conf();

    $mail = new PHPMailer();
    $mail->IsSMTP();
    $mail->Mailer = "smtp";
    $mail->CharSet = 'UTF-8';
    $mail->Encoding = 'base64';

    // $mail->SMTPDebug  = 0;
    // $mail->SMTPAuth   = true;
    // $mail->SMTPSecure = "ssl";
    // $mail->Port       = 465;
    // $mail->SMTPKeepAlive = true;
    // $mail->Host       = $conf::$mail_host;
    // $mail->Username   = $conf::$mail_username;
    // $mail->Password   = $conf::$mail_password;

    $mail = SetupMail($mail, $conf);


    $mail->IsHTML(true);

    $receiver = "";
    $cc = "";
    $assignee = [];

    $_list = explode(",", $access);
    foreach($_list as &$c_list)
    {
        $notifior = GetAccessNotifiers($c_list, $serial_name);
        foreach($notifior as &$list)
        {
            if($order_type == 'Order - Close Deal'){

                if($action == 'new_message_17' || $action == 'new_message_18'){

                    if($list["username"] == 'Nestor Rosales')
                    {
                        $receiver .= "Aiza Eisma" . ", ";
                        $mail->AddAddress("aiza@feliix.com", "Aiza Eisma");
                    }
                    else{
                        if($list["username"] == 'Cristina Matining')
                        {
                            $receiver .= "Alleah Belmonte" . ", ";
                            $mail->AddAddress("alleah.feliix@gmail.com", "Alleah Belmonte");
                        }
                        else{
                            $receiver .= $list["username"] . ", ";
                            $mail->AddAddress($list["email"], $list["username"]);
                        }
                    }
                }
                else{
                    if($list["username"] == 'Nestor Rosales')
                    {
                        $receiver .= "Aiza Eisma" . ", ";
                        $mail->AddAddress("aiza@feliix.com", "Aiza Eisma");
                        $receiver .= $list["username"] . ", ";
                        $mail->AddAddress($list["email"], $list["username"]);
                    }
                    else{
                        if($list["username"] == 'Cristina Matining')
                        {
                            $receiver .= "Alleah Belmonte" . ", ";
                            $mail->AddAddress("alleah.feliix@gmail.com", "Alleah Belmonte");
                            $receiver .= $list["username"] . ", ";
                            $mail->AddAddress($list["email"], $list["username"]);
                        }
                        else{
                            $receiver .= $list["username"] . ", ";
                            $mail->AddAddress($list["email"], $list["username"]);
                        }
                    }
                }
            }
            else
            {
                $receiver .= $list["username"] . ", ";
                $mail->AddAddress($list["email"], $list["username"]);
            }
        }
    }

    $receiver = rtrim($receiver, ", ");

    // explore cc into array
    $cc_list = explode(",", $access_cc);
    foreach($cc_list as &$c_list)
    {
        $notifior = GetAccessNotifiers($c_list, $serial_name);
        foreach($notifior as &$list)
        {
            $cc = $list["username"];
            $mail->AddCC($list["email"], $list["username"]);
        }
    }


    $mail->SetFrom("feliix.it@gmail.com", "Feliix.System");
    $mail->AddReplyTo("feliix.it@gmail.com", "Feliix.System");

    $mail->Subject = "";

    $url = "";

    if($action == 'new_message_17')
    {
        $item_sn = $items['serial_number'];

        $notifior = GetAccessNotifiersByName($name, $serial_name);
        foreach($notifior as &$list)
        {
            $receiver = $list["username"];
            $mail->AddCC($list["email"], $list["username"]);
        }

        $receiver = "All";

        $mail->Subject = 'New message was created for item #' . $item_sn . ' in "' . $order_type . ': ' . $serial_name . '" ';
        $header = $name . ' created a new message for item #' . $item_sn . ' in "' . $order_type . ': ' . $serial_name . '". Please check details below:';
        $url = "https://feliix.myvnc.com/order_taiwan_p1?id=" . $od_id;

    }

    if($action == 'new_message_18')
    {
        $item_sn = $items['serial_number'];

        $notifior = GetAccessNotifiersByName($name, $serial_name);
        foreach($notifior as &$list)
        {
            $receiver = $list["username"];
            $mail->AddCC($list["email"], $list["username"]);
        }

        $receiver = "All";

        $mail->Subject = 'Existing message was deleted for item #' . $item_sn . ' in "' . $order_type . ': ' . $serial_name . '" ';
        $header = $name . ' deleted an existing message for item #' . $item_sn . ' in "' . $order_type . ': ' . $serial_name . '". Please check details below:';
        $url = "https://feliix.myvnc.com/order_taiwan_p1?id=" . $od_id;

    }

    if($action == 'new_message_19')
    {
        $item_sn = $items['serial_number'];

        $notifior = GetAccessNotifiersByName($name, $serial_name);
        foreach($notifior as &$list)
        {
            $receiver = $list["username"];
            $mail->AddCC($list["email"], $list["username"]);
        }

        $receiver = "All";

        $mail->Subject = 'New message was created for item #' . $item_sn . ' in "' . $order_type . ': ' . $serial_name . '" ';
        $header = $name . ' created a new message for item #' . $item_sn . ' in "' . $order_type . ': ' . $serial_name . '". Please check details below:';
        $url = "https://feliix.myvnc.com/order_taiwan_p2?id=" . $od_id;

    }

    if($action == 'new_message_20')
    {
        $item_sn = $items['serial_number'];

        $notifior = GetAccessNotifiersByName($name, $serial_name);
        foreach($notifior as &$list)
        {
            $receiver = $list["username"];
            $mail->AddCC($list["email"], $list["username"]);
        }

        $receiver = "All";

        $mail->Subject = 'Existing message was deleted for item #' . $item_sn . ' in "' . $order_type . ': ' . $serial_name . '" ';
        $header = $name . ' deleted an existing message for item #' . $item_sn . ' in "' . $order_type . ': ' . $serial_name . '". Please check details below:';
        $url = "https://feliix.myvnc.com/order_taiwan_p2?id=" . $od_id;

    }

    if($action == 'new_message_21')
    {
        $item_sn = $items['serial_number'];

        $notifior = GetAccessNotifiersByName($name, $serial_name);
        foreach($notifior as &$list)
        {
            $receiver = $list["username"];
            $mail->AddCC($list["email"], $list["username"]);
        }

        $receiver = "All";

        $mail->Subject = 'New message was created for item #' . $item_sn . ' in "' . $order_type . ': ' . $serial_name . '" ';
        $header = $name . ' created a new message for item #' . $item_sn . ' in "' . $order_type . ': ' . $serial_name . '". Please check details below:';
        $url = "https://feliix.myvnc.com/order_taiwan_p3?id=" . $od_id;

    }

    if($action == 'new_message_22')
    {
        $item_sn = $items['serial_number'];

        $notifior = GetAccessNotifiersByName($name, $serial_name);
        foreach($notifior as &$list)
        {
            $receiver = $list["username"];
            $mail->AddCC($list["email"], $list["username"]);
        }

        $receiver = "All";

        $mail->Subject = 'Existing message was deleted for item #' . $item_sn . ' in "' . $order_type . ': ' . $serial_name . '" ';
        $header = $name . ' deleted an existing message for item #' . $item_sn . ' in "' . $order_type . ': ' . $serial_name . '". Please check details below:';
        $url = "https://feliix.myvnc.com/order_taiwan_p3?id=" . $od_id;

    }


    $content = '<!DOCTYPE html>
    <html lang="en">
    <head>
        <meta http-equiv="content-type" content="text/html; charset=utf-8"/>
    </head>
    <body>

    <div style="width: 766px; padding: 25px 70px 20px 70px; border: 2px solid rgb(230,230,230); color: black;">

        <table style="width: 100%;">
            <tbody>
            <tr>
                <td style="font-size: 16px; padding: 10px 0 10px 5px;">';
                    $content = $content . "Dear " . $receiver . ",";
                    $content = $content . '
                </td>
            </tr>
            <tr>
                <td style="font-size: 16px; padding: 0 0 20px 5px; text-align: justify;">';
                    $content = $content . $header;
                    $content = $content . '
                </td>
            </tr>

            <tr>
                <td style="font-size: 15px; padding: 0 0 5px 15px; text-align: justify; line-height: 1.8;">';
                    $content = $content . 'Order Type: ' . $order_type . '<br>';
                    $content = $content . 'Order Name: ' . $serial_name . ' ' . $order_name . '<br>';
                    $content = $content . 'Related Project: ' . $project_name . '<br>';
                    $content = $content . 'Submission Time: ' . date('Y/m/d h:i:s a', time()) . '<br>';
                    $content = $content . 'Submitter: ' . $name . '<br>';
                    $content = $content . '
                </td>
            </tr>
            </tbody>
        </table>
        <table style="margin-left: 15px; width: 96%;">
            <tbody>
            <tr>
                <td colspan="3"
                    style="background-color: #DFEAEA; border: 2px solid #94BABB; border-bottom: 1px solid #94BABB; padding: 8px; font-size: 14px; font-weight: 600; text-align: center; border-top-left-radius: 9px; border-top-right-radius: 9px;">
                    Affected Item
                </td>
            </tr>
            <tr>
                <td style="border-left: 2px solid #94BABB; border-bottom: 1px solid #94BABB; padding: 8px; font-size: 14px; font-weight: 600; text-align: center; width: 160px;">
                    #
                </td>

                <td style="border-left: 1px solid #94BABB; border-bottom: 1px solid #94BABB; border-right: padding: 8px; font-size: 14px; font-weight: 600; text-align: center; width: 220px;">
                    Product Code
                </td>

                <td style="border-left: 1px solid #94BABB; border-bottom: 1px solid #94BABB; border-right: 2px solid #94BABB; padding: 8px; font-size: 14px; font-weight: 600; text-align: center; width: 340px;">
                    Message
                </td>
            </tr>
            ';

           /* 表格裡面只會有一個產品 */
            $content = $content . '
            <tr>
                <td style="border-left: 2px solid #94BABB; border-bottom: 2px solid #94BABB; padding: 8px; font-size: 14px; text-align: center; width: 160px; border-bottom-left-radius: 9px;">
                    ';
                    $content = $content . $items['serial_number'] . '';
                    $content = $content . '
                </td>
                <td style="border-left: 1px solid #94BABB; border-bottom: 2px solid #94BABB; padding: 8px; font-size: 14px; text-align: center; width: 220px;" >
                    ';
                    $content = $content . $items['code'] . '';
                    $content = $content . '
                </td>
                <td style="border-left: 1px solid #94BABB; border-bottom: 2px solid #94BABB; border-right: 2px solid #94BABB; padding: 8px; font-size: 14px; text-align: center; width: 340px; border-bottom-right-radius: 9px;">
                    ';
                    $content = $content . $remark . '';
                    $content = $content . '
                </td>
            </tr>
            ';


        $content = $content . '
            </tbody>
                </table>
                <hr style="margin-top: 45px;">
                <table style="width: 100%;">
                    <tbody>
                    <tr>
                        <td style="font-size: 16px; padding: 5px 0 0 5px; line-height: 1.5;">
                            Please click this link to view the target webpage: ';
                            $content = $content . '<a href="' . $url . '">' . $url . '</a> ';
                            $content = $content . '
                        </td>
                    </tr>
                    </tbody>
                </table>
            </div>
            </body>
            </html>';

    $mail->MsgHTML($content);
    if($mail->Send()) {
        logMail($receiver, $content);
        return true;
//        echo "Error while sending Email.";
//        var_dump($mail);
    } else {
        logMail($receiver, $mail->ErrorInfo . $content);
        return false;
//        echo "Email sent successfully";
    }

}


function mockup_notification03($name, $access,  $access_cc, $project_name, $serial_name, $order_name, $order_type, $remark, $action, $items, $od_id)
{
    $conf = new Conf();

    $mail = new PHPMailer();
    $mail->IsSMTP();
    $mail->Mailer = "smtp";
    $mail->CharSet = 'UTF-8';
    $mail->Encoding = 'base64';

    // $mail->SMTPDebug  = 0;
    // $mail->SMTPAuth   = true;
    // $mail->SMTPSecure = "ssl";
    // $mail->Port       = 465;
    // $mail->SMTPKeepAlive = true;
    // $mail->Host       = $conf::$mail_host;
    // $mail->Username   = $conf::$mail_username;
    // $mail->Password   = $conf::$mail_password;

    $mail = SetupMail($mail, $conf);


    $mail->IsHTML(true);
    
    $receiver = "";
    $cc = "";
    $assignee = [];

    $_list = explode(",", $access);
    foreach($_list as &$c_list)
    {
        $notifior = GetAccessNotifiers($c_list, $serial_name);
        foreach($notifior as &$list)
        {
            $receiver .= $list["username"] . ", ";
            $mail->AddAddress($list["email"], $list["username"]);
        }
    }

    $receiver = rtrim($receiver, ", ");
    
    // explore cc into array
    $cc_list = explode(",", $access_cc);
    foreach($cc_list as &$c_list)
    {
        $notifior = GetAccessNotifiers($c_list, $serial_name);
        foreach($notifior as &$list)
        {
            $cc = $list["username"];
            $mail->AddCC($list["email"], $list["username"]);
        }
    }
    

    $mail->SetFrom("feliix.it@gmail.com", "Feliix.System");
    $mail->AddReplyTo("feliix.it@gmail.com", "Feliix.System");

    $mail->Subject = "";

    $url = "";

    if($action == 'new_message_17')
    {
        $item_sn = $items['serial_number'];

        $notifior = GetAccessNotifiersByName($name, $serial_name);
        foreach($notifior as &$list)
        {
            $receiver = $list["username"];
            $mail->AddCC($list["email"], $list["username"]);
        }

        $receiver = "All";

        $mail->Subject = 'New message was created for item #' . $item_sn . ' in "' . $order_type . ': ' . $serial_name . '" ';
        $header = $name . ' created a new message for item #' . $item_sn . ' in "' . $order_type . ': ' . $serial_name . '". Please check details below:';
        $url = "https://feliix.myvnc.com/order_taiwan_p1?id=" . $od_id;
        
    }

    if($action == 'new_message_18')
    {
        $item_sn = $items['serial_number'];

        $notifior = GetAccessNotifiersByName($name, $serial_name);
        foreach($notifior as &$list)
        {
            $receiver = $list["username"];
            $mail->AddCC($list["email"], $list["username"]);
        }

        $receiver = "All";

        $mail->Subject = 'Existing message was deleted for item #' . $item_sn . ' in "' . $order_type . ': ' . $serial_name . '" ';
        $header = $name . ' deleted an existing message for item #' . $item_sn . ' in "' . $order_type . ': ' . $serial_name . '". Please check details below:';
        $url = "https://feliix.myvnc.com/order_taiwan_p1?id=" . $od_id;
        
    }

    if($action == 'new_message_19')
    {
        $item_sn = $items['serial_number'];

        $notifior = GetAccessNotifiersByName($name, $serial_name);
        foreach($notifior as &$list)
        {
            $receiver = $list["username"];
            $mail->AddCC($list["email"], $list["username"]);
        }

        $receiver = "All";

        $mail->Subject = 'New message was created for item #' . $item_sn . ' in "' . $order_type . ': ' . $serial_name . '" ';
        $header = $name . ' created a new message for item #' . $item_sn . ' in "' . $order_type . ': ' . $serial_name . '". Please check details below:';
        $url = "https://feliix.myvnc.com/order_taiwan_p2?id=" . $od_id;
        
    }

    if($action == 'new_message_20')
    {
        $item_sn = $items['serial_number'];

        $notifior = GetAccessNotifiersByName($name, $serial_name);
        foreach($notifior as &$list)
        {
            $receiver = $list["username"];
            $mail->AddCC($list["email"], $list["username"]);
        }

        $receiver = "All";

        $mail->Subject = 'Existing message was deleted for item #' . $item_sn . ' in "' . $order_type . ': ' . $serial_name . '" ';
        $header = $name . ' deleted an existing message for item #' . $item_sn . ' in "' . $order_type . ': ' . $serial_name . '". Please check details below:';
        $url = "https://feliix.myvnc.com/order_taiwan_p2?id=" . $od_id;
        
    }

    if($action == 'new_message_21')
    {
        $item_sn = $items['serial_number'];

        $notifior = GetAccessNotifiersByName($name, $serial_name);
        foreach($notifior as &$list)
        {
            $receiver = $list["username"];
            $mail->AddCC($list["email"], $list["username"]);
        }

        $receiver = "All";

        $mail->Subject = 'New message was created for item #' . $item_sn . ' in "' . $order_type . ': ' . $serial_name . '" ';
        $header = $name . ' created a new message for item #' . $item_sn . ' in "' . $order_type . ': ' . $serial_name . '". Please check details below:';
        $url = "https://feliix.myvnc.com/order_taiwan_p3?id=" . $od_id;
        
    }

    if($action == 'new_message_22')
    {
        $item_sn = $items['serial_number'];

        $notifior = GetAccessNotifiersByName($name, $serial_name);
        foreach($notifior as &$list)
        {
            $receiver = $list["username"];
            $mail->AddCC($list["email"], $list["username"]);
        }

        $receiver = "All";

        $mail->Subject = 'Existing message was deleted for item #' . $item_sn . ' in "' . $order_type . ': ' . $serial_name . '" ';
        $header = $name . ' deleted an existing message for item #' . $item_sn . ' in "' . $order_type . ': ' . $serial_name . '". Please check details below:';
        $url = "https://feliix.myvnc.com/order_taiwan_p3?id=" . $od_id;
        
    }


    $content = '<!DOCTYPE html>
    <html lang="en">
    <head>
        <meta http-equiv="content-type" content="text/html; charset=utf-8"/>
    </head>
    <body>

    <div style="width: 766px; padding: 25px 70px 20px 70px; border: 2px solid rgb(230,230,230); color: black;">

        <table style="width: 100%;">
            <tbody>
            <tr>
                <td style="font-size: 16px; padding: 10px 0 10px 5px;">';
                    $content = $content . "Dear " . $receiver . ",";
                    $content = $content . '
                </td>
            </tr>
            <tr>
                <td style="font-size: 16px; padding: 0 0 20px 5px; text-align: justify;">';
                    $content = $content . $header;
                    $content = $content . '
                </td>
            </tr>

            <tr>
                <td style="font-size: 15px; padding: 0 0 5px 15px; text-align: justify; line-height: 1.8;">';
                    $content = $content . 'Order Type: ' . $order_type . '<br>';
                    $content = $content . 'Order Name: ' . $serial_name . ' ' . $order_name . '<br>';
                    $content = $content . 'Related Project: ' . $project_name . '<br>';
                    $content = $content . 'Submission Time: ' . date('Y/m/d h:i:s a', time()) . '<br>';
                    $content = $content . 'Submitter: ' . $name . '<br>';
                    $content = $content . '
                </td>
            </tr>
            </tbody>
        </table>
        <table style="margin-left: 15px; width: 96%;">
            <tbody>
            <tr>
                <td colspan="3"
                    style="background-color: #DFEAEA; border: 2px solid #94BABB; border-bottom: 1px solid #94BABB; padding: 8px; font-size: 14px; font-weight: 600; text-align: center; border-top-left-radius: 9px; border-top-right-radius: 9px;">
                    Affected Item
                </td>
            </tr>
            <tr>
                <td style="border-left: 2px solid #94BABB; border-bottom: 1px solid #94BABB; padding: 8px; font-size: 14px; font-weight: 600; text-align: center; width: 160px;">
                    #
                </td>

                <td style="border-left: 1px solid #94BABB; border-bottom: 1px solid #94BABB; border-right: padding: 8px; font-size: 14px; font-weight: 600; text-align: center; width: 220px;">
                    Product Code
                </td>

                <td style="border-left: 1px solid #94BABB; border-bottom: 1px solid #94BABB; border-right: 2px solid #94BABB; padding: 8px; font-size: 14px; font-weight: 600; text-align: center; width: 340px;">
                    Message
                </td>
            </tr>
            ';

           /* 表格裡面只會有一個產品 */
            $content = $content . '
            <tr>
                <td style="border-left: 2px solid #94BABB; border-bottom: 2px solid #94BABB; padding: 8px; font-size: 14px; text-align: center; width: 160px; border-bottom-left-radius: 9px;">
                    ';
                    $content = $content . $items['serial_number'] . '';
                    $content = $content . '
                </td>
                <td style="border-left: 1px solid #94BABB; border-bottom: 2px solid #94BABB; padding: 8px; font-size: 14px; text-align: center; width: 220px;" >
                    ';
                    $content = $content . $items['code'] . '';
                    $content = $content . '
                </td>
                <td style="border-left: 1px solid #94BABB; border-bottom: 2px solid #94BABB; border-right: 2px solid #94BABB; padding: 8px; font-size: 14px; text-align: center; width: 340px; border-bottom-right-radius: 9px;">
                    ';
                    $content = $content . $remark . '';
                    $content = $content . '
                </td>
            </tr>
            ';


        $content = $content . '
            </tbody>
                </table>
                <hr style="margin-top: 45px;">
                <table style="width: 100%;">
                    <tbody>
                    <tr>
                        <td style="font-size: 16px; padding: 5px 0 0 5px; line-height: 1.5;">
                            Please click this link to view the target webpage: ';
                            $content = $content . '<a href="' . $url . '">' . $url . '</a> ';
                            $content = $content . '
                        </td>
                    </tr>
                    </tbody>
                </table>
            </div>
            </body>
            </html>';

    $mail->MsgHTML($content);
    if($mail->Send()) {
        logMail($receiver, $content);
        return true;
//        echo "Error while sending Email.";
//        var_dump($mail);
    } else {
        logMail($receiver, $mail->ErrorInfo . $content);
        return false;
//        echo "Email sent successfully";
    }

}


function order_type_notification03($name, $access,  $access_cc, $project_name, $serial_name, $order_name, $order_type, $remark, $action, $items, $od_id, $type)
{
    $conf = new Conf();

    $mail = new PHPMailer();
    $mail->IsSMTP();
    $mail->Mailer = "smtp";
    $mail->CharSet = 'UTF-8';
    $mail->Encoding = 'base64';

    // $mail->SMTPDebug  = 0;
    // $mail->SMTPAuth   = true;
    // $mail->SMTPSecure = "ssl";
    // $mail->Port       = 465;
    // $mail->SMTPKeepAlive = true;
    // $mail->Host       = $conf::$mail_host;
    // $mail->Username   = $conf::$mail_username;
    // $mail->Password   = $conf::$mail_password;

    $mail = SetupMail($mail, $conf);


    $mail->IsHTML(true);
    
    $receiver = "";
    $cc = "";
    $assignee = [];

    $_list = explode(",", $access);
    foreach($_list as &$c_list)
    {
        $notifior = GetAccessNotifiers($c_list, $serial_name);
        foreach($notifior as &$list)
        {
            $receiver .= $list["username"] . ", ";
            $mail->AddAddress($list["email"], $list["username"]);
        }
    }

    $receiver = rtrim($receiver, ", ");
    
    // explore cc into array
    $cc_list = explode(",", $access_cc);
    foreach($cc_list as &$c_list)
    {
        $notifior = GetAccessNotifiers($c_list, $serial_name);
        foreach($notifior as &$list)
        {
            $cc = $list["username"];
            $mail->AddCC($list["email"], $list["username"]);
        }
    }
    

    $mail->SetFrom("feliix.it@gmail.com", "Feliix.System");
    $mail->AddReplyTo("feliix.it@gmail.com", "Feliix.System");

    $mail->Subject = "";

    $url = "";

    if($action == 'new_message_17')
    {
        $item_sn = $items['serial_number'];

        $notifior = GetAccessNotifiersByName($name, $serial_name);
        foreach($notifior as &$list)
        {
            $receiver = $list["username"];
            $mail->AddCC($list["email"], $list["username"]);
        }

        $receiver = "All";

        $mail->Subject = 'New message was created for item #' . $item_sn . ' in "' . $order_type . ': ' . $serial_name . '" ';
        $header = $name . ' created a new message for item #' . $item_sn . ' in "' . $order_type . ': ' . $serial_name . '". Please check details below:';
        $url = "https://feliix.myvnc.com/order_taiwan_" . $type . "_p1?id=" . $od_id;
        
    }

    if($action == 'new_message_18')
    {
        $item_sn = $items['serial_number'];

        $notifior = GetAccessNotifiersByName($name, $serial_name);
        foreach($notifior as &$list)
        {
            $receiver = $list["username"];
            $mail->AddCC($list["email"], $list["username"]);
        }

        $receiver = "All";

        $mail->Subject = 'Existing message was deleted for item #' . $item_sn . ' in "' . $order_type . ': ' . $serial_name . '" ';
        $header = $name . ' deleted an existing message for item #' . $item_sn . ' in "' . $order_type . ': ' . $serial_name . '". Please check details below:';
        $url = "https://feliix.myvnc.com/order_taiwan_" . $type . "_p1?id=" . $od_id;
        
    }

    if($action == 'new_message_19')
    {
        $item_sn = $items['serial_number'];

        $notifior = GetAccessNotifiersByName($name, $serial_name);
        foreach($notifior as &$list)
        {
            $receiver = $list["username"];
            $mail->AddCC($list["email"], $list["username"]);
        }

        $receiver = "All";

        $mail->Subject = 'New message was created for item #' . $item_sn . ' in "' . $order_type . ': ' . $serial_name . '" ';
        $header = $name . ' created a new message for item #' . $item_sn . ' in "' . $order_type . ': ' . $serial_name . '". Please check details below:';
        $url = "https://feliix.myvnc.com/order_taiwan_" . $type . "_p2?id=" . $od_id;
        
    }

    if($action == 'new_message_20')
    {
        $item_sn = $items['serial_number'];

        $notifior = GetAccessNotifiersByName($name, $serial_name);
        foreach($notifior as &$list)
        {
            $receiver = $list["username"];
            $mail->AddCC($list["email"], $list["username"]);
        }

        $receiver = "All";

        $mail->Subject = 'Existing message was deleted for item #' . $item_sn . ' in "' . $order_type . ': ' . $serial_name . '" ';
        $header = $name . ' deleted an existing message for item #' . $item_sn . ' in "' . $order_type . ': ' . $serial_name . '". Please check details below:';
        $url = "https://feliix.myvnc.com/order_taiwan_" . $type . "_p2?id=" . $od_id;
        
    }

    if($action == 'new_message_21')
    {
        $item_sn = $items['serial_number'];

        $notifior = GetAccessNotifiersByName($name, $serial_name);
        foreach($notifior as &$list)
        {
            $receiver = $list["username"];
            $mail->AddCC($list["email"], $list["username"]);
        }

        $receiver = "All";

        $mail->Subject = 'New message was created for item #' . $item_sn . ' in "' . $order_type . ': ' . $serial_name . '" ';
        $header = $name . ' created a new message for item #' . $item_sn . ' in "' . $order_type . ': ' . $serial_name . '". Please check details below:';
        $url = "https://feliix.myvnc.com/order_taiwan_" . $type . "_p3?id=" . $od_id;
        
    }

    if($action == 'new_message_22')
    {
        $item_sn = $items['serial_number'];

        $notifior = GetAccessNotifiersByName($name, $serial_name);
        foreach($notifior as &$list)
        {
            $receiver = $list["username"];
            $mail->AddCC($list["email"], $list["username"]);
        }

        $receiver = "All";

        $mail->Subject = 'Existing message was deleted for item #' . $item_sn . ' in "' . $order_type . ': ' . $serial_name . '" ';
        $header = $name . ' deleted an existing message for item #' . $item_sn . ' in "' . $order_type . ': ' . $serial_name . '". Please check details below:';
        $url = "https://feliix.myvnc.com/order_taiwan_" . $type . "_p3?id=" . $od_id;
        
    }


    $content = '<!DOCTYPE html>
    <html lang="en">
    <head>
        <meta http-equiv="content-type" content="text/html; charset=utf-8"/>
    </head>
    <body>

    <div style="width: 766px; padding: 25px 70px 20px 70px; border: 2px solid rgb(230,230,230); color: black;">

        <table style="width: 100%;">
            <tbody>
            <tr>
                <td style="font-size: 16px; padding: 10px 0 10px 5px;">';
                    $content = $content . "Dear " . $receiver . ",";
                    $content = $content . '
                </td>
            </tr>
            <tr>
                <td style="font-size: 16px; padding: 0 0 20px 5px; text-align: justify;">';
                    $content = $content . $header;
                    $content = $content . '
                </td>
            </tr>

            <tr>
                <td style="font-size: 15px; padding: 0 0 5px 15px; text-align: justify; line-height: 1.8;">';
                    $content = $content . 'Order Type: ' . $order_type . '<br>';
                    $content = $content . 'Order Name: ' . $serial_name . ' ' . $order_name . '<br>';
                    $content = $content . 'Related Task: ' . $project_name . '<br>';
                    $content = $content . 'Submission Time: ' . date('Y/m/d h:i:s a', time()) . '<br>';
                    $content = $content . 'Submitter: ' . $name . '<br>';
                    $content = $content . '
                </td>
            </tr>
            </tbody>
        </table>
        <table style="margin-left: 15px; width: 96%;">
            <tbody>
            <tr>
                <td colspan="3"
                    style="background-color: #DFEAEA; border: 2px solid #94BABB; border-bottom: 1px solid #94BABB; padding: 8px; font-size: 14px; font-weight: 600; text-align: center; border-top-left-radius: 9px; border-top-right-radius: 9px;">
                    Affected Item
                </td>
            </tr>
            <tr>
                <td style="border-left: 2px solid #94BABB; border-bottom: 1px solid #94BABB; padding: 8px; font-size: 14px; font-weight: 600; text-align: center; width: 160px;">
                    #
                </td>

                <td style="border-left: 1px solid #94BABB; border-bottom: 1px solid #94BABB; border-right: padding: 8px; font-size: 14px; font-weight: 600; text-align: center; width: 220px;">
                    Product Code
                </td>

                <td style="border-left: 1px solid #94BABB; border-bottom: 1px solid #94BABB; border-right: 2px solid #94BABB; padding: 8px; font-size: 14px; font-weight: 600; text-align: center; width: 340px;">
                    Message
                </td>
            </tr>
            ';

           /* 表格裡面只會有一個產品 */
            $content = $content . '
            <tr>
                <td style="border-left: 2px solid #94BABB; border-bottom: 2px solid #94BABB; padding: 8px; font-size: 14px; text-align: center; width: 160px; border-bottom-left-radius: 9px;">
                    ';
                    $content = $content . $items['serial_number'] . '';
                    $content = $content . '
                </td>
                <td style="border-left: 1px solid #94BABB; border-bottom: 2px solid #94BABB; padding: 8px; font-size: 14px; text-align: center; width: 220px;" >
                    ';
                    $content = $content . $items['code'] . '';
                    $content = $content . '
                </td>
                <td style="border-left: 1px solid #94BABB; border-bottom: 2px solid #94BABB; border-right: 2px solid #94BABB; padding: 8px; font-size: 14px; text-align: center; width: 340px; border-bottom-right-radius: 9px;">
                    ';
                    $content = $content . $remark . '';
                    $content = $content . '
                </td>
            </tr>
            ';


        $content = $content . '
            </tbody>
                </table>
                <hr style="margin-top: 45px;">
                <table style="width: 100%;">
                    <tbody>
                    <tr>
                        <td style="font-size: 16px; padding: 5px 0 0 5px; line-height: 1.5;">
                            Please click this link to view the target webpage: ';
                            $content = $content . '<a href="' . $url . '">' . $url . '</a> ';
                            $content = $content . '
                        </td>
                    </tr>
                    </tbody>
                </table>
            </div>
            </body>
            </html>';

    $mail->MsgHTML($content);
    if($mail->Send()) {
        logMail($receiver, $content);
        return true;
//        echo "Error while sending Email.";
//        var_dump($mail);
    } else {
        logMail($receiver, $mail->ErrorInfo . $content);
        return false;
//        echo "Email sent successfully";
    }

}

function order_notification03Access7($name, $access,  $access_cc, $project_name, $serial_name, $order_name, $order_type, $remark, $action, $items, $od_id, $access7)
{
    $conf = new Conf();

    $mail = new PHPMailer();
    $mail->IsSMTP();
    $mail->Mailer = "smtp";
    $mail->CharSet = 'UTF-8';
    $mail->Encoding = 'base64';

    // $mail->SMTPDebug  = 0;
    // $mail->SMTPAuth   = true;
    // $mail->SMTPSecure = "ssl";
    // $mail->Port       = 465;
    // $mail->SMTPKeepAlive = true;
    // $mail->Host       = $conf::$mail_host;
    // $mail->Username   = $conf::$mail_username;
    // $mail->Password   = $conf::$mail_password;

    $mail = SetupMail($mail, $conf);


    $mail->IsHTML(true);

    $receiver = "";
    $cc = "";
    $assignee = [];

    $_list = explode(",", $access);
    foreach($_list as &$c_list)
    {
        $notifior = GetAccessNotifiers($c_list, $serial_name);
        foreach($notifior as &$list)
        {
            if($order_type == 'Order - Close Deal'){

                if($action == 'new_message_17' || $action == 'new_message_18'){

                    if($list["username"] == 'Nestor Rosales')
                    {
                        $receiver .= "Aiza Eisma" . ", ";
                        $mail->AddAddress("aiza@feliix.com", "Aiza Eisma");
                    }
                    else{
                        if($list["username"] == 'Cristina Matining')
                        {
                            $receiver .= "Alleah Belmonte" . ", ";
                            $mail->AddAddress("alleah.feliix@gmail.com", "Alleah Belmonte");
                        }
                        else{
                            $receiver .= $list["username"] . ", ";
                            $mail->AddAddress($list["email"], $list["username"]);
                        }
                    }
                }
                else{
                    if($list["username"] == 'Nestor Rosales')
                    {
                        $receiver .= "Aiza Eisma" . ", ";
                        $mail->AddAddress("aiza@feliix.com", "Aiza Eisma");
                        $receiver .= $list["username"] . ", ";
                        $mail->AddAddress($list["email"], $list["username"]);
                    }
                    else{
                        if($list["username"] == 'Cristina Matining')
                        {
                            $receiver .= "Alleah Belmonte" . ", ";
                            $mail->AddAddress("alleah.feliix@gmail.com", "Alleah Belmonte");
                            $receiver .= $list["username"] . ", ";
                            $mail->AddAddress($list["email"], $list["username"]);
                        }
                        else{
                            $receiver .= $list["username"] . ", ";
                            $mail->AddAddress($list["email"], $list["username"]);
                        }
                    }
                }
            }
            else
            {
                $receiver .= $list["username"] . ", ";
                $mail->AddAddress($list["email"], $list["username"]);
            }
        }
    }

    if($access7 != '')
    {
        $notifior = GetAccessNotifiersByName($access7, $serial_name);
        foreach($notifior as &$list)
        {
            $receiver .= $list["username"] . ", ";
            $mail->AddAddress($list["email"], $list["username"]);
        }
    }


    $receiver = rtrim($receiver, ", ");

    // explore cc into array
    $cc_list = explode(",", $access_cc);
    foreach($cc_list as &$c_list)
    {
        $notifior = GetAccessNotifiers($c_list, $serial_name);
        foreach($notifior as &$list)
        {
            $cc = $list["username"];
            $mail->AddCC($list["email"], $list["username"]);
        }
    }


    $mail->SetFrom("feliix.it@gmail.com", "Feliix.System");
    $mail->AddReplyTo("feliix.it@gmail.com", "Feliix.System");

    $mail->Subject = "";

    $url = "";

    if($action == 'new_message_17')
    {
        $item_sn = $items['serial_number'];

        $notifior = GetAccessNotifiersByName($name, $serial_name);
        foreach($notifior as &$list)
        {
            $receiver = $list["username"];
            $mail->AddCC($list["email"], $list["username"]);
        }

        $receiver = "All";

        $mail->Subject = 'New message was created for item #' . $item_sn . ' in "' . $order_type . ': ' . $serial_name . '" ';
        $header = $name . ' created a new message for item #' . $item_sn . ' in "' . $order_type . ': ' . $serial_name . '". Please check details below:';
        $url = "https://feliix.myvnc.com/order_taiwan_p1?id=" . $od_id;

    }

    if($action == 'new_message_18')
    {
        $item_sn = $items['serial_number'];

        $notifior = GetAccessNotifiersByName($name, $serial_name);
        foreach($notifior as &$list)
        {
            $receiver = $list["username"];
            $mail->AddCC($list["email"], $list["username"]);
        }

        $receiver = "All";

        $mail->Subject = 'Existing message was deleted for item #' . $item_sn . ' in "' . $order_type . ': ' . $serial_name . '" ';
        $header = $name . ' deleted an existing message for item #' . $item_sn . ' in "' . $order_type . ': ' . $serial_name . '". Please check details below:';
        $url = "https://feliix.myvnc.com/order_taiwan_p1?id=" . $od_id;

    }

    if($action == 'new_message_19')
    {
        $item_sn = $items['serial_number'];

        $notifior = GetAccessNotifiersByName($name, $serial_name);
        foreach($notifior as &$list)
        {
            $receiver = $list["username"];
            $mail->AddCC($list["email"], $list["username"]);
        }

        $receiver = "All";

        $mail->Subject = 'New message was created for item #' . $item_sn . ' in "' . $order_type . ': ' . $serial_name . '" ';
        $header = $name . ' created a new message for item #' . $item_sn . ' in "' . $order_type . ': ' . $serial_name . '". Please check details below:';
        $url = "https://feliix.myvnc.com/order_taiwan_p2?id=" . $od_id;

    }

    if($action == 'new_message_20')
    {
        $item_sn = $items['serial_number'];

        $notifior = GetAccessNotifiersByName($name, $serial_name);
        foreach($notifior as &$list)
        {
            $receiver = $list["username"];
            $mail->AddCC($list["email"], $list["username"]);
        }

        $receiver = "All";

        $mail->Subject = 'Existing message was deleted for item #' . $item_sn . ' in "' . $order_type . ': ' . $serial_name . '" ';
        $header = $name . ' deleted an existing message for item #' . $item_sn . ' in "' . $order_type . ': ' . $serial_name . '". Please check details below:';
        $url = "https://feliix.myvnc.com/order_taiwan_p2?id=" . $od_id;

    }

    if($action == 'new_message_21')
    {
        $item_sn = $items['serial_number'];

        $notifior = GetAccessNotifiersByName($name, $serial_name);
        foreach($notifior as &$list)
        {
            $receiver = $list["username"];
            $mail->AddCC($list["email"], $list["username"]);
        }

        $receiver = "All";

        $mail->Subject = 'New message was created for item #' . $item_sn . ' in "' . $order_type . ': ' . $serial_name . '" ';
        $header = $name . ' created a new message for item #' . $item_sn . ' in "' . $order_type . ': ' . $serial_name . '". Please check details below:';
        $url = "https://feliix.myvnc.com/order_taiwan_p3?id=" . $od_id;

    }

    if($action == 'new_message_22')
    {
        $item_sn = $items['serial_number'];

        $notifior = GetAccessNotifiersByName($name, $serial_name);
        foreach($notifior as &$list)
        {
            $receiver = $list["username"];
            $mail->AddCC($list["email"], $list["username"]);
        }

        $receiver = "All";

        $mail->Subject = 'Existing message was deleted for item #' . $item_sn . ' in "' . $order_type . ': ' . $serial_name . '" ';
        $header = $name . ' deleted an existing message for item #' . $item_sn . ' in "' . $order_type . ': ' . $serial_name . '". Please check details below:';
        $url = "https://feliix.myvnc.com/order_taiwan_p3?id=" . $od_id;

    }


    $content = '<!DOCTYPE html>
    <html lang="en">
    <head>
        <meta http-equiv="content-type" content="text/html; charset=utf-8"/>
    </head>
    <body>

    <div style="width: 766px; padding: 25px 70px 20px 70px; border: 2px solid rgb(230,230,230); color: black;">

        <table style="width: 100%;">
            <tbody>
            <tr>
                <td style="font-size: 16px; padding: 10px 0 10px 5px;">';
                    $content = $content . "Dear " . $receiver . ",";
                    $content = $content . '
                </td>
            </tr>
            <tr>
                <td style="font-size: 16px; padding: 0 0 20px 5px; text-align: justify;">';
                    $content = $content . $header;
                    $content = $content . '
                </td>
            </tr>

            <tr>
                <td style="font-size: 15px; padding: 0 0 5px 15px; text-align: justify; line-height: 1.8;">';
                    $content = $content . 'Order Type: ' . $order_type . '<br>';
                    $content = $content . 'Order Name: ' . $serial_name . ' ' . $order_name . '<br>';
                    $content = $content . 'Related Project: ' . $project_name . '<br>';
                    $content = $content . 'Submission Time: ' . date('Y/m/d h:i:s a', time()) . '<br>';
                    $content = $content . 'Submitter: ' . $name . '<br>';
                    $content = $content . '
                </td>
            </tr>
            </tbody>
        </table>
        <table style="margin-left: 15px; width: 96%;">
            <tbody>
            <tr>
                <td colspan="3"
                    style="background-color: #DFEAEA; border: 2px solid #94BABB; border-bottom: 1px solid #94BABB; padding: 8px; font-size: 14px; font-weight: 600; text-align: center; border-top-left-radius: 9px; border-top-right-radius: 9px;">
                    Affected Item
                </td>
            </tr>
            <tr>
                <td style="border-left: 2px solid #94BABB; border-bottom: 1px solid #94BABB; padding: 8px; font-size: 14px; font-weight: 600; text-align: center; width: 160px;">
                    #
                </td>

                <td style="border-left: 1px solid #94BABB; border-bottom: 1px solid #94BABB; border-right: padding: 8px; font-size: 14px; font-weight: 600; text-align: center; width: 220px;">
                    Product Code
                </td>

                <td style="border-left: 1px solid #94BABB; border-bottom: 1px solid #94BABB; border-right: 2px solid #94BABB; padding: 8px; font-size: 14px; font-weight: 600; text-align: center; width: 340px;">
                    Message
                </td>
            </tr>
            ';

           /* 表格裡面只會有一個產品 */
            $content = $content . '
            <tr>
                <td style="border-left: 2px solid #94BABB; border-bottom: 2px solid #94BABB; padding: 8px; font-size: 14px; text-align: center; width: 160px; border-bottom-left-radius: 9px;">
                    ';
                    $content = $content . $items['serial_number'] . '';
                    $content = $content . '
                </td>
                <td style="border-left: 1px solid #94BABB; border-bottom: 2px solid #94BABB; padding: 8px; font-size: 14px; text-align: center; width: 220px;" >
                    ';
                    $content = $content . $items['code'] . '';
                    $content = $content . '
                </td>
                <td style="border-left: 1px solid #94BABB; border-bottom: 2px solid #94BABB; border-right: 2px solid #94BABB; padding: 8px; font-size: 14px; text-align: center; width: 340px; border-bottom-right-radius: 9px;">
                    ';
                    $content = $content . $remark . '';
                    $content = $content . '
                </td>
            </tr>
            ';


        $content = $content . '
            </tbody>
                </table>
                <hr style="margin-top: 45px;">
                <table style="width: 100%;">
                    <tbody>
                    <tr>
                        <td style="font-size: 16px; padding: 5px 0 0 5px; line-height: 1.5;">
                            Please click this link to view the target webpage: ';
                            $content = $content . '<a href="' . $url . '">' . $url . '</a> ';
                            $content = $content . '
                        </td>
                    </tr>
                    </tbody>
                </table>
            </div>
            </body>
            </html>';

    $mail->MsgHTML($content);
    if($mail->Send()) {
        logMail($receiver, $content);
        return true;
//        echo "Error while sending Email.";
//        var_dump($mail);
    } else {
        logMail($receiver, $mail->ErrorInfo . $content);
        return false;
//        echo "Email sent successfully";
    }

}


function mockup_notification03Access7($name, $access,  $access_cc, $project_name, $serial_name, $order_name, $order_type, $remark, $action, $items, $od_id, $access7)
{
    $conf = new Conf();

    $mail = new PHPMailer();
    $mail->IsSMTP();
    $mail->Mailer = "smtp";
    $mail->CharSet = 'UTF-8';
    $mail->Encoding = 'base64';

    // $mail->SMTPDebug  = 0;
    // $mail->SMTPAuth   = true;
    // $mail->SMTPSecure = "ssl";
    // $mail->Port       = 465;
    // $mail->SMTPKeepAlive = true;
    // $mail->Host       = $conf::$mail_host;
    // $mail->Username   = $conf::$mail_username;
    // $mail->Password   = $conf::$mail_password;

    $mail = SetupMail($mail, $conf);


    $mail->IsHTML(true);
    
    $receiver = "";
    $cc = "";
    $assignee = [];

    $_list = explode(",", $access);
    foreach($_list as &$c_list)
    {
        $notifior = GetAccessNotifiers($c_list, $serial_name);
        foreach($notifior as &$list)
        {
            $receiver .= $list["username"] . ", ";
            $mail->AddAddress($list["email"], $list["username"]);
        }
    }

    if($access7 != '')
    {
        $notifior = GetAccessNotifiersByName($access7, $serial_name);
        foreach($notifior as &$list)
        {
            $receiver = $list["username"];
            $mail->AddCC($list["email"], $list["username"]);
        }
    }


    $receiver = rtrim($receiver, ", ");
    
    // explore cc into array
    $cc_list = explode(",", $access_cc);
    foreach($cc_list as &$c_list)
    {
        $notifior = GetAccessNotifiers($c_list, $serial_name);
        foreach($notifior as &$list)
        {
            $cc = $list["username"];
            $mail->AddCC($list["email"], $list["username"]);
        }
    }
    

    $mail->SetFrom("feliix.it@gmail.com", "Feliix.System");
    $mail->AddReplyTo("feliix.it@gmail.com", "Feliix.System");

    $mail->Subject = "";

    $url = "";

    if($action == 'new_message_17')
    {
        $item_sn = $items['serial_number'];

        $notifior = GetAccessNotifiersByName($name, $serial_name);
        foreach($notifior as &$list)
        {
            $receiver = $list["username"];
            $mail->AddCC($list["email"], $list["username"]);
        }

        $receiver = "All";

        $mail->Subject = 'New message was created for item #' . $item_sn . ' in "' . $order_type . ': ' . $serial_name . '" ';
        $header = $name . ' created a new message for item #' . $item_sn . ' in "' . $order_type . ': ' . $serial_name . '". Please check details below:';
        $url = "https://feliix.myvnc.com/order_taiwan_mockup_p1?id=" . $od_id;
        
    }

    if($action == 'new_message_18')
    {
        $item_sn = $items['serial_number'];

        $notifior = GetAccessNotifiersByName($name, $serial_name);
        foreach($notifior as &$list)
        {
            $receiver = $list["username"];
            $mail->AddCC($list["email"], $list["username"]);
        }

        $receiver = "All";

        $mail->Subject = 'Existing message was deleted for item #' . $item_sn . ' in "' . $order_type . ': ' . $serial_name . '" ';
        $header = $name . ' deleted an existing message for item #' . $item_sn . ' in "' . $order_type . ': ' . $serial_name . '". Please check details below:';
        $url = "https://feliix.myvnc.com/order_taiwan_mockup_p1?id=" . $od_id;
        
    }

    if($action == 'new_message_19')
    {
        $item_sn = $items['serial_number'];

        $notifior = GetAccessNotifiersByName($name, $serial_name);
        foreach($notifior as &$list)
        {
            $receiver = $list["username"];
            $mail->AddCC($list["email"], $list["username"]);
        }

        $receiver = "All";

        $mail->Subject = 'New message was created for item #' . $item_sn . ' in "' . $order_type . ': ' . $serial_name . '" ';
        $header = $name . ' created a new message for item #' . $item_sn . ' in "' . $order_type . ': ' . $serial_name . '". Please check details below:';
        $url = "https://feliix.myvnc.com/order_taiwan_mockup_p2?id=" . $od_id;
        
    }

    if($action == 'new_message_20')
    {
        $item_sn = $items['serial_number'];

        $notifior = GetAccessNotifiersByName($name, $serial_name);
        foreach($notifior as &$list)
        {
            $receiver = $list["username"];
            $mail->AddCC($list["email"], $list["username"]);
        }

        $receiver = "All";

        $mail->Subject = 'Existing message was deleted for item #' . $item_sn . ' in "' . $order_type . ': ' . $serial_name . '" ';
        $header = $name . ' deleted an existing message for item #' . $item_sn . ' in "' . $order_type . ': ' . $serial_name . '". Please check details below:';
        $url = "https://feliix.myvnc.com/order_taiwan_mockup_p2?id=" . $od_id;
        
    }

    if($action == 'new_message_21')
    {
        $item_sn = $items['serial_number'];

        $notifior = GetAccessNotifiersByName($name, $serial_name);
        foreach($notifior as &$list)
        {
            $receiver = $list["username"];
            $mail->AddCC($list["email"], $list["username"]);
        }

        $receiver = "All";

        $mail->Subject = 'New message was created for item #' . $item_sn . ' in "' . $order_type . ': ' . $serial_name . '" ';
        $header = $name . ' created a new message for item #' . $item_sn . ' in "' . $order_type . ': ' . $serial_name . '". Please check details below:';
        $url = "https://feliix.myvnc.com/order_taiwan_mockup_p3?id=" . $od_id;
        
    }

    if($action == 'new_message_22')
    {
        $item_sn = $items['serial_number'];

        $notifior = GetAccessNotifiersByName($name, $serial_name);
        foreach($notifior as &$list)
        {
            $receiver = $list["username"];
            $mail->AddCC($list["email"], $list["username"]);
        }

        $receiver = "All";

        $mail->Subject = 'Existing message was deleted for item #' . $item_sn . ' in "' . $order_type . ': ' . $serial_name . '" ';
        $header = $name . ' deleted an existing message for item #' . $item_sn . ' in "' . $order_type . ': ' . $serial_name . '". Please check details below:';
        $url = "https://feliix.myvnc.com/order_taiwan_mockup_p3?id=" . $od_id;
        
    }


    $content = '<!DOCTYPE html>
    <html lang="en">
    <head>
        <meta http-equiv="content-type" content="text/html; charset=utf-8"/>
    </head>
    <body>

    <div style="width: 766px; padding: 25px 70px 20px 70px; border: 2px solid rgb(230,230,230); color: black;">

        <table style="width: 100%;">
            <tbody>
            <tr>
                <td style="font-size: 16px; padding: 10px 0 10px 5px;">';
                    $content = $content . "Dear " . $receiver . ",";
                    $content = $content . '
                </td>
            </tr>
            <tr>
                <td style="font-size: 16px; padding: 0 0 20px 5px; text-align: justify;">';
                    $content = $content . $header;
                    $content = $content . '
                </td>
            </tr>

            <tr>
                <td style="font-size: 15px; padding: 0 0 5px 15px; text-align: justify; line-height: 1.8;">';
                    $content = $content . 'Order Type: ' . $order_type . '<br>';
                    $content = $content . 'Order Name: ' . $serial_name . ' ' . $order_name . '<br>';
                    $content = $content . 'Related Project: ' . $project_name . '<br>';
                    $content = $content . 'Submission Time: ' . date('Y/m/d h:i:s a', time()) . '<br>';
                    $content = $content . 'Submitter: ' . $name . '<br>';
                    $content = $content . '
                </td>
            </tr>
            </tbody>
        </table>
        <table style="margin-left: 15px; width: 96%;">
            <tbody>
            <tr>
                <td colspan="3"
                    style="background-color: #DFEAEA; border: 2px solid #94BABB; border-bottom: 1px solid #94BABB; padding: 8px; font-size: 14px; font-weight: 600; text-align: center; border-top-left-radius: 9px; border-top-right-radius: 9px;">
                    Affected Item
                </td>
            </tr>
            <tr>
                <td style="border-left: 2px solid #94BABB; border-bottom: 1px solid #94BABB; padding: 8px; font-size: 14px; font-weight: 600; text-align: center; width: 160px;">
                    #
                </td>

                <td style="border-left: 1px solid #94BABB; border-bottom: 1px solid #94BABB; border-right: padding: 8px; font-size: 14px; font-weight: 600; text-align: center; width: 220px;">
                    Product Code
                </td>

                <td style="border-left: 1px solid #94BABB; border-bottom: 1px solid #94BABB; border-right: 2px solid #94BABB; padding: 8px; font-size: 14px; font-weight: 600; text-align: center; width: 340px;">
                    Message
                </td>
            </tr>
            ';

           /* 表格裡面只會有一個產品 */
            $content = $content . '
            <tr>
                <td style="border-left: 2px solid #94BABB; border-bottom: 2px solid #94BABB; padding: 8px; font-size: 14px; text-align: center; width: 160px; border-bottom-left-radius: 9px;">
                    ';
                    $content = $content . $items['serial_number'] . '';
                    $content = $content . '
                </td>
                <td style="border-left: 1px solid #94BABB; border-bottom: 2px solid #94BABB; padding: 8px; font-size: 14px; text-align: center; width: 220px;" >
                    ';
                    $content = $content . $items['code'] . '';
                    $content = $content . '
                </td>
                <td style="border-left: 1px solid #94BABB; border-bottom: 2px solid #94BABB; border-right: 2px solid #94BABB; padding: 8px; font-size: 14px; text-align: center; width: 340px; border-bottom-right-radius: 9px;">
                    ';
                    $content = $content . $remark . '';
                    $content = $content . '
                </td>
            </tr>
            ';


        $content = $content . '
            </tbody>
                </table>
                <hr style="margin-top: 45px;">
                <table style="width: 100%;">
                    <tbody>
                    <tr>
                        <td style="font-size: 16px; padding: 5px 0 0 5px; line-height: 1.5;">
                            Please click this link to view the target webpage: ';
                            $content = $content . '<a href="' . $url . '">' . $url . '</a> ';
                            $content = $content . '
                        </td>
                    </tr>
                    </tbody>
                </table>
            </div>
            </body>
            </html>';

    $mail->MsgHTML($content);
    if($mail->Send()) {
        logMail($receiver, $content);
        return true;
//        echo "Error while sending Email.";
//        var_dump($mail);
    } else {
        logMail($receiver, $mail->ErrorInfo . $content);
        return false;
//        echo "Email sent successfully";
    }

}



function order_type_notification03Access7($name, $access,  $access_cc, $project_name, $serial_name, $order_name, $order_type, $remark, $action, $items, $od_id, $access7, $type)
{
    $conf = new Conf();

    $mail = new PHPMailer();
    $mail->IsSMTP();
    $mail->Mailer = "smtp";
    $mail->CharSet = 'UTF-8';
    $mail->Encoding = 'base64';

    // $mail->SMTPDebug  = 0;
    // $mail->SMTPAuth   = true;
    // $mail->SMTPSecure = "ssl";
    // $mail->Port       = 465;
    // $mail->SMTPKeepAlive = true;
    // $mail->Host       = $conf::$mail_host;
    // $mail->Username   = $conf::$mail_username;
    // $mail->Password   = $conf::$mail_password;

    $mail = SetupMail($mail, $conf);


    $mail->IsHTML(true);
    
    $receiver = "";
    $cc = "";
    $assignee = [];

    $_list = explode(",", $access);
    foreach($_list as &$c_list)
    {
        $notifior = GetAccessNotifiers($c_list, $serial_name);
        foreach($notifior as &$list)
        {
            $receiver .= $list["username"] . ", ";
            $mail->AddAddress($list["email"], $list["username"]);
        }
    }

    if($access7 != '')
    {
        $notifior = GetAccessNotifiersByName($access7, $serial_name);
        foreach($notifior as &$list)
        {
            $receiver = $list["username"];
            $mail->AddCC($list["email"], $list["username"]);
        }
    }


    $receiver = rtrim($receiver, ", ");
    
    // explore cc into array
    $cc_list = explode(",", $access_cc);
    foreach($cc_list as &$c_list)
    {
        $notifior = GetAccessNotifiers($c_list, $serial_name);
        foreach($notifior as &$list)
        {
            $cc = $list["username"];
            $mail->AddCC($list["email"], $list["username"]);
        }
    }
    

    $mail->SetFrom("feliix.it@gmail.com", "Feliix.System");
    $mail->AddReplyTo("feliix.it@gmail.com", "Feliix.System");

    $mail->Subject = "";

    $url = "";

    if($action == 'new_message_17')
    {
        $item_sn = $items['serial_number'];

        $notifior = GetAccessNotifiersByName($name, $serial_name);
        foreach($notifior as &$list)
        {
            $receiver = $list["username"];
            $mail->AddCC($list["email"], $list["username"]);
        }

        $receiver = "All";

        $mail->Subject = 'New message was created for item #' . $item_sn . ' in "' . $order_type . ': ' . $serial_name . '" ';
        $header = $name . ' created a new message for item #' . $item_sn . ' in "' . $order_type . ': ' . $serial_name . '". Please check details below:';
        $url = "https://feliix.myvnc.com/order_taiwan_" . $type . "_p1?id=" . $od_id;
        
    }

    if($action == 'new_message_18')
    {
        $item_sn = $items['serial_number'];

        $notifior = GetAccessNotifiersByName($name, $serial_name);
        foreach($notifior as &$list)
        {
            $receiver = $list["username"];
            $mail->AddCC($list["email"], $list["username"]);
        }

        $receiver = "All";

        $mail->Subject = 'Existing message was deleted for item #' . $item_sn . ' in "' . $order_type . ': ' . $serial_name . '" ';
        $header = $name . ' deleted an existing message for item #' . $item_sn . ' in "' . $order_type . ': ' . $serial_name . '". Please check details below:';
        $url = "https://feliix.myvnc.com/order_taiwan_" . $type . "_p1?id=" . $od_id;
        
    }

    if($action == 'new_message_19')
    {
        $item_sn = $items['serial_number'];

        $notifior = GetAccessNotifiersByName($name, $serial_name);
        foreach($notifior as &$list)
        {
            $receiver = $list["username"];
            $mail->AddCC($list["email"], $list["username"]);
        }

        $receiver = "All";

        $mail->Subject = 'New message was created for item #' . $item_sn . ' in "' . $order_type . ': ' . $serial_name . '" ';
        $header = $name . ' created a new message for item #' . $item_sn . ' in "' . $order_type . ': ' . $serial_name . '". Please check details below:';
        $url = "https://feliix.myvnc.com/order_taiwan_" . $type . "_p2?id=" . $od_id;
        
    }

    if($action == 'new_message_20')
    {
        $item_sn = $items['serial_number'];

        $notifior = GetAccessNotifiersByName($name, $serial_name);
        foreach($notifior as &$list)
        {
            $receiver = $list["username"];
            $mail->AddCC($list["email"], $list["username"]);
        }

        $receiver = "All";

        $mail->Subject = 'Existing message was deleted for item #' . $item_sn . ' in "' . $order_type . ': ' . $serial_name . '" ';
        $header = $name . ' deleted an existing message for item #' . $item_sn . ' in "' . $order_type . ': ' . $serial_name . '". Please check details below:';
        $url = "https://feliix.myvnc.com/order_taiwan_" . $type . "_p2?id=" . $od_id;
        
    }

    if($action == 'new_message_21')
    {
        $item_sn = $items['serial_number'];

        $notifior = GetAccessNotifiersByName($name, $serial_name);
        foreach($notifior as &$list)
        {
            $receiver = $list["username"];
            $mail->AddCC($list["email"], $list["username"]);
        }

        $receiver = "All";

        $mail->Subject = 'New message was created for item #' . $item_sn . ' in "' . $order_type . ': ' . $serial_name . '" ';
        $header = $name . ' created a new message for item #' . $item_sn . ' in "' . $order_type . ': ' . $serial_name . '". Please check details below:';
        $url = "https://feliix.myvnc.com/order_taiwan_" . $type . "_p3?id=" . $od_id;
        
    }

    if($action == 'new_message_22')
    {
        $item_sn = $items['serial_number'];

        $notifior = GetAccessNotifiersByName($name, $serial_name);
        foreach($notifior as &$list)
        {
            $receiver = $list["username"];
            $mail->AddCC($list["email"], $list["username"]);
        }

        $receiver = "All";

        $mail->Subject = 'Existing message was deleted for item #' . $item_sn . ' in "' . $order_type . ': ' . $serial_name . '" ';
        $header = $name . ' deleted an existing message for item #' . $item_sn . ' in "' . $order_type . ': ' . $serial_name . '". Please check details below:';
        $url = "https://feliix.myvnc.com/order_taiwan_" . $type . "_p3?id=" . $od_id;
        
    }


    $content = '<!DOCTYPE html>
    <html lang="en">
    <head>
        <meta http-equiv="content-type" content="text/html; charset=utf-8"/>
    </head>
    <body>

    <div style="width: 766px; padding: 25px 70px 20px 70px; border: 2px solid rgb(230,230,230); color: black;">

        <table style="width: 100%;">
            <tbody>
            <tr>
                <td style="font-size: 16px; padding: 10px 0 10px 5px;">';
                    $content = $content . "Dear " . $receiver . ",";
                    $content = $content . '
                </td>
            </tr>
            <tr>
                <td style="font-size: 16px; padding: 0 0 20px 5px; text-align: justify;">';
                    $content = $content . $header;
                    $content = $content . '
                </td>
            </tr>

            <tr>
                <td style="font-size: 15px; padding: 0 0 5px 15px; text-align: justify; line-height: 1.8;">';
                    $content = $content . 'Order Type: ' . $order_type . '<br>';
                    $content = $content . 'Order Name: ' . $serial_name . ' ' . $order_name . '<br>';
                    $content = $content . 'Related Task: ' . $project_name . '<br>';
                    $content = $content . 'Submission Time: ' . date('Y/m/d h:i:s a', time()) . '<br>';
                    $content = $content . 'Submitter: ' . $name . '<br>';
                    $content = $content . '
                </td>
            </tr>
            </tbody>
        </table>
        <table style="margin-left: 15px; width: 96%;">
            <tbody>
            <tr>
                <td colspan="3"
                    style="background-color: #DFEAEA; border: 2px solid #94BABB; border-bottom: 1px solid #94BABB; padding: 8px; font-size: 14px; font-weight: 600; text-align: center; border-top-left-radius: 9px; border-top-right-radius: 9px;">
                    Affected Item
                </td>
            </tr>
            <tr>
                <td style="border-left: 2px solid #94BABB; border-bottom: 1px solid #94BABB; padding: 8px; font-size: 14px; font-weight: 600; text-align: center; width: 160px;">
                    #
                </td>

                <td style="border-left: 1px solid #94BABB; border-bottom: 1px solid #94BABB; border-right: padding: 8px; font-size: 14px; font-weight: 600; text-align: center; width: 220px;">
                    Product Code
                </td>

                <td style="border-left: 1px solid #94BABB; border-bottom: 1px solid #94BABB; border-right: 2px solid #94BABB; padding: 8px; font-size: 14px; font-weight: 600; text-align: center; width: 340px;">
                    Message
                </td>
            </tr>
            ';

           /* 表格裡面只會有一個產品 */
            $content = $content . '
            <tr>
                <td style="border-left: 2px solid #94BABB; border-bottom: 2px solid #94BABB; padding: 8px; font-size: 14px; text-align: center; width: 160px; border-bottom-left-radius: 9px;">
                    ';
                    $content = $content . $items['serial_number'] . '';
                    $content = $content . '
                </td>
                <td style="border-left: 1px solid #94BABB; border-bottom: 2px solid #94BABB; padding: 8px; font-size: 14px; text-align: center; width: 220px;" >
                    ';
                    $content = $content . $items['code'] . '';
                    $content = $content . '
                </td>
                <td style="border-left: 1px solid #94BABB; border-bottom: 2px solid #94BABB; border-right: 2px solid #94BABB; padding: 8px; font-size: 14px; text-align: center; width: 340px; border-bottom-right-radius: 9px;">
                    ';
                    $content = $content . $remark . '';
                    $content = $content . '
                </td>
            </tr>
            ';


        $content = $content . '
            </tbody>
                </table>
                <hr style="margin-top: 45px;">
                <table style="width: 100%;">
                    <tbody>
                    <tr>
                        <td style="font-size: 16px; padding: 5px 0 0 5px; line-height: 1.5;">
                            Please click this link to view the target webpage: ';
                            $content = $content . '<a href="' . $url . '">' . $url . '</a> ';
                            $content = $content . '
                        </td>
                    </tr>
                    </tbody>
                </table>
            </div>
            </body>
            </html>';

    $mail->MsgHTML($content);
    if($mail->Send()) {
        logMail($receiver, $content);
        return true;
//        echo "Error while sending Email.";
//        var_dump($mail);
    } else {
        logMail($receiver, $mail->ErrorInfo . $content);
        return false;
//        echo "Email sent successfully";
    }

}



function tag_group_notification($name, $user_id, $items)
{
    $conf = new Conf();

    $mail = new PHPMailer();
    $mail->IsSMTP();
    $mail->Mailer = "smtp";
    $mail->CharSet = 'UTF-8';
    $mail->Encoding = 'base64';

    // $mail->SMTPDebug  = 0;
    // $mail->SMTPAuth   = true;
    // $mail->SMTPSecure = "ssl";
    // $mail->Port       = 465;
    // $mail->SMTPKeepAlive = true;
    // $mail->Host       = $conf::$mail_host;
    // $mail->Username   = $conf::$mail_username;
    // $mail->Password   = $conf::$mail_password;

    $mail = SetupMail($mail, $conf);
    $mail->IsHTML(true);

    // Lighting Department 所有的人、Sales Department 所有的人、Ariel Lin 需要放入收件人名單
    $receiver = "";
    $notifior = GetAccessNotifiersByDepartment("Lighting");
    foreach($notifior as &$list)
    {
        $receiver .= $list["username"] . ", ";
        $mail->AddAddress($list["email"], $list["username"]);
    }

    $notifior = GetAccessNotifiersByDepartment("Sales");
    foreach($notifior as &$list)
    {
        $receiver .= $list["username"] . ", ";
        $mail->AddAddress($list["email"], $list["username"]);
    }

    $mail->AddAddress("ariel@feliix.com", "Ariel Lin");



    //cc收件人名單
    // 異動者(也就是點擊 Save 按鈕的人) 放入 cc收件人名單名字
    $notifior = GetNotifiers($user_id);
    foreach($notifior as &$list)
    {
        $mail->AddCC($list["email"], $list["username"]);
    }


    // Engineering Department 的 Aiza 和 Alleah 放入cc收件人名單
    $mail->AddCC("aiza@feliix.com", "Aiza Eisma");
    $mail->AddCC("alleah.feliix@gmail.com", "Alleah Belmonte");


    $mail->SetFrom("feliix.it@gmail.com", "Feliix.System");
    $mail->AddReplyTo("feliix.it@gmail.com", "Feliix.System");


    // Tag 群組 的 Save 動作
    $mail->Subject = '[Product Tag Notification] ' . $name . ' updated groups of tag';
    $header = $name . ' updated groups of tag. Please check the affected groups of tag below:';
    $url = "https://feliix.myvnc.com/tag_mgt";
    

    $content = '<!DOCTYPE html>
    <html lang="en">
    <head>
        <meta http-equiv="content-type" content="text/html; charset=utf-8"/>
    </head>
    <body>

    <div style="width: 766px; padding: 25px 70px 20px 70px; border: 2px solid rgb(230,230,230); color: black;">

        <table style="width: 100%;">
            <tbody>
            <tr>
                <td style="font-size: 16px; padding: 10px 0 10px 5px;">';
                    $content = $content . "Dear all,";
                    $content = $content . '
                </td>
            </tr>
            <tr>
                <td style="font-size: 16px; padding: 0 0 20px 5px; text-align: justify;">';
                    $content = $content . $header;
                    $content = $content . '
                </td>
            </tr>

            </tbody>
        </table>
        <table style="margin-left: 15px; width: 96%;">
            <tbody>
            <tr>
                <td colspan="2"
                    style="background-color: #DFEAEA; border: 2px solid #94BABB; border-bottom: 1px solid #94BABB; padding: 8px; font-size: 14px; font-weight: 600; text-align: center; border-top-left-radius: 9px; border-top-right-radius: 9px;">
                    Affected Groups of Tag
                </td>
            </tr>
            <tr>
                <td style="border-left: 2px solid #94BABB; border-bottom: 1px solid #94BABB; padding: 8px; font-size: 14px; font-weight: 600; text-align: center; width: 360px;">
                    Before Update
                </td>

                <td style="border-left: 1px solid #94BABB; border-bottom: 1px solid #94BABB; border-right: 2px solid #94BABB; padding: 8px; font-size: 14px; font-weight: 600; text-align: center; width: 360px;">
                    After Update
                </td>
            </tr>
            ';

            /* 除了最後一個異動的東西 */
            $i = 0;
            for($i=0; $i<count($items)-1; $i++)
            {
                $before = explode(",", $items[$i]);

                $content = $content . '
                <tr>
                    <td style="border-left: 2px solid #94BABB; border-bottom: 1px solid #94BABB; padding: 8px; font-size: 14px; text-align: center; width: 360px;">
                        ';
                        $content = $content . $before[0] . '';
                        $content = $content . '
                    </td>
                    <td style="border-left: 1px solid #94BABB; border-bottom: 1px solid #94BABB; border-right: 2px solid #94BABB; padding: 8px; font-size: 14px; text-align: center; width: 360px;">
                        ';
                        $content = $content . $before[1] . '';
                        $content = $content . '
                    </td>
                </tr>
                ';
            }

            $before = explode(",", $items[$i]);

            /* 最後一個異動的東西 */
            $content = $content . '
            <tr>
                <td style="border-left: 2px solid #94BABB; border-bottom: 2px solid #94BABB; padding: 8px; font-size: 14px; text-align: center; width: 360px; border-bottom-left-radius: 9px;">
                    ';
                    $content = $content . $before[0] . '';
                    $content = $content . '
                </td>
                <td style="border-left: 1px solid #94BABB; border-bottom: 2px solid #94BABB; border-right: 2px solid #94BABB; padding: 8px; font-size: 14px; text-align: center; width: 360px; border-bottom-right-radius: 9px;">
                    ';
                    $content = $content . $before[1] . '';
                    $content = $content . '
                </td>
            </tr>
            ';


        $content = $content . '
            </tbody>
                </table>
                <hr style="margin-top: 45px;">
                <table style="width: 100%;">
                    <tbody>
                    <tr>
                        <td style="font-size: 16px; padding: 5px 0 0 5px; line-height: 1.5;">
                            Click this link to visit the webpage of Tag Management: ';
                            $content = $content . '<a href="' . $url . '">' . $url . '</a> ';
                            $content = $content . '
                        </td>
                    </tr>
                    </tbody>
                </table>
            </div>
            </body>
            </html>';

    $mail->MsgHTML($content);
    if($mail->Send()) {
        logMail($receiver, $content);
        return true;
//        echo "Error while sending Email.";
//        var_dump($mail);
    } else {
        logMail($receiver, $mail->ErrorInfo . $content);
        return false;
//        echo "Email sent successfully";
    }

}


function tag_notification($name, $user_id, $tag_group, $items)
{
    $conf = new Conf();

    $mail = new PHPMailer();
    $mail->IsSMTP();
    $mail->Mailer = "smtp";
    $mail->CharSet = 'UTF-8';
    $mail->Encoding = 'base64';

    // $mail->SMTPDebug  = 0;
    // $mail->SMTPAuth   = true;
    // $mail->SMTPSecure = "ssl";
    // $mail->Port       = 465;
    // $mail->SMTPKeepAlive = true;
    // $mail->Host       = $conf::$mail_host;
    // $mail->Username   = $conf::$mail_username;
    // $mail->Password   = $conf::$mail_password;

    $mail = SetupMail($mail, $conf);
    $mail->IsHTML(true);

    // Lighting Department 所有的人、Sales Department 所有的人、Ariel Lin 需要放入收件人名單
    $receiver = "";
    $notifior = GetAccessNotifiersByDepartment("Lighting");
    foreach($notifior as &$list)
    {
        $receiver .= $list["username"] . ", ";
        $mail->AddAddress($list["email"], $list["username"]);
    }

    $notifior = GetAccessNotifiersByDepartment("Sales");
    foreach($notifior as &$list)
    {
        $receiver .= $list["username"] . ", ";
        $mail->AddAddress($list["email"], $list["username"]);
    }

    $mail->AddAddress("ariel@feliix.com", "Ariel Lin");



    //cc收件人名單
    // 異動者(也就是點擊 Save 按鈕的人) 放入 cc收件人名單名字
    $notifior = GetNotifiers($_record["create_id"]);
    foreach($notifior as &$list)
    {
        $mail->AddCC($list["email"], $list["username"]);
    }


    // Engineering Department 的 Aiza 和 Alleah 放入cc收件人名單
    $mail->AddCC("aiza@feliix.com", "Aiza Eisma");
    $mail->AddCC("alleah.feliix@gmail.com", "Alleah Belmonte");


    $mail->SetFrom("feliix.it@gmail.com", "Feliix.System");
    $mail->AddReplyTo("feliix.it@gmail.com", "Feliix.System");

    // Tag 群組 的 Save 動作
    $mail->Subject = '[Product Tag Notification] ' . $name . ' updated tags for the group "' . $tag_group . '"';
    $header = $name . ' updated tags for the group "' . $tag_group . '". Please check the affected tags in the group "' . $tag_group . '" below:';
    $url = "https://feliix.myvnc.com/tag_mgt";



    $content = '<!DOCTYPE html>
    <html lang="en">
    <head>
        <meta http-equiv="content-type" content="text/html; charset=utf-8"/>
    </head>
    <body>

    <div style="width: 766px; padding: 25px 70px 20px 70px; border: 2px solid rgb(230,230,230); color: black;">

        <table style="width: 100%;">
            <tbody>
            <tr>
                <td style="font-size: 16px; padding: 10px 0 10px 5px;">';
                    $content = $content . "Dear all,";
                    $content = $content . '
                </td>
            </tr>
            <tr>
                <td style="font-size: 16px; padding: 0 0 20px 5px; text-align: justify;">';
                    $content = $content . $header;
                    $content = $content . '
                </td>
            </tr>

            </tbody>
        </table>
        <table style="margin-left: 15px; width: 96%;">
            <tbody>
            <tr>
                <td colspan="2"
                    style="background-color: #DFEAEA; border: 2px solid #94BABB; border-bottom: 1px solid #94BABB; padding: 8px; font-size: 14px; font-weight: 600; text-align: center; border-top-left-radius: 9px; border-top-right-radius: 9px;">
                    Affected Tags
                </td>

            </tr>
            <tr>
                <td style="border-left: 2px solid #94BABB; border-bottom: 1px solid #94BABB; padding: 8px; font-size: 14px; font-weight: 600; text-align: center; width: 360px;">
                    Before Update
                </td>

                <td style="border-left: 1px solid #94BABB; border-bottom: 1px solid #94BABB; border-right: 2px solid #94BABB; padding: 8px; font-size: 14px; font-weight: 600; text-align: center; width: 360px;">
                    After Update
                </td>
            </tr>
            ';

            /* 除了最後一個異動的東西 */
            $i = 0;
            for($i=0; $i<count($items)-1; $i++)
            {
                $before = explode(",", $items[$i]);

                $content = $content . '
                <tr>
                    <td style="border-left: 2px solid #94BABB; border-bottom: 1px solid #94BABB; padding: 8px; font-size: 14px; text-align: center; width: 360px;">
                        ';
                        $content = $content . $before[0] . '';
                        $content = $content . '
                    </td>
                    <td style="border-left: 1px solid #94BABB; border-bottom: 1px solid #94BABB; border-right: 2px solid #94BABB; padding: 8px; font-size: 14px; text-align: center; width: 360px;">
                        ';
                        $content = $content . $before[1] . '';
                        $content = $content . '
                    </td>
                </tr>
                ';
            }

            $before = explode(",", $items[$i]);

            /* 最後一個異動的東西 */
            $content = $content . '
            <tr>
                <td style="border-left: 2px solid #94BABB; border-bottom: 2px solid #94BABB; padding: 8px; font-size: 14px; text-align: center; width: 360px; border-bottom-left-radius: 9px;">
                    ';
                    $content = $content . $before[0] . '';
                    $content = $content . '
                </td>
                <td style="border-left: 1px solid #94BABB; border-bottom: 2px solid #94BABB; border-right: 2px solid #94BABB; padding: 8px; font-size: 14px; text-align: center; width: 360px; border-bottom-right-radius: 9px;">
                    ';
                    $content = $content . $before[1] . '';
                    $content = $content . '
                </td>
            </tr>
            ';


        $content = $content . '
            </tbody>
                </table>
                <hr style="margin-top: 45px;">
                <table style="width: 100%;">
                    <tbody>
                    <tr>
                        <td style="font-size: 16px; padding: 5px 0 0 5px; line-height: 1.5;">
                            Click this link to visit the webpage of Tag Management: ';
                            $content = $content . '<a href="' . $url . '">' . $url . '</a> ';
                            $content = $content . '
                        </td>
                    </tr>
                    </tbody>
                </table>
            </div>
            </body>
            </html>';

    $mail->MsgHTML($content);
    if($mail->Send()) {
        logMail($receiver, $content);
        return true;
//        echo "Error while sending Email.";
//        var_dump($mail);
    } else {
        logMail($receiver, $mail->ErrorInfo . $content);
        return false;
//        echo "Email sent successfully";
    }

}

function order_notification04($name, $access,  $access_cc, $project_name, $serial_name, $order_name, $order_type, $remark, $action, $items, $od_id)
{
    $conf = new Conf();

    $mail = new PHPMailer();
    $mail->IsSMTP();
    $mail->Mailer = "smtp";
    $mail->CharSet = 'UTF-8';
    $mail->Encoding = 'base64';

    // $mail->SMTPDebug  = 0;
    // $mail->SMTPAuth   = true;
    // $mail->SMTPSecure = "ssl";
    // $mail->Port       = 465;
    // $mail->SMTPKeepAlive = true;
    // $mail->Host       = $conf::$mail_host;
    // $mail->Username   = $conf::$mail_username;
    // $mail->Password   = $conf::$mail_password;

    $mail = SetupMail($mail, $conf);


    $mail->IsHTML(true);
    
    $receiver = "";
    $cc = "";
    $assignee = [];

    $_list = explode(",", $access);
    foreach($_list as &$c_list)
    {
        $notifior = GetAccessNotifiers($c_list, $serial_name);
        foreach($notifior as &$list)
        {
            $receiver .= $list["username"] . ", ";
            $mail->AddAddress($list["email"], $list["username"]);
        }
    }

    $receiver = rtrim($receiver, ", ");
    
    // explore cc into array
    $cc_list = explode(",", $access_cc);
    foreach($cc_list as &$c_list)
    {
        $notifior = GetAccessNotifiers($c_list, $serial_name);
        foreach($notifior as &$list)
        {
            $cc = $list["username"];
            $mail->AddCC($list["email"], $list["username"]);
        }
    }
    

    $mail->SetFrom("feliix.it@gmail.com", "Feliix.System");
    $mail->AddReplyTo("feliix.it@gmail.com", "Feliix.System");

    $mail->Subject = "";

    $url = "";

    if($action == 'new_message_23')
    {
        $item_sn = $items['serial_number'];

        $notifior = GetAccessNotifiersByName($name, $serial_name);
        foreach($notifior as &$list)
        {
            $receiver = $list["username"];
            $mail->AddCC($list["email"], $list["username"]);
        }

        $receiver = "All";

        $mail->Subject = 'Item #' . $item_sn . ' in "' . $order_type . ': ' . $serial_name . '" was revised';
        $header = $name . ' revised the item #' . $item_sn . ' in "' . $order_type . ': ' . $serial_name . '". Please check details below:';
        $url = "https://feliix.myvnc.com/order_taiwan_p3?id=" . $od_id;
        
    }


    $content = '<!DOCTYPE html>
    <html lang="en">
    <head>
        <meta http-equiv="content-type" content="text/html; charset=utf-8"/>
    </head>
    <body>

    <div style="width: 766px; padding: 25px 70px 20px 70px; border: 2px solid rgb(230,230,230); color: black;">

        <table style="width: 100%;">
            <tbody>
            <tr>
                <td style="font-size: 16px; padding: 10px 0 10px 5px;">';
                    $content = $content . "Dear " . $receiver . ",";
                    $content = $content . '
                </td>
            </tr>
            <tr>
                <td style="font-size: 16px; padding: 0 0 20px 5px; text-align: justify;">';
                    $content = $content . $header;
                    $content = $content . '
                </td>
            </tr>

            <tr>
                <td style="font-size: 15px; padding: 0 0 5px 15px; text-align: justify; line-height: 1.8;">';
                    $content = $content . 'Order Type: ' . $order_type . '<br>';
                    $content = $content . 'Order Name: ' . $serial_name . ' ' . $order_name . '<br>';
                    $content = $content . 'Related Project: ' . $project_name . '<br>';
                    $content = $content . 'Submission Time: ' . date('Y/m/d h:i:s a', time()) . '<br>';
                    $content = $content . 'Submitter: ' . $name . '<br>';
                    $content = $content . '
                </td>
            </tr>
            </tbody>
        </table>
        <table style="margin-left: 15px; width: 96%;">
            <tbody>
            <tr>
                <td colspan="2"
                    style="background-color: #DFEAEA; border: 2px solid #94BABB; border-bottom: 1px solid #94BABB; padding: 8px; font-size: 14px; font-weight: 600; text-align: center; border-top-left-radius: 9px; border-top-right-radius: 9px;">
                    Affected Item
                </td>
            </tr>
            <tr>
                <td style="border-left: 2px solid #94BABB; border-bottom: 1px solid #94BABB; padding: 8px; font-size: 14px; font-weight: 600; text-align: center; width: 280px;">
                    #
                </td>

                <td style="border-left: 1px solid #94BABB; border-bottom: 1px solid #94BABB; border-right: 2px solid #94BABB; padding: 8px; font-size: 14px; font-weight: 600; text-align: center; width: 440px;">
                    Product Code
                </td>

            </tr>
            ';

           /* 只會有一個產品 */
            $content = $content . '
            <tr>
                <td style="border-left: 2px solid #94BABB; border-bottom: 2px solid #94BABB; padding: 8px; font-size: 14px; text-align: center; width: 280px; border-bottom-left-radius: 9px;">
                    ';
                    $content = $content . $items['serial_number'] . '';
                    $content = $content . '
                </td>
                <td style="border-left: 1px solid #94BABB; border-bottom: 2px solid #94BABB; border-right: 2px solid #94BABB; padding: 8px; font-size: 14px; text-align: center; width: 440px; border-bottom-right-radius: 9px;">
                    ';
                    $content = $content . $items['code'] . '';
                    $content = $content . '
                </td>
                
            </tr>
            ';


        $content = $content . '
            </tbody>
                </table>
                <hr style="margin-top: 45px;">
                <table style="width: 100%;">
                    <tbody>
                    <tr>
                        <td style="font-size: 16px; padding: 5px 0 0 5px; line-height: 1.5;">
                            Please click this link to view the target webpage: ';
                            $content = $content . '<a href="' . $url . '">' . $url . '</a> ';
                            $content = $content . '
                        </td>
                    </tr>
                    </tbody>
                </table>
            </div>
            </body>
            </html>';

    $mail->MsgHTML($content);
    if($mail->Send()) {
        logMail($receiver, $content);
        return true;
//        echo "Error while sending Email.";
//        var_dump($mail);
    } else {
        logMail($receiver, $mail->ErrorInfo . $content);
        return false;
//        echo "Email sent successfully";
    }

}


function mockup_notification04($name, $access,  $access_cc, $project_name, $serial_name, $order_name, $order_type, $remark, $action, $items, $od_id)
{
    $conf = new Conf();

    $mail = new PHPMailer();
    $mail->IsSMTP();
    $mail->Mailer = "smtp";
    $mail->CharSet = 'UTF-8';
    $mail->Encoding = 'base64';

    // $mail->SMTPDebug  = 0;
    // $mail->SMTPAuth   = true;
    // $mail->SMTPSecure = "ssl";
    // $mail->Port       = 465;
    // $mail->SMTPKeepAlive = true;
    // $mail->Host       = $conf::$mail_host;
    // $mail->Username   = $conf::$mail_username;
    // $mail->Password   = $conf::$mail_password;

    $mail = SetupMail($mail, $conf);


    $mail->IsHTML(true);
    
    $receiver = "";
    $cc = "";
    $assignee = [];

    $_list = explode(",", $access);
    foreach($_list as &$c_list)
    {
        $notifior = GetAccessNotifiers($c_list, $serial_name);
        foreach($notifior as &$list)
        {
            $receiver .= $list["username"] . ", ";
            $mail->AddAddress($list["email"], $list["username"]);
        }
    }

    $receiver = rtrim($receiver, ", ");
    
    // explore cc into array
    $cc_list = explode(",", $access_cc);
    foreach($cc_list as &$c_list)
    {
        $notifior = GetAccessNotifiers($c_list, $serial_name);
        foreach($notifior as &$list)
        {
            $cc = $list["username"];
            $mail->AddCC($list["email"], $list["username"]);
        }
    }
    

    $mail->SetFrom("feliix.it@gmail.com", "Feliix.System");
    $mail->AddReplyTo("feliix.it@gmail.com", "Feliix.System");

    $mail->Subject = "";

    $url = "";

    if($action == 'new_message_23')
    {
        $item_sn = $items['serial_number'];

        $notifior = GetAccessNotifiersByName($name, $serial_name);
        foreach($notifior as &$list)
        {
            $receiver = $list["username"];
            $mail->AddCC($list["email"], $list["username"]);
        }

        $receiver = "All";

        $mail->Subject = 'Item #' . $item_sn . ' in "' . $order_type . ': ' . $serial_name . '" was revised';
        $header = $name . ' revised the item #' . $item_sn . ' in "' . $order_type . ': ' . $serial_name . '". Please check details below:';
        $url = "https://feliix.myvnc.com/order_taiwan_mockup_p3?id=" . $od_id;
        
    }


    $content = '<!DOCTYPE html>
    <html lang="en">
    <head>
        <meta http-equiv="content-type" content="text/html; charset=utf-8"/>
    </head>
    <body>

    <div style="width: 766px; padding: 25px 70px 20px 70px; border: 2px solid rgb(230,230,230); color: black;">

        <table style="width: 100%;">
            <tbody>
            <tr>
                <td style="font-size: 16px; padding: 10px 0 10px 5px;">';
                    $content = $content . "Dear " . $receiver . ",";
                    $content = $content . '
                </td>
            </tr>
            <tr>
                <td style="font-size: 16px; padding: 0 0 20px 5px; text-align: justify;">';
                    $content = $content . $header;
                    $content = $content . '
                </td>
            </tr>

            <tr>
                <td style="font-size: 15px; padding: 0 0 5px 15px; text-align: justify; line-height: 1.8;">';
                    $content = $content . 'Order Type: ' . $order_type . '<br>';
                    $content = $content . 'Order Name: ' . $serial_name . ' ' . $order_name . '<br>';
                    $content = $content . 'Related Project: ' . $project_name . '<br>';
                    $content = $content . 'Submission Time: ' . date('Y/m/d h:i:s a', time()) . '<br>';
                    $content = $content . 'Submitter: ' . $name . '<br>';
                    $content = $content . '
                </td>
            </tr>
            </tbody>
        </table>
        <table style="margin-left: 15px; width: 96%;">
            <tbody>
            <tr>
                <td colspan="2"
                    style="background-color: #DFEAEA; border: 2px solid #94BABB; border-bottom: 1px solid #94BABB; padding: 8px; font-size: 14px; font-weight: 600; text-align: center; border-top-left-radius: 9px; border-top-right-radius: 9px;">
                    Affected Item
                </td>
            </tr>
            <tr>
                <td style="border-left: 2px solid #94BABB; border-bottom: 1px solid #94BABB; padding: 8px; font-size: 14px; font-weight: 600; text-align: center; width: 280px;">
                    #
                </td>

                <td style="border-left: 1px solid #94BABB; border-bottom: 1px solid #94BABB; border-right: 2px solid #94BABB; padding: 8px; font-size: 14px; font-weight: 600; text-align: center; width: 440px;">
                    Product Code
                </td>

            </tr>
            ';

           /* 只會有一個產品 */
            $content = $content . '
            <tr>
                <td style="border-left: 2px solid #94BABB; border-bottom: 2px solid #94BABB; padding: 8px; font-size: 14px; text-align: center; width: 280px; border-bottom-left-radius: 9px;">
                    ';
                    $content = $content . $items['serial_number'] . '';
                    $content = $content . '
                </td>
                <td style="border-left: 1px solid #94BABB; border-bottom: 2px solid #94BABB; border-right: 2px solid #94BABB; padding: 8px; font-size: 14px; text-align: center; width: 440px; border-bottom-right-radius: 9px;">
                    ';
                    $content = $content . $items['code'] . '';
                    $content = $content . '
                </td>
                
            </tr>
            ';


        $content = $content . '
            </tbody>
                </table>
                <hr style="margin-top: 45px;">
                <table style="width: 100%;">
                    <tbody>
                    <tr>
                        <td style="font-size: 16px; padding: 5px 0 0 5px; line-height: 1.5;">
                            Please click this link to view the target webpage: ';
                            $content = $content . '<a href="' . $url . '">' . $url . '</a> ';
                            $content = $content . '
                        </td>
                    </tr>
                    </tbody>
                </table>
            </div>
            </body>
            </html>';

    $mail->MsgHTML($content);
    if($mail->Send()) {
        logMail($receiver, $content);
        return true;
//        echo "Error while sending Email.";
//        var_dump($mail);
    } else {
        logMail($receiver, $mail->ErrorInfo . $content);
        return false;
//        echo "Email sent successfully";
    }

}


function order_type_notification04($name, $access,  $access_cc, $project_name, $serial_name, $order_name, $order_type, $remark, $action, $items, $od_id, $type)
{
    $conf = new Conf();

    $mail = new PHPMailer();
    $mail->IsSMTP();
    $mail->Mailer = "smtp";
    $mail->CharSet = 'UTF-8';
    $mail->Encoding = 'base64';

    // $mail->SMTPDebug  = 0;
    // $mail->SMTPAuth   = true;
    // $mail->SMTPSecure = "ssl";
    // $mail->Port       = 465;
    // $mail->SMTPKeepAlive = true;
    // $mail->Host       = $conf::$mail_host;
    // $mail->Username   = $conf::$mail_username;
    // $mail->Password   = $conf::$mail_password;

    $mail = SetupMail($mail, $conf);


    $mail->IsHTML(true);
    
    $receiver = "";
    $cc = "";
    $assignee = [];

    $_list = explode(",", $access);
    foreach($_list as &$c_list)
    {
        $notifior = GetAccessNotifiers($c_list, $serial_name);
        foreach($notifior as &$list)
        {
            $receiver .= $list["username"] . ", ";
            $mail->AddAddress($list["email"], $list["username"]);
        }
    }

    $receiver = rtrim($receiver, ", ");
    
    // explore cc into array
    $cc_list = explode(",", $access_cc);
    foreach($cc_list as &$c_list)
    {
        $notifior = GetAccessNotifiers($c_list, $serial_name);
        foreach($notifior as &$list)
        {
            $cc = $list["username"];
            $mail->AddCC($list["email"], $list["username"]);
        }
    }
    

    $mail->SetFrom("feliix.it@gmail.com", "Feliix.System");
    $mail->AddReplyTo("feliix.it@gmail.com", "Feliix.System");

    $mail->Subject = "";

    $url = "";

    if($action == 'new_message_23')
    {
        $item_sn = $items['serial_number'];

        $notifior = GetAccessNotifiersByName($name, $serial_name);
        foreach($notifior as &$list)
        {
            $receiver = $list["username"];
            $mail->AddCC($list["email"], $list["username"]);
        }

        $receiver = "All";

        $mail->Subject = 'Item #' . $item_sn . ' in "' . $order_type . ': ' . $serial_name . '" was revised';
        $header = $name . ' revised the item #' . $item_sn . ' in "' . $order_type . ': ' . $serial_name . '". Please check details below:';
        $url = "https://feliix.myvnc.com/order_taiwan_" . $type . "_p3?id=" . $od_id;
        
    }


    $content = '<!DOCTYPE html>
    <html lang="en">
    <head>
        <meta http-equiv="content-type" content="text/html; charset=utf-8"/>
    </head>
    <body>

    <div style="width: 766px; padding: 25px 70px 20px 70px; border: 2px solid rgb(230,230,230); color: black;">

        <table style="width: 100%;">
            <tbody>
            <tr>
                <td style="font-size: 16px; padding: 10px 0 10px 5px;">';
                    $content = $content . "Dear " . $receiver . ",";
                    $content = $content . '
                </td>
            </tr>
            <tr>
                <td style="font-size: 16px; padding: 0 0 20px 5px; text-align: justify;">';
                    $content = $content . $header;
                    $content = $content . '
                </td>
            </tr>

            <tr>
                <td style="font-size: 15px; padding: 0 0 5px 15px; text-align: justify; line-height: 1.8;">';
                    $content = $content . 'Order Type: ' . $order_type . '<br>';
                    $content = $content . 'Order Name: ' . $serial_name . ' ' . $order_name . '<br>';
                    $content = $content . 'Related Task: ' . $project_name . '<br>';
                    $content = $content . 'Submission Time: ' . date('Y/m/d h:i:s a', time()) . '<br>';
                    $content = $content . 'Submitter: ' . $name . '<br>';
                    $content = $content . '
                </td>
            </tr>
            </tbody>
        </table>
        <table style="margin-left: 15px; width: 96%;">
            <tbody>
            <tr>
                <td colspan="2"
                    style="background-color: #DFEAEA; border: 2px solid #94BABB; border-bottom: 1px solid #94BABB; padding: 8px; font-size: 14px; font-weight: 600; text-align: center; border-top-left-radius: 9px; border-top-right-radius: 9px;">
                    Affected Item
                </td>
            </tr>
            <tr>
                <td style="border-left: 2px solid #94BABB; border-bottom: 1px solid #94BABB; padding: 8px; font-size: 14px; font-weight: 600; text-align: center; width: 280px;">
                    #
                </td>

                <td style="border-left: 1px solid #94BABB; border-bottom: 1px solid #94BABB; border-right: 2px solid #94BABB; padding: 8px; font-size: 14px; font-weight: 600; text-align: center; width: 440px;">
                    Product Code
                </td>

            </tr>
            ';

           /* 只會有一個產品 */
            $content = $content . '
            <tr>
                <td style="border-left: 2px solid #94BABB; border-bottom: 2px solid #94BABB; padding: 8px; font-size: 14px; text-align: center; width: 280px; border-bottom-left-radius: 9px;">
                    ';
                    $content = $content . $items['serial_number'] . '';
                    $content = $content . '
                </td>
                <td style="border-left: 1px solid #94BABB; border-bottom: 2px solid #94BABB; border-right: 2px solid #94BABB; padding: 8px; font-size: 14px; text-align: center; width: 440px; border-bottom-right-radius: 9px;">
                    ';
                    $content = $content . $items['code'] . '';
                    $content = $content . '
                </td>
                
            </tr>
            ';


        $content = $content . '
            </tbody>
                </table>
                <hr style="margin-top: 45px;">
                <table style="width: 100%;">
                    <tbody>
                    <tr>
                        <td style="font-size: 16px; padding: 5px 0 0 5px; line-height: 1.5;">
                            Please click this link to view the target webpage: ';
                            $content = $content . '<a href="' . $url . '">' . $url . '</a> ';
                            $content = $content . '
                        </td>
                    </tr>
                    </tbody>
                </table>
            </div>
            </body>
            </html>';

    $mail->MsgHTML($content);
    if($mail->Send()) {
        logMail($receiver, $content);
        return true;
//        echo "Error while sending Email.";
//        var_dump($mail);
    } else {
        logMail($receiver, $mail->ErrorInfo . $content);
        return false;
//        echo "Email sent successfully";
    }

}

function product_notify($action, $_record)
{
    $conf = new Conf();

    $mail = new PHPMailer();
    $mail->IsSMTP();
    $mail->Mailer = "smtp";
    $mail->CharSet = 'UTF-8';
    $mail->Encoding = 'base64';

    // $mail->SMTPDebug  = 0;
    // $mail->SMTPAuth   = true;
    // $mail->SMTPSecure = "ssl";
    // $mail->Port       = 465;
    // $mail->SMTPKeepAlive = true;
    // $mail->Host       = $conf::$mail_host;
    // $mail->Username   = $conf::$mail_username;
    // $mail->Password   = $conf::$mail_password;

    $mail = SetupMail($mail, $conf);

    $mail->IsHTML(true);

    //收件人名單內容
    if($_record["category"] == "20000000")
    {
        // Office Department 所有的人、Sales Department 所有的人、Ariel Lin 需要放入收件人名單
        $notifior = GetAccessNotifiersByDepartment("Office");
        foreach($notifior as &$list)
        {
            $receiver .= $list["username"] . ", ";
            $mail->AddAddress($list["email"], $list["username"]);
        }

        $notifior = GetAccessNotifiersByDepartment("Sales");
        foreach($notifior as &$list)
        {
            $receiver .= $list["username"] . ", ";
            $mail->AddAddress($list["email"], $list["username"]);
        }

        $mail->AddAddress("ariel@feliix.com", "Ariel Lin");
    }


    if($_record["category"] == "10000000")
    {
        // Lighting Department 所有的人、Sales Department 所有的人、Ariel Lin 需要放入收件人名單
        $notifior = GetAccessNotifiersByDepartment("Lighting");
        foreach($notifior as &$list)
        {
            $receiver .= $list["username"] . ", ";
            $mail->AddAddress($list["email"], $list["username"]);
        }

        $notifior = GetAccessNotifiersByDepartment("Sales");
        foreach($notifior as &$list)
        {
            $receiver .= $list["username"] . ", ";
            $mail->AddAddress($list["email"], $list["username"]);
        }

        $mail->AddAddress("ariel@feliix.com", "Ariel Lin");
    }


    //cc收件人名單
    if($action == "add") {
        // 建立者 放入 cc收件人名單名字
        $notifior = GetNotifiers($_record["create_id"]);
        foreach($notifior as &$list)
        {
            $receiver .= $list["username"] . ", ";
            $mail->AddCC($list["email"], $list["username"]);
        }

        if($_record["category"] == "10000000")
        {
            // Aiza 放入cc收件人名單
            $receiver .= "Aiza Eisma" . ", ";
            $mail->AddCC("aiza@feliix.com", "Aiza Eisma");
            $receiver .= "Alleah Belmonte" . ", ";
            $mail->AddCC("alleah.feliix@gmail.com", "Alleah Belmonte");
        }

    }

    if( $action == "update" ){
        // 編輯者  放入 cc收件人名單名字
        $notifior = GetNotifiers($_record["updated_id"]);
        foreach($notifior as &$list)
        {
            $receiver .= $list["username"] . ", ";
            $mail->AddCC($list["email"], $list["username"]);
        }

        if($_record["category"] == "10000000")
        {
            // Aiza 放入cc收件人名單
            $receiver .= "Aiza Eisma" . ", ";
            $mail->AddCC("aiza@feliix.com", "Aiza Eisma");
            $receiver .= "Alleah Belmonte" . ", ";
            $mail->AddCC("alleah.feliix@gmail.com", "Alleah Belmonte");
        }

    }

    if( $action == "delete" ){
        // 刪除者名字 放入 cc收件人名單名字
        $notifior = GetNotifiers($_record["updated_id"]);
        foreach($notifior as &$list)
        {
            $receiver .= $list["username"] . ", ";
            $mail->AddCC($list["email"], $list["username"]);
        }

        if($_record["category"] == "10000000")
        {
            // Aiza 放入cc收件人名單
            $receiver .= "Aiza Eisma" . ", ";
            $mail->AddCC("aiza@feliix.com", "Aiza Eisma");
            $receiver .= "Alleah Belmonte" . ", ";
            $mail->AddCC("alleah.feliix@gmail.com", "Alleah Belmonte");
        }

    }


    $mail->SetFrom("feliix.it@gmail.com", "Feliix.System");
    $mail->AddReplyTo("feliix.it@gmail.com", "Feliix.System");
    // $mail->AddCC("tryhelpbuy@gmail.com", "tryhelpbuy");


    //信件主旨
    if( $action == "add" )
        $mail->Subject = "[Product Notification] " . $_record["creator"] . " created a new product in Product Database";
    if( $action == "update" )
        $mail->Subject = "[Product Notification] " . $_record["updator"] . " revised an existing product in Product Database";
    if( $action == "delete" )
        $mail->Subject = "[Product Notification] " . $_record["updator"] . " deleted an existing product in Product Database";



    $content = '<!DOCTYPE html>
    <html lang="en">
    <head>
        <meta http-equiv="content-type" content="text/html; charset=utf-8"/>
    </head>
    <body>

        <div style="width: 766px; padding: 25px 70px 20px 70px; border: 2px solid rgb(230,230,230); color: black;">
            <table style="width: 100%;">
                <tbody>
                <tr>
                    <td style="font-size: 18px; padding: 20px 0 20px 5px;"> Dear all,</td>
                </tr>
                <tr>
                    <td style="font-size: 18px; padding: 0 0 20px 5px; text-align: justify;">';
if( $action == "add" )
    $content = $content . $_record["creator"] . " created a new product in Product Database. Below is the details of the product:";
if( $action == "update" )
    $content = $content . $_record["updator"] . " revised an existing product in Product Database. Below is the details of the product after revision:";
if( $action == "delete" )
    $content = $content . $_record["updator"] . " deleted an existing product in Product Database. Below is the details of the product:";

$content = $content . '
                    </td>
                </tr>
                </tbody>
            </table>

<table style="width: 100%">
<tbody>
<tr>
    <td style="background-color: #F0F0F0; border: 2px solid #FFFFFF; padding: 8px; font-size: 14px; width: 280px; font-weight: 600;">
        <eng style="font-size: 16px;">
            Image
        </eng>
    </td>
    <td style="background-color: #F0F0F0; border: 2px solid #FFFFFF; padding: 8px; width: 440px; font-size: 16px;">';

    if($_record["photo1"] != "")
        $content .= "<img style='max-width: 400px; max-height: 400px;' src='https://storage.cloud.google.com/feliiximg/" . $_record["photo1"] . "' >";

    $content .= '</td>
    </tr>

    <tr>
        <td style="background-color: #F0F0F0; border: 2px solid #FFFFFF; padding: 8px; font-size: 14px; width: 280px; font-weight: 600;">
            <eng style="font-size: 16px;">
                ID
            </eng>
        </td>
        <td style="background-color: #F0F0F0; border: 2px solid #FFFFFF; padding: 8px; width: 440px; font-size: 16px;">';

    $content .= $_record["id"];

    $content .= '</td>
    </tr>

    <tr>
        <td style="background-color: #F0F0F0; border: 2px solid #FFFFFF; padding: 8px; font-size: 14px; width: 280px; font-weight: 600;">
            <eng style="font-size: 16px;">
                Code
            </eng>
        </td>
        <td style="background-color: #F0F0F0; border: 2px solid #FFFFFF; padding: 8px; width: 440px; font-size: 16px;">';

    $content .= $_record["code"];

    $content .= '</td>
    </tr>

    <tr>
        <td style="background-color: #F0F0F0; border: 2px solid #FFFFFF; padding: 8px; font-size: 14px; width: 280px; font-weight: 600;">
            <eng style="font-size: 16px;">
                Brand
            </eng>
        </td>
        <td style="background-color: #F0F0F0; border: 2px solid #FFFFFF; padding: 8px; width: 440px; font-size: 16px;">';

    $content .= $_record["brand"];

    $content .= '</td>
    </tr>

    <tr>
        <td style="background-color: #F0F0F0; border: 2px solid #FFFFFF; padding: 8px; font-size: 14px; width: 280px; font-weight: 600;">
            <eng style="font-size: 16px;">
                Specification
            </eng>
        </td>
        <td style="background-color: #F0F0F0; border: 2px solid #FFFFFF; padding: 8px; width: 440px; font-size: 16px;">';

        foreach ($_record["attribute_list"] as &$att) {

            $att_value = "";

            foreach($att["value"] as &$att_value_list)
            {
                $att_value .=  $att_value_list . ", ";
            }

            $att_value = rtrim($att_value, ", ");

            $content .= "<ul style='margin: 8px 0; padding-left: 20px;'><li>" . $att["category"] . ' : ' . $att_value . '</li></ul>';
        }

    $content .= '</td>
    </tr>

    <tr>
        <td style="background-color: #F0F0F0; border: 2px solid #FFFFFF; padding: 8px; font-size: 14px; width: 280px; font-weight: 600;">
            <eng style="font-size: 16px;">
                Created Time
            </eng>
        </td>
        <td style="background-color: #F0F0F0; border: 2px solid #FFFFFF; padding: 8px; width: 440px; font-size: 16px;">';

    $content .= $_record["creator"] . ' at ' . $_record["created_at"];

if( $action == "update" || $action == "delete" )
{
    $content .= '</td>
    </tr>

    <tr>
        <td style="background-color: #F0F0F0; border: 2px solid #FFFFFF; padding: 8px; font-size: 14px; width: 280px; font-weight: 600;">
            <eng style="font-size: 16px;">
                Last Updated Time
            </eng>
        </td>
        <td style="background-color: #F0F0F0; border: 2px solid #FFFFFF; padding: 8px; width: 440px; font-size: 16px;">';

        if($_record["updator"] != "")
            $content .= $_record["updator"] . ' at ' . $_record["updated_at"];
}

if( $action == "delete")
{
    $content .= '</td>
    </tr>

    <tr>
        <td style="background-color: #F0F0F0; border: 2px solid #FFFFFF; padding: 8px; font-size: 14px; width: 280px; font-weight: 600;">
            <eng style="font-size: 16px;">
                Deleted Time
            </eng>
        </td>
        <td style="background-color: #F0F0F0; border: 2px solid #FFFFFF; padding: 8px; width: 440px; font-size: 16px;">';

    $content .= $_record["deletor"] . ' at ' . $_record["deleted_time"];
}
    $content .= '</td>

        </tr>
        </tbody>
    </table>';

    // tail
    $content .= '<hr style="margin-top: 45px;">

    <table style="width: 100%;">
        <tbody>
        <tr>
            <td style="font-size: 16px; padding: 5px 0 0 5px; line-height: 1.5;">';

            if( $action == "add" || $action == "update" ){
                $content = $content . 'Please log on to Feliix >> Product Database >> Product Catalog to view the product.<br>';
                $content = $content . 'URL:  <a href="https://feliix.myvnc.com/product_catalog_code?d=' . $_record["id"] . '">https://feliix.myvnc.com/product_catalog_code?d=' . $_record["id"] . '</a> ';
            }
            if( $action == "delete" ){
                $content = $content . 'URL:  <a href="https://feliix.myvnc.com/product_display_code?id=' . $_record["id"] . '">https://feliix.myvnc.com/product_display_code?id=' . $_record["id"] . '</a> ';
            }

    $content = $content . '
            </td>
        </tr>
        </tbody>

    </table>

</div>


</body>
</html>';



    $mail->MsgHTML($content);
    if($mail->Send()) {
        logMail($email1, $content);
        return true;
//        echo "Error while sending Email.";
//        var_dump($mail);
    } else {
        logMail($email1, $mail->ErrorInfo . $content);
        return false;
//        echo "Email sent successfully";
    }
}

function GetChargeNotifiersByTitle($title)
{
    $database = new Database();
    $db = $database->getConnection();

    $sql = "
            SELECT username, email, title 
            FROM user u
            LEFT JOIN user_title ut
            ON u.title_id = ut.id 
            WHERE title IN(
                '" . $title . "'
            )  and u.status = 1";
    

    $merged_results = array();

    $stmt = $db->prepare($sql);
    $stmt->execute();

    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        $merged_results[] = $row;
    }

    return $merged_results;
}

function GetAccessNotifiersByDepartment($department)
{
    $database = new Database();
    $db = $database->getConnection();

    $sql = "
        SELECT username, email, department
        FROM user u LEFT JOIN user_department ut
        ON u.apartment_id  = ut.id 
        WHERE department IN(
            '" . $department . "')  and u.status = 1";

    $merged_results = array();

    $stmt = $db->prepare($sql);
    $stmt->execute();

    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        $merged_results[] = $row;
    }

    return $merged_results;
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
                'Sales Manager',
                'Office Systems Manager',
                'Office Systems Assistant Manager',
                'Operations Manager',
                'Managing Director')  and u.status = 1";
    }
    else
    {
        $sql = "
            SELECT username, email, title 
            FROM user u
            LEFT JOIN user_title ut
            ON u.title_id = ut.id 
            WHERE title IN(
                'Sales Manager',
                'Lighting Manager',
                'Lighting Assistant Manager',
                'Operations Manager',
                'Managing Director')  and u.status = 1";
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
                'Managing Director') and u.status = 1";
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
                'Managing Director') and u.status = 1";
    }

    $merged_results = array();

    $stmt = $db->prepare($sql);
    $stmt->execute();

    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        $merged_results[] = $row;
    }

    return $merged_results;
}

function GetProjectServiceNotifiers()
{
    $database = new Database();
    $db = $database->getConnection();

    $sql = "
            SELECT username, email, title 
            FROM user u
            LEFT JOIN user_title ut
            ON u.title_id = ut.id 
            WHERE title IN(
                'Engineering Manager') and u.status = 1";


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
            'Managing Director') and u.status = 1";

    $merged_results = array();

    $stmt = $db->prepare($sql);
    $stmt->execute();

    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        $merged_results[] = $row;
    }

    return $merged_results;
}

function GetAccessNotifiersByName($title, $order_type)
{
    $database = new Database();
    $db = $database->getConnection();

    $sql = "
            SELECT username, email, department 
            FROM user
            left join user_department ud on user.apartment_id = ud.id
            WHERE username IN(
                '" . $title . "'
            )  and user.status = 1";
    
    $merged_results = array();

    $stmt = $db->prepare($sql);
    $stmt->execute();

    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        $merged_results[] = $row;
    }

    return $merged_results;
}

function GetAccessNotifiers($field, $order_type){
    $field = trim($field);
    $username = "";
    $database = new Database();
    $db = $database->getConnection();

    $query = "SELECT ". $field . " FROM access_control ";
    $stmt = $db->prepare( $query );
    $stmt->execute();
    while($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        $username = $row[$field];
    }

    // splite usernames and implode with quotes
    $usernames = explode(",", $username);
    $username = implode("','", $usernames);

    if($username == '')
     return [];
    
    $sql = "
        SELECT username, email, department 
        FROM user
        left join user_department ud on user.apartment_id = ud.id
        WHERE username IN('" . $username . "') and user.status = 1";

    // get first character from order type
    $department = substr($order_type, 0, 1);

    if($field == 'access1')
    {
        if($department == 'O')
        {
            $sql = $sql . " and department = 'Office'";
        }

        if($department == 'L')
        {
            $sql = $sql . " and department = 'Lighting'";
        }
    }

    $merged_results = array();

    $stmt = $db->prepare($sql);
    $stmt->execute();

    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        $merged_results[] = $row;
    }

    return $merged_results;

}

function GetProjectCategoryByStageId($id)
{

    $catagory_id = 1;
    $database = new Database();
    $db = $database->getConnection();

    $sql = "
            select catagory_id from project_stages ps, project_main p where ps.project_id = p.id and ps.id =  " . $id;
    
    $merged_results = array();

    $stmt = $db->prepare($sql);
    $stmt->execute();

    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        $catagory_id = $row["catagory_id"];
    }

    return $catagory_id;
}

function GetProjectCategoryByProjectId($id)
{
    

    $catagory_id = 1;
    $database = new Database();
    $db = $database->getConnection();

    $sql = "
            select catagory_id from project_main where id =  " . $id;
    
    $merged_results = array();

    $stmt = $db->prepare($sql);
    $stmt->execute();

    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        $catagory_id = $row["catagory_id"];
    }

    return $catagory_id;
}

function send_car_approval_mail_1($to, $cc, $project, $creator, $date_check, $time_check, $service_check, $driver_check, $date, $time, $service, $att)
{
    $conf = new Conf();

    $mail = new PHPMailer();
    $mail->IsSMTP();
    $mail->Mailer = "smtp";
    $mail->CharSet = 'UTF-8';
    $mail->Encoding = 'base64';

    // $mail->SMTPDebug  = 0;
    // $mail->SMTPAuth   = true;
    // $mail->SMTPSecure = "ssl";
    // $mail->Port       = 465;
    // $mail->SMTPKeepAlive = true;
    // $mail->Host       = $conf::$mail_host;
    // $mail->Username   = $conf::$mail_username;
    // $mail->Password   = $conf::$mail_password;

    $mail = SetupMail($mail, $conf);

    $mail->IsHTML(true);

    $mail->SetFrom("feliix.it@gmail.com", "Feliix.System");
    $mail->AddReplyTo("feliix.it@gmail.com", "Feliix.System");

    $notifior = array();
    $notifior = GetNotifiersByName($to);
    foreach($notifior as &$list)
    {
        $mail->AddAddress($list["email"], $list["username"]);
    }

    $notifior = GetNotifiersByName($cc);
    foreach($notifior as &$list)
    {
        $mail->AddCC($list["email"], $list["username"]);
    }

    $notifior = GetCarCheckers();
    foreach($notifior as &$list)
    {
        $mail->AddCC($list["email"], $list["username"]);
    }

    $mail->addAttachment($att);

    $mail->Subject = "[Car Schedule] Your request of car schedule has been approved";
    $content =  "<p>Dear all,</p>";
    $content = $content . "<p>Your request of car schedule has been approved. Below is the details:</p>";
    $content = $content . "<p>Approved Result</p>";
    $content = $content . "<p>Date: " . $date_check . "</p>";
    $content = $content . "<p>Time: " . $time_check . "</p>";
    $content = $content . "<p>Assigned Car: " . $service_check . "</p>";
    $content = $content . "<p>Assigned Driver: " . $driver_check . "</p>";
    $content = $content . "<p>------------------------------------------------------------------------------</p>";
    $content = $content . "<p>Content of Request</p>";
    $content = $content . "<p>Schedule Name: " . $project . "</p>";
    $content = $content . "<p>Creator: " . $creator . "</p>";
    $content = $content . "<p>Date Use: " . $date . "</p>";
    $content = $content . "<p>Time: " . $time . "</p>";
    $content = $content . "<p>Car Use: " . $service . "</p>";

    $content = $content . "<p></p>";

    $mail->MsgHTML($content);
    if($mail->Send()) {
        logMail($creator, $content);
        return true;
//        echo "Error while sending Email.";
//        var_dump($mail);
    } else {
        logMail($creator, $mail->ErrorInfo . $content);
        return false;
//        echo "Email sent successfully";
    }

}


function send_car_request_mail_2($to, $cc, $project, $creator, $date_check, $time_check, $service_check, $driver_check, $date, $time, $service, $att)
{
    $conf = new Conf();

    $mail = new PHPMailer();
    $mail->IsSMTP();
    $mail->Mailer = "smtp";
    $mail->CharSet = 'UTF-8';
    $mail->Encoding = 'base64';

    // $mail->SMTPDebug  = 0;
    // $mail->SMTPAuth   = true;
    // $mail->SMTPSecure = "ssl";
    // $mail->Port       = 465;
    // $mail->SMTPKeepAlive = true;
    // $mail->Host       = $conf::$mail_host;
    // $mail->Username   = $conf::$mail_username;
    // $mail->Password   = $conf::$mail_password;

    $mail = SetupMail($mail, $conf);

    $mail->IsHTML(true);

    $mail->SetFrom("feliix.it@gmail.com", "Feliix.System");
    $mail->AddReplyTo("feliix.it@gmail.com", "Feliix.System");

    $notifior = array();
    $notifior = GetNotifiersByName($to);
    foreach($notifior as &$list)
    {
        $mail->AddCC($list["email"], $list["username"]);
    }


    $notifior = GetCarChecker1();
    $checker1 = "";
    foreach($notifior as &$list)
    {
        $mail->AddAddress($list["email"], $list["username"]);
        $checker1 .= $list["username"] . ", ";
    }

    $checker1 = rtrim($checker1, ", ");

    $mail->addAttachment($att);

    $mail->Subject = "[Car Schedule] A request of car schedule is waiting for your approval";
    $content =  "<p>Dear " . $checker1 . ",</p>";
    $content = $content . "<p>A request of car schedule is waiting for your approval. Below is the details:</p>";
    $content = $content . "<p>Schedule Name: " . $project . "</p>";
    $content = $content . "<p>Creator: " . $creator . "</p>";
    $content = $content . "<p>Date Use: " . $date . "</p>";
    $content = $content . "<p>Time: " . $time . "</p>";
    $content = $content . "<p>Car Use: " . $service . "</p>";

    $content = $content . "<p></p>";

    $mail->MsgHTML($content);
    if($mail->Send()) {
        logMail($creator, $content);
        return true;
//        echo "Error while sending Email.";
//        var_dump($mail);
    } else {
        logMail($creator, $mail->ErrorInfo . $content);
        return false;
//        echo "Email sent successfully";
    }

}

function withdraw_car_approval_mail_3($to, $cc, $project, $creator, $date_check, $time_check, $service_check, $driver_check, $date, $time, $service, $att, $requestor)
{
    $conf = new Conf();

    $mail = new PHPMailer();
    $mail->IsSMTP();
    $mail->Mailer = "smtp";
    $mail->CharSet = 'UTF-8';
    $mail->Encoding = 'base64';

    // $mail->SMTPDebug  = 0;
    // $mail->SMTPAuth   = true;
    // $mail->SMTPSecure = "ssl";
    // $mail->Port       = 465;
    // $mail->SMTPKeepAlive = true;
    // $mail->Host       = $conf::$mail_host;
    // $mail->Username   = $conf::$mail_username;
    // $mail->Password   = $conf::$mail_password;

    $mail = SetupMail($mail, $conf);

    $mail->IsHTML(true);

    $mail->SetFrom("feliix.it@gmail.com", "Feliix.System");
    $mail->AddReplyTo("feliix.it@gmail.com", "Feliix.System");

    $notifior = array();
    $notifior = GetNotifiersByName($to);
    foreach($notifior as &$list)
    {
        $mail->AddCC($list["email"], $list["username"]);
    }


    $notifior = GetCarCheckers();
    $checker1 = "";
    foreach($notifior as &$list)
    {
        $mail->AddAddress($list["email"], $list["username"]);
        $checker1 .= $list["username"] . ", ";
    }

    $checker1 = rtrim($checker1, ", ");

    $mail->addAttachment($att);

    $mail->Subject = "[Car Schedule] An approved request of car schedule was withdrawn by " . $requestor;
    $content =  "<p>Dear all,</p>";
    $content = $content . "<p>An approved request of car schedule was withdrawn by " . $requestor . ". Below is the details:</p>";
    $content = $content . "<p>Previous Approved Result</p>";
    $content = $content . "<p>Date: " . $date_check . "</p>";
    $content = $content . "<p>Time: " . $time_check . "</p>";
    $content = $content . "<p>Assigned Car: " . $service_check . "</p>";
    $content = $content . "<p>Assigned Driver: " . $driver_check . "</p>";
    $content = $content . "<p>------------------------------------------------------------------------------</p>";
    $content = $content . "<p>Content of Request</p>";
    $content = $content . "<p>Schedule Name: " . $project . "</p>";
    $content = $content . "<p>Creator: " . $creator . "</p>";
    $content = $content . "<p>Date Use: " . $date . "</p>";
    $content = $content . "<p>Time: " . $time . "</p>";
    $content = $content . "<p>Car Use: " . $service . "</p>";

    $content = $content . "<p></p>";

    $mail->MsgHTML($content);
    if($mail->Send()) {
        logMail($creator, $content);
        return true;
//        echo "Error while sending Email.";
//        var_dump($mail);
    } else {
        logMail($creator, $mail->ErrorInfo . $content);
        return false;
//        echo "Email sent successfully";
    }

}


function withdraw_car_request_mail_4($to, $cc, $project, $creator, $date_check, $time_check, $service_check, $driver_check, $date, $time, $service, $att, $requestor)
{
    $conf = new Conf();

    $mail = new PHPMailer();
    $mail->IsSMTP();
    $mail->Mailer = "smtp";
    $mail->CharSet = 'UTF-8';
    $mail->Encoding = 'base64';

    // $mail->SMTPDebug  = 0;
    // $mail->SMTPAuth   = true;
    // $mail->SMTPSecure = "ssl";
    // $mail->Port       = 465;
    // $mail->SMTPKeepAlive = true;
    // $mail->Host       = $conf::$mail_host;
    // $mail->Username   = $conf::$mail_username;
    // $mail->Password   = $conf::$mail_password;

    $mail = SetupMail($mail, $conf);

    $mail->IsHTML(true);

    $mail->SetFrom("feliix.it@gmail.com", "Feliix.System");
    $mail->AddReplyTo("feliix.it@gmail.com", "Feliix.System");

    $notifior = array();
    $notifior = GetNotifiersByName($to);
    foreach($notifior as &$list)
    {
        $mail->AddCC($list["email"], $list["username"]);
    }


    $notifior = GetCarChecker1();
    $checker1 = "";
    foreach($notifior as &$list)
    {
        $mail->AddAddress($list["email"], $list["username"]);
        $checker1 .= $list["username"] . ", ";
    }

    $checker1 = rtrim($checker1, ", ");

    $mail->addAttachment($att);

    $mail->Subject = "[Car Schedule] A request of car schedule was withdrawn by " . $requestor;
    $content =  "<p>Dear " . $checker1 . ",</p>";
    $content = $content . "<p>A request of car schedule was withdrawn by " . $requestor . ". Below is the details:</p>";
    $content = $content . "<p>Content of Request</p>";
    $content = $content . "<p>Schedule Name: " . $project . "</p>";
    $content = $content . "<p>Creator: " . $creator . "</p>";
    $content = $content . "<p>Date Use: " . $date . "</p>";
    $content = $content . "<p>Time: " . $time . "</p>";
    $content = $content . "<p>Car Use: " . $service . "</p>";

    $content = $content . "<p></p>";

    $mail->MsgHTML($content);
    if($mail->Send()) {
        logMail($creator, $content);
        return true;
//        echo "Error while sending Email.";
//        var_dump($mail);
    } else {
        logMail($creator, $mail->ErrorInfo . $content);
        return false;
//        echo "Email sent successfully";
    }

}


function delete_car_approval_mail_5($to, $cc, $project, $creator, $date_check, $time_check, $service_check, $driver_check, $date, $time, $service, $att, $requestor)
{
    $conf = new Conf();

    $mail = new PHPMailer();
    $mail->IsSMTP();
    $mail->Mailer = "smtp";
    $mail->CharSet = 'UTF-8';
    $mail->Encoding = 'base64';

    // $mail->SMTPDebug  = 0;
    // $mail->SMTPAuth   = true;
    // $mail->SMTPSecure = "ssl";
    // $mail->Port       = 465;
    // $mail->SMTPKeepAlive = true;
    // $mail->Host       = $conf::$mail_host;
    // $mail->Username   = $conf::$mail_username;
    // $mail->Password   = $conf::$mail_password;

    $mail = SetupMail($mail, $conf);

    $mail->IsHTML(true);

    $mail->SetFrom("feliix.it@gmail.com", "Feliix.System");
    $mail->AddReplyTo("feliix.it@gmail.com", "Feliix.System");

    $notifior = array();
    $notifior = GetNotifiersByName($to);
    foreach($notifior as &$list)
    {
        $mail->AddCC($list["email"], $list["username"]);
    }


    $notifior = GetCarCheckers();
    $checker1 = "";
    foreach($notifior as &$list)
    {
        $mail->AddAddress($list["email"], $list["username"]);
        $checker1 .= $list["username"] . ", ";
    }

    $checker1 = rtrim($checker1, ", ");

    $mail->addAttachment($att);

    $mail->Subject = "[Car Schedule] An approved request of car schedule was withdrawn and deleted by " . $requestor;
    $content =  "<p>Dear all,</p>";
    $content = $content . "<p>An approved request of car schedule was withdrawn and deleted by " . $requestor . ". Below is the details:</p>";
    $content = $content . "<p>Previous Approved Result</p>";
    $content = $content . "<p>Date: " . $date_check . "</p>";
    $content = $content . "<p>Time: " . $time_check . "</p>";
    $content = $content . "<p>Assigned Car: " . $service_check . "</p>";
    $content = $content . "<p>Assigned Driver: " . $driver_check . "</p>";
    $content = $content . "<p>------------------------------------------------------------------------------</p>";
    $content = $content . "<p>Content of Request</p>";
    $content = $content . "<p>Schedule Name: " . $project . "</p>";
    $content = $content . "<p>Creator: " . $creator . "</p>";
    $content = $content . "<p>Date Use: " . $date . "</p>";
    $content = $content . "<p>Time: " . $time . "</p>";
    $content = $content . "<p>Car Use: " . $service . "</p>";

    $content = $content . "<p></p>";

    $mail->MsgHTML($content);
    if($mail->Send()) {
        logMail($creator, $content);
        return true;
//        echo "Error while sending Email.";
//        var_dump($mail);
    } else {
        logMail($creator, $mail->ErrorInfo . $content);
        return false;
//        echo "Email sent successfully";
    }

}


function delete_car_request_mail_6($to, $cc, $project, $creator, $date_check, $time_check, $service_check, $driver_check, $date, $time, $service, $att, $requestor)
{
    $conf = new Conf();

    $mail = new PHPMailer();
    $mail->IsSMTP();
    $mail->Mailer = "smtp";
    $mail->CharSet = 'UTF-8';
    $mail->Encoding = 'base64';

    // $mail->SMTPDebug  = 0;
    // $mail->SMTPAuth   = true;
    // $mail->SMTPSecure = "ssl";
    // $mail->Port       = 465;
    // $mail->SMTPKeepAlive = true;
    // $mail->Host       = $conf::$mail_host;
    // $mail->Username   = $conf::$mail_username;
    // $mail->Password   = $conf::$mail_password;

    $mail = SetupMail($mail, $conf);

    $mail->IsHTML(true);

    $mail->SetFrom("feliix.it@gmail.com", "Feliix.System");
    $mail->AddReplyTo("feliix.it@gmail.com", "Feliix.System");

    $notifior = array();
    $notifior = GetNotifiersByName($to);
    foreach($notifior as &$list)
    {
        $mail->AddCC($list["email"], $list["username"]);
    }


    $notifior = GetCarChecker1();
    $checker1 = "";
    foreach($notifior as &$list)
    {
        $mail->AddAddress($list["email"], $list["username"]);
        $checker1 .= $list["username"] . ", ";
    }

    $checker1 = rtrim($checker1, ", ");

    $mail->addAttachment($att);

    $mail->Subject = "[Car Schedule] A request of car schedule was withdrawn and deleted by " . $requestor;
    $content =  "<p>Dear " . $checker1 . ",</p>";
    $content = $content . "<p>A request of car schedule was withdrawn and deleted by " . $requestor . ". Below is the details:</p>";
    $content = $content . "<p>Content of Request</p>";
    $content = $content . "<p>Schedule Name: " . $project . "</p>";
    $content = $content . "<p>Creator: " . $creator . "</p>";
    $content = $content . "<p>Date Use: " . $date . "</p>";
    $content = $content . "<p>Time: " . $time . "</p>";
    $content = $content . "<p>Car Use: " . $service . "</p>";

    $content = $content . "<p></p>";

    $mail->MsgHTML($content);
    if($mail->Send()) {
        logMail($creator, $content);
        return true;
//        echo "Error while sending Email.";
//        var_dump($mail);
    } else {
        logMail($creator, $mail->ErrorInfo . $content);
        return false;
//        echo "Email sent successfully";
    }

}

function SetupMail($mail, $conf)
{
    $mail->SMTPDebug  = 0;
    $mail->SMTPAuth   = true;
    $mail->SMTPSecure = "ssl";
    $mail->Port       = 465;
    $mail->SMTPKeepAlive = true;
    $mail->Host       = $conf::$mail_host;
    $mail->Username   = $conf::$mail_username;
    $mail->Password   = $conf::$mail_password;


    // $mail->SMTPDebug  = 0;
    // $mail->SMTPAuth   = true;
    // $mail->SMTPSecure = "tls";
    // $mail->Port       = 587;
    // $mail->SMTPKeepAlive = true;
    // $mail->Host       = 'smtp.ethereal.email';
    // $mail->Username   = 'jermey.wilkinson@ethereal.email';
    // $mail->Password   = 'zXX3N6QwJ5AYZUjbKe';

    // $mail->SMTPDebug  = 0;
    // $mail->SMTPAuth   = true;
    // $mail->SMTPSecure = "tls";
    // $mail->Port       = 587;
    // $mail->SMTPKeepAlive = true;
    // $mail->Host       = 'smtp.ethereal.email';
    // $mail->Username   = 'calista.lubowitz@ethereal.email';
    // $mail->Password   = 'VzkRWsx6FszvrQ1ZTW';

    return $mail;

}

?>