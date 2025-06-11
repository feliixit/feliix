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

// include_once 'mail.php';

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
            
            $petty_list = (isset($_POST['level1']) ?  $_POST['level1'] : '[]');
            $petty_array = json_decode($petty_list, true);
            $code = isset($_POST['code']) ? $_POST['code'] : '';
            
            try {
                //$pre_array = GetPreTags($db);

                //$diff = show_diff($pre_array, $petty_array);

                // delete previous data
                $query = "delete from office_items_brand where status = -1 and parent_code = '$code'";
                $stmt = $db->prepare($query);
                $stmt->execute();

                // petty_list
                $query = "update office_items_brand
                set status = -1 where parent_code = '$code'";
                
                
                // prepare the query
                $stmt = $db->prepare($query);
                
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
                
                for ($i = 0; $i < count($petty_array); $i++) {

                    $sn = $i+1;

                    $query = "INSERT INTO office_items_brand
                    SET
                    `sn` = :sn,
                    `code` = :code,
                    `category` = :category,
                    `parent_code` = '$code',
                    `create_id` = :create_id,
                    `created_at` = now()";
                    
                    // prepare the query
                    $stmt = $db->prepare($query);
                    
                    // bind the values
                    $stmt->bindParam(':create_id', $user_id);
                    $stmt->bindParam(':code', $petty_array[$i]['code']);
                    $stmt->bindParam(':category', $petty_array[$i]['category']);

                    $stmt->bindParam(':sn', $sn);
                    
                    
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
                
                $db->commit();
                
                // // 有不同才寄信
                // if(count($diff) > 0)
                //     tag_group_notification($user_name, $user_id, $diff);

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
        

        function GetPreTags($db) {
            $query = "SELECT id, sn, group_name from tag_group where status <> -1 order by sn";

            // prepare the query
            $stmt = $db->prepare($query);
            $stmt->execute();
            
            $result = array();
            while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                $result[] = $row;
            }

            return $result;
        }

        function show_diff($pre_item, $item)
        {
            $diff = [];

            // 1. check if the item is new
            foreach ($item as $it) {
                if($it['id'] == 0)
                    $diff[] = "," . $it['group_name'];
            }
            // 2. check if the value is different
            foreach($pre_item as $it) {
                foreach($item as $i) {
                    if($it['id'] == $i['id'] && $it['group_name'] != $i['group_name'])
                        $diff[] =  $it['group_name'] . "," . $i['group_name'];
                }
            }
            // 3. check if the key is deleted
            foreach($pre_item as $it) {
                $found = false;
                foreach($item as $i) {
                    if($it['id'] == $i['id'])
                        $found = true;
                }
                if(!$found)
                    $diff[] = $it['group_name'] . ",";
            }

            return $diff;
        }