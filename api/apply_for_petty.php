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
$request_type = (isset($_POST['request_type']) ?  $_POST['request_type'] : '');
$project_name = (isset($_POST['project_name']) ?  $_POST['project_name'] : '');
$payable_to = (isset($_POST['payable_to']) ?  $_POST['payable_to'] : '');
$payable_other = (isset($_POST['payable_other']) ?  $_POST['payable_other'] : '');
$remark = (isset($_POST['remark']) ?  $_POST['remark'] : '');

$petty_list = (isset($_POST['petty_list']) ?  $_POST['petty_list'] : '');
$petty_array = json_decode(stripslashes($petty_list),true);

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
    
        $query = "INSERT INTO apply_for_petty
        SET
            `uid` = :uid,
            `request_no` = :request_no,
            `date_requested` = :date_requested,
            `request_type` = :request_type,
            `project_name` = :project_name,
            `payable_to` = :payable_to,
            `payable_other` = :payable_other,
            `remark` = :remark,
            `status` = 1,
            `created_at` = now()";

        // prepare the query
        $stmt = $db->prepare($query);

        // bind the values
        $stmt->bindParam(':uid', $uid);
        $stmt->bindParam(':request_no', $request_no);
        $stmt->bindParam(':date_requested', $date_requested);
        $stmt->bindParam(':request_type', $request_type);
        $stmt->bindParam(':project_name', $project_name);
        $stmt->bindParam(':payable_to', $payable_to);
        $stmt->bindParam(':payable_other', $payable_other);
        $stmt->bindParam(':remark', $remark);
     

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
                echo json_encode("Failure at " . date("Y-m-d") . " " . date("h:i:sa") . " " . array($arr[2]));
                die();
            }
        } catch (Exception $e) {
            error_log($e->getMessage());
            $db->rollback();
            http_response_code(501);
            echo json_encode(array("Failure at " . date("Y-m-d") . " " . date("h:i:sa") . " " . $e->getMessage()));
            die();
        }

        // petty_list
        for($i=0 ; $i < count($petty_array) ; $i++)
        {
            $query = "INSERT INTO petty_list
            SET
                `petty_id` = :petty_id,
                `payee` = :payee,
                `particulars` = :particulars,
                `price` = :price,
                `qty` = :qty,
               
                `status` = 1,
                `created_at` = now()";

            // prepare the query
            $stmt = $db->prepare($query);

            // bind the values
            $stmt->bindParam(':petty_id', $last_id);
            $stmt->bindParam(':payee', $petty_array[$i]['payee']);
            $stmt->bindParam(':particulars', $petty_array[$i]['particulars']);
            $stmt->bindParam(':price', $petty_array[$i]['price']);
            $stmt->bindParam(':qty', $petty_array[$i]['qty']);
         

            try {
                // execute the query, also check if query was successful
                if (!$stmt->execute()) {
                    $arr = $stmt->errorInfo();
                    error_log($arr[2]);
                    $db->rollback();
                    http_response_code(501);
                    echo json_encode("Failure at " . date("Y-m-d") . " " . date("h:i:sa") . " " . array($arr[2]));
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
        $batch_type = "petty";

        try {
            $total = count($_FILES['files']['name']);
            // Loop through each file
            for( $i=0 ; $i < $total ; $i++ ) {

                if(isset($_FILES['files']['name'][$i]))
                {
                    $image_name = $_FILES['files']['name'][$i];
                    $valid_extensions = array("jpg","jpeg","png","gif","pdf","docx","doc","xls","xlsx","ppt","pptx","zip","rar","7z","txt","dwg","skp","psd","evo");
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

                        if($bucket->upload(
                        fopen($_FILES['files']['tmp_name'][$i], 'r'),
                        ['name' => $upload_name]
                        ))
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
                        }
                    }
                    else
                    {
                        $message = 'Only Images or Office files allowed to upload';
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
        $query = "INSERT INTO petty_history
        SET
            `petty_id` = :petty_id,
            `actor` = :actor,
            `action` = 'Submitted',
            `reason` = '',
            `status` = 1,
            `created_at` = now()";

        // prepare the query
        $stmt = $db->prepare($query);

        // bind the values
        $stmt->bindParam(':petty_id', $batch_id);
        $stmt->bindParam(':actor', $user_name);
        
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
    catch (Exception $e){

        error_log($e->getMessage());
        $db->rollback();
        http_response_code(501);
        echo json_encode(array("Failure at " . date("Y-m-d") . " " . date("h:i:sa") . $e->getMessage()));
        die();

    }
}

