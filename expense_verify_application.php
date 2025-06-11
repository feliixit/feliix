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

    $user_name = $decoded->data->username;

    if($decoded->data->limited_access == true)
                header( 'location:index' );

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


$sql = "SELECT  pm.id,
        request_no, 
        DATE_FORMAT(pm.date_requested, '%Y/%m/%d') date_requested,
        p.username requestor,
        request_type,
        project_name1,
        project_name,
        u.username payable_to,
        payable_other,
        remark,
        pm.`status` ,
        DATE_FORMAT(pm.created_at, '%Y/%m/%d %T') created_at,
        DATE_FORMAT(Now(), '%Y/%m/%d') today,
        info_account,
        info_category,
        info_sub_category,
        info_remark,
        info_remark_other,
        pm.amount_verified,
        pm.amount_liquidated,
        pm.remark_liquidated,
        pm.total_amount_liquidate,
        pm.amount_of_return,
        pm.method_of_return,
                        pm.rtype,
                        pm.dept_name
from apply_for_petty pm 
LEFT JOIN user u ON u.id = pm.payable_to 
LEFT JOIN user p ON p.id = pm.uid 
where  pm.id = " . $id;


$stmt = $db->prepare($sql);
$stmt->execute();

$id = 0;
$request_no = "";
$date_requested = "";
$request_type_id = 0;
$request_type = "";
$project_name = "";
$project_name1 = "";
$payable_to = "";
$payable_other = "";
$remark = "";
$status = 0;
$desc = "";

$requestor = "";
$created_at = "";

$today = "";

$info_account = "";
$info_category = "";
$info_sub_category = "";
$info_remark = "";
$info_remark_other = "";

$release_date = "";
$release_items = [];
$liquidate_date = "";
$liquidate_items = [];

$total_amount_liquidate = "";
        $amount_of_return = "";
        $method_of_return = "";
        $apply_for_petty_liquidate = [];

$amount_liquidated = 0;
$remark_liquidated = "";

$amount_verified = 0;

$releasor = "";

$total = 0;

$history = [];
$list = [];
$items = [];

$rtype = "";
        $dept_name = "";


while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
    $id = $row['id'];
    $request_no = $row['request_no'];
    $date_requested = $row['date_requested'];
    $request_type_id = $row['request_type'];
    $request_type = GetPettyType($row['request_type']);
    $requestor = $row['requestor'];
    $project_name = $row['project_name'];
    $project_name1 = $row['project_name1'];
    $payable_to = $row['payable_to'];
    $payable_other = $row['payable_other'];
    $remark = $row['remark'];
    $status = $row['status'];
    $desc = GetStatus($row['status']);
    $items = GetAttachment($row['id'], $db);
    $history = GetHistory($row['id'], $db);
    $list = GetList($row['id'], $db);
    $created_at = $row['created_at'];

    $release_date = GetReleaseHistory($row['id'], $db);
    $release_items = GetReleaseAttachment($row['id'], $db);
    $liquidate_date = GetLiquidateHistory($row['id'], $db);
    $liquidate_items = GetLiquidateAttachment($row['id'], $db);

    $releasor = GetReleaser($row['id'], $db);

    $amount_liquidated = $row['amount_liquidated'];
    $remark_liquidated = $row['remark_liquidated'];

    $apply_for_petty_liquidate = GetAmountPettyLiquidate($row['id'], $db);

    $total_amount_liquidate = $row['total_amount_liquidate'];
    $amount_of_return = $row['amount_of_return'];
    $method_of_return = $row['method_of_return'];

    $combine_liquidate = [];
    if($amount_liquidated == null)
    {
        //$total_amount_liquidate = 0;
        foreach ($list as &$value) {
            $obj = array(
                "id" => $value['id'],
                "sn" => $value['sn'],
                "payee" => $value['payee'],
                "particulars" => $value['particulars'],
                "price" => $value['price'],
                "qty" => $value['qty'],
                "status" => $value['status']
            );

            //$total_amount_liquidate += $value['price'] * $value['qty'];
            $combine_liquidate[] = $obj;
        }
    }
    else
    {
        $combine_liquidate = $apply_for_petty_liquidate;
    }


    $amount_verified = $row['amount_verified'];

    $today = $row['today'];

    $info_account = $row['info_account'];
    $info_category = $row['info_category'];
    $info_sub_category = $row['info_sub_category'];
    $info_remark = $row['info_remark'];
    $info_remark_other = $row['info_remark_other'];

    $rtype = $row['rtype'];
            $dept_name = $row['dept_name'];
            $department = GetDepartment($row['dept_name']);

    foreach ($list as &$value) {
        $total += $value['price'] * $value['qty'];
    }

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
$table->addCell(7500, ['borderSize' => 6])->addText($requestor, ['bold' => false], ['align' => \PhpOffice\PhpWord\Style\Cell::VALIGN_CENTER]);

