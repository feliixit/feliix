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
        
        // insert quotation
        InsertQuotation($id, $user_id, $db);
        
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

function InsertQuotation($id, $user_id, $db)
{
    
    $query = "INSERT INTO quotation_eng(
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
        status, 
        pixa_s,
        show_s,
        pixa_t,
        show_t,
        pixa_p,
        show_p,
        pixa_r,
        show_r,
        pixa_i,
        show_i,
        pixa_c,
        show_c,
        create_id, 
        created_at)
        SELECT 
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
        0,
        pixa_s,
        show_s,
        pixa_t,
        show_t,
        pixa_p,
        show_p,
        pixa_r,
        show_r,
        pixa_i,
        show_i,
        pixa_c,
        show_c,
        :create_id,
        now()
        FROM quotation_eng where status <> -1 and id = :id";
        
        // prepare the query
        $stmt = $db->prepare($query);
        
        $stmt->bindParam(':create_id', $user_id);
        $stmt->bindParam(':id', $id);
        
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
        
        // quotation_eng_payment_term
        $query = "INSERT INTO quotation_eng_payment_term(
            quotation_id,  
            payment_method, 
            brief, 
            list, 
            status, 
            create_id, 
            created_at)
            SELECT 
            $last_id,
            payment_method,
            brief,
            list,
            0,
            :create_id,
            now()
            FROM quotation_eng_payment_term where status <> -1 and quotation_id = :id";
            
        // prepare the query
        $stmt = $db->prepare($query);
        
        // bind the values
        $stmt->bindParam(':id', $id);
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
        
        
        // signature
        $query = "INSERT INTO quotation_eng_signature
        (
            quotation_id,
            type,
            photo,
            name,
            position,
            phone,
            email,
            status,
            `create_id`,
            created_at
            )
            select " . $last_id . ", type, photo, name, position, phone, email, 0, :create_id, now() 
            from quotation_eng_signature where status <> -1 and quotation_id = :quotation_id";
        // prepare the query
        $stmt = $db->prepare($query);
        
        // bind the values
        $stmt->bindParam(':quotation_id', $id);
        $stmt->bindParam(':create_id', $user_id);
        
        try {
            // execute the query, also check if query was successful
            if ($stmt->execute()) {
                //$last_id = $db->lastInsertId();
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
        
        // term
        $query = "INSERT INTO quotation_eng_term
        (
            quotation_id,
            title,
            brief,
            list,
            status,
            `create_id`,
            created_at
            )
            select " . $last_id . ", title, brief, list, 0, :create_id, now() 
            from quotation_eng_term where status <> -1 and quotation_id = :quotation_id";
        // prepare the query
        $stmt = $db->prepare($query);
        
        // bind the values
        $stmt->bindParam(':quotation_id', $id);
        $stmt->bindParam(':create_id', $user_id);
        
        try {
            // execute the query, also check if query was successful
            if ($stmt->execute()) {
                //$last_id = $db->lastInsertId();
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
        
        $query = "INSERT INTO quotation_eng_total(
            quotation_id, 
            discount, 
            vat, 
            show_vat, 
            valid, 
            total, 
            status, 
            create_id, 
            created_at,
            pixa,
            `show`, 
            show_word)
            select
            " . $last_id . ",
            discount,
            vat,
            show_vat,
            valid,
            total,
            0,
            :create_id,
            now(),
            pixa,
            `show`, 
            show_word
            from quotation_eng_total where status <> -1 and quotation_id = :id";
        
        // prepare the query
        $stmt = $db->prepare($query);
        
        // bind the values
        $stmt->bindParam(':id', $id);
        $stmt->bindParam(':create_id', $user_id);
        
        // execute the query, also check if query was successful
        try {
            // execute the query, also check if query was successful
            if ($stmt->execute()) {
                //$last_id = $db->lastInsertId();
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

        $query = "INSERT INTO quotation_eng_general_requirement(
            quotation_id, 
            title, 
            block, 
            status, 
            create_id, 
            created_at)
            select
            " . $last_id . ",
            title,
            block,
            0,
            :create_id,
            now()
            from quotation_eng_general_requirement where status <> -1 and quotation_id = :id";
        
        // prepare the query
        $stmt = $db->prepare($query);
        
        // bind the values
        $stmt->bindParam(':id', $id);
        $stmt->bindParam(':create_id', $user_id);
        
        // execute the query, also check if query was successful
        try {
            // execute the query, also check if query was successful
            if ($stmt->execute()) {
                //$last_id = $db->lastInsertId();
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
        
        $query = "INSERT INTO quotation_eng_consumable(
            quotation_id, 
            title, 
            block, 
            status, 
            create_id, 
            created_at)
            select
            " . $last_id . ",
            title,
            block,
            0,
            :create_id,
            now()
            from quotation_eng_consumable where status <> -1 and quotation_id = :id";
        
        // prepare the query
        $stmt = $db->prepare($query);
        
        // bind the values
        $stmt->bindParam(':id', $id);
        $stmt->bindParam(':create_id', $user_id);
        
        // execute the query, also check if query was successful
        try {
            // execute the query, also check if query was successful
            if ($stmt->execute()) {
                //$last_id = $db->lastInsertId();
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

        
        $query = "INSERT INTO quotation_eng_installation(
            quotation_id, 
            title, 
            block, 
            status, 
            create_id, 
            created_at)
            select
            " . $last_id . ",
            title,
            block,
            0,
            :create_id,
            now()
            from quotation_eng_installation where status <> -1 and quotation_id = :id";
        
        // prepare the query
        $stmt = $db->prepare($query);
        
        // bind the values
        $stmt->bindParam(':id', $id);
        $stmt->bindParam(':create_id', $user_id);
        
        // execute the query, also check if query was successful
        try {
            // execute the query, also check if query was successful
            if ($stmt->execute()) {
                //$last_id = $db->lastInsertId();
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
        