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

$consumable = (isset($_POST['consumable']) ?  $_POST['consumable'] : '[]');
$consumable_ary = json_decode($consumable, true);


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

        $show_c = '';
        $pixa_c = 0;

        $show_c = $consumable_ary["show_c"];
        $pixa_c = $consumable_ary["pixa_c"];
    
        $query = "INSERT INTO quotation_eng
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

            `show_c` = :show_c,
            `pixa_c` = :pixa_c,
            
            `status` = 0,
            `create_id` = :create_id,
            `created_at` =  now() ";

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

        $stmt->bindParam(':show_c', $show_c);
        $stmt->bindParam(':pixa_c', $pixa_c);

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

        // requirements
        $query = "INSERT INTO quotation_eng_consumable
        SET
            `quotation_id` = :quotation_id,
            `title` = :title,
            `block` = :block,
            `status` = 0,
            `create_id` = :create_id,
            `created_at` = now()";

        $json = json_encode($consumable_ary['block']);

        // prepare the query
        $stmt = $db->prepare($query);

        // bind the values
        $stmt->bindParam(':quotation_id', $last_id);
        $stmt->bindParam(':title', $consumable_ary['title']);
        $stmt->bindParam(':block', $json);
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
        


        if($add_term == 'y' && $project_id != 0 && $kind == ''){
            $project_category = GetProjectInfo($project_id, $db);

            if($project_category == 2)
            {
                $title = "Warranty";
                $brief = "Terms and Condition";
                $list = "【*1 year warranty for Everlight, Feliix SSIT, and Feliix SB Decorative】
【*2 years warranty for Feliix SB & Colors】
【*3 years warranty for Feliix TONS】
【*5 years warranty for Feliix Decorative】
【*Does not cover defects resulting from normal wear, improper use, or improper installation which does not conform to the installation instructions.】
【*Warranty is null and void when tampered/seal is broken】";

                $query = "INSERT INTO quotation_eng_term
                SET
                    `quotation_id` = :quotation_id,
                    `title` = :title,
                    `brief` = :brief,
                    `list` = :list,
                    `status` = 0,
                    `create_id` = :create_id,
                    `created_at` = now()";

                // prepare the query
                $stmt = $db->prepare($query);
                // bind the values
                $stmt->bindParam(':quotation_id', $last_id);
                $stmt->bindParam(':title', $title);
                $stmt->bindParam(':brief', $brief);
                $stmt->bindParam(':list', $list);
            
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

                $title = "Purchased Order";
                $brief = "Terms and Condition";
                $list = '"FELIIX SB"
【*LEAD TIME; 60-75 Working Days; changes, will depend on the availability of stocks(Starts upon receiving the downpayment)】

"EVERLIGHT | DECORATIVE | FELIIX SSIT"
【*LEAD TIME; 60-75 Working Days; changes, will depend on the availability of stocks(Starts upon receiving the downpayment)】

"FELIIX TONS"
【*LEAD TIME; 60-75 Working Days; changes, will depend on the availability of stocks(Starts upon receiving the downpayment)】

【*50% Downpayment before processing the items, 50% upon delivery】
【*Custom items- approval of specs by client and/or designer】
【*Installation of items is not part of the service unless requested by client for additional cost.】';

                $query = "INSERT INTO quotation_eng_term
                SET
                    `quotation_id` = :quotation_id,
                    `title` = :title,
                    `brief` = :brief,
                    `list` = :list,
                    `status` = 0,
                    `create_id` = :create_id,
                    `created_at` = now()";

                // prepare the query
                $stmt = $db->prepare($query);
                // bind the values
                $stmt->bindParam(':quotation_id', $last_id);
                $stmt->bindParam(':title', $title);
                $stmt->bindParam(':brief', $brief);
                $stmt->bindParam(':list', $list);
            
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

                $title = "Disclaimer";
                $brief = "";
                $list = "Feliix Inc. is not responsible for specification and layout revisions that may affect lux outcomes, unless it is a proposal produced and approved by the company itself.";

                $query = "INSERT INTO quotation_eng_term
                SET
                    `quotation_id` = :quotation_id,
                    `title` = :title,
                    `brief` = :brief,
                    `list` = :list,
                    `status` = 0,
                    `create_id` = :create_id,
                    `created_at` = now()";

                // prepare the query
                $stmt = $db->prepare($query);
                // bind the values
                $stmt->bindParam(':quotation_id', $last_id);
                $stmt->bindParam(':title', $title);
                $stmt->bindParam(':brief', $brief);
                $stmt->bindParam(':list', $list);
            
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

            if($project_category == OFFICE)
            {
                $title = "Terms and Conditions";
                $brief = "";
                $list = "1. Warranty: 5 years upon delivery and lifetime service warranty. This limited warranty does not cover those defects brought about by normal wear and tear. During the Warranty Period, Feliix Inc. will repair or replace products or parts of a product that proves defective because of improper material or workmanship under normal use and maintenance.
2. Quotation valid for 2 Weeks.
3. Leadtime:30- 45 Days (Start production upon P.O and down payment)
4. Above price is subject to change without prior notice due to exchange rate of Dollar to Peso.
Building freight charges to be charged accordingly to client.
Any returned deliveries due to unfinished site and/or resulting for items not to be installed, will be billed 30 days after delivery on site and/or acknowledgement of arrival of goods.
5. Covid related requirements (e.g. Swab Test, etc.) are reimbursable to the client.
6. Delivery of items is free of charge within Metro Manila only. Out of town delivery shall be billed accordingly.";

                $query = "INSERT INTO quotation_eng_term
                SET
                    `quotation_id` = :quotation_id,
                    `title` = :title,
                    `brief` = :brief,
                    `list` = :list,
                    `status` = 0,
                    `create_id` = :create_id,
                    `created_at` = now()";

                // prepare the query
                $stmt = $db->prepare($query);
                // bind the values
                $stmt->bindParam(':quotation_id', $last_id);
                $stmt->bindParam(':title', $title);
                $stmt->bindParam(':brief', $brief);
                $stmt->bindParam(':list', $list);
            
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