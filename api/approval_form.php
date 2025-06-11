<?php
error_reporting(0);
//header("Access-Control-Allow-Origin: https://feliix.myvnc.com");
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: *");
header("Access-Control-Max-Age: 3600");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

$id = (isset($_GET['id']) ?  $_GET['id'] : 0);
$qid = (isset($_GET['qid']) ?  $_GET['qid'] : 0);

$jwt = (isset($_COOKIE['jwt']) ?  $_COOKIE['jwt'] : null);

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
$conf = new Conf();

use Google\Cloud\Storage\StorageClient;

use \Firebase\JWT\JWT;

if (!isset($jwt)) {
    http_response_code(401);

    echo json_encode(array("message" => "Access denied1."));
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

    

    if($qid != 0 && $id == 0)
    {
        $user_id = $decoded->data->id;

        $merged_results = array();
        // get quotation
        $merged_results = GetQuotation($qid, $db, "");
        
        // insert quotation
        $new_id = InsertQuotation($qid, $user_id, $merged_results, $db);

        $id = $new_id;
    }

    $merged_results = array();
    
    $prefix = "approval_form_";

    $query = "SELECT id, 
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
                    (SELECT COUNT(*) FROM " . $prefix . "quotation_page WHERE quotation_id = " . $prefix . "quotation.id and " . $prefix . "quotation_page.status <> -1) page_count
                    FROM " . $prefix . "quotation
                    WHERE status <> -1 and id=$id";


    $query = $query . " order by " . $prefix . "quotation.created_at desc ";

    if (!empty($_GET['page'])) {
        $page = filter_input(INPUT_GET, 'page', FILTER_VALIDATE_INT);
        if (false === $page) {
            $page = 1;
        }
    }

    if (!empty($_GET['size'])) {
        $size = filter_input(INPUT_GET, 'size', FILTER_VALIDATE_INT);
        if (false === $size) {
            $size = 10;
        }

        $offset = ($page - 1) * $size;

        $query = $query . " LIMIT " . $offset . "," . $size;
    }

    $stmt = $db->prepare($query);
    $stmt->execute();

    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        $id = $row['id'];
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
        $page_count = $row['page_count'];
        $pages = GetPages($row['id'], $db, $prefix);

        $block_names = GetBlockNames($row['id'], $db, $prefix);
        $total_info = GetTotalInfo($row['id'], $db, $prefix);
        $term_info = GetTermInfo($row['id'], $db, $prefix);
        $payment_term_info = GetPaymentTermInfo($row['id'], $db, $prefix);
        $sig_info = GetSigInfo($row['id'], $db, $prefix);

        $subtotal_info = GetSubTotalInfo($row['id'], $db, $prefix);
        $subtotal_novat_a = GetSubTotalNoVatA($row['id'], $db, $prefix);
        $subtotal_novat_b = GetSubTotalNoVatB($row['id'], $db, $prefix);

        $subtotal_info_not_show_a = GetSubTotalInfoNotShowA($row['id'], $db, $prefix);
        $subtotal_info_not_show_b = GetSubTotalInfoNotShowB($row['id'], $db, $prefix);

        $total_info['back_total'] = ($subtotal_info_not_show_a + $subtotal_info_not_show_b) * (100 - $total_info['discount']) / 100;
        if($total_info['vat'] == 'Y')
            $total_info['back_total'] += $subtotal_info_not_show_a * (100 - $total_info['discount']) / 100 * 0.12;

        $total_info['back_total'] = number_format($total_info['back_total'], 2, '.', '');

        $project_name = $row['project_name'];
        $project_location = $row['project_location'];
        $po = $row['po'];
        $request_by = $row['request_by'];
        $request_date = $row['request_date'];
        $submit_by = $row['submit_by'];
        $submit_date = $row['submit_date'];
        $signature_page = $row['signature_page'];
        $signature_pixel = $row['signature_pixel'];

        $sig_info['pixel'] = $signature_pixel;

        // print
        $product_array = GetProductItems($pages, $row['id'], $db, $prefix);

        $merged_results[] = array(
            "id" => $id,
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
            "page_count" => $page_count,
            "pages" => $pages,
            "block_names" => $block_names,
            "total_info" => $total_info,
            "term_info" => $term_info,
            "payment_term_info" => $payment_term_info,
            "sig_info" => $sig_info,
            "subtotal_info" => $subtotal_info,

            "subtotal_novat_a" => $subtotal_novat_a,
            "subtotal_novat_b" => $subtotal_novat_b,
            "subtotal_info_not_show_a" => $subtotal_info_not_show_a,
            "subtotal_info_not_show_b" => $subtotal_info_not_show_b,

            "project_name" => $project_name,
            "project_location" => $project_location,
            "po" => $po,
            "request_by" => $request_by,
            "request_date" => $request_date,
            "submit_by" => $submit_by,
            "submit_date" => $submit_date,
            "signature_page" => $signature_page,
            "signature_pixel" => $signature_pixel,

            "product_array" => $product_array,
        );
    }



    echo json_encode($merged_results, JSON_UNESCAPED_SLASHES);
}


