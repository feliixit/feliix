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

$uid = (isset($_GET['uid']) ?  $_GET['uid'] : '');
$id = (isset($_GET['id']) ?  $_GET['id'] : '');
$fc = (isset($_GET['fc']) ?  $_GET['fc'] : '');
$fs = (isset($_GET['fs']) ?  $_GET['fs'] : '');
$ft = (isset($_GET['ft']) ?  $_GET['ft'] : '');
$fp = (isset($_GET['fp']) ?  $_GET['fp'] : '');
$ft = urldecode($ft);

$fal = (isset($_GET['fal']) ?  $_GET['fal'] : '');
$fau = (isset($_GET['fau']) ?  $_GET['fau'] : '');
$fpl = (isset($_GET['fpl']) ?  $_GET['fpl'] : '');
$fpu = (isset($_GET['fpu']) ?  $_GET['fpu'] : '');
$frl = (isset($_GET['frl']) ?  $_GET['frl'] : '');
$fru = (isset($_GET['fru']) ?  $_GET['fru'] : '');

$fal_eq = (isset($_GET['fal_eq']) ?  $_GET['fal_eq'] : '');
$fau_eq = (isset($_GET['fau_eq']) ?  $_GET['fau_eq'] : '');
$fpl_eq = (isset($_GET['fpl_eq']) ?  $_GET['fpl_eq'] : '');
$fpu_eq = (isset($_GET['fpu_eq']) ?  $_GET['fpu_eq'] : '');
$frl_eq = (isset($_GET['frl_eq']) ?  $_GET['frl_eq'] : '');
$fru_eq = (isset($_GET['fru_eq']) ?  $_GET['fru_eq'] : '');

$aging = (isset($_GET['aging']) ?  $_GET['aging'] : '');

$fk = (isset($_GET['fk']) ?  $_GET['fk'] : '');
$fk = urldecode($fk);

$fkp = (isset($_GET['fkp']) ?  $_GET['fkp'] : '');
$fkp = urldecode($fkp);

$of1 = (isset($_GET['of1']) ?  $_GET['of1'] : '');
$ofd1 = (isset($_GET['ofd1']) ?  $_GET['ofd1'] : '');
$of2 = (isset($_GET['of2']) ?  $_GET['of2'] : '');
$ofd2 = (isset($_GET['ofd2']) ?  $_GET['ofd2'] : '');

$page = (isset($_GET['page']) ?  $_GET['page'] : 1);
$size = (isset($_GET['size']) ?  $_GET['size'] : 5);

$merged_results = array();
$return_result = array();

$query_cnt = "SELECT count(*) cnt
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
                WHERE  1 = 1 ";

$query = "SELECT pm.id,
            Coalesce(pc.category, '')                    category,
            pct.client_type,
            pct.class_name                               pct_class,
            pp.priority,
            pp.class_name                                pp_class,
            pm.project_name,
            pm.final_amount,
            pm.tax_withheld,
            pm.billing_name,
            (SELECT sum(pp.amount) FROM   project_proof pp  WHERE  pp.project_id = pm.id  AND pp.status = 1  AND pp.kind = 1) payment,
            (SELECT sum(pp.amount) FROM   project_proof pp  WHERE  pp.project_id = pm.id  AND pp.status = 1  AND pp.kind = 0) dp_payment,
            (SELECT sum(cash_out - cash_in) from price_record pr where pr.project_name = pm.project_name and is_enabled = 1 and pr.project_name <> '' ) expense,
            Coalesce(ps.project_status, '')              project_status,
            Coalesce((SELECT project_est_prob.prob
                    FROM   project_est_prob
                    WHERE  project_est_prob.project_id = pm.id
                    ORDER  BY created_at DESC
                    LIMIT  1), pm.estimate_close_prob) estimate_close_prob,
            user.username,
            pm.special,
            Date_format(pm.created_at, '%Y-%m-%d')       created_at,
            Date_format(pm.updated_at, '%Y-%m-%d')       updated_at,
            Coalesce((SELECT project_stage.stage
                    FROM   project_stages
                            LEFT JOIN project_stage
                                    ON project_stage.id = project_stages.stage_id
                    WHERE  project_stages.project_id = pm.id
                            AND project_stages.stages_status_id = 1
                    ORDER  BY `sequence` DESC
                    LIMIT  1), '')                     stage
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
            WHERE  1 = 1 ";

if($fc != "" && $fc != "0")
{
    $query = $query . " and pm.catagory_id = " . $fc . " ";
    $query_cnt = $query_cnt . " and pm.catagory_id = " . $fc . " ";
}

if($fs != "" && $fs != "0" && $fs != "v" && $fs != "w")
{
    $query = $query . " and pm.project_status_id = '" . $fs . "' ";
    $query_cnt = $query_cnt . " and pm.project_status_id = '" . $fs . "' ";
}

if($fs == "v")
{
    $query = $query . " and (SELECT count(*) FROM   project_proof pp  WHERE  pp.project_id = pm.id  AND pp.status > 0  AND pp.`kind` <> 2) > 0 ";
    $query_cnt = $query_cnt . " and (SELECT count(*) FROM   project_proof pp  WHERE  pp.project_id = pm.id  AND pp.status  > 0  AND pp.`kind` <> 2) > 0 ";
}

if($fs == "w")
{
    $query = $query . " and (SELECT count(*) FROM   project_proof pp  WHERE  pp.project_id = pm.id  AND pp.status  > 0  AND pp.`kind` <> 2) = 0 ";
    $query_cnt = $query_cnt . " and (SELECT count(*) FROM   project_proof pp  WHERE  pp.project_id = pm.id  AND pp.status  > 0  AND pp.`kind` <> 2) = 0 ";
}

