<?php
ob_start();
error_reporting(E_ALL);

include_once 'config/database.php';
include_once 'config/conf.php';

require '../vendor/autoload.php';

include_once 'config/core.php';
include_once 'libs/php-jwt-master/src/BeforeValidException.php';
include_once 'libs/php-jwt-master/src/ExpiredException.php';
include_once 'libs/php-jwt-master/src/SignatureInvalidException.php';
include_once 'libs/php-jwt-master/src/JWT.php';

use \Firebase\JWT\JWT;

use PhpOffice\PhpWord\IOFactory;

try {
    $jwt = (isset($_COOKIE['jwt']) ?  $_COOKIE['jwt'] : null);
    // decode jwt
    $decoded = JWT::decode($jwt, $key, array('HS256'));

    $user_name = $decoded->data->username;

    //if(passport_decrypt( base64_decode($uid)) !== $decoded->data->username )
    //    header( 'location:index.php' );
}
// if decode fails, it means jwt is invalid
catch (Exception $e) {

    die();
}

$database = new Database();
$db = $database->getConnection();

$id = (isset($_POST['id']) ?  $_POST['id'] : 0);
// $id = 4;

$sql = "SELECT  pm.id,
pm.uid,
request_no, 
DATE_FORMAT(pm.date_requested, '%Y/%m/%d') date_requested,
reason,
listing,
remarks,
pm.`status`,
p.username,
DATE_FORMAT(pm.created_at, '%Y/%m/%d %T') created_at
from apply_for_office_item pm 
LEFT JOIN user p ON p.id = pm.uid where pm.id = " . $id;

$stmt = $db->prepare($sql);
$stmt->execute();

$id = 0;
$request_no = "";
$date_requested = "";
$reason = "";
$listing = "";
$remarks = "";
$status = "";
$requestor = "";
$created_at = "";

$list = [];
$attachment = [];


while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
    $id = $row['id'];
    $request_no = $row['request_no'];
    $date_requested = $row['date_requested'];
    $reason = $row['reason'];
    $listing = $row['listing'];
    $remarks = $row['remarks'];
    $status = $row['status'];
    $requestor = $row['username'];
    $created_at = $row['created_at'];
    
    $desc = GetStatus($row['status']);
    $attachment = GetAttachment($id, $db);
    $history = GetHistory($id, $db);

    $list = JSON_decode($row['listing'], true);
    $list = UpdateQty($list, $db);

    $merged_results[] = array(
        "id" => $id,
        "request_no" => $request_no,
        "date_requested" => $date_requested,
        "reason" => $reason,
        "listing" => $listing,
        "remarks" => $remarks,
        "status" => $status,
        "desc" => $desc,
        "attachment" => $attachment,
        "history" => $history,
        "requestor" => $requestor,
        "created_at" => $created_at,
        "list" => $list
    );

}



// Creating the new document...
$phpWord = new PhpOffice\PhpWord\PhpWord();

/* Note: any element you append to a document must reside inside of a Section. */

// Adding an empty Section to the document...
$section = $phpWord->addSection();
// Adding Text element to the Section having font styled by default...

$table = $section->addTable('table', [
    'borderSize' => 6,
    'borderColor' => 'F73605',
    'afterSpacing' => 0,
    'Spacing' => 0,
    'cellMargin' => 0,
    'align' => 'center'
]);

$table->addRow();
$table->addCell(3000, ['borderSize' => 6, 'bgColor' => 'EFEFEF'])->addText("Request No.", ['bold' => true], ['align' => \PhpOffice\PhpWord\Style\Cell::VALIGN_CENTER]);
$table->addCell(7500, ['borderSize' => 6, 'bgColor' => 'EFEFEF'])->addText($request_no, ['bold' => true], ['align' => \PhpOffice\PhpWord\Style\Cell::VALIGN_CENTER]);


$table->addRow();
$table->addCell(3000, ['borderSize' => 6])->addText("Application Time", ['bold' => false], ['align' => \PhpOffice\PhpWord\Style\Cell::VALIGN_CENTER]);
$table->addCell(7500, ['borderSize' => 6])->addText($created_at, ['bold' => false], ['align' => \PhpOffice\PhpWord\Style\Cell::VALIGN_CENTER]);

