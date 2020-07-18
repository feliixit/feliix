<?php
error_reporting(E_ERROR | E_PARSE);

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
use \Firebase\JWT\JWT;

$leave_id = 0;
$uid = 0;
$action = '';

try 
{
    $p = (isset($_GET['p']) ?  $_GET['p'] : "");
    $url = passport_decrypt(base64url_decode($p));
    $remove_http = str_replace('http://', '', $url);

    $split_parameters = explode('&', $remove_http);

    for($i = 0; $i < count($split_parameters); $i++) {
        $final_split = explode('=', $split_parameters[$i]);
        if($final_split[0] == 'uid')
            $uid = $final_split[1];
        if($final_split[0] == 'leave_id')
            $leave_id = $final_split[1];
        if($final_split[0] == 'action')
            $action = $final_split[1];
    }
} 
    catch (Exception $e){

    http_response_code(401);

    echo json_encode(array("message" => "Access denied."));
    die();
}

if($uid == 0 || $leave_id == 0 || $action == '')
    die();

include_once 'config/database.php';
$database = new Database();
$db = $database->getConnection();

$user_id = $uid;

$merged_results = [];

$id = $leave_id;

if($action == 'approve') {
    $query = "
        update apply_for_leave a LEFT JOIN user u ON a.uid = u.id 
        set approval_id = " . $user_id . ", approval_at = NOW()
        WHERE a.STATUS <> -1 AND approval_id = 0 AND reject_id = 0 AND re_approval_id = 0 AND re_reject_id = 0 
        and uid IN 
        ( SELECT id FROM user WHERE apartment_id IN (SELECT apartment_id FROM leave_flow WHERE uid = " . $user_id . " and flow = 1))
        AND a.id in(" . $id . ")
    ";

    $stmt = $db->prepare($query);

    if (!$stmt->execute()) {
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

    $stmt = $db->prepare($query);

    if (!$stmt->execute()) {
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

    $stmt = $db->prepare($query);

    if (!$stmt->execute()) {
        $arr = $stmt->errorInfo();
        error_log($arr[2]);
    }


    $subquery = "SELECT GROUP_CONCAT(uid) uid FROM leave_flow WHERE apartment_id IN (SELECT apartment_id FROM leave_flow WHERE uid = " . $user_id . " and flow = 2) AND uid <> " . $user_id . " ";
    //$subquery = " SELECT user.id, username, duty_date, duty_time, location FROM user LEFT JOIN on_duty ON user.id = on_duty.uid WHERE duty_date = '2020/05/11' AND on_duty.duty_type = 'A' and on_duty.uid = 1 ORDER BY on_duty.created_at ";

    $stmt1 = $db->prepare($subquery);
    $stmt1->execute();

    $row_id = "";

    while ($row1 = $stmt1->fetch(PDO::FETCH_ASSOC)) {
        $row_id = $row1['uid'];
    }

    if ($row_id != "") {
        $query = "
            update apply_for_leave a LEFT JOIN user u ON a.uid = u.id 
            set approval_id = " . $user_id . ", approval_at = NOW(),
            re_approval_id = " . $user_id . ", re_approval_at = NOW()
            WHERE a.STATUS <> -1 AND approval_id = 0 AND reject_id = 0 AND re_approval_id = 0 AND re_reject_id = 0 
            and uid IN 
            (" . $row_id . ")
            AND a.id in(" . $id . ")
        ";

        $stmt = $db->prepare($query);

        if (!$stmt->execute()) {
            $arr = $stmt->errorInfo();
            error_log($arr[2]);
        }
    }
}

if($action == 'reject')
{

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

}


?>