if($fp == "A")
{
    $query = $query . " and (SELECT count(*) FROM   project_proof pp  WHERE  pp.project_id = pm.id  AND pp.status > 0  AND pp.`kind` = 0) > 0 and (SELECT count(*) FROM   project_proof pp  WHERE  pp.project_id = pm.id  AND pp.status > 0  AND pp.`kind` = 1) > 0 ";
    $query_cnt = $query_cnt . " and (SELECT count(*) FROM   project_proof pp  WHERE  pp.project_id = pm.id  AND pp.status  > 0  AND pp.`kind` = 0) > 0 and (SELECT count(*) FROM   project_proof pp  WHERE  pp.project_id = pm.id  AND pp.status  > 0  AND pp.`kind` = 1) > 0 ";
}

if($fp == "D")
{
    $query = $query . " and (SELECT count(*) FROM   project_proof pp  WHERE  pp.project_id = pm.id  AND pp.status > 0  AND pp.`kind` = 0) > 0 and (SELECT count(*) FROM   project_proof pp  WHERE  pp.project_id = pm.id  AND pp.status > 0  AND pp.`kind` = 1) = 0 ";
    $query_cnt = $query_cnt . " and (SELECT count(*) FROM   project_proof pp  WHERE  pp.project_id = pm.id  AND pp.status  > 0  AND pp.`kind` = 0) > 0 and (SELECT count(*) FROM   project_proof pp  WHERE  pp.project_id = pm.id  AND pp.status  > 0  AND pp.`kind` = 1) = 0 ";
}

if($fp == "F")
{
    $query = $query . " and (SELECT count(*) FROM   project_proof pp  WHERE  pp.project_id = pm.id  AND pp.status > 0  AND pp.`kind` = 0) = 0 and (SELECT count(*) FROM   project_proof pp  WHERE  pp.project_id = pm.id  AND pp.status > 0  AND pp.`kind` = 1) > 0 ";
    $query_cnt = $query_cnt . " and (SELECT count(*) FROM   project_proof pp  WHERE  pp.project_id = pm.id  AND pp.status  > 0  AND pp.`kind` = 0) = 0 and (SELECT count(*) FROM   project_proof pp  WHERE  pp.project_id = pm.id  AND pp.status  > 0  AND pp.`kind` = 1) > 0 ";
}

if($fp == "N")
{
    $query = $query . " and (SELECT count(*) FROM   project_proof pp  WHERE  pp.project_id = pm.id  AND pp.status > 0  AND pp.`kind` = 0) = 0 and (SELECT count(*) FROM   project_proof pp  WHERE  pp.project_id = pm.id  AND pp.status > 0  AND pp.`kind` = 1) = 0 ";
    $query_cnt = $query_cnt . " and (SELECT count(*) FROM   project_proof pp  WHERE  pp.project_id = pm.id  AND pp.status  > 0  AND pp.`kind` = 0) = 0 and (SELECT count(*) FROM   project_proof pp  WHERE  pp.project_id = pm.id  AND pp.status  > 0  AND pp.`kind` = 1) = 0 ";
}

if($ft != "" && $ft != "0")
{
    $query = $query . " and user.username = '" . $ft . "' ";
    $query_cnt = $query_cnt . " and user.username = '" . $ft . "' ";
}

if($id != "" && $id != "0")
{
    $query = $query . " and pm.id = " . $id . " ";
    $query_cnt = $query_cnt . " and pm.id = " . $id . " ";
}

if($fal != "" && $fal_eq == "s")
{
    $query = $query . " and pm.final_amount > " . $fal . " ";
    $query_cnt = $query_cnt . " and pm.final_amount > " . $fal . " ";
}

if($fal != "" && $fal_eq == "se")
{
    $query = $query . " and pm.final_amount >= " . $fal . " ";
    $query_cnt = $query_cnt . " and pm.final_amount >= " . $fal . " ";
}

if($fau != "" && $fau_eq == "s")
{
    $query = $query . " and pm.final_amount < " . $fau . " ";
    $query_cnt = $query_cnt . " and pm.final_amount < " . $fau . " ";
}

if($fau != "" && $fau_eq == "se")
{
    $query = $query . " and pm.final_amount <= " . $fau . " ";
    $query_cnt = $query_cnt . " and pm.final_amount <= " . $fau . " ";
}

if($fpl != "" && $fpl_eq == "s")
{
    $query = $query . " and Coalesce((SELECT sum(pp.amount) FROM  project_proof pp  WHERE  pp.project_id = pm.id  AND pp.status = 1  AND pp.kind = 0), -999999999.99) > " . $fpl . " ";
    $query_cnt = $query_cnt . " and Coalesce((SELECT sum(pp.amount) FROM  project_proof pp  WHERE  pp.project_id = pm.id  AND pp.status = 1  AND pp.kind = 0), -999999999.99) > " . $fpl . " ";
}

if($fpl != "" && $fpl_eq == "se")
{
    $query = $query . " and Coalesce((SELECT sum(pp.amount) FROM  project_proof pp  WHERE  pp.project_id = pm.id  AND pp.status = 1  AND pp.kind = 0), -999999999.99) >= " . $fpl . " ";
    $query_cnt = $query_cnt . " and Coalesce((SELECT sum(pp.amount) FROM  project_proof pp  WHERE  pp.project_id = pm.id  AND pp.status = 1  AND pp.kind = 0), -999999999.99) >= " . $fpl . " ";
}

if($fpu != "" && $fpu_eq == "s")
{
    $query = $query . " and Coalesce((SELECT sum(pp.amount) FROM  project_proof pp  WHERE  pp.project_id = pm.id  AND pp.status = 1  AND pp.kind = 0), 999999999.99) < " . $fpu . " ";
    $query_cnt = $query_cnt . " and Coalesce((SELECT sum(pp.amount) FROM  project_proof pp  WHERE  pp.project_id = pm.id  AND pp.status = 1  AND pp.kind = 0), 999999999.99) < " . $fpu . " ";
}

if($fpu != "" && $fpu_eq == "se")
{
    $query = $query . " and Coalesce((SELECT sum(pp.amount) FROM  project_proof pp  WHERE  pp.project_id = pm.id  AND pp.status = 1  AND pp.kind = 0), 999999999.99) <= " . $fpu . " ";
    $query_cnt = $query_cnt . " and Coalesce((SELECT sum(pp.amount) FROM  project_proof pp  WHERE  pp.project_id = pm.id  AND pp.status = 1  AND pp.kind = 0), 999999999.99) <= " . $fpu . " ";
}