$table->addRow();
$table->addCell(3000, ['borderSize' => 6])->addText("Status", ['bold' => false], ['align' => \PhpOffice\PhpWord\Style\Cell::VALIGN_CENTER]);
$table->addCell(7500, ['borderSize' => 6])->addText($desc, ['bold' => false], ['align' => \PhpOffice\PhpWord\Style\Cell::VALIGN_CENTER]);

$table->addRow();
$table->addCell(3000, ['borderSize' => 6])->addText("Processing History", ['bold' => false], ['align' => \PhpOffice\PhpWord\SimpleType\Jc::CENTER, 'valign' => \PhpOffice\PhpWord\SimpleType\Jc::BOTH]);
$cell = $table->addCell(7500, ['borderSize' => 6]);
addMultiLineText($cell, $history);

$table->addRow();
$table->addCell(3000, ['borderSize' => 6])->addText("Date Needed", ['bold' => false], ['align' => \PhpOffice\PhpWord\Style\Cell::VALIGN_CENTER]);
$table->addCell(7500, ['borderSize' => 6])->addText($date_requested, ['bold' => false], ['align' => \PhpOffice\PhpWord\Style\Cell::VALIGN_CENTER]);

$table->addRow();
$table->addCell(3000, ['borderSize' => 6])->addText("Type", ['bold' => false], ['align' => \PhpOffice\PhpWord\Style\Cell::VALIGN_CENTER]);
$table->addCell(7500, ['borderSize' => 6])->addText($request_type, ['bold' => false], ['align' => \PhpOffice\PhpWord\Style\Cell::VALIGN_CENTER]);

$table->addRow();
$table->addCell(3000, ['borderSize' => 6])->addText("Project Name", ['bold' => false], ['align' => \PhpOffice\PhpWord\Style\Cell::VALIGN_CENTER]);
$table->addCell(7500, ['borderSize' => 6])->addText(fiter_str($project_name1), ['bold' => false], ['align' => \PhpOffice\PhpWord\Style\Cell::VALIGN_CENTER]);

$table->addRow();
$table->addCell(3000, ['borderSize' => 6])->addText("Reason", ['bold' => false], ['align' => \PhpOffice\PhpWord\Style\Cell::VALIGN_CENTER]);
if($rtype == '')
    $table->addCell(7500, ['borderSize' => 6])->addText($project_name, ['bold' => false], ['align' => \PhpOffice\PhpWord\Style\Cell::VALIGN_CENTER]);
if($rtype == 'team')
    $table->addCell(7500, ['borderSize' => 6])->addText('Team Building (' . $department . ') — ' . $project_name, ['bold' => false], ['align' => \PhpOffice\PhpWord\Style\Cell::VALIGN_CENTER]);

$table->addRow();
$table->addCell(3000, ['borderSize' => 6])->addText("Total Amount Requested", ['bold' => false], ['align' => \PhpOffice\PhpWord\Style\Cell::VALIGN_CENTER]);
$table->addCell(7500, ['borderSize' => 6])->addText(number_format($total, 2), ['bold' => false], ['align' => \PhpOffice\PhpWord\Style\Cell::VALIGN_CENTER]);

$table->addRow();
$table->addCell(3000, ['borderSize' => 6])->addText("Attachments", ['bold' => false], ['align' => \PhpOffice\PhpWord\Style\Cell::VALIGN_CENTER]);
$cell = $table->addCell(7500, ['borderSize' => 6]);
addMultiLineAttach($cell, $items);

$table->addRow();
$table->addCell(3000, ['borderSize' => 6])->addText("Payable to", ['bold' => false], ['align' => \PhpOffice\PhpWord\Style\Cell::VALIGN_CENTER]);
$table->addCell(7500, ['borderSize' => 6])->addText(($payable_other != '') ? 'Other:' . $payable_other : 'Requestor', ['bold' => false], ['align' => \PhpOffice\PhpWord\Style\Cell::VALIGN_CENTER]);

