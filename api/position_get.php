<?php
error_reporting(0);
//header("Access-Control-Allow-Origin: https://feliix.myvnc.com");
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

include_once 'config/database.php';
include_once 'config/conf.php';
require_once '../vendor/autoload.php';

$database = new Database();
$db = $database->getConnection();
$conf = new Conf();


use \Firebase\JWT\JWT;

if (!isset($jwt)) {
    http_response_code(401);

    echo json_encode(array("message" => "Access denied."));
    die();
} else {

    $merged_results = array();
    $return_result = array();

    $did  = (isset($_GET['did']) ?  $_GET['did'] : "");

    $query = "SELECT ut.id tid, ut.title, ud.id did, ud.department
    FROM user_title  ut
    LEFT JOIN user_department ud ON ut.department_id = ud.id
    where ut.status <> -1 ";

    if($did != "")
        $query = $query . " and department_id=$did ";

    $query = $query . " order by ud.id, ut.id ";

    $stmt = $db->prepare($query);
    $stmt->execute();

    $did = 0;
    $items = [];

    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {

        if ($did != $row['did'] && $did != 0) {
            $merged_results[] = array(
                "did" => $did,
                "department" => $department,
                "items" => $items,
            );

            $items = [];
        }

        $tid = $row['tid'];
        $title = $row['title'];
        $did = $row['did'];
        $department = $row['department'];

        $items[] = array(
            'tid' => $tid,
            'title' => $title
        );

    }

    if ($did != 0) {
        $merged_results[] = array(
            "did" => $did,
            "department" => $department,
            "items" => $items,

        );
    }

    $return_result = $merged_results;

    echo json_encode($return_result, JSON_UNESCAPED_SLASHES);
}
