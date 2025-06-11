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

// $key = (isset($_GET['key']) ?  $_GET['key'] : '');
// $key = urldecode($key);

$parent = (isset($_GET['parent']) ?  $_GET['parent'] : '');

$page = (isset($_GET['page']) ?  $_GET['page'] : "1");
$size = (isset($_GET['size']) ?  $_GET['size'] : "20");

$merged_results = array();

$lv1 = "";
$lv2 = "";
$lv3 = "";
$lv4 = "";

// seperate parent into 4 levels
if($parent != "")
{
    $lv1 = substr($parent, 0, 2);
    $lv2 = substr($parent, 2, 2);
    $lv3 = substr($parent, 4, 2);
    $lv4 = substr($parent, 6, 2);
}

$query = "SELECT d.id, m.code code1, m.category cat1, s.code code2, s.category cat2, b.code code3, b.category cat3, d.code code4, d.category cat4, IFNULL(q.qty, 0) qty, IFNULL(q.reserve_qty, 0) reserve_qty, d.photo, '' url
            FROM office_items_main_category m
            left join office_items_sub_category s on m.code = s.parent_code ";
if($lv1 != "")
{
    $query = $query . " and m.code = '" . $lv1 . "' ";
}
$query = $query . " left join office_items_brand b on CONCAT(m.code, s.code) = b.parent_code ";
if($lv2 != "")
{
    $query = $query . " and s.code = '" . $lv2 . "' ";
}
$query = $query . " left join office_items_description d on CONCAT(m.code,s.code,b.code) = d.parent_code ";
if($lv3 != "")
{
    $query = $query . " and b.code = '" . $lv3 . "' ";
}
$query = $query . " LEFT JOIN office_items_stock q ON q.code = concat(d.parent_code, d.code) ";
$query = $query . "   where m.status <> -1 and s.status <> -1 and b.status <> -1 and d.status <> -1 ";

if($lv4 != "")
{
    $query = $query . " and d.code = '" . $lv4 . "' ";
}



$query_cnt = "SELECT count(*) cnt
                FROM office_items_main_category m
                left join office_items_sub_category s on m.code = s.parent_code ";
if($lv1 != "")
{
    $query_cnt = $query_cnt . " and m.code = '" . $lv1 . "' ";
}
$query_cnt = $query_cnt . " left join office_items_brand b on CONCAT(m.code, s.code) = b.parent_code ";
if($lv2 != "")
{
    $query_cnt = $query_cnt . " and s.code = '" . $lv2 . "' ";
}
$query_cnt = $query_cnt . " left join office_items_description d on CONCAT(m.code,s.code,b.code) = d.parent_code ";
if($lv3 != "")
{
    $query_cnt = $query_cnt . " and b.code = '" . $lv3 . "' ";
}
$query_cnt = $query_cnt . "  LEFT JOIN office_items_stock q ON q.code = concat(d.parent_code, d.code) ";
$query_cnt = $query_cnt . "   where m.status <> -1 and s.status <> -1 and b.status <> -1 and d.status <> -1 ";

if($lv4 != "")
{
    $query_cnt = $query_cnt . " and d.code = '" . $lv4 . "' ";
}

// if($key != "")
// {
//     $query = $query . " and parent_code = '" . $key . "' ";
//     $query_cnt = $query_cnt . " and parent_code = '" . $key . "' ";
// }


$query = $query . " order by m.sn, s.sn, b.sn, d.sn ";


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

$cnt = 0;
$stmt_cnt = $db->prepare( $query_cnt );
$stmt_cnt->execute();
while($row = $stmt_cnt->fetch(PDO::FETCH_ASSOC)) {
    $cnt = $row['cnt'];
}

while($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
    $merged_results[] = $row;
}

if(count($merged_results) > 0)
{
    $merged_results[0]['cnt'] = $cnt;
}

for($i = 0; $i < count($merged_results); $i++)
{
    if($merged_results[$i]['photo'] != '')
        $merged_results[$i]['url'] = "https://storage.googleapis.com/feliiximg/" . $merged_results[$i]['photo'];
}

echo json_encode($merged_results, JSON_UNESCAPED_SLASHES);


?>