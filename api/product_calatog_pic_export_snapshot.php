<?php
ob_start();
//error_reporting(E_ALL);
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
        $message = "snapshot";
        $batch_type = "od_message";
        $uid = $user_id;

        $data = json_decode(file_get_contents('php://input'), true);
        
        $images = $data['images']; // Expecting an array of item IDs

        $zip = new ZipArchive();
        $zipFileName = "snapshots_" . time() . ".zip";
        $zip->open($zipFileName, ZipArchive::CREATE | ZipArchive::OVERWRITE);

        foreach ($images as $data) {
            // Process each item to get the image
            $img = $data['image']; // Assuming images are passed in the request
            $item_id = $data['item_id'];
            $img = str_replace('data:image/png;base64,', '', $img);
            $img = str_replace('data:image/jpeg;base64,', '', $img);
            $img = str_replace(' ', '+', $img);
            $fileData = base64_decode($img);

            // Save the image to a temporary file
            $tempFileName = tempnam(sys_get_temp_dir(), 'img_') . '.jpg';
            file_put_contents($tempFileName, $fileData);
            $zip->addFile($tempFileName, "{$item_id}.jpg"); // Add to ZIP
        }

        if($zip->close() == false) exit("error");
	ob_clean();

        // Set headers to download the ZIP file
        header('Content-Type: application/zip');
        header('Content-Disposition: attachment; filename="' . $zipFileName . '"');
        header('Content-Length: ' . filesize($zipFileName));

        // Read the ZIP file and delete it after download
        readfile($zipFileName);
        unlink($zipFileName); // Clean up the temporary file

        break;
}
