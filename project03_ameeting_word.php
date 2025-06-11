<?php
ob_start();
error_reporting(E_ALL);

include_once 'api/config/database.php';
include_once 'api/config/conf.php';

require 'vendor/autoload.php';

include_once 'api/config/core.php';
include_once 'api/libs/php-jwt-master/src/BeforeValidException.php';
include_once 'api/libs/php-jwt-master/src/ExpiredException.php';
include_once 'api/libs/php-jwt-master/src/SignatureInvalidException.php';
include_once 'api/libs/php-jwt-master/src/JWT.php';
use \Firebase\JWT\JWT;

use PhpOffice\PhpWord\IOFactory;

try {
    $jwt = (isset($_COOKIE['jwt']) ?  $_COOKIE['jwt'] : null);
    // decode jwt
    $decoded = JWT::decode($jwt, $key, array('HS256'));

    if($decoded->data->limited_access == true)
                header( 'location:index' );

    //if(passport_decrypt( base64_decode($uid)) !== $decoded->data->username )
    //    header( 'location:index.php' );
}
    // if decode fails, it means jwt is invalid
catch (Exception $e){

    die();
}

$database = new Database();
$db = $database->getConnection();

$stage_id = (isset($_GET['stage_id']) ?  $_GET['stage_id'] : 0);

$category = "";
        $category_id = 0;
        $project_name = "";

        $sql = "select p.project_name, pc.id category_id,  Coalesce(pc.category, '')              category  
                    from project_stages ps
                left join project_main p on p.id = ps.project_id
                LEFT JOIN project_category pc ON p.catagory_id = pc.id
                where ps.id = " . $stage_id . " ";

        $stmt = $db->prepare($sql);
        $stmt->execute();

        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
    
            $category = $row['category'];
            $category_id = $row['category_id'];
            $project_name = $row['project_name'];
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
                        pm.updated_at
                from project_a_meeting pm 
                        LEFT JOIN user u ON u.id = pm.create_id 
                        LEFT JOIN user uu ON uu.id = pm.updated_id 
                        LEFT JOIN project_stages ps ON pm.stage_id = ps.id
                        LEFT JOIN project_stage pg ON ps.stage_id = pg.id
                        left join project_main p ON p.id = ps.project_id
                        LEFT JOIN project_category pc ON p.catagory_id = pc.id
                where pm.stage_id = " . $stage_id . " ";

    

        $sql = $sql . " ORDER BY pm.id ";

    

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

            if($updator == null){
                $updator = $creator;
                $updated_at = $created_at;
            }

            $other_attached_layout_files = GetAttachment($id, "other_attached_layout", $db);

        }

// Creating the new document...
$phpWord = new PhpOffice\PhpWord\PhpWord();

/* Note: any element you append to a document must reside inside of a Section. */

// Adding an empty Section to the document...
$section = $phpWord->addSection();
// Adding Text element to the Section having font styled by default...
$section->addText("  " . "  " . "  " . "  " . "  " . "  " . "  " . "  " . "  " . "  " . "  " . "  " . "  " . "  " . "  " . "  " . "  " . "  " . "  " . "  " . "  " . "  " . "  " . "  " . "  " . "  " . "  " . "  " . "  " . "  " . "SALES TEAM", array('bold' => true, 'align' => 'middle', 'size' => 14));

$section->addText("  " . "  " . "  " . "  " . "  " . "  " . "  " . "  " . "  " . "  " . "  " . "  " . "  " . "  " . "  " . "  " . "  " . "  " . "  " . "  " . "  " . "PROJECT TURNOVER CHECKLIST", array('bold' => true, 'align' => 'middle', 'size' => 14));

$section->addText("", array('bold' => true, 'valign' => 'center', 'size' => 14));

$capitalCell =
[
    'align' => 'center',
];

$styleCell =
[
    'border-style' => 'none',
    'border-size' => 0,
];

$valueCell =
[
    'border-style' => 'solid solid solid none',
    'border-color' => 'black black black',
    'underline' => 'single',
    'border-size' => 0,
];

$table = $section->addTable('table', [
    'borderSize' => 6, 
    'borderColor' => '000000', 
    'afterSpacing' => 0, 
    'Spacing'=> 5, 
    'cellMargin'=> 0,
    'font' => 'Verdana',
    'size' => 11,
]);

$table->addRow();
$textrun = $table->addCell(10500, $styleCell)->addTextRun();
$textrun->addText(htmlspecialchars("Project Name: ", ENT_COMPAT, 'UTF-8'), array('font' => 'Verdana', 'size' => 11, 'Spacing'=> 5));
$textrun->addText(htmlspecialchars($project_name, ENT_COMPAT, 'UTF-8'), array('underline' => 'single', 'font' => 'Verdana', 'size' => 11, 'Spacing'=> 5));

$table->addRow();
$textrun = $table->addCell(10500, $styleCell)->addTextRun();
$textrun->addText(htmlspecialchars("Project Category: ", ENT_COMPAT, 'UTF-8'), array('font' => 'Verdana', 'size' => 11, 'Spacing'=> 5));
$textrun->addText(htmlspecialchars($category, ENT_COMPAT, 'UTF-8'), array('underline' => 'single', 'font' => 'Verdana', 'size' => 11, 'Spacing'=> 5));

