<?php
error_reporting(0);
//header("Access-Control-Allow-Origin: https://feliix.myvnc.com");
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: *");
header("Access-Control-Max-Age: 3600");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

$jwt = (isset($_COOKIE['jwt']) ?  $_COOKIE['jwt'] : '');
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

    // decode jwt
    $decoded = JWT::decode($jwt, $key, array('HS256'));
    $user_id = $decoded->data->id;

    $username = $decoded->data->username;
    $position = $decoded->data->position;
    $department = $decoded->data->department;

    $merged_results = array();
    $return_result = array();

    $query = "SELECT pr.id, 
                pr.period, 
                pr.review_month, 
                pr.template_id,
                ud.department,  
                ut.title, 
                pt.version,
                u.username manager,
                u1.username employee, 
                pr.create_id,
                pr.user_id,
                COALESCE(pr.user_complete_at, '') user_complete_at, 
                COALESCE(pr.manager_complete_at, '') manager_complete_at,
                COALESCE(pr.mag_comment_1, '') mag_comment_1,
                COALESCE(pr.mag_comment_2, '') mag_comment_2,
                COALESCE(pr.mag_comment_3, '') mag_comment_3,
                COALESCE(pr.mag_comment_4, '') mag_comment_4,
                COALESCE(pr.mag_comment_5, '') mag_comment_5,
                COALESCE(pr.mag_comment_6, '') mag_comment_6,
                COALESCE(pr.emp_comment_1, '') emp_comment_1,
                COALESCE(pr.emp_comment_2, '') emp_comment_2,
                COALESCE(pr.emp_comment_3, '') emp_comment_3,
                COALESCE(pr.emp_comment_4, '') emp_comment_4,
                COALESCE(pr.emp_comment_5, '') emp_comment_5
                FROM performance_review pr
                LEFT JOIN performance_template pt ON pr.template_id = pt.id
                LEFT JOIN user_title ut ON ut.id = pt.title_id
                LEFT JOIN user_department ud ON ud.id = ut.department_id
                LEFT JOIN user u ON u.id = pr.create_id
                LEFT JOIN user u1 ON u1.id = pr.user_id
              WHERE pr.status <> -1
              AND pr.user_complete_at is not null AND pr.manager_complete_at is not null
              AND pr.user_id = " . $user_id;


    $query = $query . " order by pr.created_at desc limit 1";


    $stmt = $db->prepare($query);
    $stmt->execute();

    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        $id = $row['id'];
        $review_month = $row['review_month'];
        $period = $row['period'];

        if($period == 3)
            $review_next_month = GetNextMonth3($review_month);
        else
            $review_next_month = GetNextMonth($review_month);

        $department = $row['department'];
        $title = $row['title'];
        $template_id = $row['template_id'];
        $employee = $row['employee'];
        $manager = $row['manager'];
        $version = $row['version'];
        $user_complete_at = $row['user_complete_at'];
        $manager_complete_at = $row['manager_complete_at'];

        $mag_comment_1 = $row['mag_comment_1'];
        $mag_comment_2 = $row['mag_comment_2'];
        $mag_comment_3 = $row['mag_comment_3'];
        $mag_comment_4 = $row['mag_comment_4'];
        $mag_comment_5 = $row['mag_comment_5'];
        $mag_comment_6 = $row['mag_comment_6'];

        $emp_comment_1 = $row['emp_comment_1'];
        $emp_comment_2 = $row['emp_comment_2'];
        $emp_comment_3 = $row['emp_comment_3'];
        $emp_comment_4 = $row['emp_comment_4'];
        $emp_comment_5 = $row['emp_comment_5'];

        $status = "Nobody cares";
        if($user_complete_at == "" && $manager_complete_at != "")
            $status = "Lack of subordinate's opinion";
        if($user_complete_at != "" && $manager_complete_at == "")
            $status = "Lack of supervisor's opinion";
        if($user_complete_at != "" && $manager_complete_at != "")
            $status = "Done";

        if($id != 0)
        {
            
            if($user_complete_at != "" && $manager_complete_at != "")
            {
                // supervisor can see
                $agenda = GetAgenda($row['template_id'],  1, $db, 1, 2, $id);
                $agenda1 = GetAgenda($row['template_id'],  2, $db, 1, 2, $id);
                $agenda2 = GetAgenda($row['template_id'],  3, $db, 1, 2, $id);
            }
            elseif($user_complete_at != "" && $manager_complete_at != "")
            {
                // mananger and employee all finished
                $agenda = GetAgenda($row['template_id'],  1, $db, 1, 2, $id);
                $agenda1 = GetAgenda($row['template_id'],  2, $db, 1, 2, $id);
                $agenda2 = GetAgenda($row['template_id'],  3, $db, 1, 2, $id);
            }
            elseif($user_complete_at == "" && $manager_complete_at != "")
            {
                // mananger finished and employee yet

                // manager
                if($row['create_id'] == $user_id)
                {
                    $agenda = GetAgenda($row['template_id'],  1, $db, 0, 2, $id);
                    $agenda1 = GetAgenda($row['template_id'],  2, $db, 0, 2, $id);
                    $agenda2 = GetAgenda($row['template_id'],  3, $db, 0, 2, $id);
                }

                // employee
                if($row['user_id'] == $user_id)
                {
                    $agenda = GetAgenda($row['template_id'],  1, $db, 1, 0, $id);
                    $agenda1 = GetAgenda($row['template_id'],  2, $db, 1, 0, $id);
                    $agenda2 = GetAgenda($row['template_id'],  3, $db, 1, 0, $id);

                    $mag_comment_1 = "";
                    $mag_comment_2 = "";
                    $mag_comment_3 = "";
                    $mag_comment_4 = "";
                    $mag_comment_5 = "";
                    $mag_comment_6 = "";
                }
            }
            elseif($user_complete_at != "" && $manager_complete_at == "")
            {
                // mananger yet and employee finished

                // manager
                if($row['create_id'] == $user_id)
                {
                    $agenda = GetAgenda($row['template_id'],  1, $db, 0, 2, $id);
                    $agenda1 = GetAgenda($row['template_id'],  2, $db, 0, 2, $id);
                    $agenda2 = GetAgenda($row['template_id'],  3, $db, 0, 2, $id);

                    $emp_comment_1 = "";
                    $emp_comment_2 = "";
                    $emp_comment_3 = "";
                    $emp_comment_4 = "";
                    $emp_comment_5 = "";
                }

                // employee
                if($row['user_id'] == $user_id)
                {
                    $agenda = GetAgenda($row['template_id'],  1, $db, 1, 0, $id);
                    $agenda1 = GetAgenda($row['template_id'],  2, $db, 1, 0, $id);
                    $agenda2 = GetAgenda($row['template_id'],  3, $db, 1, 0, $id);
                }
            }
             elseif($user_complete_at != "" && $manager_complete_at != "")
            {
                // mananger finished and employee finished

                $agenda = GetAgenda($row['template_id'],  1, $db, 1, 2, $id);
                $agenda1 = GetAgenda($row['template_id'],  2, $db, 1, 2, $id);
                $agenda2 = GetAgenda($row['template_id'],  3, $db, 1, 2, $id);
            }
            else
            {
                $agenda = GetAgenda($row['template_id'],  1, $db, 0, 0, $id);
                $agenda1 = GetAgenda($row['template_id'],  2, $db, 0, 0, $id);
                $agenda2 = GetAgenda($row['template_id'],  3, $db, 0, 0, $id);
            }
        }
        else
        {
            $agenda = [];
            $agenda1 = [];
            $agenda2 = [];
        }
    
        $merged_results[] = array(
            "id" => $id,
            "period" => $period,
            "review_month" => $review_month,
            "review_next_month" => $review_next_month,
            "department" => $department,
            "template_id" => $template_id,
            "title" => $title,
            "version" => $version,
            "employee" => $employee,
            "manager" => $manager,
            "user_complete_at" => $user_complete_at,
            "manager_complete_at" => $manager_complete_at,
            "status" => $status,
            "agenda" => $agenda,
            "agenda1" => $agenda1,
            "agenda2" => $agenda2,

            "mag_comment_1" => $mag_comment_1,
            "mag_comment_2" => $mag_comment_2,
            "mag_comment_3" => $mag_comment_3,
            "mag_comment_4" => $mag_comment_4,
            "mag_comment_5" => $mag_comment_5,
            "mag_comment_6" => $mag_comment_6,

            "emp_comment_1" => $emp_comment_1,
            "emp_comment_2" => $emp_comment_2,
            "emp_comment_3" => $emp_comment_3,
            "emp_comment_4" => $emp_comment_4,
            "emp_comment_5" => $emp_comment_5,
        );
    }

    if ($kw != "") {
        foreach ($merged_results as &$value) {
            if (
                preg_match("/{$kw}/i", $value['employee']) ||
                preg_match("/{$kw}/i", $value['title']) ||
                preg_match("/{$kw}/i", $value['department']) ||
                preg_match("/{$kw}/i", $value['status']) 
            ) {
                $return_result[] = $value;
            }
        }
    } else
        $return_result = $merged_results;


    echo json_encode($return_result, JSON_UNESCAPED_SLASHES);
}