function InsertQuotation($id, $user_id, $merged_results, $db)
{
    if(count($merged_results) == 0) {
        return;
    }

    $qid = 0;

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

    $project_name = $merged_results[0]['first_line'] . ' ' . $merged_results[0]['second_line'];
    $project_name = trim($project_name);

    $pages_array = $merged_results[0]['pages'];

    $query = "INSERT INTO approval_form_quotation
        SET
            `q_id` = :qid,
            `title` = :title,
            `kind` = :kind,
            `project_id` = :project_id,
            `project_name` = :project_name,
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

            `status` = 0,
            `create_id` = :create_id,
            `created_at` =  now() ";

        // prepare the query
        $stmt = $db->prepare($query);

        // bind the values
        $stmt->bindParam(':qid', $id);
        $stmt->bindParam(':title', $title);
        $stmt->bindParam(':kind', $kind);
        $stmt->bindParam(':project_id', $project_id);
        $stmt->bindParam(':project_name', $project_name);
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

        $stmt->bindParam(':create_id', $user_id);

        $last_id = 0;
        // execute the query, also check if query was successful
        try {
            // execute the query, also check if query was successful
            if ($stmt->execute()) {
                $last_id = $db->lastInsertId();
                $qid = $last_id;
            } else {
                $arr = $stmt->errorInfo();
                error_log($arr[2]);

                http_response_code(501);
                echo json_encode("Failure at " . date("Y-m-d") . " " . date("h:i:sa") . " " . $arr[2]);
                die();
            }
        } catch (Exception $e) {
            error_log($e->getMessage());

            http_response_code(501);
            echo json_encode(array("Failure at " . date("Y-m-d") . " " . date("h:i:sa") . " " . $e->getMessage()));
            die();
        }

        // pages
        for($i=0 ; $i < count($pages_array) ; $i++)
        {
            $pg = $i + 1;
            // insert quotation_page
            $query = "INSERT INTO approval_form_quotation_page
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

                    http_response_code(501);
                    echo json_encode("Failure at " . date("Y-m-d") . " " . date("h:i:sa") . " " . $arr[2]);
                    die();
                }
            } catch (Exception $e) {
                error_log($e->getMessage());
                
                http_response_code(501);
                echo json_encode(array("Failure at " . date("Y-m-d") . " " . date("h:i:sa") . " " . $e->getMessage()));
                die();
            }

            $types_array = $pages_array[$i]['types'];
            for($j=0; $j < count($types_array); $j++)
            {
                $query = "INSERT INTO approval_form_quotation_page_type
                SET
                    `quotation_id` = :quotation_id,
                    `page_id` = :page_id,
                    `block_type` = :block_type,
                    `block_name` = :block_name,
                    `not_show` = :not_show,
                    `real_amount` = :real_amount,
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

                $stmt->bindParam(':not_show', $types_array[$j]['not_show']);
                $stmt->bindParam(':real_amount', $types_array[$j]['real_amount']);
              
                $stmt->bindParam(':create_id', $user_id);
            
                $type_id = 0;

                try {
                    // execute the query, also check if query was successful
                    if (!$stmt->execute()) {
                        $arr = $stmt->errorInfo();
                        error_log($arr[2]);
                        
                        http_response_code(501);
                        echo json_encode("Failure at " . date("Y-m-d") . " " . date("h:i:sa") . " " . $arr[2]);
                        die();
                    }
                    else
                        $type_id = $db->lastInsertId();
                } catch (Exception $e) {
                    error_log($e->getMessage());
                    
                    http_response_code(501);
                    echo json_encode(array("Failure at " . date("Y-m-d") . " " . date("h:i:sa") . " " . $e->getMessage()));
                    die();
                }

                $block_array = $types_array[$j]["blocks"];
                for($k=0 ; $k < count($block_array) ; $k++)
                {
                    // insert quotation_page_type_block
                    $query = "INSERT INTO approval_form_quotation_page_type_block
                    SET
                        `quotation_id` = :quotation_id,
                        `type_id` = :type_id,
                        `code` = :code,
                        `type` = :type,
                        `qty` = :qty,
                        `ratio` = :ratio,
                        `price` = :price,
                        `discount` = :discount,
                        `amount` = :amount,
                        `description` = :description,
                        `v1` = :v1,
                        `v2` = :v2,
                        `v3` = :v3,
                        `v4` = :v4,
                        `ps_var` = :ps_var,
                        `photo` = :photo,
                        `photo2` = :photo2,
                        `photo3` = :photo3,
                        `notes` = :notes,
                        `listing` = :listing,
                        `status` = 0,
                        `num` = :num,
                        `pid` = :pid,
                        `create_id` = :create_id,
                        `created_at` = now()";

                    // prepare the query
                    $stmt = $db->prepare($query);

                    $qty = isset($block_array[$k]['qty']) ? $block_array[$k]['qty'] : 0;
                    $ratio = isset($block_array[$k]['ratio']) ? $block_array[$k]['ratio'] : 0;
                    $price = isset($block_array[$k]['price']) ? $block_array[$k]['price'] : 0;
                    $discount = isset($block_array[$k]['discount']) ? $block_array[$k]['discount'] : 0;
                    $amount = isset($block_array[$k]['amount']) ? $block_array[$k]['amount'] : 0;
                    $description = isset($block_array[$k]['desc']) ? $block_array[$k]['desc'] : '';
                    $v1 = isset($block_array[$k]['v1']) ? $block_array[$k]['v1'] : '';
                    $v2 = isset($block_array[$k]['v2']) ? $block_array[$k]['v2'] : '';
                    $v3 = isset($block_array[$k]['v3']) ? $block_array[$k]['v3'] : '';
                    $v4 = isset($block_array[$k]['v4']) ? $block_array[$k]['v4'] : '';

                    $ps_var = isset($block_array[$k]['ps_var']) ? $block_array[$k]['ps_var'] : [];
                    $json_ps_var = json_encode($ps_var);

                    $listing = isset($block_array[$k]['list']) ? $block_array[$k]['list'] : '';

                    $notes = isset($block_array[$k]['notes']) ? $block_array[$k]['notes'] : '';

                    $qty == '' ? $qty = 0 : $qty = $qty;
                    $ratio == '' ? $ratio = 0 : $ratio = $ratio;
                    $price == '' ? $price = 0 : $price = $price;
                    $discount == '' ? $discount = 0 : $discount = $discount;
                    $amount == '' ? $amount = 0 : $amount = $amount;

                    // bind the values
                    $stmt->bindParam(':quotation_id', $last_id);
                    $stmt->bindParam(':type_id', $type_id);
                    $stmt->bindParam(':code', $block_array[$k]['code']);
                    $stmt->bindParam(':type', $block_array[$k]['type']);
                    $stmt->bindParam(':qty', $qty);
                    $stmt->bindParam(':ratio', $ratio);
                    $stmt->bindParam(':price', $price);
                    $stmt->bindParam(':discount', $discount);
                    $stmt->bindParam(':amount', $amount);
                    $stmt->bindParam(':description', $description);
                    $stmt->bindParam(':v1', $v1);
                    $stmt->bindParam(':v2', $v2);
                    $stmt->bindParam(':v3', $v3);
                    $stmt->bindParam(':v4', $v4);
                    $stmt->bindParam(':ps_var', $json_ps_var);
                    $stmt->bindParam(':listing', $listing);
                    
                    $stmt->bindParam(':create_id', $user_id);
                 
                    $stmt->bindParam(':photo', $block_array[$k]['photo']);
                    $stmt->bindParam(':photo2', $block_array[$k]['photo2']);
                    $stmt->bindParam(':photo3', $block_array[$k]['photo3']);
                    $stmt->bindParam(':notes', $notes);

                    $stmt->bindParam(':num', $block_array[$k]['num']);
                    $stmt->bindParam(':pid', $block_array[$k]['pid']);
                    
                
                    $block_id = 0;
                    try {
                        // execute the query, also check if query was successful
                        if ($stmt->execute()) {
                            $block_id = $db->lastInsertId();
                        } else {
                            $arr = $stmt->errorInfo();
                            error_log($arr[2]);
                            
                            http_response_code(501);
                            echo json_encode("Failure at " . date("Y-m-d") . " " . date("h:i:sa") . " " . $arr[2]);
                            die();
                        }
                    } catch (Exception $e) {
                        error_log($e->getMessage());
                        
                        http_response_code(501);
                        echo json_encode(array("Failure at " . date("Y-m-d") . " " . date("h:i:sa") . " " . $e->getMessage()));
                        die();
                    }

                
                }
            }  
        }

        $quotation_id = $last_id;
        $total = $merged_results[0]['total_info'];

        $query = "INSERT INTO approval_form_quotation_total
            SET
                `quotation_id` = :quotation_id,
                `page` = :page,
                `discount` = :discount,
                `vat` = :vat,
                `show_vat` = :show_vat,
                `valid` = :valid,
                `total` = :total,
            
                `status` = 0,
                `create_id` = :create_id,
                `created_at` =  now() ";

            // prepare the query
            $stmt = $db->prepare($query);

            $tt = $total["total"] == '' ? 0 : $total["total"];
            // bind the values
            $stmt->bindParam(':quotation_id', $quotation_id);
            $stmt->bindParam(':page', $total["page"]);
            $stmt->bindParam(':discount', $total["discount"]);
            $stmt->bindParam(':vat', $total["vat"]);
            $stmt->bindParam(':show_vat', $total["show_vat"]);
            $stmt->bindParam(':valid', $total["valid"]);
            $stmt->bindParam(':total', $tt);

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
                    
                    http_response_code(501);
                    echo json_encode("Failure at " . date("Y-m-d") . " " . date("h:i:sa") . " " . $arr[2]);
                    die();
                }
            } catch (Exception $e) {
                error_log($e->getMessage());
                
                http_response_code(501);
                echo json_encode(array("Failure at " . date("Y-m-d") . " " . date("h:i:sa") . " " . $e->getMessage()));
                die();
            }

            // term
            $query = "INSERT INTO approval_form_quotation_term
                    (
                        quotation_id,
                        page,
                        title,
                        brief,
                        list,
                        `create_id`,
                        created_at
                    )
                        select " . $quotation_id . ", page, title, brief, list, :create_id, now() 
                    from quotation_term where quotation_id = :quotation_id";
            // prepare the query
            $stmt = $db->prepare($query);

            // bind the values
            $stmt->bindParam(':quotation_id', $id);
            $stmt->bindParam(':create_id', $user_id);

            try {
                // execute the query, also check if query was successful
                if ($stmt->execute()) {
                    $last_id = $db->lastInsertId();
                } else {
                    $arr = $stmt->errorInfo();
                    error_log($arr[2]);
                    
                    http_response_code(501);
                    echo json_encode("Failure at " . date("Y-m-d") . " " . date("h:i:sa") . " " . $arr[2]);
                    die();
                }
            } catch (Exception $e) {
                error_log($e->getMessage());
                
                http_response_code(501);
                echo json_encode(array("Failure at " . date("Y-m-d") . " " . date("h:i:sa") . " " . $e->getMessage()));
                die();
            }

            // payment term
            $query = "INSERT INTO approval_form_quotation_payment_term
                    (
                        quotation_id,
                        page,
                        payment_method,
                        brief,
                        list,
                        `create_id`,
                        created_at
                    )
                        select " . $quotation_id . ", page, payment_method, brief, list, :create_id, now() 
                    from quotation_payment_term where quotation_id = :quotation_id";
            // prepare the query
            $stmt = $db->prepare($query);

            // bind the values
            $stmt->bindParam(':quotation_id', $id);
            $stmt->bindParam(':create_id', $user_id);
              
            try {
                // execute the query, also check if query was successful
                if ($stmt->execute()) {
                    $last_id = $db->lastInsertId();
                } else {
                    $arr = $stmt->errorInfo();
                    error_log($arr[2]);
                    
                    http_response_code(501);
                    echo json_encode("Failure at " . date("Y-m-d") . " " . date("h:i:sa") . " " . $arr[2]);
                    die();
                }
            } catch (Exception $e) {
                error_log($e->getMessage());
                
                http_response_code(501);
                echo json_encode(array("Failure at " . date("Y-m-d") . " " . date("h:i:sa") . " " . $e->getMessage()));
                die();
            }

            // signature
            $query = "INSERT INTO approval_form_quotation_signature
                    (
                        quotation_id,
                        page,
                        type,
                        photo,
                        name,
                        position,
                        phone,
                        email,
                        `create_id`,
                        created_at
                    )
                    
                        select " . $quotation_id . ", page, type, photo, name, position, phone, email, :create_id, now() 
                    from quotation_signature where quotation_id = :quotation_id";
            // prepare the query
            $stmt = $db->prepare($query);

            // bind the values
            $stmt->bindParam(':quotation_id', $id);
            $stmt->bindParam(':create_id', $user_id);
              
            try {
                // execute the query, also check if query was successful
                if ($stmt->execute()) {
                    $last_id = $db->lastInsertId();
                } else {
                    $arr = $stmt->errorInfo();
                    error_log($arr[2]);
                    
                    http_response_code(501);
                    echo json_encode("Failure at " . date("Y-m-d") . " " . date("h:i:sa") . " " . $arr[2]);
                    die();
                }
            } catch (Exception $e) {
                error_log($e->getMessage());
                
                http_response_code(501);
                echo json_encode(array("Failure at " . date("Y-m-d") . " " . date("h:i:sa") . " " . $e->getMessage()));
                die();
            }

            return $qid;
}

