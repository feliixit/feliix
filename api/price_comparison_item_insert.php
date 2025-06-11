<?php

error_reporting(E_ALL);
ini_set('display_errors', 1);
$jwt = (isset($_COOKIE['jwt']) ?  $_COOKIE['jwt'] : null);
include_once 'config/core.php';
include_once 'libs/php-jwt-master/src/BeforeValidException.php';
include_once 'libs/php-jwt-master/src/ExpiredException.php';
include_once 'libs/php-jwt-master/src/SignatureInvalidException.php';
include_once 'libs/php-jwt-master/src/JWT.php';
require_once '../vendor/autoload.php';


use \Firebase\JWT\JWT;
use Google\Cloud\Storage\StorageClient;

$method = $_SERVER['REQUEST_METHOD'];


if (!isset($jwt)) {
    http_response_code(401);

    echo json_encode(array("message" => "Failure at " . date("Y-m-d") . " " . date("h:i:sa") . " " . "Access denied."));
    die();
} else {
    try {
        // decode jwt
        $decoded = JWT::decode($jwt, $key, array('HS256'));
        $user_id = $decoded->data->id;
        $user_name = $decoded->data->username;
        //if(!$decoded->data->is_admin)
        //{
        //  http_response_code(401);

        //  echo json_encode(array("message" => "Access denied."));
        //  die();
        //}
    }
    // if decode fails, it means jwt is invalid
    catch (Exception $e) {

        http_response_code(401);

        echo json_encode(array("message" => "Failure at " . date("Y-m-d") . " " . date("h:i:sa") . " " . "Access denied."));
        die();
    }
}

header('Access-Control-Allow-Origin: *');

include_once 'config/database.php';

