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
        $apartment_id = $decoded->data->apartment_id;
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

$merged_results = array();

$subquery = "SELECT GROUP_CONCAT(uid) uid FROM leave_flow WHERE apartment_id IN (SELECT apartment_id FROM leave_flow WHERE uid = " . $user_id . " and flow = 2) AND uid <> " . $user_id . " ";
//$subquery = " SELECT user.id, username, duty_date, duty_time, location FROM user LEFT JOIN on_duty ON user.id = on_duty.uid WHERE duty_date = '2020/05/11' AND on_duty.duty_type = 'A' and on_duty.uid = 1 ORDER BY on_duty.created_at ";

$stmt1 = $db->prepare( $subquery );
$stmt1->execute();

$row_id = "";

while($row1 = $stmt1->fetch(PDO::FETCH_ASSOC)) {
    $row_id = $row1['uid'];
}


$query = "
SELECT * FROM (
    SELECT 0 is_checked, a.id, u.username, a.created_at, `leave` le, leave_type, start_date, start_time, end_date, end_time, 
        CASE when a.STATUS = -3 then 'V' when a.STATUS = -1 then 'W' when leave_type = 'D' then 'D' WHEN reject_id + re_reject_id > 0 THEN 'R' WHEN approval_id * re_approval_id > 0 THEN 'A'  WHEN approval_id * re_approval_id = 0 THEN 'P' END approval, 
        reason, a.pic_url, a.leave_level, a.sil, a.vl_sl, a.vl, a.sl, a.halfday 
        FROM apply_for_leave a LEFT JOIN user u ON a.uid = u.id 
        WHERE a.STATUS not in (-1, -2, -3, 1) AND approval_id = 0 AND reject_id = 0 AND re_approval_id = 0 AND re_reject_id = 0 AND leave_type <> 'H'
        and uid IN 
        ( SELECT id FROM user WHERE apartment_id IN (SELECT apartment_id FROM leave_flow WHERE uid = " . $user_id . " and flow = 1)) 
        and a.uid <> " . $user_id . "


        UNION ALL

        SELECT 0 is_checked, a.id, u.username, a.created_at, `leave` le, leave_type, start_date, start_time, end_date, end_time, 
        CASE when a.STATUS = -3 then 'V' when a.STATUS = -1 then 'W' when leave_type = 'D' then 'D' WHEN reject_id + re_reject_id > 0 THEN 'R' WHEN approval_id * re_approval_id > 0 THEN 'A'  WHEN approval_id * re_approval_id = 0 THEN 'P' END approval, 
        reason, a.pic_url, a.leave_level, a.sil, a.vl_sl, a.vl, a.sl , a.halfday
        FROM apply_for_leave a LEFT JOIN user u ON a.uid = u.id 
        WHERE a.STATUS not in (-1, -2, -3, 1) AND approval_id <> 0 AND reject_id = 0 AND re_approval_id = 0 AND re_reject_id = 0 AND leave_type <> 'H'
        and uid IN 
        ( SELECT id FROM user WHERE apartment_id IN (SELECT apartment_id FROM leave_flow WHERE uid = " . $user_id . " and flow = 2))  
        and a.uid <> " . $user_id . "  ";
        
if($row_id != "")
{
        $query = $query ." UNION ALL

        SELECT 0 is_checked, a.id, u.username, a.created_at, `leave` le, leave_type, start_date, start_time, end_date, end_time, 
        CASE when a.STATUS = -3 then 'V' when a.STATUS = -1 then 'W' when leave_type = 'D' then 'D' WHEN reject_id + re_reject_id > 0 THEN 'R' WHEN approval_id * re_approval_id > 0 THEN 'A'  WHEN approval_id * re_approval_id = 0 THEN 'P' END approval, 
        reason, a.pic_url, a.leave_level, a.sil, a.vl_sl, a.vl, a.sl, a.halfday  
        FROM apply_for_leave a LEFT JOIN user u ON a.uid = u.id 
        WHERE a.STATUS not in (-1, -2, -3, 1)  AND approval_id = 0 AND reject_id = 0 AND re_approval_id = 0 AND re_reject_id = 0 AND leave_type <> 'H'
        and a.uid IN 
        (" . $row_id .")  

        " ;
    }

$query = $query . "
    ) t
    ORDER BY t.created_at desc ";

// 20250109 
if($user_id == 3)
{
    $query = "
SELECT * FROM (
    SELECT 0 is_checked, a.id, u.username, a.created_at, `leave` le, leave_type, start_date, start_time, end_date, end_time, 
        CASE when a.STATUS = -3 then 'V' when a.STATUS = -1 then 'W' when leave_type = 'D' then 'D' WHEN reject_id + re_reject_id > 0 THEN 'R' WHEN approval_id * re_approval_id > 0 THEN 'A'  WHEN approval_id * re_approval_id = 0 THEN 'P' END approval, 
        reason, a.pic_url, a.leave_level, a.sil, a.vl_sl, a.vl, a.sl, a.halfday 
        FROM apply_for_leave a LEFT JOIN user u ON a.uid = u.id 
        WHERE a.STATUS not in (-1, -2, -3, 1) AND approval_id = 0 AND reject_id = 0 AND re_approval_id = 0 AND re_reject_id = 0 AND leave_type = 'H'
        and a.uid <> " . $user_id . "
 ) t
    ORDER BY t.created_at desc ";
}

$stmt = $db->prepare( $query );
$stmt->execute();


while($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
    $message = GetLeaveMessage($row['le'], $row['leave_type'],  $row['sil'], $row['vl_sl'], $row['vl'], $row['sl'], $row['halfday']);

    $merged_results[] = array(
        "is_checked" => $row['is_checked'],
        "id" => $row['id'],
        "username" => $row['username'],
        "created_at" => $row['created_at'],
        "le" => $row['le'],
        "leave_type" => $row['leave_type'],
        "start_date" => $row['start_date'],
        "start_time" => $row['start_time'],
        "end_date" => $row['end_date'],
        "end_time" => $row['end_time'],
        "approval" => $row['approval'],
        "reason" => $row['reason'],
        "pic_url" => $row['pic_url'],
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