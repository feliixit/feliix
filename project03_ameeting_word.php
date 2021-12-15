<?php
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
        $other_attached_layout_flies = [];

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

            $other_attached_layout_flies = GetAttachment($id, "other_attached_layout", $db);
            

        }

// Creating the new document...
$phpWord = new PhpOffice\PhpWord\PhpWord();

/* Note: any element you append to a document must reside inside of a Section. */

// Adding an empty Section to the document...
$section = $phpWord->addSection();
// Adding Text element to the Section having font styled by default...
$section->addText("");

$section->addText("");

$table = $section->addTable('table', [
    'borderSize' => 6, 
    'borderColor' => 'F73605', 
    'afterSpacing' => 0, 
    'Spacing'=> 0, 
    'cellMargin'=> 0
]);

$table->addRow();
$table->addCell(2000, ['borderSize' => 6])->addText("0. General Information", array('bold' => true));
$table->addCell(8500, ['borderSize' => 6])->addText("");

$table->addRow();
$table->addCell(2000, ['borderSize' => 6])->addText("Project Name", array('bold' => true));
$table->addCell(8500, ['borderSize' => 6])->addText($project_name);

$table->addRow();
$table->addCell(2000, ['borderSize' => 6])->addText("Project Category", array('bold' => true));
$table->addCell(8500, ['borderSize' => 6])->addText($category);

$table->addRow();
$table->addCell(2000, ['borderSize' => 6])->addText("Date of Down payment", array('bold' => true));
$table->addCell(8500, ['borderSize' => 6])->addText($down_payment_date);

$table->addRow();
$table->addCell(2000, ['borderSize' => 6])->addText("Account Executive", array('bold' => true));
$table->addCell(8500, ['borderSize' => 6])->addText($account_executive);

$table->addRow();
$table->addCell(2000, ['borderSize' => 6])->addText("PIC", array('bold' => true));
$table->addCell(8500, ['borderSize' => 6])->addText($pic);

$table->addRow();
$table->addCell(2000, ['borderSize' => 6])->addText("Last Updated", array('bold' => true));
$table->addCell(8500, ['borderSize' => 6])->addText($updator != "" ? $updator . " at " . $updated_at : "");


$table->addRow();
$table->addCell(2000, ['borderSize' => 6])->addText("1. Project Details:", array('bold' => true));
$table->addCell(8500, ['borderSize' => 6])->addText("");

$table->addRow();
$table->addCell(2000, ['borderSize' => 6])->addText("Quotation #", array('bold' => true));
$table->addCell(8500, ['borderSize' => 6])->addText($quotation);

$table->addRow();
$table->addCell(2000, ['borderSize' => 6])->addText("a. Client Name", array('bold' => true));
$table->addCell(8500, ['borderSize' => 6])->addText($client_name);

$table->addRow();
$table->addCell(2000, ['borderSize' => 6])->addText("b. Contact Person", array('bold' => true));
$table->addCell(8500, ['borderSize' => 6])->addText($contact_person);

$table->addRow();
$table->addCell(2000, ['borderSize' => 6])->addText("c. Contact Number", array('bold' => true));
$table->addCell(8500, ['borderSize' => 6])->addText($contact_number);

$table->addRow();
$table->addCell(2000, ['borderSize' => 6])->addText("d. Delivery Address", array('bold' => true));
$table->addCell(8500, ['borderSize' => 6])->addText((($delivery_address_within == '1' || $delivery_address_within == 't') ? "Within Metro Manila" : "") . " " . $delivery_address_outside == '1' || $delivery_address_outside == 't' ? "Outside Metro Manila" : "");

$table->addRow();
$table->addCell(2000, ['borderSize' => 6])->addText("e. Exact Delivery Address", array('bold' => true));
$table->addCell(8500, ['borderSize' => 6])->addText($exact_delivery_address);

$table->addRow();
$table->addCell(2000, ['borderSize' => 6])->addText("f. Detailed Delivery and Installation location ( Area / Floor / Department / Room Number )", array('bold' => true));
$table->addCell(8500, ['borderSize' => 6])->addText($detail_delivery_address);

