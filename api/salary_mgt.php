<?php
error_reporting(0);
//header("Access-Control-Allow-Origin: https://feliix.myvnc.com");
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: *");
header("Access-Control-Max-Age: 3600");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

$jwt = (isset($_GET['jwt']) ?  $_GET['jwt'] : '');
$kw = (isset($_GET['kw']) ?  $_GET['kw'] : '');
$id = (isset($_GET['id']) ?  $_GET['id'] : 0);
$dp = (isset($_GET['dp']) ?  $_GET['dp'] : '');
$kw = urldecode($kw);

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

if (!isset($jwt)) {
    http_response_code(401);

    echo json_encode(array("message" => "Access denied."));
    die();
} else {

    $merged_results = array();
    $return_result = array();

    $query = "SELECT u.id uid, u.username, ut.title, ud.department, sm.id sid, salary, sm.updated_at, uu.username updated_name, u.status
                FROM salary_mgt sm
                LEFT JOIN user u ON sm.uid = u.id
                LEFT JOIN user_title ut ON u.title_id = ut.id
                LEFT JOIN user_department ud ON u.apartment_id = ud.id
                LEFT JOIN user uu on uu.id = sm.updated_id
                WHERE 1 = 1 " . ($id != 0 ? " and u.id=$id" : ' ');

    if($dp != '') {
        $query = $query . " and u.apartment_id = $dp ";
    }

    $query = $query . " order by u.username  ";

    if (!empty($_GET['page'])) {
        $page = filter_input(INPUT_GET, 'page', FILTER_VALIDATE_INT);
        if (false === $page) {
            $page = 1;
        }
    }

    if (!empty($_GET['size'])) {
        $size = filter_input(INPUT_GET, 'size', FILTER_VALIDATE_INT);
        if (false === $size) {
            $size = 10;
        }

        $offset = ($page - 1) * $size;

        $query = $query . " LIMIT " . $offset . "," . $size;
    }

    $stmt = $db->prepare($query);
    $stmt->execute();

    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        $uid = $row['uid'];
        $username = $row['username'];
        $title = $row['title'];
        $department = $row['department'];
        $sid = $row['sid'];
        $salary = $row['salary'];
        $updated_at = $row['updated_at'];
        $updated_name = $row['updated_name'];

        $status = $row['status'];
       
        $merged_results[] = array(
            "is_checked" => 0,
            "uid" => $uid,
            "username" => $username,
            "title" => $title,
            "department" => $department,
            "sid" => $sid,
            "salary" => $salary,
            "updated_at" => $updated_at,
            "updated_name" => $updated_name,
            "status" => $status,
        );
    }

    if ($kw != "") {
        foreach ($merged_results as &$value) {
            if (
                preg_match("/{$kw}/i", $value['username']) ||
                preg_match("/{$kw}/i", $value['title']) ||
                preg_match("/{$kw}/i", $value['department']) ||
                preg_match("/{$kw}/i", $value['salary']) ||
                preg_match("/{$kw}/i", $value['updated_at']) ||
                preg_match("/{$kw}/i", $value['updated_name']) 
            ) {
                $return_result[] = $value;
            }
        }
    } else
        $return_result = $merged_results;

    echo json_encode($return_result, JSON_UNESCAPED_SLASHES);
}

?>