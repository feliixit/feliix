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
$user_id = 0;

if (!isset($jwt)) {
    http_response_code(401);

    echo json_encode(array("message" => "Access denied."));
    die();
} else {
    try {
        // decode jwt
        $decoded = JWT::decode($jwt, $key, array('HS256'));
        $GLOBALS["user_id"] = $decoded->data->id;

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

        echo json_encode(array("message" => "Access denied."));
        die();
    }
}

header('Access-Control-Allow-Origin: *');

include_once 'config/database.php';

$database = new Database();
$db = $database->getConnection();

$conf = new Conf();

switch ($method) {
    case "GET":
        $task_id = (isset($_GET['id']) ?  $_GET['id'] : 0);
        $notes = [];
        $notes = GetShipping($task_id, $db);
        $jsonEncodedReturnArray = json_encode($notes, JSON_PRETTY_PRINT);
        echo $jsonEncodedReturnArray;
        break;
    
    case 'POST':
        // get database connection
        $item_str = (isset($_POST['items']) ?  $_POST['items'] : []);
        $uid = $user_id;
        $o_id = (isset($_POST['od_id']) ?  $_POST['od_id'] : 0);
        $comment = (isset($_POST['comment']) ? $_POST['comment'] : '');
        $type = (isset($_POST['type']) ? $_POST['type'] : '');

        $items = json_decode($item_str, true);

        $od_name = (isset($_POST['od_name']) ? $_POST['od_name'] : '');
        $serial_name = (isset($_POST['serial_name']) ?  $_POST['serial_name'] : '');
        $project_name = (isset($_POST['project_name']) ?  $_POST['project_name'] : '');

        $diff = [];
        
        // update main table
        $query = "UPDATE od_main SET `updated_id` = :updated_id,  `updated_at` = now() WHERE id = :id";
        $stmt = $db->prepare($query);
        $stmt->bindParam(':updated_id', $uid);
        $stmt->bindParam(':id', $o_id);
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
       
        for($i=0; $i<count($items); $i++) {

            $id = $items[$i]['id'];

            $pre_item = GetShipping($id, $db);

            add_process($o_id, $comment, $type, json_encode($pre_item, JSON_PRETTY_PRINT), $uid, $db);

            $test_update = false;
            $delivery_updeate = false;

            if(count($pre_item) > 0)
            {
                $ret = show_diff($pre_item[0], $items[$i]);
                if($ret != "")
                {
                    $diff[] = $ret;

                    if(strpos($ret, "check_t") !== false || strpos($ret, "remark_t") !== false)
                    {
                        $test_update = true;
                    }

                    if(strpos($ret, "check_d") !== false || strpos($ret, "remark_d") !== false)
                    {
                        $delivery_updeate = true;
                    }
                    
                }
            }

            $shipping_way = $items[$i]['shipping_way'];
            $shipping_number = $items[$i]['shipping_number'];
            $shipping_vendor = $items[$i]['shipping_vendor'];
            $eta = $items[$i]['eta'];
            $arrive = $items[$i]['arrive'];
            $charge = $items[$i]['charge'];
            $test = $items[$i]['test'];
            $delivery = $items[$i]['delivery'];
            $final = $items[$i]['final'];
            $remark = $items[$i]['remark'];
            $remark_t = $items[$i]['remark_t'];
            $remark_d = $items[$i]['remark_d'];
            $check_t = $items[$i]['check_t'];
            $check_d = $items[$i]['check_d'];

            $photo4 = $items[$i]['photo4'];
            $photo5 = $items[$i]['photo5'];

            //if($shipping_way == 'air')
            //    $shipping_number = "";

        
            $query = "update od_item
                SET
                `shipping_way` = :shipping_way,
                `shipping_number` = :shipping_number,
                `shipping_vendor` = :shipping_vendor,
                `eta` = :eta,
                `arrive` = :arrive,
                `charge` = :charge,
                `test` = :test,
                `delivery` = :delivery,
                `final` = :final,
                `remark` = :remark,
                `remark_t` = :remark_t,
                `remark_d` = :remark_d,
                `check_t` = :check_t,
                `check_d` = :check_d,
                ";

                if($photo4 == '')
                {
                    $query .= " `photo4` = '', ";
                }
                
                if($photo5 == '')
                {
                    $query .= " `photo5` = '', ";
                }
                
                if($test_update == true)
                {
                    $query .= " `test_updated_name` = '$user_name',
                    `test_updated_at` = now(), ";
                }

                if($delivery_updeate == true)
                {
                    $query .= " `delivery_updated_name` = '$user_name',
                    `delivery_updated_at` = now(), ";
                }
                
                
            $query .= "         
                `updated_id` = :updated_id,
                `updated_at` = now()
            where id = :id";
         

            // prepare the query
            $stmt = $db->prepare($query);

            // bind the values
            $stmt->bindParam(':shipping_way', $shipping_way);
            $stmt->bindParam(':shipping_number', $shipping_number);
            $stmt->bindParam(':shipping_vendor', $shipping_vendor);
            $stmt->bindParam(':eta', $eta);
            $stmt->bindParam(':arrive', $arrive);
            $stmt->bindParam(':charge', $charge);
            $stmt->bindParam(':test', $test);
            $stmt->bindParam(':delivery', $delivery);
            $stmt->bindParam(':final', $final);
            $stmt->bindParam(':remark', $remark);
            $stmt->bindParam(':remark_t', $remark_t);
            $stmt->bindParam(':remark_d', $remark_d);
            $stmt->bindParam(':check_t', $check_t);
            $stmt->bindParam(':check_d', $check_d);
        
            $stmt->bindParam(':updated_id', $uid);
            $stmt->bindParam(':id', $id);

            $last_id = 0;
            // execute the query, also check if query was successful
            try {
                // execute the query, also check if query was successful
                if ($stmt->execute()) {
                    $last_id = $db->lastInsertId();
                } else {
                    $arr = $stmt->errorInfo();
                    error_log($arr[2]);
                }
            } catch (Exception $e) {
                error_log($e->getMessage());
            }

            $_id = $id;

            $batch_type = "od_item";
            $batch_id = $_id;

            $key = "photo_" . $_id . "_4";
            if (array_key_exists($key, $_FILES))
            {
                $update_name = SaveImage($key, $batch_id, $batch_type, $user_id, $db, $conf);
                if($update_name != "")
                {
                    UpdateImageNameVariation("4", $update_name, $batch_id, $db);
                    UpdateImageRealNameVariation("4", substr($update_name, strpos($update_name, '_') + 1), $batch_id, $db);

                    $items[$i]['photot4_name'] = substr($update_name, strpos($update_name, '_') + 1);
                }
            }

            $key = "photo_" . $_id . "_5";
            if (array_key_exists($key, $_FILES))
            {
                $update_name = SaveImage($key, $batch_id, $batch_type, $user_id, $db, $conf);
                if($update_name != "")
                {
                    UpdateImageNameVariation("5", $update_name, $batch_id, $db);
                    UpdateImageRealNameVariation("5", substr($update_name, strpos($update_name, '_') + 1), $batch_id, $db);

                    $items[$i]['photot5_name'] = substr($update_name, strpos($update_name, '_') + 1);
                }
            }

        }

        $jsonEncodedReturnArray = json_encode($diff, JSON_PRETTY_PRINT);

        $items_array = [];

        foreach ($items as $item) {
            foreach ($diff as $di)
            {
                // find id with value in strings
                $temp = explode(",", $di);
                $sets = array();
                foreach ($temp as $value) {
                    $array = explode(': ', $value);
                    $array[0] = str_replace(' ', '', $array[0]);
                    $array[0] = str_replace("'", '', $array[0]);
                    $array[1] = trim($array[1], "'");
                    $sets[$array[0]] = $array[1];
                }

                if($item['id'] == $sets["id"])
                {
                    $items_array[] = $item;
                    break;
                }
            }
        }

        if(count($items_array) == 0)
        {
            echo $jsonEncodedReturnArray;
            break;
        }

        if($type == 'ship_info')
            order_notification($user_name, 'access4', 'access2,access1,access3,access5', $project_name, $serial_name, $od_name, 'Order - Mockup', $comment, $type, $items_array, $o_id);
        if($type == 'ware_info')
            order_notification($user_name, 'access5', 'access2,access1,access3,access4', $project_name, $serial_name, $od_name, 'Order - Mockup', $comment, $type, $items_array, $o_id);
        if($type == 'assing_test')
            order_notification02($user_name, '', 'access1,access3,access5', $project_name, $serial_name, $od_name, 'Order - Mockup', $comment, $type, $items_array, $o_id);
        if($type == 'edit_test')
            order_notification02($user_name, 'access5', 'access1,access2,access3,access4', $project_name, $serial_name, $od_name, 'Order - Mockup', $comment, $type, $items_array, $o_id);
        if($type == 'assign_delivery')
            order_notification02($user_name, '', 'access1,access3,access5', $project_name, $serial_name, $od_name, 'Order - Mockup', $comment, $type, $items_array, $o_id);
        if($type == 'edit_delivery')
            order_notification02($user_name, 'access5', 'access1,access2,access3,access4', $project_name, $serial_name, $od_name, 'Order - Mockup', $comment, $type, $items_array, $o_id);
        
        echo $jsonEncodedReturnArray;

        break;
}

