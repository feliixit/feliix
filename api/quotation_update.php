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

        $pageless = (isset($_POST['pageless']) ?  $_POST['pageless'] : '');

        $pages = (isset($_POST['pages']) ?  $_POST['pages'] : '[]');
        $pre_pages = (isset($_POST['pre_pages']) ?  $_POST['pre_pages'] : '[]');
        $pages_array = json_decode($pages,true);


        if ($id == 0) {
            http_response_code(401);
            echo json_encode(array("message" => "Failure at " . date("Y-m-d") . " " . date("h:i:sa") . " " . "Access denied."));
            die();
        }

        $_id = IsExist($id, $db);

        try {

            if ($_id == 0) {
                $query = "INSERT INTO quotation
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
                        `prepare_by_third_line` = :prepare_by_third_line,
                        `footer_first_line` = :footer_first_line,
                        `footer_second_line` = :footer_second_line,
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
                    $stmt->bindParam(':prepare_by_third_line', $prepare_by_third_line);
                    $stmt->bindParam(':footer_first_line', $footer_first_line);
                    $stmt->bindParam(':footer_second_line', $footer_second_line);
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
            }
            else
                {
                // now you can apply
                $query = "update quotation
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
                        `prepare_by_third_line` = :prepare_by_third_line,
                        `footer_first_line` = :footer_first_line,
                        `footer_second_line` = :footer_second_line,
                        `pageless` = :pageless,
                        `updated_id` = :updated_id,
                        `updated_at` = now()
                        where id = :id";

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
                $stmt->bindParam(':prepare_by_third_line', $prepare_by_third_line);
                $stmt->bindParam(':footer_first_line', $footer_first_line);
                $stmt->bindParam(':footer_second_line', $footer_second_line);
                $stmt->bindParam(':pageless', $pageless);
                
                $stmt->bindParam(':updated_id', $user_id);

                $stmt->bindParam(':id', $id);

                $last_id = $id;
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
            }


            // delete previous -1
            $query = "delete from quotation_page 
            WHERE
            `quotation_id` = :quotation_id
            AND `status` = -1 ";

            // prepare the query
            $stmt = $db->prepare($query);

            // bind the values
            $stmt->bindParam(':quotation_id', $last_id);

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

            // quotation_page
            $query = "update quotation_page
                        set `status` = -1
                      WHERE
                      `quotation_id` = :quotation_id";

            // prepare the query
            $stmt = $db->prepare($query);

            // bind the values
            $stmt->bindParam(':quotation_id', $last_id);

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

            // delete previous -1
            $query = "delete from quotation_page_type 
            WHERE
            `quotation_id` = :quotation_id
            AND `status` = -1 ";

            // prepare the query
            $stmt = $db->prepare($query);

            // bind the values
            $stmt->bindParam(':quotation_id', $last_id);

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

            // quotation_page_type
            $query = "update quotation_page_type
                        set `status` = -1
                      WHERE
                      `quotation_id` = :quotation_id";

            // prepare the query
            $stmt = $db->prepare($query);

            // bind the values
            $stmt->bindParam(':quotation_id', $last_id);

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

            // delete previous -1
            $query = "delete from quotation_page_type_block 
            WHERE `quotation_id` = :quotation_id AND `status` = -1 ";

            // prepare the query
            $stmt = $db->prepare($query);

            // bind the values
            $stmt->bindParam(':quotation_id', $last_id);

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

            // quotation_page_type_block
            $query = "insert into quotation_page_type_block(quotation_id, type_id, code, type, photo, qty, price, discount, amount, description, listing, status, create_id, created_at, num, pid, v1, v2, v3, ratio, photo2, photo3, notes) 
            select :quotation_id, type_id, code, type, photo, qty, price, discount, amount, description, listing, -1, create_id, now(), num, pid, v1, v2, v3, ratio, photo2, photo3, notes from quotation_page_type_block where quotation_id = :org_id and status = 0";

            // prepare the query
            $stmt = $db->prepare($query);

            // bind the values
            $stmt->bindParam(':quotation_id', $last_id);
            $stmt->bindParam(':org_id', $id);

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
            


            for($i=0 ; $i < count($pages_array) ; $i++)
            {
                $pg =  $i+1;
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
                
                    // type_id
                    $type_id = 0;
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
                    if($types_array[$j]['org_id'] != 0)
                        UpdateTypeBlock($types_array[$j]['org_id'], $type_id, $db);
                }  
            }

            // insert quotation_update_log
            $query = "INSERT INTO quotation_update_log(quotation_id, user_id, `action`, previous_data, current_data, attachment, create_id, created_at) values(:quotation_id, :user_id, 'page_save', :previous_data, :current_data, '', :create_id, now())";
            $stmt = $db->prepare($query);
            $stmt->bindParam(':quotation_id', $last_id);
            $stmt->bindParam(':user_id', $user_id);
            $stmt->bindParam(':previous_data', $pre_pages);
            $stmt->bindParam(':current_data', $pages);
            $stmt->bindParam(':create_id', $user_id);
            try {
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

            
            $db->commit();

            http_response_code(200);
            echo json_encode(array("message" => "Success at " . date("Y-m-d") . " " . date("h:i:sa")));
        } catch (Exception $e) {

            error_log($e->getMessage());
            $db->rollback();
            http_response_code(501);
            echo json_encode(array("Failure at " . date("Y-m-d") . " " . date("h:i:sa") . " " . $e->getMessage()));
            die();
        }
        break;
}


function IsExist($quotation_id, $db)
{
    $sql = "SELECT id from quotation where id = :quotation_id";
           

    $stmt = $db->prepare($sql);
    $stmt->bindParam(':quotation_id',  $quotation_id);
    $stmt->execute();

    $_id = 0;
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        $_id = $row['id'];
    }

    return $_id;
}

function UpdateTypeBlock($org_id, $new_id, $db){

    $query = "update quotation_page_type_block
    SET type_id = :new_id where type_id=:org_id and status = 0";

    // prepare the query
    $stmt = $db->prepare($query);

    // bind the values
    $stmt->bindParam(':new_id', $new_id);

    $stmt->bindParam(':org_id', $org_id);


    try {
        // execute the query, also check if query was successful
        if ($stmt->execute()) {
      
        }
        else
        {
            $arr = $stmt->errorInfo();
            error_log($arr[2]);
        }
    }
    catch (Exception $e)
    {
        error_log($e->getMessage());
        $db->rollback();
        http_response_code(501);
        echo json_encode(array("Failure at " . date("Y-m-d") . " " . date("h:i:sa") . " " . $e->getMessage()));
        die();
    }
}
