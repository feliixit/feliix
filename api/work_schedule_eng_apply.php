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

$items = isset($_POST['items']) ? $_POST['items'] : '';
$man_power = isset($_POST['man_power']) ? $_POST['man_power'] : '';

$man_power_arr = array();
if($man_power != '')
    $man_power_arr = json_decode($man_power, true);

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
        
        $is_existed = false;

        // sum the man power by type every 7 days
        $weeks = 0;
        if(count($man_power_arr) != 0)
        {
            $weekly_man_power1 = [];
            $weekly_man_power2 = [];
            $weekly_man_power3 = [];
            $weekly_man_power4 = [];
            $weekly_man_power5 = [];
            $weekly_man_power6 = [];

            foreach($man_power_arr as $key => $value)
            {
                if($key == 'man_power1')
                {
                    $weeks = ceil(count($value) / 7);

                    $temp = array();
                    $temp = array_chunk($value, 7);
                    for($i = 0; $i < count($temp); $i++)
                    {
                        // sum $temp[$i]
                        $sum = 0;
                        foreach($temp[$i] as $k => $v)
                        {
                            if($v == '')
                                $v = 0;
                            $sum += $v;
                        }
                        $weekly_man_power1[] = $sum;
                    }
                }
                if($key == 'man_power2')
                {
                    $temp = array();
                    $temp = array_chunk($value, 7);
                    for($i = 0; $i < count($temp); $i++)
                    {
                        // sum $temp[$i]
                        $sum = 0;
                        foreach($temp[$i] as $k => $v)
                        {
                            if($v == '')
                                $v = 0;
                            $sum += $v;
                        }
                        $weekly_man_power2[] = $sum;
                    }
                }
                if($key == 'man_power3')
                {
                    $temp = array();
                    $temp = array_chunk($value, 7);
                    for($i = 0; $i < count($temp); $i++)
                    {
                        // sum $temp[$i]
                        $sum = 0;
                        foreach($temp[$i] as $k => $v)
                        {
                            if($v == '')
                                $v = 0;
                            $sum += $v;
                        }
                        $weekly_man_power3[] = $sum;
                    }
                }
                if($key == 'man_power4')
                {
                    $temp = array();
                    $temp = array_chunk($value, 7);
                    for($i = 0; $i < count($temp); $i++)
                    {
                        // sum $temp[$i]
                        $sum = 0;
                        foreach($temp[$i] as $k => $v)
                        {
                            if($v == '')
                                $v = 0;
                            $sum += $v;
                        }
                        $weekly_man_power4[] = $sum;
                    }
                }
                if($key == 'man_power5')
                {
                    $temp = array();
                    $temp = array_chunk($value, 7);
                    for($i = 0; $i < count($temp); $i++)
                    {
                        // sum $temp[$i]
                        $sum = 0;
                        foreach($temp[$i] as $k => $v)
                        {
                            if($v == '')
                                $v = 0;
                            $sum += $v;
                        }
                        $weekly_man_power5[] = $sum;
                    }
                }
                if($key == 'man_power6')
                {
                    $temp = array();
                    $temp = array_chunk($value, 7);
                    for($i = 0; $i < count($temp); $i++)
                    {
                        // sum $temp[$i]
                        $sum = 0;
                        foreach($temp[$i] as $k => $v)
                        {
                            if($v == '')
                                $v = 0;
                            $sum += $v;
                        }
                        $weekly_man_power6[] = $sum;
                    }
                }
            }
        }

        $weeks_array = [];
        $weeks_array_json = '';
        if($weeks > 0)
        {
            for($i = 0; $i < $weeks; $i++)
            {
                $obj = array(
                    "man_power1" => $weekly_man_power1[$i], 
                    "man_power2" => $weekly_man_power2[$i],
                    "man_power3" => $weekly_man_power3[$i],
                    "man_power4" => $weekly_man_power4[$i], 
                    "man_power5" => $weekly_man_power5[$i],
                    "man_power6" => $weekly_man_power6[$i],
                );
                $weeks_array[] = $obj;
            }
        }

        $man_power_weekly = json_encode($weeks_array);
        
        if($_id != 0)
        {
            $is_existed = false;
            
            $query = "SELECT id
            FROM work_schedule_eng
            where id = :id";
            
            $stmt = $db->prepare( $query );
            $stmt->bindParam(':id', $_id);
            
            // execute the query
            $stmt->execute();
            
            while($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                $is_existed = true;
            }
        }
        
        if($_id == 0 || !$is_existed)
        {
            
            $query = "INSERT INTO work_schedule_eng
            SET
                `period` = 0,
                `rate_leadman` = 1400,
                `rate_sr_technician` = 1200,
                `rate_technician` = 1000,
                `rate_electrician` = 1400,
                `rate_helper` = 900,
                `items` = :items,
                `man_power` = :man_power,
                `man_power_weekly` = :man_power_weekly,
                `status` = 0,
                `create_id` = :create_id,
                `created_at` =  now() ";
            
            // prepare the query
            $stmt = $db->prepare($query);
            
            // bind the values
            $stmt->bindParam(':items', $items);
            $stmt->bindParam(':man_power', $man_power);
            $stmt->bindParam(':man_power_weekly', $man_power_weekly);
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

            $query = "update work_schedule_eng
                SET
                    `items` = :items,
                    `man_power` = :man_power,
                    `man_power_weekly` = :man_power_weekly,
                    `updated_id` = :updated_id,
                    `updated_at` = now()
                    where id = :id";
            
            // prepare the query
            $stmt = $db->prepare($query);
            
            // bind the values
            $stmt->bindParam(':items', $items);
            $stmt->bindParam(':man_power', $man_power);
            $stmt->bindParam(':man_power_weekly', $man_power_weekly);
            $stmt->bindParam(':updated_id', $user_id);
            
            $stmt->bindParam(':id', $_id);
            
            $last_id = $_id;
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
?>