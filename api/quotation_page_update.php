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
        $db->beginTransaction();
        $conf = new Conf();

        $jwt = (isset($_POST['jwt']) ?  $_POST['jwt'] : null);
        $id = (isset($_POST['id']) ?  $_POST['id'] : 0);
        
        $types = (isset($_POST['types']) ?  $_POST['types'] : '[]');
        $types_array = json_decode($types,true);
        $pageless = (isset($_POST['pageless']) ?  $_POST['pageless'] : '');

        if ($id == 0) {
            http_response_code(401);
            echo json_encode(array("message" => "Failure at " . date("Y-m-d") . " " . date("h:i:sa") . " " . "Access denied."));
            die();
        }


                $type_id = 0;
            
                    $query = "INSERT INTO quotation_page_type
                    SET
                        `quotation_id` = :quotation_id,
                        `page_id` = :page_id,
                        `block_type` = :block_type,
                        `block_name` = :block_name,
                        `not_show` = :not_show,
                        `real_amount` = :real_amount,
                        `pixa` = :pixa,
                        `status` = 0,
                        `create_id` = :create_id,
                        `created_at` = now()";

                    // prepare the query
                    $stmt = $db->prepare($query);

                    // bind the values
                    $stmt->bindParam(':quotation_id', $id);
                    $stmt->bindParam(':page_id', $types_array['page_id']);
                    $stmt->bindParam(':block_type', $types_array['type']);
                    $stmt->bindParam(':block_name', $types_array['name']);
                    $stmt->bindParam(':pixa', $types_array['pixa']);
                    $stmt->bindParam(':not_show', $types_array['not_show']);
                    $stmt->bindParam(':real_amount', $types_array['real_amount']);
                
                    $stmt->bindParam(':create_id', $user_id);
                
                    // type_id
                    try {
                        // execute the query, also check if query was successful
                        if (!$stmt->execute()) {
                            $arr = $stmt->errorInfo();
                            error_log($arr[2]);
                            $db->rollback();
                            http_response_code(501);
                            echo json_encode("Failure at " . date("Y-m-d") . " " . date("h:i:sa") . " " . $arr[2]);
                            die();
                        }
                        else
                            $type_id = $db->lastInsertId();
                    } catch (Exception $e) {
                        error_log($e->getMessage());
                        $db->rollback();
                        http_response_code(501);
                        echo json_encode(array("Failure at " . date("Y-m-d") . " " . date("h:i:sa") . " " . $e->getMessage()));
                        die();
                    }
                    
            $query = "UPDATE quotation SET `pageless` = :pageless WHERE `id` = :id";
            $stmt = $db->prepare($query);
            $stmt->bindParam(':pageless', $pageless);
            $stmt->bindParam(':id', $id);
            if (!$stmt->execute()) {
                $arr = $stmt->errorInfo();
                error_log($arr[2]);
                $db->rollback();
                http_response_code(501);
                echo json_encode("Failure at " . date("Y-m-d") . " " . date("h:i:sa") . " " . $arr[2]);
                die();
            }
            

            $db->commit();

            http_response_code(200);
            echo json_encode(array("message" => $type_id));
       
        break; 
    
}

