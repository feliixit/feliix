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

$info_account = (isset($_POST['info_account']) ?  $_POST['info_account'] : '');
$info_category = (isset($_POST['info_category']) ?  $_POST['info_category'] : '');
$sub_category = (isset($_POST['sub_category']) ?  $_POST['sub_category'] : '');
$info_remark = (isset($_POST['info_remark']) ?  $_POST['info_remark'] : '');

$items_to_delete = (isset($_POST['items_to_delete']) ?  $_POST['items_to_delete'] : "[]");
$items_array = json_decode(stripslashes($items_to_delete),true);

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
        // now you can apply
        if ($crud == "Send To OP" || $crud == "Send To MD") {

            $query = "update apply_for_petty
                   SET
                  `status` =  :status,
                  `updated_at` = now(),
                  `info_account` =  :info_account,
                  `info_category` =  :info_category,
                  `info_sub_category` =  :info_sub_category,
                  `info_remark` =  :info_remark
                   where id = :id ";

            // prepare the query
            $stmt = $db->prepare($query);

            // bind the values
            $stmt->bindParam(':id', $id);
            $stmt->bindParam(':status', GetAction($crud));
            $stmt->bindParam(':info_account', $info_account);
            $stmt->bindParam(':info_category', $info_category);
            $stmt->bindParam(':info_sub_category', $sub_category);
            $stmt->bindParam(':info_remark', $info_remark);
        } elseif ($crud == "Liquidating") {
            $query = "update apply_for_petty
                   SET
                  `status` =  :status,
                  `updated_at` = now(),
                  `amount_liquidated` =  :amount_liquidated
                   where id = :id ";

            // prepare the query
            $stmt = $db->prepare($query);

            // bind the values
            $stmt->bindParam(':id', $id);
            $stmt->bindParam(':status', GetAction($crud));
            $stmt->bindParam(':amount_liquidated', $amount);
        } elseif ($crud == "Verifier Verified") {
            $query = "update apply_for_petty
                   SET
                  `status` =  :status,
                  `updated_at` = now(),
                  `amount_verified` =  :amount_verified
                   where id = :id ";

            // prepare the query
            $stmt = $db->prepare($query);

            // bind the values
            $stmt->bindParam(':id', $id);
            $stmt->bindParam(':status', GetAction($crud));
            $stmt->bindParam(':amount_verified', $amount);
        } else {
            $query = "update apply_for_petty
                   SET
                  `status` =  :status,
                  `updated_at` = now()
                   where id = :id ";

            // prepare the query
            $stmt = $db->prepare($query);

            // bind the values
            $stmt->bindParam(':id', $id);
            $stmt->bindParam(':status', GetAction($crud));
        }
        try {
            // execute the query, also check if query was successful
            if (!$stmt->execute()) {
                $arr = $stmt->errorInfo();
                error_log($arr[2]);
                $db->rollback();
                http_response_code(501);
                echo json_encode(array($arr[2]));
                die();
            }
        } catch (Exception $e) {
            error_log($e->getMessage());
            $db->rollback();
            http_response_code(501);
            echo json_encode(array($e->getMessage()));
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
                    echo json_encode(array($arr[2]));
                    die();
                }
            } catch (Exception $e) {
                error_log($e->getMessage());
                $db->rollback();
                http_response_code(501);
                echo json_encode(array($e->getMessage()));
                die();
            }
        }

        $batch_id = $id;
        $batch_type = $crud;

        if(isset($_FILES['files']['name']))
        {
            try {
                $total = count($_FILES['files']['name']);
                // Loop through each file
                for ($i = 0; $i < $total; $i++) {

                    if (isset($_FILES['files']['name'][$i])) {
                        $image_name = $_FILES['files']['name'][$i];
                        $valid_extensions = array("jpg", "jpeg", "png", "gif", "pdf", "docx", "doc", "xls", "xlsx", "ppt", "pptx", "zip", "rar", "7z", "txt", "dwg", "skp", "psd", "evo");
                        $extension = pathinfo($image_name, PATHINFO_EXTENSION);
                        if (in_array(strtolower($extension), $valid_extensions)) {
                            //$upload_path = 'img/' . time() . '.' . $extension;

                            $storage = new StorageClient([
                                'projectId' => 'predictive-fx-284008',
                                'keyFilePath' => $conf::$gcp_key
                            ]);

                            $bucket = $storage->bucket('feliiximg');

                            $upload_name = time() . '_' . pathinfo($image_name, PATHINFO_FILENAME) . '.' . $extension;

                            if ($bucket->upload(
                                fopen($_FILES['files']['tmp_name'][$i], 'r'),
                                ['name' => $upload_name]
                            )) {
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
                                $stmt->bindParam(':batch_type', GetDesc($batch_type));
                                $stmt->bindParam(':filename', $image_name);
                                $stmt->bindParam(':gcp_name', $upload_name);

                                $stmt->bindParam(':create_id', $user_id);

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
                                    $db->rollback();
                                    http_response_code(501);
                                    echo json_encode(array($e->getMessage()));
                                    die();
                                }


                                $message = 'Uploaded';
                                $code = 0;
                                $upload_id = $last_id;
                                $image = $image_name;
                            } else {
                                $message = 'There is an error while uploading file';
                            }
                        } else {
                            $message = 'Only Images or Office files allowed to upload';
                        }
                    }
                }
            } catch (Exception $e) {
                $db->rollback();
                http_response_code(501);
                echo json_encode(array("Error uploading, Please use laptop to upload again."));
                die();
            }
        }

        // save history
        $query = "INSERT INTO petty_history
        SET
            `petty_id` = :petty_id,
            `actor` = :actor,
            `action` = :_action,
            `reason` = :remark,
            `status` = 1,
            `created_at` = now()";

        // prepare the query
        $stmt = $db->prepare($query);

        // bind the values
        $stmt->bindParam(':petty_id', $id);
        $stmt->bindParam(':actor', $user_name);
        $stmt->bindParam(':_action', GetDesc($crud));
        $stmt->bindParam(':remark', $remark);

        try {
            // execute the query, also check if query was successful
            if (!$stmt->execute()) {
                $arr = $stmt->errorInfo();
                error_log($arr[2]);
                $db->rollback();
                http_response_code(501);
                echo json_encode(array($arr[2]));
                die();
            }
        } catch (Exception $e) {
            error_log($e->getMessage());
            $db->rollback();
            http_response_code(501);
            echo json_encode(array($e->getMessage()));
            die();
        }


        $db->commit();
        http_response_code(200);
        echo json_encode(array("message" => $crud . " Success at " . date("Y-m-d") . " " . date("h:i:sa")));
    } catch (Exception $e) {

        error_log($e->getMessage());
        $db->rollback();
        http_response_code(501);
        echo json_encode(array($e->getMessage()));
        die();
    }
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
        case "Liquidating":
            $location = 8;
            break;
        case "Finish Releasing":
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
        case "Send To MD":
            $location = "Checker Checked";
            break;
        case "OP Send To MD":
            $location = "OP Approved";
            break;
        case "OP Review Reject To User":
            $location = "OP Rejected ";
            break;
        case "OP Review Reject To Checker":
            $location = "OP Rejected ";
            break;
        case "MD Review Reject To User":
            $location = "MD Rejected ";
            break;
        case "MD Review Reject To Checker":
            $location = "MD Rejected ";
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
            $location = "Releasing";
            break;
    }

    return $location;
}
