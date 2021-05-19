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

    echo json_encode(array("message" => "Access denied1."));
    die();
} else {

    $merged_results = array();

    $title = (isset($_GET['title']) ? $_GET['title'] : 0);

    $query = "SELECT pt.id, pt.version, ud.id did, department, ut.id tid, ut.title, u.username created_name, COALESCE(pt.created_at, '') created_at, u1.username updated_name, COALESCE(pt.updated_at, '') updated_at
                    FROM performance_template pt
                    LEFT JOIN user_title ut ON ut.id = pt.title_id
                    LEFT JOIN user_department ud ON ud.id = ut.department_id
                    LEFT JOIN user u ON u.id = pt.create_id
                    LEFT JOIN user u1 ON u1.id = pt.updated_id
                    WHERE pt.status <> -1 " . ($title != 0 ? " and pt.title_id=$title" : ' and 1 <> 1');



    $query = $query . " order by pt.created_at desc ";

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
        $id = $row['id'];
        $version = $row['version'];
        $did = $row['did'];
        $tid = $row['tid'];
 
        $department = $row['department'];
        $title = $row['title'];
        $created_name = $row['created_name'];
        $created_at = $row['created_at'];
        $updated_name = $row['updated_name'];
        $updated_at = $row['updated_at'];
       
        $merged_results[] = array(
            "id" => $id,
            "did" => $did,
            "tid" => $tid,
            "version" => $version,

            "department" => $department,
            "title" => $title,
            "created_name" => $created_name,
            "created_at" => $created_at,
            "updated_name" => $updated_name,
            "updated_at" => $updated_at,
           
        );
    }



    echo json_encode($merged_results, JSON_UNESCAPED_SLASHES);
}