function GetQuotation($id, $db, $prefix) {
    $merged_results = array();
    

    $query = "SELECT id, 
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
                    (SELECT COUNT(*) FROM " . $prefix . "quotation_page WHERE quotation_id = " . $prefix . "quotation.id and " . $prefix . "quotation_page.status <> -1) page_count,
                    pageless
                    FROM " . $prefix . "quotation
                    WHERE status <> -1 and id=$id";


    $query = $query . " order by " . $prefix . "quotation.created_at desc ";

    $stmt = $db->prepare($query);
    $stmt->execute();

    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        $id = $row['id'];
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
        $page_count = $row['page_count'];
        $pages = GetPages($row['id'], $db, $prefix);

        $block_names = GetBlockNames($row['id'], $db, $prefix);
        $total_info = GetTotalInfo($row['id'], $db, $prefix);
        $term_info = GetTermInfo($row['id'], $db, $prefix);
        $sig_info = GetSigInfo($row['id'], $db, $prefix);

        $pageless = $row['pageless'];

        $subtotal_info = GetSubTotalInfo($row['id'], $db, $prefix);

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
            "page_count" => $page_count,
            "pages" => $pages,
            "block_names" => $block_names,
            "total_info" => $total_info,
            "term_info" => $term_info,
            "sig_info" => $sig_info,
            "subtotal_info" => $subtotal_info,
            "pageless" => $pageless
        );
    }

    return $merged_results;
}

