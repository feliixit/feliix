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

        $batch_id = 1;
        $query = "select coalesce(max(batch_id) + 1, 1) cnt from project_quotation";

        $stmt = $db->prepare( $query );
        $stmt->execute();

        while($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $batch_id = $row['cnt'];
        }

        $query = "INSERT INTO project_quotation
                SET
                    project_id = :project_id,
                    remark = :remark,
                    batch_id = :batch_id,
                    create_id = :create_id,
                    created_at = now()";
    
        // prepare the query
        $stmt = $db->prepare($query);
    
        // bind the values
        $stmt->bindParam(':project_id', $pid);
        $stmt->bindParam(':remark', $remark);
        $stmt->bindParam(':batch_id', $batch_id);
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
        $batch_type = 'quote';

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
