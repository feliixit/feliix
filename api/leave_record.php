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

if($type == 'A' || $type == 'N')
    $query = "SELECT 0 is_checked, id, `leave` le, leave_type, start_date, start_time, end_date, end_time, CASE when STATUS = -3 then 'V'  when STATUS = -1 then 'W' when leave_type = 'D' then 'D' WHEN reject_id + re_reject_id > 0 THEN 'R' WHEN approval_id * re_approval_id > 0 THEN 'A'  WHEN approval_id * re_approval_id = 0 THEN 'P' END approval, reason, pic_url, created_at, leave_level, sil, vl_sl, vl, sl, halfday FROM apply_for_leave WHERE start_date >= '" . $sdate1 . "' and start_date < '" . $edate1 . "' and   uid = " . $user_id ;
else {
    # code...
    $query = "SELECT 0 is_checked, id, `leave` le, leave_type, start_date, start_time, end_date, end_time, CASE when STATUS = -1 then 'W' when leave_type = 'D' then 'D' WHEN reject_id + re_reject_id > 0 THEN 'R' WHEN approval_id * re_approval_id > 0 THEN 'A' WHEN approval_id * re_approval_id > 0 THEN 'A'  WHEN approval_id * re_approval_id = 0 THEN 'P' END approval, reason, pic_url, created_at, leave_level, sil, vl_sl, vl, sl, halfday  FROM apply_for_leave WHERE start_date >= '" . $sdate1 . "' and start_date < '" . $edate1 . "' and status in (0, 1) and uid = " . $user_id . " and approval_id * re_approval_id = 0 and reject_id + re_reject_id = 0 ";
}

$stmt = $db->prepare( $query );
$stmt->execute();



while($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
    $message = GetLeaveMessage($row['le'], $row['leave_type'],  $row['sil'], $row['vl_sl'], $row['vl'], $row['sl'], $row['halfday']);

    $merged_results[] = array(
        "is_checked" => $row['is_checked'],
        "id" => $row['id'],
        "le" => $row['le'],
        "leave_type" => $row['leave_type'],
        "start_date" => $row['start_date'],
        "start_time" => $row['start_time'],
        "end_date" => $row['end_date'],
        "end_time" => $row['end_time'],
        "approval" => $row['approval'],
        "reason" => $row['reason'],
        "pic_url" => $row['pic_url'],
        "created_at" => $row['created_at'],
        "leave_level" => $row['leave_level'],
        "sil" => $row['sil'],
        "vl_sl" => $row['vl_sl'],
        "vl" => $row['vl'],
        "sl" => $row['sl'],
        "halfday" => $row['halfday'],
        "message" => $message

    );
}

echo json_encode($merged_results, JSON_UNESCAPED_SLASHES);


function GetLeaveMessage($leave, $leave_type, $sil, $vl_sl, $vl, $sl, $halfday)
{

    $message = "Consume" . "\r\n";
    if($sil > 0)
        $message .=  "Service Incentive Leave: " . $sil . " day(s)"  . "\r\n";

    if($vl_sl > 0)
        $message .=  "Vacation Leave/Sick Leave: " . $vl_sl . " day(s)"  . "\r\n";

    if($vl > 0)
        $message .=  "Vacation Leave: " . $vl . " day(s)"  . "\r\n";

    if($sl > 0)
        $message .=  "Sick Leave: " . $sl . " day(s)"  . "\r\n";

    if($leave_type == 'U')
        $message .=  "Unpaid Leave: " . $leave . " day(s)"  . "\r\n";

    if($halfday > 0)
        $message .=  "Consume Manager Halfday Planning: 0.5 day(s)"  . "\r\n";

    if($leave_type == 'A' || $leave_type == 'B' || $leave_type == 'C' || $leave_type == 'D')
        $message =  "";

    return $message;

}