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

$account = (isset($_GET['account']) ?  $_GET['account'] : 0);
$category = (isset($_GET['category']) ?  $_GET['category'] : '');
$sub_category = (isset($_GET['sub_category']) ?  $_GET['sub_category'] : '');
$start_date = (isset($_GET['start_date']) ?  $_GET['start_date'] : '');
$end_date = (isset($_GET['end_date']) ?  $_GET['end_date'] : '');
$keyword = (isset($_GET['keyword']) ?  $_GET['keyword'] : '');
$select_date_type =(isset($_GET['select_date_type']) ?  $_GET['select_date_type'] : 0);

$page = (isset($_GET['page']) ?  $_GET['page'] : "");
//$size = (isset($_GET['size']) ?  $_GET['size'] : "");


$merged_results = array();



$query = "SELECT sum(cash_out) sum, SUBSTRING(paid_date, 1, 7) `date` from price_record  where is_enabled = 1 ";
$sql = "";

 
            if(!empty($start_date)) {
                $sql = $sql . " and paid_date >= '$start_date' ";
            }

            if(!empty($end_date)) {
                $sql = $sql . " and paid_date < date_add('$end_date', interval 1 day) ";
            }
 
            
            $query = $query.$sql." GROUP BY SUBSTRING(paid_date, 1, 7) order by SUBSTRING(paid_date, 1, 7) asc";



$stmt = $db->prepare( $query );
$stmt->execute();



//while($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
//    array_push($merged_results,$row);
//}

while($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
    $merged_results[] = $row;
}


echo json_encode($merged_results, JSON_UNESCAPED_SLASHES);