$table->addRow();
$table->addCell(3000, ['borderSize' => 6])->addText("Remarks or Payment Instructions", ['bold' => false], ['align' => \PhpOffice\PhpWord\Style\Cell::VALIGN_CENTER]);
$table->addCell(7500, ['borderSize' => 6])->addText($remark, ['bold' => false], ['align' => \PhpOffice\PhpWord\Style\Cell::VALIGN_CENTER]);

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
$table1->addCell(2500, ['borderSize' => 6, 'bgColor' => 'EFEFEF'])->addText("Payee", ['bold' => true], ['align' => \PhpOffice\PhpWord\Style\Cell::VALIGN_CENTER]);
$table1->addCell(5000, ['borderSize' => 6, 'bgColor' => 'EFEFEF'])->addText("Particulars",  ['bold' => true], ['align' => \PhpOffice\PhpWord\Style\Cell::VALIGN_CENTER]);
$table1->addCell(1000, ['borderSize' => 6, 'bgColor' => 'EFEFEF'])->addText("Price",  ['bold' => true], ['align' => \PhpOffice\PhpWord\Style\Cell::VALIGN_CENTER]);
$table1->addCell(1000, ['borderSize' => 6, 'bgColor' => 'EFEFEF'])->addText("Qty",  ['bold' => true], ['align' => \PhpOffice\PhpWord\Style\Cell::VALIGN_CENTER]);
$table1->addCell(1000, ['borderSize' => 6, 'bgColor' => 'EFEFEF'])->addText("Amount",  ['bold' => true], ['align' => \PhpOffice\PhpWord\Style\Cell::VALIGN_CENTER]);


foreach ($list as &$value) {
    $table1->addRow();
    $table1->addCell(2600, ['borderSize' => 6])->addText($value['payee'], [], ['align' => \PhpOffice\PhpWord\Style\Cell::VALIGN_CENTER]);
    $table1->addCell(6100, ['borderSize' => 6])->addText($value['particulars'], [], ['align' => \PhpOffice\PhpWord\Style\Cell::VALIGN_CENTER]);
    $table1->addCell(600, ['borderSize' => 6])->addText(number_format($value['price'], 2), [], ['align' => \PhpOffice\PhpWord\Style\Cell::VALIGN_CENTER]);
    $table1->addCell(600, ['borderSize' => 6])->addText(number_format($value['qty']), [], ['align' => \PhpOffice\PhpWord\Style\Cell::VALIGN_CENTER]);
    $table1->addCell(600, ['borderSize' => 6])->addText(number_format($value['price'] * $value['qty'], 2), [], ['align' => \PhpOffice\PhpWord\Style\Cell::VALIGN_CENTER]);
}


$section->addText("");
$section->addText("Additional Info");

$table2 = $section->addTable('table2', [
    'borderSize' => 6,
    'borderColor' => 'F73605',
    'afterSpacing' => 0,
    'Spacing' => 0,
    'cellMargin' => 0,
    'textAlign' => 'center'
]);

$table2->addRow();
$table2->addCell(5250, ['borderSize' => 6])->addText("Account", [], ['align' => \PhpOffice\PhpWord\Style\Cell::VALIGN_CENTER]);
$table2->addCell(5250, ['borderSize' => 6])->addText($info_account,  [], ['align' => \PhpOffice\PhpWord\Style\Cell::VALIGN_CENTER]);

$table2->addRow();
$table2->addCell(5250, ['borderSize' => 6])->addText("Category", [], ['align' => \PhpOffice\PhpWord\Style\Cell::VALIGN_CENTER]);
$table2->addCell(5250, ['borderSize' => 6])->addText($info_category . (($info_sub_category != '') ? ' >> ' . $info_sub_category : ''),  [], ['align' => \PhpOffice\PhpWord\Style\Cell::VALIGN_CENTER]);

$table2->addRow();
$table2->addCell(5250, ['borderSize' => 6])->addText("Remarks or Payment Instructions", [], ['align' => \PhpOffice\PhpWord\Style\Cell::VALIGN_CENTER]);
$table2->addCell(5250, ['borderSize' => 6])->addText($info_remark . (($info_remark_other != '') ? ' : ' . $info_remark_other : ''),  [], ['align' => \PhpOffice\PhpWord\Style\Cell::VALIGN_CENTER]);


