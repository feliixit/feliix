<?php
error_reporting(E_ALL);
//header("Access-Control-Allow-Origin: https://feliix.myvnc.com");
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: POST");
header("Access-Control-Max-Age: 3600");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

$jwt = (isset($_POST['jwt']) ?  $_POST['jwt'] : null);

$transmittal_date = isset($_POST['transmittal_date']) ? $_POST['transmittal_date'] : '';
$transmittal_to = isset($_POST['transmittal_to']) ? $_POST['transmittal_to'] : '';
$transmittal_from = isset($_POST['transmittal_from']) ? $_POST['transmittal_from'] : '';
$transmittal_subject = isset($_POST['transmittal_subject']) ? $_POST['transmittal_subject'] : '';
$transmittal_remark = isset($_POST['transmittal_remark']) ? $_POST['transmittal_remark'] : '';
$transmittal_purpose = (isset($_POST['transmittal_purpose']) ? $_POST['transmittal_purpose'] : []);

$transmittal_purpose = json_decode($transmittal_purpose, true);

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

        // now you can apply
        $uid = $user_id;
    
        $query = "INSERT INTO transmittal
        SET
            `transmittal_date` = :transmittal_date,
            `transmittal_to` = :transmittal_to,
            `transmittal_from` = :transmittal_from,
            `transmittal_subject` = :transmittal_subject,
            `transmittal_remark` = :transmittal_remark,
            `transmittal_purpose` = :transmittal_purpose,

            `status` = 0,
            `create_id` = :create_id,
            `created_at` =  now() ";

        // prepare the query
        $stmt = $db->prepare($query);

        $transmittal_purpose_string = implode(",", $transmittal_purpose);

        // bind the values
        $stmt->bindParam(':transmittal_date', $transmittal_date);
        $stmt->bindParam(':transmittal_to', $transmittal_to);
        $stmt->bindParam(':transmittal_from', $transmittal_from);
        $stmt->bindParam(':transmittal_subject', $transmittal_subject);
        $stmt->bindParam(':transmittal_remark', $transmittal_remark);
        $stmt->bindParam(':transmittal_purpose', $transmittal_purpose_string);
      

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

        $db->commit();

        
        http_response_code(200);
        echo json_encode(array("message" => "Success at " . date("Y-m-d") . " " . date("h:i:sa"), "id" => $last_id));
        
    }
    catch (Exception $e){

        error_log($e->getMessage());
        $db->rollback();
        http_response_code(501);
        echo json_encode(array("Failure at " . date("Y-m-d") . " " . date("h:i:sa") . $e->getMessage()));
        die();

    }
}

