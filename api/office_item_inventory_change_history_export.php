<?php
ob_start();
// required headers
error_reporting(0);
 
require '../vendor/autoload.php';
// required to encode json web token
include_once 'config/core.php';
include_once 'libs/php-jwt-master/src/BeforeValidException.php';
include_once 'libs/php-jwt-master/src/ExpiredException.php';
include_once 'libs/php-jwt-master/src/SignatureInvalidException.php';
include_once 'libs/php-jwt-master/src/JWT.php';

include_once 'config/conf.php';

use \Firebase\JWT\JWT;
 
// files needed to connect to database
include_once 'config/database.php';

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\IOFactory;
 
// get database connection
$database = new Database();
$db = $database->getConnection();

// get jwt
$jwt = (isset($_COOKIE['jwt']) ?  $_COOKIE['jwt'] : null);

$fs = (isset($_POST['fs']) ?  $_POST['fs'] : '');
$fap = (isset($_POST['fap']) ?  $_POST['fap'] : '');
$fap = urldecode($fap);

$fp = (isset($_POST['fp']) ? $_POST['fp'] : '');
$fp = urldecode($fp);

$fk = (isset($_POST['fk']) ?  $_POST['fk'] : '');
$fk = urldecode($fk);

$fds = (isset($_POST['fds']) ?  $_POST['fds'] : '');
$fds = str_replace('-', '/', $fds);
$fde = (isset($_POST['fde']) ?  $_POST['fde'] : '');
$fde = str_replace('-', '/', $fde);

$of1 = (isset($_POST['of1']) ?  $_POST['of1'] : '');
$ofd1 = (isset($_POST['ofd1']) ?  $_POST['ofd1'] : '');
$of2 = (isset($_POST['of2']) ?  $_POST['of2'] : '');
$ofd2 = (isset($_POST['ofd2']) ?  $_POST['ofd2'] : '');

$lv1 = (isset($_POST['lv1']) ?  $_POST['lv1'] : '');
$lv2 = (isset($_POST['lv2']) ?  $_POST['lv2'] : '');
$lv3 = (isset($_POST['lv3']) ?  $_POST['lv3'] : '');
$lv4 = (isset($_POST['lv4']) ?  $_POST['lv4'] : '');

$conf = new Conf();

