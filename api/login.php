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

$database = new Database();
$db = $database->getConnection();

$user = new User($db);
$login_history = new LoginHistory($db);

$username = (isset($_POST['username']) ?  $_POST['username'] : "");
$password = (isset($_POST['password']) ?  $_POST['password'] : "");

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['recaptcha_response'])) {

    // Build POST request:
    $recaptcha_url = 'https://www.google.com/recaptcha/api/siteverify';
    $recaptcha_secret = '6Le2uvQUAAAAAEtH76v-4KS_joDZ0ettksO6d1nz';
    $recaptcha_response = $_POST['recaptcha_response'];

    // Make and decode POST request:
    $recaptcha = file_get_contents($recaptcha_url . '?secret=' . $recaptcha_secret . '&response=' . $recaptcha_response);
    $recaptcha = json_decode($recaptcha);

    // Take action based on the score returned:
    if ($recaptcha->score >= 0.5) 
        $cap = 1;
} 

// set product property values
$user->email = $username;
$user_exists = $user->userCanLogin();

if($user_exists)
    $login_history->uid = $user->id;
else
    $login_history->uid = 0;

$login_history->ip = $_SERVER['REMOTE_ADDR'];


 
// generate json web token
include_once 'config/core.php';
include_once 'libs/php-jwt-master/src/BeforeValidException.php';
include_once 'libs/php-jwt-master/src/ExpiredException.php';
include_once 'libs/php-jwt-master/src/SignatureInvalidException.php';
include_once 'libs/php-jwt-master/src/JWT.php';
use \Firebase\JWT\JWT;


// check if email exists and if password is correct
//if($user_exists && password_verify($password, $user->password) && $cap == 1 && $user->status == 1){
if($user_exists && password_verify($password, $user->password)  && $user->status == 1){
    $token = array(
       "iss" => $iss,
       "aud" => $aud,
       "iat" => $iat,
       "nbf" => $nbf,
       "data" => array(
           "id" => $user->id,
           "username" => $user->username,
           "email" => $user->email,
           "is_admin" => $user->is_admin,
           "department" => $user->department,
           "position" => $user->position,
       )
    );

    // write login log
    $login_history->status = "login";
    $login_history->create();
 
    // set response code
    http_response_code(200);
 
    // generate jwt
    $jwt = JWT::encode($token, $key);
    echo json_encode(
            array(
                "message" => "Successful login.",
                "jwt" => $jwt,
                "uid" => passport_encrypt(base64_encode($user->username))
            )
        );
 
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