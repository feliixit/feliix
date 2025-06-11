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
include_once 'config/conf.php';
include_once 'config/database.php';


use Google\Cloud\Storage\StorageClient;


use \Firebase\JWT\JWT;

$method = $_SERVER['REQUEST_METHOD'];
$user_id = 0;

const TO_COMPLETE = 6;

$crud = 'Releaser released';

if (!isset($jwt)) {
    http_response_code(401);
    
    echo json_encode(array("message" => "Access denied."));
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
        
        echo json_encode(array("message" => "Access denied."));
        die();
    }
}

header('Access-Control-Allow-Origin: *');

include_once 'config/database.php';

$database = new Database();
$db = $database->getConnection();
$db->beginTransaction();
$conf = new Conf();

switch ($method) {
    
    case 'POST':
        // get database connection
        
        $batch_type = "office_item_release";
        
        $uid = $user_id;
        
        $data = json_decode(file_get_contents('php://input'), true);
        
        $item_id = $data['item_id'];
        $sig_date = $data['sig_date'];
        // $sig_name = $data['sig_name'];
        // $releaser_sig_date = $data['releaser_sig_date'];
        // $releaser_sig_name = $data['releaser_sig_name'];
        $list = $data['list'];

        $list = UpdateQty($list, $db);

        $request_no = $data['request_no'];
        
        $sig_date = str_replace('data:image/png;base64,', '', $sig_date);
        $sig_date = str_replace('data:image/jpeg;base64,', '', $sig_date);
        $sig_date = str_replace(' ', '+', $sig_date);
        if($sig_date != "")
        $file_sig_date = base64_decode($sig_date);
        
        // $sig_name = str_replace(' ', '+', $sig_name[1]);
        // if($sig_name != "")
        // $file_sig_name = base64_decode($sig_name);
        
        // $releaser_sig_date = str_replace(' ', '+', $releaser_sig_date[1]);
        // if($releaser_sig_date != "")
        // $file_releaser_sig_date = base64_decode($releaser_sig_date);
        
        // $releaser_sig_name = str_replace(' ', '+', $releaser_sig_name[1]);
        // if($releaser_sig_name != "")
        // $file_releaser_sig_name = base64_decode($releaser_sig_name);
        
        $file_name_sig_name = "";
        // $file_name_sig_date = "";
        
        // $file_name_releaser_sig_name = "";
        // $file_name_releaser_sig_date = "";
        
        try {
            if (isset($file_sig_date)) {
                upload_file($item_id, $file_sig_date, "online_voucher.jpg", $user_id, $db, $conf);
            }
            
            // if(isset($file_sig_name))
            // {
            //     upload_file($item_id, $file_sig_name, "sig_name.jpg", $user_id, $db, $conf);
            // }
            
            // if(isset($file_releaser_sig_date))
            // {
            //     upload_file($item_id, $file_releaser_sig_date, "releaser_sig_date.jpg", $user_id, $db, $conf);
            // }
            
            // if(isset($file_releaser_sig_name))
            // {
            //     upload_file($item_id, $file_releaser_sig_name, "releaser_sig_name.jpg", $user_id, $db, $conf);
            // }
            
        }catch (Exception $e){
            
            http_response_code(401);
            
            echo json_encode(array("message" => "Access denied."));
            die();
        }
        
        foreach($list as $item)
        {
            $code = $item['code1'] . $item['code2'] . $item['code3'] . $item['code4'];
            $amount = $item['amount'] * -1;

            $act = "Office Item Application";
            $act_1 = "OIA-" . $request_no;
            $act_2 = "Release " . $item['amount'];
            
            // office_stock_history
            $query = "INSERT INTO office_stock_history
                SET
                    `request_id` = :request_id,
                    `code` = :code,
                    `qty` = :qty,
                    `reserve_qty` = :qty,
                    `action` = :_action,
                    `act_1` = :act_1,
                    `act_2` = :act_2,
                    `status` = 1,
                    `qty_before` = " . $item['qty'] . ",
                    `qty_after` = " . ($item['qty'] - $item['amount']) . ",
                    `create_id` = :create_id,
                    `created_at` = now()";
            
            // prepare the query
            $stmt = $db->prepare($query);
            
            // bind the values
            $stmt->bindParam(':request_id', $item_id);
            $stmt->bindParam(':code', $code);
            $stmt->bindParam(':qty', $amount);
            $stmt->bindParam(':_action', $act);
            $stmt->bindParam(':act_1', $act_1);
            $stmt->bindParam(':act_2', $act_2);
            $stmt->bindParam(':create_id', $user_id);
            
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
            
            $query = "update office_items_stock set 
                            reserve_qty = reserve_qty - :qty, 
                            qty = qty - :qty,
                            updated_id = :updated_id, 
                            updated_at = now() 
                            where code = :code";
            
            // prepare the query
            $stmt = $db->prepare($query);
            
            // bind the values
            $stmt->bindParam(':code', $code);
            $stmt->bindParam(':qty', $item['amount']);
            $stmt->bindParam(':updated_id', $user_id);
            
            // execute the query, also check if query was successful
            if (!$stmt->execute()) {
                $arr = $stmt->errorInfo();
                error_log($arr[2]);
                $db->rollback();
                http_response_code(501);
                echo json_encode(array("Failure at " . date("Y-m-d") . " " . date("h:i:sa") . " " . "Out of stock, Cannot release!"));
                die();
            }
            
        }
        
        // to complete
        $query = "update apply_for_office_item set 
                        status = " . TO_COMPLETE . ", 
                        updated_id = :updated_id, 
                        updated_at = now() 
                        where id = :id";
        
        // prepare the query
        $stmt = $db->prepare($query);
        
        // bind the values
        $stmt->bindParam(':id', $item_id);
        $stmt->bindParam(':updated_id', $user_id);
        
        // execute the query, also check if query was successful
        if (!$stmt->execute()) {
            $arr = $stmt->errorInfo();
            error_log($arr[2]);
            $db->rollback();
            http_response_code(501);
            echo json_encode(array("Failure at " . date("Y-m-d") . " " . date("h:i:sa") . " " . $arr[2]));
            die();
        }
        
        
        $query = "INSERT INTO office_item_apply_history
            SET
        `request_id` = :request_id,
        `actor` = :actor,
        `action` = :_action,
        `reason` = :remark,
        `status` = 1,
        `created_at` = now()";
        
        // prepare the query
        $stmt = $db->prepare($query);
        
        // bind the values
        $stmt->bindParam(':request_id', $item_id);
        $stmt->bindParam(':actor', $user_name);
        $stmt->bindParam(':_action', $crud);
        $stmt->bindParam(':remark', $remark);
        
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

        $db->commit();
    
        http_response_code(200);
        echo json_encode(array("message" => "Success at " . date("Y-m-d") . " " . date("h:i:sa")));
        
            
    }

