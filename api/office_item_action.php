<?php
error_reporting(E_ALL);
//header("Access-Control-Allow-Origin: https://feliix.myvnc.com");
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: POST");
header("Access-Control-Max-Age: 3600");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

$jwt = (isset($_POST['jwt']) ?  $_POST['jwt'] : null);
$id = (isset($_POST['id']) ?  $_POST['id'] : '');
$crud = (isset($_POST['crud']) ?  $_POST['crud'] : '');
$remark = (isset($_POST['remark']) ?  $_POST['remark'] : '');
$amount = (isset($_POST['amount']) ?  $_POST['amount'] : 0);
$list = (isset($_POST['list']) ?  $_POST['list'] : "[]");
$list_array = json_decode($list,true);

$info_account = (isset($_POST['info_account']) ?  $_POST['info_account'] : '');
$info_category = (isset($_POST['info_category']) ?  $_POST['info_category'] : '');
$sub_category = (isset($_POST['sub_category']) ?  $_POST['sub_category'] : '');
$info_remark = (isset($_POST['info_remark']) ?  $_POST['info_remark'] : '');
$info_remark_other = (isset($_POST['info_remark_other']) ?  $_POST['info_remark_other'] : '');

$items_to_delete = (isset($_POST['items_to_delete']) ?  $_POST['items_to_delete'] : "[]");
$items_array = json_decode($items_to_delete,true);

$amount_of_return = (isset($_POST['amount_of_return']) ?  $_POST['amount_of_return'] : "");
$method_of_return = (isset($_POST['method_of_return']) ?  $_POST['method_of_return'] : "");
$total_amount_liquidate = (isset($_POST['total_amount_liquidate']) ?  $_POST['total_amount_liquidate'] : "");
$petty = (isset($_POST['items']) ?  $_POST['items'] : "[]");
$petty_array = json_decode($petty,true);

$request_no = (isset($_POST['request_no']) ?  $_POST['request_no'] : '');


include_once 'config/core.php';
include_once 'libs/php-jwt-master/src/BeforeValidException.php';
include_once 'libs/php-jwt-master/src/ExpiredException.php';
include_once 'libs/php-jwt-master/src/SignatureInvalidException.php';
include_once 'libs/php-jwt-master/src/JWT.php';

include_once 'config/database.php';
include_once 'objects/apply_for_leave.php';
include_once 'objects/leave.php';
include_once 'config/conf.php';
require_once '../vendor/autoload.php';

include_once 'mail.php';

$database = new Database();
$db = $database->getConnection();
$db->beginTransaction();
$conf = new Conf();

use \Firebase\JWT\JWT;
use Google\Cloud\Storage\StorageClient;

const TO_WITHDRAW = -1;
const TO_REJECT = 2;
const TO_VOID = 3;
const TO_APPROVE = 4;
const TO_RELEASE = 5;
const TO_COMPLETE = 6;

