<?php
 error_reporting(E_ERROR | E_PARSE);
//error_reporting(0);

header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: POST");
header("Access-Control-Max-Age: 3600");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

$jwt = (isset($_COOKIE['jwt']) ?  $_COOKIE['jwt'] : null);
include_once 'config/core.php';
include_once 'libs/php-jwt-master/src/BeforeValidException.php';
include_once 'libs/php-jwt-master/src/ExpiredException.php';
include_once 'libs/php-jwt-master/src/SignatureInvalidException.php';
include_once 'libs/php-jwt-master/src/JWT.php';

include_once 'config/database.php';
include_once 'objects/user.php';




use \Firebase\JWT\JWT;
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

        $database = new Database();
        $db = $database->getConnection();

        $user = new User($db);

        $user->email = $decoded->data->email;
        $user_exists = $user->userCanLogin();

        //$user = $decoded->data->username;
        $department = $decoded->data->department;
        $title = $decoded->data->position;
        $is_manager = $user->is_manager;
        $sick_leave = $user->sick_leave;
        $annual_leave = $user->annual_leave;
        $manager_leave = $user->manager_leave;
        $head_of_department = $user->head_of_department;
        $is_viewer = $user->is_viewer;
        $leave_level = $user->leave_level;
        $user_id = $user->id;

        //echo json_encode(array("username" => $user, "department" => $department, "title" => $title));

        echo json_encode(array("username" => $user->username, "department" => $department, "title" => $title, "is_manager" => $is_manager, "sick_leave" => $sick_leave, "annual_leave" => $annual_leave, "manager_leave" => $manager_leave,  "head_of_department" => $head_of_department , "is_viewer" => $is_viewer, "user_id" => $user_id, "leave_level" => $leave_level, "email" => $user->email));

    }
        // if decode fails, it means jwt is invalid
    catch (Exception $e){

        http_response_code(401);

        echo json_encode(array("message" => "Access denied."));
        die();
    }
}