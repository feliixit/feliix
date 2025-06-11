<?php
error_reporting(E_ALL);
//header("Access-Control-Allow-Origin: https://feliix.myvnc.com");
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: POST");
header("Access-Control-Max-Age: 3600");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

$jwt = (isset($_POST['jwt']) ?  $_POST['jwt'] : null);
$request_no = (isset($_POST['request_no']) ?  $_POST['request_no'] : '');
$date_requested = (isset($_POST['date_requested']) ?  $_POST['date_requested'] : '');
$reason = (isset($_POST['reason']) ?  $_POST['reason'] : '');

$remark = (isset($_POST['remark']) ?  $_POST['remark'] : '');

$petty_list = (isset($_POST['petty_list']) ?  $_POST['petty_list'] : '[]');
$array_list = json_decode($petty_list, true);

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

const FOR_APPROVE = 4;
const FOR_RELEASE = 5;

if ( !isset( $jwt ) ) {
    http_response_code(401);

    echo json_encode(array("message" => "Failure at " . date("Y-m-d") . " " . date("h:i:sa") . " Access denied."));
    die();
}
else
{
    try {
        // decode jwt
        $decoded = JWT::decode($jwt, $key, array('HS256'));

        $user_id = $decoded->data->id;
        $apartment_id = $decoded->data->apartment_id;

        $user_name = $decoded->data->username;
        $user_department = $decoded->data->department;

        
        // now you can apply
        $uid = $user_id;
    
        $query = "INSERT INTO apply_for_office_item
        SET
            `uid` = :uid,
            `request_no` = :request_no,
            `date_requested` = :date_requested,
            `reason` = :reason,
            `remarks` = :remark,
            `listing` = :listing,
            `status` = :status,
            `create_id` = :create_id,
            `created_at` = now()";

        // prepare the query
        $stmt = $db->prepare($query);

        $status = CheckItemsForStatus($array_list);

        // bind the values
        $stmt->bindParam(':uid', $uid);
        $stmt->bindParam(':request_no', $request_no);
        $stmt->bindParam(':date_requested', $date_requested);
        $stmt->bindParam(':reason', $reason);
        $stmt->bindParam(':listing', $petty_list);
        $stmt->bindParam(':status', $status);
        $stmt->bindParam(':remark', $remark);
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

        if($last_id != 0)
        {
            // update new request no
            $new_request_no = sprintf("%05d", $last_id);

            $query = "update apply_for_office_item
                SET
                    `request_no` = :request_no
                    where id = :id";

            // prepare the query
            $stmt = $db->prepare($query);

            // bind the values
            $stmt->bindParam(':request_no', $new_request_no);
 
            $stmt->bindParam(':id', $last_id);

            // execute the query, also check if query was successful
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

        $batch_id = $last_id;
        $batch_type = "apply_office_item";

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

        // save history
        foreach ($array_list as $item)
        {
            $code = $item['code1'] . $item['code2'] . $item['code3'] . $item['code4'];

            // $query = "INSERT INTO office_stock_history
            // SET
            //     `request_id` = :request_id,
            //     `code` = :code,
            //     `reserve_qty` = :qty,
            //     `action` = 'APPLY',
            //     `status` = 1,
            //     `create_id` = :create_id,
            //     `created_at` = now()";
    
            // // prepare the query
            // $stmt = $db->prepare($query);
    
            // // bind the values
            // $stmt->bindParam(':request_id', $batch_id);
            // $stmt->bindParam(':code', $code);
            // $stmt->bindParam(':qty', $item['amount']);
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

            $query = "update office_items_stock
            SET
                `reserve_qty` = `reserve_qty` + " . $item['amount'] . "
                where code = :code";
    
            // prepare the query
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

        $crud = "Submitted";

        // bind the values
        $stmt->bindParam(':request_id', $batch_id);
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
        // SendNotifyMail($batch_id);
        
        http_response_code(200);
        echo json_encode(array("message" => "Success at " . date("Y-m-d") . " " . date("h:i:sa") . " <br> Request No. is " . $new_request_no));
        
    }
    catch (Exception $e){

        error_log($e->getMessage());
        $db->rollback();
        http_response_code(501);
        echo json_encode(array("Failure at " . date("Y-m-d") . " " . date("h:i:sa") . $e->getMessage()));
        die();

    }
}

function CheckItemsForStatus($array_list)
{
    $status = FOR_RELEASE;
    foreach ($array_list as $item)
    {
        if($item['cat1'] != "OFFICE SUPPLIES")
        {
            $status = FOR_APPROVE;
            break;
        }
    }
    return $status;
}

function SendNotifyMail($id)
{

    $request_no = "";
    $applicant = "";
    $department = "";
    $application_Time = "";
    $project_name = "";
    $project_name1 = "";
    $date_request = "";
    $total_amount = "";
 

    $notifior = array();

    $database = new Database();
    $db = $database->getConnection();

    $_record = GetPettyDetail($id, $db);

    $request_no = $_record[0]["request_no"];
    $applicant = $_record[0]["username"];
    $department = $_record[0]["department"];
    $application_Time = str_replace("-", "/", $_record[0]["created_at"]);
    $project_name = $_record[0]["project_name"];
    $project_name1 = $_record[0]["project_name1"];
    $date_request = $_record[0]["date_requested"];
    $total_amount = $_record[0]["total"];

    $notifior = GetNotifyer(1, $db);
    foreach($notifior as &$list)
    {
        send_expense_mail($request_no, $applicant, $list["username"], $list["email"], $department, $application_Time, $project_name1, $project_name, $date_request, $total_amount, "Apply");
    }

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

function GetPettyDetail($id, $db)
{
    $sql = "SELECT request_no, project_name1, project_name, u.username, u.email, ud.department, ap.created_at, ap.date_requested, 
            (SELECT SUM(price * qty) FROM petty_list WHERE petty_id = :id1) total, ap.amount_liquidated, ap.remark_liquidated
            FROM apply_for_office_item ap 
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

function GetStatus($loc)
{
    $location = "";
    switch ($loc) {
        case 3:
            $location = "Void";
            break;
        case -1:
            $location = "Withdrawn";
            break;
        case 2:
            $location = "Rejected";
            break;
        case 4:
            $location = "For Approve";
            break;
        case 5:
            $location = "For Release";
            break;
        case 6:
            $location = "Completed";
            break;
    }

    return $location;
}