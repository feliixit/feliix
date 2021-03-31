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
$id = (isset($_GET['id']) ?  $_GET['id'] : '');
$fc = (isset($_GET['fc']) ?  $_GET['fc'] : '');
$fs = (isset($_GET['fs']) ?  $_GET['fs'] : '');
$ft = (isset($_GET['ft']) ?  $_GET['ft'] : '');
$fal = (isset($_GET['fal']) ?  $_GET['fal'] : '');
$fau = (isset($_GET['fau']) ?  $_GET['fau'] : '');
$fpl = (isset($_GET['fpl']) ?  $_GET['fpl'] : '');
$fpu = (isset($_GET['fpu']) ?  $_GET['fpu'] : '');
$fk = (isset($_GET['fk']) ?  $_GET['fk'] : '');

$of1 = (isset($_GET['of1']) ?  $_GET['of1'] : '');
$ofd1 = (isset($_GET['ofd1']) ?  $_GET['ofd1'] : '');
$of2 = (isset($_GET['of2']) ?  $_GET['of2'] : '');
$ofd2 = (isset($_GET['ofd2']) ?  $_GET['ofd2'] : '');

$page = (isset($_GET['page']) ?  $_GET['page'] : "");
$size = (isset($_GET['size']) ?  $_GET['size'] : "");

$merged_results = array();

$query = "SELECT pm.id, COALESCE(pc.category, '') category, pct.client_type, pct.class_name pct_class, pp.priority, pp.class_name pp_class, pm.project_name, pm.final_amount, COALESCE(ps.project_status, '') project_status, COALESCE((SELECT project_est_prob.prob FROM project_est_prob WHERE project_est_prob.project_id = pm.id order by created_at desc limit 1), pm.estimate_close_prob) estimate_close_prob, user.username, DATE_FORMAT(pm.created_at, '%Y-%m-%d') created_at, COALESCE((SELECT project_stage.stage FROM project_stages LEFT JOIN project_stage ON project_stage.id = project_stages.stage_id WHERE project_stages.project_id = pm.id and project_stages.stages_status_id = 1 ORDER BY `sequence` desc LIMIT 1), '') stage FROM project_main pm LEFT JOIN project_category pc ON pm.catagory_id = pc.id LEFT JOIN project_client_type pct ON pm.client_type_id = pct.id LEFT JOIN project_priority pp ON pm.priority_id = pp.id LEFT JOIN project_status ps ON pm.project_status_id = ps.id LEFT JOIN project_stage pst ON pm.stage_id = pst.id LEFT JOIN user ON pm.create_id = user.id where 1= 1 ";

if($fc != "" && $fc != "0")
{
    $query = $query . " and pm.catagory_id = " . $fc . " ";
}

if($fs != "" && $fs != "0")
{
    $query = $query . " and pm.project_status_id = '" . $fs . "' ";
}

if($ft != "" && $ft != "0")
{
    $query = $query . " and user.username = '" . $ft . "' ";
}


$query = $query . " order by pm.created_at desc ";

if(!empty($_GET['page'])) {
    $page = filter_input(INPUT_GET, 'page', FILTER_VALIDATE_INT);
    if(false === $page) {
        $page = 1;
    }
}

if(!empty($_GET['size'])) {
    $size = filter_input(INPUT_GET, 'size', FILTER_VALIDATE_INT);
    if(false === $size) {
        $size = 10;
    }

    $offset = ($page - 1) * $size;

    $query = $query . " LIMIT " . $offset . "," . $size;
}

$stmt = $db->prepare( $query );
$stmt->execute();

