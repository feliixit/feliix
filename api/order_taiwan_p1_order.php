<?php
error_reporting(E_ERROR | E_PARSE);

header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: *");
header("Access-Control-Max-Age: 3600");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

$jwt = (isset($_COOKIE['jwt']) ?  $_COOKIE['jwt'] : null);
include_once 'config/core.php';
include_once 'config/conf.php';
include_once 'libs/php-jwt-master/src/BeforeValidException.php';
include_once 'libs/php-jwt-master/src/ExpiredException.php';
include_once 'libs/php-jwt-master/src/SignatureInvalidException.php';
include_once 'libs/php-jwt-master/src/JWT.php';
include_once 'mail.php';
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
        $user_name = $decoded->data->username;
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
$conf = new Conf();

$uid = $user_id;

$od_id = (isset($_POST['od_id']) ?  $_POST['od_id'] : 0);
$items = (isset($_POST['items']) ?  $_POST['items'] : 0);

$comment = (isset($_POST['comment']) ? $_POST['comment'] : '');

$od_name = (isset($_POST['od_name']) ? $_POST['od_name'] : '');
$serial_name = (isset($_POST['serial_name']) ?  $_POST['serial_name'] : '');
$project_name = (isset($_POST['project_name']) ?  $_POST['project_name'] : '');

$action = 'ordered';

$items_array = json_decode($items,true);


// update main table
$query = "UPDATE od_main SET `updated_id` = :updated_id,  `updated_at` = now() WHERE id = :id";
$stmt = $db->prepare($query);
$stmt->bindParam(':updated_id', $uid);
$stmt->bindParam(':id', $od_id);
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


try{

    for($i=0; $i<count($items_array); $i++) 
    {
        $item_id = $items_array[$i]['id'];

        if($item_id != 0)
        {
            $query = "update od_item
            SET
                `confirm` = 'O',
                `status_at` = now()
            where id = :id  ";

            // prepare the query
            $stmt = $db->prepare($query);

            $stmt->bindParam(':id', $item_id);
        }
    
        $jsonEncodedReturnArray = "";
        if ($stmt->execute()) {
            $returnArray = array('ret' => $item_id);
            $jsonEncodedReturnArray = json_encode($returnArray, JSON_PRETTY_PRINT);

        }
        else
        {
            $arr = $stmt->errorInfo();
            error_log($arr[2]);
        }

        // update product_category
        $pid = $items_array[$i]['pid'];
        if($pid != 0)
            UpdateProduct($od_id, $items_array[$i], $serial_name, $db);
        if($pid != 0)
            UpdateProductQty($od_id, $items_array[$i], $db);

    }

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

    order_notification($user_name, 'access1,access3,access4,access5', 'access2', $project_name, $serial_name, $od_name, 'Order - Close Deal', $comment, $action, $items_array, $od_id);

    echo $jsonEncodedReturnArray;
}
catch (Exception $e)
{
    error_log($e->getMessage());
}

function UpdateProduct($od_id, $item, $od_name,  $db)
{
    $pid = $item['pid'];
    $v1 = $item['v1'];
    $v2 = $item['v2'];
    $v3 = $item['v3'];
    $v4 = $item['v4'];
    $ps_var = $item['ps_var'];

    if($v1 != '' || $v2 != '' || $v3 != '' || $v4 != '')
    {
        if($v4 != '')
            $query = "update product set last_order = :od_id, last_order_name = :od_name, last_order_type = 'close-deal', last_order_at = now() where product_id = :pid and 1st_variation like '%" . $v1 . "' and 2rd_variation like '%" . $v2 . "' and 3th_variation like '%" . $v3 . "' and 4th_variation like '%" . $v4 . "' ";
        else
            $query = "update product set last_order = :od_id, last_order_name = :od_name, last_order_type = 'close-deal', last_order_at = now() where product_id = :pid and 1st_variation like '%" . $v1 . "' and 2rd_variation like '%" . $v2 . "' and 3th_variation like '%" . $v3 . "' ";
        // prepare the query
        $stmt = $db->prepare($query);

        // bind the values
        $stmt->bindParam(':od_id', $od_id);
        $stmt->bindParam(':od_name', $od_name);

        $stmt->bindParam(':pid', $pid);

        $stmt->execute();
    }

    
    foreach($ps_var as $ps_lines)
    {
        $jsonstr = "";

        $lines = explode("\n", $ps_lines);
        foreach($lines as $line)
        {
            if(trim($line) != "")
            {
                // split key and value by :
                $line = explode(":", $line);
                $key = $line[0];
                $value = $line[1];
                $jsonstr .= '"' . trim($key) . '":"' . trim($value) . '",';
            }
        }

        $jsonstr = rtrim($jsonstr, ",");
        $jsonstr = "{" . $jsonstr . "}";

        $var_json = json_decode($jsonstr, true);
        
        $v1 = "";
        $v2 = "";
        $v3 = "";
        $v4 = "";
        // iterate through json
        foreach ($var_json as $key => $value) {
            if($key != 'id')
            {
                if($v1 == "")
                    $v1 = $value;
                else if($v2 == "")
                    $v2 = $value;
                else if($v3 == "")
                    $v3 = $value;
                else if($v4 == "")
                    $v4 = $value;
            }
            else if($key == 'id')
            {
                $pid = $value;
            }
        }

        if($v4 != '')
            $query = "update product set last_order = :od_id, last_order_name = :od_name, last_order_type = 'close-deal', last_order_at = now() where product_id = :pid and 1st_variation like '%" . $v1 . "' and 2rd_variation like '%" . $v2 . "' and 3th_variation like '%" . $v3 . "' and 4th_variation like '%" . $v4 . "' ";
        else
            $query = "update product set last_order = :od_id, last_order_name = :od_name, last_order_type = 'close-deal', last_order_at = now() where product_id = :pid and 1st_variation like '%" . $v1 . "' and 2rd_variation like '%" . $v2 . "' and 3th_variation like '%" . $v3 . "' ";

        // prepare the query
        $stmt = $db->prepare($query);

        // bind the values
        $stmt->bindParam(':od_id', $od_id);
        $stmt->bindParam(':od_name', $od_name);

        $stmt->bindParam(':pid', $pid);


        $stmt->execute();
        

    }

    if($v1 == '' && $v2 == '' && $v3 == '' && $v4 == '' && count($ps_var) == 0)
    {
        // update product_category
        $pid = $item['pid'];
        $query = "update product_category set last_order = :od_id, last_order_name = :od_name, last_order_type = 'close-deal', last_order_at = now() where id = :pid ";
        // prepare the query
        $stmt = $db->prepare($query);
    
        // bind the values
        $stmt->bindParam(':od_id', $od_id);
        $stmt->bindParam(':od_name', $od_name);
        
        $stmt->bindParam(':pid', $pid);
    
        $stmt->execute();
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