if($frl != "" && $frl_eq == "s")
{
    $query = $query . " and Coalesce(final_amount, -999999999.99) - Coalesce((SELECT sum(pp.amount) FROM  project_proof pp  WHERE  pp.project_id = pm.id  AND pp.status = 1  AND pp.kind = 1), 0) - Coalesce((SELECT sum(pp.amount) FROM  project_proof pp  WHERE  pp.project_id = pm.id  AND pp.status = 1  AND pp.kind = 0), 0) - Coalesce(tax_withheld, 0) > " . $frl . " ";
    $query_cnt = $query_cnt . " and Coalesce(final_amount, -999999999.99) - Coalesce((SELECT sum(pp.amount) FROM  project_proof pp  WHERE  pp.project_id = pm.id  AND pp.status = 1  AND pp.kind = 1), 0) - Coalesce((SELECT sum(pp.amount) FROM  project_proof pp  WHERE  pp.project_id = pm.id  AND pp.status = 1  AND pp.kind = 0), 0) - Coalesce(tax_withheld, 0) > " . $frl . " ";
}

if($frl != "" && $frl_eq == "se")
{
    $query = $query . " and Coalesce(final_amount, -999999999.99) - Coalesce((SELECT sum(pp.amount) FROM  project_proof pp  WHERE  pp.project_id = pm.id  AND pp.status = 1  AND pp.kind = 1), 0) - Coalesce((SELECT sum(pp.amount) FROM  project_proof pp  WHERE  pp.project_id = pm.id  AND pp.status = 1  AND pp.kind = 0), 0) - Coalesce(tax_withheld, 0) >= " . $frl . " ";
    $query_cnt = $query_cnt . " and Coalesce(final_amount, -999999999.99) - Coalesce((SELECT sum(pp.amount) FROM  project_proof pp  WHERE  pp.project_id = pm.id  AND pp.status = 1  AND pp.kind = 1), 0) - Coalesce((SELECT sum(pp.amount) FROM  project_proof pp  WHERE  pp.project_id = pm.id  AND pp.status = 1  AND pp.kind = 0), 0) - Coalesce(tax_withheld, 0) >= " . $frl . " ";
}

if($fru != "" && $fru_eq == "s")
{
    $query = $query . " and Coalesce(final_amount, 999999999.99) - Coalesce((SELECT sum(pp.amount) FROM  project_proof pp  WHERE  pp.project_id = pm.id  AND pp.status = 1  AND pp.kind = 1), 0) - Coalesce((SELECT sum(pp.amount) FROM  project_proof pp  WHERE  pp.project_id = pm.id  AND pp.status = 1  AND pp.kind = 0), 0) - Coalesce(tax_withheld, 0) < " . $fru . " ";
    $query_cnt = $query_cnt . " and Coalesce(final_amount, 999999999.99) - Coalesce((SELECT sum(pp.amount) FROM  project_proof pp  WHERE  pp.project_id = pm.id  AND pp.status = 1  AND pp.kind = 1), 0) - Coalesce((SELECT sum(pp.amount) FROM  project_proof pp  WHERE  pp.project_id = pm.id  AND pp.status = 1  AND pp.kind = 0), 0) - Coalesce(tax_withheld, 0) < " . $fru . " ";
}

if($fru != "" && $fru_eq == "se")
{
    $query = $query . " and Coalesce(final_amount, 999999999.99) - Coalesce((SELECT sum(pp.amount) FROM  project_proof pp  WHERE  pp.project_id = pm.id  AND pp.status = 1  AND pp.kind = 1), 0) - Coalesce((SELECT sum(pp.amount) FROM  project_proof pp  WHERE  pp.project_id = pm.id  AND pp.status = 1  AND pp.kind = 0), 0) - Coalesce(tax_withheld, 0) <= " . $fru . " ";
    $query_cnt = $query_cnt . " and Coalesce(final_amount, 999999999.99) - Coalesce((SELECT sum(pp.amount) FROM  project_proof pp  WHERE  pp.project_id = pm.id  AND pp.status = 1  AND pp.kind = 1), 0) - Coalesce((SELECT sum(pp.amount) FROM  project_proof pp  WHERE  pp.project_id = pm.id  AND pp.status = 1  AND pp.kind = 0), 0) - Coalesce(tax_withheld, 0) <= " . $fru . " ";
}

if($aging != "")
{
    if($aging == "0")
    {
        $query = $query . " and DATEDIFF(CURRENT_DATE, pm.date_data_submission) < 30 ";
        $query_cnt = $query_cnt . " and DATEDIFF(CURRENT_DATE, pm.date_data_submission) < 30 ";
    }
    else if($aging == "30")
    {
        $query = $query . " and DATEDIFF(CURRENT_DATE, pm.date_data_submission) >= 30 and DATEDIFF(CURRENT_DATE, pm.date_data_submission) < 60 ";
        $query_cnt = $query_cnt . " and DATEDIFF(CURRENT_DATE, pm.date_data_submission) >= 30 and DATEDIFF(CURRENT_DATE, pm.date_data_submission) < 60 ";
    }
    else if($aging == "60")
    {
        $query = $query . " and DATEDIFF(CURRENT_DATE, pm.date_data_submission) >= 60 and DATEDIFF(CURRENT_DATE, pm.date_data_submission) < 90 ";
        $query_cnt = $query_cnt . " and DATEDIFF(CURRENT_DATE, pm.date_data_submission) >= 60 and DATEDIFF(CURRENT_DATE, pm.date_data_submission) < 90 ";
    }
    else if($aging == "90")
    {
        $query = $query . " and DATEDIFF(CURRENT_DATE, pm.date_data_submission) >= 90 and DATEDIFF(CURRENT_DATE, pm.date_data_submission) < 120 ";
        $query_cnt = $query_cnt . " and DATEDIFF(CURRENT_DATE, pm.date_data_submission) >= 90 and DATEDIFF(CURRENT_DATE, pm.date_data_submission) < 120 ";
    }
    else if($aging == "120")
    {
        $query = $query . " and DATEDIFF(CURRENT_DATE, pm.date_data_submission) >= 120 ";
        $query_cnt = $query_cnt . " and DATEDIFF(CURRENT_DATE, pm.date_data_submission) >= 120 ";
    }
}