$table->addRow();
$table->addCell(2000, ['borderSize' => 6])->addText("g. Permit Processing", array('bold' => true));
$table->addCell(8500, ['borderSize' => 6])->addText((($delivery_permit == '1' || $delivery_permit == 't') ? "Delivery Permit" : "") . " " . $work_permit == '1' || $work_permit == 't' ? "Work Permit (for Delivery and Install Projects)" : "");

$table->addRow();
$table->addCell(2000, ['borderSize' => 6])->addText("g. Permit Processing notes", array('bold' => true));
$table->addCell(8500, ['borderSize' => 6])->addText($permit_processing_note);


$table->addRow();
$table->addCell(2000, ['borderSize' => 6])->addText("h. Other Client Concern / Request", array('bold' => true));
$table->addCell(8500, ['borderSize' => 6])->addText($other_request);


$table->addRow();
$table->addCell(2000, ['borderSize' => 6])->addText("2. Delivery Schedule", array('bold' => true));
$table->addCell(8500, ['borderSize' => 6])->addText("");

$table->addRow();
$table->addCell(2000, ['borderSize' => 6])->addText("a. Date of Delivery / Site Timeline", array('bold' => true));
$table->addCell(8500, ['borderSize' => 6])->addText($date_of_delivery);

$table->addRow();
$table->addCell(2000, ['borderSize' => 6])->addText("b. Deadline with the Client", array('bold' => true));
$table->addCell(8500, ['borderSize' => 6])->addText($client_deadline);


$table->addRow();
$table->addCell(2000, ['borderSize' => 6])->addText("3. Scope", array('bold' => true));
$table->addCell(8500, ['borderSize' => 6])->addText("");

$table->addRow();
$table->addCell(2000, ['borderSize' => 6])->addText("a. Delivery:1st delivery: List of items with stock", array('bold' => true));
$table->addCell(8500, ['borderSize' => 6])->addText($delivery_1st_items);

$table->addRow();
$table->addCell(2000, ['borderSize' => 6])->addText("a. Delivery:2nd delivery onwards: List of indent items", array('bold' => true));
$table->addCell(8500, ['borderSize' => 6])->addText($delivery_2nd_items);


$table->addRow();
$table->addCell(2000, ['borderSize' => 6])->addText("b. Installation", array('bold' => true));
$cell = $table->addCell(8500, ['borderSize' => 6]);
$content = "";
$content .= $os_delivery_only == "1" || $os_delivery_only == "t" ? "Office Systems Furniture: Delivery Only\n" : "";
$content .= $os_delivery_install == "1" || $os_delivery_install == "t" ? "Office Systems Furniture: Delivery and Install\n" : "";
$content .= $lt_delivery_only == "1" || $lt_delivery_only == "t" ? "Lighting: Delivery Only\n" : "";
$content .= $lt_delivery_install == "1" || $lt_delivery_install == "t" ? "Lighting: Delivery and Install\n" : "";
$content .= $delivery_install == "1" || $delivery_install == "t" ? "Decorative Lighting: Delivery and Install\n" : "";
addMultiLineText($cell, $content);

$table->addRow();
$table->addCell(2000, ['borderSize' => 6])->addText("c. Tagging of Products", array('bold' => true));
$content = "";
$content .= $scope_attached_layout == "1" || $scope_attached_layout == "t" ? "See attached approved furniture layout / lighting layout" : "";
$table->addCell(8500, ['borderSize' => 6])->addText($content);



$table->addRow();
$table->addCell(2000, ['borderSize' => 6])->addText("4. 3rd Party Contractor", array('bold' => true));
$table->addCell(8500, ['borderSize' => 6])->addText("");

$table->addRow();
$table->addCell(2000, ['borderSize' => 6])->addText("Timeline", array('bold' => true));
$content = "";
$content .= $timeline_check == "1" || $timeline_check == "t" ? $timeline : "";
$table->addCell(8500, ['borderSize' => 6])->addText($content);

