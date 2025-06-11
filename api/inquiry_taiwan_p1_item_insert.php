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
        $iq_id = (isset($_POST['iq_id']) ?  $_POST['iq_id'] : 0);
        $block = (isset($_POST['block']) ?  $_POST['block'] : []);

        $access7 = (isset($_POST['access7']) ?  $_POST['access7'] : false);

        $block_array = json_decode($block,true);

    
        if ($iq_id == 0) {
            http_response_code(401);
            echo json_encode(array("message" => "Failure at " . date("Y-m-d") . " " . date("h:i:sa") . " " . "Access denied."));
            die();
        }


        try {
            $sn = GetMaxSn($iq_id, $db);

            for($i=0; $i<count($block_array); $i++) 
            {
                $sn++;
                // insert quotation_page_type_block
                $query = "INSERT INTO iq_item
                    SET
                    `iq_id` = :iq_id,
                    `sn` = :sn,
                    `confirm` = :confirm,
                    `brand` = :brand,
                    `brand_other` = :brand_other,
                    `photo1` = :photo1,
                    `photo2` = :photo2,
                    `photo3` = :photo3,
                    `code` = :code,
                    `brief` = :brief,
                    `listing` = :listing,
                    `qty` = :qty,
                    `srp` = :srp,
                    `date_needed` = :date_needed,
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

                $confirm = isset($block_array[$i]['confirm']) ? $block_array[$i]['confirm'] : '';
                $brand = isset($block_array[$i]['brand']) ? $block_array[$i]['brand'] : '';
                $brand_other = isset($block_array[$i]['brand_other']) ? $block_array[$i]['brand_other'] : '';

                $photo1 = isset($block_array[$i]['photo1']) ? $block_array[$i]['photo1'] : '';
                $photo2 = isset($block_array[$i]['photo2']) ? $block_array[$i]['photo2'] : '';
                $photo3 = isset($block_array[$i]['photo3']) ? $block_array[$i]['photo3'] : '';

                $code = isset($block_array[$i]['code']) ? $block_array[$i]['code'] : '';
                $brief = isset($block_array[$i]['brief']) ? $block_array[$i]['brief'] : '';
                $listing = isset($block_array[$i]['listing']) ? $block_array[$i]['listing'] : '';

                $qty = isset($block_array[$i]['qty']) ? $block_array[$i]['qty'] : '';
                $srp = isset($block_array[$i]['srp']) ? $block_array[$i]['srp'] : '';
                $date_needed = isset($block_array[$i]['date_needed']) ? $block_array[$i]['date_needed'] : '';
                $pid = isset($block_array[$i]['pid']) ? $block_array[$i]['pid'] : '';

                $v1 = isset($block_array[$i]['v1']) ? $block_array[$i]['v1'] : '';
                $v2 = isset($block_array[$i]['v2']) ? $block_array[$i]['v2'] : '';
                $v3 = isset($block_array[$i]['v3']) ? $block_array[$i]['v3'] : '';
                $v4 = isset($block_array[$i]['v4']) ? $block_array[$i]['v4'] : '';

                $ps_var = isset($block_array[$i]['ps_var']) ? $block_array[$i]['ps_var'] : [];
                $json_ps_var = json_encode($ps_var);

                $status = isset($block_array[$i]['status']) ? $block_array[$i]['status'] : 0;
                $status = $status == '' ? 0 : $status;
       

                // bind the values
                $stmt->bindParam(':iq_id', $iq_id);
                $stmt->bindParam(':sn', $sn);
                $stmt->bindParam(':confirm', $confirm);
                $stmt->bindParam(':brand', $brand);
                $stmt->bindParam(':brand_other', $brand_other);
                $stmt->bindParam(':photo1', $photo1);
                $stmt->bindParam(':photo2', $photo2);
                $stmt->bindParam(':photo3', $photo3);
                $stmt->bindParam(':code', $code);
                $stmt->bindParam(':brief', $brief);
                $stmt->bindParam(':listing', $listing);
                $stmt->bindParam(':qty', $qty);
                $stmt->bindParam(':srp', $srp);
                $stmt->bindParam(':date_needed', $date_needed);
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

            // update access7 users
            if($access7 == "true")
                AddAcces7($iq_id, $user_name, $db);

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

function AddAcces7($iq_id, $username, $db)
{
    $access7 = "";
    $query = "SELECT access7 FROM `iq_main` WHERE id = :iq_id";

    // prepare the query
    $stmt = $db->prepare($query);

    // bind the values
    $stmt->bindParam(':iq_id', $iq_id);

    try {
        // execute the query, also check if query was successful
        if ($stmt->execute()) {
            $row = $stmt->fetch(PDO::FETCH_ASSOC);
            $access7 = $row['access7'];
        } else {
            $arr = $stmt->errorInfo();
            error_log($arr[2]);
        }
    } catch (Exception $e) {
        error_log($e->getMessage());
    }

    // seperate by comma and check if username is already in the list
    $access7_array = explode(",", $access7);
    if (!in_array($username, $access7_array)) {
        array_push($access7_array, $username);
    }
    // implode by comma and update to access7
    $access7 = implode(",", $access7_array);

    $query = "UPDATE `iq_main` SET access7 = :access7 WHERE id = :iq_id";

    // prepare the query
    $stmt = $db->prepare($query);

    // bind the values
    $stmt->bindParam(':access7', $access7);
    $stmt->bindParam(':iq_id', $iq_id);

    try {
        // execute the query, also check if query was successful
        if ($stmt->execute()) {
            return true;
        } else {
            $arr = $stmt->errorInfo();
            error_log($arr[2]);
            return false;
        }
    } catch (Exception $e) {
        error_log($e->getMessage());
        return false;
    }
}

function GetMaxSn($iq_id, $db)
{
    $max_sn = 0;
    $query = "SELECT max(sn*1) as max_sn FROM `iq_item` WHERE iq_id = :iq_id";

    // prepare the query
    $stmt = $db->prepare($query);

    // bind the values
    $stmt->bindParam(':iq_id', $iq_id);

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