if($fkp != "")
{
    $query = $query . " and pm.project_name like '%" . $fkp . "%' ";
    $query_cnt = $query_cnt . " and pm.project_name like '%" . $fkp . "%' ";
}

$sOrder = "";
if($of1 != "" && $of1 != "0")
{
    switch ($of1)
    {
        case 1:
            if($ofd1 == 2)
                $sOrder = "Coalesce(pm.created_at, '0000-00-00') desc";
            else
                $sOrder = "Coalesce(pm.created_at, '9999-99-99') ";
            break;  
        case 2:
            if($ofd1 == 2)
                $sOrder = "Coalesce(pm.updated_at, '0000-00-00') desc";
            else
                $sOrder = "Coalesce(pm.updated_at, '9999-99-99') ";
            break;  
        case 3:
            if($ofd1 == 2)
                $sOrder = "Coalesce(pm.final_amount, 0) desc";
            else
                $sOrder = "Coalesce(pm.final_amount, 99999999)";
            break;  
        case 4:
            if($ofd1 == 2)
                $sOrder = "Coalesce((SELECT sum(pp.amount) FROM  project_proof pp  WHERE  pp.project_id = pm.id  AND pp.status = 1  AND pp.kind = 0), 0) desc";
            else
                $sOrder = "Coalesce((SELECT sum(pp.amount) FROM  project_proof pp  WHERE  pp.project_id = pm.id  AND pp.status = 1  AND pp.kind = 0), 99999999)";
            break;
        case 5:
            if($ofd1 == 2)
                $sOrder = "Coalesce((SELECT sum(pp.amount) FROM  project_proof pp  WHERE  pp.project_id = pm.id  AND pp.status = 1  AND pp.kind = 1), 0) desc";
            else
                $sOrder = "Coalesce((SELECT sum(pp.amount) FROM  project_proof pp  WHERE  pp.project_id = pm.id  AND pp.status = 1  AND pp.kind = 1), 99999999)";
            break;
        case 6:
            if($ofd1 == 2)
                $sOrder = "Coalesce(pm.final_amount - Coalesce((SELECT sum(pp.amount) FROM  project_proof pp  WHERE  pp.project_id = pm.id  AND pp.status = 1), 0), 0) desc";
            else
                $sOrder = "Coalesce(pm.final_amount - Coalesce((SELECT sum(pp.amount) FROM  project_proof pp  WHERE  pp.project_id = pm.id  AND pp.status = 1), 0), 99999999) ";
            break;
        default:
    }
}

if($of2 != "" && $of2 != "0" && $sOrder != "")
{
    switch ($of2)
    {
        case 1:
            if($ofd2 == 2)
                $sOrder .= ", Coalesce(pm.created_at, '0000-00-00') desc";
            else
                $sOrder .= ", Coalesce(pm.created_at, '9999-99-99') ";
            break;  
        case 2:
            if($ofd2 == 2)
                $sOrder .= ", Coalesce(pm.updated_at, '0000-00-00') desc";
            else
                $sOrder .= ", Coalesce(pm.updated_at, '9999-99-99') ";
            break;  
        case 3:
            if($ofd2 == 2)
                $sOrder .= ", Coalesce(pm.final_amount, 0) desc";
            else
                $sOrder .= ", Coalesce(pm.final_amount, 99999999)";
            break;  
        case 4:
            if($ofd2 == 2)
                $sOrder .= ", Coalesce((SELECT sum(pp.amount) FROM  project_proof pp  WHERE  pp.project_id = pm.id  AND pp.status = 1  AND pp.kind = 0), 0) desc";
            else
                $sOrder .= ", Coalesce((SELECT sum(pp.amount) FROM  project_proof pp  WHERE  pp.project_id = pm.id  AND pp.status = 1  AND pp.kind = 0), 99999999)";
            break;
        case 5:
            if($ofd2 == 2)
                $sOrder .= ", Coalesce((SELECT sum(pp.amount) FROM  project_proof pp  WHERE  pp.project_id = pm.id  AND pp.status = 1  AND pp.kind = 1), 0) desc";
            else
                $sOrder .= ", Coalesce((SELECT sum(pp.amount) FROM  project_proof pp  WHERE  pp.project_id = pm.id  AND pp.status = 1  AND pp.kind = 1), 99999999)";
            break;
        case 6:
            if($ofd2 == 2)
                $sOrder .= ", Coalesce(pm.final_amount - Coalesce((SELECT sum(pp.amount) FROM  project_proof pp  WHERE  pp.project_id = pm.id  AND pp.status = 1), 0), 0) desc";
            else
                $sOrder .= ", Coalesce(pm.final_amount - Coalesce((SELECT sum(pp.amount) FROM  project_proof pp  WHERE  pp.project_id = pm.id  AND pp.status = 1), 0), 99999999) ";
            break;
        default:
    }
}


