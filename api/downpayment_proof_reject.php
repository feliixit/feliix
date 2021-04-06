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
$receive_date = (isset($_POST['receive_date']) ?  $_POST['receive_date'] : '');
$amount = (isset($_POST['amount']) ?  $_POST['amount'] : 0);
$invoice = (isset($_POST['invoice']) ?  $_POST['invoice'] : '');
$detail = (isset($_POST['detail']) ?  $_POST['detail'] : '');
$remark = (isset($_POST['remark']) ?  $_POST['remark'] : '');

$query = "
        UPDATE project_proof a
        SET    a.updated_id = :updated_id,
            a.updated_at = Now(),
            a.status = -1,
            a.received_date = :receive_date, 
            a.amount = :amount, 
            a.invoice = :invoice, 
            a.detail = :detail, 
            a.proof_remark = :proof_remark
        WHERE  a.status = 0
            AND a.id = :id ";

$stmt = $db->prepare( $query );

$stmt->bindParam(':updated_id', $user_id);
$stmt->bindParam(':receive_date', $receive_date);
$stmt->bindParam(':amount', $amount);
$stmt->bindParam(':invoice', $invoice);
$stmt->bindParam(':detail', $detail);
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
    $subquery = "SELECT p.project_name, pm.remark, u.username, u.email, pm.created_at, pm.status, pm.proof_remark, p.catagory_id, pm.kind  FROM project_proof pm left join user u on u.id = pm.create_id LEFT JOIN project_main p ON p.id = pm.project_id  WHERE pm.id = " . $id . "  ";

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
    }


    send_check_notify_mail_new($leaver, $email1, $project_name, $remark, $subtime, $proof_remark, "False", $category, $kind);
}

http_response_code(200);
echo json_encode(array("message" => "Success at " . date("Y-m-d") . " " . date("h:i:sa")));