while($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
    $id = $row['id'];
    $category = $row['category'];
    $client_type = $row['client_type'];
    $pct_class = $row['pct_class'];
    $priority = $row['priority'];

    $pp_class = $row['pp_class'];
    $project_name = $row['project_name'];
    $project_status = $row['project_status'];
    $estimate_close_prob = $row['estimate_close_prob'];
    $username = $row['username'];

    $final_amount = $row['final_amount'];

    $payment_amount = GetPaymentAmount($row['id'], $db);
    $down_payment_amount = GetDownPaymentAmount($row['id'], $db);

    $ar = null;
    if($final_amount != null)
    {
        $pay = 0;
        if($payment_amount != null)
            $pay = $payment_amount;
        $down_pay = 0;
        if($down_payment_amount != null)
            $down_pay = $down_payment_amount;

        $ar = $final_amount - $pay - $down_pay;
    }

    $created_at = $row['created_at'];
    $stage = $row['stage'];
    $quote = GetQuote($row['id'], $db);
    $payment = GetPayment($row['id'], $db);
    $final_quotation = GetFinalQuote($row['id'], $db);

    $merged_results[] = array(
        "id" => $id,
        "category" => $category,
        "client_type" => $client_type,
        "pct_class" => $pct_class,
        "priority" => $priority,
        "pp_class" => $pp_class,
        "project_name" => $project_name,
        "project_status" => $project_status,
        "payment_amount" => $payment_amount,
        "down_payment_amount" => $down_payment_amount,
        "ar" => $ar,
        "final_quotation" => $final_quotation,
        "username" => $username,
        "created_at" => $created_at,
        "stage" => $stage,
        "final_amount" => $final_amount,
        "quote" => $quote,
        "payment" => $payment,
    );
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

function GetQuote($project_id, $db){
    $query = "
        SELECT pm.id,
            pm.remark comment,
            u.username,
            pm.created_at,
            final_quotation
        FROM   project_quotation pm
            LEFT JOIN user u
                    ON u.id = pm.create_id
        WHERE  project_id = " . $project_id . "
            AND pm.status <> -1 
    ";

    // prepare the query
    $stmt = $db->prepare($query);
    $stmt->execute();

    $merged_results = [];

    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        $id = $row['id'];
        $comment = $row['comment'];
        $username = $row['username'];
        $created_at = $row['created_at'];
        $final_quotation = $row['final_quotation'];

        $items = GetItem($row['id'], $db, 'quote');
       
        $merged_results[] = array(
            "id" => $id,
            "comment" => $comment,
            "username" => $username,
            "created_at" => $created_at,
            "final_quotation" => $final_quotation,
            "items" => $items,
        );
    }

    return $merged_results;
}

function GetPaymentAmount($project_id, $db){
    $amount = null;
    $query = "
        SELECT 
            pm.amount
        FROM   project_proof pm
        WHERE  project_id = " . $project_id . "
            AND pm.status <> -1 
            AND pm.kind = 1
    ";

    // prepare the query
    $stmt = $db->prepare($query);
    $stmt->execute();

    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        if($row['amount'] != null)
            $amount += $row['amount'];
    }

    return $amount;
}

function GetDownPaymentAmount($project_id, $db){
    $amount = null;
    $query = "
        SELECT 
            pm.amount
        FROM   project_proof pm
        WHERE  project_id = " . $project_id . "
            AND pm.status <> -1 
            AND pm.kind = 0
    ";

    // prepare the query
    $stmt = $db->prepare($query);
    $stmt->execute();

    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        if($row['amount'] != null)
            $amount += $row['amount'];
    }

    return $amount;
}

function GetPayment($project_id, $db){
    $query = "
        SELECT pm.id,
            pm.remark,
            u.username,
            pm.created_at,
            pm.received_date,
            pm.kind,
            pm.amount,
            pm.invoice,
            pm.detail,
            pm.checked,
            pm.checked_id,
            pm.checked_at
        FROM   project_proof pm
            LEFT JOIN user u
                ON u.id = pm.create_id
        WHERE  project_id = " . $project_id . "
            AND pm.status <> -1 
    ";

    // prepare the query
    $stmt = $db->prepare($query);
    $stmt->execute();

    $merged_results = [];

    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        $id = $row['id'];
        $remark = $row['remark'];
        $username = $row['username'];
        $created_at = $row['created_at'];
        $received_date = $row['received_date'];
        $kind = $row['kind'];
        $amount = $row['amount'];
        $invoice = $row['invoice'];
        $detail = $row['detail'];
        $checked = $row['checked'];
        $checked_id = $row['checked_id'];
        $checked_at = $row['checked_at'];

        $items = GetItem($row['id'], $db, 'proof');
       
        $merged_results[] = array(
            "id" => $id,
            "remark" => $remark,
            "username" => $username,
            "created_at" => $created_at,
            "received_date" => $received_date,
            "kind" => $kind,
            "amount" => $amount,
            "invoice" => $invoice,
            "detail" => $detail,
            "checked" => $checked,
            "checked_id" => $checked_id,
            "checked_at" => $checked_at,
            "items" => $items,
        );
    }

    return $merged_results;
}


function GetItem($batch_id, $db, $type){
    $query = "
        
        SELECT f.id,
            coalesce(f.filename, '')   filename,
            coalesce(f.bucketname, '') bucket,
            coalesce(f.gcp_name, '')   gcp_name,
            u.username,
            f.created_at
        FROM   gcp_storage_file f

            LEFT JOIN user u
                ON u.id = f.create_id
        WHERE batch_id = " . $batch_id . "
        AND f.batch_type = '" . $type . "'
            AND f.status <> -1 
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