// if jwt is not empty
if($jwt){
 
    // if decode succeed, show user details
    try {
        
        $list_array = array();
 
        // decode jwt
        $decoded = JWT::decode($jwt, $key, array('HS256'));

        $list_array = GetChangeHistory($db, $fs, $fap, $fp, $fk, $fds, $fde, $of1, $ofd1, $of2, $ofd2, $lv1, $lv2, $lv3, $lv4);

        // response in json format
            http_response_code(200);


            // response in json format
            $styleArray = array(
                'borders' => array(
                    'allBorders' => array(
                        'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                        'color' => array('rgb' => '000000'),
                    ),
                ),
            );

            $bold_border_style = array(
                'borders' => array(
                    'outline' => array(
                        'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THICK,
                        'color' => array('rgb' => '000000'),
                    ),
                ),
            );

            $bold_border_style1 = array(
                'borders' => array(
                    'left' => array(
                        'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THICK,
                        'color' => array('rgb' => '000000'),
                    ),
                    'right' => array(
                        'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THICK,
                        'color' => array('rgb' => '000000'),
                    ),
                ),
            );

            $bold_border_style2 = array(
                'borders' => array(
                    'left' => array(
                        'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THICK,
                        'color' => array('rgb' => '000000'),
                    ),
                    'right' => array(
                        'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THICK,
                        'color' => array('rgb' => '000000'),
                    ),
                    'bottom' => array(
                        'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THICK,
                        'color' => array('rgb' => '000000'),
                    ),
                ),
            );

            $boldandthin_border_style = array(
                'borders' => array(
                    'outline' => array(
                        'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THICK,
                        'color' => array('rgb' => '000000'),
                    ),
                    'inside' => array(
                        'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                        'color' => array('rgb' => '000000'),
                    ),
                ),
            );

            $boldandthin_border_style1 = array(
                'borders' => array(
                    'left' => array(
                        'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THICK,
                        'color' => array('rgb' => '000000'),
                    ),
                    'right' => array(
                        'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THICK,
                        'color' => array('rgb' => '000000'),
                    ),
                    'bottom' => array(
                        'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                        'color' => array('rgb' => '000000'),
                    ),
                    'inside' => array(
                        'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                        'color' => array('rgb' => '000000'),
                    ),
                ),
            );

            $center_style = array(
                'alignment' => array(
                    'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
                    'vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER,
                )
            );

            $right_style = array(
                'alignment' => array(
                    'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_RIGHT,
                    'vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER,
                )
            );

            $doubleunderline_style = array(
                'alignment' => array(
                    'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_RIGHT,
                    'vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER,
                )
            );


            $spreadsheet = new Spreadsheet();

            $spreadsheet->getProperties()->setCreator('PhpOffice')
                    ->setLastModifiedBy('PhpOffice')
                    ->setTitle('Office 2007 XLSX Test Document')
                    ->setSubject('Office 2007 XLSX Test Document')
                    ->setDescription('PhpOffice')
                    ->setKeywords('PhpOffice')
                    ->setCategory('PhpOffice');

            $sheet = $spreadsheet->getActiveSheet();
            $sheet->getSheetView()->setZoomScale(60);
            $sheet->getPageSetup()->setPaperSize(\PhpOffice\PhpSpreadsheet\Worksheet\PageSetup::PAPERSIZE_A4);
            $sheet->getPageSetup()->setOrientation(\PhpOffice\PhpSpreadsheet\Worksheet\PageSetup::ORIENTATION_PORTRAIT);
            $sheet->getPageSetup()->setFitToPage(true);
            $sheet->getPageSetup()->setFitToWidth(1);
            $sheet->getPageSetup()->setFitToHeight(0);
            $sheet->getPageMargins()->setTop(0.76);
            $sheet->getPageMargins()->setRight(0.24);
            $sheet->getPageMargins()->setLeft(0.24);
            $sheet->getPageMargins()->setBottom(0.76);

            // $sheet->setTitle("盛盛訂購單");

            $sheet->getStyle('A1:I300')->getAlignment()->setHorizontal('center');
            $sheet->getStyle('A1:I300')->getAlignment()->setVertical('center');


            $sheet->getColumnDimension('A')->setWidth(17.15);
            $sheet->getColumnDimension('B')->setWidth(40.82);
            $sheet->getColumnDimension('C')->setWidth(50.82);
            $sheet->getColumnDimension('D')->setWidth(40.82);
            $sheet->getColumnDimension('E')->setWidth(40.82);
            $sheet->getColumnDimension('F')->setWidth(40.82);
            $sheet->getColumnDimension('G')->setWidth(40.82);
            $sheet->getColumnDimension('H')->setWidth(40.82);
            $sheet->getColumnDimension('I')->setWidth(40.82);

            // header
            $sheet->setCellValue('A1', 'Executed Time');
            $sheet->setCellValue('B1', 'Code');
            $sheet->setCellValue('C1', 'Image');
            $sheet->setCellValue('D1', 'Full Description');
            $sheet->setCellValue('E1', 'Executor');
            $sheet->setCellValue('F1', 'Reason');
            $sheet->setCellValue('G1', 'Qty Before');
            $sheet->setCellValue('H1', 'Qty Change');
            $sheet->setCellValue('I1', 'Qty After');



            $sheet->getStyle('A1:I1')->getFont()->setBold(true);

            $i = 2;

            foreach($list_array as $row)
            {
                $sheet->setCellValue('A'. $i, $row['created_at']);

                $sheet->setCellValue('B'. $i, $row['code1'] . $row['code2'] . $row['code3'] . $row['code4']);

                if($row['photo'] != '')
                {
                    $row['photo'] = str_replace('+', '%20', $row['photo']);
                    $row['photo'] = str_replace(' ', '%20', $row['photo']);

                    grab_image($row['photo'], $conf::$upload_path . preg_replace('/[^A-Za-z0-9]/', '', $row['photo']));

                    $objDrawing = new \PhpOffice\PhpSpreadsheet\Worksheet\Drawing();
                    $objDrawing->setName('photo');
                    $objDrawing->setDescription('photo');
                    $objDrawing->setPath($conf::$upload_path . preg_replace('/[^A-Za-z0-9]/', '', $row['photo']));
                    $objDrawing->setCoordinates('C' . $i);
                    $objDrawing->setWidthAndHeight(100, 100);
                    $objDrawing->setResizeProportional(true);
                    $objDrawing->setOffsetX(80);
                    $objDrawing->setOffsetY(30);
                    $objDrawing->setWorksheet($sheet);

                    $sheet->getRowDimension($i)->setRowHeight(120);

                    $sheet->getStyle('C'. $i)->applyFromArray($center_style);
                }

                $sheet->setCellValue('D'. $i, $row['cat1'] . " >> " . $row['cat2'] . " >> " . $row['cat3'] . " >> " . $row['cat4']);

                $sheet->setCellValue('E'. $i, $row['created_by']);

                $richTextF = new \PhpOffice\PhpSpreadsheet\RichText\RichText();
                $richTextF->createText($row['action'] . "\n");
                $payable = $richTextF->createTextRun($row['act_1'] . "\n");
                $payable->getFont()->setBold(true);
                $richTextF->createText($row['act_2']);

                $sheet->setCellValue('F'. $i, $richTextF);
                $sheet->getStyle('F'. $i)->getAlignment()->setWrapText(true);
                $sheet->getCell('F' . $i)->getHyperlink()->setUrl($row['url']);

                $sheet->setCellValue('G'. $i, $row['qty_before']);

                $richText = new \PhpOffice\PhpSpreadsheet\RichText\RichText();
             
                $att_str = $row['qty'];

                $payable = $richText->createTextRun($att_str);

                if($row['qty'] >= 0)
                    $payable->getFont()->getColor()->setARGB("009900");
                else if($row['qty'] < 0)
                    $payable->getFont()->getColor()->setARGB("FF0000");

                $sheet->setCellValue('H'. $i, $richText);
                $sheet->getStyle('H'. $i)->getAlignment()->setWrapText(true);


                $sheet->setCellValue('I'. $i, $row['qty_after']);

                $i++;
            }


            ob_end_clean();

            header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
            header('Content-Disposition: attachment;filename="file.xlsx"');

            header('Cache-Control: max-age=0');
            // If you're serving to IE 9, then the following may be needed
            header('Cache-Control: max-age=1');
            // If you're serving to IE over SSL, then the following may be needed
            header('Expires: Mon, 26 Jul 1997 05:00:00 GMT'); // Date in the past
            header('Last-Modified: ' . gmdate('D, d M Y H:i:s') . ' GMT'); // always modified
            header('Cache-Control: cache, must-revalidate'); // HTTP/1.1
            header('Pragma: public'); // HTTP/1.0
            $writer = IOFactory::createWriter($spreadsheet, 'Xlsx');
            $writer->save('php://output');


            exit;
    }
 
    // if decode fails, it means jwt is invalid
    catch (Exception $e){
    
        // set response code
        http_response_code(401);
    
        // show error message
        echo json_encode(array(
            "message" => "Access denied.",
            "error" => $e->getMessage()
        ));
    }
}
// show error message if jwt is empty
else{
 
    // set response code
    http_response_code(401);
 
    // tell the user access denied
    echo json_encode(array("message" => "Access denied."));
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

    //$image_file = urlencode($image_file);

    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $image_url);
    //Create a new file where you want to save
    $fp = fopen($image_file, 'w');
    curl_setopt($ch, CURLOPT_FILE, $fp);
    curl_exec ($ch);
    curl_close ($ch);
    fclose($fp);

    $ext = pathinfo($image_file, PATHINFO_EXTENSION);

    createResizedImage($image_file, $image_file,strtolower($ext));
}