$table->addRow();
$textrun = $table->addCell(10500, $styleCell)->addTextRun();
$textrun->addText(htmlspecialchars("Date of Down payment: ", ENT_COMPAT, 'UTF-8'), array('font' => 'Verdana', 'size' => 11, 'Spacing'=> 5));
$textrun->addText(htmlspecialchars($down_payment_date, ENT_COMPAT, 'UTF-8'), array('underline' => 'single', 'font' => 'Verdana', 'size' => 11, 'Spacing'=> 5));

$table->addRow();
$textrun = $table->addCell(10500, $styleCell)->addTextRun();
$textrun->addText(htmlspecialchars("Customer Value Supervisor: ", ENT_COMPAT, 'UTF-8'), array('font' => 'Verdana', 'size' => 11, 'Spacing'=> 5));
$textrun->addText(htmlspecialchars($account_executive, ENT_COMPAT, 'UTF-8'), array('underline' => 'single', 'font' => 'Verdana', 'size' => 11, 'Spacing'=> 5));

$table->addRow();
$textrun = $table->addCell(10500, $styleCell)->addTextRun();
$textrun->addText(htmlspecialchars("PIC: ", ENT_COMPAT, 'UTF-8'), array('font' => 'Verdana', 'size' => 11, 'Spacing'=> 5));
$textrun->addText(htmlspecialchars($pic, ENT_COMPAT, 'UTF-8'), array('underline' => 'single', 'font' => 'Verdana', 'size' => 11, 'Spacing'=> 5));

$table->addRow();
$textrun = $table->addCell(10500, $styleCell)->addTextRun();
$textrun->addText(htmlspecialchars("", ENT_COMPAT, 'UTF-8'), array('bold' => true));

/*
$table->addRow();
$table->addCell(600, $styleCell)->addText("Last Updated", array('bold' => true));
$table->addCell(8500, $styleCell)->addText($updator != "" ? $updator . " at " . $updated_at : "");
*/

$table->addRow();
$textrun = $table->addCell(10500, $styleCell)->addTextRun();
$textrun->addText(htmlspecialchars("    " . "1. Project Details", ENT_COMPAT, 'UTF-8'), array('bold' => true, 'size' => 12, 'font' => 'Verdana',  'Spacing'=> 5));
$textrun->addText(htmlspecialchars("", ENT_COMPAT, 'UTF-8'), array('underline' => 'single', 'font' => 'Verdana', 'size' => 11, 'Spacing'=> 5));


$cat_team = '';
if($category_id == "1")
{
    $cat_team = "Office Systems Furniture Project";
}

if($category_id == "2")
{
    $cat_team = "Lighting Project";
}


$table->addRow();
$textrun = $table->addCell(10500, $styleCell)->addTextRun();
$textrun->addText(htmlspecialchars("    " . "    " . $cat_team . " - Quotation #: ", ENT_COMPAT, 'UTF-8'), array('font' => 'Verdana', 'size' => 11, 'Spacing'=> 5));
$textrun->addText(htmlspecialchars($quotation, ENT_COMPAT, 'UTF-8'), array('underline' => 'single', 'font' => 'Verdana', 'size' => 11, 'Spacing'=> 5));


$table->addRow();
$textrun = $table->addCell(10500, $styleCell)->addTextRun();
$textrun->addText(htmlspecialchars("    " . "    " . "a. Client Name: ", ENT_COMPAT, 'UTF-8'), array('font' => 'Verdana', 'size' => 11, 'Spacing'=> 5));
$textrun->addText(htmlspecialchars($client_name, ENT_COMPAT, 'UTF-8'), array('underline' => 'single', 'font' => 'Verdana', 'size' => 11, 'Spacing'=> 5));


$table->addRow();
$textrun = $table->addCell(10500, $styleCell)->addTextRun();
$textrun->addText(htmlspecialchars("    " . "    " . "b. Contact Person: ", ENT_COMPAT, 'UTF-8'), array('font' => 'Verdana', 'size' => 11, 'Spacing'=> 5));
$textrun->addText(htmlspecialchars($contact_person, ENT_COMPAT, 'UTF-8'), array('underline' => 'single', 'font' => 'Verdana', 'size' => 11, 'Spacing'=> 5));


$table->addRow();
$textrun = $table->addCell(10500, $styleCell)->addTextRun();
$textrun->addText(htmlspecialchars("    " . "    " . "c. Contact Number: ", ENT_COMPAT, 'UTF-8'), array('font' => 'Verdana', 'size' => 11, 'Spacing'=> 5));
$textrun->addText(htmlspecialchars($contact_number, ENT_COMPAT, 'UTF-8'), array('underline' => 'single', 'font' => 'Verdana', 'size' => 11, 'Spacing'=> 5));


$table->addRow();
$textrun = $table->addCell(10500, $styleCell)->addTextRun();
$textrun->addText(htmlspecialchars("    " . "    " . "d. Delivery Address: ", ENT_COMPAT, 'UTF-8'), array('font' => 'Verdana', 'size' => 11, 'Spacing'=> 5));
$textrun->addText(htmlspecialchars("", ENT_COMPAT, 'UTF-8'), array('underline' => 'single', 'font' => 'Verdana', 'size' => 11, 'Spacing'=> 5));


