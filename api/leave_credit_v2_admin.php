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

$sdate1 = (isset($_GET['sdate1']) ?  $_GET['sdate1'] : '');
$edate1 = (isset($_GET['edate1']) ?  $_GET['edate1'] : '');

$sdate2 = (isset($_GET['sdate2']) ?  $_GET['sdate2'] : '');
$edate2 = (isset($_GET['edate2']) ?  $_GET['edate2'] : '');

$uid = (isset($_GET['uid']) ? $_GET['uid'] : 0);
$user_id = $uid;

$merged_results = array();

if($sdate1 == '' && $sdate2 == '')
{
    $merged_results[] = array( 
        "sil_credit" => 0,
        "sil_taken" => 0,
        "sil_approval" => 0,
        
        "sl_credit" => 0,
        "sl_taken" => 0,
        "sl_approval" => 0,

        "pl_taken" => 0,
        "pl_approval" => 0,

        "ab_taken" => 0,
        "ab_approval" => 0,

        "halfday_credit" => 0,
        "halfday_taken" => 0,
        "halfday_approval" => 0,
    );

    echo json_encode($merged_results, JSON_UNESCAPED_SLASHES); 
    die();
}

// leave credit!

$sil_credit = 0;
$vl_sl_credit = 0;
$vl_credit = 0;
$sl_credit = 0;
$halfday_credit = 0;

$leave_level = '';

$query = "SELECT leave_level, sil, vl_sl, vl, sl, halfday from user where id = " . $user_id ;

$stmt = $db->prepare( $query );
$stmt->execute();

while($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
    $sil_credit = $row['sil'];
    $vl_sl_credit = $row['vl_sl'];
    $vl_credit = $row['vl'];
    $sl_credit = $row['sl'];

    $leave_level = $row['leave_level'];
    $halfday_credit = $row['halfday'];
}


/* fetch data */
if($edate2 != "")
    $query = "SELECT SUM(`sil`) sil, sum(`vl_sl`) vl_sl, sum(`vl`) vl, sum(`sl`) sl, sum(`ul`) ul, sum(`halfday`) halfday, CASE  WHEN re_approval_id > 0 THEN 'A'  WHEN re_approval_id = 0 THEN 'P' END approval FROM apply_for_leave WHERE start_date >= '" . $sdate1 . "' AND start_date <= '" . $edate2 . "' and status in (0, 1) and uid = " . $user_id . " group by  CASE WHEN re_approval_id > 0 THEN 'A'  WHEN re_approval_id = 0 THEN 'P' END";
else
    $query = "SELECT SUM(`sil`) sil, sum(`vl_sl`) vl_sl, sum(`vl`) vl, sum(`sl`) sl, sum(`ul`) ul, sum(`halfday`) halfday, CASE  WHEN re_approval_id > 0 THEN 'A'  WHEN re_approval_id = 0 THEN 'P' END approval FROM apply_for_leave WHERE and start_date >= '" . $sdate1 . "' AND start_date <= '" . $edate1 . "' and status in (0, 1) and uid = " . $user_id . " group by  CASE WHEN re_approval_id > 0 THEN 'A'  WHEN re_approval_id = 0 THEN 'P' END";

$stmt = $db->prepare( $query );
$stmt->execute();



$sil_taken = 0;
$sil_approval = 0;

$vl_sl_taken = 0;
$vl_sl_approval = 0;

$vl_taken = 0;
$vl_approval = 0;

$sl_taken = 0;
$sl_approval = 0;

$ul_taken = 0;
$ul_approval = 0;

$halfday_taken = 0;
$halfday_approval = 0;

while($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
    $sil = $row['sil'];
    $vl_sl = $row['vl_sl'];
    $vl = $row['vl'];
    $sl = $row['sl'];
    $ul = $row['ul'];
    $halfday = $row['halfday'];

    $approval = $row['approval'];
  

    if($approval == 'A')
    {
        

        $sil_taken += $sil;
        $vl_sl_taken += $vl_sl;
        $vl_taken += $vl;
        $sl_taken += $sl;
        $ul_taken += $ul;
        $halfday_taken += $halfday;
    }
    else
    {
        $sil_approval += $sil;
        $vl_sl_approval += $vl_sl;
        $vl_approval += $vl;
        $sl_approval += $sl;
        $ul_approval += $ul;
        $halfday_approval += $halfday;

    }
}

if($sil_credit < 0 || $vl_sl_credit < 0 || $vl_credit < 0 || $sl_credit < 0)
{
    $merged_results[] = array(
        "sil_credit" => $sil_credit,
        "sil_taken" => $sil_taken,
        "sil_approval" => $sil_approval,
    
        "vl_sl_credit" => $vl_sl_credit,
        "vl_sl_taken" => $vl_sl_taken,
        "vl_sl_approval" => $vl_sl_approval,
    
        "vl_credit" => $vl_credit,
        "vl_taken" => $vl_taken,
        "vl_approval" => $vl_approval,
    
        "sl_credit" => $sl_credit,
        "sl_taken" => $sl_taken,
        "sl_approval" => $sl_approval,
    
        "ul_taken" => $ul_taken,
        "ul_approval" => $ul_approval,

        "halfday_credit" => $halfday_credit,
        "halfday_taken" => $halfday_taken,
        "halfday_approval" => $halfday_approval,
    
        "leave_level" => $leave_level,
    
        "message" => 'Leave credit is not enough.',
    );

    echo json_encode($merged_results, JSON_UNESCAPED_SLASHES);
    
    die();
}

$merged_results[] = array(
    "sil_credit" => $sil_credit,
    "sil_taken" => $sil_taken,
    "sil_approval" => $sil_approval,

    "vl_sl_credit" => $vl_sl_credit,
    "vl_sl_taken" => $vl_sl_taken,
    "vl_sl_approval" => $vl_sl_approval,

    "vl_credit" => $vl_credit,
    "vl_taken" => $vl_taken,
    "vl_approval" => $vl_approval,

    "sl_credit" => $sl_credit,
    "sl_taken" => $sl_taken,
    "sl_approval" => $sl_approval,

    "ul_taken" => $ul_taken,
    "ul_approval" => $ul_approval,

    "halfday_credit" => $halfday_credit,
    "halfday_taken" => $halfday_taken,
    "halfday_approval" => $halfday_approval,

    "leave_level" => $leave_level,

    "message" => '',
);

echo json_encode($merged_results, JSON_UNESCAPED_SLASHES);
