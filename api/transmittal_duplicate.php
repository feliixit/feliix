<?php
error_reporting(E_ALL);
//header("Access-Control-Allow-Origin: https://feliix.myvnc.com");
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: POST");
header("Access-Control-Max-Age: 3600");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

$jwt = (isset($_POST['jwt']) ?  $_POST['jwt'] : null);
$id = (isset($_POST['id']) ?  $_POST['id'] : '');

$id == "" ? $id = 0 : $id = $id;


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

if (!isset($jwt)) {
    http_response_code(401);

    echo json_encode(array("message" => "Access denied."));
    die();
} else {
    try {
        // decode jwt
        $decoded = JWT::decode($jwt, $key, array('HS256'));

        $user_id = $decoded->data->id;

        $merged_results = array();
        // get quotation
        $merged_results = GetQuotation($id, $db);
        
        // insert quotation
        InsertQuotation($id, $user_id, $merged_results, $db);

        $db->commit();
        

        http_response_code(200);
        echo json_encode(array("message" => " Success at " . date("Y-m-d") . " " . date("h:i:sa")));
    } catch (Exception $e) {

        error_log($e->getMessage());
        $db->rollback();
        http_response_code(501);
        echo json_encode(array("Failure at " . date("Y-m-d") . " " . date("h:i:sa") . " " . $e->getMessage()));
        die();
    }
}