function GetSubTotalInfo($qid, $db, $prefix)
{
    $total = 0;

    $query = "
            select COALESCE(sum(amount), 0) amt from " . $prefix . "quotation_page_type_block
            WHERE `status` <> -1 and  type_id in (select id from " . $prefix . "quotation_page_type where quotation_id = " . $qid . " and not_show = '' and real_amount = 0 and status <> -1)
            union all
            select COALESCE(sum(real_amount), 0) from " . $prefix . "quotation_page_type where quotation_id = " . $qid . " and not_show = '' and real_amount <> 0 and status <> -1
    ";

    // prepare the query
    $stmt = $db->prepare($query);
    $stmt->execute();

    $merged_results = [];

    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
  
        $total += $row['amt'];

    }

    return $total;
}

function GetSubTotalInfoNotShowA($qid, $db, $prefix)
{
    $total = 0;

    $query = "
            select COALESCE(sum(amount), 0) amt from " . $prefix . "quotation_page_type_block
            WHERE `status` <> -1 and type_id in (select id from " . $prefix . "quotation_page_type where quotation_id = " . $qid . " and not_show = '' and block_type = 'A'  and real_amount = 0 and status <> -1)
            union all
            select COALESCE(sum(real_amount), 0) from " . $prefix . "quotation_page_type where quotation_id = " . $qid . " and not_show = '' and real_amount <> 0 and block_type = 'A'  and status <> -1
    ";

    // prepare the query
    $stmt = $db->prepare($query);
    $stmt->execute();

    $merged_results = [];

    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
  
        $total += $row['amt'];

    }

    return $total;
}


function GetSubTotalInfoNotShowB($qid, $db, $prefix)
{
    $total = 0;

    $query = "
            select COALESCE(sum(amount), 0) amt from " . $prefix . "quotation_page_type_block
            WHERE `status` <> -1 and type_id in (select id from " . $prefix . "quotation_page_type where quotation_id = " . $qid . " and not_show = '' and block_type = 'B'  and real_amount = 0 and status <> -1)
            union all
            select COALESCE(sum(real_amount), 0) from " . $prefix . "quotation_page_type where quotation_id = " . $qid . " and not_show = '' and real_amount <> 0 and block_type = 'B'  and status <> -1
    ";

    // prepare the query
    $stmt = $db->prepare($query);
    $stmt->execute();

    $merged_results = [];

    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
  
        $total += $row['amt'];

    }

    return $total;
}

