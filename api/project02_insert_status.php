<?php
error_reporting(E_ERROR | E_PARSE);

header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: *");
header("Access-Control-Max-Age: 3600");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

$jwt = (isset($_COOKIE['jwt']) ?  $_COOKIE['jwt'] : null);
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

$uid = $user_id;
$pid = (isset($_POST['pid']) ?  $_POST['pid'] : 0);
$project_status_edit = (isset($_POST['project_status_edit']) ?  $_POST['project_status_edit'] : 0);
$project_status_reason = (isset($_POST['project_status_reason']) ?  $_POST['project_status_reason'] : '');


$query = "INSERT INTO project_statuses
                SET
                    project_id = :project_id,
                    status = :status,
                    reason = :reason,
                   
                    create_id = :create_id,
                    created_at = now()";
    
        // prepare the query
        $stmt = $db->prepare($query);
    
        // bind the values
        $stmt->bindParam(':project_id', $pid);
        $stmt->bindParam(':status', $project_status_edit);
        $stmt->bindParam(':reason', $project_status_reason);
        $stmt->bindParam(':create_id', $user_id);

        $last_id = 0;
        // execute the query, also check if query was successful
        try {
            // execute the query, also check if query was successful
            if ($stmt->execute()) {
                $last_id = $db->lastInsertId();

                if($project_status_edit !== '9')
                {
                    $query = "update project_main
                    SET
                        
                        project_status_id = :status
                    
                    where id = :project_id ";

                    // prepare the query
                    $stmt1 = $db->prepare($query);

                    $stmt1->bindParam(':project_id', $pid);
                    $stmt1->bindParam(':status', $project_status_edit);
                }
                else
                {
                    $query = "update project_main
                    SET
                        project_status_id = :status,
                        updated_at = now(),
                        updated_id = :id
                    
                    where id = :project_id ";

                    // prepare the query
                    $stmt1 = $db->prepare($query);

                    $stmt1->bindParam(':project_id', $pid);
                    $stmt1->bindParam(':status', $project_status_edit);
                    $stmt->bindParam(':id', $user_id);
                }

                $stmt1->execute();

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

