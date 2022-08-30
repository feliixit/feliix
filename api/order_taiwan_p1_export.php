<?php
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

 
// get posted data
$data = json_decode(file_get_contents("php://input"));
 
// get jwt
$jwt = (isset($_COOKIE['jwt']) ?  $_COOKIE['jwt'] : null);

$id = (isset($_POST['id']) ?  $_POST['id'] : 0);
$ids = (isset($_POST['ids']) ?  $_POST['ids'] : '');
$ids_str = $ids;
$brand = (isset($_POST['brand']) ?  $_POST['brand'] : '');

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

            $query = "SELECT id, 
            sn, 
            confirm, 
            brand, 
            brand_other, 
            photo1, 
            photo2, 
            photo3, 
            code,
            brief,
            listing,
            qty,
            srp,
            date_needed,
            pid,
            shipping_way,
            shipping_number,
            shipping_vendor,
            eta,
            arrive,
            remark,
            remark_t,
            remark_d,
            check_t,
            check_d,
            charge,
            test,
            delivery,
            final,
            `status`
            FROM od_item
            WHERE status <> -1 and od_id=$id and id in ($ids_str)";
            

        $query = $query . " order by ABS(sn) ";

        $stmt = $db->prepare($query);
        $stmt->execute();

        $date_needed_array = [];

        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $id = $row['id'];
            $sn = $row['sn'];
            $confirm = $row['confirm'];

            if($row['status'] == 1)
                $confirm = "W";
            // for approval
            if($row['status'] == 2)
                $confirm = "F";

            $confirm_text = GetConfirmText($confirm, $db);
            
            $product = [];
            $product = GetProduct($row['pid'], $db);

            $brand = $row['brand'];
            $brand_other = $row['brand_other'];
            $photo1 = $row['photo1'];
            $photo2 = $row['photo2'];
            $photo3 = $row['photo3'];
            $code = $row['code'];
            $brief = $row['brief'];
            $listing = $row['listing'];
            $qty = $row['qty'];
            $srp = $row['srp'];
            $date_needed = $row['date_needed'];

            if($date_needed != "" && in_array($date_needed, $date_needed_array) == false)
                $date_needed_array[] = $date_needed;

            $shipping_way = $row['shipping_way'];
            $shipping_number = $row['shipping_number'];
            $shipping_vendor = $row['shipping_vendor'];

            $eta = $row['eta'];
            $arrive = $row['arrive'];
            $remark = $row['remark'];
            $remark_t = $row['remark_t'];
            $remark_d = $row['remark_d'];
            $check_t = $row['check_t'];
            $check_d = $row['check_d'];
            $charge = $row['charge'];
            $test = $row['test'];
            $delivery = $row['delivery'];
            $final = $row['final'];

            $status = $row['status'];
        

            $merged_results[] = array(
            "is_checked" => "",
            "is_edit" => false,
            "is_info" => false,
            "id" => $id,
            "sn" => $sn,
            "confirm" => $confirm,
            "brand" => $brand,
            "brand_other" => $brand_other,
            "photo1" => $photo1,
            "photo2" => $photo2,
            "photo3" => $photo3,
            "code" => $code,
            "brief" => $brief,
            "listing" => $listing,
            "qty" => $qty,
            "srp" => $srp,
            "date_needed" => $date_needed,
            "shipping_way" => $shipping_way,
            "shipping_number" => $shipping_number,
            "shipping_vendor" => $shipping_vendor,

            "product" => $product,

            "eta" => $eta,
            "arrive" => $arrive,
            "remark" => $remark,
            "remark_t" => $remark_t,
            "remark_d" => $remark_d,
            "check_t" => $check_t,
            "check_d" => $check_d,
            "charge" => $charge,
            "test" => $test,
            "delivery" => $delivery,
            "final" => $final,
            "status" => $status,
            "confirm_text" => $confirm_text,
    
            );
        }

        // order and unique array
        $date_needed_array = array_unique($date_needed_array);
        $date_needed_str = implode(',', $date_needed_array);

        // total price_ntd
        $total_price_ntd = 0;
          
            // response in json format
            $styleArray = array(
                'borders' => array(
                    'allBorders' => array(
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

            $spreadsheet = new Spreadsheet();

            $spreadsheet->getProperties()->setCreator('PhpOffice')
                    ->setLastModifiedBy('PhpOffice')
                    ->setTitle('Office 2007 XLSX Test Document')
                    ->setSubject('Office 2007 XLSX Test Document')
                    ->setDescription('PhpOffice')
                    ->setKeywords('PhpOffice')
                    ->setCategory('PhpOffice');

            $sheet = $spreadsheet->getActiveSheet();
            $sheet->setTitle("盛盛訂購單");


            $sheet->getStyle('A1:I300')->getAlignment()->setHorizontal('center');
            $sheet->getStyle('A1:I300')->getAlignment()->setVertical('center');

            $sheet->getStyle('J1:J1')->getAlignment()->setHorizontal('center');
            $sheet->getStyle('J1:J1')->getAlignment()->setVertical('center');
            $sheet->getStyle('J2:J300')->getAlignment()->setHorizontal('left');
            $sheet->getStyle('J2:J300')->getAlignment()->setVertical('top');

            $sheet->getStyle('K1:S300')->getAlignment()->setHorizontal('center');
            $sheet->getStyle('K1:S300')->getAlignment()->setVertical('center');

            // header
            $sheet->setCellValue('B1', '訂購單');
            $sheet->setCellValue('B4', 'To:' . $brand);
            $sheet->setCellValue('B5', '訂單編號:'); $sheet->setCellValue('C5', 'LPO 093'); 
            $sheet->setCellValue('B6', '專案名稱:'); $sheet->setCellValue('C6', 'FELIIX SB SAMPLE (PULL-OUT PINLIGHT)'); 
            $sheet->setCellValue('B7', '訂單日期:'); $sheet->setCellValue('C7', date("Y/m/d")); 
            $sheet->setCellValue('B8', '需求日期:'); $sheet->setCellValue('C8', $date_needed_str);


            // body title
            $sheet->setCellValue('B10', '圖示' . "\n" . 'IMAGE'); $sheet->setCellValue('C10', '圖示' . "\n" . 'IMAGE'); $sheet->setCellValue('D10', '圖示' . "\n" . 'IMAGE');
            $sheet->setCellValue('E10', '品名/型號' . "\n" . 'CODE'); $sheet->setCellValue('F10', '顏色/規格' . "\n" . 'COLOR/SPEC'); $sheet->setCellValue('G10', '數量' . "\n" . 'QTY');
            $sheet->setCellValue('H10', '單價' . "\n" . 'PRICE'); $sheet->setCellValue('I10', '總價' . "\n" . 'AMOUNT'); $sheet->setCellValue('J10', '交期' . "\n" . 'DELIVERY');
            $sheet->setCellValue('K10', '寄送地址'); $sheet->setCellValue('L10', '海運 or 空運');

            $sheet->getStyle('B10')->getAlignment()->setWrapText(true);
            $sheet->getStyle('C10')->getAlignment()->setWrapText(true);
            $sheet->getStyle('D10')->getAlignment()->setWrapText(true);
            $sheet->getStyle('E10')->getAlignment()->setWrapText(true);
            $sheet->getStyle('F10')->getAlignment()->setWrapText(true);
            $sheet->getStyle('G10')->getAlignment()->setWrapText(true);
            $sheet->getStyle('H10')->getAlignment()->setWrapText(true);
            $sheet->getStyle('I10')->getAlignment()->setWrapText(true);
            $sheet->getStyle('J10')->getAlignment()->setWrapText(true);
            
            // body
            $i = 11;
            foreach($merged_results as $row)
            {
                if($row['photo1'] != '')
                {
                    grab_image(str_replace(' ', '%20', $row['photo1']), $conf::$upload_path . str_replace(' ', '%20', $row['photo1']));

                    $objDrawing = new \PhpOffice\PhpSpreadsheet\Worksheet\Drawing();
                    $objDrawing->setName('photo1');
                    $objDrawing->setDescription('photo1');
                    $objDrawing->setPath($conf::$upload_path  . str_replace(' ', '%20', $row['photo1']));
                    $objDrawing->setCoordinates('B' . $i);
                    $objDrawing->setWidthAndHeight(100, 100);
                    $objDrawing->setResizeProportional(true);
                    $objDrawing->setOffsetX(15);
                    $objDrawing->setOffsetY(30);
                    $objDrawing->setWorksheet($sheet);

                    $sheet->getRowDimension($i)->setRowHeight(120);

                    $sheet->getStyle('B'. $i)->applyFromArray($center_style);
                }

                if($row['photo2'] != '')
                {
                    grab_image(str_replace(' ', '%20', $row['photo2']), $conf::$upload_path . str_replace(' ', '%20', $row['photo2']));

                    $objDrawing = new \PhpOffice\PhpSpreadsheet\Worksheet\Drawing();
                    $objDrawing->setName('photo2');
                    $objDrawing->setDescription('photo2');
                    $objDrawing->setPath($conf::$upload_path  . str_replace(' ', '%20', $row['photo2']));
                    $objDrawing->setCoordinates('C' . $i);
                    $objDrawing->setWidthAndHeight(100, 100);
                    $objDrawing->setResizeProportional(true);
                    $objDrawing->setOffsetX(15);
                    $objDrawing->setOffsetY(30);
                    $objDrawing->setWorksheet($sheet);

                    $sheet->getRowDimension($i)->setRowHeight(120);

                    $sheet->getStyle('C'. $i)->applyFromArray($center_style);
                }

                if($row['photo3'] != '')
                {
                    grab_image(str_replace(' ', '%20', $row['photo3']), $conf::$upload_path . str_replace(' ', '%20', $row['photo3']));

                    $objDrawing = new \PhpOffice\PhpSpreadsheet\Worksheet\Drawing();
                    $objDrawing->setName('photo3');
                    $objDrawing->setDescription('photo3');
                    $objDrawing->setPath($conf::$upload_path  . str_replace(' ', '%20', $row['photo3']));
                    $objDrawing->setCoordinates('D' . $i);
                    $objDrawing->setWidthAndHeight(100, 100);
                    $objDrawing->setResizeProportional(true);
                    $objDrawing->setOffsetX(15);
                    $objDrawing->setOffsetY(30);
                    $objDrawing->setWorksheet($sheet);

                    $sheet->getRowDimension($i)->setRowHeight(120);

                    $sheet->getStyle('D'. $i)->applyFromArray($center_style);
                }

                $sheet->setCellValue('E'. $i, $row['code']);
                $sheet->getStyle('E'. $i)->getAlignment()->setWrapText(true);

                $sheet->setCellValue('F'. $i, $row['brief'] . "\n" . $row['listing']);
                $sheet->getStyle('F'. $i)->getAlignment()->setWrapText(true);

                $sheet->setCellValue('G' . $i, $row['qty']);
                $sheet->getStyle('G'. $i)->applyFromArray($center_style);

                $price = "";
                if($row['product'] != false) {
                    if($row['product']['price_ntd'])
                        $price = $row['product']['price_ntd'];

                    if($row['qty'] != '' && $price != '') {
                        $total_price_ntd = $total_price_ntd + $price * $row['qty'];
                    }
                }

                $sheet->setCellValue('H' . $i, $price);
                $sheet->getStyle('H'. $i)->applyFromArray($center_style);

                $amount = "";
                if($row['qty'] != '' && $price != '') {
                    $amount = $price * $row['qty'];
                }

                $sheet->setCellValue('I' . $i, $amount);
                $sheet->getStyle('I'. $i)->applyFromArray($center_style);

                $sheet->setCellValue('J' . $i, '');

                $vendor = "";
                if($row['shipping_vendor'] == 'ssit')
                    $vendor = "盛盛";
                if($row['shipping_vendor'] == 'cfs')
                    $vendor = "卡菲斯";
                $sheet->setCellValue('K' . $i, $vendor);
                $sheet->getStyle('K'. $i)->applyFromArray($center_style);

                $sheet->setCellValue('L' . $i, $row['shipping_way']);
                $sheet->getStyle('L'. $i)->applyFromArray($center_style);

                $i++;
            }

            // footer_top
            $sheet->setCellValue('B' . $i, '稅');
            $sheet->getStyle('B'. $i)->applyFromArray($center_style);

            if($total_price_ntd != '')
                $sheet->setCellValue('C' . $i, $total_price_ntd * 0.05);
     
            $sheet->setCellValue('H' . $i, '5%');
            $sheet->getStyle('H'. $i)->applyFromArray($center_style);
            $i++;

            $sheet->setCellValue('B' . $i, '總計');
            $sheet->getStyle('B'. $i)->applyFromArray($center_style);

            if($total_price_ntd != '')
                $sheet->setCellValue('C' . $i, $total_price_ntd * 1.05);

            $sheet->setCellValue('H' . $i, '含稅');
            $sheet->getStyle('H'. $i)->applyFromArray($center_style);
            $i++;

            // footer_middle
            $sheet->setCellValue('B' . $i, '每箱請貼上麥頭:');
            $i++;
            $sheet->setCellValue('B' . $i, 'FELIIX' . "\n" . '訂單號碼  專案名稱' . "\n" . 'C/NO. 品牌縮寫');
            $sheet->getStyle('B'. $i)->applyFromArray($center_style);
            $sheet->getStyle('B'. $i)->getAlignment()->setWrapText(true);
            $i++;

            $sheet->setCellValue('B' . $i, '請寄到卡菲斯:');
            $i++;
            $sheet->setCellValue('B' . $i, '116台北市文山區羅斯福路六段142巷82號一樓' . "\n" . '電話：02-2935-1589' . "\n" . '(寄件人: 盛盛國際 林巧雯)');
            $sheet->getStyle('B'. $i)->getAlignment()->setWrapText(true);
            $i++;

            $sheet->setCellValue('B' . $i, '請寄到盛盛:');
            $i++;
            $sheet->setCellValue('B' . $i, '50089 台灣彰化市金馬路1段80之1號' . "\n" . '電話：04-7274745' . "\n" . '(寄件人: 盛盛國際 林巧雯)');
            $sheet->getStyle('B'. $i)->getAlignment()->setWrapText(true);
            $i++;
        
            $sheet->setCellValue('B' . $i, '請貼上菲律賓收件人:');
            $i++;
            $sheet->setCellValue('B' . $i, 'FELIIX Inc.' . "\n" . '664 7th Avenue corner, 7th St, Caloocan, 1405 Metro Manila' . "\n" . 'Contact person: KRISTEL TAN' . "\n" . 'Mobile: 0917-625-1198');
            $sheet->getStyle('B'. $i)->getAlignment()->setWrapText(true);
            $i++;

            // footer_bottom
            $sheet->setCellValue('B' . $i, '訂購人:');  $sheet->setCellValue('C' . $i, '林巧雯  Ariel Lin');  
            $sheet->getStyle('B'. $i)->applyFromArray($right_style);
            $i++;
            $sheet->setCellValue('B' . $i, '公司名稱: ');  $sheet->setCellValue('C' . $i, '盛盛國際有限公司 SSIT INC.');  
            $sheet->getStyle('B'. $i)->applyFromArray($right_style);

            // invoice
            $objDrawing = new \PhpOffice\PhpSpreadsheet\Worksheet\Drawing();
            $objDrawing->setName('invoice');
            $objDrawing->setDescription('invoice');
            $objDrawing->setPath($conf::$upload_path ."invoice.png");
            $objDrawing->setCoordinates('G' . $i);
            $objDrawing->setWidthAndHeight(100, 100);
            $objDrawing->setResizeProportional(true);
            $objDrawing->setOffsetX(15);
            $objDrawing->setOffsetY(30);
            $objDrawing->setWorksheet($sheet);

            $sheet->getRowDimension($i)->setRowHeight(120);

            $sheet->getStyle('D'. $i)->applyFromArray($center_style);

            
            $i++;
            $sheet->setCellValue('B' . $i, '地址:');  $sheet->setCellValue('C' . $i, '50089 台灣彰化市金馬路1段80之1號');  
            $sheet->getStyle('B'. $i)->applyFromArray($right_style);
            $i++;
            $sheet->setCellValue('C' . $i, 'NO.80-1, SEC.1, JINMA RD., CHANGHUA CITY, 50089, TAIWAN (R.O.C.)');  
            $i++;
            $sheet->setCellValue('B' . $i, '電話:');  $sheet->setCellValue('C' . $i, '04-7274745');  
            $sheet->getStyle('B'. $i)->applyFromArray($right_style);
            $i++;
            $sheet->setCellValue('B' . $i, '傳真:');  $sheet->setCellValue('C' . $i, '04-7286267');  
            $sheet->getStyle('B'. $i)->applyFromArray($right_style);
            $i++;
            $sheet->setCellValue('B' . $i, '統一編號:');  $sheet->setCellValue('C' . $i, '53474792');  
            $sheet->getStyle('B'. $i)->applyFromArray($right_style);
            $i++;

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


function GetConfirmText($loc)
{
    $location = "";
    switch ($loc) {
        case "C":
            $location = "Confirmed";
            break;
        case "N":
            $location = "Not Yet Confirmed";
            break;
        case "D":
            $location = "Deleted";
            break;
        case "W":
            $location = "Waiting Notes from TW";
            break;
        case "F":
            $location = "For Approval";
            break;
        case "A":
            $location = "Approved";
            break;
        case "R":
            $location = "Rejected";
            break;
        case "O":
            $location = "Ordered";
            break;
        case "E":
            $location = "Canceled";
            break;
        default:
            $location = "";
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

function GetProduct($id, $db){
    $query = "SELECT * FROM product_category WHERE id = :id";
    $stmt = $db->prepare($query);
    $stmt->bindParam(':id', $id);
    $stmt->execute();
    $row = $stmt->fetch(PDO::FETCH_ASSOC);
    return $row;
}

?>