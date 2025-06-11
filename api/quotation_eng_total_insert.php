<?php
error_reporting(E_ALL);
//header("Access-Control-Allow-Origin: https://feliix.myvnc.com");
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: POST");
header("Access-Control-Max-Age: 3600");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

$jwt = (isset($_POST['jwt']) ?  $_POST['jwt'] : null);

$quotation_id = isset($_POST['quotation_id']) ? $_POST['quotation_id'] : 0;
$discount = isset($_POST['discount']) ? $_POST['discount'] : 0;
$vat = isset($_POST['vat']) ? $_POST['vat'] : '';
$show_vat = isset($_POST['show_vat']) ? $_POST['show_vat'] : '';
$valid = isset($_POST['valid']) ? $_POST['valid'] : '';
$total = isset($_POST['total']) ? $_POST['total'] : 0;
$pixa = isset($_POST['pixa']) ? $_POST['pixa'] : 0;
$show = isset($_POST['show']) ? $_POST['show'] : '';
$show_word = isset($_POST['show_word']) ? $_POST['show_word'] : '';

$total == '' ? $total = 0 : $total = $total;
$discount == '' ? $discount = 0 : $discount = $discount;


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

        $pre_vat = '';

        $_id = IsExist($quotation_id, $pre_vat, $db);
        if($_id == 0)
        {
        
            $query = "INSERT INTO quotation_eng_total
            SET
                `quotation_id` = :quotation_id,
                `discount` = :discount,
                `vat` = :vat,
                `show_vat` = :show_vat,
                `valid` = :valid,
                `total` = :total,
                `pixa` = :pixa,
                `show` = :show,
                `show_word` = :show_word,
            
                `status` = 0,
                `create_id` = :create_id,
                `created_at` =  now() ";

            // prepare the query
            $stmt = $db->prepare($query);

            // bind the values
            $stmt->bindParam(':quotation_id', $quotation_id);
            $stmt->bindParam(':discount', $discount);
            $stmt->bindParam(':vat', $vat);
            $stmt->bindParam(':show_vat', $show_vat);
            $stmt->bindParam(':valid', $valid);
            $stmt->bindParam(':total', $total);
            $stmt->bindParam(':pixa', $pixa);
            $stmt->bindParam(':show', $show);
            $stmt->bindParam(':show_word', $show_word);

            $stmt->bindParam(':create_id', $user_id);
        
            $last_id = 0;
            // execute the query, also check if query was successful
            try {
                // execute the query, also check if query was successful
                if ($stmt->execute()) {
                    $last_id = $db->lastInsertId();
                } else {
                    $arr = $stmt->errorInfo();
                    error_log($arr[2]);
                    $db->rollback();
                    http_response_code(501);
                    echo json_encode("Failure at " . date("Y-m-d") . " " . date("h:i:sa") . " " . $arr[2]);
                    die();
                }
            } catch (Exception $e) {
                error_log($e->getMessage());
                $db->rollback();
                http_response_code(501);
                echo json_encode(array("Failure at " . date("Y-m-d") . " " . date("h:i:sa") . " " . $e->getMessage()));
                die();
            }

        }
        else
        {
            $query = "update quotation_eng_total
                SET
                    `discount` = :discount,
                    `vat` = :vat,
                    `show_vat` = :show_vat,
                    `valid` = :valid,
                    `total` = :total,
                    `pixa` = :pixa,
                    `show` = :show,
                    `show_word` = :show_word,

                    `updated_id` = :updated_id,
                    `updated_at` = now()
                    where id = :id";

            // prepare the query
            $stmt = $db->prepare($query);

            // bind the values
            $stmt->bindParam(':discount', $discount);
            $stmt->bindParam(':vat', $vat);
            $stmt->bindParam(':show_vat', $show_vat);
            $stmt->bindParam(':valid', $valid);
            $stmt->bindParam(':total', $total);
            $stmt->bindParam(':pixa', $pixa);
            $stmt->bindParam(':show', $show);
            $stmt->bindParam(':show_word', $show_word);
            
            $stmt->bindParam(':updated_id', $user_id);

            $stmt->bindParam(':id', $_id);

            $last_id = $quotation_id;
            // execute the query, also check if query was successful
            try {
                // execute the query, also check if query was successful
                if (!$stmt->execute()) {
                    $arr = $stmt->errorInfo();
                    error_log($arr[2]);
                    $db->rollback();
                    http_response_code(501);
                    echo json_encode(array("Failure at " . date("Y-m-d") . " " . date("h:i:sa") . " " . $arr[2]));
                    die();
                }
            } catch (Exception $e) {
                error_log($e->getMessage());
                $db->rollback();
                http_response_code(501);
                echo json_encode(array("Failure at " . date("Y-m-d") . " " . date("h:i:sa") . " " . $e->getMessage()));
                die();
            }
        }


        $db->commit();

        
        http_response_code(200);
        echo json_encode(array("message" => "Success at " . date("Y-m-d") . " " . date("h:i:sa"), "id" => $last_id));
        
    }
    catch (Exception $e){

        error_log($e->getMessage());
        $db->rollback();
        http_response_code(501);
        echo json_encode(array("Failure at " . date("Y-m-d") . " " . date("h:i:sa") . $e->getMessage()));
        die();

    }
}


function IsExist($quotation_id, &$pre_vat, $db)
{
    $sql = "SELECT id, vat from quotation_eng_total where quotation_id = :quotation_id";
           
    $stmt = $db->prepare($sql);
    $stmt->bindParam(':quotation_id',  $quotation_id);
    $stmt->execute();

    $_id = 0;
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        $_id = $row['id'];
        $pre_vat = $row['vat'];
    }

    return $_id;
}
