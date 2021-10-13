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
$receive_date = (isset($_POST['receive_date']) ?  $_POST['receive_date'] : '');
$amount = (isset($_POST['amount']) ?  $_POST['amount'] : 0);
$invoice = (isset($_POST['invoice']) ?  $_POST['invoice'] : '');
$detail = (isset($_POST['detail']) ?  $_POST['detail'] : '');
$payment_method = (isset($_POST['payment_method']) ?  $_POST['payment_method'] : '');
$bank_name = (isset($_POST['bank_name']) ?  $_POST['bank_name'] : '');
$check_number = (isset($_POST['check_number']) ?  $_POST['check_number'] : '');
$bank_account = (isset($_POST['bank_account']) ?  $_POST['bank_account'] : '');
$remark = (isset($_POST['remark']) ?  $_POST['remark'] : '');

$query = "
    update project_proof a 
    set 
    a.updated_id = :updated_id, 
    a.updated_at = NOW(), 
    a.STATUS = 1, 
    a.received_date = :receive_date, 
    a.amount = :amount, 
    a.invoice = :invoice, 
    a.detail = :detail, 
    a.payment_method = :payment_method, 
    a.bank_name = :bank_name, 
    a.check_number = :check_number, 
    a.bank_account = :bank_account, 
    a.proof_remark = :proof_remark
    WHERE a.STATUS = 0
    AND a.id = :id";


$stmt = $db->prepare( $query );

$stmt->bindParam(':updated_id', $user_id);
$stmt->bindParam(':receive_date', $receive_date);
$stmt->bindParam(':amount', $amount);
$stmt->bindParam(':invoice', $invoice);
$stmt->bindParam(':detail', $detail);
$stmt->bindParam(':payment_method', $payment_method);
$stmt->bindParam(':bank_name', $bank_name);
$stmt->bindParam(':check_number', $check_number);
$stmt->bindParam(':bank_account', $bank_account);
$stmt->bindParam(':proof_remark', $remark);
$stmt->bindParam(':id', $id);

if (!$stmt->execute())
{
    $arr = $stmt->errorInfo();
    error_log($arr[2]);
    http_response_code(501);
    echo json_encode(array("Failure at " . date("Y-m-d") . " " . date("h:i:sa") . " " . $arr[2]));
    die();
}
else
{

    // send mail
    $subquery = "SELECT p.project_name, 
                        pm.remark, 
                        u.username, 
                        u.email, 
                        pm.created_at, 
                        pm.status, 
                        pm.proof_remark, 
                        p.catagory_id, 
                        pm.kind, 
                        pm.amount, 
                        pm.received_date,
                        p.send_mail,
                        pm.payment_method,
                        pm.bank_name,
                        pm.check_number,
                        pm.bank_account,
                        pm.invoice 
                    FROM project_proof pm 
                    left join user u on u.id = pm.create_id 
                    LEFT JOIN project_main p ON p.id = pm.project_id  
                    WHERE pm.id = " . $id . " and pm.status <> -1 ";

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
    $kind = 0;
    $amount = 0.0;
    $receive_date = "";
    $send_mail = "";

    $payment_method = "";
    $bank_name = "";
    $check_number = "";
    $bank_account = "";
    $invoice = "";

    while($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        $project_name = $row['project_name'];
        $remark = $row['remark'];
        $leaver = $row['username'];
        $subtime = $row['created_at'];
        $status = $row['status'];
        $proof_remark = $row['proof_remark'];
        $email1 = $row['email'];
        $category = $row['catagory_id'];
        $kind = $row['kind'];
        $amount = $row['amount'];
        $receive_date = $row['receive_date'];
        $send_mail = $row['send_mail'];

        $payment_method = $row['payment_method'];
        $bank_name = $row['bank_name'];
        $check_number = $row['check_number'];
        $bank_account = $row['bank_account'];

        $invoice = $row['invoice'];
    }

    send_check_notify_mail_new($leaver, 
                                $email1, 
                                $project_name, 
                                $remark, 
                                $subtime, 
                                $proof_remark, 
                                "True", 
                                $category, 
                                $kind, 
                                $amount, 
                                $receive_date, 
                                $send_mail,
                                $payment_method,
                                $bank_name,
                                $check_number,
                                $bank_account,
                                $invoice);

    
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

    http_response_code(200);
    echo json_encode(array("message" => "Success at " . date("Y-m-d") . " " . date("h:i:sa")));


function formateDate($_date){
    return substr($_date, 0, 4)."/".substr($_date, 4, 2)."/".substr($_date, 6, 2);
}