$table->addRow();
$textrun = $table->addCell(10500, $styleCell)->addTextRun();
$textrun->addText(htmlspecialchars("", ENT_COMPAT, 'UTF-8'), array('bold' => true));
$textrun->addText(htmlspecialchars("    " . "    " . "    " . (($delivery_address_within == '1' || $delivery_address_within == 't') ? "■ Within Metro Manila" : "□ Within Metro Manila"), ENT_COMPAT, 'UTF-8'), array('bold' => false, 'font' => 'Verdana', 'size' => 11, 'Spacing'=> 5));


$table->addRow();
$textrun = $table->addCell(10500, $styleCell)->addTextRun();
$textrun->addText(htmlspecialchars("", ENT_COMPAT, 'UTF-8'), array('bold' => true));
$textrun->addText(htmlspecialchars("    " . "    " . "    " . (($delivery_address_outside == '1' || $delivery_address_outside == 't') ? "■ Outside Metro Manila" : "□ Outside Metro Manila"), ENT_COMPAT, 'UTF-8'), array('bold' => false, 'font' => 'Verdana', 'size' => 11, 'Spacing'=> 5));


$table->addRow();
$textrun = $table->addCell(10500, $styleCell)->addTextRun();
$textrun->addText(htmlspecialchars("    " . "    " . "e. Exact Delivery Address: ", ENT_COMPAT, 'UTF-8'), array('font' => 'Verdana', 'size' => 11, 'Spacing'=> 5));
$textrun->addText(htmlspecialchars($exact_delivery_address, ENT_COMPAT, 'UTF-8'), array('underline' => 'single', 'font' => 'Verdana', 'size' => 11, 'Spacing'=> 5));


$table->addRow();
$textrun = $table->addCell(10500, $styleCell)->addTextRun();
$textrun->addText(htmlspecialchars("    " . "    " . "f. Detailed Delivery and Installation location ( Area / Floor / Department / Room Number ): ", ENT_COMPAT, 'UTF-8'), array('font' => 'Verdana', 'size' => 11, 'Spacing'=> 5));
$textrun->addText(htmlspecialchars($detail_delivery_address, ENT_COMPAT, 'UTF-8'), array('underline' => 'single', 'font' => 'Verdana', 'size' => 11, 'Spacing'=> 5));



$table->addRow();
$textrun = $table->addCell(10500, $styleCell)->addTextRun();
$textrun->addText(htmlspecialchars("    " . "    " . "    " . (($attached_layout == '1' || $attached_layout == 't') ? "■ See attached approved furniture layout / lighting layout" : "□ See attached approved furniture layout / lighting layout"), ENT_COMPAT, 'UTF-8'), array('bold' => false, 'font' => 'Verdana', 'size' => 11, 'Spacing'=> 5));
$textrun->addText(htmlspecialchars("", ENT_COMPAT, 'UTF-8'), array('underline' => 'single', 'font' => 'Verdana', 'size' => 11, 'Spacing'=> 5));


foreach($attached_layout_files as $file)
{
    $table->addRow();
    $table->addCell(10500, $styleCell)->addText("    " . "    " . "    " . "    " . $file["name"], array('bold' => false, 'font' => 'Verdana', 'size' => 11, 'Spacing'=> 5));
    
}

$table->addRow();
$textrun = $table->addCell(10500, $styleCell)->addTextRun();
$textrun->addText(htmlspecialchars("    " . "    " . "g. Permit Processing:", ENT_COMPAT, 'UTF-8'), array('font' => 'Verdana', 'size' => 11, 'Spacing'=> 5));
$textrun->addText(htmlspecialchars("", ENT_COMPAT, 'UTF-8'), array('underline' => 'single', 'font' => 'Verdana', 'size' => 11, 'Spacing'=> 5));

$table->addRow();
$textrun = $table->addCell(10500, $styleCell)->addTextRun();
$textrun->addText(htmlspecialchars("    " . "    " . "    " . "", ENT_COMPAT, 'UTF-8'), array('bold' => true, 'font' => 'Verdana', 'size' => 11, 'Spacing'=> 5));
$textrun->addText(htmlspecialchars((($delivery_permit == '1' || $delivery_permit == 't') ? "■ Delivery Permit" : "□ Delivery Permit"), ENT_COMPAT, 'UTF-8'), array('bold' => false, 'font' => 'Verdana', 'size' => 11, 'Spacing'=> 5));


$table->addRow();
$textrun = $table->addCell(10500, $styleCell)->addTextRun();
$textrun->addText(htmlspecialchars("    " . "    " . "    " . "", ENT_COMPAT, 'UTF-8'), array('bold' => true, 'font' => 'Verdana', 'size' => 11, 'Spacing'=> 5));
$textrun->addText(htmlspecialchars((($work_permit == '1' || $work_permit == 't') ? "■ Work Permit (for Delivery and Install projects)" : "□ Work Permit (for Delivery and Install projects)"), ENT_COMPAT, 'UTF-8'), array('bold' => false, 'font' => 'Verdana', 'size' => 11, 'Spacing'=> 5));


