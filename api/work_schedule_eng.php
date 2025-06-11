<?php
error_reporting(0);
//header("Access-Control-Allow-Origin: https://feliix.myvnc.com");
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: *");
header("Access-Control-Max-Age: 3600");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

$id = (isset($_GET['id']) ?  $_GET['id'] : 0);
$jwt = (isset($_COOKIE['jwt']) ?  $_COOKIE['jwt'] : null);
$user_id = 0;

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

    // decode jwt
    $decoded = JWT::decode($jwt, $key, array('HS256'));
    $GLOBALS["user_id"] = $decoded->data->id;
    
    $user_name = $decoded->data->username;
    $user_department = $decoded->data->department;

    $merged_results = array();
    

    $query = "SELECT pm.id,
                    pm.period, 
                    pm.rate_leadman, 
                    pm.rate_sr_technician, 
                    pm.rate_technician, 
                    pm.`rate_electrician`, 
                    pm.rate_helper, 
                    pm.items,
                    pm.man_power,
                    pm.man_power_weekly,
                    pm.`status`,
                    c_user.username AS created_by, 
                    u_user.username AS updated_by,
                    DATE_FORMAT(pm.created_at, '%Y-%m-%d %H:%i:%s') created_at, 
                    DATE_FORMAT(pm.updated_at, '%Y-%m-%d %H:%i:%s') updated_at
                    FROM work_schedule_eng pm
                    LEFT JOIN user c_user ON pm.create_id = c_user.id 
                    LEFT JOIN user u_user ON pm.updated_id = u_user.id 
                    WHERE pm.status <> -1 and pm.id = " . $id . "; ";


    $stmt = $db->prepare($query);
    $stmt->execute();

    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        $id = $row['id'];

        $period = $row['period'];
        $rate_leadman = $row['rate_leadman'];
        $rate_sr_technician = $row['rate_sr_technician'];
        $rate_technician = $row['rate_technician'];
        $rate_electrician = $row['rate_electrician'];
        $rate_helper = $row['rate_helper'];
        $items = $row['items'];
        $man_power = $row['man_power'];
        $man_power_week = $row['man_power_weekly'];
        $status = $row['status'];

        $created_by = $row['created_by'];
        $updated_by = $row['updated_by'];
        $created_at = $row['created_at'];
        $updated_at = $row['updated_at'];

        if($period == "")
            $period = 0;
        if($rate_leadman == "")
            $rate_leadman = 1400;
        if($rate_sr_technician == "")
            $rate_sr_technician = 1200;
        if($rate_technician == "")
            $rate_technician = 1000;
        if($rate_electrician == "")
            $rate_electrician = 1400;
        if($rate_helper == "")
            $rate_helper = 900;

        if($items == null)
            $items = "[]";
        
        if($man_power == null)
            $man_power = "[]";

        if($man_power_week == null)
            $man_power_week = "[]";
        
        $merged_results[] = array(
            "id" => $id,
            "period" => $period,
            "rate_leadman" => $rate_leadman,
            "rate_sr_technician" => $rate_sr_technician,
            "rate_technician" => $rate_technician,
            "rate_electrician" => $rate_electrician,
            "rate_helper" => $rate_helper,
            "items" => $items,
            "man_power" => $man_power,
            "man_power_weekly" => $man_power_week,
            "status" => $status,
            "created_by" => $created_by,
            "updated_by" => $updated_by,
            "created_at" => $created_at,
            "updated_at" => $updated_at,
        );
    }



    echo json_encode($merged_results, JSON_UNESCAPED_SLASHES);
}


?>
