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

$is_manager = (isset($_POST['is_manager']) ?  $_POST['is_manager'] : '');

$timeStart = (isset($_POST['timeStart']) ?  $_POST['timeStart'] : '');
$amStart = (isset($_POST['amStart']) ?  $_POST['amStart'] : '');

$timeEnd = (isset($_POST['timeEnd']) ?  $_POST['timeEnd'] : '');
$amEnd = (isset($_POST['amEnd']) ?  $_POST['amEnd'] : '');

$leave_type = (isset($_POST['leave_type']) ?  $_POST['leave_type'] : '');


$leaves = array();
$applied = array();
$holiday = array();

if($timeStart == '' && $timeEnd == '')
{
    http_response_code(401);
    echo json_encode(array("message" => "Apply Date not valid."));
    die();
}

if($timeStart > $timeEnd)
{
    http_response_code(401);
    echo json_encode(array("message" => "Apply Date not valid."));
    die();
}

$startYear = substr($timeStart, 0, 4);
$endYear = substr($timeEnd, 0, 4);

if($startYear != $endYear)
{
    http_response_code(401);
    echo json_encode(array("message" => "Leave accross years should be divided into 2 leave applications, leave this year and leave next year."));
    die();
}

// leave credit!
$al_credit = 0;
$sl_credit = 0;

$query = "SELECT is_manager, annual_leave, sick_leave from user where id = " . $user_id ;

$stmt = $db->prepare( $query );
$stmt->execute();

while($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
    $is_manager = $row['is_manager'];
    $al_credit = $row['annual_leave'];
    $sl_credit = $row['sick_leave'];
}

// 1. Check if history have the same day
$begin = new DateTime($timeStart);
$end = new DateTime($timeEnd);


$interval = DateInterval::createFromDateString('1 day');
$period = new DatePeriod($begin, $interval, $end);

foreach ($period as $dt) {
    array_push($leaves, $dt->format("Ymd") . " A");
    array_push($leaves, $dt->format("Ymd") . " P");
}

array_push($leaves, $end->format("Ymd") . " A");

if($is_manager == "1")
{
    if($amStart == "P")
        unset($leaves[0]);

    if($amEnd == "P")
        array_push($leaves, $end->format("Ymd") . " P");
}
else
{
    array_push($leaves, $end->format("Ymd") . " P");
}

$query = "SELECT apply_date, apply_period, leave_type  from `leave` where uid = " . $user_id . " and status = 0 and SUBSTRING(apply_date, 1, 4) = '" . $startYear . "'";
$stmt = $db->prepare( $query );
$stmt->execute();
while($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
    $apply_date = $row['apply_date'];
    $apply_period = $row['apply_period'];

    if($row['leave_type'] == 'A')
        $al_credit -= 0.5;

    if($row['leave_type'] == 'B')
        $sl_credit -= 0.5;

    array_push($applied, $apply_date . " " . $apply_period);
}

$inter = array_intersect($leaves, $applied);
if(count($inter) > 0)
{
    http_response_code(401);

    echo json_encode(array("message" => "Duplicate apply."));
    die();
}

// 2. over credit
$query = "SELECT from_date FROM holiday";
$stmt = $db->prepare( $query );
$stmt->execute();
while($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
    $from_date = $row['from_date'];

    array_push($holiday, $from_date . " A");
    array_push($holiday, $from_date . " P");
}

// 3. exclude holiday
$result = array_diff($leaves, $holiday);

if($leave_type == 'A') {
    $al_credit = $al_credit - count($result) * 0.5;
}
if($leave_type == 'B') {
    $sl_credit = $sl_credit - count($result) * 0.5;
}

if($sl_credit < 0 || $al_credit < 0)
{
    http_response_code(401);

    echo json_encode(array("message" => "Apply over yearly credit."));
    die();
}

echo json_encode(array("period" => count($result) * 0.5));


