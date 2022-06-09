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
$project_name = (isset($_POST['project_name']) ?  $_POST['project_name'] : '');
$project_category = (isset($_POST['project_category']) ?  $_POST['project_category'] : 0);
$client_type = (isset($_POST['client_type']) ?  $_POST['client_type'] : 0);
$priority = (isset($_POST['priority']) ?  $_POST['priority'] : 0);
$status = (isset($_POST['status']) ?  $_POST['status'] : 0);
$reason = (isset($_POST['reason']) ?  $_POST['reason'] : '');
$probability = (isset($_POST['probability']) ?  $_POST['probability'] : 0);
$special_note = (isset($_POST['special_note']) ?  $_POST['special_note'] : '');
$special = (isset($_POST['special']) ?  $_POST['special'] : '');

if(trim($project_category) == "")
    $project_category = 0;
if(trim($client_type) == "")
    $client_type = 0;
if(trim($priority) == "")
    $priority = 0;
if(trim($status) == "")
    $status = 0;
if(trim($probability) == "")
    $probability = 0;



    $query = "INSERT INTO project_main
                SET
                    catagory_id = :catagory_id,
                    client_type_id = :client_type_id,
                    priority_id = :priority_id,
                    project_status_id = :project_status_id,
                    project_name = :project_name,
                    close_reason = :close_reason,
                    special_note = :special_note,
                    special = :special,
                    estimate_close_prob = :probability,
                    create_id = :create_id,
                    created_at = now()";

    if($status == 6 || $status == 9)
    {
        $query = "INSERT INTO project_main
        SET
            catagory_id = :catagory_id,
            client_type_id = :client_type_id,
            priority_id = :priority_id,
            project_status_id = :project_status_id,
            project_name = :project_name,
            close_reason = :close_reason,
            special_note = :special_note,
            special = :special,
            estimate_close_prob = :probability,
            create_id = :create_id,
            created_at = now(),
            updated_id = :create_id,
            updated_at = now()";
    }
    
        // prepare the query
        $stmt = $db->prepare($query);
    
        // sanitize
        $project_name=htmlspecialchars(strip_tags($project_name));
        $close_reason=htmlspecialchars(strip_tags($reason));
        $special_note=htmlspecialchars(strip_tags($special_note));
        $special=htmlspecialchars(strip_tags($special));
       
        // bind the values
        $stmt->bindParam(':catagory_id', $project_category);
        $stmt->bindParam(':client_type_id', $client_type);
        $stmt->bindParam(':priority_id', $priority);
        $stmt->bindParam(':project_status_id', $status);
        $stmt->bindParam(':project_name', $project_name);
        $stmt->bindParam(':close_reason', $reason);
        $stmt->bindParam(':special_note', $special_note);
        $stmt->bindParam(':special', $special);
        $stmt->bindParam(':probability', $probability);
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

        // insert project estimate_close_prob
    
        $query = "INSERT INTO project_est_prob
        SET
            project_id = :project_id,
            comment = :comment,
            prob = :prob,
          
            create_id = :create_id,
            created_at = now()";

        // prepare the query
        $stmt = $db->prepare($query);
    
        // bind the values
        $stmt->bindParam(':project_id', $last_id);
        $stmt->bindParam(':comment', $reason);
        $stmt->bindParam(':prob', $probability);

        $stmt->bindParam(':create_id', $user_id);

        // execute the query, also check if query was successful
        try {
            // execute the query, also check if query was successful
            if ($stmt->execute()) {
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
                    pc.id category_id, 
                    pct.client_type, 
                    pct.id client_type_id, 
                    pct.class_name pct_class, 
                    pp.priority, 
                    pp.id priority_id, 
                    pp.class_name pp_class, 
                    pm.project_name, 
                    COALESCE(ps.project_status, '') project_status, 
                    pm.estimate_close_prob, 
                    pm.designer,       
                    pm.`type`,       
                    pm.scope,       
                    pm.office_location,       
                    pm.background_client,       
                    pm.background_project,       
                    pm.contractor, 
                    user.username, 
                    user.id uid, 
                    DATE_FORMAT(pm.created_at, '%Y-%m-%d %T') created_at, 
                    DATE_FORMAT(pm.updated_at, '%Y-%m-%d') updated_at, 
                    COALESCE((SELECT project_stage.stage FROM project_stages LEFT JOIN project_stage ON project_stage.id = project_stages.stage_id WHERE project_stages.project_id = pm.id and project_stages.stages_status_id = 1 ORDER BY `sequence` desc LIMIT 1), '') stage, 
                    pm.location, 
                    pm.contactor, 
                    pm.contact_number, 
                    pm.client, 
                    pm.edit_reason 
            FROM project_main pm 
            LEFT JOIN project_category pc ON pm.catagory_id = pc.id 
            LEFT JOIN project_client_type pct ON pm.client_type_id = pct.id 
            LEFT JOIN project_priority pp ON pm.priority_id = pp.id 
            LEFT JOIN project_status ps ON pm.project_status_id = ps.id 
            LEFT JOIN project_stage pst ON pm.stage_id = pst.id 
            LEFT JOIN user ON pm.create_id = user.id  WHERE pm.id = " . $bid . "  ";

    $stmt = $db->prepare( $sql );
    $stmt->execute();

    $project_name = "";
    $username = "";
    $created_at = "";
    $category = "";
    $client_type = "";
    $priority = "";
    $project_status = "";
    $estimate_close_prob = "";
    $create_id = "";

    while($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        $project_name = $row['project_name'];
        $username = $row['username'];
        $created_at = $row['created_at'];
        $category = $row['category'];
        $client_type = $row['client_type'];
        $priority = $row['priority'];
        $project_status = $row['project_status'];
        $estimate_close_prob = $row['estimate_close_prob'];
        $create_id = $row['uid'];
    }

    project01_notify_mail('01', $project_name, $username, $created_at, $category, $client_type, $priority, $project_status, $estimate_close_prob, $bid, $create_id);
}