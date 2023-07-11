<?php
error_reporting(E_ALL);
//header("Access-Control-Allow-Origin: https://feliix.myvnc.com");
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: POST");
header("Access-Control-Max-Age: 3600");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

$jwt = (isset($_POST['jwt']) ?  $_POST['jwt'] : null);

$first_line = isset($_POST['first_line']) ? $_POST['first_line'] : '';
$second_line = isset($_POST['second_line']) ? $_POST['second_line'] : '';
$project_category = isset($_POST['project_category']) ? $_POST['project_category'] : '';
$quotation_no = isset($_POST['quotation_no']) ? $_POST['quotation_no'] : '';
$quotation_date = isset($_POST['quotation_date']) ? $_POST['quotation_date'] : '';
$prepare_for_first_line = isset($_POST['prepare_for_first_line']) ? $_POST['prepare_for_first_line'] : '';
$prepare_for_second_line = isset($_POST['prepare_for_second_line']) ? $_POST['prepare_for_second_line'] : '';
$prepare_for_third_line = isset($_POST['prepare_for_third_line']) ? $_POST['prepare_for_third_line'] : '';
$prepare_by_first_line = isset($_POST['prepare_by_first_line']) ? $_POST['prepare_by_first_line'] : '';
$prepare_by_second_line = isset($_POST['prepare_by_second_line']) ? $_POST['prepare_by_second_line'] : '';

$project_name = isset($_POST['project_name']) ? $_POST['project_name'] : '';
$project_location = isset($_POST['project_location']) ? $_POST['project_location'] : '';
$po = isset($_POST['po']) ? $_POST['po'] : '';
$request_by = isset($_POST['request_by']) ? $_POST['request_by'] : '';
$request_date = isset($_POST['request_date']) ? $_POST['request_date'] : '';
$submit_by = isset($_POST['submit_by']) ? $_POST['submit_by'] : '';
$submit_date = isset($_POST['submit_date']) ? $_POST['submit_date'] : '';

$pageless = isset($_POST['pageless']) ? $_POST['pageless'] : '';

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
    
        $query = "INSERT INTO approval_form_quotation
        SET
            `first_line` = :first_line,
            `second_line` = :second_line,
            `project_category` = :project_category,
            `quotation_no` = :quotation_no,
            `quotation_date` = :quotation_date,
            `prepare_for_first_line` = :prepare_for_first_line,
            `prepare_for_second_line` = :prepare_for_second_line,
            `prepare_for_third_line` = :prepare_for_third_line,
            `prepare_by_first_line` = :prepare_by_first_line,
            `prepare_by_second_line` = :prepare_by_second_line,
            `project_name` = :project_name,
            `project_location` = :project_location,
            `po` = :po,
            `request_by` = :request_by,
            `request_date` = :request_date,
            `submit_by` = :submit_by,
            `submit_date` = :submit_date,

            `pageless` = :pageless,
            `status` = 0,
            `create_id` = :create_id,
            `created_at` =  now() ";

        // prepare the query
        $stmt = $db->prepare($query);

        // bind the values
        $stmt->bindParam(':first_line', $first_line);
        $stmt->bindParam(':second_line', $second_line);
        $stmt->bindParam(':project_category', $project_category);
        $stmt->bindParam(':quotation_no', $quotation_no);
        $stmt->bindParam(':quotation_date', $quotation_date);
        $stmt->bindParam(':prepare_for_first_line', $prepare_for_first_line);
        $stmt->bindParam(':prepare_for_second_line', $prepare_for_second_line);
        $stmt->bindParam(':prepare_for_third_line', $prepare_for_third_line);
        $stmt->bindParam(':prepare_by_first_line', $prepare_by_first_line);
        $stmt->bindParam(':prepare_by_second_line', $prepare_by_second_line);

        $stmt->bindParam(':project_name', $project_name);
        $stmt->bindParam(':project_location', $project_location);
        $stmt->bindParam(':po', $po);
        $stmt->bindParam(':request_by', $request_by);
        $stmt->bindParam(':request_date', $request_date);
        $stmt->bindParam(':submit_by', $submit_by);
        $stmt->bindParam(':submit_date', $submit_date);

        $stmt->bindParam(':pageless', $pageless);

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

