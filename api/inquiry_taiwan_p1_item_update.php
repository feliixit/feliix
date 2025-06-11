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
include_once 'mail.php';

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

        $item = (isset($_POST['item']) ?  $_POST['item'] : "[]");

        $page = (isset($_POST['page']) ?  $_POST['page'] : 0);
        $access2 = (isset($_POST['access2']) ? $_POST['access2'] : false);

        $block_array = json_decode($block,true);
        $item_array = json_decode($item,true);

        $iq_name = (isset($_POST['iq_name']) ? $_POST['iq_name'] : '');
        $serial_name = (isset($_POST['serial_name']) ?  $_POST['serial_name'] : '');
        $project_name = (isset($_POST['project_name']) ?  $_POST['project_name'] : '');

        $access7 = (isset($_POST['access7']) ? $_POST['access7'] : false);

        $confirm = "";
    
        if ($iq_id == 0) {
            http_response_code(401);
            echo json_encode(array("message" => "Failure at " . date("Y-m-d") . " " . date("h:i:sa") . " " . "Access denied."));
            die();
        }


        try {
            for($i=0; $i<count($block_array); $i++) 
            {
                // get previous block confirm
                $query = "select confirm from iq_item where id = :id";
                $stmt = $db->prepare($query);
                $stmt->bindParam(':id', $block_array[$i]['id']);
                $stmt->execute();
                $row = $stmt->fetch(PDO::FETCH_ASSOC);
                $pre_confirm = $row['confirm'];

                $confirm = (isset($block_array[$i]['confirm']) ?  $block_array[$i]['confirm'] : '');

                // record pre_confirm
                if($pre_confirm != $confirm && $confirm == 'W')
                    PreserveConfirm($iq_id, $pre_confirm, $user_id, $db);

                // insert quotation_page_type_block
                $query = "UPDATE iq_item
                    SET
                    `sn` = :sn,
                    `confirm` = :confirm,
                    `brand` = :brand,
                    `brand_other` = :brand_other, ";

if($block_array[$i]['photo1'] == '')
{
    $query .= " `photo1` = '', ";
}

if($block_array[$i]['photo2'] == '')
{
    $query .= " `photo2` = '', ";
}

if($block_array[$i]['photo3'] == '')
{
    $query .= " `photo3` = '', ";
}


                $query .= "         
                    `code` = :code,
                    `brief` = :brief,
                    `listing` = :listing,
                    `qty` = :qty,
                    `srp` = :srp,
                    `date_needed` = :date_needed,
                    ";

                if(isset($block_array[$i]['shipping_way']))
                    $query .= " `shipping_way` = :shipping_way, ";

                if(isset($block_array[$i]['shipping_number']))
                    $query .= " `shipping_number` = :shipping_number, ";

                $query .= "
                    `pid` = :pid,
                    `v1` = :v1,
                    `v2` = :v2,
                    `v3` = :v3,
                    `v4` = :v4,
                    `ps_var` = :ps_var,
                    updated_id = :updated_id,
                    updated_at = now()
                    where id = :id
                    ";


                // prepare the query
                $stmt = $db->prepare($query);

                $id = isset($block_array[$i]['id']) ? $block_array[$i]['id'] : 0;
                $sn = isset($block_array[$i]['sn']) ? $block_array[$i]['sn'] : 0;

                $confirm = isset($block_array[$i]['confirm']) ? $block_array[$i]['confirm'] : '';
                $brand = isset($block_array[$i]['brand']) ? $block_array[$i]['brand'] : '';
                $brand_other = isset($block_array[$i]['brand_other']) ? $block_array[$i]['brand_other'] : '';
                
                $code = isset($block_array[$i]['code']) ? $block_array[$i]['code'] : '';
                $brief = isset($block_array[$i]['brief']) ? $block_array[$i]['brief'] : '';
                $listing = isset($block_array[$i]['listing']) ? $block_array[$i]['listing'] : '';

                $qty = isset($block_array[$i]['qty']) ? $block_array[$i]['qty'] : '';
                $srp = isset($block_array[$i]['srp']) ? $block_array[$i]['srp'] : '';
                $date_needed = isset($block_array[$i]['date_needed']) ? $block_array[$i]['date_needed'] : '';

                $shipping_way = isset($block_array[$i]['shipping_way']) ? $block_array[$i]['shipping_way'] : '';
                $shipping_number = isset($block_array[$i]['shipping_number']) ? $block_array[$i]['shipping_number'] : '';

                $pid = isset($block_array[$i]['pid']) ? $block_array[$i]['pid'] : '';

                $v1 = isset($block_array[$i]['v1']) ? $block_array[$i]['v1'] : '';
                $v2 = isset($block_array[$i]['v2']) ? $block_array[$i]['v2'] : '';
                $v3 = isset($block_array[$i]['v3']) ? $block_array[$i]['v3'] : '';
                $v4 = isset($block_array[$i]['v4']) ? $block_array[$i]['v4'] : '';

                $ps_var = isset($block_array[$i]['ps_var']) ? $block_array[$i]['ps_var'] : [];
                $json_ps_var = json_encode($ps_var);

                // bind the values
                $stmt->bindParam(':id', $id);
                $stmt->bindParam(':sn', $sn);
                $stmt->bindParam(':confirm', $confirm);
                $stmt->bindParam(':brand', $brand);
                $stmt->bindParam(':brand_other', $brand_other);
    
                $stmt->bindParam(':code', $code);
                $stmt->bindParam(':brief', $brief);
                $stmt->bindParam(':listing', $listing);
                $stmt->bindParam(':qty', $qty);
                $stmt->bindParam(':srp', $srp);
                $stmt->bindParam(':date_needed', $date_needed);

                if(isset($block_array[$i]['shipping_way']))
                    $stmt->bindParam(':shipping_way', $shipping_way);

                if(isset($block_array[$i]['shipping_number']))
                    $stmt->bindParam(':shipping_number', $shipping_number);

                $stmt->bindParam(':pid', $pid);

                $stmt->bindParam(':v1', $v1);
                $stmt->bindParam(':v2', $v2);
                $stmt->bindParam(':v3', $v3);
                $stmt->bindParam(':v4', $v4);

                $stmt->bindParam(':ps_var', $json_ps_var);
              
                $stmt->bindParam(':updated_id', $user_id);
               
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

                $_id = $block_array[$i]['id'];

                $batch_type = "iq_item";
                $batch_id = $_id;

                $key = "photo_1";
                if (array_key_exists($key, $_FILES))
                {
                    $update_name = SaveImage($key, $batch_id, $batch_type, $user_id, $db, $conf);
                    if($update_name != "")
                        UpdateImageNameVariation("1", $update_name, $batch_id, $db);
                }

                $key = "photo_2";
                if (array_key_exists($key, $_FILES))
                {
                    $update_name = SaveImage($key, $batch_id, $batch_type, $user_id, $db, $conf);
                    if($update_name != "")
                        UpdateImageNameVariation("2", $update_name, $batch_id, $db);
                }

                $key = "photo_3";
                if (array_key_exists($key, $_FILES))
                {
                    $update_name = SaveImage($key, $batch_id, $batch_type, $user_id, $db, $conf);
                    if($update_name != "")
                        UpdateImageNameVariation("3", $update_name, $batch_id, $db);
                }

                if($page == 3 && $access2 == 'true')
                    order_notification04($user_name, 'access1,access3,access4,access5', '', $project_name, $serial_name, $iq_name, 'Order - Close Deal', '', 'new_message_23', $item_array, $iq_id);

            }

            if($access7 == 'true')
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


function SaveImage($type, $batch_id, $batch_type, $user_id, $db, $conf)
{
    try {
        if($_FILES[$type]['name'] == null)
            return "";
        // Loop through each file

        if(isset($_FILES[$type]['name']))
        {
            $image_name = $_FILES[$type]['name'];
            $valid_extensions = array("jpg","jpeg","png","gif","pdf","docx","doc","xls","xlsx","ppt","pptx","zip","rar","7z","txt","dwg","skp","psd","evo","dwf","bmp");
            $extension = pathinfo($image_name, PATHINFO_EXTENSION);
            if (in_array(strtolower($extension), $valid_extensions)) 
            {
                //$upload_path = 'img/' . time() . '.' . $extension;

                $storage = new StorageClient([
                    'projectId' => 'predictive-fx-284008',
                    'keyFilePath' => $conf::$gcp_key
                ]);

                $bucket = $storage->bucket('feliiximg');

                $upload_name = time() . '_' . pathinfo($image_name, PATHINFO_FILENAME) . '.' . $extension;

                $file_size = filesize($_FILES[$type]['tmp_name']);
                $size = 0;

                $obj = $bucket->upload(
                    fopen($_FILES[$type]['tmp_name'], 'r'),
                    ['name' => $upload_name]);

                $info = $obj->info();
                $size = $info['size'];

                if($size == $file_size && $file_size != 0 && $size != 0)
                {
                    $query = "INSERT INTO gcp_storage_file
                    SET
                        batch_id = :batch_id,
                        batch_type = :batch_type,
                        filename = :filename,
                        gcp_name = :gcp_name,

                        create_id = :create_id,
                        created_at = now()";

                    // prepare the query
                    $stmt = $db->prepare($query);
                
                    // bind the values
                    $stmt->bindParam(':batch_id', $batch_id);
                    $stmt->bindParam(':batch_type', $batch_type);
                    $stmt->bindParam(':filename', $image_name);
                    $stmt->bindParam(':gcp_name', $upload_name);
        
                    $stmt->bindParam(':create_id', $user_id);

                    try {
                        // execute the query, also check if query was successful
                        if ($stmt->execute()) {
                            $last_id = $db->lastInsertId();
                        }
                        else
                        {
                            $arr = $stmt->errorInfo();
                            error_log($arr[2]);
                        }
                    }
                    catch (Exception $e)
                    {
                        error_log($e->getMessage());
                        $db->rollback();
                        http_response_code(501);
                        echo json_encode(array("Failure at " . date("Y-m-d") . " " . date("h:i:sa") . " " . $e->getMessage()));
                        die();
                    }

                    return $upload_name;
                }
                else
                {
                    $message = 'There is an error while uploading file';
                    $db->rollback();
                    http_response_code(501);
                    echo json_encode(array("Failure at " . date("Y-m-d") . " " . date("h:i:sa") . " " . $message));
                    die();
                    
                }
            }
            else
            {
                $message = 'Only Images or Office files allowed to upload';
                $db->rollback();
                http_response_code(501);
                echo json_encode(array("Failure at " . date("Y-m-d") . " " . date("h:i:sa") . " " . $message));
                die();
            }
        }

        
    } catch (Exception $e) {
        $db->rollback();
        http_response_code(501);
        echo json_encode(array("Failure at " . date("Y-m-d") . " " . date("h:i:sa") . " Error uploading, Please use laptop to upload again."));
        die();
    }
}


function UpdateImageNameVariation($sn, $upload_name, $batch_id, $db){
    
    $query = "update iq_item
    SET photo" . $sn . " = :gcp_name where id=:id";

    // prepare the query
    $stmt = $db->prepare($query);

    // bind the values
    $stmt->bindParam(':id', $batch_id);

    $stmt->bindParam(':gcp_name', $upload_name);


    try {
        // execute the query, also check if query was successful
        if ($stmt->execute()) {
            $last_id = $db->lastInsertId();
        }
        else
        {
            $arr = $stmt->errorInfo();
            error_log($arr[2]);
        }
    }
    catch (Exception $e)
    {
        error_log($e->getMessage());
        $db->rollback();
        http_response_code(501);
        echo json_encode(array("Failure at " . date("Y-m-d") . " " . date("h:i:sa") . " " . $e->getMessage()));
        die();
    }
}


function PreserveConfirm($iq_id, $pre_confirm, $user_id, $db){
    
    $comment = $pre_confirm;
    $action = "change_confirm";
    $items = '["' . $pre_confirm . '"]';

    $query = "INSERT INTO iq_process
    SET
        `iq_id` = :iq_id,
        `comment` = :comment,
        `action` = :action,
        `items` = :items,
        `status` = 0,
        `create_id` = :create_id,
        `created_at` =  now() ";

    // prepare the query
    $stmt = $db->prepare($query);

    // bind the values
    $stmt->bindParam(':iq_id', $iq_id);
    $stmt->bindParam(':comment', $comment);
    $stmt->bindParam(':action', $action);
    $stmt->bindParam(':items', $items);
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
