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

$followup = isset($_POST['followup']) ? $_POST['followup'] : '';

$project_name = isset($_POST['project_name']) ? $_POST['project_name'] : '';
$project_location = isset($_POST['project_location']) ? $_POST['project_location'] : '';
$po = isset($_POST['po']) ? $_POST['po'] : '';
$request_by = isset($_POST['request_by']) ? $_POST['request_by'] : '';
$request_date = isset($_POST['request_date']) ? $_POST['request_date'] : '';
$submit_by = isset($_POST['submit_by']) ? $_POST['submit_by'] : '';
$submit_date = isset($_POST['submit_date']) ? $_POST['submit_date'] : '';

$add_term = isset($_POST['add_term']) ? $_POST['add_term'] : '';

$generate = isset($_POST['generate']) ? $_POST['generate'] : '';

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

        if($generate == 'N')
            $serial_name = "";
        else
            $serial_name = GetSerailName($db);
    
        $query = "INSERT INTO transmittal
        SET
            `title` = :title,
            `kind` = :kind,
            `serial_name` = :serial_name,
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

            `project_name` = :project_name,
            `project_location` = :project_location,
            `po` = :po,
            `request_by` = :request_by,
            `request_date` = :request_date,
            `submit_by` = :submit_by,
            `submit_date` = :submit_date,

            `followup` = :followup,
            
            `pageless` = :pageless,
            `contact` = :contact,

            `pixa_t` = 10,
            
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
        $stmt->bindParam(':serial_name', $serial_name);
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

        $stmt->bindParam(':project_name', $project_name);
        $stmt->bindParam(':project_location', $project_location);
        $stmt->bindParam(':po', $po);
        $stmt->bindParam(':request_by', $request_by);
        $stmt->bindParam(':request_date', $request_date);
        $stmt->bindParam(':submit_by', $submit_by);
        $stmt->bindParam(':submit_date', $submit_date);

        $stmt->bindParam(':followup', $followup);

        $stmt->bindParam(':contact', $contact);
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

        // pages
        for($i=0 ; $i < count($pages_array) ; $i++)
        {
            $pg = $i + 1;
            // insert quotation_page
            $query = "INSERT INTO transmittal_page
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
                $query = "INSERT INTO transmittal_page_type
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

                $query = "INSERT INTO transmittal_term
                SET
                    `quotation_id` = :quotation_id,
                    `page` = 0,
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

                $query = "INSERT INTO transmittal_term
                SET
                    `quotation_id` = :quotation_id,
                    `page` = 0,
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

                $query = "INSERT INTO transmittal_term
                SET
                    `quotation_id` = :quotation_id,
                    `page` = 0,
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
                $list = "1. Warranty: 2 years upon delivery and lifetime service warranty. This limited warranty does not cover those defects brought about by normal wear and tear. During the Warranty Period, Feliix Inc. will repair or replace products or parts of a product that proves defective because of improper material or workmanship under normal use and maintenance.
2. Quotation valid for 2 Weeks.
3. Leadtime:30- 45 Days (Start production upon P.O and down payment)
4. Above price is subject to change without prior notice due to exchange rate of Dollar to Peso.
Building freight charges to be charged accordingly to client.
Any returned deliveries due to unfinished site and/or resulting for items not to be installed, will be billed 30 days after delivery on site and/or acknowledgement of arrival of goods.
5. Covid related requirements (e.g. Swab Test, etc.) are reimbursable to the client.
6. Delivery of items is free of charge within Metro Manila only. Out of town delivery shall be billed accordingly.";

                $query = "INSERT INTO transmittal_term
                SET
                    `quotation_id` = :quotation_id,
                    `page` = 0,
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
    if($result == false)
        return "";
    else
        return $result['catagory_id'];
    
}

function GetSerailName($db)
{
    // get 2 digits of year now
    $year = date("y");
    $query = "SELECT serial_name FROM transmittal WHERE SUBSTRING(serial_name, 1, 2) = '" . $year . "'  ORDER BY serial_name DESC LIMIT 1";
    $stmt = $db->prepare($query);
    $stmt->execute();

    // maybe null
    $result = $stmt->fetch(PDO::FETCH_ASSOC);
    if ($result == false)
    {
        $serial = $year . "-0001";
        return $serial;
    }
    else
        $serial = $result['serial_name'];

    // parse the serial name
    $serial = substr($serial, 3);
    $serial = intval($serial);

    $serial = $serial + 1;
    $serial = str_pad($serial, 4, '0', STR_PAD_LEFT);
    return $year . "-" . $serial;
}
