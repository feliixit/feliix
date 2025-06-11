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

include_once 'libs/php-jwt-master/src/BeforeValidException.php';
include_once 'libs/php-jwt-master/src/ExpiredException.php';
include_once 'libs/php-jwt-master/src/SignatureInvalidException.php';
include_once 'libs/php-jwt-master/src/JWT.php';

use Google\Cloud\Storage\StorageClient;

upload_feliix_file();
upload_ludb_file();
send_schedule_edit_goodby_mail();

function upload_feliix_file()
{
    $feliix_file = '/home/feliix_it/mysql/' . date('Y-m-d') . '-feliix.gz';
    $conf = new Conf();

    $storage = new StorageClient([
        'projectId' => 'predictive-fx-284008',
        'keyFilePath' => $conf::$gcp_key
    ]);

    $bucket = $storage->bucket('feliiximg');

    $file = fopen($feliix_file, 'r');

    $object = $bucket->upload($file, [
        'name' => date('Y-m-d') . '-feliix.gz'
    ]);
}

function upload_ludb_file()
{
    $ludb_file = '/home/feliix_it/mysql/' . date('Y-m-d') . '-ludb.gz';
    $conf = new Conf();

    $storage = new StorageClient([
        'projectId' => 'predictive-fx-284008',
        'keyFilePath' => $conf::$gcp_key
    ]);

    $bucket = $storage->bucket('feliiximg');

    $file = fopen($ludb_file, 'r');

    $object = $bucket->upload($file, [
        'name' => date('Y-m-d') . '-ludb.gz'
    ]);
}

function send_schedule_edit_goodby_mail()
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

    $mail->SetFrom("feliix.it@gmail.com", "Feliix.System");
    $mail->AddReplyTo("feliix.it@gmail.com", "Feliix.System");

    $mail->AddAddress("dereckyin@gmail.com", "Dereck Yin");

    // today
    $today = date('Y-m-d');

    $mail->Subject = "[" . $today . "] Database files";
    $content =  "<p>Dear, It's your " . $today . " db subscription.</p>";

    $baseURL = "https://storage.googleapis.com/feliiximg/";

    $content .= "<p>Here is the download link for the database files:</p>";
    $content .= "<p><a href='" . $baseURL . date('Y-m-d') . "-feliix.gz'>Feliix Database</a></p>";
    $content .= "<p><a href='" . $baseURL . date('Y-m-d') . "-ludb.gz'>LUDB Database</a></p>";

    $mail->MsgHTML($content);
    if($mail->Send()) {
        return true;
//        echo "Error while sending Email.";
//        var_dump($mail);
    } else {
        return false;
//        echo "Email sent successfully";
    }

}