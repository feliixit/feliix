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
$edit_project_name = (isset($_POST['edit_project_name']) ?  $_POST['edit_project_name'] : '');
$edit_category = (isset($_POST['edit_category']) ?  $_POST['edit_category'] : 0);
$edit_client_type = (isset($_POST['edit_client_type']) ?  $_POST['edit_client_type'] : 0);
$edit_priority = (isset($_POST['edit_priority']) ?  $_POST['edit_priority'] : 0);
$edit_contactor = (isset($_POST['edit_contactor']) ?  $_POST['edit_contactor'] : '');
$edit_location = (isset($_POST['edit_location']) ?  $_POST['edit_location'] : '');
$creator = (isset($_POST['creator']) ?  $_POST['creator'] : 0);
$edit_contact_number = (isset($_POST['edit_contact_number']) ?  $_POST['edit_contact_number'] : '');
$edit_client = (isset($_POST['edit_client']) ?  $_POST['edit_client'] : '');
$edit_edit_reason = (isset($_POST['edit_edit_reason']) ?  $_POST['edit_edit_reason'] : '');

// extended
$edit_designer = (isset($_POST['edit_designer']) ?  $_POST['edit_designer'] : '');
$edit_type = (isset($_POST['edit_type']) ?  $_POST['edit_type'] : '');
$edit_scope = (isset($_POST['edit_scope']) ?  $_POST['edit_scope'] : '');
$edit_scope_other = (isset($_POST['edit_scope_other']) ?  $_POST['edit_scope_other'] : '');
$edit_office_location = (isset($_POST['edit_office_location']) ?  $_POST['edit_office_location'] : '');
$edit_background_client = (isset($_POST['edit_background_client']) ?  $_POST['edit_background_client'] : '');
$edit_background_project = (isset($_POST['edit_background_project']) ?  $_POST['edit_background_project'] : '');
$edit_contractor = (isset($_POST['edit_contractor']) ?  $_POST['edit_contractor'] : '');

$edit_key_person = (isset($_POST['edit_key_person']) ?  $_POST['edit_key_person'] : '');
$de_key_person = json_decode($edit_key_person);

$edit_party_contactor = (isset($_POST['edit_party_contactor']) ?  $_POST['edit_party_contactor'] : '');
$de_party_contactor = json_decode($edit_party_contactor);

$query = "update project_key_person set status = -1 where project_id = :project_id";
$stmt = $db->prepare($query);
$stmt->bindParam(':project_id', $pid);
$stmt->execute();

foreach ($de_key_person as &$value) {
    $query = "insert into project_key_person set project_id = :project_id, `type` = :type, `name` = :name, `number` = :number, create_id = :create_id, created_at = now() ";
    $stmt = $db->prepare($query);
    $stmt->bindParam(':project_id', $pid);
    $stmt->bindParam(':type', $value->type);
    $stmt->bindParam(':name', $value->name);
    $stmt->bindParam(':number', $value->number);
    $stmt->bindParam(':create_id', $user_id);

    $stmt->execute();
}

$query = "update project_party_contactor set status = -1 where project_id = :project_id";
$stmt = $db->prepare($query);
$stmt->bindParam(':project_id', $pid);
$stmt->execute();

foreach ($de_party_contactor as &$value) {
    $query = "insert into project_party_contactor set project_id = :project_id, `type` = :type, `name` = :name, `number` = :number, create_id = :create_id, created_at = now() ";
    $stmt = $db->prepare($query);
    $stmt->bindParam(':project_id', $pid);
    $stmt->bindParam(':type', $value->type);
    $stmt->bindParam(':name', $value->name);
    $stmt->bindParam(':number', $value->number);
    $stmt->bindParam(':create_id', $user_id);

    $stmt->execute();
}

$query = "INSERT INTO project_edit_info
                SET
                    project_id = :project_id,
                    reason = :reason,
                   
                    create_id = :create_id,
                    created_at = now()";
    
        // prepare the query
        $stmt = $db->prepare($query);
    
        // bind the values
        $stmt->bindParam(':project_id', $pid);
        $stmt->bindParam(':reason', $edit_edit_reason);
        $stmt->bindParam(':create_id', $user_id);

        $last_id = 0;
        // execute the query, also check if query was successful
        try {
            // execute the query, also check if query was successful
            if ($stmt->execute()) {
                $last_id = $db->lastInsertId();

                $query = "update project_main
                SET
                    project_name = :edit_project_name,
                    catagory_id = :edit_category,
                    client_type_id = :edit_client_type,
                    priority_id = :edit_priority,
                    contactor = :edit_contactor,
                    location = :edit_location,
                    create_id = :create_id,
                    contact_number = :edit_contact_number,
                    client = :edit_client,
                    edit_reason = :edit_edit_reason,

                    designer = :edit_designer,
                    type = :edit_type,
                    scope = :edit_scope,
                    scope_other = :edit_scope_other,
                    office_location = :edit_office_location,
                    background_client = :edit_background_client,
                    background_project = :edit_background_project,
                    contractor = :edit_contractor
                
                where id = :project_id ";
    
                // prepare the query
                $stmt1 = $db->prepare($query);

                $stmt1->bindParam(':project_id', $pid);
                $stmt1->bindParam(':edit_project_name', $edit_project_name);
                $stmt1->bindParam(':edit_category', $edit_category);
                $stmt1->bindParam(':edit_client_type', $edit_client_type);
                $stmt1->bindParam(':edit_priority', $edit_priority);
                $stmt1->bindParam(':edit_contactor', $edit_contactor);
                $stmt1->bindParam(':create_id', $creator);
                $stmt1->bindParam(':edit_location', $edit_location);
                $stmt1->bindParam(':edit_contact_number', $edit_contact_number);
                $stmt1->bindParam(':edit_client', $edit_client);
                $stmt1->bindParam(':edit_edit_reason', $edit_edit_reason);

                $stmt1->bindParam(':edit_designer', $edit_designer);
                $stmt1->bindParam(':edit_type', $edit_type);
                $stmt1->bindParam(':edit_scope', $edit_scope);
                $stmt1->bindParam(':edit_scope_other', $edit_scope_other);
                $stmt1->bindParam(':edit_office_location', $edit_office_location);
                $stmt1->bindParam(':edit_background_client', $edit_background_client);
                $stmt1->bindParam(':edit_background_project', $edit_background_project);
                $stmt1->bindParam(':edit_contractor', $edit_contractor);


                $stmt1->execute();

                $arr = $stmt1->errorInfo();
                error_log($arr[2]);

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

