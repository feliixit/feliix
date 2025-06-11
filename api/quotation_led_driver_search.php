<?php
error_reporting(E_ALL);
//header("Access-Control-Allow-Origin: https://feliix.myvnc.com");
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: POST");
header("Access-Control-Max-Age: 3600");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

$jwt = (isset($_COOKIE['jwt']) ?  $_COOKIE['jwt'] : null);

$tag = (isset($_GET['tag']) ?  $_GET['tag'] : '');
$range = (isset($_GET['range']) ?  $_GET['range'] : '');

include_once 'config/core.php';
include_once 'libs/php-jwt-master/src/BeforeValidException.php';
include_once 'libs/php-jwt-master/src/ExpiredException.php';
include_once 'libs/php-jwt-master/src/SignatureInvalidException.php';
include_once 'libs/php-jwt-master/src/JWT.php';

include_once 'config/database.php';
include_once 'config/conf.php';
require_once '../vendor/autoload.php';

$database = new Database();
$db = $database->getConnection();
$db->beginTransaction();
$conf = new Conf();

use \Firebase\JWT\JWT;
use Google\Cloud\Storage\StorageClient;

if ( !isset( $jwt ) ) {
    http_response_code(401);
    
    echo json_encode(array("message" => "Failure at " . date("Y-m-d") . " " . date("h:i:sa") . " Access denied."));
    die();
}
else
{
    try {
        // decode jwt
        $decoded = JWT::decode($jwt, $key, array('HS256'));
        
        $user_id = $decoded->data->id;
        
        // now you can apply
        $uid = $user_id;
        
        $data = [];

        $range_pid = [];
        $range_sql = "";
        if($range == '100W')
            $range_sql = "SELECT distinct pid FROM product_category_tags_index WHERE `key` = 'Wattage' and `watt` >= 1 and `watt` <= 100";
        if($range == '200W')
            $range_sql = "SELECT distinct pid FROM product_category_tags_index WHERE `key` = 'Wattage' and `watt` >= 101 and `watt` <= 200";
        if($range == '300W')
            $range_sql = "SELECT distinct pid FROM product_category_tags_index WHERE `key` = 'Wattage' and `watt` >= 201 and `watt` <= 300";
        if($range == '400W')
            $range_sql = "SELECT distinct pid FROM product_category_tags_index WHERE `key` = 'Wattage' and `watt` >= 301 and `watt` <= 400";
        if($range == '500W')
            $range_sql = "SELECT distinct pid FROM product_category_tags_index WHERE `key` = 'Wattage' and `watt` >= 401 and `watt` <= 500";
        if($range == 'above')
            $range_sql = "SELECT distinct pid FROM product_category_tags_index WHERE `key` = 'Wattage' and `watt` >= 501";

        if($range != '' && $tag != '')
        {
            $query = "SELECT id, code, attributes FROM product_category where id in (" . $range_sql . " and pid in (SELECT distinct pid FROM product_category_tags_index WHERE `key` = '" . $tag . "' and pid in (SELECT distinct pid FROM product_category_tags_index WHERE `key` = 'LED DRIVER'))) and status <> -1 and `out` <> 'Y' ";
        }

        if($range != '' && $tag == '')
        {
            $query = "SELECT id, code, attributes FROM product_category where id in (" . $range_sql . " and pid in (SELECT distinct pid FROM product_category_tags_index WHERE `key` = 'LED DRIVER')) and status <> -1 and `out` <> 'Y' ";
        }

        if($range == '' && $tag != '')
        {
            $query = "SELECT id, code, attributes FROM product_category where id in (SELECT distinct pid FROM product_category_tags_index WHERE `key` = '" . $tag . "' and pid in (SELECT distinct pid FROM product_category_tags_index WHERE `key` = 'LED DRIVER')) and status <> -1 and `out` <> 'Y' ";
        }

        if($range == '' && $tag == '')
        {
            $query = "SELECT id, code, attributes FROM product_category where id in (SELECT distinct pid FROM product_category_tags_index WHERE `key` = 'LED DRIVER') and status <> -1 and `out` <> 'Y' ";
        }

        $stmt = $db->prepare( $query );

        $stmt->execute();

        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)){
            extract($row);
            $wattage = "";
            $attributes = json_decode($attributes, true);
            foreach ($attributes as $att) {
                $key = $att['category'];
                $value = $att['value'];
                if($key == "Wattage") {
                    $wattage = $value;
                }
            }

            $data[] = array(
                'id' => $id,
                'code' => $code,
                'wattage' => $wattage,
            );
        }
        
        
        http_response_code(200);
        echo json_encode(array("message" => "Success at " . date("Y-m-d") . " " . date("h:i:sa"), "data" => $data));
        
    }
    catch (Exception $e){
        
        error_log($e->getMessage());
        $db->rollback();
        http_response_code(501);
        echo json_encode(array("Failure at " . date("Y-m-d") . " " . date("h:i:sa") . $e->getMessage()));
        die();
        
    }
}
?>