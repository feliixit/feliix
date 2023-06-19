<?php
error_reporting(E_ALL);
//header("Access-Control-Allow-Origin: https://feliix.myvnc.com");
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: POST");
header("Access-Control-Max-Age: 3600");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

$jwt = (isset($_POST['jwt']) ?  $_POST['jwt'] : null);

$_id = isset($_POST['id']) ? $_POST['id'] : 0;
$group = isset($_POST['group']) ? $_POST['group'] : [];
$group_array = json_decode($group, true);

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
        $stmt->bindParam(':id', $_id);
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

        // price_comparison_option
        $query = "UPDATE price_comparison_legend set `status` = -1, `updated_id` = :updated_id,  `updated_at` = now()
                WHERE
                `group_id` in (select id from price_comparison_group where p_id = :p_id)";

        // prepare the query
        $stmt = $db->prepare($query);

        // bind the values
        $stmt->bindParam(':p_id', $_id);
        $stmt->bindParam(':updated_id', $user_id);
        
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


        // delete price_comparison_group
        $query = "UPDATE price_comparison_group set `status` = -1, `updated_id` = :updated_id,  `updated_at` = now()
                WHERE
                `p_id` = :p_id";

        // prepare the query
        $stmt = $db->prepare($query);

        // bind the values
        $stmt->bindParam(':p_id', $_id);
        $stmt->bindParam(':updated_id', $user_id);

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

        $i = 0;
        $j = 0;

        foreach($group_array as &$group) {

            $query = "INSERT INTO price_comparison_group
            SET
                `p_id` = :p_id,
                `title` = :title,
                `sn` = :sn,
                `color` = :color,
                                
                `status` = 0,
                `create_id` = :create_id,
                `created_at` =  now() ";

            // prepare the query
            $stmt = $db->prepare($query);

            $i = $i + 1;
            // bind the values
            $stmt->bindParam(':p_id', $_id);
            $stmt->bindParam(':title', $group['title']);
            $stmt->bindParam(':sn', $i);
            $stmt->bindParam(':color', $group['color']);
         
            
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

            $legend_array = $group['legend'];

            foreach($legend_array as &$legend) {

                if($legend['sn'] == 0)
                {
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
        
                    $j = $j + 1;
                    // bind the values
                    $stmt->bindParam(':group_id', $last_id);
                    $stmt->bindParam(':title', $legend['title']);
                    $stmt->bindParam(':sn', $j);
                    $stmt->bindParam(':color', $legend['color']);
                 
                    
                    $stmt->bindParam(':create_id', $user_id);
                
                    $last_legend_id = 0;
                    // execute the query, also check if query was successful
                    try {
                        // execute the query, also check if query was successful
                        if ($stmt->execute()) {
                            $last_legend_id = $db->lastInsertId();
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
                    $query = "update price_comparison_legend
                    SET
                        `group_id` = :group_id,
                        `title` = :title,
                        `sn` = :sn,
                        `color` = :color,
                        `status` = 0
                    WHERE
                        `id` = :id
                         ";
        
                    // prepare the query
                    $stmt = $db->prepare($query);
        
                    $j = $j + 1;
                    // bind the values
                    $stmt->bindParam(':group_id', $last_id);
                    $stmt->bindParam(':id', $legend['id']);
                    $stmt->bindParam(':title', $legend['title']);
                    $stmt->bindParam(':sn', $j);
                    $stmt->bindParam(':color', $legend['color']);
                
                    $last_legend_id = 0;
                    // execute the query, also check if query was successful
                    try {
                        // execute the query, also check if query was successful
                        if ($stmt->execute()) {
                            $last_legend_id = $db->lastInsertId();
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