if (!isset($jwt)) {
    http_response_code(401);

    echo json_encode(array("message" => "Access denied."));
    die();
} else {
    try {
        // decode jwt
        $decoded = JWT::decode($jwt, $key, array('HS256'));

        $user_id = $decoded->data->id;
        $apartment_id = $decoded->data->apartment_id;

        $user_name = $decoded->data->username;
        $user_department = $decoded->data->department;

        $uid = $user_id;

        // approve approval
        if ($crud == "Approver Approved") {
            
            // to release
            $query = "update apply_for_office_item set 
                        status = " . TO_RELEASE . ", 
                        updated_id = :updated_id, 
                        updated_at = now() 
                        where id = :id";

            // prepare the query
            $stmt = $db->prepare($query);

            // bind the values
            $stmt->bindParam(':id', $id);
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

        }

        // for reject
        if ($crud == "Approver Reject" || $crud == "Releaser Rejected") {
            foreach($list_array as $item)
            {
                $code = $item['code1'] . $item['code2'] . $item['code3'] . $item['code4'];
                $amount = $item['amount'] * -1;
                // // office_stock_history
                // $query = "INSERT INTO office_stock_history
                // SET
                //     `request_id` = :request_id,
                //     `code` = :code,
                //     `reserve_qty` = :qty,
                //     `action` = :_action,
                //     `status` = 1,
                //     `create_id` = :create_id,
                //     `created_at` = now()";
    
                // // prepare the query
                // $stmt = $db->prepare($query);
    
                // // bind the values
                // $stmt->bindParam(':request_id', $id);
                // $stmt->bindParam(':code', $code);
                // $stmt->bindParam(':qty', $amount);
                // $stmt->bindParam(':_action', $crud);
                // $stmt->bindParam(':create_id', $user_id);
    
                // try {
                //     // execute the query, also check if query was successful
                //     if (!$stmt->execute()) {
                //         $arr = $stmt->errorInfo();
                //         error_log($arr[2]);
                //         $db->rollback();
                //         http_response_code(501);
                //         echo json_encode(array("Failure at " . date("Y-m-d") . " " . date("h:i:sa") . " " . $arr[2]));
                //         die();
                //     }
                // } catch (Exception $e) {
                //     error_log($e->getMessage());
                //     $db->rollback();
                //     http_response_code(501);
                //     echo json_encode(array("Failure at " . date("Y-m-d") . " " . date("h:i:sa") . " " . $e->getMessage()));
                //     die();
                // }

                $query = "update office_items_stock set 
                            reserve_qty = reserve_qty - :qty, 
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
                    echo json_encode(array("Failure at " . date("Y-m-d") . " " . date("h:i:sa") . " " . $arr[2]));
                    die();
                }

            }

            // to reject
            $query = "update apply_for_office_item set 
                        status = " . TO_REJECT . ", 
                        updated_id = :updated_id, 
                        updated_at = now() 
                        where id = :id";

            // prepare the query
            $stmt = $db->prepare($query);

            // bind the values
            $stmt->bindParam(':id', $id);
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

        }

        
        // for void
        if ($crud == "Releaser Voided") {

            $item = UpdateQty($item, $db);

            foreach($list_array as $item)
            {
                $code = $item['code1'] . $item['code2'] . $item['code3'] . $item['code4'];
                $amount = $item['amount'] * -1;
                // office_stock_history
                $query = "INSERT INTO office_stock_history
                SET
                    `request_id` = :request_id,
                    `code` = :code,
                    `reserve_qty` = :qty,
                    `action` = :_action,
                    `status` = 1,
                    `qty_before` = " . $item['qty_before'] . ",
                    `qty_after` = " . ($item['qty_before']) . ",
                    `create_id` = :create_id,
                    `created_at` = now()";
    
                // prepare the query
                $stmt = $db->prepare($query);
    
                // bind the values
                $stmt->bindParam(':request_id', $id);
                $stmt->bindParam(':code', $code);
                $stmt->bindParam(':qty', $amount);
                $stmt->bindParam(':_action', $crud);
                $stmt->bindParam(':create_id', $user_id);
    
                // try {
                //     // execute the query, also check if query was successful
                //     if (!$stmt->execute()) {
                //         $arr = $stmt->errorInfo();
                //         error_log($arr[2]);
                //         $db->rollback();
                //         http_response_code(501);
                //         echo json_encode(array("Failure at " . date("Y-m-d") . " " . date("h:i:sa") . " " . $arr[2]));
                //         die();
                //     }
                // } catch (Exception $e) {
                //     error_log($e->getMessage());
                //     $db->rollback();
                //     http_response_code(501);
                //     echo json_encode(array("Failure at " . date("Y-m-d") . " " . date("h:i:sa") . " " . $e->getMessage()));
                //     die();
                // }

                $query = "update office_items_stock set 
                            reserve_qty = reserve_qty - :qty, 
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
                    echo json_encode(array("Failure at " . date("Y-m-d") . " " . date("h:i:sa") . " " . $arr[2]));
                    die();
                }

            }

            // to reject
            $query = "update apply_for_office_item set 
                        status = " . TO_VOID . ", 
                        updated_id = :updated_id, 
                        updated_at = now() 
                        where id = :id";

            // prepare the query
            $stmt = $db->prepare($query);

            // bind the values
            $stmt->bindParam(':id', $id);
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

        }

        // for withdraw
        if ($crud == "Withdraw") {
            foreach($list_array as $item)
            {
                $code = $item['code1'] . $item['code2'] . $item['code3'] . $item['code4'];
                $amount = $item['amount'] * -1;
                // // office_stock_history
                // $query = "INSERT INTO office_stock_history
                // SET
                //     `request_id` = :request_id,
                //     `code` = :code,
                //     `reserve_qty` = :qty,
                //     `action` = :_action,
                //     `status` = 1,
                //     `create_id` = :create_id,
                //     `created_at` = now()";
    
                // // prepare the query
                // $stmt = $db->prepare($query);
    
                // // bind the values
                // $stmt->bindParam(':request_id', $id);
                // $stmt->bindParam(':code', $code);
                // $stmt->bindParam(':qty', $amount);
                // $stmt->bindParam(':_action', $crud);
                // $stmt->bindParam(':create_id', $user_id);
    
                // try {
                //     // execute the query, also check if query was successful
                //     if (!$stmt->execute()) {
                //         $arr = $stmt->errorInfo();
                //         error_log($arr[2]);
                //         $db->rollback();
                //         http_response_code(501);
                //         echo json_encode(array("Failure at " . date("Y-m-d") . " " . date("h:i:sa") . " " . $arr[2]));
                //         die();
                //     }
                // } catch (Exception $e) {
                //     error_log($e->getMessage());
                //     $db->rollback();
                //     http_response_code(501);
                //     echo json_encode(array("Failure at " . date("Y-m-d") . " " . date("h:i:sa") . " " . $e->getMessage()));
                //     die();
                // }

                $query = "update office_items_stock set 
                            reserve_qty = reserve_qty - :qty, 
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
                    echo json_encode(array("Failure at " . date("Y-m-d") . " " . date("h:i:sa") . " " . $arr[2]));
                    die();
                }

            }

            // to withdraw
            $query = "update apply_for_office_item set 
                        status = " . TO_WITHDRAW . ", 
                        updated_id = :updated_id, 
                        updated_at = now() 
                        where id = :id";

            // prepare the query
            $stmt = $db->prepare($query);

            // bind the values
            $stmt->bindParam(':id', $id);
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

        }

        // for Released
        if ($crud == "Releaser released") {
            
            $list_array = UpdateQty($list_array, $db);

            foreach($list_array as $item)
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
                    `qty_before` = " . $item['qty_before'] . ",
                    `qty_after` = " . ($item['qty_before'] - $item['amount']) . ",
                    `create_id` = :create_id,
                    `created_at` = now()";
    
                // prepare the query
                $stmt = $db->prepare($query);
    
                // bind the values
                $stmt->bindParam(':request_id', $id);
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
            $stmt->bindParam(':id', $id);
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

            $batch_id = $id;
            $batch_type = "office_item_release";

            try {
                $total = count($_FILES['files']['name']);
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


        }

        // save history
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
        $stmt->bindParam(':request_id', $id);
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

        // Send Mail
        //SendNotifyMail($id, $crud);

        http_response_code(200);
        echo json_encode(array("message" => "Success at " . date("Y-m-d") . " " . date("h:i:sa")));
    } catch (Exception $e) {

        error_log($e->getMessage());
        $db->rollback();
        http_response_code(501);
        echo json_encode(array("Failure at " . date("Y-m-d") . " " . date("h:i:sa") . " " . $e->getMessage()));
        die();
    }
}