$table->addRow();
$textrun = $table->addCell(10500, $styleCell)->addTextRun();
$textrun->addText(htmlspecialchars("    " . "    " . "    " . "Notes: ", ENT_COMPAT, 'UTF-8'), array('font' => 'Verdana', 'size' => 11, 'Spacing'=> 5));
$textrun->addText(htmlspecialchars($permit_processing_note, ENT_COMPAT, 'UTF-8'), array('underline' => 'single', 'font' => 'Verdana', 'size' => 11, 'Spacing'=> 5));


$table->addRow();
$textrun = $table->addCell(10500, $styleCell)->addTextRun();
$textrun->addText(htmlspecialchars("    " . "    " . "h. Other Client Concern / Request: ", ENT_COMPAT, 'UTF-8'), array('font' => 'Verdana', 'size' => 11, 'Spacing'=> 5));
$textrun->addText(htmlspecialchars($other_request, ENT_COMPAT, 'UTF-8'), array('underline' => 'single', 'font' => 'Verdana', 'size' => 11, 'Spacing'=> 5));

$table->addRow();
$textrun = $table->addCell(10500, $styleCell)->addTextRun();
$textrun->addText(htmlspecialchars("", ENT_COMPAT, 'UTF-8'), array('bold' => true, 'font' => 'Verdana', 'size' => 11, 'Spacing'=> 5));

$table->addRow();
$textrun = $table->addCell(10500, $styleCell)->addTextRun();
$textrun->addText(htmlspecialchars("    " . "", ENT_COMPAT, 'UTF-8'), array('bold' => true, 'font' => 'Verdana', 'size' => 11, 'Spacing'=> 5));
$textrun->addText(htmlspecialchars("2. Delivery Schedule", ENT_COMPAT, 'UTF-8'), array('bold' => true, 'size' => 12, 'font' => 'Verdana', 'Spacing'=> 5));


$table->addRow();
$textrun = $table->addCell(10500, $styleCell)->addTextRun();
$textrun->addText(htmlspecialchars("    " . "    " . "a. Date of Delivery / Site Timeline: ", ENT_COMPAT, 'UTF-8'), array('font' => 'Verdana', 'size' => 11, 'Spacing'=> 5));
$textrun->addText(htmlspecialchars($date_of_delivery, ENT_COMPAT, 'UTF-8'), array('underline' => 'single', 'font' => 'Verdana', 'size' => 11, 'Spacing'=> 5));


$table->addRow();
$textrun = $table->addCell(10500, $styleCell)->addTextRun();
$textrun->addText(htmlspecialchars("    " . "    " . "b. Deadline with the Client: ", ENT_COMPAT, 'UTF-8'), array('font' => 'Verdana', 'size' => 11, 'Spacing'=> 5));
$textrun->addText(htmlspecialchars($client_deadline, ENT_COMPAT, 'UTF-8'), array('underline' => 'single', 'font' => 'Verdana', 'size' => 11, 'Spacing'=> 5));

$table->addRow();
$textrun = $table->addCell(10500, $styleCell)->addTextRun();
$textrun->addText(htmlspecialchars("", ENT_COMPAT, 'UTF-8'), array('bold' => true, 'font' => 'Verdana', 'size' => 11, 'Spacing'=> 5));

$table->addRow();
$textrun = $table->addCell(10500, $styleCell)->addTextRun();
$textrun->addText(htmlspecialchars("", ENT_COMPAT, 'UTF-8'), array('bold' => true, 'font' => 'Verdana', 'size' => 11, 'Spacing'=> 5));
$textrun->addText(htmlspecialchars("    " . "3. Scope", ENT_COMPAT, 'UTF-8'), array('bold' => true, 'size' => 12, 'font' => 'Verdana', 'Spacing'=> 5));


$table->addRow();
$textrun = $table->addCell(10500, $styleCell)->addTextRun();
$textrun->addText(htmlspecialchars("    " . "    " . "a. Delivery", ENT_COMPAT, 'UTF-8'), array('font' => 'Verdana', 'size' => 11, 'Spacing'=> 5));
$textrun->addText(htmlspecialchars("", ENT_COMPAT, 'UTF-8'), array('underline' => 'single', 'font' => 'Verdana', 'size' => 11, 'Spacing'=> 5));

// $table->addRow();
// $textrun = $table->addCell(10500, $styleCell)->addTextRun();
// $textrun->addText(htmlspecialchars("    " . "    " . "    " . "", ENT_COMPAT, 'UTF-8'), array('bold' => true, 'font' => 'Verdana', 'size' => 11, 'Spacing'=> 5));
// $textrun->addText(htmlspecialchars($delivery_1st_items, ENT_COMPAT, 'UTF-8'), array('bold' => false, 'font' => 'Verdana', 'size' => 11, 'Spacing'=> 5));
$table2 = $section->addTable('table', [
    'borderSize' => 6, 
    'borderColor' => '000000', 
    'afterSpacing' => 0, 
    'Spacing'=> 5, 
    'cellMargin'=> 0,
    'font' => 'Verdana',
    'size' => 11,
]);