if($of2 != "" && $of2 != "0" && $sOrder == "")
{
    switch ($of2)
    {
        case 1:
            if($ofd2 == 2)
                $sOrder = "Coalesce(pm.created_at, '0000-00-00') desc";
            else
                $sOrder = "Coalesce(pm.created_at, '9999-99-99') ";
            break;  
        case 2:
            if($ofd2 == 2)
                $sOrder = "Coalesce(pm.updated_at, '0000-00-00') desc";
            else
                $sOrder = "Coalesce(pm.updated_at, '9999-99-99') ";
            break;  
        case 3:
            if($ofd2 == 2)
                $sOrder = "Coalesce(pm.final_amount, 0) desc";
            else
                $sOrder = "Coalesce(pm.final_amount, 99999999)";
            break;  
        case 4:
            if($ofd2 == 2)
                $sOrder = "Coalesce((SELECT sum(pp.amount) FROM  project_proof pp  WHERE  pp.project_id = pm.id  AND pp.status = 1  AND pp.kind = 0), 0) desc";
            else
                $sOrder = "Coalesce((SELECT sum(pp.amount) FROM  project_proof pp  WHERE  pp.project_id = pm.id  AND pp.status = 1  AND pp.kind = 0), 99999999)";
            break;
        case 5:
            if($ofd2 == 2)
                $sOrder = "Coalesce((SELECT sum(pp.amount) FROM  project_proof pp  WHERE  pp.project_id = pm.id  AND pp.status = 1  AND pp.kind = 1), 0) desc";
            else
                $sOrder = "Coalesce((SELECT sum(pp.amount) FROM  project_proof pp  WHERE  pp.project_id = pm.id  AND pp.status = 1  AND pp.kind = 1), 99999999)";
            break;
        case 6:
            if($ofd2 == 2)
                $sOrder = "Coalesce(pm.final_amount - Coalesce((SELECT sum(pp.amount) FROM  project_proof pp  WHERE  pp.project_id = pm.id  AND pp.status = 1), 0), 0) desc";
            else
                $sOrder = "Coalesce(pm.final_amount - Coalesce((SELECT sum(pp.amount) FROM  project_proof pp  WHERE  pp.project_id = pm.id  AND pp.status = 1), 0), 99999999) ";
            break;
        default:
    }
}

if($sOrder != "")
    $query = $query . " order by  " . $sOrder;
else
    $query = $query . " order by pm.created_at desc ";

if(!empty($_GET['page'])) {
    $page = filter_input(INPUT_GET, 'page', FILTER_VALIDATE_INT);
    if(false === $page) {
        $page = 1;
    }
}

if($fk == "")
{
    if(!empty($_GET['page'])) {
        $page = filter_input(INPUT_GET, 'page', FILTER_VALIDATE_INT);
        if(false === $page) {
            $page = 1;
        }
    }

    if(!empty($_GET['size'])) {
        $size = filter_input(INPUT_GET, 'size', FILTER_VALIDATE_INT);
        if(false === $size) {
            $size = 5;
        }

        $offset = ($page - 1) * $size;

        $query = $query . " LIMIT " . $offset . "," . $size;
    }
}

$cnt = 0;
$stmt_cnt = $db->prepare( $query_cnt );
$stmt_cnt->execute();

while($row = $stmt_cnt->fetch(PDO::FETCH_ASSOC)) {
    $cnt = $row['cnt'];
}


$stmt = $db->prepare( $query );
$stmt->execute();

while($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
    $id = $row['id'];
    $category = $row['category'];
    $category_name = GetCategory($row['category'], $db);
    $client_type = $row['client_type'];
    $pct_class = $row['pct_class'];
    $priority = $row['priority'];

    $pp_class = $row['pp_class'];
    $project_name = $row['project_name'];
    $project_status = $row['project_status'];
    $estimate_close_prob = $row['estimate_close_prob'];
    $username = $row['username'];

    $pm = $row['payment'];
    $dpm = $row['dp_payment'];
    $expense = $row['expense'];

    $final_amount = $row['final_amount'];
    $tax_withheld = $row['tax_withheld'];
    $billing_name = $row['billing_name'];
    $special = $row['special'];

    $quote_file_string = GetQuoteFileString($row['id'], $db);

    $payment_amount = GetPaymentAmount($row['id'], $db);
    $down_payment_amount = GetDownPaymentAmount($row['id'], $db);

    $apply_for_petty = GetApplyForPetty($row['id'], $db);
    $apply_for_petty_commission = GetApplyForPettyCommition($row['id'], $db);

    $ar = null;
    if($final_amount != null)
    {
        $pay = 0;
        if($payment_amount != null)
            $pay = $payment_amount;
        $down_pay = 0;
        if($down_payment_amount != null)
            $down_pay = $down_payment_amount;

        $ar = $final_amount - $pay - $down_pay - $tax_withheld;
    }

    if($ar != null)
        $ar = number_format((float)$ar, 2, '.', '');

    $created_at = $row['created_at'];
    $updated_at = $row['updated_at'];
    $stage = $row['stage'];
    $quote = GetQuote($row['id'], $db);
    $payment = GetPayment($row['id'], $db);

    $client_po = GetClientPO($row['id'], $db);
    $client_other = GetClientOther($row['id'], $db);

    $client_po_files = GetClientPOFile($row['id'], $db);
    $client_other_files = GetClientOtherFile($row['id'], $db);

    $final_quotation = GetFinalQuote($row['id'], $db);

    $down_pay_amount = RetriveDownPaymentAmount($payment);
    $down_pay_date = RetriveDownPaymentDate($payment);

    $full_pay_amount = RetrivePaymentAmount($payment);
    $full_pay_date = RetrivePaymentDate($payment);

    $invoice = RetrieveInvoice($payment);

    // client_other's Dte of sub
    $date_data_submission = "";
    $aging = "";

    foreach($client_other as $value)
    {
        if($value['kind'] == 4)
        {
            $date_data_submission = $value['date_data_submission'];
            $aging = $value['aging'];
        }
    }

    $merged_results[] = array(
        "id" => $id,
        "category" => $category,
        "category_name" => $category_name,
        "client_type" => $client_type,
        "pct_class" => $pct_class,
        "priority" => $priority,
        "pp_class" => $pp_class,
        "project_name" => $project_name,
        "project_status" => $project_status,
        "payment_amount" => $payment_amount,
        "down_payment_amount" => $down_payment_amount,
        "ar" => $ar,
        "final_quotation" => $final_quotation,
        "username" => $username,
        "created_at" => $created_at,
        "updated_at" => $updated_at,
        "stage" => $stage,
        "final_amount" => $final_amount,
        "tax_withheld" => $tax_withheld,
        "net_amount" => ($final_amount - $tax_withheld == 0 ? "" : $final_amount - $tax_withheld),
        "billing_name" => $billing_name,
        "quote" => $quote,
        "client_po" => $client_po,
        "client_other" => $client_other,
        "client_po_file" => $client_po_files,
        "client_other_file" => $client_other_files,
        "payment" => $payment,
        "down_pay_amount" => $down_pay_amount,
        "down_pay_date" => $down_pay_date,
        "full_pay_amount" => $full_pay_amount,
        "full_pay_date" => $full_pay_date,
        "invoice" => $invoice,
        "pm" => $pm,
        "dpm" => $dpm,
        "expense" => $expense,
        "quote_file_string" => $quote_file_string,
        "special" => $special,
        "apply_for_petty" => $apply_for_petty,
        "apply_for_petty_commission" => $apply_for_petty_commission,
        "cnt" => $cnt,
        "date_data_submission" => $date_data_submission,
        "aging" => $aging
    );
}

