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

        $img_url = 'https://storage.googleapis.com/feliiximg/';

        $database = new Database();
        $db = $database->getConnection();
        $db->beginTransaction();
        $conf = new Conf();

        $jwt = (isset($_POST['jwt']) ?  $_POST['jwt'] : null);
        $received_items = (isset($_POST['received_items']) ?  $_POST['received_items'] : []);

        $block_array = json_decode($received_items,true);

        try {
            $_id = $block_array['id'];

            $key = "photo_4";
            if (array_key_exists($key, $_FILES))
            {
                $update_name = SaveImage($key, $batch_id, $batch_type, $user_id, $db, $conf);
                if($update_name != "")
                {
                    $block_array['photo4'] = $img_url . $update_name;

                    $sql = "update od_item set photo4 = :photo4 where id = :id";
                    $stmt = $db->prepare($sql);
                    $stmt->bindParam(':id', $_id);
                    $stmt->bindParam(':photo4', $update_name);
                    $stmt->execute();
                }
            }

            if($block_array['photo4'] == "")
            {
                $sql = "update od_item set photo4 = :photo4 where id = :id";
                $stmt = $db->prepare($sql);
                $stmt->bindParam(':id', $_id);
                $stmt->bindParam(':photo4', $block_array['photo4']);
                $stmt->execute();
            }

            $key = "photo_5";
            if (array_key_exists($key, $_FILES))
            {
                $update_name = SaveImage($key, $batch_id, $batch_type, $user_id, $db, $conf);
                if($update_name != "")
                {
                    $block_array['photo5'] = $img_url . $update_name;

                    $sql = "update od_item set photo5 = :photo5 where id = :id";
                    $stmt = $db->prepare($sql);
                    $stmt->bindParam(':id', $_id);
                    $stmt->bindParam(':photo5', $update_name);
                    $stmt->execute();
                }
            }

            if($block_array['photo5'] == "")
            {
                $sql = "update od_item set photo5 = :photo5 where id = :id";
                $stmt = $db->prepare($sql);
                $stmt->bindParam(':id', $_id);
                $stmt->bindParam(':photo5', $block_array['photo5']);
                $stmt->execute();
            }

            // update remark, charge
            $remark = $block_array['remark'];
            $charge = $block_array['charge'];
            $sql = "update od_item set remark = :remark, charge = :charge where id = :id";
            $stmt = $db->prepare($sql);
            $stmt->bindParam(':id', $_id);
            $stmt->bindParam(':remark', $remark);
            $stmt->bindParam(':charge', $charge);
            $stmt->execute();

            // for($i = 0; $i < count($block_array['items']); $i++)
            // {
            //     $item = $block_array['items'][$i];
                
            //     $key = "photo_1_" . $item['id'];
            //     if (array_key_exists($key, $_FILES))
            //     {
            //         $update_name = SaveImage($key, $batch_id, $batch_type, $user_id, $db, $conf);
            //         if($update_name != "")
            //             $block_array['items'][$i]['photo1'] = $img_url . $update_name;
            //     }

            //     $key = "photo_2_" . $item['id'];
            //     if (array_key_exists($key, $_FILES))
            //     {
            //         $update_name = SaveImage($key, $batch_id, $batch_type, $user_id, $db, $conf);
            //         if($update_name != "")
            //             $block_array['items'][$i]['photo2'] = $img_url . $update_name;
            //     }

            // }

            $query = "update od_item set received_list = :received_list, updated_id = :updated_id, updated_at = now() where id = :id";

            $stmt = $db->prepare($query);

            $stmt->bindParam(':id', $_id);
            $stmt->bindParam(':received_list', json_encode($block_array));
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



function AddAcces7($od_id, $username, $db)
{
    $access7 = "";
    $query = "SELECT access7 FROM `od_main` WHERE id = :od_id";

    // prepare the query
    $stmt = $db->prepare($query);

    // bind the values
    $stmt->bindParam(':od_id', $od_id);

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

    $query = "UPDATE `od_main` SET access7 = :access7 WHERE id = :od_id";

    // prepare the query
    $stmt = $db->prepare($query);

    // bind the values
    $stmt->bindParam(':access7', $access7);
    $stmt->bindParam(':od_id', $od_id);

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
    
    $query = "update od_item
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


function PreserveConfirm($od_id, $pre_confirm, $user_id, $db){
    
    $comment = $pre_confirm;
    $action = "change_confirm";
    $items = '["' . $pre_confirm . '"]';

    $query = "INSERT INTO od_process
    SET
        `od_id` = :od_id,
        `comment` = :comment,
        `action` = :action,
        `items` = :items,
        `status` = 0,
        `create_id` = :create_id,
        `created_at` =  now() ";

    // prepare the query
    $stmt = $db->prepare($query);

    // bind the values
    $stmt->bindParam(':od_id', $od_id);
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

function UpdateProductQty($od_id, $item, $db)
{
    $pid = $item['pid'];
    $qty = 0;
    $qty_str = $item['qty'];
    $backup_qty = 0;
    $backup_qty_str = $item['backup_qty'];
    $org_incoming_element = [];

    $new_incoming_qty = 0;
    $new_incoming_element = [];

    $v1 = $item['v1'];
    $v2 = $item['v2'];
    $v3 = $item['v3'];
    $v4 = $item['v4'];
    $ps_var = $item['ps_var'];

    // check the original qty
    $sql = "select incoming_qty, incoming_element from product_category where id = :pid ";
    
    $stmt = $db->prepare($sql);
    $stmt->bindParam(':pid', $pid);
    $stmt->execute();
    $num = $stmt->rowCount();
    if($num > 0)
    {
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        if($row['incoming_element'] != '')
            $org_incoming_element = json_decode($row['incoming_element'], true);
        else
            $org_incoming_element = [];
    }

    if($qty_str != '') $qty = preg_replace('/[^0-9]/', '', $qty_str);

    if($backup_qty_str != '') $backup_qty = preg_replace('/[^0-9]/', '', $backup_qty_str);

    // update new incoming element, if existed, update the qty else add new element
    $found = false;
    foreach($org_incoming_element as $element)
    {
        if($element['od_id'] == $od_id && $element['v1'] == $v1 && $element['v2'] == $v2 && $element['v3'] == $v3 && $element['v4'] == $v4 && $element['ps_var'] == $ps_var)
        {
            $element['qty'] = $qty;
            $element['backup_qty'] = $backup_qty;
            $new_incoming_qty += $qty + $backup_qty;
            $found = true;
        }
        else
        {
            $new_incoming_qty += $element['qty'] + $element['backup_qty'];
        }
        $new_incoming_element[] = $element;
    }

    if($found == false)
    {
        $new_incoming_qty += $qty + $backup_qty;
        $new_incoming_element[] = array('od_id' => $od_id, 'qty' => $qty, 'backup_qty' => $backup_qty, 'v1' => $v1, 'v2' => $v2, 'v3' => $v3, 'v4' => $v4, 'ps_var' => $ps_var, 'order_date' => date("Y-m-d H:i:s"), 'order_type' => 'taiwan');
    }

    $sql = "update product_category set incoming_qty = :incoming_qty, incoming_element = :incoming_element where id = :pid ";
    $stmt = $db->prepare($sql);
    $stmt->bindParam(':incoming_qty', $new_incoming_qty);
    $stmt->bindParam(':incoming_element', json_encode($new_incoming_element));
    $stmt->bindParam(':pid', $pid);
    $stmt->execute();
}

function RemoveProductQty($od_id, $item, $db)
{
    $pid = $item['pid'];
    $org_incoming_element = [];

    $new_incoming_qty = 0;
    $new_incoming_element = [];

    $v1 = $item['v1'];
    $v2 = $item['v2'];
    $v3 = $item['v3'];
    $v4 = $item['v4'];
    $ps_var = $item['ps_var'];

    // check the original qty
    $sql = "select incoming_qty, incoming_element from product_category where id = :pid ";
    
    $stmt = $db->prepare($sql);
    $stmt->bindParam(':pid', $pid);
    $stmt->execute();
    $num = $stmt->rowCount();
    if($num > 0)
    {
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        if($row['incoming_element'] != '')
            $org_incoming_element = json_decode($row['incoming_element'], true);
        else
            $org_incoming_element = [];
    }

    foreach($org_incoming_element as $element)
    {
        if($element['od_id'] == $od_id && $element['v1'] == $v1 && $element['v2'] == $v2 && $element['v3'] == $v3 && $element['v4'] == $v4 && $element['ps_var'] == $ps_var)
        {
            $found = true;
        }
        else
        {
            $new_incoming_qty += $element['qty'] + $element['backup_qty'];
            $new_incoming_element[] = $element;
        }
    }

    $sql = "update product_category set incoming_qty = :incoming_qty, incoming_element = :incoming_element where id = :pid ";
    $stmt = $db->prepare($sql);
    $stmt->bindParam(':incoming_qty', $new_incoming_qty);
    $stmt->bindParam(':incoming_element', json_encode($new_incoming_element));
    $stmt->bindParam(':pid', $pid);
    $stmt->execute();
}