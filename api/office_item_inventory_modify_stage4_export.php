<?php
ob_start();
// required headers
//error_reporting(0);
 
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
$id = (isset($_POST['id']) ?  $_POST['id'] : 0);
$list = (isset($_POST['list']) ?  $_POST['list'] : "");
$list_array = json_decode($list, true);

$conf = new Conf();

// if jwt is not empty
if($jwt){
 
    // if decode succeed, show user details
    try {
 
        // decode jwt
        $decoded = JWT::decode($jwt, $key, array('HS256'));

        // response in json format
            http_response_code(200);

            $merged_results = array();

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

            $sheet->getStyle('A1:F300')->getAlignment()->setHorizontal('center');
            $sheet->getStyle('A1:F300')->getAlignment()->setVertical('center');


            $sheet->getColumnDimension('A')->setWidth(17.15);
            $sheet->getColumnDimension('B')->setWidth(40.82);
            $sheet->getColumnDimension('C')->setWidth(50.82);
            $sheet->getColumnDimension('D')->setWidth(40.82);
            $sheet->getColumnDimension('E')->setWidth(40.82);
            $sheet->getColumnDimension('F')->setWidth(40.82);

            // header
            $sheet->setCellValue('A1', 'Code');
            $sheet->setCellValue('B1', 'Image');
            $sheet->setCellValue('C1', 'Particulars');
            $sheet->setCellValue('D1', 'Modified Qty');
            $sheet->setCellValue('E1', 'Comment');
            $sheet->setCellValue('F1', 'Stock in Qty After Modification');


            $sheet->getStyle('A1:F1')->getFont()->setBold(true);

            $i = 2;

            foreach($list_array as $row)
            {
                $sheet->setCellValue('A'. $i, $row['code1'] . $row['code2'] . $row['code3'] . $row['code4']);

                if($row['url'] != '')
                {
                    $row['url'] = str_replace('+', '%20', $row['url']);
                    $row['url'] = str_replace(' ', '%20', $row['url']);

                    grab_image($row['url'], $conf::$upload_path . preg_replace('/[^A-Za-z0-9]/', '', $row['url']));

                    $objDrawing = new \PhpOffice\PhpSpreadsheet\Worksheet\Drawing();
                    $objDrawing->setName('url');
                    $objDrawing->setDescription('url');
                    $objDrawing->setPath($conf::$upload_path . preg_replace('/[^A-Za-z0-9]/', '', $row['url']));
                    $objDrawing->setCoordinates('B' . $i);
                    $objDrawing->setWidthAndHeight(100, 100);
                    $objDrawing->setResizeProportional(true);
                    $objDrawing->setOffsetX(80);
                    $objDrawing->setOffsetY(30);
                    $objDrawing->setWorksheet($sheet);

                    $sheet->getRowDimension($i)->setRowHeight(120);

                    $sheet->getStyle('B'. $i)->applyFromArray($center_style);
                }

                $sheet->setCellValue('C'. $i, $row['cat1'] . " >> " . $row['cat2'] . " >> " . $row['cat3'] . " >> " . $row['cat4']);

                //$sheet->setCellValue('D'. $i, $row['qty']);

                $richText = new \PhpOffice\PhpSpreadsheet\RichText\RichText();
                $richText1 = new \PhpOffice\PhpSpreadsheet\RichText\RichText();
                $richText2 = new \PhpOffice\PhpSpreadsheet\RichText\RichText();

                $amount = $row['qty1'];
                $sign = $row['sign'];

                $att_str = " " . $sign . " " . $amount . "\n";

                $payable = $richText->createTextRun($att_str);

                if($sign == "+")
                    $payable->getFont()->getColor()->setARGB("009900");
                else if($sign == "-")
                    $payable->getFont()->getColor()->setARGB("FF0000");

                $richText->createText($row['note']);

                $sheet->setCellValue('D'. $i, $richText);
                $sheet->getStyle('D'. $i)->getAlignment()->setWrapText(true);

                if(isset($row['qty2']) && $row['qty2'] != '')
                {
                    $amount = $row['qty2'];
                    $sign = $row['sign2'];

                    $att_str = " " . $sign . " " . $amount . "\n";

                    $payable1 = $richText1->createTextRun($att_str);

                    if($sign == "+")
                        $payable1->getFont()->getColor()->setARGB("009900");
                    else if($sign == "-")
                        $payable1->getFont()->getColor()->setARGB("FF0000");
    
                    // if($row['qty2'] - $row['qty'] > 0)
                    //     $payable1->getFont()->getColor()->setARGB("009900");
                    // else if($row['qty2'] - $row['qty'] < 0)
                    //     $payable1->getFont()->getColor()->setARGB("FF0000");
                }

                
                if(isset($row['comment']))
                    $richText1->createText($row['comment']);

                $sheet->setCellValue('E'. $i, $richText1);
                $sheet->getStyle('E'. $i)->getAlignment()->setWrapText(true);



                $amount = $row['qty2'] != '' ? $row['qty2'] : $row['qty1'];
                $sign = $row['sign2'] != '' ? $row['sign2'] : $row['sign'];

                $att_str = " " . $sign . " " . $amount . " → " . $row['qty_after'];
    
                    $richText2->createText($row['qty_before']);

                    $payable2 = $richText2->createTextRun($att_str);

                    if($sign == "+")
                        $payable2->getFont()->getColor()->setARGB("009900");
                    else if($sign == "-")
                        $payable2->getFont()->getColor()->setARGB("FF0000");
                    //else
                    //$payable2->getFont()->getColor()->setARGB("009900");
    
                    // if($row['qty2'] - $row['qty'] > 0)
                    //     $payable1->getFont()->getColor()->setARGB("009900");
                    // else if($row['qty2'] - $row['qty'] < 0)
                    //     $payable1->getFont()->getColor()->setARGB("FF0000");
                

                $sheet->setCellValue('F'. $i, $richText2);
                $sheet->getStyle('F'. $i)->getAlignment()->setWrapText(true);

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



?>