function SendNotifyMail($id, $action)
{
    $requestor = "";
    $requestor_email = "";
    $request_no = "";
    $applicant = "";
    $department = "";
    $application_Time = "";
    $project_name = "";
    $project_name1 = "";
    $date_request = "";
    $total_amount = "";
    $reject_reason = "";

    $date_release = "";
    $date_liquidate = "";
    $liquidate_amount = "";
    $remarks = "";

    $info_account = "";

    $notifior = array();

    $database = new Database();
    $db = $database->getConnection();

    $_record = GetPettyDetail($id, $db);
    $requestor = $_record[0]["username"];
    $requestor_email = $_record[0]["email"];
    $request_no = $_record[0]["request_no"];
    $applicant = $_record[0]["username"];
    $department = $_record[0]["department"];
    $application_Time = str_replace("-", "/", $_record[0]["created_at"]);
    $project_name = $_record[0]["project_name"];
    $project_name1 = $_record[0]["project_name1"];
    $date_request = $_record[0]["date_requested"];
    $total_amount = $_record[0]["total"];

    $liquidate_amount = $_record[0]["amount_liquidated"];
    $remarks = $_record[0]["remark_liquidated"];

    $info_account = $_record[0]["info_account"];

    switch ($action) {
        case "Checking Reject":
            $reject_reason = GetRejectReason($id, GetDesc($action), $db); 
            reject_expense_mail($request_no, $applicant, $requestor, $requestor_email, $department, $application_Time, $project_name1, $project_name, $date_request, $total_amount, $reject_reason, $action);
            break;
        case "Send To OP":
            $notifior = GetNotifyer(2, $db);
            foreach($notifior as &$list)
            {
                send_expense_mail($request_no,  $applicant, $list["username"], $list["email"], $department, $application_Time, $project_name1, $project_name, $date_request, $total_amount, $action);
            }
            break;
        case "Send To OP ONLY":
            $notifior = GetNotifyer(2, $db);
            foreach($notifior as &$list)
            {
                send_expense_mail($request_no,  $applicant, $list["username"], $list["email"], $department, $application_Time, $project_name1, $project_name, $date_request, $total_amount, $action);
            }
            break;
        case "Send To MD":
            $notifior = GetNotifyer(3, $db);
            foreach($notifior as &$list)
            {
                send_expense_mail($request_no,  $applicant, $list["username"], $list["email"], $department, $application_Time, $project_name1, $project_name, $date_request, $total_amount, $action);
            }
            break;
        case "OP Send To MD":
            $notifior = GetNotifyer(3, $db);
            foreach($notifior as &$list)
            {
                send_expense_mail($request_no,  $applicant, $list["username"], $list["email"], $department, $application_Time, $project_name1, $project_name, $date_request, $total_amount, $action);
            }
            break;
        case "OP Review Reject To User":
            $reject_reason = GetRejectReason($id, GetDesc($action), $db); 

            reject_expense_mail($request_no, $applicant, $requestor, $requestor_email, $department, $application_Time, $project_name1, $project_name, $date_request, $total_amount, $reject_reason, $action);
            break;
        case "OP Review Reject To Checker":
            $reject_reason = GetRejectReason($id, GetDesc($action), $db); 
            $notifior = GetNotifyer(2, $db);
            foreach($notifior as &$list)
            {
                reject_expense_mail($request_no,  $applicant, $list["username"], $list["email"], $department, $application_Time, $project_name1, $project_name, $date_request, $total_amount, $reject_reason, $action);
            }
            break;
        case "MD Review Reject To User":
            $reject_reason = GetRejectReason($id, GetDesc($action), $db);
            reject_expense_mail($request_no, $applicant, $requestor, $requestor_email, $department, $application_Time, $project_name1, $project_name, $date_request, $total_amount, $reject_reason, $action);
            break;
        case "MD Review Reject To Checker":
            $reject_reason = GetRejectReason($id, GetDesc($action), $db); 
            $notifior = GetNotifyer(2, $db);
            foreach($notifior as &$list)
            {
                reject_expense_mail($request_no,  $applicant, $list["username"], $list["email"], $department, $application_Time, $project_name1, $project_name, $date_request, $total_amount, $reject_reason, $action);
            }
            break;
        case "MD Send To Releaser":
            $notifior = GetReleasers($db, $info_account);
            foreach($notifior as &$list)
            {
                send_expense_mail($request_no,  $applicant, $list["username"], $list["email"], $department, $application_Time, $project_name1, $project_name, $date_request, $total_amount, $action);
            }
            break;
        case "Releasing":
            $location = 6;
            break;
        case "Liquidated":
            $date_release = GetReleaseHistory($id, $db);
            $date_liquidate = GetLiquidateHistory($id, $db);

            $notifior = GetNotifyer(7, $db);
            foreach($notifior as &$list)
            {
                send_liquidate_mail($request_no,  $applicant, $list["username"], $list["email"], $department, $application_Time, $project_name1, $project_name, $date_request, $total_amount, $action, $date_release, $date_liquidate, $liquidate_amount, $remarks);
            }
        case "Finish Releasing":
            $location = 9;
            break;
        case "Finish Releasing PCR":
            $location = 9;
            break;
        case "Verifier Rejected":
            $location = 7;
            break;
        case "Verifier Verified":
            $location = 9;
            break;
        case "Void":
            $reject_reason = GetRejectReason($id, GetDesc($action), $db); 

            void_expense_mail($request_no, $applicant, $requestor, $requestor_email, $department, $application_Time, $project_name1, $project_name, $date_request, $total_amount, $reject_reason, $action);
            break;
        default:
            return;
            break;
        }
}