function InsertQuotation($id, $user_id, $merged_results, $db)
{
    if(count($merged_results) == 0) {
        return;
    }

    $title = $merged_results[0]['title'];
    $kind = $merged_results[0]['kind'];
    $project_id = $merged_results[0]['project_id'];
    $first_line = $merged_results[0]['first_line'];
    $second_line = $merged_results[0]['second_line'];
    $project_category = $merged_results[0]['project_category'];
    $quotation_no = $merged_results[0]['quotation_no'];
    $quotation_date = $merged_results[0]['quotation_date'];
    $prepare_for_first_line = $merged_results[0]['prepare_for_first_line'];
    $prepare_for_second_line = $merged_results[0]['prepare_for_second_line'];
    $prepare_for_third_line = $merged_results[0]['prepare_for_third_line'];
    $prepare_by_first_line = $merged_results[0]['prepare_by_first_line'];
    $prepare_by_second_line = $merged_results[0]['prepare_by_second_line'];
    $footer_first_line = $merged_results[0]['footer_first_line'];
    $footer_second_line = $merged_results[0]['footer_second_line'];
    $pageless = $merged_results[0]['pageless'];

    $project_name = $merged_results[0]['project_name'];
    $project_location = $merged_results[0]['project_location'];
    $po = $merged_results[0]['po'];
    $request_by = $merged_results[0]['request_by'];
    $request_date = $merged_results[0]['request_date'];
    $submit_by = $merged_results[0]['submit_by'];
    $submit_date = $merged_results[0]['submit_date'];
    $signature_page = $merged_results[0]['signature_page'];
    $signature_pixel = $merged_results[0]['signature_pixel'];

    $transmittal_date = $merged_results[0]['transmittal_date'];
    $transmittal_to = $merged_results[0]['transmittal_to'];
    $transmittal_from = $merged_results[0]['transmittal_from'];
    $transmittal_subject = $merged_results[0]['transmittal_subject'];
    $transmittal_remark = $merged_results[0]['transmittal_remark'];
    $transmittal_purpose = $merged_results[0]['transmittal_purpose'];
    $contact = $merged_results[0]['contact'];

    //$pages_array = $merged_results[0]['pages'];

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
            `signature_page` = :signature_page,
            `signature_pixel` = :signature_pixel,

            `transmittal_date` = :transmittal_date,
            `transmittal_to` = :transmittal_to,
            `transmittal_from` = :transmittal_from,
            `transmittal_subject` = :transmittal_subject,
            `transmittal_remark` = :transmittal_remark,
            `transmittal_purpose` = :transmittal_purpose,

            `contact` = :contact,

            `pageless` = :pageless,

            `status` = 0,
            `create_id` = :create_id,
            `created_at` =  now() ";

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
        $stmt->bindParam(':signature_page', $signature_page);
        $stmt->bindParam(':signature_pixel', $signature_pixel);

        $stmt->bindParam(':transmittal_date', $transmittal_date);
        $stmt->bindParam(':transmittal_to', $transmittal_to);
        $stmt->bindParam(':transmittal_from', $transmittal_from);
        $stmt->bindParam(':transmittal_subject', $transmittal_subject);
        $stmt->bindParam(':transmittal_remark', $transmittal_remark);
        $stmt->bindParam(':transmittal_purpose', $transmittal_purpose);

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

        // insert quotation_page_type_block
        $query = "INSERT INTO transmittal_page_type_block(`quotation_id`, `type_id`, `code`, `type`, `qty`, `unit`, `ratio`, `price`, `discount`, `amount`, `description`, `v1`, `v2`, `v3`, `photo`, `photo2`, `photo3`, `notes`, `listing`, `status`, `num`, `pid`, `create_id`, `created_at`)
        SELECT " . $last_id . ", `type_id`, `code`, `type`, `qty`, `unit`, `ratio`, `price`, `discount`, `amount`, `description`, `v1`, `v2`, `v3`, `photo`, `photo2`, `photo3`, `notes`, `listing`, `status`, `num`, `pid`, " . $user_id . ", NOW() FROM transmittal_page_type_block WHERE `quotation_id` = " . $id . " and `status` <> -1";

        // prepare the query
        $stmt = $db->prepare($query);
        
        try {
            // execute the query, also check if query was successful
            if ($stmt->execute()) {
                $block_id = $db->lastInsertId();
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

function GetQuotation($id, $db) {
    $merged_results = array();
    

    $query = "SELECT id, 
                    q_id,
                    title,
                    kind,
                    project_id,
                    first_line, 
                    second_line, 
                    project_category, 
                    quotation_no, 
                    quotation_date, 
                    prepare_for_first_line, 
                    prepare_for_second_line, 
                    prepare_for_third_line,
                    prepare_by_first_line,
                    prepare_by_second_line,
                    footer_first_line,
                    footer_second_line,
                    project_name,
                    project_location,
                    po,
                    request_by,
                    request_date,
                    submit_by,
                    submit_date,
                    signature_page,
                    signature_pixel,
                    1 page_count,
                    pageless,
                    `transmittal_date`,
                    `transmittal_to`,
                    `transmittal_from`,
                    `transmittal_subject`,
                    `transmittal_remark`,
                    `transmittal_purpose`,
                    `contact`
                    FROM transmittal
                    WHERE status <> -1 and id=$id";


    $query = $query . " order by transmittal.created_at desc ";

    $stmt = $db->prepare($query);
    $stmt->execute();

    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        $id = $row['id'];
        $q_id = $row['q_id'];
        $title = $row['title'];
        $kind = $row['kind'];
        $project_id = $row['project_id'];
        $first_line = $row['first_line'];
        $second_line = $row['second_line'];
        $project_category = $row['project_category'];
        $quotation_no = $row['quotation_no'];
        $quotation_date = $row['quotation_date'];
        $prepare_for_first_line = $row['prepare_for_first_line'];
        $prepare_for_second_line = $row['prepare_for_second_line'];
        $prepare_for_third_line = $row['prepare_for_third_line'];
        $prepare_by_first_line = $row['prepare_by_first_line'];
        $prepare_by_second_line = $row['prepare_by_second_line'];
        $footer_first_line = $row['footer_first_line'];
        $footer_second_line = $row['footer_second_line'];

        $transmittal_date = $row['transmittal_date'];
        $transmittal_to = $row['transmittal_to'];
        $transmittal_from = $row['transmittal_from'];
        $transmittal_subject = $row['transmittal_subject'];
        $transmittal_remark = $row['transmittal_remark'];
        $transmittal_purpose = $row['transmittal_purpose'];
        $contact = $row['contact'];

        $project_name = $row['project_name'];
        $project_location = $row['project_location'];
        $po = $row['po'];
        $request_by = $row['request_by'];
        $request_date = $row['request_date'];
        $submit_by = $row['submit_by'];
        $submit_date = $row['submit_date'];
        $signature_page = $row['signature_page'];
        $signature_pixel = $row['signature_pixel'];

        $page_count = $row['page_count'];

        $pageless = $row['pageless'];
        $merged_results[] = array(
            "id" => $id,
            "title" => $title,
            "kind" => $kind,
            "project_id" => $project_id,
            "first_line" => $first_line,
            "second_line" => $second_line,
            "project_category" => $project_category,
            "quotation_no" => $quotation_no,
            "quotation_date" => $quotation_date,
            "prepare_for_first_line" => $prepare_for_first_line,
            "prepare_for_second_line" => $prepare_for_second_line,
            "prepare_for_third_line" => $prepare_for_third_line,
            "prepare_by_first_line" => $prepare_by_first_line,
            "prepare_by_second_line" => $prepare_by_second_line,
            "footer_first_line" => $footer_first_line,
            "footer_second_line" => $footer_second_line,
            "project_name" => $project_name,
            "project_location" => $project_location,
            "po" => $po,
            "request_by" => $request_by,
            "request_date" => $request_date,
            "submit_by" => $submit_by,
            "submit_date" => $submit_date,
            "signature_page" => $signature_page,
            "signature_pixel" => $signature_pixel,
            "page_count" => $page_count,

            "pageless" => $pageless,

            "transmittal_date" => $transmittal_date,
            "transmittal_to" => $transmittal_to,
            "transmittal_from" => $transmittal_from,
            "transmittal_subject" => $transmittal_subject,
            "transmittal_remark" => $transmittal_remark,
            "transmittal_purpose" => $transmittal_purpose,
            "contact" => $contact,
        );
    }

    return $merged_results;
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