$section->addText("");


$table4 = $section->addTable('table', [
    'borderSize' => 6,
    'borderColor' => 'F73605',
    'afterSpacing' => 0,
    'Spacing' => 0,
    'cellMargin' => 0,
    'align' => 'center'
]);

$table4->addRow();
$table4->addCell(3000, ['borderSize' => 6, 'bgColor' => 'EFEFEF'])->addText("Request No.", ['bold' => true], ['align' => \PhpOffice\PhpWord\Style\Cell::VALIGN_CENTER]);
$table4->addCell(7500, ['borderSize' => 6, 'bgColor' => 'EFEFEF'])->addText($request_no, ['bold' => true], ['align' => \PhpOffice\PhpWord\Style\Cell::VALIGN_CENTER]);

$table4->addRow();
$table4->addCell(3000, ['borderSize' => 6])->addText("Total Amount Requested", ['bold' => false], ['align' => \PhpOffice\PhpWord\Style\Cell::VALIGN_CENTER]);
$table4->addCell(7500, ['borderSize' => 6])->addText(number_format($total, 2), ['bold' => false], ['align' => \PhpOffice\PhpWord\Style\Cell::VALIGN_CENTER]);


$table4->addRow();
$table4->addCell(3000, ['borderSize' => 6])->addText("Date Released", ['bold' => false], ['align' => \PhpOffice\PhpWord\Style\Cell::VALIGN_CENTER]);
$table4->addCell(7500, ['borderSize' => 6])->addText($release_date, ['bold' => false], ['align' => \PhpOffice\PhpWord\Style\Cell::VALIGN_CENTER]);

$table4->addRow();
$table4->addCell(3000, ['borderSize' => 6])->addText("Proof of Release", ['bold' => false], ['align' => \PhpOffice\PhpWord\Style\Cell::VALIGN_CENTER]);
$cell = $table4->addCell(7500, ['borderSize' => 6]);
addMultiLineAttach($cell, $release_items);

$table4->addRow();
$table4->addCell(3000, ['borderSize' => 6])->addText("Date Liquidated", ['bold' => false], ['align' => \PhpOffice\PhpWord\Style\Cell::VALIGN_CENTER]);
$table4->addCell(7500, ['borderSize' => 6])->addText($liquidate_date, ['bold' => false], ['align' => \PhpOffice\PhpWord\Style\Cell::VALIGN_CENTER]);

$table4->addRow();
$table4->addCell(3000, ['borderSize' => 6])->addText("Total Amount in Liquidation Listing", ['bold' => false], ['align' => \PhpOffice\PhpWord\Style\Cell::VALIGN_CENTER]);
$table4->addCell(7500, ['borderSize' => 6])->addText(number_format($total_amount_liquidate, 2), ['bold' => false], ['align' => \PhpOffice\PhpWord\Style\Cell::VALIGN_CENTER]);


$table4->addRow();
$table4->addCell(3000, ['borderSize' => 6])->addText("Amount Liquidated", ['bold' => false], ['align' => \PhpOffice\PhpWord\Style\Cell::VALIGN_CENTER]);
$table4->addCell(7500, ['borderSize' => 6])->addText(number_format($amount_liquidated, 2), ['bold' => false], ['align' => \PhpOffice\PhpWord\Style\Cell::VALIGN_CENTER]);


$table4->addRow();
$table4->addCell(3000, ['borderSize' => 6])->addText("Amount of Return Money", ['bold' => false], ['align' => \PhpOffice\PhpWord\Style\Cell::VALIGN_CENTER]);
$table4->addCell(7500, ['borderSize' => 6])->addText(number_format($amount_of_return, 2), ['bold' => false], ['align' => \PhpOffice\PhpWord\Style\Cell::VALIGN_CENTER]);


$table4->addRow();
$table4->addCell(3000, ['borderSize' => 6])->addText("Amount of Return Money", ['bold' => false], ['align' => \PhpOffice\PhpWord\Style\Cell::VALIGN_CENTER]);
$table4->addCell(7500, ['borderSize' => 6])->addText($method_of_return, ['bold' => false], ['align' => \PhpOffice\PhpWord\Style\Cell::VALIGN_CENTER]);


