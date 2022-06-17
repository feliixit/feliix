<?php

error_reporting(E_ALL);
ini_set('display_errors', 1);
$jwt = (isset($_COOKIE['jwt']) ?  $_COOKIE['jwt'] : null);
include_once 'config/core.php';
include_once 'libs/php-jwt-master/src/BeforeValidException.php';
include_once 'libs/php-jwt-master/src/ExpiredException.php';
include_once 'libs/php-jwt-master/src/SignatureInvalidException.php';
include_once 'libs/php-jwt-master/src/JWT.php';

include_once 'mail.php';

use \Firebase\JWT\JWT;

$method = $_SERVER['REQUEST_METHOD'];


if (!isset($jwt)) {
    http_response_code(401);

    echo json_encode(array("message" => "Access denied."));
    die();
} else {
    try {
        // decode jwt
        $decoded = JWT::decode($jwt, $key, array('HS256'));
        $user_id = $decoded->data->id;
        //if(!$decoded->data->is_admin)
        //{
        //  http_response_code(401);

        //  echo json_encode(array("message" => "Access denied."));
        //  die();
        //}
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

switch ($method) {
    case 'GET':
        $stage_id = (isset($_GET['stage_id']) ?  $_GET['stage_id'] : 0);

        $status = (isset($_GET['status']) ?  $_GET['status'] : '');
        $priority = (isset($_GET['priority']) ?  $_GET['priority'] : '');
        $duedate = (isset($_GET['duedate']) ?  $_GET['duedate'] : '');

        $page = (isset($_GET['page']) ?  $_GET['page'] : "");
        $size = (isset($_GET['size']) ?  $_GET['size'] : "");

        $category = "";
        $category_id = 0;

        $sql = "select pc.id category_id,  Coalesce(pc.category, '')              category  
                    from project_stages ps
                left join project_main p on p.id = ps.project_id
                LEFT JOIN project_category pc ON p.catagory_id = pc.id
                where ps.id = " . $stage_id . " ";

        $stmt = $db->prepare($sql);
        $stmt->execute();

        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
    
            $category = $row['category'];
            $category_id = $row['category_id'];
        }

        $sql = "SELECT pm.id, 
                        pm.stage_id, 
                        ps.project_id,
                        pg.stage, 
                        Coalesce(pc.category, '')              category,
                        down_payment_date, 
                        account_executive, 
                        pic, 
                        quotation,
                        client_name,
                        contact_person,
                        pm.contact_number,
                        delivery_address_within,
                        delivery_address_outside,
                        exact_delivery_address,
                        detail_delivery_address,
                        attached_layout,
                        delivery_permit,
                        work_permit,
                        permit_processing_note,
                        other_request,
                        date_of_delivery,
                        client_deadline,
                        delivery_1st,
                        delivery_1st_items,
                        delivery_2nd,
                        delivery_2nd_items,
                        os_delivery_only,
                        os_delivery_install,
                        lt_delivery_only,
                        lt_delivery_install,
                        delivery_install,
                        scope_attached_layout,
                        timeline_check,
                        timeline,
                        data_check,
                        `data`,
                        electrical_check,
                        electrical,
                        flooring_check,
                        flooring,
                        type_and_ceiling,
                        painting_check,
                        painting,
                        ceiling_electrical_check,
                        ceiling_electrical,
                        manpower_check,
                        manpower,
                        materials_check,
                        materials,
                        trucking_services,
                        purchasing_of_special_products_check,
                        purchasing_of_special_products,
                        tools_check,
                        tools,
                        pm.`status` task_status, 
                        u.id uid, 
                        u.username creator, 
                        pm.created_at,
                        uu.username updator,
                        pm.updated_at,
                        p.special
                from project_a_meeting pm 
                        LEFT JOIN user u ON u.id = pm.create_id 
                        LEFT JOIN user uu ON uu.id = pm.updated_id 
                        LEFT JOIN project_stages ps ON pm.stage_id = ps.id
                        LEFT JOIN project_stage pg ON ps.stage_id = pg.id
                        left join project_main p ON p.id = ps.project_id
                        LEFT JOIN project_category pc ON p.catagory_id = pc.id
                where pm.stage_id = " . $stage_id . " ";

        if ($status != 0) {
            $sql = $sql . " and pm.`status` = " . $status . " ";
        }

        if ($priority != 0) {
            $sql = $sql . " and pm.priority = " . $priority . " ";
        }

        if ($duedate != '') {
            $sql = $sql . " and DATE_FORMAT(pm.due_date, '%Y-%m-%d') = '" . $duedate . "' ";
        }

        if (!empty($_GET['page'])) {
            $page = filter_input(INPUT_GET, 'page', FILTER_VALIDATE_INT);
            if (false === $page) {
                $page = 1;
            }
        }

        $sql = $sql . " ORDER BY pm.id ";

        if (!empty($_GET['size'])) {
            $size = filter_input(INPUT_GET, 'size', FILTER_VALIDATE_INT);
            if (false === $size) {
                $size = 10;
            }

            $offset = ($page - 1) * $size;

            $sql = $sql . " LIMIT " . $offset . "," . $size;
        }

        $merged_results = array();

        $stmt = $db->prepare($sql);
        $stmt->execute();

        $id = 0;
   
        $project_id = 0;
   
        $stage = "";
        $down_payment_date = "";
        $account_executive = "";
        $pic = "";
        $quotation = "";
        $client_name = "";
        $contact_person = "";
        $contact_number = "";
        $delivery_address_within = "";
        $delivery_address_outside = "";
        $exact_delivery_address = "";
        $detail_delivery_address = "";
        $attached_layout = "";
        $delivery_permit = "";
        $work_permit = "";
        $permit_processing_note = "";
        $other_request = "";
        $date_of_delivery = "";
        $client_deadline = "";
        $delivery_1st = "";
        $delivery_1st_items = "";
        $delivery_2nd = "";
        $delivery_2nd_items = "";
        $os_delivery_only = "";
        $os_delivery_install = "";
        $lt_delivery_only = "";
        $lt_delivery_install = "";
        $delivery_install = "";
        $scope_attached_layout = "";
        $timeline_check = "";
        $timeline = "";
        $data_check = "";
        $data = "";
        $electrical_check = "";
        $electrical = "";
        $flooring_check = "";
        $flooring = "";
        $type_and_ceiling = "";
        $painting_check = "";
        $painting = "";
        $ceiling_electrical_check = "";
        $ceiling_electrical = "";
        $manpower_check = "";
        $manpower = "";
        $materials_check = "";
        $materials = "";
        $trucking_services = "";
        $purchasing_of_special_products_check = "";
        $purchasing_of_special_products = "";
        $tools_check = "";
        $tools = "";
        $task_status = "";
        $uid = 0;
        $creator = "";
        $created_at = "";
        $updator = "";
        $updated_at = "";

        $special = "";
               
        $attached_layout_files = [];
        $scope_attached_layout_files = [];
        $other_attached_layout_files = [];

        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $id = $row['id'];
            $stage_id = $row['stage_id'];
            $project_id = $row['project_id'];
            $category = $row['category'];
            $stage = $row['stage'];
            $down_payment_date = $row['down_payment_date'];
            $account_executive = $row['account_executive'];
            $pic = $row['pic'];
            $quotation = $row['quotation'];
            $client_name = $row['client_name'];
            $contact_person = $row['contact_person'];
            $contact_number = $row['contact_number'];
            $delivery_address_within = $row['delivery_address_within'];
            $delivery_address_outside = $row['delivery_address_outside'];
            $exact_delivery_address = $row['exact_delivery_address'];
            $detail_delivery_address = $row['detail_delivery_address'];
            $attached_layout = $row['attached_layout'];

            $attached_layout_files = GetAttachment($id, "attached_layout", $db);

            $delivery_permit = $row['delivery_permit'];
            $work_permit = $row['work_permit'];
            $permit_processing_note = $row['permit_processing_note'];
            $other_request = $row['other_request'];
            $date_of_delivery = $row['date_of_delivery'];
            $client_deadline = $row['client_deadline'];
            $delivery_1st = $row['delivery_1st'];
            $delivery_1st_items = $row['delivery_1st_items'];
            $delivery_2nd = $row['delivery_2nd'];
            $delivery_2nd_items = $row['delivery_2nd_items'];
            $os_delivery_only = $row['os_delivery_only'];
            $os_delivery_install = $row['os_delivery_install'];
            $lt_delivery_only = $row['lt_delivery_only'];
            $lt_delivery_install = $row['lt_delivery_install'];
            $delivery_install = $row['delivery_install'];
            $scope_attached_layout = $row['scope_attached_layout'];

            $scope_attached_layout_files = GetAttachment($id, "scope_attached_layout", $db);

            $timeline_check = $row['timeline_check'];
            $timeline = $row['timeline'];
            $data_check = $row['data_check'];
            $data = $row['data'];
            $electrical_check = $row['electrical_check'];
            $electrical = $row['electrical'];
            $flooring_check = $row['flooring_check'];
            $flooring = $row['flooring'];
            $type_and_ceiling = $row['type_and_ceiling'];
            $painting_check = $row['painting_check'];
            $painting = $row['painting'];
            $ceiling_electrical_check = $row['ceiling_electrical_check'];
            $ceiling_electrical = $row['ceiling_electrical'];
            $manpower_check = $row['manpower_check'];
            $manpower = $row['manpower'];
            $materials_check = $row['materials_check'];
            $materials = $row['materials'];
            $trucking_services = $row['trucking_services'];
            $purchasing_of_special_products_check = $row['purchasing_of_special_products_check'];
            $purchasing_of_special_products = $row['purchasing_of_special_products'];
            $tools_check = $row['tools_check'];
            $tools = $row['tools'];
            $task_status = $row['task_status'];
            $uid = $row['uid'];
            $creator = $row['creator'];
            $created_at = $row['created_at'];
            $updator = $row['updator'];
            $updated_at = $row['updated_at'];

            $special = $row['special'];

            if($updator == null){
                $updator = $creator;
                $updated_at = $created_at;
            }

            $other_attached_layout_files = GetAttachment($id, "other_attached_layout", $db);
            

        }

        $merged_results[] = array(
            "id" => $id,
            "stage_id" => $stage_id,
            "project_id" => $project_id,
            "category" => $category,
            "category_id" => $category_id,
            "stage" => $stage,
            "down_payment_date" => $down_payment_date,
            "account_executive" => $account_executive,
            "pic" => $pic,
            "quotation" => $quotation,
            "client_name" => $client_name,
            "contact_person" => $contact_person,
            "contact_number" => $contact_number,
            "delivery_address_within" => formatCheckBox($delivery_address_within),
            "delivery_address_outside" => formatCheckBox($delivery_address_outside),
            "exact_delivery_address" => $exact_delivery_address,
            "detail_delivery_address" => $detail_delivery_address,
            "attached_layout" => formatCheckBox($attached_layout),
            "attached_layout_files" => $attached_layout_files,
            "delivery_permit" => formatCheckBox($delivery_permit),
            "work_permit" => formatCheckBox($work_permit),
            "permit_processing_note" => $permit_processing_note,
            "other_request" => $other_request,
            "date_of_delivery" => $date_of_delivery,
            "client_deadline" => $client_deadline,
            "delivery_1st" => formatCheckBox($delivery_1st),
            "delivery_1st_items" => $delivery_1st_items,
            "delivery_2nd" => formatCheckBox($delivery_2nd),
            "delivery_2nd_items" => $delivery_2nd_items,
            "os_delivery_only" => formatCheckBox($os_delivery_only),
            "os_delivery_install" => formatCheckBox($os_delivery_install),
            "lt_delivery_only" => formatCheckBox($lt_delivery_only),
            "lt_delivery_install" => formatCheckBox($lt_delivery_install),
            "delivery_install" => formatCheckBox($delivery_install),
            "scope_attached_layout" => formatCheckBox($scope_attached_layout),
            "scope_attached_layout_files" => $scope_attached_layout_files,
            "timeline_check" => formatCheckBox($timeline_check),
            "timeline" => $timeline,
            "data_check" => formatCheckBox($data_check),
            "data" => $data,
            "electrical_check" => formatCheckBox($electrical_check),
            "electrical" => $electrical,
            "flooring_check" => formatCheckBox($flooring_check),
            "flooring" => $flooring,
            "type_and_ceiling" => $type_and_ceiling,
            "painting_check" => formatCheckBox($painting_check),
            "painting" => $painting,
            "ceiling_electrical_check" => formatCheckBox($ceiling_electrical_check),
            "ceiling_electrical" => $ceiling_electrical,
            "manpower_check" => formatCheckBox($manpower_check),
            "manpower" => $manpower,
            "materials_check" => formatCheckBox($materials_check),
            "materials" => $materials,
            "trucking_services" => formatCheckBox($trucking_services),
            "purchasing_of_special_products_check" => formatCheckBox($purchasing_of_special_products_check),
            "purchasing_of_special_products" => $purchasing_of_special_products,
            "tools_check" => formatCheckBox($tools_check),
            "tools" => $tools,
            "task_status" => $task_status,
            "uid" => $uid,
            "creator" => $creator,
            "created_at" => $created_at,
            "updator" => $updator,
            "updated_at" => $updated_at,
            "other_attached_layout_files" => $other_attached_layout_files,
            "special" => $special,
        );


        echo json_encode($merged_results, JSON_UNESCAPED_SLASHES);

        break;

}

function formatCheckBox($value){
    $ret = '';
    if($value == 't' || $value == '1'){
        $ret = '1';
    }

    if($value == 'f' || $value == '0'){
        $ret = '';
    }

    return $ret;
}

function GetAttachment($id, $type, $db)
{
    $sql = "select h.id, 
                COALESCE(h.filename, '') filename, 
                COALESCE(h.gcp_name, '') gcp_name
            from project_a_meeting p 
            LEFT JOIN gcp_storage_file h ON h.batch_id = p.id AND h.batch_type = '" . $type . "'
            where p.id = " . $id . " and h.`status` <> -1 order by h.id";

    $items = array();

    $stmt = $db->prepare($sql);
    $stmt->execute();

    $is_checked = "";
    $gcp_name = "";
    $filename = "";
   

    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        $id = $row['id'];
        $gcp_name = $row["gcp_name"];
        $filename = $row["filename"];

        if ($filename != "")
            $items[] = array(
                'id' => $id,
                'checked' => true,
                'file' => null,
                'gcp_name' => $gcp_name,
                'name' => $filename,
            );
    }

    return $items;

}