function GetSubTotalNoVat($qid, $db, $prefix)
{
    $total = 0;

    $query = "
            select sum(qty * price * (1 - discount / 100) * ratio) amt from " . $prefix . "quotation_page_type_block
            WHERE `status` <> -1 and type_id in (select id from " . $prefix . "quotation_page_type where quotation_id = " . $qid . " and not_show = ''  and status <> -1)
    ";

    // prepare the query
    $stmt = $db->prepare($query);
    $stmt->execute();

    $merged_results = [];

    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
  
        $total = $row['amt'];

    }

    return $total;
}


function GetSubTotalNoVatA($qid, $db, $prefix)
{
    $total = 0;

    $query = "
            select sum(qty * price * (1 - discount / 100) * ratio) amt from " . $prefix . "quotation_page_type_block
            WHERE `status` <> -1 and type_id in (select id from " . $prefix . "quotation_page_type where quotation_id = " . $qid . " and not_show = '' and block_type = 'A' and status <> -1)
    ";

    // prepare the query
    $stmt = $db->prepare($query);
    $stmt->execute();

    $merged_results = [];

    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
  
        $total = $row['amt'];

    }

    return $total;
}


function GetSubTotalNoVatB($qid, $db, $prefix)
{
    $total = 0;

    $query = "
            select sum(qty * price * (1 - discount / 100) * ratio) amt from " . $prefix . "quotation_page_type_block
            WHERE `status` <> -1 and type_id in (select id from " . $prefix . "quotation_page_type where quotation_id = " . $qid . " and not_show = '' and block_type = 'B' and status <> -1)
    ";

    // prepare the query
    $stmt = $db->prepare($query);
    $stmt->execute();

    $merged_results = [];

    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
  
        $total = $row['amt'];

    }

    return $total;
}

function GetSubTotalNoVatNotShow($qid, $db, $prefix)
{
    $total = 0;

    $query = "
            select COALESCE(sum(qty * ratio * price * (1 - discount / 100)), 0) amt from " . $prefix . "quotation_page_type_block
            WHERE `status` <> -1 and type_id in (select id from " . $prefix . "quotation_page_type where quotation_id = " . $qid . " and not_show = '' and status <> -1)
    ";

    // prepare the query
    $stmt = $db->prepare($query);
    $stmt->execute();

    $merged_results = [];

    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
  
        $total = $row['amt'];

    }

    return $total;
}


function GetBlockNames($qid, $db, $prefix){
    $query = "
            SELECT qpt.id,
                qp.page,
                block_type,
                block_name,
                not_show,
                real_amount,
                page_id
            FROM  " . $prefix . "quotation_page_type qpt
            left join " . $prefix . "quotation_page qp on qpt.page_id = qp.id
            WHERE  qpt.quotation_id = " . $qid . "
            AND qpt.`status` <> -1 
            and qp.`status` <> -1
            ORDER BY qpt.id
    ";

    // prepare the query
    $stmt = $db->prepare($query);
    $stmt->execute();

    $merged_results = [];

    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        $id = $row['id'];
        $page = $row['page'];
        $block_type = $row['block_type'];
        $block_name = $row['block_name'];
        $page_id = $row['page_id'];

        $not_show = $row['not_show'];
        $real_amount = $row['real_amount'];

        $blocks = [];

        $blocks = GetBlocks($id, $db, $prefix);
        $subtotal = GetSubtotal($blocks);

        $merged_results[] = array(
            "id" => $id,
            "page" => $page,
            "type" => $block_type,
            "name" => $block_name,
            "not_show" => $not_show,
            "real_amount" => $real_amount,
            "blocks" => $blocks,
            "page_id" => $page_id,
            "subtotal" => $subtotal,
        );
    }

    return $merged_results;
}

function GetSubtotal($ary)
{
    $total = 0;
    foreach ($ary as $item) {
        $total += $item['amount'];
    }

    return $total;
}

function GetPages($qid, $db, $prefix){
    $query = "
        SELECT id,
            page
        FROM   " . $prefix . "quotation_page
        WHERE  quotation_id = " . $qid . "
        AND `status` <> -1 and create_id is not null
        ORDER BY id
    ";

    // prepare the query
    $stmt = $db->prepare($query);
    $stmt->execute();

    $merged_results = [];

    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        $id = $row['id'];
        $page = $row['page'];
        $type = GetTypes($id, $db, $prefix);
        $total = GetTotal($qid, $page, $db, $prefix);
        $term = GetTerm($qid, $page, $db, $prefix);
        $payment_term = GetPaymentTerm($qid, $page, $db, $prefix);
        $sig = GetSig($qid, $page, $db, $prefix);
  
        $merged_results[] = array(
            "id" => $id,
            "page" => $page,
            "types" => $type,
            "total" => $total,
            "term" => $term,
            "payment_term" => $payment_term,
            "sig" => $sig,
        );
    }

    return $merged_results;
}