$cnt = 0;

if($fk != "")
{
    $_result = array();

    foreach ($merged_results as &$value) {
        if(
            preg_match("/{$fk}/i", $value['project_name']) || 
            preg_match("/{$fk}/i", $value['quote_file_string']))
        {
            $_result[] = $value;
        }
    }

    $cnt = count($_result);

    if($cnt > 0)
    {
        $return_result = array_slice($_result, ($page - 1) * $size, $size);
        $return_result[0]['cnt'] = $cnt;
    }
    else
    {
        $return_result = $_result;
    }
}
else
    $return_result = $merged_results;


echo json_encode($return_result, JSON_UNESCAPED_SLASHES);

function GetApplyForPetty($project_id, $db)
{
    $sql = "SELECT  Coalesce(ap.amount_verified, 0) amount_verified, ap.request_type,
                        Coalesce((select SUM(pl.price * pl.qty) from petty_list pl WHERE pl.petty_id = ap.id AND pl.`status` <> -1), 0) amount_applied
                    FROM apply_for_petty ap 
                    where project_name1 = (SELECT project_name FROM project_main WHERE id = " . $project_id . ") 
                    and ap.status = 9";

    $merged_results = array();

    $stmt = $db->prepare( $sql );
    $stmt->execute();

    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        $merged_results[] = $row;
    }

    $amount_verified = 0;

    foreach ($merged_results as &$value) {
        if($value['request_type'] == "1")
        $amount_verified += $value['amount_verified'];
        if($value['request_type'] == "2")
        $amount_verified += $value['amount_applied'];
    }

    return $amount_verified;
}

function GetApplyForPettyCommition($project_id, $db)
{
    $sql = "SELECT  Coalesce(ap.amount_verified, 0) amount_verified, ap.request_type,
                        Coalesce((select SUM(pl.price * pl.qty) from petty_list pl WHERE pl.petty_id = ap.id AND pl.`status` <> -1), 0) amount_applied
                    FROM apply_for_petty ap 
                    where project_name1 = (SELECT project_name FROM project_main WHERE id = " . $project_id . ") and info_category = 'Projects' and info_sub_category = 'Commission'
                    and ap.status = 9";

    $merged_results = array();

    $stmt = $db->prepare( $sql );
    $stmt->execute();

    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        $merged_results[] = $row;
    }

    $amount_verified = 0;

    foreach ($merged_results as &$value) {
        if($value['request_type'] == "1")
        $amount_verified += $value['amount_verified'];
        if($value['request_type'] == "2")
        $amount_verified += $value['amount_applied'];
    }

    return $amount_verified;
}

function GetFinalQuote($project_id, $db){
    $query = "
        SELECT 
            COALESCE(f.filename, '') filename, 
            COALESCE(f.bucketname, '') bucket, 
            COALESCE(f.gcp_name, '') gcp_name
        FROM   project_quotation pm
           
        LEFT JOIN gcp_storage_file f 
            ON f.batch_id = pm.id AND f.batch_type = 'quote' 
        WHERE  project_id = " . $project_id . "
            AND pm.status <> -1 
            AND pm.final_quotation = 1
    ";

    // prepare the query
    $stmt = $db->prepare($query);
    $stmt->execute();

    $merged_results = [];

    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        $merged_results[] = $row;
    }

    return $merged_results;
}

function GetClientOtherFile($project_id, $db){
    $query = "
        SELECT 
            COALESCE(f.filename, '') filename, 
            COALESCE(f.bucketname, '') bucket, 
            COALESCE(f.gcp_name, '') gcp_name
        FROM gcp_storage_file f 
           
        LEFT JOIN   project_client_po pm
            ON f.batch_id = pm.id AND f.batch_type = 'client_other' 
        WHERE  project_id = " . $project_id . "
            AND pm.status <> -1 
  
    ";

    // prepare the query
    $stmt = $db->prepare($query);
    $stmt->execute();

    $merged_results = [];

    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        $merged_results[] = $row;
    }

    return $merged_results;
}

function GetClientPOFile($project_id, $db){
    $query = "
        SELECT 
            COALESCE(f.filename, '') filename, 
            COALESCE(f.bucketname, '') bucket, 
            COALESCE(f.gcp_name, '') gcp_name
        FROM  gcp_storage_file f 
           
        LEFT JOIN  project_client_po pm 
            ON f.batch_id = pm.id AND f.batch_type = 'client_po' 
        WHERE  project_id = " . $project_id . "
            AND pm.status <> -1 
 
    ";

    // prepare the query
    $stmt = $db->prepare($query);
    $stmt->execute();

    $merged_results = [];

    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        $merged_results[] = $row;
    }

    return $merged_results;
}

