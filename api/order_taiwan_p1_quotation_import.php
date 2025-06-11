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
        $od_id = (isset($_POST['od_id']) ?  $_POST['od_id'] : 0);
        $qid = (isset($_POST['qid']) ?  $_POST['qid'] : 0);
        $sn = (isset($_POST['sn']) ?  $_POST['sn'] : 0);

        $access7 = (isset($_POST['access7']) ?  $_POST['access7'] : false);

        if ($od_id == 0) {
            http_response_code(401);
            echo json_encode(array("message" => "Failure at " . date("Y-m-d") . " " . date("h:i:sa") . " " . "Access denied."));
            die();
        }

        $block_array = GetQuotationItems($qid, $db);

        // get order type
        $order_type = "";
        $query = "SELECT order_type FROM `od_main` WHERE id = :od_id";
        $stmt = $db->prepare($query);
        $stmt->bindParam(':od_id', $od_id);
        try {
            if ($stmt->execute()) {
                $row = $stmt->fetch(PDO::FETCH_ASSOC);
                $order_type = $row['order_type'];
            } 
        } catch (Exception $e) {
            error_log($e->getMessage());
            $db->rollback();
            http_response_code(501);
            echo json_encode(array("Failure at " . date("Y-m-d") . " " . date("h:i:sa") . " " . $e->getMessage()));
            die();
        }

        $which_pool = "Project Pool";
        $as_sample = "No";

        if($order_type == "mockup")
        {
            $which_pool = "Project Pool";
            $as_sample = "Yes";
        }
        
        if($order_type == "stock")
        {
            $which_pool = "Stock Pool";
            $as_sample = "No";
        }
        
        if($order_type == "sample")
        {
            $which_pool = "Stock Pool";
            $as_sample = "Yes";
        }
        
        try {

            $sn = GetMaxSn($od_id, $db);

            for($i=0; $i<count($block_array); $i++) 
            {
                $sn++;
                // insert quotation_page_type_block
                $query = "INSERT INTO od_item
                    SET
                    `od_id` = :od_id,
                    `sn` = :sn,
                    `confirm` = :confirm,
                    `brand` = :brand,
                    `brand_other` = :brand_other,
                    `photo1` = :photo1,
                    `photo2` = :photo2,
                    `photo3` = :photo3,
                    `code` = :code,
                    `brief` = :brief,
                    `listing` = :listing,
                    `qty` = :qty,
                    `backup_qty` = '',
                    `unit` = '',
                    `srp` = :srp,
                    `date_needed` = :date_needed,
                    `pid` = :pid,
                    `v1` = :v1,
                    `v2` = :v2,
                    `v3` = :v3,
                    `v4` = :v4,
                    `ps_var` = :ps_var,
                    `which_pool` = :which_pool,
                    `as_sample` = :as_sample,
                    `status` = 0,
                    `status_at` = now(),
                    `normal` = :normal,
                    `create_id` = :create_id,
                    `created_at` = now()";

                // prepare the query
                $stmt = $db->prepare($query);

                $confirm = 'N';
                //$brand = '';
                $brand = GetBrandInfo($block_array[$i]['pid'], $db);
                $brand_other = '';

                $photo1 = isset($block_array[$i]['photo']) ? $block_array[$i]['photo'] : '';
                $photo2 = isset($block_array[$i]['photo2']) ? $block_array[$i]['photo2'] : '';
                $photo3 = isset($block_array[$i]['photo3']) ? $block_array[$i]['photo3'] : '';

                $code = isset($block_array[$i]['code']) ? $block_array[$i]['code'] : '';

                if($brand == '')
                    $brand = MatchBrandPattern($code);

                $brief = isset($block_array[$i]['desc']) ? $block_array[$i]['desc'] : '';
                $listing = isset($block_array[$i]['list']) ? $block_array[$i]['list'] : '';

                $qty = isset($block_array[$i]['qty']) ? $block_array[$i]['qty'] : '';
                $srp = isset($block_array[$i]['amount']) ? $block_array[$i]['amount'] : '';
                $date_needed =  '';
                $pid = isset($block_array[$i]['pid']) ? $block_array[$i]['pid'] : 0;

                $v1 = isset($block_array[$i]['v1']) ? $block_array[$i]['v1'] : '';
                $v2 = isset($block_array[$i]['v2']) ? $block_array[$i]['v2'] : '';
                $v3 = isset($block_array[$i]['v3']) ? $block_array[$i]['v3'] : '';
                $v4 = isset($block_array[$i]['v4']) ? $block_array[$i]['v4'] : '';

                $ps_var = isset($block_array[$i]['ps_var']) ? $block_array[$i]['ps_var'] : [];
                $json_ps_var = json_encode($ps_var);

                // check if normal product
                $is_normal = IsNormalProduct($pid, $v1, $v2, $v3, $v4, $db);

                // bind the values
                $stmt->bindParam(':od_id', $od_id);
                $stmt->bindParam(':sn', $sn);
                $stmt->bindParam(':confirm', $confirm);
                $stmt->bindParam(':brand', $brand);
                $stmt->bindParam(':brand_other', $brand_other);
                $stmt->bindParam(':photo1', $photo1);
                $stmt->bindParam(':photo2', $photo2);
                $stmt->bindParam(':photo3', $photo3);
                $stmt->bindParam(':code', $code);
                $stmt->bindParam(':brief', $brief);
                $stmt->bindParam(':listing', $listing);
                $stmt->bindParam(':qty', $qty);
     
                $stmt->bindParam(':srp', $srp);
                $stmt->bindParam(':date_needed', $date_needed);
                $stmt->bindParam(':pid', $pid);

                $stmt->bindParam(':v1', $v1);
                $stmt->bindParam(':v2', $v2);
                $stmt->bindParam(':v3', $v3);
                $stmt->bindParam(':v4', $v4);

                $stmt->bindParam(':ps_var', $json_ps_var);
                $stmt->bindParam(':which_pool', $which_pool);
                $stmt->bindParam(':as_sample', $as_sample);

                $stmt->bindParam(':normal', $is_normal);
              
                $stmt->bindParam(':create_id', $user_id);
               
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

            // update access7 users
            if($access7 == "true")
                AddAcces7($od_id, $user_name, $db);

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


function AddAcces7($od_id, $username, $db)
{
    $access7 = "";
    $query = "SELECT access7 FROM `od_main` WHERE id = :od_id";

    // prepare the query
    $stmt = $db->prepare($query);

    // bind the values
    $stmt->bindParam(':od_id', $od_id);

    try {
        // execute the query, also check if query was successful
        if ($stmt->execute()) {
            $row = $stmt->fetch(PDO::FETCH_ASSOC);
            $access7 = $row['access7'];
        } else {
            $arr = $stmt->errorInfo();
            error_log($arr[2]);
        }
    } catch (Exception $e) {
        error_log($e->getMessage());
    }

    // seperate by comma and check if username is already in the list
    $access7_array = explode(",", $access7);
    if (!in_array($username, $access7_array)) {
        array_push($access7_array, $username);
    }
    // implode by comma and update to access7
    $access7 = implode(",", $access7_array);

    $query = "UPDATE `od_main` SET access7 = :access7 WHERE id = :od_id";

    // prepare the query
    $stmt = $db->prepare($query);

    // bind the values
    $stmt->bindParam(':access7', $access7);
    $stmt->bindParam(':od_id', $od_id);

    try {
        // execute the query, also check if query was successful
        if ($stmt->execute()) {
            return true;
        } else {
            $arr = $stmt->errorInfo();
            error_log($arr[2]);
            return false;
        }
    } catch (Exception $e) {
        error_log($e->getMessage());
        return false;
    }
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
  
        $blocks = [];

        $blocks = GetBlocks($id, $db);


        $merged_results[] = array(
            "id" => $id,

            "blocks" => $blocks,
   
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
        photo2,
        photo3,
        qty,
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
        pid
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
        $photo2 = $row['photo2'];
        $photo3 = $row['photo3'];
        $qty = $row['qty'];
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
    
        $type == "" ? "" : "image";
        $url = $photo == "" ? "" : "https://storage.googleapis.com/feliiximg/" . $photo;
  
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
            "qty" => $qty,
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
          
        );
    }

    return $merged_results;
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
 
        $merged_results[] = array(
            "id" => $id,
            "page" => $page,
            "types" => $type,
        );
    }

    return $merged_results;
}