function createResizedImage(
    string $imagePath = '',
    string $newPath = '',

    string $outExt = 'DEFAULT'
) : ?string
{
    if (!$newPath or !file_exists ($imagePath)) {
        return null;
    }

    $types = [IMAGETYPE_JPEG, IMAGETYPE_PNG, IMAGETYPE_GIF, IMAGETYPE_BMP, IMAGETYPE_WEBP];
    $type = exif_imagetype ($imagePath);

    if (!in_array ($type, $types)) {
        return null;
    }

    list ($width, $height) = getimagesize ($imagePath);

    $ratio = $width/$height; // width/height
    if( $ratio > 1) {
        $newWidth = 100;
        $newHeight = 100/$ratio;
    }
    else {
        $newWidth = 100*$ratio;
        $newHeight = 100;
    }


    $outBool = in_array ($outExt, ['jpg', 'jpeg', 'png', 'gif', 'bmp', 'webp', 'JPG', 'JPEG', 'PNG', 'GIF', 'BMP', 'WEBP']);

    switch ($type) {
        case IMAGETYPE_JPEG:
            $image = imagecreatefromjpeg ($imagePath);
            if (!$outBool) $outExt = 'jpg';
            break;
        case IMAGETYPE_PNG:
            $image = imagecreatefrompng ($imagePath);
            if (!$outBool) $outExt = 'png';
            break;
        case IMAGETYPE_GIF:
            $image = imagecreatefromgif ($imagePath);
            if (!$outBool) $outExt = 'gif';
            break;
        case IMAGETYPE_BMP:
            $image = imagecreatefrombmp ($imagePath);
            if (!$outBool) $outExt = 'bmp';
            break;
        case IMAGETYPE_WEBP:
            $image = imagecreatefromwebp ($imagePath);
            if (!$outBool) $outExt = 'webp';
    }

    $newImage = imagecreatetruecolor ($newWidth, $newHeight);

    //TRANSPARENT BACKGROUND
    $color = imagecolorallocatealpha ($newImage, 0, 0, 0, 127); //fill transparent back
    imagefill ($newImage, 0, 0, $color);
    imagesavealpha ($newImage, true);

    //ROUTINE
    imagecopyresampled ($newImage, $image, 0, 0, 0, 0, $newWidth, $newHeight, $width, $height);


    switch (true) {
        case in_array ($outExt, ['jpg', 'jpeg']): $success = imagejpeg ($newImage, $newPath);
            break;
        case $outExt === 'png': $success = imagepng ($newImage, $newPath);
            break;
        case $outExt === 'gif': $success = imagegif ($newImage, $newPath);
            break;
        case  $outExt === 'bmp': $success = imagebmp ($newImage, $newPath);
            break;
        case  $outExt === 'webp': $success = imagewebp ($newImage, $newPath);
    }

    if (!$success) {
        return null;
    }

    return $newPath;
}

