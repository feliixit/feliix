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


include_once 'mail.php';

include_once 'config/database.php';
$database = new Database();
$db = $database->getConnection();

$merged_results = [];

$id = (isset($_POST['id']) ?  $_POST['id'] : 0);

$query = "
    update apply_for_leave a LEFT JOIN user u ON a.uid = u.id 
    set approval_id = " . $user_id . ", approval_at = NOW()
    WHERE a.STATUS <> -1 AND approval_id = 0 AND reject_id = 0 AND re_approval_id = 0 AND re_reject_id = 0 
    and uid IN 
    ( SELECT id FROM user WHERE apartment_id IN (SELECT apartment_id FROM leave_flow WHERE uid = " . $user_id . " and flow = 1))
    AND a.id in(" . $id . ")
";

$stmt = $db->prepare( $query );

if (!$stmt->execute())
{
    $arr = $stmt->errorInfo();
    error_log($arr[2]);
}

$query = "
    update apply_for_leave a LEFT JOIN user u ON a.uid = u.id 
    set re_approval_id = " . $user_id . ", re_approval_at = NOW()
    WHERE a.STATUS <> -1 AND approval_id  = " . $user_id . " AND reject_id = 0 AND re_approval_id = 0 AND re_reject_id = 0 
    and `leave` <= 2 
    and uid IN 
    ( SELECT id FROM user WHERE apartment_id IN (SELECT apartment_id FROM leave_flow WHERE uid = " . $user_id . " and flow = 1))
    AND a.id in (" . $id . ")
";

$stmt = $db->prepare( $query );

if (!$stmt->execute())
{
    $arr = $stmt->errorInfo();
    error_log($arr[2]);
}


$query = "
    update apply_for_leave a LEFT JOIN user u ON a.uid = u.id 
    set re_approval_id = " . $user_id . ", re_approval_at = NOW()
    WHERE a.STATUS <> -1 AND approval_id <> 0 AND reject_id = 0 AND re_approval_id = 0 AND re_reject_id = 0 
    and uid IN 
    ( SELECT id FROM user WHERE apartment_id IN (SELECT apartment_id FROM leave_flow WHERE uid = " . $user_id . " and flow = 2))
    AND a.id in (" . $id . ")
";

$stmt = $db->prepare( $query );

if (!$stmt->execute())
{
    $arr = $stmt->errorInfo();
    error_log($arr[2]);
}