function GetQuote($project_id, $db){
    $query = "
        SELECT pm.id,
            pm.remark comment,
            u.username,
            pm.created_at,
            final_quotation
        FROM   project_quotation pm
            LEFT JOIN user u
                    ON u.id = pm.create_id
        WHERE  project_id = " . $project_id . "
            AND pm.status <> -1 
        ORDER BY final_quotation desc, created_at
    ";

    // prepare the query
    $stmt = $db->prepare($query);
    $stmt->execute();

    $merged_results = [];

    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        $id = $row['id'];
        $comment = $row['comment'];
        $username = $row['username'];
        $created_at = $row['created_at'];
        $final_quotation = $row['final_quotation'];

        $create = substr($row['created_at'], 0, 10);

        $items = GetItem($row['id'], $db, 'quote');

        $searchstr = $comment . " " . GetItemString($row['id'], $db, 'quote') . " " . $username . " " . ($final_quotation ==  '0' ? 'N' : 'Y');
       
        $merged_results[] = array(
            "id" => $id,
            "comment" => $comment,
            "username" => $username,
            "created_at" => $created_at,
            "final_quotation" => $final_quotation,
            "items" => $items,
            "create" => $create,
            "searchstr" => strtolower($searchstr),
        );
    }

    return $merged_results;
}

function GetPaymentAmount($project_id, $db){
    $amount = null;
    $query = "
        SELECT 
            pm.amount
        FROM   project_proof pm
        WHERE  project_id = " . $project_id . "
            AND pm.status = 1
            AND pm.kind = 1
    ";

    // prepare the query
    $stmt = $db->prepare($query);
    $stmt->execute();

    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        if($row['amount'] != null)
            $amount += $row['amount'];
    }

    return $amount;
}

function GetDownPaymentAmount($project_id, $db){
    $amount = null;
    $query = "
        SELECT 
            pm.amount
        FROM   project_proof pm
        WHERE  project_id = " . $project_id . "
            AND pm.status = 1
            AND pm.kind = 0
    ";

    // prepare the query
    $stmt = $db->prepare($query);
    $stmt->execute();

    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        if($row['amount'] != null)
            $amount += $row['amount'];
    }

    return $amount;
}


function GetClientPO($project_id, $db){
    $query = "
        SELECT pm.id,
            pm.remark,
            u.username,
            pm.created_at,
            pm.kind,
            pm.date_data_submission,
            DATEDIFF(CURRENT_DATE, pm.date_data_submission) aging
        FROM   project_client_po pm
            LEFT JOIN user u
                ON u.id = pm.create_id
        WHERE  project_id = " . $project_id . "
            AND pm.`kind` = 1 AND pm.status <> -1
    ";

    // prepare the query
    $stmt = $db->prepare($query);
    $stmt->execute();

    $merged_results = [];

    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        $id = $row['id'];
        $remark = $row['remark'];
        $username = $row['username'];
        $created_at = $row['created_at'];
        $kind = $row['kind'];
        $date_data_submission = $row['date_data_submission'];
        $aging = $row['aging'];
     
        $items = GetItem($row['id'], $db, 'client_po');

        $searchstr = $remark . " "  . GetItemString($row['id'], $db, 'client_po') . " " . $username;
        $create = substr($created_at, 0, 10);
        $searchstr = $create . " " . $searchstr;
        $merged_results[] = array(
            "id" => $id,
            "comment" => $remark,
            "username" => $username,
            "created_at" => $created_at,
            "kind" => $kind,
            "items" => $items,
            "create" => $create,
            "searchstr" => strtolower($searchstr),
            "date_data_submission" => $date_data_submission,
            "aging" => $aging,
        );
    }

    return $merged_results;
}

function GetClientRemarks($project_id, $db){
    $query = "
        SELECT pm.id,
            pm.remark,
            u.username,
            pm.created_at,
            pm.kind,
            pm.date_data_submission,
            DATEDIFF(CURRENT_DATE, pm.date_data_submission) aging
        FROM   project_client_po pm
            LEFT JOIN user u
                ON u.id = pm.create_id
        WHERE  project_id = " . $project_id . "
            AND pm.`kind` = 3 AND pm.status <> -1
    ";

    // prepare the query
    $stmt = $db->prepare($query);
    $stmt->execute();

    $merged_results = [];

    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        $id = $row['id'];
        $remark = $row['remark'];
        $username = $row['username'];
        $created_at = $row['created_at'];
        $kind = $row['kind'];
        $date_data_submission = $row['date_data_submission'];
        $aging = $row['aging'];
     
        $items = GetItem($row['id'], $db, 'client_other');

        $searchstr = ($kind ==  '2' ? 'Other Files' : 'Remarks/Status') . " " . $remark . " "  . GetItemString($row['id'], $db, 'client_po') . " " . $username;
        $create = substr($created_at, 0, 10);
        $searchstr = $create . " " . $searchstr;
        $merged_results[] = array(
            "id" => $id,
            "comment" => $remark,
            "username" => $username,
            "created_at" => $created_at,
            "kind" => $kind,
            "items" => $items,
            "create" => $create,
            "searchstr" => strtolower($searchstr),
            "date_data_submission" => $date_data_submission,
            "aging" => $aging,
        );
    }

    return $merged_results;
}

function GetClientOther($project_id, $db){
    $query = "
        SELECT pm.id,
            pm.remark,
            u.username,
            pm.created_at,
            pm.kind,
            pm.date_data_submission,
            DATEDIFF(CURRENT_DATE, pm.date_data_submission) aging
        FROM   project_client_po pm
            LEFT JOIN user u
                ON u.id = pm.create_id
        WHERE  project_id = " . $project_id . "
            AND pm.`kind` <> 1 AND pm.status <> -1
    ";

    // prepare the query
    $stmt = $db->prepare($query);
    $stmt->execute();

    $merged_results = [];

    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        $id = $row['id'];
        $remark = $row['remark'];
        $username = $row['username'];
        $created_at = $row['created_at'];
        $kind = $row['kind'];
        $date_data_submission = $row['date_data_submission'];
        $aging = $row['aging'];
     
        $items = GetItem($row['id'], $db, 'client_other');

        $searchstr = ($kind ==  '2' ? 'Other Files' : 'Remarks/Status') . " " . $remark . " "  . GetItemString($row['id'], $db, 'client_po') . " " . $username;
        $create = substr($created_at, 0, 10);
        $searchstr = $create . " " . $searchstr;
        $merged_results[] = array(
            "id" => $id,
            "comment" => $remark,
            "username" => $username,
            "created_at" => $created_at,
            "kind" => $kind,
            "items" => $items,
            "create" => $create,
            "searchstr" => strtolower($searchstr),
            "date_data_submission" => $date_data_submission,
            "aging" => $aging,
        );
    }

    return $merged_results;
}

