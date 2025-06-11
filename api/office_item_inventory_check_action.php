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

// include_once 'mail.php';

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
            
            $phase = (isset($_POST['phase']) ?  $_POST['phase'] : '[]');

            $notes = (isset($_POST['notes']) ?  $_POST['notes'] : '');
            $id = (isset($_POST['id']) ?  $_POST['id'] : '0');
            $status = (isset($_POST['status']) ?  $_POST['status'] : '0');
            $stage = (isset($_POST['stage']) ?  $_POST['stage'] : 1);
            $request_no = (isset($_POST['request_no']) ?  $_POST['request_no'] : '');

            if($status == 4)
                $phase = UpdateQty($phase, $db);
            
            try {
                $query = "update office_item_inventory_check
                set ";
if($stage == 1)
{
                $query .= " note_1 = :note_1, ";
}
if($stage == 2)
{
                $query .= " note_2 = :note_1, ";
}
if($stage == 3)
{
                $query .= " note_3 = :note_1, ";
}

if($status == 3)
{
                $query .= " check_id = " . $user_id . ", check_at = now(), ";
}

if($status == 4)
{
                $query .= " approval_id = " . $user_id . ", approval_at = now(), ";
}

if($status == 2 && $stage == 3)
{
                $query .= " check_id = 0, check_at = null, ";
}
                $query .= " phase_1 = :phase_1,
                `status` = :status,
                updated_id = :updated_id,
                updated_at = now()
                where id = :id";
            
                // prepare the query
                $stmt = $db->prepare($query);

                $stmt->bindParam(':note_1', $notes);
                $stmt->bindParam(':phase_1', $phase);
                $stmt->bindParam(':status', $status);
                $stmt->bindParam(':updated_id', $user_id);
                $stmt->bindParam(':id', $id);
                
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

                if($status == 4)
                {
                    update_office_item_qty($id, $request_no, $phase, $db, $user_id);
                    update_office_item_forzen($db);
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

        function update_office_item_forzen($db)
        {
            $frozen = "";
            
            $query = "select count(*) cnt from office_item_inventory_check where status not in (-1, 4)";
            $stmt = $db->prepare($query);
            
            if($stmt->execute()) {
                $row = $stmt->fetch(PDO::FETCH_ASSOC);
                if($row['cnt'] > 0) {
                    $frozen = "Y";
                } else {
                    $frozen = "";
                }
            } else {
                echo json_encode(array("message" => "Failed"));
                die();
            }
            
            $query = "UPDATE access_control SET frozen_office = :frozen";
            $stmt = $db->prepare($query);
            
            $stmt->bindParam(':frozen', $frozen);
            
            if(!$stmt->execute()) {
                echo json_encode(array("message" => "Failed"));
                die();
            }
        }
        

        function update_office_item_qty($request_id, $request_no, $phase, $db, $user_id)
        {
            $phase_array = json_decode($phase, true);

            foreach ($phase_array as $key => $value) {
                $code = $value['code1'] . $value['code2'] . $value['code3'] . $value['code4'];

                $amount = $value['qty'] != "" ? $value['qty'] : 0;
                $qty = isset($value['qty2']) ? $value['qty2'] : $value['qty1'];

                if($qty == "")
                    $qty = $value['qty1'];

                if($amount != $qty)
                {
                    $query = "select * from office_items_stock where code = :code";
                    $stmt = $db->prepare($query);
                    $stmt->bindParam(':code', $code);
                    $stmt->execute();

                    $num = $stmt->rowCount();
                    if ($num == 0) {
                        $query = "insert into office_items_stock (code, qty, create_id, created_at, updated_id, updated_at, status) values (:code, :qty, :updated_id, now(), :updated_id, now(), 1)";
                        $stmt = $db->prepare($query);
                        $stmt->bindParam(':code', $code);
                        $stmt->bindParam(':qty', $qty);
                        $stmt->bindParam(':updated_id', $user_id);
                        $stmt->execute();
                    }
                    else
                    {
                        $query = "update office_items_stock set qty = :qty, updated_id = :updated_id, updated_at = now() where code = :code";
                        $stmt = $db->prepare($query);
                        $stmt->bindParam(':qty', $qty);
                        $stmt->bindParam(':code', $code);
                        $stmt->bindParam(':updated_id', $user_id);
                        $stmt->execute();
                    }

                    $action = 'Set to ' . $qty;
                    $query = "insert into office_stock_history (request_id, code, qty, action, act_1, act_2, create_id, created_at, `status`, qty_before, qty_after) values (:request_id, :code, :qty, 'Inventory Check', '" . $request_no . "', '" . $action . "', :updated_id, now(), 1, " . $value['qty'] . ", " . $qty . ")";
                    $stmt = $db->prepare($query);

                    $diff = $qty - $amount;

                    $stmt->bindParam(':request_id', $request_id);
                    $stmt->bindParam(':code', $code);
                    $stmt->bindParam(':qty', $diff);
                    $stmt->bindParam(':updated_id', $user_id);

                    $stmt->execute();
                }
            }
            
        }

        function UpdateQty($list, $db)
        {
            $phase_array = json_decode($list, true);

            foreach($phase_array as &$item)
            {
                $code = $item['code1'] . $item['code2'] . $item['code3'] . $item['code4'];

                $sql = "select qty from office_items_stock where code = '" . $code . "'";
                $stmt = $db->prepare($sql);
                $stmt->execute();
                
                $qty = 0;
                while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                    $qty = $row['qty'];

                }

                $amount = $item['qty2'] != "" ? $item['qty2'] : $item['qty1'];
                $item['qty_before'] = $qty;
                $item['qty_after'] = $amount;
            }

            return json_encode($phase_array);
        }
        ?>