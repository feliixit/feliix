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
$remark = (isset($_POST['remark']) ?  $_POST['remark'] : '');

include_once 'config/core.php';
include_once 'libs/php-jwt-master/src/BeforeValidException.php';
include_once 'libs/php-jwt-master/src/ExpiredException.php';
include_once 'libs/php-jwt-master/src/SignatureInvalidException.php';
include_once 'libs/php-jwt-master/src/JWT.php';

include_once 'config/database.php';

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
        $pid = (isset($_POST['pid']) ?  $_POST['pid'] : 0);
        $remark = (isset($_POST['remark']) ?  $_POST['remark'] : '');
        $kind = (isset($_POST['kind']) ?  $_POST['kind'] : 0);
        $date_data_submission = (isset($_POST['date_data_submission']) ?  $_POST['date_data_submission'] : '');

        $batch_id = 1;
        $query = "select coalesce(max(batch_id) + 1, 1) cnt from project_client_po";

        $stmt = $db->prepare( $query );
        $stmt->execute();

        while($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $batch_id = $row['cnt'];
        }

        if($kind == 4)
        {
            // delete previous data
            $query = "DELETE FROM project_client_po WHERE project_id = :project_id and kind = 4 and status = -1";
            // prepare the query
            $stmt = $db->prepare($query);
        
            // bind the values
            $stmt->bindParam(':project_id', $pid);

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

            // update status -1
            $query = "update project_client_po set status = -1 WHERE project_id = :project_id and kind = 4";
            // prepare the query
            $stmt = $db->prepare($query);
        
            // bind the values
            $stmt->bindParam(':project_id', $pid);

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

            $query = "update project_main set date_data_submission = :date_data_submission WHERE id = :project_id";
            // prepare the query
            $stmt = $db->prepare($query);
        
            // bind the values
            $stmt->bindParam(':project_id', $pid);
            $stmt->bindParam(':date_data_submission', $date_data_submission);

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

            $remark = $date_data_submission . ". " . $remark;
        }
        



        $query = "INSERT INTO project_client_po
                SET
                    project_id = :project_id,
                    remark = :remark,
                    batch_id = :batch_id,
                    kind = :kind,
                    date_data_submission = :date_data_submission,
                    create_id = :create_id,
                    created_at = now()";
    
        // prepare the query
        $stmt = $db->prepare($query);
    
        // bind the values
        $stmt->bindParam(':project_id', $pid);
        $stmt->bindParam(':remark', $remark);
        $stmt->bindParam(':batch_id', $batch_id);
        $stmt->bindParam(':kind', $kind);
        $stmt->bindParam(':date_data_submission', $date_data_submission);
        $stmt->bindParam(':create_id', $user_id);

        $last_id = 0;

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

            $last_id = $db->lastInsertId();

        } catch (Exception $e) {
            error_log($e->getMessage());
            $db->rollback();
            http_response_code(501);
            echo json_encode(array("Failure at " . date("Y-m-d") . " " . date("h:i:sa") . " " . $e->getMessage()));
            die();
        }

        $batch_id = $last_id;

        if($kind == 1)
            $batch_type = 'client_po';
        if($kind == 2)
            $batch_type = 'client_other';
        if($kind == 3)
            $batch_type = 'client_other';
        if($kind == 4)
            $batch_type = 'client_other';

        $_pic_url = "";
        $_real_url = "";

        if(isset($_FILES['files']['name']))
        {
            try {
                $total = count($_FILES['files']['name']);
                // Loop through each file
                for ($i = 0; $i < $total; $i++) {

                    if (isset($_FILES['files']['name'][$i])) {
                        $image_name = $_FILES['files']['name'][$i];
                        $valid_extensions = array("jpg", "jpeg", "png", "gif", "pdf", "docx", "doc", "xls", "xlsx", "ppt", "pptx", "zip", "rar", "7z", "txt", "dwg", "skp", "psd", "evo","dwf");
                        $extension = pathinfo($image_name, PATHINFO_EXTENSION);
                        if (in_array(strtolower($extension), $valid_extensions)) {
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
                                    } else {
                                        $arr = $stmt->errorInfo();
                                        error_log($arr[2]);
                                    }
                                } catch (Exception $e) {
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

                            } else {
                                $message = 'There is an error while uploading file';
                                $db->rollback();
                                http_response_code(501);
                                echo json_encode(array("Failure at " . date("Y-m-d") . " " . date("h:i:sa") . " " . $message));
                                die();
                            }
                        } else {
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
                echo json_encode(array("Failure at " . date("Y-m-d") . " " . date("h:i:sa") . " " . "Error uploading, Please use laptop to upload again."));
                die();
            }
        }

        $db->commit();

        // SendNotifyMail($batch_id);

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


function SendNotifyMail($bid)
{
    $database = new Database();
    $db = $database->getConnection();

    $sql = "SELECT p.project_name, pm.remark, u.username, u.email, pm.created_at, p.catagory_id, pm.kind, p.special, COALESCE(p.final_amount, 0) final_amount FROM project_proof pm left join user u on u.id = pm.create_id LEFT JOIN project_main p ON p.id = pm.project_id  WHERE pm.id = " . $bid . " and pm.status <> -2 ";

    $stmt = $db->prepare( $sql );
    $stmt->execute();

    $project_name = "";
    $remark = "";
    $leaver = "";
    $subtime = "";
    $email1 = "";
    $category = "";
    $kind = 0;
    $special = "";
    $final_amount = "";

    while($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        $project_name = $row['project_name'];
        $remark = $row['remark'];
        $leaver = $row['username'];
        $subtime = $row['created_at'];
        $email1 = $row['email'];
        $category = $row['catagory_id'];
        $kind = $row['kind'];
        $special = $row['special'];
        $final_amount = $row['final_amount'];
    }

    send_pay_notify_mail_new($leaver, $email1, $leaver, $project_name, $remark, $subtime, $category, $kind, $special, $final_amount, $bid);
}