<?php
 error_reporting(E_ERROR | E_PARSE);
//error_reporting(0);

header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: POST");
header("Access-Control-Max-Age: 3600");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

$jwt = (isset($_POST['jwt']) ?  $_POST['jwt'] : null);
include_once 'config/core.php';
include_once 'libs/php-jwt-master/src/BeforeValidException.php';
include_once 'libs/php-jwt-master/src/ExpiredException.php';
include_once 'libs/php-jwt-master/src/SignatureInvalidException.php';
include_once 'libs/php-jwt-master/src/JWT.php';

include_once 'config/database.php';
include_once 'objects/user.php';




use \Firebase\JWT\JWT;
if ( !isset( $jwt ) ) {
    http_response_code(401);

    echo json_encode(array("message" => "Access denied."));
    die();
}
else
{
    try {

        $uid = isset($_POST['uid']) ? $_POST['uid'] : 0;

        $database = new Database();
        $db = $database->getConnection();

        $data = userCanLogin($uid, $db);

        $username = $data['username'];
        $department = $data['department'];
        $is_manager = $data['is_manager'];
        $sick_leave = $data['sick_leave'];
        $annual_leave = $data['annual_leave'];  
        $manager_leave = $data['manager_leave'];
        $head_of_department = $data['head_of_department'];
        $is_viewer = $data['is_viewer'];
        $leave_level = $data['leave_level'];
        $user_id = $data['id'];
        $title = $data['title'];

        //echo json_encode(array("username" => $user, "department" => $department, "title" => $title));

        echo json_encode(array("username" => $username, "department" => $department, "title" => $title, "is_manager" => $is_manager, "sick_leave" => $sick_leave, "annual_leave" => $annual_leave, "manager_leave" => $manager_leave,  "head_of_department" => $head_of_department , "is_viewer" => $is_viewer, "user_id" => $user_id, "leave_level" => $leave_level));

    }
        // if decode fails, it means jwt is invalid
    catch (Exception $e){

        http_response_code(401);

        echo json_encode(array("message" => "Access denied."));
        die();
    }
}


function userCanLogin($uid, $db){
    // query to check if email exists
    $query = "SELECT user.id, username, password, user.status, is_admin, need_punch, COALESCE(department, '') department, 
            apartment_id, title_id, COALESCE(title, '') title, annual_leave, sick_leave, COALESCE(is_manager, 0) is_manager, COALESCE(test_manager, '0') test_manager, manager_leave, user_title.head_of_department,user.is_viewer, user.pic_url, user.leave_level
            FROM user
            LEFT JOIN user_department ON user.apartment_id = user_department.id 
            LEFT JOIN user_title ON user.title_id = user_title.id
            WHERE user.id = ? ";

    $data = array();

    // prepare the query
    $stmt = $db->prepare( $query );

    // bind given email value
    $stmt->bindParam(1, $uid);

    // execute the query
    $stmt->execute();

    // get number of rows
    $num = $stmt->rowCount();

    // if email exists, assign values to object properties for easy access and use for php sessions
    if($num>0){
        $data = $stmt->fetch(PDO::FETCH_ASSOC);
    }

    // return false if email does not exist in the database
    return $data;
}