$table2->addRow();
$table2->addCell(1000, $styleCell);
$cell = $table2->addCell(9500, $styleCell);
addMultiLineText($cell, $delivery_1st_items);


/*
$table->addRow();
$textrun = $table->addCell(10500, $styleCell)->addTextRun();
$textrun->addText(htmlspecialchars("    " . "    " . "    " . "", ENT_COMPAT, 'UTF-8'), array('bold' => true, 'font' => 'Verdana', 'size' => 11, 'Spacing'=> 5));
$textrun->addText(htmlspecialchars((($delivery_1st == '1' || $delivery_1st == 't') ? "■ 1st delivery: List of items with stock" : "□ 1st delivery: List of items with stock"), ENT_COMPAT, 'UTF-8'), array('bold' => false, 'font' => 'Verdana', 'size' => 11, 'Spacing'=> 5));


if($delivery_1st == '1' || $delivery_1st == 't')
{
    $table->addRow();
    $textrun = $table->addCell(10500, $styleCell)->addTextRun();
    $textrun->addText(htmlspecialchars("    " . "    " . "    " . "    " . "", ENT_COMPAT, 'UTF-8'), array('bold' => true, 'font' => 'Verdana', 'size' => 11, 'Spacing'=> 5));
    $textrun->addText(htmlspecialchars($delivery_1st_items, ENT_COMPAT, 'UTF-8'), array('underline' => 'single', 'font' => 'Verdana', 'size' => 11, 'Spacing'=> 5));
    
}

$table->addRow();
$textrun = $table->addCell(10500, $styleCell)->addTextRun();
$textrun->addText(htmlspecialchars("    " . "    " . "    " . "", ENT_COMPAT, 'UTF-8'), array('bold' => true, 'font' => 'Verdana', 'size' => 11, 'Spacing'=> 5));
$textrun->addText(htmlspecialchars((($delivery_2nd == '1' || $delivery_2nd == 't') ? "■ 2nd delivery onwards: List of indent items" : "□ 2nd delivery onwards: List of indent items"), ENT_COMPAT, 'UTF-8'), array('bold' => false, 'font' => 'Verdana', 'size' => 11, 'Spacing'=> 5));


if($delivery_2nd == '1' || $delivery_2nd == 't')
{
    $table->addRow();
    $textrun = $table->addCell(10500, $styleCell)->addTextRun();
    $textrun->addText(htmlspecialchars("    " . "    " . "    " . "    " . "", ENT_COMPAT, 'UTF-8'), array('bold' => true, 'font' => 'Verdana', 'size' => 11, 'Spacing'=> 5));
    $textrun->addText(htmlspecialchars($delivery_2nd_items, ENT_COMPAT, 'UTF-8'), array('underline' => 'single', 'font' => 'Verdana', 'size' => 11, 'Spacing'=> 5));
}

*/
$table3 = $section->addTable('table', [
    'borderSize' => 6, 
    'borderColor' => '000000', 
    'afterSpacing' => 0, 
    'Spacing'=> 5, 
    'cellMargin'=> 0,
    'font' => 'Verdana',
    'size' => 11,
]);

$table3->addRow();
$textrun = $table3->addCell(10500, $styleCell)->addTextRun();
$textrun->addText(htmlspecialchars("    " . "    " . "b. Installation", ENT_COMPAT, 'UTF-8'), array('font' => 'Verdana', 'size' => 11, 'Spacing'=> 5));
$textrun->addText(htmlspecialchars("", ENT_COMPAT, 'UTF-8'), array('underline' => 'single', 'font' => 'Verdana', 'size' => 11, 'Spacing'=> 5));

$table3->addRow();
$textrun = $table3->addCell(10500, $styleCell)->addTextRun();
$textrun->addText(htmlspecialchars("    " . "    " . "    " . "", ENT_COMPAT, 'UTF-8'), array('bold' => true, 'font' => 'Verdana', 'size' => 11, 'Spacing'=> 5));
$textrun->addText(htmlspecialchars((($os_delivery_only == '1' || $os_delivery_only == 't') ? "■ Office Systems Furniture: Delivery Only" : "□ Office Systems Furniture: Delivery Only"), ENT_COMPAT, 'UTF-8'), array('bold' => false, 'font' => 'Verdana', 'size' => 11, 'Spacing'=> 5));

$table3->addRow();
$textrun = $table3->addCell(10500, $styleCell)->addTextRun();
$textrun->addText(htmlspecialchars("    " . "    " . "    " . "", ENT_COMPAT, 'UTF-8'), array('bold' => true, 'font' => 'Verdana', 'size' => 11, 'Spacing'=> 5));
$textrun->addText(htmlspecialchars((($os_delivery_install == '1' || $os_delivery_install == 't') ? "■ Office Systems Furniture: Delivery and Install" : "□ Office Systems Furniture: Delivery and Install"), ENT_COMPAT, 'UTF-8'), array('bold' => false, 'font' => 'Verdana', 'size' => 11, 'Spacing'=> 5));

