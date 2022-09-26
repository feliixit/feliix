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
        $user_name = $decoded->data->username;

    }
        // if decode fails, it means jwt is invalid
    catch (Exception $e){

        http_response_code(401);

        echo json_encode(array("message" => "Access denied."));
        die();
    }
}

include_once 'mail.php';

include_once 'config/database.php';
$database = new Database();
$db = $database->getConnection();

$uid = $user_id;
$stage_id = (isset($_POST['stage_id']) ?  $_POST['stage_id'] : 0);
$project_stage_id = (isset($_POST['project_stage_id']) ?  $_POST['project_stage_id'] : 0);
$stages_status_id = (isset($_POST['stages_status_id']) ?  $_POST['stages_status_id'] : 0);
$sequence = (isset($_POST['sequence']) ?  $_POST['sequence'] : '');
$stage_edit_reason = (isset($_POST['stage_edit_reason']) ?  $_POST['stage_edit_reason'] : '');
$stage_edit_title = (isset($_POST['stage_edit_title']) ?  $_POST['stage_edit_title'] : '');


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
                    stage_title = :title,
                    updated_id = :create_id,
                    updated_at = now()
                
                where id = :id ";
    
                // prepare the query
                $stmt1 = $db->prepare($query);

                $stmt1->bindParam(':sequence', $sequence);
                $stmt1->bindParam(':stage_id', $project_stage_id);
                $stmt1->bindParam(':stages_status_id', $stages_status_id);
                $stmt1->bindParam(':title', $stage_edit_title);
                $stmt1->bindParam(':create_id', $uid);
                $stmt1->bindParam(':id', $stage_id);

                if ($stmt1->execute()) {
                    $returnArray = array('ret' => $stage_id);
                    $jsonEncodedReturnArray = json_encode($returnArray, JSON_PRETTY_PRINT);
                }
                else
                {
                    $arr = $stmt1->errorInfo();
                    error_log($arr[2]);
                }

                // 在project02.php中，當右側的Delivery Stage 或是 Installation Stage，它們的Status狀態透過左上邊的Edit/Delete Stage按鈕(如圖一)，變成 Close時，需要寄發通知信。
                if(($project_stage_id == 6 || $project_stage_id == 7) && $stages_status_id == 3)
                {
                    $title = "";

                    $result = GetMailInfo($stage_id, $db);

                    $stage_name = "";

                    if($project_stage_id == 6)
                    {
                        $stage_name = "Delivery";
                        $title = 'Stage "Delivery" was closed in Project ' . $result[0]["project_name"];
                    }
                    if($project_stage_id == 7)
                    {
                        $stage_name = "Installation";
                        $title = 'Stage "Installation" was closed in Project ' . $result[0]["project_name"];
                    }

                    
                    if($result[0]["project_category"] == "Lighting")
                        stage_close_notify( $result[0]["project_creator"],  // $project_creator_id, 
                                            $result[0]["project_id"],       // $project_id, 
                                            $result[0]["project_name"],     // $project_name, 
                                            $stage_name,                    // $stage_name, 
                                            $user_name,                     // $modify_name, 
                                            $result[0]["stage_creator"],         // $stage_creator_name, 
                                            $result[0]["stage_created_at"],       // $stage_create_at, 
                                            $title,                         // $title
                                            $result[0]["light_id"]);        // $cc_to                       
                    if($result[0]["project_category"] == "Office Systems")
                        stage_close_notify($result[0]["project_creator"],  // $project_creator_id, 
                                            $result[0]["project_id"],       // $project_id, 
                                            $result[0]["project_name"],     // $project_name, 
                                            $stage_name,                    // $stage_name, 
                                            $user_name,                     // $modify_name, 
                                            $result[0]["stage_creator"],         // $stage_creator_name, 
                                            $result[0]["stage_created_at"],       // $stage_create_at, 
                                            $title,                         // $title
                                            $result[0]["office_id"]);
                }

                if($project_stage_id == 4 && $stages_status_id == 3)
                {
                    $title = "";

                    $result = GetMailInfo($stage_id, $db);

                    $stage_name = "";

                    
                    $stage_name = "Order";
                    $title = 'Stage "Order" was closed in Project ' . $result[0]["project_name"];
                    
                
                    stage_order_close_notify( $result[0]["project_creator"],  // $project_creator_id, 
                                        $result[0]["project_id"],       // $project_id, 
                                        $result[0]["project_name"],     // $project_name, 
                                        $stage_name,                    // $stage_name, 
                                        $user_name,                     // $modify_name, 
                                        $result[0]["stage_creator"],         // $stage_creator_name, 
                                        $result[0]["stage_created_at"],       // $stage_create_at, 
                                        $title,                         // $title
                                        $result[0]["light_id"]);        // $cc_to                       
                
                    
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


        function GetMailInfo($sid, $db){
            $query = "
                SELECT 
                    pm.id, 
                    pm.project_name, 
                    Coalesce(pc.category, '') category, 
                    u.username, 
                    ps.created_at,
                    pm.create_id,
                    (SELECT GROUP_CONCAT(id) FROM user WHERE title_id IN(9, 10, 35, 28) ) light_id,
                    (SELECT GROUP_CONCAT(id) FROM user WHERE title_id IN(14, 15, 35, 28) ) office_id
                FROM project_stages ps
                LEFT JOIN project_main pm 
                    ON ps.project_id = pm.id
                LEFT JOIN project_category pc
                ON pm.catagory_id = pc.id
                LEFT JOIN user u 
                    ON ps.create_id = u.id
     
                WHERE ps.id = " . $sid;
            
        
            // prepare the query
            $stmt = $db->prepare($query);
            $stmt->execute();
        
            $merged_results = [];
        
            while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                $project_name = $row['project_name'];
                $category = $row['category'];
                $pid = $row['id'];
                $username = $row['username'];
                $created_at = $row['created_at'];
                $create_id = $row['create_id'];
                $light_id = $row['light_id'];
                $office_id = $row['office_id'];
             
                $merged_results[] = array(
                    "project_id" => $pid,
                    "project_name" => $project_name,
                    "project_category" => $category,
                    "stage_creator" => $username,
                    "stage_created_at" => $created_at,
                    "project_creator" => $create_id,
                    "light_id" => $light_id,
                    "office_id" => $office_id,
                );
            }
        
            return $merged_results;
        }