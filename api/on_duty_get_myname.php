<?php
 error_reporting(E_ERROR | E_PARSE);
//error_reporting(0);

header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: POST");
header("Access-Control-Max-Age: 3600");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

$jwt = (isset($_POST['jwt']) ?  $_POST['jwt'] : null);
include_once 'config/core.php';
include_once 'libs/php-jwt-master/src/BeforeValidException.php';
include_once 'libs/php-jwt-master/src/ExpiredException.php';
include_once 'libs/php-jwt-master/src/SignatureInvalidException.php';
include_once 'libs/php-jwt-master/src/JWT.php';
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

        $user = $decoded->data->username;
        $department = $decoded->data->department;
        $title = $decoded->data->position;
        $is_manager = $decoded->data->is_manager;
        $sick_leave = $decoded->data->sick_leave;
        $annual_leave = $decoded->data->annual_leave;
        $head_of_department = $decoded->data->head_of_department;

        //echo json_encode(array("username" => $user, "department" => $department, "title" => $title));

        echo json_encode(array("username" => $user, "department" => $department, "title" => $title, "is_manager" => $is_manager, "sick_leave" => $sick_leave, "annual_leave" => $annual_leave, "head_of_department" => $head_of_department));

    }
        // if decode fails, it means jwt is invalid
    catch (Exception $e){

        http_response_code(401);

        echo json_encode(array("message" => "Access denied."));
        die();
    }
}