function GetReleaseHistory($_id, $db)
{
    $sql = "select DATE_FORMAT(pm.created_at, '%Y/%m/%d') created_at from petty_history pm 
            where `status` <> -1 and petty_id = " . $_id . " and `action` = 'Releaser Released' order by created_at desc limit 1";

    $merged_results = "";

    $stmt = $db->prepare($sql);
    $stmt->execute();

    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        $merged_results = $row['created_at'];
    }

    return $merged_results;
}

function GetLiquidateHistory($_id, $db)
{
    $sql = "select DATE_FORMAT(pm.created_at, '%Y/%m/%d') created_at from petty_history pm 
            where `status` <> -1 and petty_id = " . $_id . " and `action` = 'Liquidated' order by created_at desc limit 1";

    $merged_results = "";

    $stmt = $db->prepare($sql);
    $stmt->execute();

    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        $merged_results = $row['created_at'];
    }

    return $merged_results;
}

function GetPettyDetail($id, $db)
{
    $sql = "SELECT request_no, project_name1, project_name, u.username, u.email, ud.department, ap.created_at, ap.date_requested, 
            (SELECT SUM(price * qty) FROM petty_list WHERE petty_id = :id1) total, ap.amount_liquidated, ap.remark_liquidated, ap.info_account
            FROM apply_for_petty ap 
            LEFT JOIN user u ON ap.uid = u.id 
            left JOIN user_department ud ON ud.id = u.apartment_id
            where ap.id = :id";

    $merged_results = array();

    $stmt = $db->prepare($sql);
    $stmt->bindParam(':id1',  $id);
    $stmt->bindParam(':id',  $id);
    $stmt->execute();

    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        $merged_results[] = $row;
    }

    return $merged_results;
}

