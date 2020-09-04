<?php
error_reporting(E_ERROR | E_PARSE);

header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: *");
header("Access-Control-Max-Age: 3600");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

$jwt = (isset($_COOKIE['jwt']) ?  $_COOKIE['jwt'] : null);
include_once 'config/core.php';
include_once 'config/conf.php';
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

        $user_id = $decoded->data->id;

    }
        // if decode fails, it means jwt is invalid
    catch (Exception $e){

        http_response_code(401);

        echo json_encode(array("message" => "Access denied."));
        die();
    }
}

include_once 'config/database.php';
$database = new Database();
$db = $database->getConnection();
$conf = new Conf();

$uid = $user_id;
$pid = (isset($_POST['pid']) ?  $_POST['pid'] : 0);

$stage_id_to_edit = (isset($_POST['stage_id_to_edit']) ?  $_POST['stage_id_to_edit'] : '');


$query = "INSERT INTO project_action_comment
                SET
                    project_id = :project_id,
                    comment = :comment,
                    picname1 = :filename1,
                    picname2 = :filename2,
                    create_id = :create_id,
                    created_at = now()";
    
        // prepare the query
        $stmt = $db->prepare($query);
    
        // bind the values
        $stmt->bindParam(':project_id', $pid);
        $stmt->bindParam(':comment', $comment);
        $stmt->bindParam(':filename1', $filename1);
        $stmt->bindParam(':filename2', $filename2);
        $stmt->bindParam(':create_id', $user_id);

        $last_id = 0;
        // execute the query, also check if query was successful
        try {
            // execute the query, also check if query was successful
            if ($stmt->execute()) {
                $last_id = $db->lastInsertId();

            }
            else
            {
                $arr = $stmt->errorInfo();
                error_log($arr[2]);
            }
        }
        catch (Exception $e)
        {
            error_log($e->getMessage());
        }


        return $last_id;

