<?php
error_reporting(E_ALL);
//header("Access-Control-Allow-Origin: https://feliix.myvnc.com");
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: POST");
header("Access-Control-Max-Age: 3600");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

$jwt = (isset($_POST['jwt']) ?  $_POST['jwt'] : null);

$price_id = isset($_POST['price_id']) ? $_POST['price_id'] : 0;
$page = isset($_POST['page']) ? $_POST['page'] : 1;
$discount = isset($_POST['discount']) ? $_POST['discount'] : 0;
$vat = isset($_POST['vat']) ? $_POST['vat'] : '';
$show_vat = isset($_POST['show_vat']) ? $_POST['show_vat'] : '';
$show_t = isset($_POST['show_t']) ? $_POST['show_t'] : '';
$valid = isset($_POST['valid']) ? $_POST['valid'] : '';
$total1 = isset($_POST['total1']) ? $_POST['total1'] : '';
$total2 = isset($_POST['total2']) ? $_POST['total2'] : '';
$total3 = isset($_POST['total3']) ? $_POST['total3'] : '';


$discount == '' ? $discount = 0 : $discount = $discount;
$page == '' ? $page = 1 : $page = $page;

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

        // update main table
        $query = "UPDATE price_comparison SET `updated_id` = :updated_id,  `updated_at` = now() WHERE id = :id";
        $stmt = $db->prepare($query);
        $stmt->bindParam(':updated_id', $uid);
        $stmt->bindParam(':id', $price_id);
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

        $pre_vat = '';

        $_id = IsExist($price_id, $pre_vat, $db);
        if($_id == 0)
        {
        
            $query = "INSERT INTO price_comparison_total
            SET
                `price_id` = :price_id,
                `page` = :page,
                `discount` = :discount,
                `vat` = :vat,
                `show_vat` = :show_vat,
                `show_t` = :show_t,
                `valid` = :valid, ";
if($total1 != '')
{
    $query .= " `total1` = :total1, ";
}
if($total2 != '')
{
    $query .= " `total2` = :total2, ";
}
if($total3 != '')
{
    $query .= " `total3` = :total3, ";
}
                
            
            $query .= " `status` = 0,
                `create_id` = :create_id,
                `created_at` =  now() ";

            // prepare the query
            $stmt = $db->prepare($query);

            // bind the values
            $stmt->bindParam(':price_id', $price_id);
            $stmt->bindParam(':page', $page);
            $stmt->bindParam(':discount', $discount);
            $stmt->bindParam(':vat', $vat);
            $stmt->bindParam(':show_vat', $show_vat);
            $stmt->bindParam(':show_t', $show_t);
            $stmt->bindParam(':valid', $valid);
            if($total1 != '')
            {
                $stmt->bindParam(':total1', $total1);
            }
            if($total2 != '')
            {
                $stmt->bindParam(':total2', $total2);
            }
            if($total3 != '')
            {
                $stmt->bindParam(':total3', $total3);
            }
           

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
            $query = "update price_comparison_total
                SET
                    `page` = :page,
                    `discount` = :discount,
                    `vat` = :vat,
                    `show_vat` = :show_vat,
                    `show_t` = :show_t,
                    `valid` = :valid,";
                    if($total1 != '')
                    {
                        $query .= " `total1` = :total1, ";
                    }
                    elseif($total1 == '')
                    {
                        $query .= " `total1` = null, ";
                    }
                    if($total2 != '')
                    {
                        $query .= " `total2` = :total2, ";
                    }
                    elseif($total2 == '')
                    {
                        $query .= " `total2` = null, ";
                    }
                    if($total3 != '')
                    {
                        $query .= " `total3` = :total3, ";
                    }
                    elseif($total3 == '')
                    {
                        $query .= " `total3` = null, ";
                    }
            $query .= "
                    `updated_id` = :updated_id,
                    `updated_at` = now()
                    where id = :id";

            // prepare the query
            $stmt = $db->prepare($query);

            // bind the values
            $stmt->bindParam(':page', $page);
            $stmt->bindParam(':discount', $discount);
            $stmt->bindParam(':vat', $vat);
            $stmt->bindParam(':show_vat', $show_vat);
            $stmt->bindParam(':show_t', $show_t);
            $stmt->bindParam(':valid', $valid);
            
            if($total1 != '')
            {
                $stmt->bindParam(':total1', $total1);
            }
            if($total2 != '')
            {
                $stmt->bindParam(':total2', $total2);
            }
            if($total3 != '')
            {
                $stmt->bindParam(':total3', $total3);
            }
     
            
            $stmt->bindParam(':updated_id', $user_id);

            $stmt->bindParam(':id', $_id);

            $last_id = $price_id;
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

        // // recaculate prduct amount with vat
        // if($pre_vat != 'P' && $vat == 'P')
        // {
        //     $query = "update quotation_page_type_block
        //         SET
        //             `amount` = qty * price * 1.12 * (1 - discount / 100) * ratio
             
        //             where quotation_id = :id and type in ('image', 'noimage') ";

        //     // prepare the query
        //     $stmt = $db->prepare($query);

        //     $stmt->bindParam(':id', $last_id, PDO::PARAM_INT);

        //     // execute the query, also check if query was successful
        //     try {
        //         // execute the query, also check if query was successful
        //         if (!$stmt->execute()) {
        //             $arr = $stmt->errorInfo();
        //             error_log($arr[2]);
        //             $db->rollback();
        //             http_response_code(501);
        //             echo json_encode(array("Failure at " . date("Y-m-d") . " " . date("h:i:sa") . " " . $arr[2]));
        //             die();
        //         }
        //     } catch (Exception $e) {
        //         error_log($e->getMessage());
        //         $db->rollback();
        //         http_response_code(501);
        //         echo json_encode(array("Failure at " . date("Y-m-d") . " " . date("h:i:sa") . " " . $e->getMessage()));
        //         die();
        //     }
        // }

        // if($pre_vat == 'P' && $vat !== 'P')
        // {
        //     $query = "update quotation_page_type_block
        //         SET
        //             `amount` = qty * price * (1 - discount / 100) * ratio
             
        //             where quotation_id = :id";

        //     // prepare the query
        //     $stmt = $db->prepare($query);

        //     $stmt->bindParam(':id', $last_id, PDO::PARAM_INT);

        //     // execute the query, also check if query was successful
        //     try {
        //         // execute the query, also check if query was successful
        //         if (!$stmt->execute()) {
        //             $arr = $stmt->errorInfo();
        //             error_log($arr[2]);
        //             $db->rollback();
        //             http_response_code(501);
        //             echo json_encode(array("Failure at " . date("Y-m-d") . " " . date("h:i:sa") . " " . $arr[2]));
        //             die();
        //         }
        //     } catch (Exception $e) {
        //         error_log($e->getMessage());
        //         $db->rollback();
        //         http_response_code(501);
        //         echo json_encode(array("Failure at " . date("Y-m-d") . " " . date("h:i:sa") . " " . $e->getMessage()));
        //         die();
        //     }
        // }

        // if(($pre_vat == 'P' && $vat !== 'P') || ($pre_vat != 'P' && $vat == 'P'))
        // {
        //     // update quotation_page_type.real_amount
        //     $query = "UPDATE quotation_page_type p,( SELECT type_id, sum(amount)  as mysum FROM quotation_page_type_block GROUP BY type_id) as s
        //                 SET p.real_amount = s.mysum
        //                 WHERE p.id = s.type_id
        //                 and p.quotation_id = :id
        //                 and p.block_type = 'A' ";

        //     // prepare the query
        //     $stmt = $db->prepare($query);

        //     $stmt->bindParam(':id', $last_id, PDO::PARAM_INT);

        //     // execute the query, also check if query was successful
        //     try {
        //         // execute the query, also check if query was successful
        //         if (!$stmt->execute()) {
        //             $arr = $stmt->errorInfo();
        //             error_log($arr[2]);
        //             $db->rollback();
        //             http_response_code(501);
        //             echo json_encode(array("Failure at " . date("Y-m-d") . " " . date("h:i:sa") . " " . $arr[2]));
        //             die();
        //         }
        //     } catch (Exception $e) {
        //         error_log($e->getMessage());
        //         $db->rollback();
        //         http_response_code(501);
        //         echo json_encode(array("Failure at " . date("Y-m-d") . " " . date("h:i:sa") . " " . $e->getMessage()));
        //         die();
        //     }
        // }
        

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


function IsExist($price_id, &$pre_vat, $db)
{
    $sql = "SELECT id, vat from price_comparison_total where price_id = :price_id";
           
    $stmt = $db->prepare($sql);
    $stmt->bindParam(':price_id',  $price_id);
    $stmt->execute();

    $_id = 0;
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        $_id = $row['id'];
        $pre_vat = $row['vat'];
    }

    return $_id;
}