function GetPayment($project_id, $db){
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
            pm.payment_method_1,
            pm.payment_method_other,
            pm.status checked,
            pm.checked_id,
            pm.checked_at
        FROM   project_proof pm
            LEFT JOIN user u
                ON u.id = pm.create_id
        WHERE  project_id = " . $project_id . "
            AND pm.status <> -2
    ";

    // prepare the query
    $stmt = $db->prepare($query);
    $stmt->execute();

    $merged_results = [];

    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        $id = $row['id'];
        $remark = $row['remark'];
        $username = $row['username'];
        $created_at = $row['created_at'];
        $received_date = $row['received_date'];
        $kind = $row['kind'];
        $amount = $row['amount'];
        $invoice = $row['invoice'];
        $detail = $row['detail'];
        $payment_method_1 = $row['payment_method_1'];
        $payment_method_other = $row['payment_method_other'];
        $checked = $row['checked'];
        $checked_id = $row['checked_id'];
        $checked_at = $row['checked_at'];

        $items = GetItem($row['id'], $db, 'proof');

        $status = "";
        switch ($checked)
        {
            case "0":
                $status = "Under Checking";
                break;
            case "1":
                $status = "Checked: True";
                break;
            case "-1":
                $status = "Checked: False";
                break;
            default:
                $status = "Under Checking";
        }

        $searchstr = ($kind ==  '0' ? 'Down Payment' : 'Full Payment') . " " . $remark . " " . $status . " " . GetItemString($row['id'], $db, 'proof') . " " . $username . " " . $amount;
        $create = substr($created_at, 0, 10);
        $merged_results[] = array(
            "id" => $id,
            "remark" => $remark,
            "username" => $username,
            "created_at" => $created_at,
            "received_date" => $received_date,
            "kind" => $kind,
            "amount" => $amount,
            "invoice" => $invoice,
            "detail" => $detail,
            "payment_method_1" => $payment_method_1,
            "payment_method_other" => $payment_method_other,
            "checked" => $checked,
            "checked_id" => $checked_id,
            "checked_at" => $checked_at,
            "items" => $items,
            "create" => $create,
            "searchstr" => strtolower($searchstr),
        );
    }

    return $merged_results;
}

function GetItemString($batch_id, $db, $type){
    $query = "
        
        SELECT f.id,
            coalesce(f.filename, '')   filename 
        FROM   gcp_storage_file f

            LEFT JOIN user u
                ON u.id = f.create_id
        WHERE batch_id = " . $batch_id . "
        AND f.batch_type = '" . $type . "'
            AND f.status <> -1 
    ";

    // prepare the query
    $stmt = $db->prepare($query);
    $stmt->execute();

    $merged_results = "";

    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        $merged_results .= " " . $row['filename'];
    }

    return $merged_results;
}

function GetItem($batch_id, $db, $type){
    $query = "
        
        SELECT f.id,
            coalesce(f.filename, '')   filename,
            coalesce(f.bucketname, '') bucket,
            coalesce(f.gcp_name, '')   gcp_name,
            u.username,
            f.created_at
        FROM   gcp_storage_file f

            LEFT JOIN user u
                ON u.id = f.create_id
        WHERE batch_id = " . $batch_id . "
        AND f.batch_type = '" . $type . "'
            AND f.status <> -1 
    ";

    // prepare the query
    $stmt = $db->prepare($query);
    $stmt->execute();

    $merged_results = [];

    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        $merged_results[] = $row;
    }

    return $merged_results;
}

function GetCategory($id, $db)
{
    $category = "";
    $query = "SELECT category FROM project_category  where status <> -1 ".($id ? " and id=$id" : '');

    // prepare the query
    $stmt = $db->prepare($query);
    $stmt->execute();

    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        if($row['category'] != null)
            $category = $row['category'];
    }

    return $category;
}

function GetQuoteFileString($project_id, $db){
    $query = "
        SELECT 
            COALESCE(f.filename, '') filename
        FROM   project_quotation pm
           
        LEFT JOIN gcp_storage_file f 
            ON f.batch_id = pm.id AND f.batch_type = 'quote' 
        WHERE  project_id = " . $project_id . "
            AND pm.status <> -1 
            AND pm.final_quotation = 1
    ";

    // prepare the query
    $stmt = $db->prepare($query);
    $stmt->execute();

    $merged_results = "";

    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        $merged_results .= $row['filename'] . " ";
    }

    return $merged_results;
}

function RetriveDownPaymentAmount($payment) {
    $merged_results = [];

    foreach($payment as $pay)
    {
        if($pay["kind"] == 0)
            array_push($merged_results,$pay["amount"]);
    }
    return $merged_results;
}

function RetriveDownPaymentDate($payment) {
    $merged_results = [];

    foreach($payment as $pay)
    {
        if($pay["kind"] == 0)
            array_push($merged_results, $pay["received_date"] == "" ? "N/A" : $pay["received_date"]);
    }
    return $merged_results;
}

function RetrivePaymentAmount($payment) {
    $merged_results = [];

    foreach($payment as $pay)
    {
        if($pay["kind"] == 1)
            array_push($merged_results,$pay["amount"]);
    }
    return $merged_results;
}

function RetrivePaymentDate($payment) {
    $merged_results = [];

    foreach($payment as $pay)
    {
        if($pay["kind"] == 1)
            array_push($merged_results, $pay["received_date"] == "" ? "N/A" : $pay["received_date"]);
    }
    return $merged_results;
}

function RetrieveInvoice($payment) {
    $merged_results = [];

    foreach($payment as $pay)
    {
        array_push($merged_results, $pay["invoice"] == "" ? "N/A" : $pay["invoice"]);
    }
    return $merged_results;
}