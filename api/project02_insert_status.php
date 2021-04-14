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
$project_status_edit = (isset($_POST['project_status_edit']) ?  $_POST['project_status_edit'] : 0);
$project_status_reason = (isset($_POST['project_status_reason']) ?  $_POST['project_status_reason'] : '');

$pre_status = GetProjectPreStatus($db, $pid);


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

                if($project_status_edit != '9' && $project_status_edit != '6')
                {
                    $query = "update project_main
                    SET
                        
                        project_status_id = :status,
                        updated_at = NULL,
                        updated_id = 0
                    
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
                        updated_id = :uid
                    
                    where id = :project_id ";

                    // prepare the query
                    $stmt1 = $db->prepare($query);

                    $stmt1->bindParam(':project_id', $pid);
                    $stmt1->bindParam(':status', $project_status_edit);
                    $stmt1->bindParam(':uid', $user_id);
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

        if($last_id != 0)
            SendNotifyMail($pid, $pre_status);

        return $last_id;

function GetProjectPreStatus($db, $project_id)
{
    $status = "";

    $sql = "SELECT ps.project_status 
            from project_statuses p
            LEFT JOIN project_status ps 
            ON p.status = ps.id
            WHERE p.project_id = " . $project_id . "  
            ORDER BY p.created_at DESC
            LIMIT 1";

    $stmt = $db->prepare( $sql );
    $stmt->execute();

    while($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        $status = $row['project_status'];
    }

    if($status == "")
    {
        $sql = "SELECT ps.project_status 
                    from project_main p
                    LEFT JOIN project_status ps 
            ON p.project_status_id = ps.id
            WHERE p.id = " . $project_id . "  ";

        $stmt = $db->prepare( $sql );
        $stmt->execute();

        while($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $status = $row['project_status'];
        }
    }

    return $status;
}

function SendNotifyMail($bid, $pre_status)
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
        LEFT JOIN user ON pm.create_id = user.id  WHERE pm.id = " . $bid . " ";

    $stmt = $db->prepare( $sql );
    $stmt->execute();

    $project_name = "";
    $username = "";
    $created_at = "";
    $project_category = "";
    $client_type = "";
    $project_id = "";
    $priority = "";
    $estimate_close_prob = "";
    $project_status = "";

    while($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        $project_name = $row['project_name'];
        $username = $row['username'];
        $created_at = $row['created_at'];
        $project_category = $row['category'];
        $client_type = $row['client_type'];
        $project_id = $row['id'];
        $priority = $row['priority'];
        $estimate_close_prob = $row['estimate_close_prob'];
        $project_status = $row['project_status'];
    }

    project02_status_change_notify_mail($project_name, $project_category, $username, $created_at, $client_type, $priority, $estimate_close_prob, $project_status, $pre_status, $project_id);

}