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

$uid = (isset($_GET['uid']) ?  $_GET['uid'] : '');
$sdate1 = (isset($_GET['sdate1']) ?  $_GET['sdate1'] : '');
$edate1 = (isset($_GET['edate1']) ?  $_GET['edate1'] : '');

$type = (isset($_GET['type']) ?  $_GET['type'] : '');

$merged_results = array();

if($sdate1 == '')
{
    echo json_encode($merged_results, JSON_UNESCAPED_SLASHES); 
    die();
}

if($type == 'A')
    $query = "SELECT 0 is_checked, id, `leave` le, leave_type, start_date, start_time, end_date, end_time, CASE  WHEN reject_id + re_reject_id > 0 THEN 'R' WHEN approval_id * re_approval_id > 0 THEN 'A'  WHEN approval_id * re_approval_id = 0 THEN 'P' END approval, reason, pic_url, created_at FROM apply_for_leave WHERE start_date >= '" . $sdate1 . "' and start_date < '" . $edate1 . "' and status <> -1 and uid = " . $user_id ;
else {
    # code...
    $query = "SELECT 0 is_checked, id, `leave` le, leave_type, start_date, start_time, end_date, end_time, CASE  WHEN reject_id + re_reject_id > 0 THEN 'R' WHEN approval_id * re_approval_id > 0 THEN 'A' WHEN approval_id * re_approval_id > 0 THEN 'A'  WHEN approval_id * re_approval_id = 0 THEN 'P' END approval, reason, pic_url, created_at FROM apply_for_leave WHERE start_date >= '" . $sdate1 . "' and start_date < '" . $edate1 . "' and status <> -1 and uid = " . $user_id . " and approval_id * re_approval_id = 0 and reject_id + re_reject_id = 0 ";
}

$stmt = $db->prepare( $query );
$stmt->execute();



while($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
    $merged_results[] = $row;
}

echo json_encode($merged_results, JSON_UNESCAPED_SLASHES);