function GetTotal($qid, $page, $db, $prefix){
    $query = "
        SELECT 
        `page`,
        discount,
        vat,
        show_vat,
        valid,
        total
        FROM " . $prefix . "quotation_total
        WHERE  quotation_id = " . $qid . "
        AND  page = " . $page . "
        AND `status` <> -1 
        ORDER BY id
    ";

    // prepare the query
    $stmt = $db->prepare($query);
    $stmt->execute();

    $merged_results = [];
    
    $discount = 0;

    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        $page = $row['page'];
        $discount = $row['discount'];
        $vat = $row['vat'];
        $show_vat = $row['show_vat'];
        $valid = $row['valid'];
        $total = $row['total'];

        $merged_results[] = array(
            "page" => $page,
            "discount" => $discount,
            "vat" => $vat,
            "show_vat" => $show_vat,
            "valid" => $valid,
            "total" => $total,
            
            
        );
    }

    return $merged_results;
}


function GetTerm($qid, $page, $db, $prefix){
    $query = "
        SELECT 
        `page`,
        title,
        brief,
        list 
        FROM " . $prefix . "quotation_term
        WHERE  quotation_id = " . $qid . "
        AND  page = " . $page . "
        AND `status` <> -1 
        ORDER BY id
    ";

    // prepare the query
    $stmt = $db->prepare($query);
    $stmt->execute();

    $merged_results = [];
    

    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        $page = $row['page'];
        $title = $row['title'];
        $brief = $row['brief'];
        $list = $row['list'];
    

        $merged_results[] = array(
            "page" => $page,
            "title" => $title,
            "brief" => $brief,
            "list" => $list,
          
        );
    }

    return $merged_results;
}


function GetPaymentTerm($qid, $page, $db, $prefix){
    $query = "
        SELECT 
        `page`,
        payment_method,
        brief,
        list 
        FROM " . $prefix . "quotation_payment_term
        WHERE  quotation_id = " . $qid . "
        AND  page = " . $page . "
        AND `status` <> -1 
        ORDER BY id
    ";

    // prepare the query
    $stmt = $db->prepare($query);
    $stmt->execute();

    $merged_results = [];
    

    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        $page = $row['page'];
        $payment = $row['payment_method'];
        $brief = $row['brief'];
        $list = $row['list'];
    
        $payment_method = explode (";", $payment);
        $payment_method= array_filter($payment_method);
        $payment_method = array_map('trim', $payment_method);
        $item = json_decode($list, TRUE); 

        $merged_results = array(
            "page" => $page,
            "payment_method" => $payment_method,
            "brief" => $brief,
            "list" => $item,
          
        );
    }

    return $merged_results;
}


function GetSigInfo($qid, $db, $prefix)
{
    $query = "
        SELECT 
        id,
        `page`,
        `type`,
        `name`,
        `photo`,
        position,
        phone,
        email 
        FROM " . $prefix . "quotation_signature
        WHERE  quotation_id = " . $qid . "
        AND `status` <> -1 
        ORDER BY id
    ";

    // prepare the query
    $stmt = $db->prepare($query);
    $stmt->execute();

    $item_client = [];
    $item_company = [];

    $merged_results = [];
    
    $id = 0;
    $page = 0;
    $type = '';
    $name = '';
    $photo = '';
    $url = '';
    $position = '';
    $phone = '';
    $email = '';
  
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        $id = $row['id'];
        $page = $row['page'];
        $type = $row['type'];
        $name = $row['name'];
        $photo = $row['photo'];
        $position = $row['position'];
        $phone = $row['phone'];
        $email = $row['email'];
       
        if($type == 'C'){
            $item_client[] = array(
                "id" => $id,
                "type" => $type,
                "photo" => '',
                "url" => '',
                "name" => $name,
                "position" => $position,
                "phone" => $phone,
                "email" => $email,
            );
        }
        
        if($type ==  'F'){
            $item_company[] = array(
                "id" => $id,
                "type" => $type,
                "photo" => $photo,
                "url" =>  $photo != '' ? 'https://storage.googleapis.com/feliiximg/' . $photo : '',
                "name" => $name,
                "position" => $position,
                "phone" => $phone,
                "email" => $email,
            );
        }
        
    }

    $merged_results = array(
        "page" => $page,
        "item_client" => $item_client,
        "item_company" => $item_company,
        "pixel" => "",
    );

    return $merged_results;
}


function GetSig($qid, $page, $db, $prefix)
{
    $query = "
        SELECT 
        id,
        `page`,
        `type`,
        `name`,
        `photo`,
        position,
        phone,
        email 
        FROM " . $prefix . "quotation_signature
        WHERE  quotation_id = " . $qid . "
        AND  page = " . $page . "
        AND `status` <> -1 
        ORDER BY id
    ";

    // prepare the query
    $stmt = $db->prepare($query);
    $stmt->execute();

    $item_client = [];
    $item_company = [];

    $merged_results = [];
    
    $id = 0;
    $page = 0;
    $type = '';
    $name = '';
    $photo = '';
    $url = '';
    $position = '';
    $phone = '';
    $email = '';
  
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        $id = $row['id'];
        $page = $row['page'];
        $type = $row['type'];
        $name = $row['name'];
        $photo = $row['photo'];
        $position = $row['position'];
        $phone = $row['phone'];
        $email = $row['email'];
       
        if($type == 'C'){
            $item_client[] = array(
                "id" => $id,
                "type" => $type,
                "photo" => '',
                "url" => '',
                "name" => $name,
                "position" => $position,
                "phone" => $phone,
                "email" => $email,
            );
        }
        
        if($type ==  'F'){
            $item_company[] = array(
                "id" => $id,
                "type" => $type,
                "photo" => $photo,
                "url" =>  $photo != '' ? 'https://storage.googleapis.com/feliiximg/' . $photo : '',
                "name" => $name,
                "position" => $position,
                "phone" => $phone,
                "email" => $email,
            );
        }
        
    }

    $merged_results = array(
        "page" => $page,
        "item_client" => $item_client,
        "item_company" => $item_company,
               
    );

    return $merged_results;
}

