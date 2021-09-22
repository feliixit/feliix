<?php
error_reporting(0);
//header("Access-Control-Allow-Origin: https://feliix.myvnc.com");
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: *");
header("Access-Control-Max-Age: 3600");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

$id = (isset($_GET['id']) ?  $_GET['id'] : 0);
$jwt = (isset($_COOKIE['jwt']) ?  $_COOKIE['jwt'] : null);
$action = (isset($_POST['action']) ?  $_POST['action'] : 1);

include_once 'config/core.php';
include_once 'libs/php-jwt-master/src/BeforeValidException.php';
include_once 'libs/php-jwt-master/src/ExpiredException.php';
include_once 'libs/php-jwt-master/src/SignatureInvalidException.php';
include_once 'libs/php-jwt-master/src/JWT.php';

include_once 'config/database.php';
include_once 'config/conf.php';
require_once '../vendor/autoload.php';

$database = new Database();
$db = $database->getConnection();
$conf = new Conf();

use Google\Cloud\Storage\StorageClient;

use \Firebase\JWT\JWT;

$method = $_SERVER['REQUEST_METHOD'];
$user_id = 0;

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

    switch ($method) {
        case 'GET':

            $merged_results = array();

            $query = "SELECT id, project_group from project_group 
                            WHERE status <> -1 ";


            $query = $query . " order by project_group";

            $stmt = $db->prepare($query);
            $stmt->execute();

            while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                $id = $row['id'];
                $project_group = $row['project_group'];

                $detail = GetProject($row['id'], $db);

                $merged_results[] = array(
                    "id" => $id,
                    "project_group" => $project_group,
                    "detail" => $detail,
                );
            }
            echo json_encode($merged_results, JSON_UNESCAPED_SLASHES);

            break;

        case 'POST':

            $action = (isset($_POST['action']) ?  $_POST['action'] : 1);
            $id = (isset($_POST['id']) ?  $_POST['id'] : 0);
            $project_group = (isset($_POST['project_group']) ?  $_POST['project_group'] : '');

            if ($action == 1) {
                $query = "INSERT INTO project_group
                SET
                    project_group = :project_group,
        
                    create_id = :create_id,
                    created_at = now()";

                // prepare the query
                $stmt = $db->prepare($query);

                // bind the values
                $stmt->bindParam(':project_group', $project_group);

                $stmt->bindParam(':create_id', $user_id);

                $last_id = 0;
                // execute the query, also check if query was successful
                try {
                    // execute the query, also check if query was successful
                    if ($stmt->execute()) {
                        $last_id = $db->lastInsertId();
                    } else {
                        $arr = $stmt->errorInfo();
                        error_log($arr[2]);
                    }
                } catch (Exception $e) {
                    error_log($e->getMessage());
                }

                $returnArray = array("message" => " Insert success at " . date("Y-m-d") . " " . date("h:i:sa"));
                $jsonEncodedReturnArray = json_encode($returnArray, JSON_PRETTY_PRINT);

                echo $jsonEncodedReturnArray;
            } 
            else if ($action == 2) {
                $query = "update project_group
                SET
                    status = :status,
        
                    updated_id = :updated_id,
                    updated_at = now()
                    where id = :id";

                // prepare the query
                $stmt = $db->prepare($query);

                $status = -1;

                // bind the values
                $stmt->bindParam(':status', $status);
                $stmt->bindParam(':updated_id', $user_id);
                $stmt->bindParam(':id', $id);

                $last_id = 0;
                // execute the query, also check if query was successful
                try {
                    // execute the query, also check if query was successful
                    if ($stmt->execute()) {
                    } else {
                        $arr = $stmt->errorInfo();
                        error_log($arr[2]);
                    }
                } catch (Exception $e) {
                    error_log($e->getMessage());
                }

                $query = "update project_main
                SET
                    group_id = :status
                    where group_id = :id";

                // prepare the query
                $stmt = $db->prepare($query);

                $status = 0;

                // bind the values
                $stmt->bindParam(':status', $status);
                $stmt->bindParam(':id', $id);

                $last_id = 0;
                // execute the query, also check if query was successful
                try {
                    // execute the query, also check if query was successful
                    if ($stmt->execute()) {
                    } else {
                        $arr = $stmt->errorInfo();
                        error_log($arr[2]);
                    }
                } catch (Exception $e) {
                    error_log($e->getMessage());
                }

                $returnArray = array("message" => " Delete success at " . date("Y-m-d") . " " . date("h:i:sa"));
                $jsonEncodedReturnArray = json_encode($returnArray, JSON_PRETTY_PRINT);

                echo $jsonEncodedReturnArray;
            }
            else if ($action == 3) {
                $query = "update project_group
                SET
                    project_group = :project_group,
        
                    updated_id = :updated_id,
                    updated_at = now()
                    where id=:id";

                // prepare the query
                $stmt = $db->prepare($query);

                // bind the values
                $stmt->bindParam(':project_group', $project_group);
                $stmt->bindParam(':updated_id', $user_id);
                $stmt->bindParam(':id', $id);

                $last_id = 0;
                // execute the query, also check if query was successful
                try {
                    // execute the query, also check if query was successful
                    if ($stmt->execute()) {
                    } else {
                        $arr = $stmt->errorInfo();
                        error_log($arr[2]);
                    }
                } catch (Exception $e) {
                    error_log($e->getMessage());
                }

                $returnArray = array("message" => " Update success at " . date("Y-m-d") . " " . date("h:i:sa"));
                $jsonEncodedReturnArray = json_encode($returnArray, JSON_PRETTY_PRINT);

                echo $jsonEncodedReturnArray;
            }

            break;
    }
}

function GetProject($group_id, $db)
{
    $merged_results = array();

    $query = "SELECT pm.id, 
    COALESCE(pc.category, '') category, 
    pm.project_name, 
    COALESCE(ps.project_status, '') project_status, 
    user.username, 
    COALESCE(DATE_FORMAT(pm.created_at, '%Y-%m-%d'), '') created_at, 
    COALESCE(DATE_FORMAT(pm.updated_at, '%Y-%m-%d'), '') updated_at
    FROM project_main pm 
    LEFT JOIN project_category pc ON pm.catagory_id = pc.id 
    LEFT JOIN project_client_type pct ON pm.client_type_id = pct.id 
    LEFT JOIN project_priority pp ON pm.priority_id = pp.id 
    LEFT JOIN project_status ps ON pm.project_status_id = ps.id 
    LEFT JOIN project_stage pst ON pm.stage_id = pst.id 
    LEFT JOIN user ON pm.create_id = user.id where pm.status <> -1  and pm.group_id=" . $group_id;


    $query = $query . " order by pm.project_name ";

    $stmt = $db->prepare($query);
    $stmt->execute();

    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        $id = $row['id'];
        $category = $row['category'];
        $project_name = $row['project_name'];
        $project_status = $row['project_status'];
        $username = $row['username'];
        $created_at =  $row['created_at'];
        $updated_at =  $row['updated_at'];

        $merged_results[] = array(
            "id" => $id,
            "category" => $category,
            "project_name" => $project_name,
            "project_status" => $project_status,
            "username" => $username,
            "created_at" => $created_at,
            "updated_at" => $updated_at,

        );
    }

    return $merged_results;
}