function GetRejectReason($_id, $action, $db)
{
    $sql = "select reason from petty_history pm 
            where `status` <> -1 and petty_id = :id and `action` = :action order by created_at desc limit 1";

    $merged_results = "";

    $stmt = $db->prepare($sql);
    $stmt->bindParam(':id',  $_id);
    $stmt->bindParam(':action',  $action);
    $stmt->execute();

    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        $merged_results = $row['reason'];
    }

    return $merged_results;
}

function GetNotifyer($action, $db)
{
    $sql = "SELECT username, email FROM expense_flow ap
    LEFT JOIN user u ON ap.uid = u.id 
    WHERE flow in (:action)";

    $merged_results = array();

    $stmt = $db->prepare($sql);
    $stmt->bindParam(':action',  $action);
    $stmt->execute();

    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        $merged_results[] = $row;
    }

    return $merged_results;
}

function GetReleasers($db, $info_account)
{
    $flow = 0;

    if($info_account == 'Office Petty Cash')
        $flow = 4;
    if($info_account == 'Online Transactions')
        $flow = 5;
    if($info_account == 'Security Bank')
        $flow = 6;
    $sql = "SELECT username, email FROM expense_flow ap
    LEFT JOIN user u ON ap.uid = u.id 
    WHERE flow = " . $flow;

    $merged_results = array();

    $stmt = $db->prepare($sql);
    $stmt->execute();

    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        $merged_results[] = $row;
    }

    return $merged_results;
}

