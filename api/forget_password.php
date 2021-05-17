<?php
error_reporting(E_ERROR | E_PARSE);

header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: POST");
header("Access-Control-Max-Age: 3600");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

// files needed to connect to database
include_once 'config/database.php';
include_once 'objects/user.php';
include_once 'objects/login_history.php';
include_once 'config/conf.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
require '../PHPMailer-master/src/Exception.php';
require '../PHPMailer-master/src/PHPMailer.php';
require '../PHPMailer-master/src/SMTP.php';

$database = new Database();
$db = $database->getConnection();

$user = new User($db);
$login_history = new LoginHistory($db);

$conf = new Conf();

$email = (isset($_POST['email']) ?  $_POST['email'] : "");

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['recaptcha_response'])) {

    // Build POST request:
    $recaptcha_url = 'https://www.google.com/recaptcha/api/siteverify';
    $recaptcha_response = $_POST['recaptcha_response'];

    // Make and decode POST request:
    $recaptcha = file_get_contents($recaptcha_url . '?secret=' . $conf::$recaptcha_secret . '&response=' . $recaptcha_response);
    $recaptcha = json_decode($recaptcha);

    // Take action based on the score returned:
    if ($recaptcha->score >= 0.5) 
        $cap = 1;
} 

// set product property values
$user->email = $email;
$user_exists = $user->userCanLogin();

if($user_exists)
    $login_history->uid = $user->id;
else
    $login_history->uid = 0;

$login_history->ip = $_SERVER['REMOTE_ADDR'];

// check if email exists and if password is correct
//if($user_exists && password_verify($password, $user->password) && $cap == 1 && $user->status == 1){
if($login_history->uid != 0){
    $token = bin2hex(random_bytes(50));

    // store token in the password-reset database table against the user's email
    $sql = "INSERT INTO password_reset(email, token) VALUES ('$email', '$token')";

    $stmt = $db->prepare( $sql );
    $stmt->execute();
    

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
    $mail->AddAddress($email, $user->username);

    $mail->SetFrom("feliix.it@gmail.com", "Feliix.System");

    $msg = "Hi ". $user->username. ", click on this <a href=" . $conf::$mail_ip . "/new_password.php?token=" . $token . ">link</a> to reset your password on our site";
    $msg = wordwrap($msg,70);

    $content = "";
    $content = $content . "<p>" . $msg . "</p>";

    $content = $content . "<p> </p>";

    $mail->MsgHTML($content);
    if($mail->Send()) {
        //logMail($email, $content);
        return true;
//        echo "Error while sending Email.";
//        var_dump($mail);
    } else {
        //logMail($email, $mail->ErrorInfo);
        return false;
//        echo "Email sent successfully";
    }
  

}
// login failed
else{
    if($user->status == 0)
    {
        if($login_history->uid !== 0)
        {
            // write login log
            $login_history->status = "not activated";
            $login_history->create();
        }
        else
        {
            // write login log
            $login_history->status = $user->username . " not existed";
            $login_history->create();
        }


        $returnArray = array('error' => 'User is not activated.');
        $jsonEncodedReturnArray = json_encode($returnArray, JSON_PRETTY_PRINT);
    } else
    {
        // write login log
        $login_history->status = "Invalid user ID or password";
        $login_history->create();

        $returnArray = array('error' => 'Invalid user ID or password.');
        $jsonEncodedReturnArray = json_encode($returnArray, JSON_PRETTY_PRINT);
    }

    echo $jsonEncodedReturnArray;
}


?>