switch ($method) {

    case 'POST':

        $database = new Database();
        $db = $database->getConnection();
        $db->beginTransaction();
        $conf = new Conf();

        $jwt = (isset($_POST['jwt']) ?  $_POST['jwt'] : null);
        $od_id = (isset($_POST['od_id']) ?  $_POST['od_id'] : 0);
        $block = (isset($_POST['block']) ?  $_POST['block'] : []);

        $block_array = json_decode($block,true);

    
        if ($od_id == 0) {
            http_response_code(401);
            echo json_encode(array("message" => "Failure at " . date("Y-m-d") . " " . date("h:i:sa") . " " . "Access denied."));
            die();
        }


        try {
            $sn = GetMaxSn($od_id, $db);

            for($i=0; $i<count($block_array); $i++) 
            {
                $sn++;
                // insert quotation_page_type_block
                $query = "INSERT INTO price_comparison_item
                    SET
                    `od_id` = :od_id,
                    `option_id` = :option_id,
                    `legend_id` = :legend_id,
                    `sn` = :sn,
                    `photo1` = :photo1,
                    `photo2` = :photo2,
                    `photo3` = :photo3,
                    `code` = :code,
                    `brief` = :brief,
                    `list` = :list,
                    `qty` = :qty,
                    `price` = :price,
                    `ratio` = :ratio,
                    `notes` = :notes,
                    `discount` = :discount,
                    `amount` = :amount,
                    `desc` = :desc,
                    `pid` = :pid,
                    `v1` = :v1,
                    `v2` = :v2,
                    `v3` = :v3,
                    `v4` = :v4,
                    `ps_var` = :ps_var,
                    `status` = :status,
                    `create_id` = :create_id,
                    `created_at` = now()";

                // prepare the query
                $stmt = $db->prepare($query);

                //$sn = isset($block_array[$i]['sn']) ? $block_array[$i]['sn'] : 0;

                $od_id = (isset($_POST['od_id']) ?  $_POST['od_id'] : 0);

                $option_id = isset($block_array[$i]['option_id']) ? $block_array[$i]['option_id'] : 0;
                $legend_id = isset($block_array[$i]['legend_id']) ? $block_array[$i]['legend_id'] : 0;

                $photo1 = isset($block_array[$i]['photo1']) ? $block_array[$i]['photo1'] : '';
                $photo2 = isset($block_array[$i]['photo2']) ? $block_array[$i]['photo2'] : '';
                $photo3 = isset($block_array[$i]['photo3']) ? $block_array[$i]['photo3'] : '';

                $code = isset($block_array[$i]['code']) ? $block_array[$i]['code'] : '';
                $brief = isset($block_array[$i]['brief']) ? $block_array[$i]['brief'] : '';
                $list = isset($block_array[$i]['list']) ? $block_array[$i]['list'] : '';

                $qty = isset($block_array[$i]['qty']) ? $block_array[$i]['qty'] : '';
                $price = isset($block_array[$i]['price']) ? $block_array[$i]['price'] : '';
                $ratio = isset($block_array[$i]['ratio']) ? $block_array[$i]['ratio'] : 0;
                $notes = isset($block_array[$i]['notes']) ? $block_array[$i]['notes'] : '';
                $amount = isset($block_array[$i]['amount']) ? $block_array[$i]['amount'] : 0;
                $discount = isset($block_array[$i]['discount']) ? $block_array[$i]['discount'] : 0;
                $desc = isset($block_array[$i]['desc']) ? $block_array[$i]['desc'] : '';

                $pid = isset($block_array[$i]['pid']) ? $block_array[$i]['pid'] : '';

                $v1 = isset($block_array[$i]['v1']) ? $block_array[$i]['v1'] : '';
                $v2 = isset($block_array[$i]['v2']) ? $block_array[$i]['v2'] : '';
                $v3 = isset($block_array[$i]['v3']) ? $block_array[$i]['v3'] : '';
                $v4 = isset($block_array[$i]['v4']) ? $block_array[$i]['v4'] : '';

                $ps_var = isset($block_array[$i]['ps_var']) ? $block_array[$i]['ps_var'] : [];
                $json_ps_var = json_encode($ps_var);

                $status = isset($block_array[$i]['status']) ? $block_array[$i]['status'] : 0;
                $status = $status == '' ? 0 : $status;
       
                $ratio = $ratio == '' ? 0 : $ratio;
                $amount = $amount == '' ? 0 : $amount;
                $discount = $discount == '' ? 0 : $discount;

                // bind the values
                $stmt->bindParam(':od_id', $od_id);
                $stmt->bindParam(':option_id', $option_id);
                $stmt->bindParam(':legend_id', $legend_id);
                $stmt->bindParam(':sn', $sn);
       
                $stmt->bindParam(':photo1', $photo1);
                $stmt->bindParam(':photo2', $photo2);
                $stmt->bindParam(':photo3', $photo3);
                $stmt->bindParam(':code', $code);
                $stmt->bindParam(':brief', $brief);
                $stmt->bindParam(':list', $list);
                $stmt->bindParam(':qty', $qty);
                $stmt->bindParam(':price', $price);
                $stmt->bindParam(':ratio', $ratio);
                $stmt->bindParam(':notes', $notes);
                $stmt->bindParam(':discount', $discount);
                $stmt->bindParam(':amount', $amount);
                $stmt->bindParam(':desc', $desc);
                
                $stmt->bindParam(':pid', $pid);

                $stmt->bindParam(':v1', $v1);
                $stmt->bindParam(':v2', $v2);
                $stmt->bindParam(':v3', $v3);
                $stmt->bindParam(':v4', $v4);

                $stmt->bindParam(':ps_var', $json_ps_var);

                $stmt->bindParam(':status', $status);
              
                $stmt->bindParam(':create_id', $user_id);
               
                try {
                    // execute the query, also check if query was successful
                    if ($stmt->execute()) {
                        $block_id = $db->lastInsertId();
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

            $db->commit();

            http_response_code(200);
            echo json_encode(array("message" => "Success at " . date("Y-m-d") . " " . date("h:i:sa")));
        } catch (Exception $e) {

            error_log($e->getMessage());
            $db->rollback();
            http_response_code(501);
            echo json_encode(array("Failure at " . date("Y-m-d") . " " . date("h:i:sa") . " " . $e->getMessage()));
            die();
        }
        break;
}

function GetMaxSn($od_id, $db)
{
    $max_sn = 0;
    $query = "SELECT max(sn*1) as max_sn FROM `price_comparison_item` WHERE od_id = :od_id";

    // prepare the query
    $stmt = $db->prepare($query);

    // bind the values
    $stmt->bindParam(':od_id', $od_id);

    try {
        // execute the query, also check if query was successful
        if ($stmt->execute()) {
            $row = $stmt->fetch(PDO::FETCH_ASSOC);
            $max_sn = $row['max_sn'];
            return $max_sn;
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