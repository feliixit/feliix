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

$password = (isset($_POST['password']) ?  $_POST['password'] : "");
$token = (isset($_POST['token']) ?  $_POST['token'] : "");

$sql = "select * from password_reset where token = '$token' limit 1";

$stmt = $db->prepare( $sql );
$stmt->execute();

$email = '';
$status = 0;

while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
    $id = $row['id'];
    $email = $row['email'];
    $status = $row['status'];
}

$login_history->ip = $_SERVER['REMOTE_ADDR'];

if($status == 1)
{
    echo json_encode(array("error" => "Password reset expired, please reset again."));
    die();
}

// check if email exists and if password is correct
//if($user_exists && password_verify($password, $user->password) && $cap == 1 && $user->status == 1){
if($email != ''){
    if(!empty($password)){
        $password=htmlspecialchars(strip_tags($password));
        $password_hash = password_hash($password, PASSWORD_BCRYPT);
    }
    // store token in the password-reset database table against the user's email
    $sql = "UPDATE user
    SET
        password = :password
    WHERE email = :email";

    $stmt = $db->prepare($sql);

    $stmt->bindParam(':password', $password_hash);
    $stmt->bindParam(':email', $email);
        
    // execute the query
    try {
        // execute the query, also check if query was successful
        if (!$stmt->execute()) {
            $arr = $stmt->errorInfo();
            error_log($arr[2]);
            
            echo json_encode(array("error" => $arr[2]));
            die();
        }
    } catch (Exception $e) {
        error_log($e->getMessage());

        http_response_code(501);
        echo json_encode(array("error" => $e->getMessage()));
        die();
    }

    $sql = "UPDATE password_reset
    SET
        status = 1,
        updated_at = now()
    WHERE id = :id";

    $stmt = $db->prepare($sql);

    $stmt->bindParam(':id', $id);

    try {
        // execute the query, also check if query was successful
        if (!$stmt->execute()) {
            $arr = $stmt->errorInfo();
            error_log($arr[2]);
            
            echo json_encode(array("error" => $arr[2]));
            die();
        }
    } catch (Exception $e) {
        error_log($e->getMessage());

        http_response_code(501);
        echo json_encode(array("error" => $e->getMessage()));
        die();
    }
}

?>