if($category_id == "1")
{
    $table->addRow();
    $table->addCell(2000, ['borderSize' => 6])->addText("Data", array('bold' => true));
    $content = "";
    $content .= $data_check == "1" || $data_check == "t" ? $data : "";
    $table->addCell(8500, ['borderSize' => 6])->addText($content);

    $table->addRow();
    $table->addCell(2000, ['borderSize' => 6])->addText("Electrical", array('bold' => true));
    $content = "";
    $content .= $electrical_check == "1" || $electrical_check == "t" ? $electrical : "";
    $table->addCell(8500, ['borderSize' => 6])->addText($content);

    $table->addRow();
    $table->addCell(2000, ['borderSize' => 6])->addText("Flooring", array('bold' => true));
    $content = "";
    $content .= $flooring_check == "1" || $flooring_check == "t" ? $flooring : "";
    $table->addCell(8500, ['borderSize' => 6])->addText($content);

}

if($category_id == "2")
{
    $table->addRow();
    $table->addCell(2000, ['borderSize' => 6])->addText("Type and Ceiling Height", array('bold' => true));
    $table->addCell(8500, ['borderSize' => 6])->addText($type_and_ceiling);

    $table->addRow();
    $table->addCell(2000, ['borderSize' => 6])->addText("Painting", array('bold' => true));
    $content = "";
    $content .= $painting_check == "1" || $painting_check == "t" ? $painting : "";
    $table->addCell(8500, ['borderSize' => 6])->addText($content);

    $table->addRow();
    $table->addCell(2000, ['borderSize' => 6])->addText("Electrical", array('bold' => true));
    $content = "";
    $content .= $ceiling_electrical_check == "1" || $ceiling_electrical_check == "t" ? $ceiling_electrical : "";
    $table->addCell(8500, ['borderSize' => 6])->addText($content);
}


$table->addRow();
$table->addCell(2000, ['borderSize' => 6])->addText("5. Outsourcing c/o Admin (if needed)", array('bold' => true));
$table->addCell(8500, ['borderSize' => 6])->addText("");

$table->addRow();
$table->addCell(2000, ['borderSize' => 6])->addText("Manpower", array('bold' => true));
$content = "";
$content .= $manpower_check == "1" || $manpower_check == "t" ? $manpower : "";
$table->addCell(8500, ['borderSize' => 6])->addText($content);

$table->addRow();
$table->addCell(2000, ['borderSize' => 6])->addText("Materials", array('bold' => true));
$content = "";
$content .= $materials_check == "1" || $materials_check == "t" ? $materials : "";
$table->addCell(8500, ['borderSize' => 6])->addText($content);

$table->addRow();
$table->addCell(2000, ['borderSize' => 6])->addText("Trucking Services", array('bold' => true));
$content = "";
$content .= $trucking_services == "1" || $trucking_services == "t" ? "Trucking Services" : "";
$table->addCell(8500, ['borderSize' => 6])->addText($content);

$table->addRow();
$table->addCell(2000, ['borderSize' => 6])->addText("Purchasing of Special Products", array('bold' => true));
$content = "";
$content .= $purchasing_of_special_products_check == "1" || $purchasing_of_special_products_check == "t" ? $purchasing_of_special_products : "";
$table->addCell(8500, ['borderSize' => 6])->addText($content);

$table->addRow();
$table->addCell(2000, ['borderSize' => 6])->addText("Tools", array('bold' => true));
$content = "";
$content .= $tools_check == "1" || $tools_check == "t" ? $tools : "";
$table->addCell(8500, ['borderSize' => 6])->addText($content);



// Adding Text element with font customized using explicitly created font style object...
$fontStyle = new \PhpOffice\PhpWord\Style\Font();
$fontStyle->setBold(true);
$fontStyle->setName('Tahoma');
$fontStyle->setSize(13);
// $myTextElement = $section->addText('"Believe you can and you\'re halfway there." (Theodor Roosevelt)');
//$myTextElement->setFontStyle($fontStyle);

// Saving the document as OOXML file...
$objWriter = \PhpOffice\PhpWord\IOFactory::createWriter($phpWord, 'Word2007', $download = true);

    header("Content-Disposition: attachment; filename=project03_a_meeting.docx");

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