$table->addRow();
$table->addCell(3000, ['borderSize' => 6])->addText("Requestor", ['bold' => false], ['align' => \PhpOffice\PhpWord\Style\Cell::VALIGN_CENTER]);
$table->addCell(7500, ['borderSize' => 6])->addText(htmlspecialchars($requestor), ['bold' => false], ['align' => \PhpOffice\PhpWord\Style\Cell::VALIGN_CENTER]);

$table->addRow();
$table->addCell(3000, ['borderSize' => 6])->addText("Status", ['bold' => false], ['align' => \PhpOffice\PhpWord\Style\Cell::VALIGN_CENTER]);
$table->addCell(7500, ['borderSize' => 6])->addText(htmlspecialchars($desc), ['bold' => false], ['align' => \PhpOffice\PhpWord\Style\Cell::VALIGN_CENTER]);

$table->addRow();
$table->addCell(3000, ['borderSize' => 6])->addText("Processing History", ['bold' => false], ['align' => \PhpOffice\PhpWord\SimpleType\Jc::CENTER, 'valign' => \PhpOffice\PhpWord\SimpleType\Jc::BOTH]);
$cell = $table->addCell(7500, ['borderSize' => 6]);
addMultiLineText($cell, $history);

$table->addRow();
$table->addCell(3000, ['borderSize' => 6])->addText("Date Needed", ['bold' => false], ['align' => \PhpOffice\PhpWord\Style\Cell::VALIGN_CENTER]);
$table->addCell(7500, ['borderSize' => 6])->addText($date_requested, ['bold' => false], ['align' => \PhpOffice\PhpWord\Style\Cell::VALIGN_CENTER]);

$table->addRow();
$table->addCell(3000, ['borderSize' => 6])->addText("Reason", ['bold' => false], ['align' => \PhpOffice\PhpWord\Style\Cell::VALIGN_CENTER]);
$table->addCell(7500, ['borderSize' => 6])->addText($reason, ['bold' => false], ['align' => \PhpOffice\PhpWord\Style\Cell::VALIGN_CENTER]);


$table->addRow();
$table->addCell(3000, ['borderSize' => 6])->addText("Attachments", ['bold' => false], ['align' => \PhpOffice\PhpWord\Style\Cell::VALIGN_CENTER]);
$cell = $table->addCell(7500, ['borderSize' => 6]);
addMultiLineAttach($cell, $attachment);

$table->addRow();
$table->addCell(3000, ['borderSize' => 6])->addText("Remarks", ['bold' => false], ['align' => \PhpOffice\PhpWord\Style\Cell::VALIGN_CENTER]);
$table->addCell(7500, ['borderSize' => 6])->addText(htmlspecialchars($remarks), ['bold' => false], ['align' => \PhpOffice\PhpWord\Style\Cell::VALIGN_CENTER]);

$section->addText("");


$table1 = $section->addTable('table1', [
    'borderSize' => 6,
    'borderColor' => 'F73605',
    'afterSpacing' => 0,
    'Spacing' => 0,
    'cellMargin' => 0,
    'textAlign' => 'center'
]);

$table1->addRow();
$table1->addCell(2500, ['borderSize' => 6, 'bgColor' => 'EFEFEF'])->addText("Code", ['bold' => true], ['align' => \PhpOffice\PhpWord\Style\Cell::VALIGN_CENTER]);
$table1->addCell(2500, ['borderSize' => 6, 'bgColor' => 'EFEFEF'])->addText("Image", ['bold' => true], ['align' => \PhpOffice\PhpWord\Style\Cell::VALIGN_CENTER]);
$table1->addCell(5000, ['borderSize' => 6, 'bgColor' => 'EFEFEF'])->addText("Particulars",  ['bold' => true], ['align' => \PhpOffice\PhpWord\Style\Cell::VALIGN_CENTER]);
$table1->addCell(1000, ['borderSize' => 6, 'bgColor' => 'EFEFEF'])->addText("Needed Qty",  ['bold' => true], ['align' => \PhpOffice\PhpWord\Style\Cell::VALIGN_CENTER]);
$table1->addCell(1000, ['borderSize' => 6, 'bgColor' => 'EFEFEF'])->addText("Stock Status",  ['bold' => true], ['align' => \PhpOffice\PhpWord\Style\Cell::VALIGN_CENTER]);

