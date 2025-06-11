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

        $data = json_decode(file_get_contents('php://input'), true);

        $items = (isset($data['items']) ?  $data['items'] : []);

        try {
            for($i = 0; $i < count($items); $i++)
            {
                $item = $items[$i];
                $item_id = $item['item_id'];

                $_id = $item['id'];
                $rec_id = $item['rec_id'];

                // update product qty
                UpdateProductQty($item, $db);
                updateOrderReceiveItem($db, $item_id, $user_id);
                updateOrderTrackingItem($db, $_id, $user_id);
                updateInventoryChangeHistory($db, $rec_id, $item, $user_id);
        

                // update received_list
                $query = "select received_list from od_item where id = :id";
                $stmt = $db->prepare($query);
                $stmt->bindParam(':id', $item_id);
                $stmt->execute();

                $num = $stmt->rowCount();
                if ($num > 0) {
                    $row = $stmt->fetch(PDO::FETCH_ASSOC);
                    $received_list = json_decode($row['received_list'], true);
                } else {
                    http_response_code(501);
                    echo json_encode(array("Failure at " . date("Y-m-d") . " " . date("h:i:sa") . " " . "No received list found."));
                    die();
                }

                // update qty in received_list
                $block_array = array();
                foreach ($received_list['items'] as $key => $value) {
                    if ($value['id'] == $receive_id) {
                        $value['qty'] = $value['qty'] - 1;
                    }
                    if ($value['qty'] > 0) {
                        array_push($block_array, $value);
                    }
                }

                $received_list['items'] = $block_array;

                $query = "update od_item set received_list = :received_list, updated_id = :updated_id, updated_at = now() where id = :id";

                $stmt = $db->prepare($query);

                $stmt->bindParam(':id', $item_id);
                $stmt->bindParam(':received_list', json_encode($received_list));
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
            }

            // check if all items are voided
            $query = "select qty from order_receive_item where id = :order_id";
            $stmt = $db->prepare($query);
            $stmt->bindParam(':order_id', $item_id);
            $stmt->execute();

            $num = $stmt->rowCount();
            if ($num > 0) {
                $row = $stmt->fetch(PDO::FETCH_ASSOC);
                $qty = $row['qty'];
            } else {
                http_response_code(501);
                echo json_encode(array("Failure at " . date("Y-m-d") . " " . date("h:i:sa") . " " . "No order found."));
                die();
            }

            if($qty <= 0)
            {
                // delete the order_receive_item
                $query = "update order_receive_item set status = -1 where id = :item_id";
                $stmt = $db->prepare($query);
                $stmt->bindParam(':item_id', $item_id);
                if (!$stmt->execute()) {
                    $arr = $stmt->errorInfo();
                    error_log($arr[2]);
                    $db->rollback();
                    http_response_code(501);
                    echo json_encode("Failure at " . date("Y-m-d") . " " . date("h:i:sa") . " " . $arr[2]);
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

function UpdateProductQty($item, $db)
{
    $pid = $item['product_id'];

    // check the original qty
    $sql = "select incoming_qty, project_qty, project_s_qty, stock_qty, stock_s_qty from product_category where id = :pid ";

    $incoming_qty = 0;
    $project_qty = 0;
    $project_s_qty = 0;
    $stock_qty = 0;
    $stock_s_qty = 0;

    $qty = 1;
    
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
            $stock = $project_s_qty - $qty;
        }
        
        if($sample == 'No')
        {
            $stock_sql = ", project_qty = :stock";
            $stock = $project_qty - $qty;
        }
    }

    else if($pool == 'Stock Pool')
    {
        if($sample == 'Yes')
        {
            $stock_sql = ", stock_s_qty = :stock";
            $stock = $stock_s_qty - $qty;
        }
        
        if($sample == 'No')
        {
            $stock_sql = ", stock_qty = :stock";
            $stock = $stock_qty - $qty;
        }
    }

    $sql = "update product_category set incoming_qty = :new_qty " . $stock_sql . " where id = :pid and incoming_qty = :incoming_qty";   // incoming_qty equality is for atomic update
    $stmt = $db->prepare($sql);
    $new_qty = $incoming_qty;
    $stmt->bindParam(':new_qty', $new_qty);
    if($stock_sql != "")
    {
        $stmt->bindParam(':stock', $stock);
    }
    $stmt->bindParam(':incoming_qty', $incoming_qty);
    $stmt->bindParam(':pid', $pid);
    $stmt->execute();
}

function updateOrderReceiveItem($db, $item_id, $user_id) {
    $query = "update order_receive_item
            SET
                qty = qty - 1,
                updated_id = :updated_id,
                updated_at = now()
            where id = :item_id;";

    $stmt = $db->prepare($query);
    $stmt->bindParam(':item_id', $item_id);
    $stmt->bindParam(':updated_id', $user_id);

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

function updateOrderTrackingItem($db, $id, $user_id) {
    
    $query = "update order_tracking_item 
                set status = -1, 
                updated_id = :updated_id,
                updated_at = now() 
            where id = :id";
    $stmt = $db->prepare($query);
    $stmt->bindParam(':id', $id);
    $stmt->bindParam(':updated_id', $user_id);

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


function updateInventoryChangeHistory($db, $last_id, $item, $user_id) {
    $barcode_list = array();

    // previous barcode list
    $query = "select affected_tracking from inventory_change_history where item_id = :item_id";
    $stmt = $db->prepare($query);
    $stmt->bindParam(':item_id', $last_id);
    $stmt->execute();

    $num = $stmt->rowCount();
    if ($num > 0) {
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        $barcode_list = json_decode($row['affected_tracking'], true);
    }

    // remove the barcode from the list
    $barcode_list = array_filter($barcode_list, function($barcode) use ($item) {
        return $barcode != $item['barcode'];
    });

    // let barcode_lsit only string array
    $barcode_list = array_values($barcode_list);
    $barcode_list = array_map('strval', $barcode_list);

    $query = "update inventory_change_history
            SET
                affected_tracking = :affected_tracking,
                affected_qty = affected_qty - 1,
                updated_id = :updated_id,
                updated_at = now()
            where id = :id;";
    $stmt = $db->prepare($query);
    $stmt->bindParam(':id', $last_id);
    $stmt->bindParam(':affected_tracking', json_encode($barcode_list));
    $stmt->bindParam(':updated_id', $user_id);

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