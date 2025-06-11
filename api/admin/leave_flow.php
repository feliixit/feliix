<?php

error_reporting(E_ALL);
ini_set('display_errors', 1);
$jwt = (isset($_COOKIE['jwt']) ?  $_COOKIE['jwt'] : null);
include_once '../config/core.php';
include_once '../libs/php-jwt-master/src/BeforeValidException.php';
include_once '../libs/php-jwt-master/src/ExpiredException.php';
include_once '../libs/php-jwt-master/src/SignatureInvalidException.php';
include_once '../libs/php-jwt-master/src/JWT.php';
use \Firebase\JWT\JWT;

$method = $_SERVER['REQUEST_METHOD'];


if ( !isset( $jwt ) ) {
    http_response_code(401);

    echo json_encode(array("message" => "Access denied."));
    die();
}
else
{
    try {
        // decode jwt
        $decoded = JWT::decode($jwt, $key, array('HS256'));
        // if(!$decoded->data->is_admin)
        // {
        //     http_response_code(401);

        //     echo json_encode(array("message" => "Access denied."));
        //     die();
        // }
    }
        // if decode fails, it means jwt is invalid
    catch (Exception $e){

        http_response_code(401);

        echo json_encode(array("message" => "Access denied."));
        die();
    }
}

header('Access-Control-Allow-Origin: *');

include_once '../config/database.php';
include_once '../objects/leave_flow.php';



$database = new Database();
$db = $database->getConnection();

switch ($method) {
    case 'GET':

        $pid  = (isset($_GET['pid']) ?  $_GET['pid'] : 0);

        $sql = "SELECT 0 as is_checked, f.id, username, d.department, flow FROM `leave_flow` f LEFT JOIN `user` u ON f.uid = u.id left join user_department d on f.apartment_id = d.id WHERE f.apartment_id = $pid AND u.`status` = 1 ";

        $sql = $sql . " ORDER BY id ";

        $merged_results = array();

        $stmt = $db->prepare( $sql );
        $stmt->execute();


        while($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $merged_results[] = $row;
        }

        echo json_encode($merged_results, JSON_UNESCAPED_SLASHES);

        break;

    case 'POST':
        // get database connection

        // instantiate product object
        $user = new LeaveFlow($db);
        $pid = (isset($_POST['pid']) ?  $_POST['pid'] : 0);
        $uid = (isset($_POST['uid']) ?  $_POST['uid'] : 0);
        $flow = (isset($_POST['flow']) ?  $_POST['flow'] : 0);

        $crud = (isset($_POST['crud']) ?  $_POST['crud'] : 0);
        $id = (isset($_POST['id']) ?  $_POST['id'] : 0);

        switch ($crud)
        {
            case 'insert':
                /* Bind parameters. Types: s = string, i = integer, d = double,  b = blob */
                $user->apartment_id = (int) $pid;
                $user->uid = (int) $uid;
                $user->flow = (int) $flow;

                $user->create();

                break;


            case 'del':
                $ids = explode(",", $id);
                foreach($ids as $item) {
                    $user->id = trim($item);
                    $user->delete();
                }

                if($id){
                    $out['message'] = "Member Deleted Successfully";
                }
                else{
                    $out['error'] = true;
                    $out['message'] = "Could not delete Member";
                }

                break;
        }

        break;
}



?>
