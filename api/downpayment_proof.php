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

$page = (isset($_GET['page']) ?  $_GET['page'] : "");
$size = (isset($_GET['size']) ?  $_GET['size'] : "");

$pid = (isset($_GET['pid']) ?  $_GET['pid'] : 0);
$fk = (isset($_GET['fk']) ?  $_GET['fk'] : '');

$merged_results = array();

$query = "SELECT pm.id pid,
            pp.id,
            pp.batch_id,
            pm.project_name,
            Coalesce(pp.status, 0)                          status,
            Coalesce(f.filename, '')                        filename,
            pp.remark,
            Coalesce(f.gcp_name, '')                        gcp_name,
            user.username,
            user.id                                         uid,
            Date_format(pp.created_at, '%Y-%m-%d %H:%i:%s') created_at,
            pp.proof_remark,
            pp.received_date,
            pp.kind,
            pp.amount,
            pp.invoice,
            pp.detail,
            pp.checked,
            pp.checked_id,
            pp.checked_at,
            pm.final_amount
          FROM   project_proof pp
          LEFT JOIN project_main pm
                ON pp.project_id = pm.id
          LEFT JOIN user
                ON pp.create_id = user.id
          LEFT JOIN gcp_storage_file f
                ON f.batch_id = pp.id
          AND f.batch_type = 'proof'
          WHERE  pp.`status` <> -2  ";

if($fk != "") {
    $query .= " AND (user.username like '%" . $fk . "%' or  pm.project_name like '%" . $fk . "%') ";
}

if(!empty($_GET['page'])) {
    $page = filter_input(INPUT_GET, 'page', FILTER_VALIDATE_INT);
    if(false === $page) {
        $page = 1;
    }
}

$query = $query . " order by pp.created_at desc, status ";

if(!empty($_GET['size'])) {
    $size = filter_input(INPUT_GET, 'size', FILTER_VALIDATE_INT);
    if(false === $size) {
        $size = 5;
    }

    $offset = ($page - 1) * $size;

    $query = $query . " LIMIT " . $offset . "," . $size;
}


$stmt = $db->prepare( $query );
$stmt->execute();

$is_checked = 0;
$sid = 0;
$id = 0;
$batch_id = 0;
$project_name = "";
$filename = "";
$gcp_name = "";
$remark = "";
$status = 0;
$username = "";
$created_at = "";
$proof_remark = "";
$proof_remark   = "";
$received_date = "";
$kind = "";
$amount = "";
$invoice = "";
$detail = "";
$checked = "";
$checked_at = "";
$final_amount = "";
$pid = 0;

$items = [];

while($row = $stmt->fetch(PDO::FETCH_ASSOC)) {

    if(($id . $batch_id != $row['id'] . $row['batch_id']) && $id != 0)
    {
        $sid = $sid + 1;

        $merged_results[] = array( 
                                "is_checked" => 0,
                                "pid" => $pid,
                                "id" => $id,
                                "sid" => $sid,
                                "project_name" => $project_name,
                                "status" => $status,
                                "remark" => $remark,
                                "items" => $items,
                                "username" => $username,
                                "created_at" => $created_at,
                                "proof_remark" => $proof_remark,
                                "received_date" => $received_date,
                                "kind" => $kind,
                                "amount" => $amount,
                                "invoice" => $invoice,
                                "detail" => $detail,
                                "checked" => $checked,
                                "checked_id" => $checked_id,
                                "created_at" => $created_at,
                                "final_quotation" => $final_quotation,
                                "final_amount" => $final_amount,
        );

        $items = [];

    }

    $id = $row['id'];
    $pid = $row['pid'];
    $created_at = $row['created_at'];
    $batch_id = $row['batch_id'];
    $username = $row['username'];
    $gcp_name = $row['gcp_name'];
    $filename = $row['filename'];
    $remark = $row['remark'];
    $project_name = $row['project_name'];
    $status = $row['status'];
    $proof_remark = $row['proof_remark'];

    $received_date = $row['received_date'];
    $kind = $row['kind'];
    $amount = $row['amount'];
    $invoice = $row['invoice'];
    $detail = $row['detail'];
    $checked = $row['checked'];
    $checked_id = $row['checked_id'];
    $checked_at = $row['checked_at'];
    $final_amount = $row['final_amount'];

    $final_quotation = GetFinalQuote($row['pid'], $db);

    if($filename != "")
      $items[] = array('filename' => $filename,
                     'gcp_name' => $gcp_name );
}

if($id != 0)
{
    $sid = $sid + 1;

    $merged_results[] = array( "is_checked" => 0,
                                "id" => $id,
                                "pid" => $pid,
                                "sid" => $sid,
                                "project_name" => $project_name,
                                "status" => $status,
                                "remark" => $remark,
                                "items" => $items,
                                "username" => $username,
                                "created_at" => $created_at,
                                "proof_remark" => $proof_remark,
                                "received_date" => $received_date,
                                "kind" => $kind,
                                "amount" => $amount,
                                "invoice" => $invoice,
                                "detail" => $detail,
                                "checked" => $checked,
                                "checked_id" => $checked_id,
                                "created_at" => $created_at,
                                "final_quotation" => $final_quotation,
                                "final_amount" => $final_amount,
            );
}


while($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
    $merged_results[] = $row;
}

echo json_encode($merged_results, JSON_UNESCAPED_SLASHES);


function GetFinalQuote($project_id, $db){
    $query = "
        SELECT 
            COALESCE(f.filename, '') filename, 
            COALESCE(f.bucketname, '') bucket, 
            COALESCE(f.gcp_name, '') gcp_name
        FROM   project_quotation pm
           
        LEFT JOIN gcp_storage_file f 
            ON f.batch_id = pm.id AND f.batch_type = 'quote' 
        WHERE  project_id = " . $project_id . "
            AND pm.status <> -1 
            AND pm.final_quotation = 1
    ";

    // prepare the query
    $stmt = $db->prepare($query);
    $stmt->execute();

    $merged_results = [];

    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        $merged_results[] = $row;
    }

    return $merged_results;
}