$table4->addRow();
$table4->addCell(3000, ['borderSize' => 6])->addText("Liquidation Files", ['bold' => false], ['align' => \PhpOffice\PhpWord\Style\Cell::VALIGN_CENTER]);
$cell = $table4->addCell(7500, ['borderSize' => 6]);
addMultiLineAttach($cell, $liquidate_items);

$table4->addRow();
$table4->addCell(3000, ['borderSize' => 6])->addText("Remarks", ['bold' => false], ['align' => \PhpOffice\PhpWord\Style\Cell::VALIGN_CENTER]);
$table4->addCell(7500, ['borderSize' => 6])->addText($remark_liquidated, ['bold' => false], ['align' => \PhpOffice\PhpWord\Style\Cell::VALIGN_CENTER]);


$section->addText("Liquidation Listing");

$table6 = $section->addTable('table6', [
    'borderSize' => 6,
    'borderColor' => 'F73605',
    'afterSpacing' => 0,
    'Spacing' => 0,
    'cellMargin' => 0,
    'textAlign' => 'center'
]);

$table6->addRow();
$table6->addCell(2500, ['borderSize' => 6, 'bgColor' => 'EFEFEF'])->addText("Vendor", ['bold' => true], ['align' => \PhpOffice\PhpWord\Style\Cell::VALIGN_CENTER]);
$table6->addCell(5000, ['borderSize' => 6, 'bgColor' => 'EFEFEF'])->addText("Particulars",  ['bold' => true], ['align' => \PhpOffice\PhpWord\Style\Cell::VALIGN_CENTER]);
$table6->addCell(1000, ['borderSize' => 6, 'bgColor' => 'EFEFEF'])->addText("Price",  ['bold' => true], ['align' => \PhpOffice\PhpWord\Style\Cell::VALIGN_CENTER]);
$table6->addCell(1000, ['borderSize' => 6, 'bgColor' => 'EFEFEF'])->addText("Qty",  ['bold' => true], ['align' => \PhpOffice\PhpWord\Style\Cell::VALIGN_CENTER]);
$table6->addCell(1000, ['borderSize' => 6, 'bgColor' => 'EFEFEF'])->addText("Amount",  ['bold' => true], ['align' => \PhpOffice\PhpWord\Style\Cell::VALIGN_CENTER]);


foreach ($combine_liquidate as &$value) {
    $table6->addRow();
    $table6->addCell(2600, ['borderSize' => 6])->addText($value['payee'], [], ['align' => \PhpOffice\PhpWord\Style\Cell::VALIGN_CENTER]);
    $table6->addCell(6100, ['borderSize' => 6])->addText($value['particulars'], [], ['align' => \PhpOffice\PhpWord\Style\Cell::VALIGN_CENTER]);
    $table6->addCell(600, ['borderSize' => 6])->addText(number_format($value['price'], 2), [], ['align' => \PhpOffice\PhpWord\Style\Cell::VALIGN_CENTER]);
    $table6->addCell(600, ['borderSize' => 6])->addText(number_format($value['qty']), [], ['align' => \PhpOffice\PhpWord\Style\Cell::VALIGN_CENTER]);
    $table6->addCell(600, ['borderSize' => 6])->addText(number_format($value['price'] * $value['qty'], 2), [], ['align' => \PhpOffice\PhpWord\Style\Cell::VALIGN_CENTER]);
}


$section->addText("");

$table3 = $section->addTable('table3', [
    'borderSize' => 'none',
    'borderColor' => 'ffffff',
    'afterSpacing' => 0,
    'Spacing' => 0,
    'cellMargin' => 0,
    'textAlign' => 'left'
]);

$table3->addRow();
$table3->addCell(5250, ['borderSize' => 'none'])->addText("Date of liquidation:", [], []);
$table3->addCell(5250, ['borderSize' => 'none'])->addText("Date",  [], []);

$table3->addRow();
$table3->addCell(5250, ['borderSize' => 'none'])->addText($liquidate_date, [], []);
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

$wording1 = "";
$wording2 = "";
if($amount_liquidated > $total)
{
    $wording1 = "Released by " . $user_name;
    $wording2 = "Received by " . $requestor;
}
else
{
    $wording1 = "Returned by " . $requestor;
    $wording2 = "Received by " . $user_name;
}

$table3->addRow();
$table3->addCell(5250, ['borderSize' => 'none'])->addText($wording1, [], []);
$table3->addCell(5250, ['borderSize' => 'none'])->addText($wording2,  [], []);

