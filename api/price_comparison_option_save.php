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
$option = isset($_POST['option']) ? $_POST['option'] : [];
$option_array = json_decode($option, true);

$org_option = isset($_POST['org_option']) ? $_POST['org_option'] : [];
$org_option_array = json_decode($org_option, true);

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

        // compare option_array and org_option_array, if different, update
        $sn = 1;
        foreach ( $option_array as $option )
        {
            // if $option['id'] not in $org_option_array, insert it, else update it
            $found = false;
            foreach ( $org_option_array as $org_option )
            {
                if ( $option['id'] == $org_option['id'] )
                {
                    $found = true;
                    break;
                }
            }

            if($found)
            {
                $sql = "UPDATE price_comparison_option SET title = :title, sn = :sn, color = :color, updated_id = :updated_id, updated_at = now() WHERE id = :id";
                $stmt = $db->prepare($sql);
                $stmt->bindParam(':title', $option['title']);
                $stmt->bindParam(':sn', $sn);
                $stmt->bindParam(':color', $option['color']);
                $stmt->bindParam(':updated_id', $uid);
                $stmt->bindParam(':id', $option['id']);
                $stmt->execute();
            }
            else
            {
                $sql = "INSERT INTO price_comparison_option (title, sn, color, p_id, create_id) VALUES (:title, :sn, :color, :p_id, :create_id)";
                $stmt = $db->prepare($sql);
                $stmt->bindParam(':title', $option['title']);
                $stmt->bindParam(':sn', $sn);
                $stmt->bindParam(':color', $option['color']);
                $stmt->bindParam(':p_id', $option['p_id']);
                $stmt->bindParam(':create_id', $uid);
                $stmt->execute();
            }


            $sn++;
        }

        foreach ( $org_option_array as $org_option )
        {
          
            $found = false;
            foreach ( $option_array as $option )
            {
                if ( $option['id'] == $org_option['id'] )
                {
                    $found = true;
                    break;
                }
            }

            // delete if not found
            if(!$found)
            {
                $sql = "UPDATE price_comparison_option SET `status` = -1 WHERE id = :id";
                $stmt = $db->prepare($sql);
                $stmt->bindParam(':id', $org_option['id']);
                $stmt->execute();
            }
            
        }

        

        $db->commit();

        
        http_response_code(200);
        echo json_encode(array("message" => "Success at " . date("Y-m-d") . " " . date("h:i:sa")));
        
    }
    catch (Exception $e){

        error_log($e->getMessage());
        $db->rollback();
        http_response_code(501);
        echo json_encode(array("Failure at " . date("Y-m-d") . " " . date("h:i:sa") . $e->getMessage()));
        die();

    }
}

