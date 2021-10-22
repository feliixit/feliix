<?php
error_reporting(E_ALL);
//header("Access-Control-Allow-Origin: https://feliix.myvnc.com");
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: POST");
header("Access-Control-Max-Age: 3600");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

$jwt = (isset($_POST['jwt']) ?  $_POST['jwt'] : null);
$uid = (isset($_POST['uid']) ?  $_POST['uid'] : 0);
$pid = (isset($_POST['pid']) ?  $_POST['pid'] : 0);
$start_date = (isset($_POST['start_date']) ?  $_POST['start_date'] : '');
$end_date = (isset($_POST['end_date']) ?  $_POST['end_date'] : '');

$detail_plus = (isset($_POST['detail_plus']) ?  $_POST['detail_plus'] : '[]');
$detail_plus_array = json_decode($detail_plus,true);

$detail_minus = (isset($_POST['detail_minus']) ?  $_POST['detail_minus'] : '[]');
$detail_minus_array = json_decode($detail_minus,true);

$other = (isset($_POST['other']) ?  $_POST['other'] : '[]');
$other_array = json_decode($other,true);


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

        if ($pid == 0) {
            http_response_code(401);
            echo json_encode(array("message" => "Failure at " . date("Y-m-d") . " " . date("h:i:sa") . " " . "Access denied."));
            die();
        }

        $query = "update salary_slip_mgt
        SET
            `start_date` = :start_date,
            `end_date` = :end_date,
            `status` = 0,
            `updated_id` = :updated_id,
            `updated_at` = now()
            where id = :id";

        // prepare the query
        $stmt = $db->prepare($query);

        // bind the values
        $stmt->bindParam(':start_date', $start_date);
        $stmt->bindParam(':end_date', $end_date);
        $stmt->bindParam(':updated_id', $user_id);
        $stmt->bindParam(':id', $pid);

        $last_id = $pid;
        // execute the query, also check if query was successful
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

        // remove old data
        $query = "DELETE FROM salary_slip_mgt_detail
                WHERE
                `salary_slip_id` = :template_id";

        // prepare the query
        $stmt = $db->prepare($query);

        // bind the values
        $stmt->bindParam(':template_id', $last_id);

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

        // remove old data
        $query = "DELETE FROM salary_slip_mgt_other
                WHERE
                `salary_slip_id` = :template_id";

        // prepare the query
        $stmt = $db->prepare($query);

        // bind the values
        $stmt->bindParam(':template_id', $last_id);

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

        // plus
        for($i=0 ; $i < count($detail_plus_array) ; $i++)
        {
            $query = "INSERT INTO salary_slip_mgt_detail
            SET
                `salary_slip_id` = :salary_slip_id,
                `type` = 1,
                `order` = :order,
                `cust` = :cust,
                `category` = :category,
                `remark` = :remark,
                `amount` = :amount,
               
                `status` = 0,
                `create_id` = :create_id,
                `created_at` = now()";

            // prepare the query
            $stmt = $db->prepare($query);

            // bind the values
            $stmt->bindParam(':salary_slip_id', $last_id);
            $stmt->bindParam(':order', $i);
            $cust = ($detail_plus_array[$i]['type'] == 0) ? 1 : 0 ;
            $stmt->bindParam(':cust', $cust);
            $stmt->bindParam(':category', $detail_plus_array[$i]['category']);
            $stmt->bindParam(':remark', $detail_plus_array[$i]['remark']);
            $amt = ($detail_plus_array[$i]['amount'] == '') ? 0 : $detail_plus_array[$i]['amount'] ;
            $stmt->bindParam(':amount', $amt);
            $stmt->bindParam(':create_id', $user_id);
         

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
            } catch (Exception $e) {
                error_log($e->getMessage());
                $db->rollback();
                http_response_code(501);
                echo json_encode(array("Failure at " . date("Y-m-d") . " " . date("h:i:sa") . " " . $e->getMessage()));
                die();
            }
  
        }

        // minus
        for($i=0 ; $i < count($detail_minus_array) ; $i++)
        {
            $query = "INSERT INTO salary_slip_mgt_detail
            SET
                `salary_slip_id` = :salary_slip_id,
                `type` = 2,
                `order` = :order,
                `cust` = :cust,
                `category` = :category,
                `remark` = :remark,
                `amount` = :amount,
               
                `status` = 0,
                `create_id` = :create_id,
                `created_at` = now()";

            // prepare the query
            $stmt = $db->prepare($query);

            // bind the values
            $stmt->bindParam(':salary_slip_id', $last_id);
            $stmt->bindParam(':order', $i);
            $cust = ($detail_minus_array[$i]['type'] == 0) ? 1 : 0 ;
            $stmt->bindParam(':cust', $cust);
            $stmt->bindParam(':category', $detail_minus_array[$i]['category']);
            $stmt->bindParam(':remark', $detail_minus_array[$i]['remark']);
            $amt = ($detail_minus_array[$i]['amount'] == '') ? 0 : $detail_minus_array[$i]['amount'] ;
            $stmt->bindParam(':amount', $amt);

            $stmt->bindParam(':create_id', $user_id);
         

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
            } catch (Exception $e) {
                error_log($e->getMessage());
                $db->rollback();
                http_response_code(501);
                echo json_encode(array("Failure at " . date("Y-m-d") . " " . date("h:i:sa") . " " . $e->getMessage()));
                die();
            }
  
        }

        // agenda1
        for($i=0 ; $i < count($other_array) ; $i++)
        {
            $query = "INSERT INTO salary_slip_mgt_other
            SET
                `salary_slip_id` = :salary_slip_id,
                `type` = 1,
                `order` = :order,
                `category` = :category,
                `previous` = :previous,
                `payment` = :payment,
               
                `status` = 0,
                `create_id` = :create_id,
                `created_at` = now()";

            // prepare the query
            $stmt = $db->prepare($query);

            // bind the values
            $stmt->bindParam(':salary_slip_id', $last_id);
            $stmt->bindParam(':order', $i);
            $stmt->bindParam(':category', $other_array[$i]['category']);
            
            $previous = ($other_array[$i]['previous'] == '') ? 0 : $other_array[$i]['previous'] ;
            $payment = ($other_array[$i]['payment'] == '') ? 0 : $other_array[$i]['payment'] ;

            $stmt->bindParam(':previous', $previous);
            $stmt->bindParam(':payment', $payment);
            $stmt->bindParam(':create_id', $user_id);
         

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
            } catch (Exception $e) {
                error_log($e->getMessage());
                $db->rollback();
                http_response_code(501);
                echo json_encode(array("Failure at " . date("Y-m-d") . " " . date("h:i:sa") . " " . $e->getMessage()));
                die();
            }
  
        }


        $db->commit();

        
        http_response_code(200);
        echo json_encode(array("message" => "Success at " . date("Y-m-d") . " " . date("h:i:sa") ));
        
    }
    catch (Exception $e){

        error_log($e->getMessage());
        $db->rollback();
        http_response_code(501);
        echo json_encode(array("Failure at " . date("Y-m-d") . " " . date("h:i:sa") . $e->getMessage()));
        die();

    }
}