function GetChangeHistory($db, $fs, $fap, $fp, $fk, $fds, $fde, $of1, $ofd1, $of2, $ofd2, $lv1, $lv2, $lv3, $lv4)
{
    $sql = "SELECT pm.id,
                        pm.request_id, 
                        pm.code,
                        pm.qty,
                        pm.`action`,
                        pm.act_1,
                        pm.act_2,
                        pm.qty_before,
                        pm.qty_after,
                        pm.`status`,
                        p.username created_by,
                        DATE_FORMAT(pm.created_at, '%Y/%m/%d %H:%i') created_at,
                        m.code code1, m.category cat1, 
                		s.code code2, s.category cat2, 
                		b.code code3, b.category cat3, 
                		d.code code4, d.category cat4, 
                		d.photo
                from office_stock_history pm 
                LEFT JOIN user p ON p.id = pm.create_id 
                left join office_items_main_category m on m.code = SUBSTRING(pm.code, 1, 2) and m.status <> -1
        		left join office_items_sub_category s on s.parent_code = SUBSTRING(pm.code, 1, 2) and s.code = SUBSTRING(pm.code, 3, 2) and s.status <> -1
        		left join office_items_brand b on b.parent_code =  SUBSTRING(pm.code, 1, 4) and b.code = SUBSTRING(pm.code, 5, 2) and b.status <> -1
        		left join office_items_description d on d.parent_code = SUBSTRING(pm.code, 1, 6) and d.code = SUBSTRING(pm.code, 7, 2) and d.status <> -1
                where pm.`status` <> -1  ";


if($lv1 != "" || $lv2 != "" || $lv3 != "" || $lv4 != "")
{
    $sql = $sql . " and pm.code like '" . $lv1 . $lv2 .$lv3 . $lv4 . "%' ";

}

if($fk != "")
{
    $sql = $sql . " and pm.code like '" . $fk . "%' ";

}

if($fds != "")
{
    $sql = $sql . " and DATE_FORMAT(pm.created_at, '%Y/%m/%d') >= '" . $fds . "' ";
    
}

if($fde != "")
{
    $sql = $sql . " and DATE_FORMAT(pm.created_at, '%Y/%m/%d') <= '" . $fde . "' ";
    
}

if($fap != "")
{
    $sql = $sql . " and p.username = '" . $fap . "' ";
    
}

// nothing selected
if($lv1 == "" && $lv2 == "" && $lv3 == "" && $lv4 == ""  && $fk == "" && $fds == "" && $fde == "" && $fap == "" && $fs == "")
{
    $sql = $sql . " and pm.code like '______' ";
    
}

$status_array = [];

if($fs != "" && $fs != "0")
{
    if(strpos($fs,"1") > -1)
        $status_array = array_merge($status_array, ["pm.act_1 like 'OIA-%'"]);
    if(strpos($fs,"2") > -1)
        $status_array = array_merge($status_array, ["pm.act_1 like 'IC-%'"]);
    if(strpos($fs,"3") > -1)
        $status_array = array_merge($status_array, ["pm.act_1 like 'IR-%'"]);
    if(strpos($fs,"4") > -1)
        $status_array = array_merge($status_array, ["pm.act_1 like 'IM-%'"]);
}

if(count($status_array) > 0)
{
    $sql = $sql . " and (" . implode(" or ", $status_array) . ") ";
    
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
                $sOrder = "pm.act_1 desc";
            else
                $sOrder = "pm.act_1 ";
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
                $sOrder .= ", Coalesce(pm.created_at, '0000-00-00') ";
            break;  
        case 2:
            if($ofd2 == 2)
                $sOrder .= ", pm.act_1 desc";
            else
                $sOrder .= ", pm.act_1 ";
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
                $sOrder = "pm.act_1 desc";
            else
                $sOrder = "pm.act_1 ";
            break;  
       
        default:
    }
}