foreach ($list as &$value) {
    $url = str_replace(' ', '%20', $value['url']);
    $table1->addRow();
    $table1->addCell(2600, ['borderSize' => 6])->addText(htmlspecialchars($value['code1'] . $value['code2'] . $value['code3'] . $value['code4']), [], ['align' => \PhpOffice\PhpWord\Style\Cell::VALIGN_CENTER]);
    if($url != "")
        $table1->addCell(2600, ['borderSize' => 6])->addImage($url, ['width' => 50, 'height' => 50, 'wrappingStyle' => 'inline']);
    else
        $table1->addCell(2600, ['borderSize' => 6])->addText("");
    $table1->addCell(6100, ['borderSize' => 6])->addText(htmlspecialchars($value['cat1'] . " >> " . $value['cat2'] . " >> " . $value['cat3'] . " >> " . $value['cat4']), [], ['align' => \PhpOffice\PhpWord\Style\Cell::VALIGN_CENTER]);
    $table1->addCell(600, ['borderSize' => 6])->addText(number_format($value['amount']), [], ['align' => \PhpOffice\PhpWord\Style\Cell::VALIGN_CENTER]);
    $table1->addCell(600, ['borderSize' => 6])->addText(number_format($value['qty']) . "\n (Reserved: " . number_format($value['reserve_qty']). ")", [], ['align' => \PhpOffice\PhpWord\Style\Cell::VALIGN_CENTER]);
}


// $section->addText("");
// $section->addText("Additional Info");

// $table2 = $section->addTable('table2', [
//     'borderSize' => 6,
//     'borderColor' => 'F73605',
//     'afterSpacing' => 0,
//     'Spacing' => 0,
//     'cellMargin' => 0,
//     'textAlign' => 'center'
// ]);

// $table2->addRow();
// $table2->addCell(5250, ['borderSize' => 6])->addText("Account", [], ['align' => \PhpOffice\PhpWord\Style\Cell::VALIGN_CENTER]);
// $table2->addCell(5250, ['borderSize' => 6])->addText(htmlspecialchars($info_account),  [], ['align' => \PhpOffice\PhpWord\Style\Cell::VALIGN_CENTER]);

// $table2->addRow();
// $table2->addCell(5250, ['borderSize' => 6])->addText("Category", [], ['align' => \PhpOffice\PhpWord\Style\Cell::VALIGN_CENTER]);
// $table2->addCell(5250, ['borderSize' => 6])->addText(htmlspecialchars($info_category) . (($info_sub_category != '') ? ' >> ' . htmlspecialchars($info_sub_category) : ''),  [], ['align' => \PhpOffice\PhpWord\Style\Cell::VALIGN_CENTER]);

// $table2->addRow();
// $table2->addCell(5250, ['borderSize' => 6])->addText("Remarks or Payment Instructions", [], ['align' => \PhpOffice\PhpWord\Style\Cell::VALIGN_CENTER]);
// $table2->addCell(5250, ['borderSize' => 6])->addText(htmlspecialchars($info_remark) . (($info_remark_other != '') ? ' : ' . $info_remark_other : ''),  [], ['align' => \PhpOffice\PhpWord\Style\Cell::VALIGN_CENTER]);


$section->addText("");

$table3 = $section->addTable('table3', [
    'borderSize' => 'none',
    'borderColor' => 'ffffff',
    'afterSpacing' => 0,
    'Spacing' => 0,
    'cellMargin' => 0,
    'textAlign' => 'left'
]);

$today = date("Y/m/d");

$table3->addRow();
$table3->addCell(5250, ['borderSize' => 'none'])->addText("Date Released:", [], []);
$table3->addCell(5250, ['borderSize' => 'none'])->addText("Date",  [], []);

$table3->addRow();
$table3->addCell(5250, ['borderSize' => 'none'])->addText($today, [], []);
$table3->addCell(5250, ['borderSize' => 'none'])->addText("Received:________________________",  [], []);