function show_diff($pre_item, $item)
{
    $diff = [];

    foreach(array_keys($pre_item) as $key) {
        if(array_key_exists($key, $item)) {
            if($pre_item[$key] != $item[$key]) {
                $diff[] = "'" . $key . "' : '" . $pre_item[$key] . " -> " . $item[$key] . "'";
            }
        }
    }

    if(count($diff) > 0)
        $diff = "'id' : " . $pre_item['id'] . ", 'items':"  . implode(",",$diff);
    else
        $diff = "";

    return $diff;
}


function GetShipping($id, $db){

    $query = "
            SELECT n.id,
            n.shipping_way,
            n.shipping_number,
            n.shipping_vendor,
            n.eta,
            n.arrive,
            n.charge,
            n.test,
            n.delivery,
            n.final,
            n.remark,
            n.remark_t,
            n.remark_d,
            n.check_t,
            n.check_d,
            n.create_id,
            n.created_at,
            n.updated_id,
            n.updated_at,
            n.photo4_name,
            n.photo5_name
                FROM   od_item n
            WHERE  n.id = " . $id . "
            ORDER BY n.id
    ";

    // prepare the query
    $stmt = $db->prepare($query);
    $stmt->execute();

    $merged_results = [];

    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        $id = $row['id'];
        $shipping_way = $row['shipping_way'];
        $shipping_number = $row['shipping_number'];
        $shipping_vendor = $row['shipping_vendor'];
        $eta = $row['eta'];
        $arrive = $row['arrive'];
        $charge = $row['charge'];
        $test = $row['test'];
        $delivery = $row['delivery'];
        $final = $row['final'];
        $remark = $row['remark'];
        $remark_t = $row['remark_t'];
        $remark_d = $row['remark_d'];
        $check_t = $row['check_t'];
        $check_d = $row['check_d'];
        $create_id = $row['create_id'];

        $photo4_name = $row['photo4_name'];
        $photo5_name = $row['photo5_name'];

        $created_at = $row['created_at'];
      
        $merged_results[] = array(
            "id" => $id,
            "shipping_way" => $shipping_way,
            "shipping_number" => $shipping_number,
            "shipping_vendor" => $shipping_vendor,
            "eta" => $eta,
            "arrive" => $arrive,
            "charge" => $charge,
            "test" => $test,
            "delivery" => $delivery,
            "final" => $final,
            "remark" => $remark,
            "remark_t" => $remark_t,
            "remark_d" => $remark_d,
            "check_t" => $check_t,
            "check_d" => $check_d,
            "create_id" => $create_id,
 
            "created_at" => $created_at,

            "photo4_name" => $photo4_name,
            "photo5_name" => $photo5_name,

        );
    }

    return $merged_results;
}

function add_process($od_id, $comment, $action, $items, $user_id, $db)
{
    
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



function SaveImage($type, $batch_id, $batch_type, $user_id, $db, $conf)
{
    try {
        if($_FILES[$type]['name'] == null)
            return "";
        // Loop through each file

        if(isset($_FILES[$type]['name']))
        {
            $image_name = $_FILES[$type]['name'];
            $valid_extensions = array("jpg","jpeg","png","gif","pdf","docx","doc","xls","xlsx","ppt","pptx","zip","rar","7z","txt","dwg","skp","psd","evo");
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



function UpdateImageRealNameVariation($sn, $upload_name, $batch_id, $db){
    
    $query = "update od_item
    SET photo" . $sn . "_name = :gcp_name where id=:id";

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