function GetQuotationItems($qid, $db){

    $pages = GetPages($qid, $db);

    $merged_results = [];

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
                $photo2 = $row['photo2'];
                $photo3 = $row['photo3'];
                $qty = $row['qty'];
                $price = $row['price'];
                $num = $row['num'];
                $pid = $row['pid'];
                $discount = $row['discount'];
                $amount = $row['amount'];
                $description = $row['desc'];
                $v1 = $row['v1'];
                $v2 = $row['v2'];
                $v3 = $row['v3'];
                $v4 = $row['v4'];
                //$ps_var = json_decode($row['ps_var'] == null ? "[]" : $row['ps_var'], true);
                $ps_var = $row['ps_var'];
                $listing = $row['list'];
            
                $type == "" ? "" : "image";
                $url = $photo == "" ? "" : "https://storage.googleapis.com/feliiximg/" . $photo;
            
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
                    "qty" => $qty,
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
                    
                );
                
            }
        }
    }

    return $merged_results;
}

function GetMaxSn($od_id, $db)
{
    $max_sn = 0;
    $query = "SELECT max(sn*1) as max_sn FROM `od_item` WHERE od_id = :od_id";

    // prepare the query
    $stmt = $db->prepare($query);

    // bind the values
    $stmt->bindParam(':od_id', $od_id);

    try {
        // execute the query, also check if query was successful
        if ($stmt->execute()) {
            $row = $stmt->fetch(PDO::FETCH_ASSOC);
            $max_sn = $row['max_sn'];
            return $max_sn;
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

function GetBrandInfo($pid, $db)
{
    $brand = "";
    $query = "SELECT brand FROM `product_category` WHERE id = :pid";

    // prepare the query
    $stmt = $db->prepare($query);

    // bind the values
    $stmt->bindParam(':pid', $pid);

    try {
        // execute the query, also check if query was successful
        if ($stmt->execute()) {
            $row = $stmt->fetch(PDO::FETCH_ASSOC);
            $brand = $row['brand'];
        
        } else {
            $arr = $stmt->errorInfo();
            error_log($arr[2]);
    
        }
    } catch (Exception $e) {
        error_log($e->getMessage());
   
    }

    return $brand;
}

function IsNormalProduct($pid, $v1, $v2, $v3, $v4, $db){
    $is_normal = 0;
    $variation_mode = 0;

    if($pid == 0)
        return $is_normal;

    $query = "SELECT variation_mode FROM `product_category` WHERE id = :pid";

    // prepare the query
    $stmt = $db->prepare($query);

    // bind the values
    $stmt->bindParam(':pid', $pid);

    try {
        // execute the query, also check if query was successful
        if ($stmt->execute()) {
            $row = $stmt->fetch(PDO::FETCH_ASSOC);
            $variation_mode = $row['variation_mode'];
        
        } else {
            $arr = $stmt->errorInfo();
            error_log($arr[2]);
    
        }
    } catch (Exception $e) {
        error_log($e->getMessage());
   
    }

    if($variation_mode == 1 && $v1 == '' && $v2 == '' && $v3 == '' && $v4 == '')
        $is_normal = 1;

    return $is_normal;
}

function MatchBrandPattern($code){
    $patterns = ['FELIIX CL ==>COLORS',
    'FELIIX DL ==>DANCELIGHT',
    'FELIIX ET ==>ELITES',
    'FELIIX EL ==>EVERLIGHT',
    'FELIIX GD ==>GLEDOPTO',
    'FELIIX GT ==>GENTECH',
    'FELIIX HG ==>HUANG GONG',
    'FELIIX LD ==>LEDOUX',
    'FELIIX RT ==>ROOSTER',
    'FELIIX SG ==>SASUGAS',
    'FELIIX SD ==>SEEDDESIGN',
    'FELIIX SB ==>SHAN BEN',
    'FELIIX ST ==>SHINE TOP',
    'FELIIX TYG ==>TAYAGI',
    'FELIIX TONS ==>TONS',
    'FELIIX WH ==>WENHUI',
    'FELIIX XL ==>XCELLENT',
    'FELIIX YD ==>YUDA'];
    $brand = "";
    foreach($patterns as $pattern)
    {
        $pattern_array = explode("==>", $pattern);

        // if code contains pattern
        if(strpos($code, $pattern_array[0]) !== false)
        {
            $brand = $pattern_array[1];
            break;
        }
    }
    return $brand;
}