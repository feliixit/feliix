<?php
error_reporting(E_ALL);
//header("Access-Control-Allow-Origin: https://feliix.myvnc.com");
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: POST");
header("Access-Control-Max-Age: 3600");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

$jwt = (isset($_POST['jwt']) ?  $_POST['jwt'] : null);
$id = (isset($_POST['id']) ?  $_POST['id'] : 0);

include_once 'config/core.php';
include_once 'libs/php-jwt-master/src/BeforeValidException.php';
include_once 'libs/php-jwt-master/src/ExpiredException.php';
include_once 'libs/php-jwt-master/src/SignatureInvalidException.php';
include_once 'libs/php-jwt-master/src/JWT.php';

include_once 'config/database.php';

include_once 'config/conf.php';
require_once '../vendor/autoload.php';
include_once 'mail.php';

$database = new Database();
$db = $database->getConnection();
$db->beginTransaction();
$conf = new Conf();

use \Firebase\JWT\JWT;
use Google\Cloud\Storage\StorageClient;

if ( !isset( $jwt ) ) {
    http_response_code(401);

    echo json_encode(array("message" => "Failure at " . date("Y-m-d") . " " . date("h:i:sa") . " Access denied."));
    die();
}
else
{
    try {
        // decode jwt
        $decoded = JWT::decode($jwt, $key, array('HS256'));

        $user_id = $decoded->data->id;
        $apartment_id = $decoded->data->apartment_id;

        $user_name = $decoded->data->username;
        $user_department = $decoded->data->department;

        $uid = $user_id;

        $pre_knowledge = knowledge_get($id, $db);
    
        $query = "UPDATE knowledge
        SET
            `status` = -1,
            `deleted_id` = :deleted_id,
            `deleted_time` = now()
            where id = :id";

        // prepare the query
        $stmt = $db->prepare($query);

        $stmt->bindParam(':deleted_id', $user_id);
        $stmt->bindParam(':id', $id);

        $last_id = $id;
        // execute the query, also check if query was successful
        try {
            // execute the query, also check if query was successful
            if ($stmt->execute()) {
                //$last_id = $db->lastInsertId();
            } else {
                $arr = $stmt->errorInfo();
                error_log($arr[2]);
                $db->rollback();
                http_response_code(501);
                echo json_encode("Failure at " . date("Y-m-d") . " " . date("h:i:sa") . " " . $arr[2]);
                die();
            }
        } catch (Exception $e) {
            error_log($e->getMessage());
            $db->rollback();
            http_response_code(501);
            echo json_encode(array("Failure at " . date("Y-m-d") . " " . date("h:i:sa") . " " . $e->getMessage()));
            die();
        }

            
        $db->commit();

        $users = knowledge_access_get($access, $db);
        $cc = array();
        
        knowledge_add_notification($user_name, date("Y/m/d") . " " . date("h:i:sa"), $users, $cc, $title, $pre_knowledge["created_by"], $pre_knowledge['created_at'], category_text($category), type_text($type), duration_text($duration), $last_id, "del");
        
        http_response_code(200);
        echo json_encode(array("message" => "Success at " . date("Y-m-d") . " " . date("h:i:sa") ));
        
    }
    catch (Exception $e){

        error_log($e->getMessage());
        $db->rollback();
        http_response_code(501);
        echo json_encode(array("Failure at " . date("Y-m-d") . " " . date("h:i:sa") . $e->getMessage()));
        die();

    }
}


function knowledge_access_get($access, $db)
{
    $users = array();

    $query = "select 
                username , email, department from `user` 
            left join `user_department` on `user`.apartment_id = `user_department`.id
            where `user`.status = 1";

    $username = "";
    $email = "";
    $department = "";

    $access_up = strtoupper($access); 

    $stmt_cnt = $db->prepare( $query );
    $stmt_cnt->execute();
    while($row = $stmt_cnt->fetch(PDO::FETCH_ASSOC)) {
        $username = $row['username'];
        $email = $row['email'];
        $department = $row['department'];

        // if username or department part of access then add to uses
        if(strpos($access_up, strtoupper($username)) !== false || strpos($access_up, strtoupper($department)) !== false || strpos($access_up, "ALL") !== false)
        {
            $users[] = $username;
        }
    }

    return implode(",", $users);
}

function duration_text($duration){
    $duration_str = '';
    if($duration > 0){
        $duration_in_huours = round($duration/60, 1);
        $duration_in_minutes = floor($duration % 60);
        
        if($duration_in_huours > 1){
            $duration_str = $duration_in_huours . '-hr ';
        }
        else
        {
            $duration_str = $duration_in_minutes . '-min';
        }

        //if($duration_in_minutes > 0){
        //    $duration_str .= $duration_in_minutes . '-min';
        //}
    }

    return $duration_str;
}

function type_text($type)
{
    if($type == 'file')
        return 'File';
    else if($type == 'link')
        return 'Web Text';
    else if($type == 'video')
        return 'Web Video';
    else
        return '';
}

function category_text($category)
{
    // split by comma and concatenate by space and comma
    $category_arr = explode(",", $category);
    $category_str = '';
    foreach($category_arr as $cat)
    {
        $category_str .= $cat . ', ';
    }

    return rtrim($category_str, ", ");

}


function knowledge_get($id, $db)
{
    $query = "SELECT pm.id,
                pm.cover, 
                pm.title, 
                pm.category, 
                pm.access, 
                pm.`type`, 
                pm.link, 
                pm.attach,
                pm.duration, 
                pm.watch,
                pm.desciption,
                pm.`status`,
                c_user.username AS created_by, 
                DATE_FORMAT(pm.created_at, '%Y/%m/%d %H:%i:%s') created_at
            FROM knowledge pm
                LEFT JOIN user c_user ON pm.create_id = c_user.id where pm.id = :id";

    // prepare the query
    $stmt = $db->prepare($query);

    // bind the values
    $stmt->bindParam(':id', $id);

    // execute the query
    $stmt->execute();

    // get number of rows
    $num = $stmt->rowCount();

    if($num > 0)
    {
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        return $row;
    }
    else
    {
        return null;
    }
}