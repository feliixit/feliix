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
    $sql = "SELECT COUNT(*) cnt,
                sum(Coalesce(pm.final_amount, 0)) final_amount,
                case 
                    when Coalesce(ps.project_status, '')  = 'Completed'  then 'c'
                    when Coalesce(ps.project_status, '')  = 'Disapproved'  then 'd'
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
                
                GROUP BY pro_status, username
                ";

            
            $sql = $sql . " 
                    ORDER BY username
                    ";

        

        $merged_results = array();

        $stmt = $db->prepare($sql);
        $stmt->execute();


    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        $merged_results[] = $row;
    }

    return $merged_results;
}

