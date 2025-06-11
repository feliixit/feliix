<?php
error_reporting(0);
//header("Access-Control-Allow-Origin: https://feliix.myvnc.com");
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: *");
header("Access-Control-Max-Age: 3600");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");


include_once 'config/core.php';
include_once 'libs/php-jwt-master/src/BeforeValidException.php';
include_once 'libs/php-jwt-master/src/ExpiredException.php';
include_once 'libs/php-jwt-master/src/SignatureInvalidException.php';
include_once 'libs/php-jwt-master/src/JWT.php';

include_once 'config/database.php';
include_once 'config/conf.php';
require_once '../vendor/autoload.php';

$database = new Database();
$db = $database->getConnection();
$conf = new Conf();

use Google\Cloud\Storage\StorageClient;

use \Firebase\JWT\JWT;

$token = (isset($_GET['token']) ?  $_GET['token'] : null);

if (!isset($token)) {
    http_response_code(401);

    echo json_encode(array("message" => "Access denied."));
    die();
} else {

    $decoded = passport_decrypt($token);

    $data = json_decode($decoded);

    $username = $data->name;
    $user_email = $data->email;
    $pid = $data->id;

    $email = $user_email;
    $user_id = 0;

    $merged_results = array();

    // add if not exists

    $query = "SELECT *
                FROM leadership_assessment_review pr
              WHERE pr.status <> -1  " . ($pid != 0 ? " and pr.pid=$pid" : ' ')  . ($email != '' ? " and pr.email='$email'" : ' ');


    $stmt = $db->prepare($query);
    $stmt->execute();
    $last_id = 0;

    // add 64 answers to json
    // $answer_str = {"answer1": "", "answer2": "", ... "answer64": ""}
    $answer_str = "{";
    for($i = 1; $i <= 64; $i++)
    {
        $answer_str .= "\"answer" . $i . "\": \"0\", ";
    }

    for($i = 1; $i <= 3; $i++)
    {
        $answer_str .= "\"comment" . $i . "\": \"\", ";
    }

    $answer_str = rtrim($answer_str, ", ");
    $answer_str .= "}";
        
    if($stmt->rowCount() == 0)
    {
        $query_insert = "insert into leadership_assessment_review(pid, user_id, period, answer, status, email, create_id) values(:pid, :user_id, 1, :answer_str, 0, :email, :user_id)";
        $stmt = $db->prepare($query_insert);

        $stmt->bindParam(':pid', $pid);
        $stmt->bindParam(':user_id', $user_id);
        $stmt->bindParam(':answer_str', $answer_str);
        $stmt->bindParam(':email', $user_email);

        $stmt->execute();

        $last_id = $db->lastInsertId();
    }

    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        $merged_results[] = $row;
    }

    if(count($merged_results) == 0)
    {
        $merged_results[] = array("id" => $last_id, "pid" => $pid, "user_id" => 0, "period" => 1, "answer" => $answer_str, "status" => 0, "email" => $user_email, "create_id" => 0);
    }

    echo json_encode($merged_results, JSON_UNESCAPED_SLASHES);
}