// 20250109 half day leave approve by kuan
if($user_id == 3){
    $query = "
        update apply_for_leave a LEFT JOIN user u ON a.uid = u.id 
        set approval_id = " . $user_id . ", approval_at = NOW(), re_approval_id = " . $user_id . ", re_approval_at = NOW()
        WHERE a.STATUS <> -1 AND leave_type = 'H'
        AND a.id in (" . $id . ")
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

if($row_id != "" )
{
    $query = "
        update apply_for_leave a LEFT JOIN user u ON a.uid = u.id 
        set approval_id = " . $user_id . ", approval_at = NOW(),
        re_approval_id = " . $user_id . ", re_approval_at = NOW()
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

// 20250506 bose approval (auto approved)
$query = "SELECT uid FROM leave_flow WHERE apartment_id IN (SELECT apartment_id from user WHERE id = (SELECT uid FROM apply_for_leave WHERE id = " . $id . ")) AND flow = 2 ";
$stmt = $db->prepare( $query );
$stmt->execute();

$is_boss_flow_2 = 0;
while($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
    if($row['uid'] == 3)
    {
        $is_boss_flow_2 = 1;
    }
}
if($is_boss_flow_2 == 1 )
{
    $query = "
        update apply_for_leave a LEFT JOIN user u ON a.uid = u.id 
        set
        re_approval_id = 3, re_approval_at = NOW()
        WHERE a.STATUS <> -1 AND re_approval_id = 0 AND re_reject_id = 0 
        AND a.id in(" . $id . ")
    ";

    $stmt = $db->prepare( $query );

    if (!$stmt->execute())
    {
        $arr = $stmt->errorInfo();
        error_log($arr[2]);
    }
}


// send mail
$mail_name = '';
$mail_email = '';
$mail_uid = 0;

$query = "SELECT u.id, u.username, u.email FROM leave_flow l left join user u ON l.uid = u.id WHERE l.apartment_id IN (SELECT apartment_id FROM leave_flow WHERE uid = " . $user_id . ") AND flow = 2 AND l.uid <> " . $user_id . "";

$stmt = $db->prepare( $query );
$stmt->execute();
while($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
    $mail_name = $row['username'];
    $mail_email = $row['email'];
    $mail_uid = $row['id'];
}

$need_mail = 0;

$leav_msg = "";
$username = "";
$leaver = "";
$department = "";
$app_time = "";
$leave_type = "";
$start_time = "";
$end_time = "";
$leave_length = 0;
$reason = "";
$imgurl = "";

$query = "
    SELECT u.username, d.department, a.created_at, a.leave_type, a.`leave`, a.start_date, a.start_time, a.end_date, a.end_time, a.reason, a.pic_url  from `user` u LEFT JOIN `apply_for_leave` a ON u.id = a.uid LEFT JOIN user_department d ON d.id = u.apartment_id 
    WHERE a.STATUS <> -1 AND a.approval_id  = " . $user_id . " AND a.reject_id = 0 AND a.re_approval_id = 0 AND a.re_reject_id = 0 
    AND a.id in (" . $id . ")
";

$stmt = $db->prepare( $query );
$stmt->execute();

while($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
    $need_mail = 1;
    $leaver = $row['username'];
    $department = $row['department'];
    $app_time = $row['created_at'];
    $leave_type = getLeaveType($row['leave_type']);
    $start_time = formateDate($row['start_date']) . " " . $row['start_time'];
    $end_time = formateDate($row['end_date']) . " " . $row['end_time'];
    $leave_length = $row['leave'];
    $reason = $row['reason'];
    $imgurl = $row['pic_url'];

    $leav_msg =  $username . " apply leave from " . $start_date . " " . $start_time . " to " . $end_date . " " . $end_time;

    sendMail($mail_name, $mail_email, '', '', '', $leaver, $department, $app_time, $leave_type, $start_time, $end_time, $leave_length, $reason, $imgurl);
}

/*

if($need_mail == 1 && $mail_uid <> 0)
{
    $date = new DateTime();

    $par_approve = "leave_id=". $id . "&uid=" . $mail_uid. "&action=approve&time=" . $date->getTimestamp();
    $par_reject = "leave_id=". $id . "&uid=" . $mail_uid. "&action=reject&time" . $date->getTimestamp();

    $conf = new Conf();

    $appove_hash = $conf::$mail_ip . "api/leave_record_approval_hash?p=" . base64url_encode(passport_encrypt($par_approve));
    $reject_hash = $conf::$mail_ip . "api/leave_record_approval_hash?p=" . base64url_encode(passport_encrypt($par_reject));

    sendMail($mail_name, $mail_email, $appove_hash, $reject_hash, $leav_msg, $leaver, $department, $app_time, $leave_type, $start_time, $end_time, $leave_length, $reason, $imgurl);
}
*/

echo json_encode($merged_results, JSON_UNESCAPED_SLASHES);



function getLeaveType($type){
    $leave_type = '';

    if($type =="A")
        $leave_type = "Service Incentive Leave";
    if($type =="N")
        $leave_type = "Vaction Leave";
    if($type =="B" || $type =="S")
        $leave_type = "Sick Leave";
    if($type =="C" || $type =="U")
        $leave_type = "Unpaid Leave";
    if($type =="D")
        $leave_type = "Absence";
    if($type =="H")
        $leave_type = "Manager Halfday Planning";
    
    return $leave_type;
}

function formateDate($_date){
    return substr($_date, 0, 4)."/".substr($_date, 4, 2)."/".substr($_date, 6, 2);
}

