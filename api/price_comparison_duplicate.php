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
    $amount = $merged_results[0]['amount'];
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
    
    $options = $merged_results[0]['options'];
    $groups = $merged_results[0]['ng'];
    
    $total_info = $merged_results[0]['total_info'];
    $term_info = $merged_results[0]['term_info'];
    $sig_info = $merged_results[0]['sig_info'];
    $payment_method = $merged_results[0]['payment_method'];
    
    $query = "INSERT INTO price_comparison
    SET
    `title` = :title,
    `kind` = :kind,
    `amount` = :amount,
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
    
    `status` = 0,
    `create_id` = :create_id,
    `created_at` =  now() ";
    
    // prepare the query
    $stmt = $db->prepare($query);
    
    // bind the values
    $stmt->bindParam(':title', $title);
    $stmt->bindParam(':kind', $kind);
    $stmt->bindParam(':amount', $amount);
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
    
    $stmt->bindParam(':create_id', $user_id);
    
    $price_id = 0;
    $options_id = [];
    // execute the query, also check if query was successful
    try {
        // execute the query, also check if query was successful
        if ($stmt->execute()) {
            $price_id = $db->lastInsertId();
            
            $options_id = insert_options($price_id, $user_id, $options, $db);
            insert_groups($price_id, $options_id, $user_id, $groups, $db);
            
            insert_total_info($price_id, $user_id, $total_info, $db);
            insert_term_info($price_id, $user_id, $term_info, $db);
            insert_payment_term($price_id, $user_id, $payment_method, $db);
            insert_sig_info($price_id, $user_id, $sig_info, $db);
            
            
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

function insert_sig_info($price_id, $user_id, $sigs, $db) {
    if(count($sigs) == 0) {
        return;
    }

    foreach($sigs as $sig)
    {
        $query = "INSERT INTO price_comparison_signature
            SET
        `price_id` = :price_id,
        `page` = 1,
        `type` = :type,
        `photo` = :photo,
        `name` = :name,
        `position` = :position,
        `phone` = :phone,
        `email` = :email,
        `status` = 0,
        `create_id` = :create_id,
        `created_at` =  now() ";
        
        // prepare the query
        $stmt = $db->prepare($query);
        
        // bind the values
        $stmt->bindParam(':price_id', $price_id);
        $stmt->bindParam(':type', $sig['type']);
        $stmt->bindParam(':photo', $sig['photo']);
        $stmt->bindParam(':name', $sig['name']);
        $stmt->bindParam(':position', $sig['position']);
        $stmt->bindParam(':phone', $sig['phone']);
        $stmt->bindParam(':email', $sig['email']);
        $stmt->bindParam(':create_id', $user_id);
        
        // execute the query, also check if query was successful
        try {
            // execute the query, also check if query was successful
            if ($stmt->execute()) {
                
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
}

function insert_term_info($price_id, $user_id, $terms, $db) {
    if(count($terms) == 0) {
        return;
    }

    foreach($terms as $item)
    {
        $query = "INSERT INTO price_comparison_term
            SET
        `price_id` = :price_id,
        `page` = 1,
        `title` = :title,
        `brief` = :brief,
        `list` = :list,
        `status` = 0,
        `create_id` = :create_id,
        `created_at` =  now() ";
        
        // prepare the query
        $stmt = $db->prepare($query);
        
        // bind the values
        $stmt->bindParam(':price_id', $price_id);
        $stmt->bindParam(':title', $item['title']);
        $stmt->bindParam(':brief', $item['brief']);
        $stmt->bindParam(':list', $item['list']);
        $stmt->bindParam(':create_id', $user_id);

        
        // execute the query, also check if query was successful
        try {
            // execute the query, also check if query was successful
            if ($stmt->execute()) {
                 
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
}

function insert_total_info($price_id, $user_id, $total_info, $db)
{
    $query = "INSERT INTO price_comparison_total
    SET
    `price_id` = :price_id,
    `page` = 0,
    `discount` = :discount,
    `vat` = :vat,
    `show_vat` = :show_vat,
    `show_t` = :show_t,
    `valid` = :valid, ";
    if($total_info['total1'] != "")
    {
        $query = $query . " `total1` = :total1, ";
    }
    if($total_info['total2'] != "")
    {
        $query = $query . " `total2` = :total2, ";
    }
    if($total_info['total3'] != "")
    {
        $query = $query . " `total3` = :total3, ";
    }
    
    $query = $query . "
    `status` = 0,
    `create_id` = :create_id,
    `created_at` =  now() ";
    
    // prepare the query
    $stmt = $db->prepare($query);
    
    // bind the values
    $stmt->bindParam(':price_id', $price_id);
    $stmt->bindParam(':discount', $total_info['discount']);
    $stmt->bindParam(':vat', $total_info['vat']);
    $stmt->bindParam(':show_vat', $total_info['show_vat']);
    $stmt->bindParam(':show_t', $total_info['show_t']);

    if($total_info['total1'] != "")
    {
        $stmt->bindParam(':total1', $total_info['total1']);
    }
    if($total_info['total2'] != "")
    {
        $stmt->bindParam(':total2', $total_info['total2']);
    }
    if($total_info['total3'] != "")
    {
        $stmt->bindParam(':total3', $total_info['total3']);
    }

    $stmt->bindParam(':valid', $total_info['valid']);

    $stmt->bindParam(':create_id', $user_id);
    
    // execute the query, also check if query was successful
    try {
        // execute the query, also check if query was successful
        if ($stmt->execute()) {
             
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

function insert_options($price_id,  $user_id, $options, $db)
{
    if(count($options) == 0) {
        return;
    }
    
    $result = [];
    
    $sn = 0;
    foreach($options as $option)
    {
        $sn += 1;
        $query = "INSERT INTO price_comparison_option
        SET
        `p_id` = :pid,
        `title` = :title,
        `sn` = :sn,
        `color` = :color, 
        `status` = 0,
        `create_id` = :create_id,
        `created_at` =  now() ";
        
        // prepare the query
        $stmt = $db->prepare($query);
        
        // bind the values
        $stmt->bindParam(':pid', $price_id);
        $stmt->bindParam(':title', $option['title']);
        $stmt->bindParam(':sn', $sn);
        $stmt->bindParam(':color', $option['color']);
        $stmt->bindParam(':create_id', $user_id);
        
        // execute the query, also check if query was successful
        try {
            if ($stmt->execute()) {
                $last_id = $db->lastInsertId();
                $result[] = $last_id;
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
    
    return $result;
}


function insert_payment_term($price_id, $user_id, $terms, $db)
{
    if(count($terms) == 0) {
        return;
    }
    
    $sn = 0;
    foreach($terms as $term)
    {
        $sn += 1;
        $query = "INSERT INTO price_comparison_payment_term
        SET
        `price_id` = :price_id,
        `page` = 1,
        `payment_method` = :payment_method,
        `brief` = :brief,
        `list` = :list,
        `status` = 0,
        `create_id` = :create_id,
        `created_at` =  now() ";
        
        // prepare the query
        $stmt = $db->prepare($query);
        
        // bind the values
        $stmt->bindParam(':price_id', $price_id);
        $stmt->bindParam(':payment_method', $term['payment_method']);
        $stmt->bindParam(':brief', $term['brief']);
        $stmt->bindParam(':list', $term['list']);
        $stmt->bindParam(':create_id', $user_id);
        
        // execute the query, also check if query was successful
        try {
            if ($stmt->execute()) {
       
            } 
            else {
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

function insert_groups($price_id, $options_id, $user_id, $groups, $db)
{
    if(count($groups) == 0) {
        return;
    }
    
    $sn = 0;
    foreach($groups as $group)
    {
        $sn += 1;
        $query = "INSERT INTO price_comparison_group
        SET
        `p_id` = :pid,
        `title` = :title,
        `color` = :color,
        `sn` = :sn,
        `status` = 0,
        `create_id` = :create_id,
        `created_at` =  now() ";
        
        // prepare the query
        $stmt = $db->prepare($query);
        
        // bind the values
        $stmt->bindParam(':pid', $price_id);
        $stmt->bindParam(':title', $group['title']);
        $stmt->bindParam(':sn', $sn);
        $stmt->bindParam(':color', $group['color']);
        $stmt->bindParam(':create_id', $user_id);
        
        $legends = $group['legend'];
        
        $group_id = 0;
        // execute the query, also check if query was successful
        try {
            if ($stmt->execute()) {
                $group_id = $db->lastInsertId();
                
                insert_legends($price_id, $group_id, $options_id, $user_id, $legends, $db);
                
            } 
            else {
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

function insert_legends($price_id, $group_id, $options_id, $user_id, $legends, $db) {
    if(count($legends) == 0) {
        return;
    }
    
    $sn = 0;
    foreach($legends as $legend)
    {
        $sn += 1;
        $query = "INSERT INTO price_comparison_legend
        SET
        `group_id` = :group_id,
        `title` = :title,
        `sn` = :sn,
        `color` = :color,
        `status` = 0,
        `create_id` = :create_id,
        `created_at` =  now() ";
        
        // prepare the query
        $stmt = $db->prepare($query);
        
        // bind the values
        $stmt->bindParam(':group_id', $group_id);
        $stmt->bindParam(':title', $legend['title']);
        $stmt->bindParam(':sn', $sn);
        $stmt->bindParam(':color', $legend['color']);
        $stmt->bindParam(':create_id', $user_id);
        
        $matrix = $legend['matrix'];
        
        $legend_id = 0;
        // execute the query, also check if query was successful
        try {
            if ($stmt->execute()) {
                $legend_id = $db->lastInsertId();
                
                insert_items($price_id, $group_id, $options_id, $legend_id, $user_id, $matrix, $db);
                
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
}

function insert_items($price_id, $group_id, $options_id, $legend_id, $user_id, $items, $db) {
    if(count($items) == 0) {
        return;
    }
    
    $sn = 0;
    foreach($items as $item)
    {
        $gp1 = $item['gp1'];
        if($gp1['id'] != 0)
        {
            $sn = $sn + 1;
            insert_gp($price_id, $options_id[0], $legend_id, $sn, $gp1, $user_id, $db);
        }
        
        $gp2 = $item['gp2'];
        if($gp2['id'] != 0)
        {
            $sn = $sn + 1;
            insert_gp($price_id, $options_id[1], $legend_id, $sn, $gp2, $user_id, $db);
        }
        
        if(count($options_id) > 2)
        {
            $gp3 = $item['gp3'];
            if($gp3['id'] != 0)
            {
                $sn = $sn + 1;
                insert_gp($price_id, $options_id[2], $legend_id, $sn, $gp3, $user_id, $db);
            }
        }
        
    }
}

function insert_gp($price_id, $option_id, $legend_id, $sn, $gp, $user_id, $db) {
    $query = "INSERT INTO price_comparison_item
    SET
    `od_id` = :price_id,
    `option_id` = :option_id,
    `legend_id` = :legend_id,
    `sn` = :sn,
    `photo1` = :photo1,
    `photo2` = :photo2,
    `photo3` = :photo3,
    `code` = :code,
    `brief` = :brief,
    `list` = :list,
    `qty` = :qty,
    `price` = :price,
    `ratio` = :ratio,
    `notes` = :notes,
    `amount` = :amount,
    `desc` = :desc,
    `pid` = :pid,
    `v1` = :v1,
    `v2` = :v2,
    `v3` = :v3,
    `v4` = :v4,
    `ps_var` = :ps_var,
    `discount` = :discount,
    `status` = 0,
    `create_id` = :create_id,
    `created_at` =  now() ";
    
    // prepare the query
    $stmt = $db->prepare($query);

    $ps_var = isset($gp['ps_var']) ? $gp['ps_var'] : [];
    $json_ps_var = json_encode($ps_var);
    
    // bind the values
    $stmt->bindParam(':price_id', $price_id);
    $stmt->bindParam(':option_id', $option_id);
    $stmt->bindParam(':legend_id', $legend_id);
    $stmt->bindParam(':sn', $sn);
    $stmt->bindParam(':photo1', $gp['photo1']);
    $stmt->bindParam(':photo2', $gp['photo2']);
    $stmt->bindParam(':photo3', $gp['photo3']);
    $stmt->bindParam(':code', $gp['code']);
    $stmt->bindParam(':brief', $gp['brief']);
    $stmt->bindParam(':list', $gp['list']);
    $stmt->bindParam(':qty', $gp['qty']);
    $stmt->bindParam(':price', $gp['price']);
    $stmt->bindParam(':ratio', $gp['ratio']);
    $stmt->bindParam(':notes', $gp['notes']);
    $stmt->bindParam(':amount', $gp['amount']);
    $stmt->bindParam(':desc', $gp['desc']);
    $stmt->bindParam(':pid', $gp['pid']);
    $stmt->bindParam(':v1', $gp['v1']);
    $stmt->bindParam(':v2', $gp['v2']);
    $stmt->bindParam(':v3', $gp['v3']);
    $stmt->bindParam(':v4', $gp['v4']);
    $stmt->bindParam(':ps_var', $json_ps_var);
    $stmt->bindParam(':discount', $gp['discount']);
    $stmt->bindParam(':create_id', $user_id);
    
    
    // execute the query, also check if query was successful
    try {
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

function GetQuotation($id, $db) {
    $merged_results = array();
    
    
    $query = "SELECT id, 
    title,
    kind,
    project_id,
    amount,
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
    footer_second_line
    FROM price_comparison
    WHERE status <> -1 and id=$id";
    
    
    $query = $query . " order by price_comparison.created_at desc ";
    
    $stmt = $db->prepare($query);
    $stmt->execute();
    
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        $id = $row['id'];
        $title = $row['title'];
        $kind = $row['kind'];
        $project_id = $row['project_id'];
        $amount = $row['amount'];
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
        
        
        $options = GetOptions($id, $db);
        
        $ng = GetGroupsDis($id, $db, $options);
        
        $total_info = GetTotalInfo($row['id'], $db);
        $payment_method = GetPaymentTerm($row['id'], $db);
        $term_info = GetTermInfo($row['id'], $db);
        $sig_info = GetSigInfo($row['id'], $db);
        
        
        $merged_results[] = array(
            "id" => $id,
            "title" => $title,
            "kind" => $kind,
            "amount" => $amount,
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
            "options" => $options,
            "ng" => $ng,
            "total_info" => $total_info,
            "term_info" => $term_info,
            "sig_info" => $sig_info,
            "payment_method" => $payment_method
            
        );
    }
    
    return $merged_results;
}


function GetItemMaxtrix($legend_id, $db, $options){
    $i = 0;
    $max_count = 0;
    
    $gp1_item = [];
    $gp2_item = [];
    $gp3_item = [];
    
    $row_item = [];
    
    foreach($options as $option){
        $items = GetItems($option['id'], $legend_id, $db);
        
        if($i == 0)
        {
            $gp1_item = $items;
            if(count($items) > $max_count)
            $max_count = count($items);
        }
        if($i == 1)
        {
            $gp2_item = $items;
            if(count($items) > $max_count)
            $max_count = count($items);
        }
        if($i == 2)
        {
            $gp3_item = $items;
            if(count($items) > $max_count)
            $max_count = count($items);
        }
        
        $i++;
    }
    
    $gp1 = [];
    $gp2 = [];
    $gp3 = [];
    for($i=0; $i<$max_count; $i++){
        if($i < count($gp1_item))
        $gp1 = $gp1_item[$i];
        else
        $gp1 =  array(
            'id' => 0,
            'od_id' => 0,
            'option_id' => 0,
            'legend_id' => 0,
            'sn' => 0,
            'photo1' => "",
            'photo2' => "",
            'photo3' => "",
            'code' => "",
            'brief' => "",
            'list' => "",
            'qty' => "",
            'price' => "",
            'ratio' => "",
            'discount' => "",
            'amount' => "",
            'desc' => "",
            'pid' => "",
            'v1' => "",
            'v2' => "",
            'v3' => "",
            'v4' => "",
            'url1' => "",
            'url2' => "",
            'url3' => "",
            'notes' => '',
        );
        
        if($i < count($gp2_item))
        $gp2 = $gp2_item[$i];
        else
        $gp2 =  array(
            'id' => 0,
            'od_id' => 0,
            'option_id' => 0,
            'legend_id' => 0,
            'sn' => 0,
            'photo1' => "",
            'photo2' => "",
            'photo3' => "",
            'code' => "",
            'brief' => "",
            'list' => "",
            'qty' => "",
            'price' => "",
            'ratio' => "",
            'discount' => "",
            'amount' => "",
            'desc' => "",
            'pid' => "",
            'v1' => "",
            'v2' => "",
            'v3' => "",
            'v4' => "",
            'url1' => "",
            'url2' => "",
            'url3' => "",
            'notes' => '',
        );
        
        if($i < count($gp3_item))
        $gp3 = $gp3_item[$i];
        else
        $gp3 =  array(
            'id' => 0,
            'od_id' => 0,
            'option_id' => 0,
            'legend_id' => 0,
            'sn' => 0,
            'photo1' => "",
            'photo2' => "",
            'photo3' => "",
            'code' => "",
            'brief' => "",
            'list' => "",
            'qty' => "",
            'price' => "",
            'ratio' => "",
            'discount' => "",
            'amount' => "",
            'desc' => "",
            'pid' => "",
            'v1' => "",
            'v2' => "",
            'v3' => "",
            'v4' => "",
            'url1' => "",
            'url2' => "",
            'url3' => "",
            'notes' => '',
        );
        
        $row_item[] = array(
            'gp1' => $gp1,
            'gp2' => $gp2,
            'gp3' => $gp3,
        );
    }
    
    return $row_item;
}

function GetItems($option_id, $legend_id, $db){
    $query = "
    SELECT id,
    od_id,
    option_id,
    legend_id,
    sn,
    photo1,
    photo2,
    photo3,
    code,
    brief,
    list,
    qty,
    price,
    ratio,
    discount,
    amount,
    `desc`,
    `notes`,
    pid,
    v1,
    v2,
    v3,
    v4,
    ps_var
    FROM   price_comparison_item
    WHERE  option_id = " . $option_id . "
    AND  legend_id = " . $legend_id . "
    AND `status` <> -1 
    ORDER BY sn
    ";
    
    // prepare the query
    $stmt = $db->prepare($query);
    $stmt->execute();
    
    $merged_results = [];
    
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        $id = $row['id'];
        $od_id = $row['od_id'];
        $option_id = $row['option_id'];
        $legend_id = $row['legend_id'];
        $sn = $row['sn'];
        
        $photo1 = $row['photo1'];
        $photo2 = $row['photo2'];
        $photo3 = $row['photo3'];
        $code = $row['code'];
        $brief = $row['brief'];
        $list = $row['list'];
        
        $qty = $row['qty'];
        
        $price = $row['price'];
        $ratio = $row['ratio'];
        $notes = $row['notes'];
        
        $discount = $row['discount'];
        
        $amount = $row['amount'];
        $desc = $row['desc'];
        $pid = $row['pid'];
        
        $v1 = $row['v1'];
        $v2 = $row['v2'];
        $v3 = $row['v3'];
        $v4 = $row['v4'];

        $ps_var = json_decode($row['ps_var'] == null ? "[]" : $row['ps_var'], true);
        
        $url1 = $photo1 == "" ? "" : "https://storage.googleapis.com/feliiximg/" . $photo1;
        $url2 = $photo2 == "" ? "" : "https://storage.googleapis.com/feliiximg/" . $photo2;
        $url3 = $photo3 == "" ? "" : "https://storage.googleapis.com/feliiximg/" . $photo3;
        
        
        $merged_results[] = array(
            'id' => $id,
            'od_id' => $od_id,
            'option_id' => $option_id,
            'legend_id' => $legend_id,
            'sn' => $sn,
            'photo1' => $photo1,
            'photo2' => $photo2,
            'photo3' => $photo3,
            'code' => $code,
            'brief' => $brief,
            'list' => $list,
            'qty' => $qty,
            'price' => $price,
            'ratio' => $ratio,
            'notes' => $notes,
            'discount' => $discount,
            'amount' => $amount,
            'desc' => $desc,
            'pid' => $pid,
            'v1' => $v1,
            'v2' => $v2,
            'v3' => $v3,
            'v4' => $v4,
            'ps_var' => $ps_var,
            'url1' => $url1,
            'url2' => $url2,
            'url3' => $url3,
        );
    }
    
    return $merged_results;
}


function GetLegendsDis($qid, $db, $options){
    $query = "
    SELECT id,
    group_id,
    title,
    sn,
    color,
    status,
    create_id,
    created_at
    FROM   price_comparison_legend 
    WHERE  group_id = " . $qid . "
    AND `status` <> -1 
    ORDER BY sn
    ";
    
    // prepare the query
    $stmt = $db->prepare($query);
    $stmt->execute();
    
    $merged_results = [];
    
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        $id = $row['id'];
        $group_id = $row['group_id'];
        $title = $row['title'];
        $sn = $row['sn'];
        $color = $row['color'];
        
        $status = $row['status'];
        $create_id = $row['create_id'];
        $created_at = $row['created_at'];
        
        $matrix = GetItemMaxtrix($row['id'], $db, $options);
        
        $merged_results[] = array(
            'id' => $id,
            'group_id' => $group_id,
            'title' => $title,
            'sn' => $sn,
            'color' => $color,
            'status' => $status,
            'create_id' => $create_id,
            'created_at' => $created_at,
            'matrix' => $matrix
        );
        
    }
    
    return $merged_results;
}



function GetOption($qid, $db, $options){
    
    $merged_results = [];
    
    foreach($options as $option){
        $items = GetItems($option['id'], $qid, $db);
        
        $merged_results[] = array(
            'id' => $option['id'],
            'p_id' => $option['p_id'],
            'title' => $option['title'],
            'sn' => $option['sn'],
            'color' => $option['color'],
            'status' => $option['status'],
            'create_id' => $option['create_id'],
            'created_at' => $option['created_at'],
            'temp_block_a' => $items,
        );
    }
    
    
    return $merged_results;
}


function GetGroupsDis($qid, $db, $options){
    
    $merged_results = array();
    
    $query = "SELECT id, 
    p_id, 
    title, 
    sn, 
    color, 
    create_id,
    created_at
    FROM price_comparison_group
    WHERE status <> -1 and p_id=$qid";
    
    
    $query = $query . " order by sn ";
    
    $stmt = $db->prepare($query);
    $stmt->execute();
    
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        $id = $row['id'];
        $p_id = $row['p_id'];
        $title = $row['title'];
        $sn = $row['sn'];
        $color = $row['color'];
        $create_id = $row['create_id'];
        $created_at = $row['created_at'];
        
        $legend = GetLegendsDis($row['id'], $db, $options);
        
        $merged_results[] = array(
            "id" => $id,
            "p_id" => $p_id,
            "title" => $title,
            "sn" => $sn,
            "color" => $color,
            "create_id" => $create_id,
            "created_at" => $created_at,
            "legend" => $legend,
        );
    }
    
    return $merged_results;
}


function GetOptions($qid, $db){
    $query = "
    SELECT id,
    p_id,
    title,
    sn,
    color,
    status,
    create_id,
    created_at
    FROM   price_comparison_option 
    WHERE  p_id = " . $qid . "
    AND `status` <> -1 
    ORDER BY sn
    ";
    
    // prepare the query
    $stmt = $db->prepare($query);
    $stmt->execute();
    
    $merged_results = [];
    
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        $id = $row['id'];
        $p_id = $row['p_id'];
        $title = $row['title'];
        $sn = $row['sn'];
        $color = $row['color'];
        
        $status = $row['status'];
        $create_id = $row['create_id'];
        $created_at = $row['created_at'];
        
        $merged_results[] = array(
            'id' => $id,
            'p_id' => $p_id,
            'title' => $title,
            'sn' => $sn,
            'color' => $color,
            'status' => $status,
            'create_id' => $create_id,
            'created_at' => $created_at
        );
        
    }
    
    return $merged_results;
}


function GetSubTotalInfo($qid, $db)
{
    $total = 0;
    
    $query = "
    select sum(amount) amt from quotation_page_type_block
    WHERE type_id in (select id from quotation_page_type where quotation_id = " . $qid . " and status <> -1)
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

function GetBlockNames($qid, $db){
    $query = "
    SELECT qpt.id,
    qp.page,
    block_type,
    block_name,
    not_show,
    real_amount,
    page_id
    FROM   quotation_page_type qpt
    left join quotation_page qp on qpt.page_id = qp.id
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
        
        $blocks = GetBlocks($id, $db);
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

function GetPages($qid, $db){
    $query = "
    SELECT id,
    page
    FROM   quotation_page
    WHERE  quotation_id = " . $qid . "
    AND `status` <> -1 
    ORDER BY id
    ";
    
    // prepare the query
    $stmt = $db->prepare($query);
    $stmt->execute();
    
    $merged_results = [];
    
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        $id = $row['id'];
        $page = $row['page'];
        $type = GetTypes($id, $db);
        $total = GetTotal($qid, $page, $db);
        $term = GetTerm($qid, $page, $db);
        $sig = GetSig($qid, $page, $db);
        
        $merged_results[] = array(
            "id" => $id,
            "page" => $page,
            "types" => $type,
            "total" => $total,
            "term" => $term,
            "sig" => $sig,
        );
    }
    
    return $merged_results;
}

function GetTotal($qid, $page, $db){
    $query = "
    SELECT 
    `page`,
    discount,
    vat,
    show_vat,
    valid,
    total
    FROM   quotation_total
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


function GetTerm($qid, $page, $db){
    $query = "
    SELECT 
    `page`,
    title,
    brief,
    list 
    FROM   quotation_term
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


function GetSigInfo($qid, $db)
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
    FROM   price_comparison_signature
    WHERE  price_id = " . $qid . "
    AND `status` <> -1 
    ORDER BY id
    ";
    
    // prepare the query
    $stmt = $db->prepare($query);
    $stmt->execute();
    
    $merged_results = [];
    
    $id = 0;
    $page = 0;
    $type = '';
    $name = '';
    $photo = '';
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
        

        $merged_results[] = array(
            "id" => $id,
            "type" => $type,
            "photo" => $photo,
            "page" => $page,
            "name" => $name,
            "position" => $position,
            "phone" => $phone,
            "email" => $email,
        );

    }
    
    
    return $merged_results;
}


function GetSig($qid, $page, $db)
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
    FROM   quotation_signature
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


function GetPaymentTerm($qid, $db){
    $query = "
        SELECT 
        `page`,
        payment_method,
        brief,
        list 
        FROM   price_comparison_payment_term
        WHERE  price_id = " . $qid . "
        AND `status` <> -1 
        ORDER BY id
    ";

    // prepare the query
    $stmt = $db->prepare($query);
    $stmt->execute();

    $merged_results = [];
    

    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        $page = $row['page'];
        $payment_method = $row['payment_method'];
        $brief = $row['brief'];
        $list = $row['list'];
    

        $merged_results[] = array(
            "page" => $page,
            "payment_method" => $payment_method,
            "brief" => $brief,
            "list" => $list,
          
        );
    }

    return $merged_results;
}


function GetTermInfo($qid, $db)
{
    $query = "
    SELECT 
    id,
    `page`,
    title,
    brief,
    list 
    FROM   price_comparison_term
    WHERE  price_id = " . $qid . "
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
        
        $merged_results[] = array(
            "id" => $id,
            "page" => $page,
            "title" => $title,
            "brief" => $brief,
            "list" => $list,
            
        );
        
    }
    
    
    return $merged_results;
}


function GetTotalInfo($qid, $db){
    $query = "
    SELECT 
    id,
    `page`,
    discount,
    vat,
    show_vat,
    show_t,
    valid,
    total1,
    total2,
    total3
    FROM   price_comparison_total
    WHERE  price_id = " . $qid . "
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
    $show_t = '';
    $valid = '';
    $total1 = '';
    $total2 = '';
    $total3 = '';
    
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        $id = $row['id'];
        $page = $row['page'];
        $discount = $row['discount'];
        $vat = $row['vat'];
        $show_vat = $row['show_vat'];
        $show_t = $row['show_t'];
        $valid = $row['valid'];
        $total1 = $row['total1'];
        $total2 = $row['total2'];
        $total3 = $row['total3'];
        
        
    }
    
    $merged_results = array(
        "id" => $id,
        "page" => $page,
        "discount" => $discount,
        "vat" => $vat,
        "show_vat" => $show_vat,
        "show_t" => $show_t,
        "valid" => $valid,
        "total1" => $total1,
        "total2" => $total2,
        "total3" => $total3,
        
        
    );
    
    return $merged_results;
}

function GetTypes($qid, $db){
    $query = "
    SELECT id,
    block_type,
    block_name,
    not_show,
    real_amount
    FROM   quotation_page_type
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
        
        $blocks = [];
        
        $blocks = GetBlocks($id, $db);
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

function GetBlocks($qid, $db){
    $query = "
    SELECT id,
    type_id,
    `type`,
    code,
    photo,
    qty,
    ratio,
    notes,
    price,
    discount,
    amount,
    description,
    listing
    FROM   quotation_page_type_block
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
        $qty = $row['qty'];
        $ratio = $row['ratio'];
        $notes = $row['notes'];
        $price = $row['price'];
        $discount = $row['discount'];
        $amount = $row['amount'];
        $description = $row['description'];
        $listing = $row['listing'];
        
        $type == "" ? "" : "image";
        $url = $photo == "" ? "" : "https://storage.googleapis.com/feliiximg/" . $photo;
        
        $merged_results[] = array(
            "id" => $id,
            "type_id" => $type_id,
            "code" => $code,
            "type" => $type,
            "photo" => $photo,
            "type" => $type,
            "url" => $url,
            "qty" => $qty,
            "ratio" => $ratio,
            "notes" => $notes,
            "price" => $price,
            "discount" => $discount,
            "amount" => $amount,
            "desc" => $description,
            "list" => $listing,
            
        );
    }
    
    return $merged_results;
}
