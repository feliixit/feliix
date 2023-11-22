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
$footer_first_line = isset($_POST['footer_first_line']) ? $_POST['footer_first_line'] : '';
$footer_second_line = isset($_POST['footer_second_line']) ? $_POST['footer_second_line'] : '';

$add_term = isset($_POST['add_term']) ? $_POST['add_term'] : '';

$title = isset($_POST['title']) ? $_POST['title'] : '';
$kind = isset($_POST['kind']) ? $_POST['kind'] : '';
$project_id = isset($_POST['project_id']) ? $_POST['project_id'] : 0;

$project_id == 0 ? $project_id = 0 : $project_id = $project_id;

$pages = (isset($_POST['pages']) ?  $_POST['pages'] : '[]');
$pages_array = json_decode($pages,true);
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

const OFFICE = 1;
const LIGHTING = 2;

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
    
        $query = "INSERT INTO soa_quotation
        SET
            `title` = :title,
            `kind` = :kind,
            `project_id` = :project_id,
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
            `footer_first_line` = :footer_first_line,
            `footer_second_line` = :footer_second_line,
            `pageless` = :pageless,
            `contact` = :contact,
            
            `status` = 0,
            `create_id` = :create_id,
            `created_at` =  now() ";

$contact = "MAIN OFFICE
25-E, 25th Flr., BDO Towers Valero,
8741 Paseo De Roxas,
1226 Makati City, Metro Manila,
Philippines

E: info@feliix.com
T: (+63) 2 8525-6288";

        // prepare the query
        $stmt = $db->prepare($query);

        // bind the values
        $stmt->bindParam(':title', $title);
        $stmt->bindParam(':kind', $kind);
        $stmt->bindParam(':project_id', $project_id);
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
        $stmt->bindParam(':footer_first_line', $footer_first_line);
        $stmt->bindParam(':footer_second_line', $footer_second_line);
        $stmt->bindParam(':pageless', $pageless);
        $stmt->bindParam(':contact', $contact);

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

        if(count($pages_array) == 0)
        {
            $query = "INSERT INTO soa_quotation_page
            SET
                `quotation_id` = :quotation_id,
    
                `page` = 1,
        
                `status` = 0,
                `create_id` = :create_id,
                `created_at` = now()";

            // prepare the query
            $stmt = $db->prepare($query);

            // bind the values
            $stmt->bindParam(':quotation_id', $last_id);

            $stmt->bindParam(':create_id', $user_id);
        

            try {
                // execute the query, also check if query was successful
                if ($stmt->execute()) {
                    //$page_id = $db->lastInsertId();
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
        }

        // pages
        for($i=0 ; $i < count($pages_array) ; $i++)
        {
            $pg = $i + 1;
            // insert quotation_page
            $query = "INSERT INTO soa_quotation_page
            SET
                `quotation_id` = :quotation_id,
    
                `page` = :page,
        
                `status` = 0,
                `create_id` = :create_id,
                `created_at` = now()";

            // prepare the query
            $stmt = $db->prepare($query);

            // bind the values
            $stmt->bindParam(':quotation_id', $last_id);
            $stmt->bindParam(':page', $pg);

            $stmt->bindParam(':create_id', $user_id);
        
            $page_id = 0;

            try {
                // execute the query, also check if query was successful
                if ($stmt->execute()) {
                    $page_id = $db->lastInsertId();
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

            $types_array = $pages_array[$i]['types'];
            for($j=0; $j < count($types_array); $j++)
            {
                $query = "INSERT INTO soa_quotation_page_type
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
                $stmt->bindParam(':quotation_id', $last_id);
                $stmt->bindParam(':page_id', $page_id);
                $stmt->bindParam(':block_type', $types_array[$j]['type']);
                $stmt->bindParam(':block_name', $types_array[$j]['name']);
                $stmt->bindParam(':pixa', $types_array[$j]['pixa']);
                $stmt->bindParam(':not_show', $types_array[$j]['not_show']);
                $stmt->bindParam(':real_amount', $types_array[$j]['real_amount']);
              
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

        }

        // payment term
        $query = "INSERT INTO soa_quotation_payment_term
                SET
                    quotation_id = :quotation_id,
                    page = 1,
                    payment_method = :payment_method,
                    brief = :brief,
                    list = :list,
                    `create_id` = :create_id,
                    `status` = 0,
                    created_at = now() ";
                
                    $t = "select " . $quotation_id . ", page, payment_method, brief, list, :create_id, now() 
                from quotation_payment_term where quotation_id = :quotation_id";
        // prepare the query
        $stmt = $db->prepare($query);

        $payment_method = 'Cash; Cheque; Credit Card; Bank Wiring;';
        $brief = '50% Downpayment & another 50% balance a day before the delivery';
        $list = '[{"id":"0", "bank_name": "BDO", "first_line":"Acct. Name: Feliix Inc. Acct no: 006910116614", "second_line":"Branch: V.A Rufino", "third_line":""}, {"id":"1", "bank_name": "SECURITY BANK", "first_line":"Acct. Name: Feliix Inc. Acct no: 0000018155245", "second_line":"Swift code: SETCPHMM", "third_line":""}]';        

        // bind the values
        $stmt->bindParam(':quotation_id', $last_id);
        $stmt->bindParam(':payment_method', $payment_method);
        $stmt->bindParam(':brief', $brief);
        $stmt->bindParam(':list', $list);

        $stmt->bindParam(':create_id', $user_id);

        try {
            // execute the query, also check if query was successful
            if ($stmt->execute()) {
               // $last_id = $db->lastInsertId();
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

function GetProjectInfo($id, $db) {
    $query = "SELECT catagory_id FROM project_main WHERE id = :id";
    $stmt = $db->prepare($query);
    $stmt->bindParam(':id', $id);
    $stmt->execute();
    $result = $stmt->fetch(PDO::FETCH_ASSOC);
    return $result['catagory_id'];

}