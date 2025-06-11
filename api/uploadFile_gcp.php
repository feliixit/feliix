<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
$jwt = (isset($_COOKIE['jwt']) ?  $_COOKIE['jwt'] : null);

include_once 'config/core.php';
include_once 'libs/php-jwt-master/src/BeforeValidException.php';
include_once 'libs/php-jwt-master/src/ExpiredException.php';
include_once 'libs/php-jwt-master/src/SignatureInvalidException.php';
include_once 'libs/php-jwt-master/src/JWT.php';
use \Firebase\JWT\JWT;

//upload.php
require_once '../vendor/autoload.php';
include_once 'config/conf.php';
include_once 'config/database.php';


use Google\Cloud\Storage\StorageClient;

$user_id = '';

if ( !isset( $jwt ) ) {
    http_response_code(401);
 
    echo json_encode(array("message" => "Access denied."));
    die();
}
else
{
  try {
          // decode jwt
          $decoded = JWT::decode($jwt, $key, array('HS256'));
          $user_id = $decoded->data->id;

      }

      catch (Exception $e){
      
          http_response_code(401);
     
        echo json_encode(array("message" => "Access denied."));
        die();
      }
}

header('Access-Control-Allow-Origin: *');  

$database = new Database();
$db = $database->getConnection();
$conf = new Conf();


$batch_type = (isset($_POST['batch_type']) ?  $_POST['batch_type'] : '');
$batch_id = (isset($_POST['batch_id']) ?  $_POST['batch_id'] : 0);

$image = '';

if(isset($_FILES['file']['name']))
{
    $image_name = $_FILES['file']['name'];
    $valid_extensions = array("jpg","jpeg","png","gif","pdf","docx","doc","xls","xlsx","ppt","pptx","zip","rar","7z","txt","dwg","skp","psd","evo","dwf","bmp");
    $extension = pathinfo($image_name, PATHINFO_EXTENSION);
    if(in_array(strtolower($extension), $valid_extensions))
    {
        //$upload_path = 'img/' . time() . '.' . $extension;

        $storage = new StorageClient([
            'projectId' => 'predictive-fx-284008',
            'keyFilePath' => $conf::$gcp_key
        ]);

        $bucket = $storage->bucket('feliiximg');

        $upload_name = time() . '_' . pathinfo($image_name, PATHINFO_FILENAME) . '.' . $extension;

        $file_size = filesize($_FILES['file']['tmp_name']);
        $size = 0;

        $obj = $bucket->upload(
            fopen($_FILES['file']['tmp_name'], 'r'),
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
            }


            $message = 'Uploaded';
            $code = 0;
            $upload_id = $last_id;
            $image = $image_name;
        }
        else
        {
            $code = 502;
            $message = 'There is an error while uploading file';
            $image = $image_name;
        }
    }
    else
    {
        $code = 502;
        $message = 'Only Images or Office files allowed to upload';
        $image = $image_name;
    }
}
else
{
    $message = 'None file';
}

$output = array(
    'code' => $code,
    'message'  => $message,
    'image'   => $image
);

echo json_encode($output);


?>