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
include_once 'mail.php';

$database = new Database();
$db = $database->getConnection();

$uid = $user_id;
$pid = (isset($_POST['pid']) ?  $_POST['pid'] : 0);
$stage_sequence = (isset($_POST['stage_sequence']) ?  $_POST['stage_sequence'] : 0);
$project_stage = (isset($_POST['project_stage']) ?  $_POST['project_stage'] : 0);
$stage_status = (isset($_POST['stage_status']) ?  $_POST['stage_status'] : 0);



$query = "INSERT INTO project_stages
                SET
                    project_id = :project_id,
                    sequence = :sequence,
                    stage_id = :stage_id,
                    stages_status_id = :stages_status_id,
                   
                    create_id = :create_id,
                    created_at = now()";
    
        // prepare the query
        $stmt = $db->prepare($query);
    
        // bind the values
        $stmt->bindParam(':project_id', $pid);
        $stmt->bindParam(':sequence', $stage_sequence);
        $stmt->bindParam(':stage_id', $project_stage);
        $stmt->bindParam(':stages_status_id', $stage_status);
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

        if($last_id != 0)
            SendNotifyMail($last_id);

        return $last_id;

function SendNotifyMail($bid)
{
    $database = new Database();
    $db = $database->getConnection();

    $sql = "SELECT pm.id, 
                COALESCE(pc.category, '') category, 
                `sequence`, 
                p.id pid,
                p.project_name,
                pst.id project_stage_id, 
                pst.`stage`, 
                (CASE `stages_status_id` WHEN '1' THEN 'Ongoing' WHEN '2' THEN 'Pending' WHEN '3' THEN 'Close' END ) as `stages_status`, 
                `stages_status_id`, 
                DATE_FORMAT(pm.created_at, '%Y-%m-%d') START, 
                pm.create_id,
                user.username, 
                DATE_FORMAT(pm.created_at, '%Y-%m-%d %T') created_at, 
                0 replies, 
                0 post, 
                '' recent,
                p.create_id main_id
            FROM project_stages pm 
            LEFT JOIN project_main p ON pm.project_id = p.id
            LEFT JOIN project_stage pst ON pm.stage_id = pst.id 
            LEFT JOIN user ON pm.create_id = user.id 
            LEFT JOIN project_category pc ON p.catagory_id = pc.id 
            WHERE pm.id = " . $bid . "  ";

    $stmt = $db->prepare( $sql );
    $stmt->execute();

    $project_name = "";
    $username = "";
    $created_at = "";
    $stage_name = "";
    $stage_status = "";
    $project_id = "";
    $stage_create_id = "";
    $category = "";
    $main_id = "";

    while($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        $project_name = $row['project_name'];
        $username = $row['username'];
        $created_at = $row['created_at'];
        $stage_name = $row['stage'];
        $stage_status = $row['stages_status'];
        $project_id = $row['pid'];
        $stage_create_id = $row['create_id'];
        $category = $row['category'];
        $main_id = $row['main_id'];
    }

    project02_stage_notify_mail($stage_name, $project_name, $username, $created_at, $stage_status, $project_id, $stage_create_id, $category, $main_id);

}