function GetNextMonth($d)
{
    $date = date('Y-m', strtotime('+1 month', strtotime($d . '-01')));
    return $date;
}

function GetNextMonth3($d)
{
    $date = date('Y-m', strtotime('+2 month', strtotime($d . '-01')));
    return $date;
}

function GetAgenda($tid, $type, $db, $emp, $mag, $rid){
    $query = "
            SELECT pm.id,
            pm.`order`,
            pm.category,
            pm.criterion,
        
            COALESCE(pr.score, '') emp_score,
            COALESCE(pr.`option`, '') emp_opt,

            COALESCE(pd.score, '') mag_score,
            COALESCE(pd.`option`, '') mag_opt
            
        
        FROM   performance_template_detail pm
        LEFT JOIN (SELECT * from performance_review_detail WHERE review_type = " . $emp . " AND review_id = " . $rid . ") pr ON pm.id = pr.review_question_id
        LEFT JOIN (SELECT * from performance_review_detail WHERE review_type = " . $mag . " AND review_id = " . $rid . ") pd ON pm.id = pd.review_question_id
           
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

        $emp_score = $row['emp_score'];
        $emp_opt = $row['emp_opt'];
        $mag_score = $row['mag_score'];
        $mag_opt = $row['mag_opt'];

        $merged_results[] = array(
            "id" => $id,
            "order" => $order,
            "category" => $category,
            "criterion" => $criterion,

            "emp_score" => $emp_score,
            "emp_opt" => $emp_opt,
            "mag_score" => $mag_score,
            "mag_opt" => $mag_opt,
          
        );
    }

    return $merged_results;
}