$table3->addRow();
$table3->addCell(5250, ['borderSize' => 'none'])->addText("", [], []);
$table3->addCell(5250, ['borderSize' => 'none'])->addText("",  [], []);

$table3->addRow();
$table3->addCell(5250, ['borderSize' => 'none'])->addText("", [], []);
$table3->addCell(5250, ['borderSize' => 'none'])->addText("",  [], []);

$table3->addRow();
$table3->addCell(5250, ['borderSize' => 'none'])->addText("", [], []);
$table3->addCell(5250, ['borderSize' => 'none'])->addText("",  [], []);

$table3->addRow();
$table3->addCell(5250, ['borderSize' => 'none'])->addText("_________________________________", [], []);
$table3->addCell(5250, ['borderSize' => 'none'])->addText("_________________________________",  [], []);

$table3->addRow();
$table3->addCell(5250, ['borderSize' => 'none'])->addText("Released by " . $user_name, [], []);
$table3->addCell(5250, ['borderSize' => 'none'])->addText("Received by " . $requestor,  [], []);


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


header("Content-Disposition: attachment; filename=schedule.docx");

header('Cache-Control: max-age=0');
// If you're serving to IE 9, then the following may be needed
header('Cache-Control: max-age=1');
// If you're serving to IE over SSL, then the following may be needed
header('Expires: Mon, 26 Jul 1997 05:00:00 GMT'); // Date in the past
header('Last-Modified: ' . gmdate('D, d M Y H:i:s') . ' GMT'); // always modified
header('Cache-Control: cache, must-revalidate'); // HTTP/1.1
header('Pragma: public'); // HTTP/1.0

$objWriter->save('php://output');


function addMultiLineText($cell, $strArr)
{
    // add text line together
    foreach ($strArr as $v) {
        if($v['actor'] == 'Submitted')
        $cell->addText($v['action'] . ' (' . $v['actor'] . ' at ' . $v['created_at'] . ')', ['bold' => false], ['align' => \PhpOffice\PhpWord\Style\Cell::VALIGN_CENTER]);
        else
        $cell->addText($v['action'] . ' ' . htmlspecialchars($v['reason']) . ' (' . $v['actor'] . ' at ' . $v['created_at'] . ')', ['bold' => false], ['align' => \PhpOffice\PhpWord\Style\Cell::VALIGN_CENTER]);
    }
}

function addMultiLineAttach($cell, $strArr)
{
    // add text line together
    foreach ($strArr as $v) {
        $cell->addLink("https://storage.googleapis.com/feliiximg/" . $v['gcp_name'], htmlspecialchars($v['filename']), 'Link', ['alignment' => \PhpOffice\PhpWord\SimpleType\Jc::CENTER, 'valign' => \PhpOffice\PhpWord\SimpleType\VerticalJc::CENTER]);
    }
}

function GetAttachment($_id, $db)
{
    $sql = "select COALESCE(h.filename, '') filename, COALESCE(h.gcp_name, '') gcp_name
            from gcp_storage_file h where h.batch_id = " . $_id . " AND h.batch_type = 'apply_office_item'
            order by h.created_at ";

    $merged_results = array();

    $stmt = $db->prepare($sql);
    $stmt->execute();

    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        $merged_results[] = $row;
    }

    return $merged_results;
}

function GetUserInfo($users, $db)
{
    $sql = "SELECT id, username, pic_url FROM user WHERE id IN (" . $users . ")";

    $merged_results = array();

    $stmt = $db->prepare($sql);
    $stmt->execute();

    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        $merged_results[] = $row;
    }

    return $merged_results;
}

function GetPriority($loc)
{
    $location = "";
    switch ($loc) {
        case "1":
            $location = "No Priority";
            break;
        case "2":
            $location = "Low";
            break;
        case "3":
            $location = "Normal";
            break;
        case "4":
            $location = "High";
            break;
        case "5":
            $location = "Urgent";
            break;
        
    }

    return $location;
}