function &GetAction($loc)
{
    $location = "";
    switch ($loc) {
        case "Revise":
            $location = 1;
            break;
        case "Withdraw":
            $location = -1;
            break;
        case "Checking Reject":
            $location = 0;
            break;
        case "Send To OP":
            $location = 3;
            break;
        case "Send To OP ONLY":
            $location = -3;
            break;
        case "OP Send To MD":
            $location = 4;
            break;
        case "Send To MD":
            $location = 4;
            break;
        case "OP Review Reject To User":
            $location = 0;
            break;
        case "OP Review Reject To Checker":
            $location = 2;
            break;
        case "MD Review Reject To User":
            $location = 0;
            break;
        case "MD Review Reject To Checker":
            $location = 2;
            break;
        case "MD Send To Releaser":
            $location = 5;
            break;
        case "Releasing":
            $location = 6;
            break;
        case "Liquidated":
            $location = 8;
            break;
        case "Finish Releasing":
            $location = 9;
            break;
        case "Finish Releasing PCR":
            $location = 9;
            break;
        case "Verifier Rejected":
            $location = 7;
            break;
        case "Verifier Verified":
            $location = 9;
            break;
        case "Void":
            $location = -2;
            break;
    }

    return $location;
}

function &GetDesc($loc)
{
    $location = $loc;
    switch ($loc) {
        case "Withdraw":
            $location = "Withdrew";
            break;
        case "Checking Reject":
            $location = "Checker Rejected";
            break;
        case "Send To OP":
            $location = "Checker Checked";
            break;
        case "Send To OP ONLY":
            $location = "Checker Checked";
            break;
        case "Send To MD":
            $location = "Checker Checked";
            break;
        case "OP Send To MD":
            $location = "OP Approved";
            break;
        case "OP Review Reject To User":
            $location = "OP Rejected";
            break;
        case "OP Review Reject To Checker":
            $location = "OP Rejected";
            break;
        case "MD Review Reject To User":
            $location = "MD Rejected";
            break;
        case "MD Review Reject To Checker":
            $location = "MD Rejected";
            break;
        case "MD Send To Releaser":
            $location = "MD Approved";
            break;
        case "Releasing":
            $location = "Releaser Released";
            break;
        case "Void":
            $location = "Releaser Voided";
            break;
        case "Finish Releasing":
            $location = "Releaser Released";
            break;
        case "Finish Releasing PCR":
            $location = "Releaser Released";
            break;
    }

    return $location;
}

function GetRecordDetail($_id, $db)
{
    $sql = "SELECT ap.request_type, ap.id, request_no, project_name1, project_name, info_account, info_category, info_sub_category, u.username, amount_verified, rtype, dept_name
            FROM apply_for_petty ap 
            LEFT JOIN user u ON ap.uid = u.id 
            where ap.id = :id";

    $merged_results = array();

    $stmt = $db->prepare($sql);
    $stmt->bindParam(':id',  $_id);
    $stmt->execute();

    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        $merged_results[] = $row;
    }

    return $merged_results;
}


function GetReleaseAttachments($_id, $db)
{
    $sql = "select id, 1 is_checked, COALESCE(h.filename, '') filename, COALESCE(h.gcp_name, '') gcp_name
            from gcp_storage_file h where h.batch_id = " . $_id . " AND h.batch_type = 'Releaser Released'
            order by h.created_at ";

    $merged_results = array();

    $stmt = $db->prepare($sql);
    $stmt->execute();

    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        $merged_results[] = $row;
    }

    return $merged_results;
}

function GetLiquidateAttachments($_id, $db)
{
    $sql = "select id, 1 is_checked, COALESCE(h.filename, '') filename, COALESCE(h.gcp_name, '') gcp_name
            from gcp_storage_file h where h.batch_id = " . $_id . " AND h.batch_type = 'Liquidated'
            order by h.created_at ";

    $merged_results = array();

    $stmt = $db->prepare($sql);
    $stmt->execute();

    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        $merged_results[] = $row;
    }

    return $merged_results;
}