function GetTermInfo($qid, $db, $prefix)
{
    $query = "
        SELECT 
        id,
        `page`,
        title,
        brief,
        list 
        FROM " . $prefix . "quotation_term
        WHERE  quotation_id = " . $qid . "
        AND `status` <> -1 
        ORDER BY id
    ";

    // prepare the query
    $stmt = $db->prepare($query);
    $stmt->execute();

    $item = [];

    $merged_results = [];
    
    $id = 0;
    $page = 0;
    $title = '';
    $brief = '';
    $list = '';

    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        $id = $row['id'];
        $page = $row['page'];
        $title = $row['title'];
        $brief = $row['brief'];
        $list = $row['list'];

        $item[] = array(
            "id" => $id,
            "page" => $page,
            "title" => $title,
            "brief" => $brief,
            "list" => $list,
          
        );
        
    }

    $merged_results = array(
        "page" => $page,
        "item" => $item,
               
    );

    return $merged_results;
}


function GetPaymentTermInfo($qid, $db, $prefix)
{
    $query = "
        SELECT 
        id,
        `page`,
        payment_method,
        brief,
        list 
        FROM " . $prefix . "quotation_payment_term
        WHERE  quotation_id = " . $qid . "
        AND `status` <> -1 
        ORDER BY id
    ";

    // prepare the query
    $stmt = $db->prepare($query);
    $stmt->execute();

    $item = [];

    $merged_results = [];
    
    $id = 0;
    $page = 0;
    $payment_method = 'Cash; Cheque; Credit Card; Bank Wiring;';
    $brief = '50% Downpayment & another 50% balance a day before the delivery';
    $list = '[{"id":"0", "bank_name": "BDO", "first_line":"Acct. Name: Feliix Inc. Acct no: 006910116614", "second_line":"Branch: V.A Rufino", "third_line":""}, {"id":"1", "bank_name": "SECURITY BANK", "first_line":"Acct. Name: Feliix Inc. Acct no: 0000018155245", "second_line":"Swift code: SETCPHMM", "third_line":"Address: 512 Edsa near Corner Urbano Plata St., Caloocan City"}]';

    $item = json_decode($list, TRUE); 
  
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {

        $page = $row['page'];
        $payment_method = $row['payment_method'];
        $brief = $row['brief'];
        $list = $row['list'];
       
        $item = json_decode($list, TRUE); 
        
    }

    $merged_results = array(
        "page" => $page,
        "payment_method" => $payment_method,
        "brief" => $brief,
        "item" => $item,
               
    );

    return $merged_results;
}


function GetTotalInfo($qid, $db, $prefix){
    $query = "
        SELECT 
        id,
        `page`,
        discount,
        vat,
        show_vat,
        valid,
        total
        FROM  " . $prefix . "quotation_total
        WHERE  quotation_id = " . $qid . "
        AND `status` <> -1 
        ORDER BY id
    ";

    // prepare the query
    $stmt = $db->prepare($query);
    $stmt->execute();

    $merged_results = [];
    
    $id = 0;
    $page = 0;
    $discount = 0;
    $vat = '';
    $show_vat = '';
    $valid = '';
    $total = '';

    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        $id = $row['id'];
        $page = $row['page'];
        $discount = $row['discount'];
        $vat = $row['vat'];
        $show_vat = $row['show_vat'];
        $valid = $row['valid'];
        $total = $row['total'];
        
        
    }

    if($prefix == '')
    {
        $vat = '';
        $show_vat = '';
    }

    $merged_results = array(
        "id" => $id,
        "page" => $page,
        "discount" => $discount,
        "vat" => $vat,
        "show_vat" => $show_vat,
        "valid" => $valid,
        "total" => $total,
        
        "real_total" => 0,

        "back_total" => 0,
    );

    return $merged_results;
}

function GetTypes($qid, $db, $prefix){
    $query = "
        SELECT id,
        block_type,
        block_name,
        not_show,
        real_amount
        FROM  " . $prefix . "quotation_page_type
        WHERE  page_id = " . $qid . "
        AND `status` <> -1 
        ORDER BY id
    ";

    // prepare the query
    $stmt = $db->prepare($query);
    $stmt->execute();

    $merged_results = [];
    

    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        $id = $row['id'];
        $block_type = $row['block_type'];
        $block_name = $row['block_name'];

        $not_show = $row['not_show'];
        $real_amount = $row['real_amount'];

        if($prefix == ''){
            $not_show = '';
            $real_amount = 0;
        }

        $blocks = [];

        $blocks = GetBlocks($id, $db, $prefix);
        $subtotal = GetSubtotal($blocks);

        $merged_results[] = array(
            "id" => $id,
            "org_id" => $id,
            "type" => $block_type,
            "name" => $block_name,
            "not_show" => $not_show,
            "real_amount" => $real_amount,
            "blocks" => $blocks,
            "subtotal" => $subtotal,
        );
    }

    return $merged_results;
}

