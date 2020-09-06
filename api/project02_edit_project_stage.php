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
$stage_id = (isset($_POST['stage_id']) ?  $_POST['stage_id'] : 0);
$project_stage_id = (isset($_POST['project_stage_id']) ?  $_POST['project_stage_id'] : 0);
$stages_status_id = (isset($_POST['stages_status_id']) ?  $_POST['stages_status_id'] : 0);
$sequence = (isset($_POST['sequence']) ?  $_POST['sequence'] : '');
$stage_edit_reason = (isset($_POST['stage_edit_reason']) ?  $_POST['stage_edit_reason'] : '');


$query = "INSERT INTO project_edit_stage
                SET
                    stage_id = :stage_id,
                    reason = :reason,
                   
                    create_id = :create_id,
                    created_at = now()";
    
        // prepare the query
        $stmt = $db->prepare($query);
    
        // bind the values
        $stmt->bindParam(':stage_id', $stage_id);
        $stmt->bindParam(':reason', $stage_edit_reason);
        $stmt->bindParam(':create_id', $uid);

        $last_id = 0;
        // execute the query, also check if query was successful
        try {
            // execute the query, also check if query was successful
            if ($stmt->execute()) {
                $last_id = $db->lastInsertId();

                $query = "update project_stages
                SET
                    sequence = :sequence,
                    stage_id = :stage_id,
                    stages_status_id = :stages_status_id,
                    updated_id = :create_id,
                    updated_at = now()
                
                where id = :id ";
    
                // prepare the query
                $stmt1 = $db->prepare($query);

                $stmt1->bindParam(':sequence', $sequence);
                $stmt1->bindParam(':stage_id', $project_stage_id);
                $stmt1->bindParam(':stages_status_id', $stages_status_id);
                $stmt1->bindParam(':create_id', $uid);
                $stmt1->bindParam(':id', $stage_id);

                if ($stmt1->execute()) {
                    $returnArray = array('ret' => $stage_id_to_edit);
                    $jsonEncodedReturnArray = json_encode($returnArray, JSON_PRETTY_PRINT);
                }
                else
                {
                    $arr = $stmt1->errorInfo();
                    error_log($arr[2]);
                }

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