function GetAttachments($_id, $db)
{
    $sql = "select id, 1 is_checked, COALESCE(h.filename, '') filename, COALESCE(h.gcp_name, '') gcp_name
            from gcp_storage_file h where h.batch_id = " . $_id . " AND h.batch_type = 'petty'
            order by h.created_at ";

    $merged_results = array();

    $stmt = $db->prepare($sql);
    $stmt->execute();

    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        $merged_results[] = $row;
    }

    return $merged_results;
}

function GetList($_id, $db)
{
    $sql = "select pm.id, sn, payee, particulars, price, qty, `status`
    from petty_list pm 
    where `status` <> -1 and petty_id = " . $_id . " order by sn ";

    $merged_results = array();

    $stmt = $db->prepare($sql);
    $stmt->execute();

    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        $merged_results[] = $row;
    }

    return $merged_results;
}

function GetDepartment($dept_name)
{
    $department = "";

    if($dept_name == 'admin')
        $department = 'Admin Department';

    if($dept_name == 'design')
        $department = 'Design Department';

    if($dept_name == 'engineering')
        $department = 'Engineering Department';

    if($dept_name == 'lighting')
        $department = 'Lighting Department';
    
    if($dept_name == 'office')
        $department = 'Office Department';
    
    if($dept_name == 'sales')
        $department = 'Sales Department';

    return $department;
}

function update_apply_for_petty_liquidate($petty_array, $db, $id, $user_id)
{
    // delete previous -1 records
    $query = "DELETE FROM apply_for_petty_liquidate WHERE petty_id = :id AND `status` = -1";

    // prepare the query
    $stmt = $db->prepare($query);

    // bind the values
    $stmt->bindParam(':id', $id);

    // execute the query, also check if query was successful
    if (!$stmt->execute()) {
        $arr = $stmt->errorInfo();
        error_log($arr[2]);
        $db->rollback();
        http_response_code(501);
        echo json_encode(array("Failure at " . date("Y-m-d") . " " . date("h:i:sa") . " " . $arr[2]));
        die();
    }

    // update previous records to status -1
    $query = "update apply_for_petty_liquidate set `status` = -1 where petty_id = :id  ";

    // prepare the query
    $stmt = $db->prepare($query);

    // bind the values
    $stmt->bindParam(':id', $id);

    // execute the query, also check if query was successful
    if (!$stmt->execute()) {
        $arr = $stmt->errorInfo();
        error_log($arr[2]);
        $db->rollback();
        http_response_code(501);
        echo json_encode(array("Failure at " . date("Y-m-d") . " " . date("h:i:sa") . " " . $arr[2]));
        die();
    }

    // insert new record
    for($i = 0; $i < count($petty_array); $i++)
    {
        $query = "INSERT INTO apply_for_petty_liquidate
        SET
            `petty_id` = :id,
            `sn` = :sn,
            `vendor` = :payee,
            `particulars` = :particulars,
            `price` = :price,
            `qty` = :qty,
            `status` = 1,
            `created_at` = now(),
            `create_id` = :created_by";

        // prepare the query
        $stmt = $db->prepare($query);

        // bind the values
        $stmt->bindParam(':id', $id);
        $stmt->bindParam(':sn', $i);
        $stmt->bindParam(':payee', $petty_array[$i]["payee"]);
        $stmt->bindParam(':particulars', $petty_array[$i]["particulars"]);
        $stmt->bindParam(':price', $petty_array[$i]["price"]);
        $stmt->bindParam(':qty', $petty_array[$i]["qty"]);
        $stmt->bindParam(':created_by', $user_id);

        // execute the query, also check if query was successful
        if (!$stmt->execute()) {
            $arr = $stmt->errorInfo();
            error_log($arr[2]);
            $db->rollback();
            http_response_code(501);
            echo json_encode(array("Failure at " . date("Y-m-d") . " " . date("h:i:sa") . " " . $arr[2]));
            die();
        }
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