if($sOrder != "")
    $sql = $sql . " order by  " . $sOrder;
else
    $sql = $sql . " order by pm.created_at ";

    
    $merged_results = array();

    $stmt = $db->prepare($sql);
    $stmt->execute();


    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        $id = $row['id'];
        $request_id = $row['request_id'];
        $code = $row['code'];
        $qty = $row['qty'];
        $action = $row['action'];
        $act_1 = $row['act_1'];
        $act_2 = $row['act_2'];
        $qty_before = $row['qty_before'];
        $qty_after = $row['qty_after'];
        $status = $row['status'];
        $created_by = $row['created_by'];
        $created_at = $row['created_at'];
        $code1 = $row['code1'];
        $code2 = $row['code2'];
        $code3 = $row['code3'];
        $code4 = $row['code4'];
        $cat1 = $row['cat1'];
        $cat2 = $row['cat2'];
        $cat3 = $row['cat3'];
        $cat4 = $row['cat4'];
        $photo = "https://storage.googleapis.com/feliiximg/" . $row['photo'];
        $url = GetUrl($row['act_1'], $row['request_id']);
        
        $merged_results[] = array(
            "id" => $id,
            "request_id" => $request_id,
            "code" => $code,
            "qty" => $qty,
            "action" => $action,
            "act_1" => $act_1,
            "act_2" => $act_2,
            "qty_before" => $qty_before,
            "qty_after" => $qty_after,
            "status" => $status,
            "created_by" => $created_by,
            "created_at" => $created_at,
            "code1" => $code1,
            "code2" => $code2,
            "code3" => $code3,
            "code4" => $code4,
            "cat1" => $cat1,
            "cat2" => $cat2,
            "cat3" => $cat3,
            "cat4" => $cat4,
            "photo" => $photo,
            "url" => $url,
        );

    }

    return $merged_results;

}


function GetUrl($act_1, $request_id)
{
    $location = "";
    // get "ORA' from "ORA-0101" as $loc
    $loc = substr($act_1, 0, strpos($act_1, "-"));
    switch ($loc) {
        case "OIA":
            $location = "office_item_application_report" . "?id=" . $request_id;
            break;
        case "IR":
            $location = "office_item_inventory_replenish" . "?id=" . $request_id;
            break;
        case "IC":
            $location = "office_item_inventory_check" . "?id=" . $request_id;
            break;
    }

    return "https://feliix.myvnc.com/" . $location;
}

?>