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
include_once 'config/conf.inc';
 
// get database connection
$database = new Database();
$db = $database->getConnection();
 
// instantiate product object
$user = new User($db);
$conf = new Conf();
 
// get posted data
//$data = json_decode(file_get_contents("php://input"));
 
// set product property values
$username = (isset($_POST['username']) ?  $_POST['username'] : "");
$email = (isset($_POST['email']) ?  $_POST['email'] : "");
$password = (isset($_POST['password1']) ?  $_POST['password1'] : "");

$user->username = $username;
$user->email = $email;
$user->password = $password;

// recaptchar
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


$filename = "";

if(isset($_FILES['file']['name'])) {
    
    $key = "myKey";
    $time = time();
    $hash = hash_hmac('sha256', $time, $key);
    $ext = pathinfo($_FILES['file']['name'], PATHINFO_EXTENSION);
    $filename = $time . $hash . "." . $ext;
    
    if (move_uploaded_file($_FILES["file"]["tmp_name"], $conf::$upload_path . $filename)) {
        $user->pic_url = $filename;
    }
}

// create the user
if(
    !empty($user->username) &&
    !empty($user->email) &&
    !empty($user->password) &&
    !$user->userExists() &&
    $user->create()
){
 
    // set response code
    http_response_code(200);
 
    // display message: user was created
    echo json_encode(array("message" => "User was created."));
}
 
// message if unable to create user
else{
 
    // set response code
    http_response_code(400);
 
    // display message: unable to create user
    echo json_encode(array("message" => "Unable to create user."));
}
?>