<?php

error_reporting(E_ALL);
ini_set('display_errors', 1);
$jwt = (isset($_COOKIE['jwt']) ?  $_COOKIE['jwt'] : null);
include_once 'config/core.php';
include_once 'libs/php-jwt-master/src/BeforeValidException.php';
include_once 'libs/php-jwt-master/src/ExpiredException.php';
include_once 'libs/php-jwt-master/src/SignatureInvalidException.php';
include_once 'libs/php-jwt-master/src/JWT.php';

include_once 'mail.php';

use \Firebase\JWT\JWT;

$method = $_SERVER['REQUEST_METHOD'];


if (!isset($jwt)) {
    http_response_code(401);

    echo json_encode(array("message" => "Access denied."));
    die();
} else {
    try {
        // decode jwt
        $decoded = JWT::decode($jwt, $key, array('HS256'));
        $user_id = $decoded->data->id;
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

switch ($method) {
    case 'GET':
        $uid = $user_id;
        $pid = (isset($_GET['pid']) ?  $_GET['pid'] : 0);

        $sql = "SELECT pm.id, 
        pm.iq_name,
        pm.task_id,
        pm.serial_name
  FROM iq_main pm 
        left join project_other_task pot on pm.task_id = pot.id
        left join project_stages ps on pot.stage_id = ps.id
        LEFT JOIN user c_user ON pm.create_id = c_user.id 
        LEFT JOIN user u_user ON pm.updated_id = u_user.id 
        left join project_main p on ps.project_id = p.id
        where ps.project_id = " . $pid . " and pm.status <> -1";

        $sql = $sql . " ORDER BY pm.id ";

        $merged_results = array();

        $stmt = $db->prepare($sql);
        $stmt->execute();

        $id = 0;
        $iq_name = "";
        $task_id = "";
        $serial_name = "";
       

        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {

            $id = $row['id'];
            $iq_name = $row['iq_name'];
            $task_id = $row['task_id'];
            $serial_name = $row['serial_name'];

                $merged_results[] = array(
                    "id" => $id,
                    "iq_name" => $iq_name,
                    "task_id" => $task_id,
                    "serial_name" => $serial_name,
                
                    
                );

           
            }


        echo json_encode($merged_results, JSON_UNESCAPED_SLASHES);

        break;

}

