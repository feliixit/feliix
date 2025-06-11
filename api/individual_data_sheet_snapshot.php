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

if (!isset($jwt)) {
    http_response_code(401);

    echo json_encode(array("message" => "Access denied."));
    die();
} else {
    try {
        // decode jwt
        $decoded = JWT::decode($jwt, $key, array('HS256'));
        $user_id = $decoded->data->id;
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
$conf = new Conf();

switch ($method) {
 
    case 'POST':
        // get database connection

        $batch_type = "individual_sig";

        $uid = $user_id;

        $data = json_decode(file_get_contents('php://input'), true);
        
        $item_id = $data['item_id'];
        $img = $data['image_name'];
        $img1 = $data['image_date'];


        $img = str_replace(' ', '+', $img[1]);
        if($img != "")
            $fileData = base64_decode($img);


        $img1 = str_replace(' ', '+', $img1[1]);
        if($img1 != "")
            $fileData1 = base64_decode($img1);
    
        $sig_name = "";
        $sig_date = "";

        try {
            if (isset($fileData)) {

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

                $image_name = "snapshot.jpg";


                $obj = $bucket->upload(
                    $fileData,
                    ['name' => $upload_name]);

                $info = $obj->info();
                $size = $info['size'];

                if($size)
                {
                    $sig_name = $upload_name;

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

            if (isset($fileData1)) {

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

                $image_name = "snapshot.jpg";


                $obj = $bucket->upload(
                    $fileData1,
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
            
        }catch (Exception $e){

            //http_response_code(401);

            //echo json_encode(array("message" => "Access denied."));
            //die();
        }

        // update employee_data_sheet
        $query = "UPDATE user
            SET
                sig_name = :sig_name,
                sig_date = :sig_date,
                auth_date = DATE_FORMAT(NOW(), '%Y-%m-%d %H:%i:%s')
            WHERE
                id = :id";

        // prepare the query
        $stmt = $db->prepare($query);

        // bind the values
        $stmt->bindParam(':sig_name', $sig_name);
        $stmt->bindParam(':sig_date', $sig_date);

        $stmt->bindParam(':id', $item_id);


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


        $returnArray = array('batch_id' => $last_id);
        $jsonEncodedReturnArray = json_encode($returnArray, JSON_PRETTY_PRINT);

        echo $jsonEncodedReturnArray;

        break;
}

