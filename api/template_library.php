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

    $title = GetTitle($id, $db);
    $library = GetLibrary($id, $db);
    $template = GetTemplate($id, $db);

    $merged_results = array(
        "title" => $title,
        "template" => $template,
        "library" => $library,
    );

    echo json_encode($merged_results, JSON_UNESCAPED_SLASHES);
}

function GetTemplate($title_id, $db){
    $merged_results = array();
 
    $query = "SELECT pt.id, pt.version, ud.id did, department, ut.id tid, ut.title, u.username created_name, COALESCE(pt.created_at, '') created_at, u1.username updated_name, COALESCE(pt.updated_at, '') updated_at,
                    (SELECT COUNT(*) FROM performance_review WHERE template_id = pt.id and performance_review.status <> -1) cited
                    FROM performance_template pt
                    LEFT JOIN user_title ut ON ut.id = pt.title_id
                    LEFT JOIN user_department ud ON ud.id = ut.department_id
                    LEFT JOIN user u ON u.id = pt.create_id
                    LEFT JOIN user u1 ON u1.id = pt.updated_id
                    WHERE pt.status <> -1 " . ($title_id != 0 ? " and pt.title_id=$title_id" : ' ');


    $query = $query . " order by pt.created_at desc limit 1";

    $stmt = $db->prepare($query);
    $stmt->execute();

    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        $id = $row['id'];
        $version = $row['version'];
        $did = $row['did'];
        $tid = $row['tid'];
        $agenda = GetAgenda($row['id'], 1, $db);
        $agenda1 = GetAgenda($row['id'], 2, $db);
        $agenda2 = GetAgenda($row['id'], 3, $db);
        $department = $row['department'];
        $title = $row['title'];
        $created_name = $row['created_name'];
        $created_at = $row['created_at'];
        $updated_name = $row['updated_name'];
        $updated_at = $row['updated_at'];
        $cited = $row['cited'];
       
        $merged_results[] = array(
            "id" => $id,
            "did" => $did,
            "tid" => $tid,
            "version" => $version,
            "agenda" => $agenda,
            "agenda1" => $agenda1,
            "agenda2" => $agenda2,
            "department" => $department,
            "title" => $title,
            "created_name" => $created_name,
            "created_at" => $created_at,
            "updated_name" => $updated_name,
            "updated_at" => $updated_at,
            "cited" => $cited,
        );
    }

    return $merged_results;
}


function GetTitle($title_id, $db){
    $merged_results = array();
 
    $query = "SELECT * from user_title pt
                    WHERE pt.status <> -1 " . ($title_id != 0 ? " and pt.id=$title_id" : ' ');


    $query = $query . " order by pt.created_at desc limit 1";

    $stmt = $db->prepare($query);
    $stmt->execute();

    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        $id = $row['id'];
        $title = $row['title'];
       
        $merged_results = array(
            "id" => $id,
            "title" => $title,
      
        );
    }

    return $merged_results;
}

function GetLibrary($title_id, $db){
    $merged_results = array();
 
    $query = "SELECT * from template_library pt
                    WHERE pt.status <> -1 " . ($title_id != 0 ? " and pt.title_id=$title_id" : ' ');


    $query = $query . " order by pt.created_at desc limit 1";

    $stmt = $db->prepare($query);
    $stmt->execute();

    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        $id = $row['id'];
        $title_id = $row['title_id'];
        $salary = $row['salary'];
        $kpi = explode("\n", $row['kpi']);
 
        $merged_results = array(
            "id" => $id,
            "title_id" => $title_id,
            "salary" => $salary,
            "kpi" => $kpi,
          
        );
    }

    return $merged_results;
}

function GetAgenda($tid, $type, $db){
    $query = "
        SELECT pm.id,
            pm.`order`,
            pm.category,
            pm.criterion
          
        FROM   performance_template_detail pm
           
        WHERE  template_id = " . $tid . "
            AND pm.`type` = " . $type . "
            AND pm.`status` <> -1 
        ORDER BY `order`
    ";

    // prepare the query
    $stmt = $db->prepare($query);
    $stmt->execute();

    $merged_results = [];

    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        $id = $row['id'];
        $order = $row['order'];
        $category = $row['category'];
        $criterion = $row['criterion'];

        $merged_results[] = array(
            "id" => $id,
            "order" => $order,
            "category" => $category,
            "criterion" => $criterion,
          
        );
    }

    return $merged_results;
}