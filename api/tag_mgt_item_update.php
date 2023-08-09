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

include_once 'mail.php';

use \Firebase\JWT\JWT;
use Google\Cloud\Storage\StorageClient;

$method = $_SERVER['REQUEST_METHOD'];

$tag_array = array();
$tag_read = false;

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
            
            $gid = (isset($_POST['lv1']) ?  $_POST['lv1'] : 0);

            $petty_list = (isset($_POST['level2']) ?  $_POST['level2'] : '[]');
            $petty_array = json_decode($petty_list, true);
            
            try {
                $pre_array = GetPreTags($gid, $db);
                $diff = show_diff($pre_array, $petty_array, $db);

                // petty_list
                $query = "update tag_item
                set status = -1, sn = 0 where group_id = :gid";
                
                // prepare the query
                $stmt = $db->prepare($query);

                $stmt->bindParam(':gid', $gid);
                
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

                    if($petty_array[$i]['id'] == '0')
                    {
                        $query = "INSERT INTO tag_item
                        SET
                        `sn` = :sn,
                        `group_id` = :group_id,
                        `item_name` = :item_name,
                        `create_id` = :create_id,
                        `created_at` = now()";
                        
                        // prepare the query
                        $stmt = $db->prepare($query);
                        
                        // bind the values
                        $stmt->bindParam(':create_id', $user_id);
                        $stmt->bindParam(':group_id', $gid);
                        $stmt->bindParam(':item_name', $petty_array[$i]['item_name']);
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
                    else
                    {
                        $query = "update tag_item
                        set `item_name` = :item_name,
                        `sn` = :sn,
                        `status` = 1,
                        `create_id` = :create_id,
                        `created_at` = now()
                        where id = :id";
                        
                        // prepare the query
                        $stmt = $db->prepare($query);
                        
                        $stmt->bindParam(':create_id', $user_id);
                        $stmt->bindParam(':item_name', $petty_array[$i]['item_name']);
                        $stmt->bindParam(':sn', $sn);
                        $stmt->bindParam(':id', $petty_array[$i]['id']);
                        
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
                }
                
                $db->commit();

                // 有不同才寄信
                if(count($diff) > 0)
                {
                    $group_name = GetGroup($gid, $db);
                    tag_notification($user_name, $user_id, $group_name, $diff);
                }
                
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

        function GetPreTags($group_id, $db) {
            $query = "SELECT id, sn, item_name from tag_item where group_id = " . $group_id . " and status <> -1 order by sn";

            // prepare the query
            $stmt = $db->prepare($query);
            $stmt->execute();
            
            $result = array();
            while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                $result[] = $row;
            }

            return $result;
        }

        function GetGroup($group_id, $db) {
            $ret = "";
            $query = "SELECT group_name from tag_group where id = " . $group_id . " and status <> -1 ";

            // prepare the query
            $stmt = $db->prepare($query);
            $stmt->execute();
            
            $result = array();
            while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                $ret = $row['group_name'];
            }

            return $ret;
        }
        
        function show_diff($pre_item, $item, $db)
        {
            $diff = [];

            // 1. check if the item is new
            foreach ($item as $it) {
                if($it['id'] == 0)
                    $diff[] = "," . $it['item_name'];
            }
            // 2. check if the value is different
            foreach($pre_item as $it) {
                foreach($item as $i) {
                    if($it['id'] == $i['id'] && $it['item_name'] != $i['item_name'])
                    {
                        $diff[] =  $it['item_name'] . "," . $i['item_name'];
                        UpdateProductTags($it['item_name'], $i['item_name'], $db);
                    }
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
                    $diff[] = $it['item_name']  . ",";
            }

            return $diff;
        }

        function UpdateProductTags($old_item, $new_item, $db)
        {
            if($GLOBALS['tag_read'] == false)
            {
                $GLOBALS['product_tags'] = ReadProductTags($db);
                $GLOBALS['tag_read'] = true;
            }

            for($i = 0; $i < count($GLOBALS['product_tags']); $i++)
            {
                $tags = explode(",", $GLOBALS['product_tags'][$i]['tags']);
                for($j = 0; $j < count($tags); $j++)
                {
                    if($tags[$j] == $old_item)
                    {
                        $tags[$j] = $new_item;
                        UpdateProductTableTags($db, $GLOBALS['product_tags'][$i]['id'], implode(",", $tags));
                        break;
                    }
                }
            }
        }

        function ReadProductTags($db)
        {
            $query = "SELECT id, tags from product_category where status <> -1";

            // prepare the query
            $stmt = $db->prepare($query);
            $stmt->execute();
            
            $result = array();
            while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                $result[] = $row;
            }

            return $result;
        }

        function UpdateProductTableTags($db, $id, $tags)
        {
            $query = "update product_category set tags = :tags where id = :id";

            $stmt = $db->prepare($query);
            $stmt->bindParam(':tags', $tags);
            $stmt->bindParam(':id', $id);
            
            $stmt->execute();

        }