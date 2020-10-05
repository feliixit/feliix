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
$remark = (isset($_POST['remark']) ?  $_POST['remark'] : '');

$query = "
    update project_proof a 
    set a.updated_id = :updated_id, a.updated_at = NOW(), a.STATUS = 1, a.proof_remark = :proof_remark
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
    $mail_name = '';
    $mail_email = '';
    $mail_uid = 0;


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

        sendGridMail($mail_name, $mail_email, '', '', '', $leaver, $department, $app_time, $leave_type, $start_time, $end_time, $leave_length, $reason, $imgurl);
    }
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
        $leave_type = "Vacation Leave";
    if($type =="B")
        $leave_type = "Emerency/Sick Leave";
    if($type =="C")
        $leave_type = "Unpaid Leave";
    if($type =="D")
        $leave_type = "Absence";
    
    return $leave_type;
}

function formateDate($_date){
    return substr($_date, 0, 4)."/".substr($_date, 4, 2)."/".substr($_date, 6, 2);
}

