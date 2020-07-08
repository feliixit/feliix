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

$subquery = "";

$merged_results = array();

$sql = "SELECT * FROM user where user.status = 1 -- and need_punch = 1 ";

$stmt = $db->prepare( $sql );
$stmt->execute();

/* fetch data */
while ($row = $stmt->fetch(PDO::FETCH_ASSOC)){
    if (isset($row)){

        $mdate = date("Ymd");


        $subquery = "SELECT u.id, u.username, l.start_date, l.start_time, l.end_date, l.end_time, l.leave_type, CASE when l.STATUS = -1 then 'W' when leave_type = 'D' then 'D' WHEN reject_id + re_reject_id > 0 THEN 'R' WHEN approval_id * re_approval_id > 0 THEN 'A'  WHEN approval_id * re_approval_id = 0 THEN 'P' END approval FROM user u LEFT JOIN apply_for_leave l ON u.id = l.uid WHERE  start_date <= '" . $mdate . "' and end_date >= '" . $mdate . "' AND l.status <> -1 and l.uid = " . $row['id'];

        $stmt1 = $db->prepare( $subquery );
        $stmt1->execute();



        while($row = $stmt1->fetch(PDO::FETCH_ASSOC)) {
            $merged_results[] = $row;
        }

    }
}

echo json_encode($merged_results, JSON_UNESCAPED_SLASHES);
