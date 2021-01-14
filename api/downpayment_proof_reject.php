<?php
error_reporting(E_ERROR | E_PARSE);

header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: *");
header("Access-Control-Max-Age: 3600");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

$jwt = (isset($_COOKIE['jwt']) ?  $_COOKIE['jwt'] : null);
include_once 'config/core.php';
include_once 'libs/php-jwt-master/src/BeforeValidException.php';
include_once 'libs/php-jwt-master/src/ExpiredException.php';
include_once 'libs/php-jwt-master/src/SignatureInvalidException.php';
include_once 'libs/php-jwt-master/src/JWT.php';
use \Firebase\JWT\JWT;
if ( !isset( $jwt ) ) {
    http_response_code(401);

    echo json_encode(array("message" => "Access denied."));
    die();
}
else
{
    try {
        // decode jwt
        $decoded = JWT::decode($jwt, $key, array('HS256'));
        $user_id = $decoded->data->id;
    }
        // if decode fails, it means jwt is invalid
    catch (Exception $e){

        http_response_code(401);

        echo json_encode(array("message" => "Access denied."));
        die();
    }
}

include_once 'config/database.php';
include_once 'mail.php';

$database = new Database();
$db = $database->getConnection();

$merged_results = [];

$id = (isset($_POST['id']) ?  $_POST['id'] : 0);
$remark = (isset($_POST['remark']) ?  $_POST['remark'] : '');

$query = "
    update project_proof a 
    set a.updated_id = :updated_id, a.updated_at = NOW(), a.STATUS = -1, proof_remark = :proof_remark
    WHERE a.STATUS = 0
    AND a.id = :id";

$stmt = $db->prepare( $query );

$stmt->bindParam(':updated_id', $user_id);
$stmt->bindParam(':proof_remark', $remark);
$stmt->bindParam(':id', $id);

if (!$stmt->execute())
{
    $arr = $stmt->errorInfo();
    error_log($arr[2]);
}
else
{

    // send mail
    $subquery = "SELECT p.project_name, pm.remark, u.username, u.email, pm.created_at, pm.status, pm.proof_remark, p.catagory_id  FROM project_proof pm left join user u on u.id = pm.create_id LEFT JOIN project_main p ON p.id = pm.project_id  WHERE pm.id = " . $id . "  ";

    $stmt = $db->prepare( $subquery );
    $stmt->execute();

    $project_name = "";
    $remark = "";
    $leaver = "";
    $subtime = "";
    $status = 0;
    $proof_remark = "";
    $email1 = "";
    $category = "";

    while($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        $project_name = $row['project_name'];
        $remark = $row['remark'];
        $leaver = $row['username'];
        $subtime = $row['created_at'];
        $status = $row['status'];
        $proof_remark = $row['proof_remark'];
        $email1 = $row['email'];
        $category = $row['catagory_id'];
    }


    send_check_notify_mail($leaver, $email1, $project_name, $remark, $subtime, $proof_remark, "False", $category);
}

echo json_encode($merged_results, JSON_UNESCAPED_SLASHES);
