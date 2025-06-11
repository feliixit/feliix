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


use \Firebase\JWT\JWT;
use Google\Cloud\Storage\StorageClient;

$method = $_SERVER['REQUEST_METHOD'];


if (!isset($jwt)) {
    http_response_code(401);

    echo json_encode(array("message" => "Failure at " . date("Y-m-d") . " " . date("h:i:sa") . " " . "Access denied."));
    die();
} else {
    try {
        // decode jwt
        $decoded = JWT::decode($jwt, $key, array('HS256'));
        $user_id = $decoded->data->id;
        $user_name = $decoded->data->username;
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

        echo json_encode(array("message" => "Failure at " . date("Y-m-d") . " " . date("h:i:sa") . " " . "Access denied."));
        die();
    }
}

header('Access-Control-Allow-Origin: *');

include_once 'config/database.php';

switch ($method) {

    case 'POST':

        $database = new Database();
        $db = $database->getConnection();
        $conf = new Conf();

        $jwt = (isset($_POST['jwt']) ?  $_POST['jwt'] : null);
        $id = (isset($_POST['id']) ?  $_POST['id'] : 0);
        
        $transmittal_date = isset($_POST['transmittal_date']) ? $_POST['transmittal_date'] : '';
        $transmittal_to = isset($_POST['transmittal_to']) ? $_POST['transmittal_to'] : '';
        $transmittal_from = isset($_POST['transmittal_from']) ? $_POST['transmittal_from'] : '';
        $transmittal_subject = isset($_POST['transmittal_subject']) ? $_POST['transmittal_subject'] : '';
        $transmittal_remark = isset($_POST['transmittal_remark']) ? $_POST['transmittal_remark'] : '';
        $transmittal_purpose = (isset($_POST['transmittal_purpose']) ? $_POST['transmittal_purpose'] : []);

        $transmittal_purpose = json_decode($transmittal_purpose, true);

        if ($id == 0) {
            http_response_code(401);
            echo json_encode(array("message" => "Failure at " . date("Y-m-d") . " " . date("h:i:sa") . " " . "Access denied."));
            die();
        }

        try {
            // now you can apply
            $query = "update transmittal
                SET
                    `transmittal_date` = :transmittal_date,
                    `transmittal_to` = :transmittal_to,
                    `transmittal_from` = :transmittal_from,
                    `transmittal_subject` = :transmittal_subject,
                    `transmittal_remark` = :transmittal_remark,
                    `transmittal_purpose` = :transmittal_purpose,

                    `updated_id` = :updated_id,
                    `updated_at` = now()
                    where id = :id";

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
            
            $stmt->bindParam(':updated_id', $user_id);

            $stmt->bindParam(':id', $id);

            $last_id = $id;
            // execute the query, also check if query was successful
            try {
                // execute the query, also check if query was successful
                if (!$stmt->execute()) {
                    $arr = $stmt->errorInfo();
                    error_log($arr[2]);

                    http_response_code(501);
                    echo json_encode(array("Failure at " . date("Y-m-d") . " " . date("h:i:sa") . " " . $arr[2]));
                    die();
                }
            } catch (Exception $e) {
                error_log($e->getMessage());

                http_response_code(501);
                echo json_encode(array("Failure at " . date("Y-m-d") . " " . date("h:i:sa") . " " . $e->getMessage()));
                die();
            }



            http_response_code(200);
            echo json_encode(array("message" => "Success at " . date("Y-m-d") . " " . date("h:i:sa")));
        } catch (Exception $e) {

            error_log($e->getMessage());

            http_response_code(501);
            echo json_encode(array("Failure at " . date("Y-m-d") . " " . date("h:i:sa") . " " . $e->getMessage()));
            die();
        }
        
}
