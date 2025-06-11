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
$prepare_by_third_line = isset($_POST['prepare_by_third_line']) ? $_POST['prepare_by_third_line'] : '';
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

        if($project_id != 0 && $kind == '')
            $info = GetProjectInfo($project_id, $db);

        $can_view = "";
        $project_category = "";
        if($info == 1)  // Office Systems
        {
            $can_view = "N";
            $project_category = "Office Systems";
        }

        if($info == 2)  // Lighting
        {
            $project_category = "Lighting";
        }
  
        // now you can apply
        $uid = $user_id;
    
        $query = "INSERT INTO quotation
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
            `prepare_by_third_line` = :prepare_by_third_line,
            `footer_first_line` = :footer_first_line,
            `footer_second_line` = :footer_second_line,
            `pageless` = :pageless,
            `can_view` = :can_view,
            
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
        $stmt->bindParam(':prepare_by_third_line', $prepare_by_third_line);
        $stmt->bindParam(':footer_first_line', $footer_first_line);
        $stmt->bindParam(':footer_second_line', $footer_second_line);
        $stmt->bindParam(':pageless', $pageless);
        $stmt->bindParam(':can_view', $can_view);

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
            $query = "INSERT INTO quotation_page
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
                $brief = "Terms and Conditions";
                $list = "1-Year Warranty : Feliix EL, ST, YD, SB Decorative, Dancelight

2-Year Warranty : Feliix HG, SB, GD

3-Year Warranty : Feliix Tons, Colors, Ledoux

5-Year Warranty : Feliix Decorative: Xcellent & SEED Design

*Warranty period commences from the date of delivery

*Does not cover defects arising from normal wear and tear, misuse, or improper installation that does not conform to the manufacturer's installation instructions.";

                $query = "INSERT INTO quotation_term
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

                $title = "Delivery & Lead Time";
                $brief = "Terms and Conditions";
                $list = '【On-Stock Products】
Within 7 business days from receipt of down payment

【General & Decorative Lighting】
30-45 business days for production upon receipt of down payment.
30 days for sea freight, or 15 days for air freight (with additional cost)

【Customized Lighting】
60 business days for production upon receipt of down payment.
30 days for sea freight, or 15 days for air freight (with additional cost)

Delivery Charges
- Free delivery within Metro Manila
- Delivery charges applies for areas outside Metro Manila';

                $query = "INSERT INTO quotation_term
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

                $title = "Notes";
                $brief = "";
                $list = '1. All customized items must be approved and signed by the client/designer prior to production.

2. Installation of lighting products is subject to additional charges, except for Feliix Decorative Xcellent and SEED Design products.

3. The client is responsible for providing the necessary wiring depending on lighting/dimming protocol:
- Phase Dimming - 2 wires (Line, Neutral) + 1 Ground
- 0/1-10V Dimming or DALI - 4 wires (Line, Neutral, 2 TF wires) + 1 Ground';

                $query = "INSERT INTO quotation_term
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
                $list = "Feliix Inc. is not held responsible for any deviations in lux levels resulting from changes to product specifications or lighting layout, unless these changes were proposed and approved by Feliix Inc. and collaboration with clients or designers.";

                $query = "INSERT INTO quotation_term
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
                $title = "Warranty & Lifetime Service";
                $brief = "Terms and Conditions";
                $list = "5-year warranty : Locally manufactured products
3-year warranty : Indent items from Taiwan

*Warranty period commences from the date of completion of Delivery and Installation.

*Does not cover defects arising from normal wear and tear, misuse, or unauthorized modifications. The warranty is void if repairs or alterations are performed by parties other than Feliix Inc.

*Feliix Inc. reserves the right to determine whether a product defect is covered by the warranty.";

                $query = "INSERT INTO quotation_term
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


                $title = "Delivery & Lead Time";
                $brief = "Terms and Conditions";
                $list = "Local Production: 30-45 business days
Indent Items from Taiwan: 60-90 business days

*Production will start upon receipt of Purchase Order (P.O.), 50% down payment, and approval of finishes or shop drawings (if necessary). Purchase orders requiring approval of shop drawings and finishes should be completed promptly after receiving the P.O. and down payment.

*Failure to provide timely approval may result in a delay of the production schedule.

*Delivery Charges
Free delivery within Metro Manila.
Delivery charges apply for areas outside Metro Manila.";

                $query = "INSERT INTO quotation_term
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


                $title = "Notes";
                $brief = "";
                $list = "*Weekends, holidays, and/or natural disasters that may cause operational delays are not accounted for in the set lead time. Additionally, lead time may be extended due to the availability of raw materials and/or site conditions.

*Any changes to the design or specifications after the proposal has been approved may result in a price adjustment and extended lead time, as agreed upon by both parties.

*Building freight charges will be charged accordingly to the client.

*Any item deliveries returned due to an unfinished site, preventing installation, will be invoiced for full payment 30 days after the delivery date/acknowledgement of receipt.";

                $query = "INSERT INTO quotation_term
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
    return $result['catagory_id'];

}