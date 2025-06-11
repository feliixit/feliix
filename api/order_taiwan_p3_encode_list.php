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
        $od_id = (isset($_POST['od_id']) ?  $_POST['od_id'] : 0);
        $serial_name = (isset($_POST['serial_name']) ?  $_POST['serial_name'] : "");
        $od_name = (isset($_POST['od_name']) ?  $_POST['od_name'] : "");
        $index = (isset($_POST['index']) ?  $_POST['index'] : 0);

        $block_array = json_decode($received_items,true);
        $new_items = [];

        try {
            $_id = $block_array['id'];
            
            for($i = 0; $i < count($block_array['items']); $i++)
            {
                
                $item = $block_array['items'][$i];

                if($item['status'] != 1)
                    continue;
                
                
                $key = "photo_1_" . $item['id'];
                if (array_key_exists($key, $_FILES))
                {
                    $update_name = SaveImage($key, $batch_id, $batch_type, $user_id, $db, $conf);
                    if($update_name != "")
                    {
                        $item['photo1'] = $img_url . $update_name;
                        $block_array['items'][$i]['photo1'] = $img_url . $update_name;
                    }
                }

                $key = "photo_2_" . $item['id'];
                if (array_key_exists($key, $_FILES))
                {
                    $update_name = SaveImage($key, $batch_id, $batch_type, $user_id, $db, $conf);
                    if($update_name != "")
                    {
                        $item['photo2'] = $img_url . $update_name;
                        $block_array['items'][$i]['photo2'] = $img_url . $update_name;
                    }
                }

                $new_items[] = $item;

                if($item['status'] == 1 && $item['id'] == $index)
                {
                    // update product qty
                    UpdateProductQty($od_id, $item, $db);
                    $last_id = insertOrderReceiveItem($db, $od_id, $_id, $item, $user_id);
                    $barcode_list = insertOrderTrackingItem($db, $item, $last_id, $user_id);
                    insertInventoryChangeHistory($db, $last_id, $barcode_list, $item, $user_id, $serial_name, $od_name, $_id);
                }
            }

            $block_array['items'] = $new_items;

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


function UpdateProductQty($od_id, $item, $db)
{
    $pid = $item['pid'];
    $qty = 0;
    $qty_str = $item['qty'];

    


    // check the original qty
    $sql = "select incoming_qty, project_qty, project_s_qty, stock_qty, stock_s_qty from product_category where id = :pid ";

    $incoming_qty = 0;
    $project_qty = 0;
    $project_s_qty = 0;
    $stock_qty = 0;
    $stock_s_qty = 0;

    if($qty_str != '') $qty = preg_replace('/[^0-9]/', '', $qty_str);
    
    $stmt = $db->prepare($sql);
    $stmt->bindParam(':pid', $pid);
    $stmt->execute();
    $num = $stmt->rowCount();
    if($num > 0)
    {
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        $incoming_qty = $row['incoming_qty'];
        $project_qty = $row['project_qty'];
        $project_s_qty = $row['project_s_qty'];
        $stock_qty = $row['stock_qty'];
        $stock_s_qty = $row['stock_s_qty'];
    }

    $pool = $item['which_pool'];
    $sample = $item['as_sample'];

    $stock_sql = "";
    $stock = 0;
    if($pool == 'Project Pool')
    {
        if($sample == 'Yes')
        {
            $stock_sql = ", project_s_qty = :stock";
            $stock = $project_s_qty + $qty;
        }
        
        if($sample == 'No')
        {
            $stock_sql = ", project_qty = :stock";
            $stock = $project_qty + $qty;
        }
    }

    else if($pool == 'Stock Pool')
    {
        if($sample == 'Yes')
        {
            $stock_sql = ", stock_s_qty = :stock";
            $stock = $stock_s_qty + $qty;
        }
        
        if($sample == 'No')
        {
            $stock_sql = ", stock_qty = :stock";
            $stock = $stock_qty + $qty;
        }
    }

    $sql = "update product_category set incoming_qty = :new_qty " . $stock_sql . " where id = :pid and incoming_qty = :incoming_qty";   // incoming_qty equality is for atomic update
    $stmt = $db->prepare($sql);
    $new_qty = $incoming_qty - $qty;
    $stmt->bindParam(':new_qty', $new_qty);
    if($stock_sql != "")
    {
        $stmt->bindParam(':stock', $stock);
    }
    $stmt->bindParam(':incoming_qty', $incoming_qty);
    $stmt->bindParam(':pid', $pid);
    $stmt->execute();
}

function insertOrderReceiveItem($db, $od_id, $item_id, $item, $user_id) {
    $query = "INSERT INTO order_receive_item
            SET
                od_id = :od_id,
                item_id = :item_id,
                receive_id = :receive_id,
                product_id = :product_id,
                pic = :pic,
                v1 = :v1,
                v2 = :v2,
                v3 = :v3,
                v4 = :v4,
                received_date = now(),
                qty = :qty,
                which_pool = :which_pool,
                as_sample = :as_sample,
                location = :location,
                project_id = :project_id,
                status = 0,
                create_id = :create_id,
                created_at = now();";

    $stmt = $db->prepare($query);
    $stmt->bindParam(':od_id', $od_id);
    $stmt->bindParam(':item_id', $item_id);
    $stmt->bindParam(':receive_id', $item['id']);
    $stmt->bindParam(':product_id', $item['pid']);
    $stmt->bindParam(':pic', $item['photo1']);
    $stmt->bindParam(':v1', $item['v1']);
    $stmt->bindParam(':v2', $item['v2']);
    $stmt->bindParam(':v3', $item['v3']);
    $stmt->bindParam(':v4', $item['v4']);
    $stmt->bindParam(':qty', $item['qty']);
    $stmt->bindParam(':which_pool', $item['which_pool']);
    $stmt->bindParam(':as_sample', $item['as_sample']);
    $stmt->bindParam(':location', $item['location']);
    $stmt->bindParam(':project_id', $item['project_id']);
    $stmt->bindParam(':create_id', $user_id);

    try {
        if ($stmt->execute()) {
            return $db->lastInsertId();
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

function insertOrderTrackingItem($db, $item, $item_id, $user_id) {
    
    $query = "select barcode from order_tracking_item where barcode like :barcode order by barcode desc limit 1";
    $stmt = $db->prepare($query);
    $str_date = $item['receive_date'];
    // remove year 20 from date head
    if($str_date != '')
    {
        $str_date = substr($str_date, 2);
    }
    
    if($str_date == '')
    {
        $str_date = date("ymd");
    }
    $str_date = str_replace('-', '', $str_date);
    $str_date = str_replace('/', '', $str_date);

    $barcode = $str_date . str_pad($item['pid'], 5, '0', STR_PAD_LEFT);
    $barcode = $barcode . '%';
    $stmt->bindParam(':barcode', $barcode);
    $stmt->execute();
    $num = $stmt->rowCount();

    $qty_base = 0;

    $barcode_list = array();

    if($num > 0)
    {
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        $barcode = $row['barcode'];
        $qty_base = substr($barcode, -5);
        $qty_base = intval($qty_base);
    }

    $inc = intval($item['qty']);

    for($i = 0; $i < $inc; $i++)
    {
        $barcode = $str_date . str_pad($item['pid'], 5, '0', STR_PAD_LEFT) . str_pad($qty_base + $i + 1, 5, '0', STR_PAD_LEFT);
        $query = "INSERT INTO order_tracking_item
        SET
            item_id = :item_id,
            barcode = :barcode,
            status = 0,
            create_id = :create_id,
            created_at = now()";
        $stmt = $db->prepare($query);
        $stmt->bindParam(':item_id', $item_id);
        $stmt->bindParam(':barcode', $barcode);
        $stmt->bindParam(':create_id', $user_id);

        try {
            // execute the query, also check if query was successful
            if ($stmt->execute()) {
                $last_id = $db->lastInsertId();

                $barcode_list[] = $barcode;

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

    return $barcode_list;
    
}


function insertInventoryChangeHistory($db, $last_id, $barcode_list, $item, $user_id, $od_serial, $od_name, $related_item) {
    $query = "INSERT INTO inventory_change_history
            SET
                item_id = :item_id,
                reason = :reason,
                pid = :pid,
                v1 = :v1,
                v2 = :v2,
                v3 = :v3,
                v4 = :v4,
                related_record = :related_record,
                releated_item = :releated_item,
                affected_qty = :affected_qty,
                affected_sign = 'Add',
                affected_tracking = :affected_tracking,
                influence_pool = :which_pool,
                new_related_project = :project_id,
                influence_location = :location,
                influence_sample = :as_sample,
                status = 0,
                create_id = :create_id,
                created_at = now();";

    $barcode_json = json_encode($barcode_list);
    $related_record = $od_serial . " " . $od_name;

    $reason = "Add item into inventory because of receiving item from order";

    $stmt = $db->prepare($query);
    $stmt->bindParam(':item_id', $last_id);
    $stmt->bindParam(':reason', $reason);
    $stmt->bindParam(':pid', $item['pid']);
    $stmt->bindParam(':v1', $item['v1']);
    $stmt->bindParam(':v2', $item['v2']);
    $stmt->bindParam(':v3', $item['v3']);
    $stmt->bindParam(':v4', $item['v4']);
    $stmt->bindParam(':related_record', $related_record);
    $stmt->bindParam(':releated_item', $related_item);
    $stmt->bindParam(':affected_qty', $item['qty']);
    $stmt->bindParam(':affected_tracking', $barcode_json);
    $stmt->bindParam(':which_pool', $item['which_pool']);
    $stmt->bindParam(':project_id', $item['project_id']);
    $stmt->bindParam(':location', $item['location']);
    $stmt->bindParam(':as_sample', $item['as_sample']);

    $stmt->bindParam(':create_id', $user_id);

    try {
        if ($stmt->execute()) {
            return $db->lastInsertId();
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