function GetPettyType($loc)
{
    $location = "";
    switch ($loc) {
        case "1":
            $location = "New";
            break;
        case "2":
            $location = "Reimbursement";
            break;
        case "3":
            $location = "Petty Cash Replenishment";
            break;
        default:
            $location = "";
            break;
    }

    return $location;
}

function GetFlow($loc)
{
    $location = "";
    switch ($loc) {
        case "4":
            $location = "'Office Petty Cash'";
            break;
        case "5":
            $location = "'Online Transactions'";
            break;
        case "6":
            $location = "'Security Bank'";
            break;
        default:
            $location = "";
            break;
    }

    return $location;
}

function GetStatus($loc)
{
    $location = "";
    switch ($loc) {
        case 3:
            $location = "Void";
            break;
        case -1:
            $location = "Withdrawn";
            break;
        case 2:
            $location = "Rejected";
            break;
        case 4:
            $location = "For Approve";
            break;
        case 5:
            $location = "For Release";
            break;
        case 6:
            $location = "Completed";
            break;
    }

    return $location;
}


function grab_image($image_url,$image_file){

    /*
    $storage = new StorageClient([
        'projectId' => 'predictive-fx-284008',
        'keyFilePath' => $key
    ]);

    $bucket = $storage->bucket('feliiximg');

    $object = $bucket->object($image_url);
    $object->downloadToFile($image_file);
    */

    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, 'https://storage.googleapis.com/feliiximg/' . $image_url);
    //Create a new file where you want to save
    $fp = fopen($image_file, 'w');
    curl_setopt($ch, CURLOPT_FILE, $fp);
    curl_exec ($ch);
    curl_close ($ch);
    fclose($fp);

    $ext = pathinfo($image_file, PATHINFO_EXTENSION);

    createResizedImage($image_file, $image_file,strtolower($ext));
}


function GetList($_id, $db)
{
    $sql = "select pm.id, sn, payee, particulars, price, qty, `status`
    from petty_list pm 
    where `status` <> -1 and petty_id = " . $_id . " order by sn ";

    $merged_results = array();

    $stmt = $db->prepare($sql);
    $stmt->execute();

    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        $merged_results[] = $row;
    }

    return $merged_results;
}

function GetHistory($_id, $db)
{
    $sql = "select pm.id, `actor`, `action`, reason, `status`, DATE_FORMAT(pm.created_at, '%Y/%m/%d %T') created_at from office_item_apply_history pm 
            where `status` <> -1 and request_id = " . $_id . " order by created_at ";

    $merged_results = array();

    $stmt = $db->prepare($sql);
    $stmt->execute();

    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        $merged_results[] = $row;
    }

    return $merged_results;
}

function fiter_str($str)
{
    $str = str_replace("&", "AND", $str);
    return $str;
}

function GetAmountPettyLiquidate($_id, $db)
{
    $sql = "select pm.id, sn, vendor payee, particulars, price, qty, `status`
    from apply_for_petty_liquidate pm 
    where `status` <> -1 and petty_id = " . $_id . " order by sn ";

    $merged_results = array();

    $stmt = $db->prepare($sql);
    $stmt->execute();

    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        $merged_results[] = $row;
    }

    return $merged_results;
}

function GetDepartment($dept_name)
{
    $department = "";

    if($dept_name == 'admin')
        $department = 'Admin Department';

    if($dept_name == 'design')
        $department = 'Design Department';

    if($dept_name == 'engineering')
        $department = 'Engineering Department';

    if($dept_name == 'lighting')
        $department = 'Lighting Department';
    
    if($dept_name == 'office')
        $department = 'Office Department';
    
    if($dept_name == 'sales')
        $department = 'Sales Department';

    return $department;
}


function UpdateQty($list, $db)
{
    foreach($list as &$item)
    {
        $code = $item['code1'] . $item['code2'] . $item['code3'] . $item['code4'];

        $sql = "select qty, reserve_qty from office_items_stock where code = '" . $code . "'";
        $stmt = $db->prepare($sql);
        $stmt->execute();
        
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $qty = $row['qty'];
            $reserve_qty = $row['reserve_qty'];

            $item['qty'] = $qty;
            $item['reserve_qty'] = $reserve_qty;
        }
    }

    return $list;
}