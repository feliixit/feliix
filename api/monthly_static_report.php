<?php

error_reporting(E_ALL);
ini_set('display_errors', 1);
$jwt = (isset($_COOKIE['jwt']) ?  $_COOKIE['jwt'] : null);
include_once 'config/core.php';
include_once 'libs/php-jwt-master/src/BeforeValidException.php';
include_once 'libs/php-jwt-master/src/ExpiredException.php';
include_once 'libs/php-jwt-master/src/SignatureInvalidException.php';
include_once 'libs/php-jwt-master/src/JWT.php';

use \Firebase\JWT\JWT;

$method = $_SERVER['REQUEST_METHOD'];

date_default_timezone_set('Asia/Taipei');

if (!isset($jwt)) {
    http_response_code(401);

    echo json_encode(array("message" => "Access denied."));
    die();
} else {
    try {
        // decode jwt
        $access7 = false;
        $decoded = JWT::decode($jwt, $key, array('HS256'));
        $user_id = $decoded->data->id;
        $position = $decoded->data->position;
        $department = $decoded->data->department;
        $username = $decoded->data->username;

        if(trim(strtoupper($department)) == '')
        {
            if(trim(strtoupper($position)) == 'OWNER' || trim(strtoupper($position)) == 'MANAGING DIRECTOR' || trim(strtoupper($position)) == 'CHIEF ADVISOR')
            {
                $access7 = true;
            }
        }

        if($username == "Glendon Wendell Co")
        {
            $access7 = true;
        }

        if(!$access7)
        {
         http_response_code(401);

         echo json_encode(array("message" => "Access denied."));
         die();
        }
    }
    // if decode fails, it means jwt is invalid
    catch (Exception $e) {

        http_response_code(401);

        echo json_encode(array("message" => "Access denied."));
        die();
    }
}

header('Access-Control-Allow-Origin: *');

include_once 'config/database.php';


$database = new Database();
$db = $database->getConnection();

$total_amount = 0;
$total_ar = 0;
$total_d = 0;
$total_p = 0;
$total_net_amount = 0;
$total_tax_withheld = 0;

switch ($method) {
    case 'GET':
        $strDate = (isset($_GET['d']) ?  $_GET['d'] : "");
        $strEDate = (isset($_GET['e']) ?  $_GET['e'] : "");
    
        if($strDate == '')
        {
            $strDate = date('Y-m-d');

        
                $strDate = date("Y-m-d",strtotime($strDate . "first day of 0 month"));
                $merged_results =  GetOneMonth($strDate,  $db);

            
        }

        if($strDate != '' && $strEDate != "")
        {

                $merged_results =  GetManyMonth($strDate, $strEDate, $db);

            
            
        }

        echo json_encode($merged_results, JSON_UNESCAPED_SLASHES);

        break;

}


function GetManyMonth($strDate, $endDate, $db)
{
    $PeriodStart = date("Y-m-d",strtotime($strDate . "last day of -1 month"));
    $PeriodEnd = date("Y-m-d",strtotime($endDate . "first day of 1 month"));

    $report1 = GetMonthSaleReport($PeriodStart, $PeriodEnd,  $db);
  
    return $report1;
}


function GetOneMonth($strDate, $db)
{
    $PeriodStart = date("Y-m-d",strtotime($strDate . "last day of -1 month"));
    $PeriodEnd = date("Y-m-d",strtotime($strDate . "first day of 1 month"));

    $report1 = GetMonthSaleReport($PeriodStart, $PeriodEnd, $db);
   
    return $report1;
}


function GetMonthSaleReport($PeriodStart, $PeriodEnd, $db){

    // 失敗專案的定義：專案當前的Status為Disapproved，且專案Status變更為Disapproved的時間點是在2021/01/01 ~ 2021/03/31之內，則該專案被視為失敗
    $sql = "SELECT pm.id, 1 cnt, pm.project_name,
                Coalesce(pm.final_amount, 0) final_amount,
                case 
                    when Coalesce(ps.project_status, '')  = 'Disapproved' and pm.updated_at > '" . $PeriodStart . "' AND pm.created_at < '" . $PeriodEnd . "' then 'd'
                    ELSE 'o'
                end
                    `pro_status`,
                user.username
                FROM   project_main pm
                LEFT JOIN project_category pc
                    ON pm.catagory_id = pc.id
                LEFT JOIN project_client_type pct
                    ON pm.client_type_id = pct.id
                LEFT JOIN project_priority pp
                    ON pm.priority_id = pp.id
                LEFT JOIN project_status ps
                    ON pm.project_status_id = ps.id
                LEFT JOIN project_stage pst
                    ON pm.stage_id = pst.id
                LEFT JOIN user
                    ON pm.create_id = user.id
                WHERE  pm.created_at > '" . $PeriodStart . "' AND pm.created_at < '" . $PeriodEnd . "' 
                
                ";

            
            $sql = $sql . " 
                    ORDER BY username
                    ";

        

        $merged_results = array();

        $stmt = $db->prepare($sql);
        $stmt->execute();


    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        $id = $row['id'];
        $cnt = $row['cnt'];
        $pro_status = $row['pro_status'];
        $username = $row['username'];
        $final_amount = $row['final_amount'];
        $project_name = $row['project_name'];

        $received_date = GetReceiveDate($row['id'], $db);

        if($received_date == "")
            $received_date = '9999-99-99';

        // 成功專案的定義：專案收到的第一筆金額時間在2021/01/01 ~ 2021/03/31之內，則該專案被視為成功
        if($received_date > $PeriodStart && $received_date < $PeriodEnd)
            $pro_status = 'c';

        if($received_date <= $PeriodStart)  
            continue;

        $merged_results[] = array(
            "id" => $id,
            "cnt" => $cnt,
            "username" => $username,
            "project_name" => $project_name,
            "pro_status" => $pro_status,
            "final_amount" => $final_amount,
        );
    }

    return $merged_results;
}

function GetReceiveDate($project_id, $db){
    $query = "
        SELECT pm.id,
            pm.remark,
            u.username,
            pm.created_at,
            pm.received_date,
            pm.kind,
            pm.amount,
            pm.invoice,
            pm.detail,
            pm.status checked,
            pm.checked_id,
            pm.checked_at
        FROM   project_proof pm
            LEFT JOIN user u
                ON u.id = pm.create_id
        WHERE  project_id = " . $project_id . "
            AND pm.status = 1
            and pm.received_date <> ''
        order by received_date limit 1
    ";

    // prepare the query
    $stmt = $db->prepare($query);
    $stmt->execute();

    $received_date = "";

    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {

        $received_date = $row['received_date'];
  
    }

    return $received_date;
}