function upload_file($item_id, $file, $image_name, $user_id, $db, $conf)
{
$batch_type = "office_item_release";
$key = "myKey";
$time = time();
$hash = hash_hmac('sha256', $time . rand(1, 65536), $key);
$ext = "jpg";
$filename = $time . $hash . "." . $ext;
$storage = new StorageClient([
    'projectId' => 'predictive-fx-284008',
    'keyFilePath' => $conf::$gcp_key
]);
$bucket = $storage->bucket('feliiximg');
$upload_name = time() . '_' . pathinfo($filename, PATHINFO_FILENAME) . '.' . $ext;
//$image_name = "releaser_sig_name.jpg";
$obj = $bucket->upload(
    $file,
    ['name' => $upload_name]);
    $info = $obj->info();
    $size = $info['size'];
    if($size)
    {
        $sig_date = $upload_name;
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
        $stmt->bindParam(':batch_id', $item_id);
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
        }
    }
    else
    {
        $code = 502;
        $message = 'There is an error while uploading file';
        $image = $image_name;
    }
}
            

function UpdateQty($list, $db)
{

    foreach($list as &$item)
    {
        $code = $item['code1'] . $item['code2'] . $item['code3'] . $item['code4'];

        $amount = $item['amount'] * -1;

        $sql = "select qty from office_items_stock where code = '" . $code . "'";
        $stmt = $db->prepare($sql);
        $stmt->execute();
        
        $qty = 0;
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $qty = $row['qty'];

        }

        $item['qty_before'] = $qty;
        $item['qty_after'] = $qty + $amount;
    }

    return $list;
}