$table3->addRow();
$textrun = $table3->addCell(10500, $styleCell)->addTextRun();
$textrun->addText(htmlspecialchars("    " . "    " . "    " . "", ENT_COMPAT, 'UTF-8'), array('bold' => true, 'font' => 'Verdana', 'size' => 11, 'Spacing'=> 5));
$textrun->addText(htmlspecialchars((($lt_delivery_only == '1' || $lt_delivery_only == 't') ? "■ Lighting: Delivery Only" : "□ Lighting: Delivery Only"), ENT_COMPAT, 'UTF-8'), array('bold' => false, 'font' => 'Verdana', 'size' => 11, 'Spacing'=> 5));


$table3->addRow();
$textrun = $table3->addCell(10500, $styleCell)->addTextRun();
$textrun->addText(htmlspecialchars("    " . "    " . "    " . "", ENT_COMPAT, 'UTF-8'), array('bold' => true, 'font' => 'Verdana', 'size' => 11, 'Spacing'=> 5));
$textrun->addText(htmlspecialchars((($lt_delivery_install == '1' || $lt_delivery_install == 't') ? "■ Lighting: Delivery and Install" : "□ Lighting: Delivery and Install"), ENT_COMPAT, 'UTF-8'), array('bold' => false, 'font' => 'Verdana', 'size' => 11, 'Spacing'=> 5));

$table3->addRow();
$textrun = $table3->addCell(10500, $styleCell)->addTextRun();
$textrun->addText(htmlspecialchars("    " . "    " . "    " . "", ENT_COMPAT, 'UTF-8'), array('bold' => true, 'font' => 'Verdana', 'size' => 11, 'Spacing'=> 5));
$textrun->addText(htmlspecialchars((($delivery_install == '1' || $delivery_install == 't') ? "■ Decorative Lighting: Delivery and Install" : "□ Decorative Lighting: Delivery and Install"), ENT_COMPAT, 'UTF-8'), array('bold' => false, 'font' => 'Verdana', 'size' => 11, 'Spacing'=> 5));

$table3->addRow();
$textrun = $table3->addCell(10500, $styleCell)->addTextRun();
$textrun->addText(htmlspecialchars("    " . "    " . "c. Tagging of Product", ENT_COMPAT, 'UTF-8'), array('font' => 'Verdana', 'size' => 11, 'Spacing'=> 5));
$textrun->addText(htmlspecialchars("", ENT_COMPAT, 'UTF-8'), array('underline' => 'single', 'font' => 'Verdana', 'size' => 11, 'Spacing'=> 5));

$table3->addRow();
$textrun = $table3->addCell(10500, $styleCell)->addTextRun();
$textrun->addText(htmlspecialchars("    " . "    " . "    " . (($scope_attached_layout == '1' || $scope_attached_layout == 't') ? "■ Please refer to approved layout for the exact location of items (attach approved floor plan)" : "□ Please refer to approved layout for the exact location of items (attach approved floor plan)"), ENT_COMPAT, 'UTF-8'), array('bold' => false, 'font' => 'Verdana', 'size' => 11, 'Spacing'=> 5));
$textrun->addText(htmlspecialchars("", ENT_COMPAT, 'UTF-8'), array('underline' => 'single', 'font' => 'Verdana', 'size' => 11, 'Spacing'=> 5));


foreach($scope_attached_layout_files as $file)
{
    $table3->addRow();
    $table3->addCell(10500, $styleCell)->addText("    " . "    " . "    " . "    " . $file["name"], array('bold' => false, 'font' => 'Verdana', 'size' => 11, 'Spacing'=> 5));
    
}

$table3->addRow();
$textrun = $table3->addCell(10500, $styleCell)->addTextRun();
$textrun->addText(htmlspecialchars("", ENT_COMPAT, 'UTF-8'), array('bold' => true, 'font' => 'Verdana', 'size' => 11, 'Spacing'=> 5));

$table3->addRow();
$textrun = $table3->addCell(10500, $styleCell)->addTextRun();
$textrun->addText(htmlspecialchars("", ENT_COMPAT, 'UTF-8'), array('bold' => true, 'font' => 'Verdana', 'size' => 11, 'Spacing'=> 5));
$textrun->addText(htmlspecialchars("    " . "    " . "4. 3rd Party Contractor", ENT_COMPAT, 'UTF-8'), array('bold' => true, 'size' => 12, 'font' => 'Verdana', 'Spacing'=> 5));

$table3->addRow();
$textrun = $table3->addCell(10500, $styleCell)->addTextRun();
$textrun->addText(htmlspecialchars("    " . "    " . "    " . (($timeline_check == '1' || $timeline_check == 't') ? "■ Timeline: " : "□ Timeline"), ENT_COMPAT, 'UTF-8'), array('font' => 'Verdana', 'size' => 11, 'Spacing'=> 5));
$textrun->addText(htmlspecialchars($timeline, ENT_COMPAT, 'UTF-8'), array('underline' => 'single', 'font' => 'Verdana', 'size' => 11, 'Spacing'=> 5));



