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
$database = new Database();
$db = $database->getConnection();

$merged_results = [];

$id = (isset($_POST['id']) ?  $_POST['id'] : 0);

$query = "
    update apply_for_leave a LEFT JOIN user u ON a.uid = u.id 
    set reject_id = " . $user_id . ", reject_at = NOW(), a.STATUS = -2
    WHERE a.STATUS <> -1 AND approval_id = 0 AND reject_id = 0 AND re_approval_id = 0 AND re_reject_id = 0 
    and uid IN 
    ( SELECT id FROM user WHERE apartment_id IN (SELECT apartment_id FROM leave_flow WHERE uid = " . $user_id . " and flow = 1))
    AND a.id = " . $id . "
";

$stmt = $db->prepare( $query );

if (!$stmt->execute())
{
    $arr = $stmt->errorInfo();
    error_log($arr[2]);
}

$query = "
    update apply_for_leave a LEFT JOIN user u ON a.uid = u.id 
    set re_reject_id = " . $user_id . ", re_reject_at = NOW(), a.STATUS = -2
    WHERE a.STATUS <> -1 AND approval_id <> 0 AND reject_id = 0 AND re_approval_id = 0 AND re_reject_id = 0 
    and uid IN 
    ( SELECT id FROM user WHERE apartment_id IN (SELECT apartment_id FROM leave_flow WHERE uid = " . $user_id . " and flow = 2))
    AND a.id = " . $id . "
";

$stmt = $db->prepare( $query );

if (!$stmt->execute())
{
    $arr = $stmt->errorInfo();
    error_log($arr[2]);
}

// 20250109 half day leave reject by kuan
if($user_id == 3){
    $query = "
        update apply_for_leave a LEFT JOIN user u ON a.uid = u.id 
        set reject_id = " . $user_id . ", reject_at = NOW(), a.STATUS = -2
        WHERE a.STATUS <> -1 AND approval_id = 0 AND reject_id = 0 AND re_approval_id = 0 AND re_reject_id = 0 and leave_type = 'H'
        AND a.id = " . $id . "
    ";

    $stmt = $db->prepare( $query );

    if (!$stmt->execute())
    {
        $arr = $stmt->errorInfo();
        error_log($arr[2]);
    }
}


$subquery = "SELECT GROUP_CONCAT(uid) uid FROM leave_flow WHERE apartment_id IN (SELECT apartment_id FROM leave_flow WHERE uid = " . $user_id . " and flow = 2) AND uid <> " . $user_id . " ";
//$subquery = " SELECT user.id, username, duty_date, duty_time, location FROM user LEFT JOIN on_duty ON user.id = on_duty.uid WHERE duty_date = '2020/05/11' AND on_duty.duty_type = 'A' and on_duty.uid = 1 ORDER BY on_duty.created_at ";

$stmt1 = $db->prepare( $subquery );
$stmt1->execute();

$row_id = "";

while($row1 = $stmt1->fetch(PDO::FETCH_ASSOC)) {
    $row_id = $row1['uid'];
}

if($row_id != "")
{
    $query = "
        update apply_for_leave a LEFT JOIN user u ON a.uid = u.id 
        set reject_id = " . $user_id . ", reject_at = NOW(),
        re_reject_id = " . $user_id . ", re_reject_at = NOW()
        WHERE a.STATUS <> -1 AND approval_id = 0 AND reject_id = 0 AND re_approval_id = 0 AND re_reject_id = 0 
        and uid IN 
        (" . $row_id . ")
        AND a.id in(" . $id . ")
    ";

    $stmt = $db->prepare( $query );

    if (!$stmt->execute())
    {
        $arr = $stmt->errorInfo();
        error_log($arr[2]);
    }
}



echo json_encode($merged_results, JSON_UNESCAPED_SLASHES);