function GetBlocks($qid, $db, $prefix){
    $query = "
        SELECT id,
        type_id,
        `type`,
        code,
        photo,
        photo2,
        photo3,
        qty,
        ratio,
        price,
        discount,
        amount,
        description,
        v1,
        v2,
        v3,
        v4,
        ps_var,
        listing,
        num,
        notes, ";
if($prefix == 'approval_form_'){
    $query .= "
        approval, ";
}
$query .= "
        pid
        FROM  " . $prefix . "quotation_page_type_block
        WHERE  type_id = " . $qid . "
        AND `status` <> -1 
        ORDER BY id
    ";

    // prepare the query
    $stmt = $db->prepare($query);
    $stmt->execute();

    $merged_results = [];

    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        $id = $row['id'];
        $type_id = $row['type_id'];
        $type = $row['type'];
        $code = $row['code'];
        $photo = $row['photo'];
        $photo2 = $row['photo2'];
        $photo3 = $row['photo3'];
        $qty = $row['qty'];
        $notes = $row['notes'];
        $ratio = $row['ratio'];
        $price = $row['price'];
        $num = $row['num'];
        $pid = $row['pid'];
        $discount = $row['discount'];
        $amount = $row['amount'];
        $description = $row['description'];
        $v1 = $row['v1'];
        $v2 = $row['v2'];
        $v3 = $row['v3'];
        $v4 = $row['v4'];
        $ps_var = json_decode($row['ps_var'] == null ? "[]" : $row['ps_var'], true);

        $listing = $row['listing'];
        $approval = [];
if($prefix == 'approval_form_'){
        // split by comma
        $approval = explode(",", $row['approval']);
}
    
        $type == "" ? "" : "image";
        $url = $photo == "" ? "" : "https://storage.googleapis.com/feliiximg/" . $photo;
        $url2 = $photo2 == "" ? "" : "https://storage.googleapis.com/feliiximg/" . $photo2;
        $url3 = $photo3 == "" ? "" : "https://storage.googleapis.com/feliiximg/" . $photo3;
  
        $merged_results[] = array(
            "id" => $id,
            "type_id" => $type_id,
            "code" => $code,
            "type" => $type,
            "photo" => $photo,
            "photo2" => $photo2,
            "photo3" => $photo3,
            "type" => $type,
            "url" => $url,
            "url2" => $url2,
            "url3" => $url3,
            "qty" => $qty,
            "notes" => $notes,
            "ratio" => $ratio,
            "num" => $num,
            "pid" => $pid,
            "price" => $price,
            "discount" => $discount,
            "amount" => $amount,
            "desc" => $description,
            "v1" => $v1,
            "v2" => $v2,
            "v3" => $v3,
            "v4" => $v4,
            "ps_var" => $ps_var,
            "list" => $listing,
            "approval" => $approval,
        );
    }

    return $merged_results;
}


function GetQuotationExport($q_id, $db)
{
    $items = "[]";

    $query = "
        SELECT items
        FROM   quotation_export
        WHERE  quotation_id = " . $q_id . "
        AND `status` <> -1 
    ";

    // prepare the query
    $stmt = $db->prepare($query);
    $stmt->execute();

    $row = $stmt->fetch(PDO::FETCH_ASSOC);
    if($row !== false)
    {
        $items = $row['items'];
    }

    // json to php array
    $items = json_decode($items, true);

    return $items;

}

function GetProductItems($pages, $q_id, $db)
{
    $merged_results = [];

    $cache_results = GetQuotationExport($q_id, $db);

    foreach($pages as $page)
    {
        foreach($page['types'] as $type)
        {
            foreach($type['blocks'] as $row)
            {
            
                $id = $row['id'];
                $type_id = $row['type_id'];
                $type = $row['type'];
                $code = $row['code'];
                $photo = $row['photo'];
                $qty = $row['qty'];
                $notes = $row['notes'];
                $price = $row['price'];
                $num = $row['num'];
                $pid = $row['pid'];
                if($pid == 0)
                {
                   // search project_category for product_id 
                     $pid = GetProductId($code, $db);
                }
                $discount = $row['discount'];
                $amount = $row['amount'];
                $description = $row['desc'];
                $v1 = $row['v1'];
                $v2 = $row['v2'];
                $v3 = $row['v3'];
                $v4 = $row['v4'];
                // $ps_var = json_decode($row['ps_var'] == null ? "[]" : $row['ps_var'], true);

                $listing = $row['list'];
            
                $type == "" ? "" : "image";
                $url = $photo == "" ? "" : "https://storage.googleapis.com/feliiximg/" . $photo;
            
                $merged_results[] = array(
                    "id" => $id,
                    "is_selected" => "",
                    "type_id" => $type_id,
                    "code" => $code,
                    "type" => $type,
                    "photo" => $photo,
                    "type" => $type,
                    "url" => $url,
                    "qty" => $qty,
                    "notes" => $notes,
                    "num" => $num,
                    "pid" => $pid,
                    "price" => $price,
                    "discount" => $discount,
                    "amount" => $amount,
                    "desc" => $description,
                    "v1" => $v1,
                    "v2" => $v2,
                    "v3" => $v3,
                    "v4" => $v4,
                    "list" => $listing,
                );
                
            }
        }
    }

    $return_result = array();
    foreach ($cache_results as $result)
    {
        // if result[id] is in merged_results, then remove it from merged_results
        $index = array_search($result['id'], array_column($merged_results, 'id'));
        if($index !== false)
        {
            $return_result[] = $merged_results[$index];
        }
    }

    foreach ($merged_results as $result)
    {
        $index = array_search($result['id'], array_column($cache_results, 'id'));
        if($index === false)
        {
            $return_result[] = $result;
        }
     
    }

    return $return_result;
}

function GetProductId($code, $db)
{
    $pid = 0;

    $query = "
        SELECT id
        FROM   product_category
        WHERE  code = '" . $code . "'
        AND `status` <> -1 
        ORDER BY id
    ";

    // prepare the query
    $stmt = $db->prepare($query);
    $stmt->execute();

    $row = $stmt->fetch(PDO::FETCH_ASSOC);
    if($row !== false)
    {
        $pid = $row['id'];
    }

    return $pid;
}