$section->addText("");


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
        $cell->addText($v['action'] . ' ' . $v['reason'] . ' (' . $v['actor'] . ' at ' . $v['created_at'] . ')', ['bold' => false], ['align' => \PhpOffice\PhpWord\Style\Cell::VALIGN_CENTER]);
    }
}

function addMultiLineAttach($cell, $strArr)
{
    // add text line together
    foreach ($strArr as $v) {
        $cell->addLink("https://storage.googleapis.com/feliiximg/" . $v['gcp_name'], $v['filename'], 'Link', ['alignment' => \PhpOffice\PhpWord\SimpleType\Jc::CENTER, 'valign' => \PhpOffice\PhpWord\SimpleType\VerticalJc::CENTER]);
    }
}

function GetAttachment($_id, $db)
{
    $sql = "select COALESCE(h.filename, '') filename, COALESCE(h.gcp_name, '') gcp_name
            from gcp_storage_file h where h.batch_id = " . $_id . " AND h.batch_type = 'petty'
            order by h.created_at ";

    $merged_results = array();

    $stmt = $db->prepare($sql);
    $stmt->execute();

    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        $merged_results[] = $row;
    }

    return $merged_results;
}

function GetReleaseAttachment($_id, $db)
{
    $sql = "select COALESCE(h.filename, '') filename, COALESCE(h.gcp_name, '') gcp_name
            from gcp_storage_file h where h.batch_id = " . $_id . " AND h.batch_type = 'Releaser Released'
            order by h.created_at ";

    $merged_results = array();

    $stmt = $db->prepare($sql);
    $stmt->execute();

    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        $merged_results[] = $row;
    }

    return $merged_results;
}

function GetLiquidateAttachment($_id, $db)
{
    $sql = "select COALESCE(h.filename, '') filename, COALESCE(h.gcp_name, '') gcp_name
            from gcp_storage_file h where h.batch_id = " . $_id . " AND h.batch_type = 'Liquidated'
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
        case -2:
            $location = "Void";
            break;
        case -1:
            $location = "Withdrawn";
            break;
        case 0:
            $location = "Rejected";
            break;
        case 1:
            $location = "For Check";
            break;
        case 2:
            $location = "For Check";
            break;
        case 3:
            $location = "For Approve";
            break;
        case 4:
            $location = "For Approve";
            break;
        case 5:
            $location = "For Release";
            break;
        case 6:
            $location = "For Liquidate";
            break;
        case 7:
            $location = "For Liquidate";
            break;
        case 8:
            $location = "For Verify";
            break;
        case 9:
            $location = "Completed";
            break;
        
                
    }

    return $location;
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
    $sql = "select pm.id, `actor`, `action`, reason, `status`, DATE_FORMAT(pm.created_at, '%Y/%m/%d %T') created_at from petty_history pm 
            where `status` <> -1 and petty_id = " . $_id . " order by created_at ";

    $merged_results = array();

    $stmt = $db->prepare($sql);
    $stmt->execute();

    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        $merged_results[] = $row;
    }

    return $merged_results;
}

function GetReleaseHistory($_id, $db)
{
    $sql = "select DATE_FORMAT(pm.created_at, '%Y/%m/%d') created_at from petty_history pm 
            where `status` <> -1 and petty_id = " . $_id . " and `action` = 'Releaser Released' order by created_at desc limit 1";

    $merged_results = "";

    $stmt = $db->prepare($sql);
    $stmt->execute();

    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        $merged_results = $row['created_at'];
    }

    return $merged_results;
}

function GetReleaser($_id, $db)
{
    $sql = "select actor from petty_history pm 
            where `status` <> -1 and petty_id = " . $_id . " and `action` = 'Releaser Released' order by created_at desc limit 1";

    $merged_results = "";

    $stmt = $db->prepare($sql);
    $stmt->execute();

    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        $merged_results = $row['actor'];
    }

    return $merged_results;
}

function GetLiquidateHistory($_id, $db)
{
    $sql = "select DATE_FORMAT(pm.created_at, '%Y/%m/%d') created_at from petty_history pm 
            where `status` <> -1 and petty_id = " . $_id . " and `action` = 'Liquidated' order by created_at desc limit 1";

    $merged_results = "";

    $stmt = $db->prepare($sql);
    $stmt->execute();

    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        $merged_results = $row['created_at'];
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
