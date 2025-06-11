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
$user_id = 0;

if (!isset($jwt)) {
    http_response_code(401);

    echo json_encode(array("message" => "Access denied."));
    die();
} else {
    try {
        // decode jwt
        $decoded = JWT::decode($jwt, $key, array('HS256'));
        $GLOBALS["user_id"] = $decoded->data->id;

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

        echo json_encode(array("message" => "Access denied."));
        die();
    }
}

header('Access-Control-Allow-Origin: *');

include_once 'config/database.php';

$database = new Database();
$db = $database->getConnection();

$conf = new Conf();

switch ($method) {

    case 'POST':
        // get database connection
        $item_str = (isset($_POST['items']) ?  $_POST['items'] : []);
        $uid = $user_id;
        $o_id = (isset($_POST['od_id']) ?  $_POST['od_id'] : 0);
        $comment = (isset($_POST['comment']) ? $_POST['comment'] : '');
        $type = (isset($_POST['type']) ? $_POST['type'] : '');

        $items = json_decode($item_str, true);

        $diff = [];

        // update main table
        $query = "UPDATE od_main SET `updated_id` = :updated_id,  `updated_at` = now() WHERE id = :id";
        $stmt = $db->prepare($query);
        $stmt->bindParam(':updated_id', $uid);
        $stmt->bindParam(':id', $o_id);
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

        for($i=0; $i<count($items); $i++) {

            // get previous block confirm
            $query = "select which_pool, as_sample from od_item where id = :id";
            $stmt = $db->prepare($query);
            $stmt->bindParam(':id', $items[$i]['id']);
            $stmt->execute();
            $row = $stmt->fetch(PDO::FETCH_ASSOC);

            $pre_which_pool = $row['which_pool'];
            $pre_as_sample = $row['as_sample'];

            $id = $items[$i]['id'];

            $which_pool = (isset($items[$i]['which_pool']) ?  $items[$i]['which_pool'] : '');
            $as_sample = (isset($items[$i]['as_sample']) ?  $items[$i]['as_sample'] : '');

        
            $query = "update od_item
                SET
                `which_pool` = :which_pool,
                `as_sample` = :as_sample,
                ";
                
            $query .= "         
                `updated_id` = :updated_id,
                `updated_at` = now()
            where id = :id";


            // prepare the query
            $stmt = $db->prepare($query);

            // bind the values
            $stmt->bindParam(':which_pool', $which_pool);
            $stmt->bindParam(':as_sample', $as_sample);

            $stmt->bindParam(':updated_id', $uid);
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
                }
            } catch (Exception $e) {
                error_log($e->getMessage());
            }

        }

        $jsonEncodedReturnArray = json_encode($diff, JSON_PRETTY_PRINT);

        $items_array = [];

        foreach ($items as $item) {
            foreach ($diff as $di)
            {
                // find id with value in strings
                $temp = explode(",", $di);
                $sets = array();
                foreach ($temp as $value) {
                    $array = explode(': ', $value);
                    $array[0] = str_replace(' ', '', $array[0]);
                    $array[0] = str_replace("'", '', $array[0]);
                    $array[1] = trim($array[1], "'");
                    $sets[$array[0]] = $array[1];
                }

                if($item['id'] == $sets["id"])
                {
                    $items_array[] = $item;
                    break;
                }
            }
        }

        echo $jsonEncodedReturnArray;

        break;
}
