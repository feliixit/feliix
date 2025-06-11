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
            $notes4 = (isset($_POST['notes4']) ?  $_POST['notes4'] : '');
            $id = (isset($_POST['id']) ?  $_POST['id'] : '0');
            $status = (isset($_POST['status']) ?  $_POST['status'] : '0');
            $stage = (isset($_POST['stage']) ?  $_POST['stage'] : 1);
            $request_no = (isset($_POST['request_no']) ?  $_POST['request_no'] : '');

            $items_to_delete = (isset($_POST['items_to_delete']) ?  $_POST['items_to_delete'] : "[]");
            $items_array = json_decode($items_to_delete, true);

            if($status == 3)
                $phase = UpdateQty($phase, $db);
            
            try {
                $query = "update office_item_inventory_modify
                set ";
if($stage == 1)
{
                $query .= " note_1 = :note_1, ";
                $query .= " note_4 = '" . $notes4 . "', ";
}
if($stage == 2)
{
                $query .= " note_2 = :note_1, ";
}
if($stage == 3)
{
                $query .= " note_3 = :note_1, ";
}

if($status == 2)
{
                $query .= " check_id = " . $user_id . ", check_at = now(), ";
}

if($status == 3)
{
                $query .= " approval_id = " . $user_id . ", approval_at = now(), ";
}

if($status == 1)
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


                $batch_id = $id;
                $batch_type = "office_item_inventory_modify";

                try {
                    // count if there is any file
                    if(isset($_FILES['files']['name']))
                    {
                        $total = count($_FILES['files']['name']);
                    }
                    else
                    {
                        $total = 0;
                    }
                    // Loop through each file
                    for( $i=0 ; $i < $total ; $i++ ) {

                        if(isset($_FILES['files']['name'][$i]))
                        {
                            $image_name = $_FILES['files']['name'][$i];
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

                                $file_size = filesize($_FILES['files']['tmp_name'][$i]);
                                $size = 0;

                                $obj = $bucket->upload(
                                    fopen($_FILES['files']['tmp_name'][$i], 'r'),
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


                                    $message = 'Uploaded';
                                    $code = 0;
                                    $upload_id = $last_id;
                                    $image = $image_name;
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

                    }
                } catch (Exception $e) {
                    $db->rollback();
                    http_response_code(501);
                    echo json_encode(array("Failure at " . date("Y-m-d") . " " . date("h:i:sa") . " Error uploading, Please use laptop to upload again."));
                    die();
                }

                // items to delete
                for ($i = 0; $i < count($items_array); $i++) {
                    $query = "DELETE FROM gcp_storage_file
                        WHERE
                            `id` = :_id";

                    // prepare the query
                    $stmt = $db->prepare($query);

                    // bind the values
                    $stmt->bindParam(':_id', $items_array[$i]);

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
                }


                if($status == 3)
                    update_office_item_qty($id, $request_no, $phase, $db, $user_id);
            
                
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
        

        function update_office_item_qty($request_id, $request_no, $phase, $db, $user_id)
        {
            $phase_array = json_decode($phase, true);

            foreach ($phase_array as $key => $value) {
                $code = $value['code1'] . $value['code2'] . $value['code3'] . $value['code4'];

                $qty = $value['qty2'] != "" ? $value['qty2'] : $value['qty1'];
                $sign = $value['sign2'] != "" ? $value['sign2'] : $value['sign'];

                if($qty == "")
                    $qty = $value['qty1'];

                if($sign == "-")
                    $qty *= -1;

                $query = "select * from office_items_stock where code = :code";
                $stmt = $db->prepare($query);
                $stmt->bindParam(':code', $code);
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

                $qty_before = $value['qty_before'];
                $qty_after = $value['qty_after'];

                $num = $stmt->rowCount();
                if ($num == 0) {
                    $query = "insert into office_items_stock (code, qty, create_id, created_at, updated_id, updated_at, status) values (:code, :qty, :updated_id, now(), :updated_id, now(), 1)";
                    $stmt = $db->prepare($query);
                    $stmt->bindParam(':code', $code);
                    $stmt->bindParam(':qty', $qty);
                    $stmt->bindParam(':updated_id', $user_id);
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
                }
                else
                {
                    $query = "update office_items_stock set qty = qty + :qty, updated_id = :updated_id, updated_at = now() where code = :code";
                    $stmt = $db->prepare($query);
                    $stmt->bindParam(':qty', $qty);
                    $stmt->bindParam(':code', $code);
                    $stmt->bindParam(':updated_id', $user_id);
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
                }

                if($sign == "-")
                    $action = 'Deduct ' . $qty * -1;
                else
                    $action = 'Add ' . $qty;
                $query = "insert into office_stock_history (request_id, code, qty, action, act_1, act_2, create_id, created_at, `status`, qty_before, qty_after) values (:request_id, :code, :qty, 'Inventory Modification', '" . $request_no . "', '" . $action . "', :updated_id, now(), 1, " . $qty_before . ", " . $qty_after . ")";
                $stmt = $db->prepare($query);

                $diff = $qty;

                $stmt->bindParam(':request_id', $request_id);
                $stmt->bindParam(':code', $code);
                $stmt->bindParam(':qty', $diff);
                $stmt->bindParam(':updated_id', $user_id);

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
                $sign = $item['sign2'] != "" ? $item['sign2'] : $item['sign'];

                $item['qty_before'] = $qty;
                if($sign == '+')
                    $item['qty_after'] = $qty + $amount;
                if($sign == '-')
                    $item['qty_after'] = $qty - $amount;
            }

            return json_encode($phase_array);
        }
        ?>