if($category_id == "1")
{
    $table3->addRow();
    $textrun = $table3->addCell(10500, $styleCell)->addTextRun();
    $textrun->addText(htmlspecialchars("    " . "    " . "    " . (($data_check == '1' || $data_check == 't') ? "■ Data: " : "□ Data"), ENT_COMPAT, 'UTF-8'), array('font' => 'Verdana', 'size' => 11, 'Spacing'=> 5));
    $textrun->addText(htmlspecialchars($data, ENT_COMPAT, 'UTF-8'), array('underline' => 'single', 'font' => 'Verdana', 'size' => 11, 'Spacing'=> 5));


    $table3->addRow();
    $textrun = $table3->addCell(10500, $styleCell)->addTextRun();
    $textrun->addText(htmlspecialchars("    " . "    " . "    " . (($electrical_check == '1' || $electrical_check == 't') ? "■ Electrical: " : "□ Electrical"), ENT_COMPAT, 'UTF-8'), array('font' => 'Verdana', 'size' => 11, 'Spacing'=> 5));
    $textrun->addText(htmlspecialchars($electrical, ENT_COMPAT, 'UTF-8'), array('underline' => 'single', 'font' => 'Verdana', 'size' => 11, 'Spacing'=> 5));


    $table3->addRow();
    $textrun = $table3->addCell(10500, $styleCell)->addTextRun();
    $textrun->addText(htmlspecialchars("    " . "    " . "    " . (($flooring_check == '1' || $flooring_check == 't') ? "■ Flooring: " : "□ Flooring"), ENT_COMPAT, 'UTF-8'), array('font' => 'Verdana', 'size' => 11, 'Spacing'=> 5));
    $textrun->addText(htmlspecialchars($flooring, ENT_COMPAT, 'UTF-8'), array('underline' => 'single', 'font' => 'Verdana', 'size' => 11, 'Spacing'=> 5));


}

if($category_id == "2")
{
    $table3->addRow();
    $textrun = $table3->addCell(10500, $styleCell)->addTextRun();
    $textrun->addText(htmlspecialchars("    " . "    " . "    " . "Type and Ceiling Height: ", ENT_COMPAT, 'UTF-8'), array('font' => 'Verdana', 'size' => 11, 'Spacing'=> 5));
    $textrun->addText(htmlspecialchars($type_and_ceiling, ENT_COMPAT, 'UTF-8'), array('underline' => 'single', 'font' => 'Verdana', 'size' => 11, 'Spacing'=> 5));


    $table3->addRow();
    $textrun = $table3->addCell(10500, $styleCell)->addTextRun();
    $textrun->addText(htmlspecialchars("    " . "    " . "    " . (($painting_check == '1' || $painting_check == 't') ? "■ Painting: " : "□ Painting"), ENT_COMPAT, 'UTF-8'), array('font' => 'Verdana', 'size' => 11, 'Spacing'=> 5));
    $textrun->addText(htmlspecialchars($painting, ENT_COMPAT, 'UTF-8'), array('underline' => 'single', 'font' => 'Verdana', 'size' => 11, 'Spacing'=> 5));


    $table3->addRow();
    $textrun = $table3->addCell(10500, $styleCell)->addTextRun();
    $textrun->addText(htmlspecialchars("    " . "    " . "    " . (($ceiling_electrical_check == '1' || $ceiling_electrical_check == 't') ? "■ Electrical: " : "□ Electrical"), ENT_COMPAT, 'UTF-8'), array('font' => 'Verdana', 'size' => 11, 'Spacing'=> 5));
    $textrun->addText(htmlspecialchars($ceiling_electrical, ENT_COMPAT, 'UTF-8'), array('underline' => 'single', 'font' => 'Verdana', 'size' => 11, 'Spacing'=> 5));

}

$table3->addRow();
$textrun = $table3->addCell(10500, $styleCell)->addTextRun();
$textrun->addText(htmlspecialchars("", ENT_COMPAT, 'UTF-8'), array('bold' => true, 'font' => 'Verdana', 'size' => 11, 'Spacing'=> 5));

$table3->addRow();
$textrun = $table3->addCell(10500, $styleCell)->addTextRun();
$textrun->addText(htmlspecialchars("    " . "    " . "", ENT_COMPAT, 'UTF-8'), array('bold' => true, 'font' => 'Verdana', 'size' => 11, 'Spacing'=> 5));
$textrun->addText(htmlspecialchars("5. Outsourcing c/o Admin (if needed)", ENT_COMPAT, 'UTF-8'), array('bold' => true, 'size' => 12, 'font' => 'Verdana', 'Spacing'=> 5));


$table3->addRow();
$textrun = $table3->addCell(10500, $styleCell)->addTextRun();
$textrun->addText(htmlspecialchars("    " . "    " . "    " . (($manpower_check == '1' || $manpower_check == 't') ? "■ Manpower: " : "□ Manpower"), ENT_COMPAT, 'UTF-8'), array('font' => 'Verdana', 'size' => 11, 'Spacing'=> 5));
$textrun->addText(htmlspecialchars($manpower, ENT_COMPAT, 'UTF-8'), array('underline' => 'single', 'font' => 'Verdana', 'size' => 11, 'Spacing'=> 5));


$table3->addRow();
$textrun = $table3->addCell(10500, $styleCell)->addTextRun();
$textrun->addText(htmlspecialchars("    " . "    " . "    " . (($materials_check == '1' || $materials_check == 't') ? "■ Materials: " : "□ Materials"), ENT_COMPAT, 'UTF-8'), array('font' => 'Verdana', 'size' => 11, 'Spacing'=> 5));
$textrun->addText(htmlspecialchars($materials, ENT_COMPAT, 'UTF-8'), array('underline' => 'single', 'font' => 'Verdana', 'size' => 11, 'Spacing'=> 5));


$table3->addRow();
$textrun = $table3->addCell(10500, $styleCell)->addTextRun();
$textrun->addText(htmlspecialchars("    " . "    " . "    " . (($trucking_services == '1' || $trucking_services == 't') ? "■ Trucking Services: " : "□ Trucking Services"), ENT_COMPAT, 'UTF-8'), array('font' => 'Verdana', 'size' => 11, 'Spacing'=> 5));
$textrun->addText(htmlspecialchars("", ENT_COMPAT, 'UTF-8'), array('underline' => 'single', 'font' => 'Verdana', 'size' => 11, 'Spacing'=> 5));

$table3->addRow();
$textrun = $table3->addCell(10500, $styleCell)->addTextRun();
$textrun->addText(htmlspecialchars("    " . "    " . "    " . (($purchasing_of_special_products_check == '1' || $purchasing_of_special_products_check == 't') ? "■ Purchasing of Special Products: " : "□ Purchasing of Special Products"), ENT_COMPAT, 'UTF-8'), array('font' => 'Verdana', 'size' => 11, 'Spacing'=> 5));
$textrun->addText(htmlspecialchars($purchasing_of_special_products, ENT_COMPAT, 'UTF-8'), array('underline' => 'single', 'font' => 'Verdana', 'size' => 11, 'Spacing'=> 5));

$table3->addRow();
$textrun = $table3->addCell(10500, $styleCell)->addTextRun();
$textrun->addText(htmlspecialchars("    " . "    " . "    " . (($tools_check == '1' || $tools_check == 't') ? "■ Tools: " : "□ Tools"), ENT_COMPAT, 'UTF-8'), array('font' => 'Verdana', 'size' => 11, 'Spacing'=> 5));
$textrun->addText(htmlspecialchars($tools, ENT_COMPAT, 'UTF-8'), array('underline' => 'single', 'font' => 'Verdana', 'size' => 11, 'Spacing'=> 5));

$table3->addRow();
$textrun = $table3->addCell(10500, $styleCell)->addTextRun();
$textrun->addText(htmlspecialchars("", ENT_COMPAT, 'UTF-8'), array('bold' => true, 'font' => 'Verdana', 'size' => 11, 'Spacing'=> 5));

if(count($other_attached_layout_files))
{
    $table3->addRow();
    $textrun = $table3->addCell(10500, $styleCell)->addTextRun();
    $textrun->addText(htmlspecialchars("    " . "    " . "", ENT_COMPAT, 'UTF-8'), array('bold' => true, 'font' => 'Verdana', 'size' => 11, 'Spacing'=> 5));
    $textrun->addText(htmlspecialchars("6. Others", ENT_COMPAT, 'UTF-8'), array('bold' => true, 'size' => 12, 'font' => 'Verdana', 'Spacing'=> 5));

}

foreach($other_attached_layout_files as $file)
{
    $table3->addRow();
    $table3->addCell(10500, $styleCell)->addText("    " . "    " . "    " . $file["name"], array('bold' => false, 'font' => 'Verdana', 'size' => 11, 'Spacing'=> 5));
    
}



// Adding Text element with font customized using explicitly created font style object...
$fontStyle = new \PhpOffice\PhpWord\Style\Font();
$fontStyle->setBold(true);
$fontStyle->setName('Tahoma');
$fontStyle->setSize(13);
// $myTextElement = $section->addText('"Believe you can and you\'re halfway there." (Theodor Roosevelt)');
//$myTextElement->setFontStyle($fontStyle);

ob_end_clean();
// Saving the document as OOXML file...
$objWriter = \PhpOffice\PhpWord\IOFactory::createWriter($phpWord, 'Word2007', $download = true);

    header("Content-Disposition: attachment; filename='PROJECT TURNOVER CHECKLIST'.docx");

    header('Cache-Control: max-age=0');
    // If you're serving to IE 9, then the following may be needed
    header('Cache-Control: max-age=1');
    // If you're serving to IE over SSL, then the following may be needed
    header('Expires: Mon, 26 Jul 1997 05:00:00 GMT'); // Date in the past
    header('Last-Modified: ' . gmdate('D, d M Y H:i:s') . ' GMT'); // always modified
    header('Cache-Control: cache, must-revalidate'); // HTTP/1.1
    header('Pragma: public'); // HTTP/1.0

    $objWriter->save('php://output');


    
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

function addMultiLineText($cell, $text)
    {
        // break from line breaks
        $strArr = explode("\n", $text);

        // add text line together
        foreach ($strArr as $v) {
            $cell->addText($v);
        }
       
    }


?>
