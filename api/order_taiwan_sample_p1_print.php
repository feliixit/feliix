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

 
// get posted data
$data = json_decode(file_get_contents("php://input"));
 
// get jwt
$jwt = (isset($_COOKIE['jwt']) ?  $_COOKIE['jwt'] : null);

$id = (isset($_POST['id']) ?  $_POST['id'] : 0);


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
            backup_qty,
            unit,
            srp,
            date_needed,
            shipping_way,
            shipping_number,
            eta,
            date_send,
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
            which_pool,
            as_sample,
            `status`,
            pid
            FROM od_item
            WHERE status <> -1 and od_id=$id";
            

        $query = $query . " order by ABS(sn) ";

        $stmt = $db->prepare($query);
        $stmt->execute();

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
        $brand = $row['brand'];
        $brand_other = $row['brand_other'];
        $photo1 = $row['photo1'];
        $photo2 = $row['photo2'];
        $photo3 = $row['photo3'];
        $code = $row['code'];
        $brief = $row['brief'];
        $listing = $row['listing'];
        $qty = $row['qty'];
        $backup_qty = $row['backup_qty'];
        $unit = $row['unit'];
        $srp = $row['srp'];
        $date_needed = $row['date_needed'];
        $shipping_way = $row['shipping_way'];
        $shipping_number = $row['shipping_number'];
        $eta = $row['eta'];
        $date_send = $row['date_send'];
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

        $which_pool = $row['which_pool'];
        $as_sample = $row['as_sample'];

        $status = $row['status'];

        $pid = $row['pid'];
    

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
        "backup_qty" => $backup_qty,
        "unit" => $unit,
        "srp" => $srp,
        "date_needed" => $date_needed,
        "shipping_way" => $shipping_way,
        "shipping_number" => $shipping_number,
        "eta" => $eta,
        "date_send" => $date_send,
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
        "which_pool" => $which_pool,
        "as_sample" => $as_sample,
        "status" => $status,
        "confirm_text" => $confirm_text,
        "pid" => $pid
            
        );
        }
          
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

            $spreadsheet = new Spreadsheet();

            $spreadsheet->getProperties()->setCreator('PhpOffice')
                    ->setLastModifiedBy('PhpOffice')
                    ->setTitle('Office 2007 XLSX Test Document')
                    ->setSubject('Office 2007 XLSX Test Document')
                    ->setDescription('PhpOffice')
                    ->setKeywords('PhpOffice')
                    ->setCategory('PhpOffice');

            $sheet = $spreadsheet->getActiveSheet();
            $sheet->setTitle("Overview");


            $sheet->getStyle('A1:H300')->getAlignment()->setHorizontal('center');
            $sheet->getStyle('A1:H300')->getAlignment()->setVertical('center');

            $sheet->getStyle('I1:I1')->getAlignment()->setHorizontal('center');
            $sheet->getStyle('I1:I1')->getAlignment()->setVertical('center');
            $sheet->getStyle('I2:I300')->getAlignment()->setHorizontal('left');
            $sheet->getStyle('I2:I300')->getAlignment()->setVertical('top');

            $sheet->getStyle('J1:S300')->getAlignment()->setHorizontal('center');
            $sheet->getStyle('J1:S300')->getAlignment()->setVertical('center');


            $sheet->setCellValue('A1', '#');
            $sheet->setCellValue('B1', 'Preliminary');
            //$sheet->setCellValue('C1', 'For Approval');
            $sheet->setCellValue('C1', 'Approved');
            $sheet->setCellValue('D1', 'Status');
            $sheet->setCellValue('E1', 'Brand');
            $sheet->setCellValue('F1', 'Photo 1');
            $sheet->setCellValue('G1', 'Photo 2');
            $sheet->setCellValue('H1', 'Photo 3');
            $sheet->setCellValue('I1', 'Description');
            $sheet->setCellValue('J1', 'Qty Needed');
            $sheet->setCellValue('K1', 'Backup Qty');
           // $sheet->setCellValue('L1', 'Amount');
            $sheet->setCellValue('L1', 'Date Needed');
            $sheet->setCellValue('N1', 'Inventory Remarks');
            $sheet->setCellValue('N1', 'Shipping Way');
            $sheet->setCellValue('O1', 'Date Send');
            $sheet->setCellValue('P1', 'ETA');
            $sheet->setCellValue('Q1', 'Arrival Date');
            $sheet->setCellValue('R1', 'Warehouse In Charge');
            $sheet->setCellValue('S1', 'Testing');
            $sheet->setCellValue('T1', 'Delivery');


            $sheet->getColumnDimension('A')->setWidth(4.82);
            $sheet->getColumnDimension('B')->setWidth(12.82);
            //$sheet->getColumnDimension('C')->setWidth(12.82);
            $sheet->getColumnDimension('C')->setWidth(12.82);
            $sheet->getColumnDimension('D')->setWidth(22.82);
            $sheet->getColumnDimension('E')->setWidth(13.82);
            $sheet->getColumnDimension('F')->setWidth(18.82);
            $sheet->getColumnDimension('G')->setWidth(18.82);
            $sheet->getColumnDimension('H')->setWidth(18.82);
            $sheet->getColumnDimension('I')->setWidth(40.82);
            $sheet->getColumnDimension('J')->setWidth(15.82);
            $sheet->getColumnDimension('K')->setWidth(15.82);
          //  $sheet->getColumnDimension('L')->setWidth(15.82);
            $sheet->getColumnDimension('L')->setWidth(20.82);
            $sheet->getColumnDimension('M')->setWidth(20.82);
            $sheet->getColumnDimension('N')->setWidth(22.82);
            $sheet->getColumnDimension('O')->setWidth(13.82);
            $sheet->getColumnDimension('P')->setWidth(13.82);
            $sheet->getColumnDimension('Q')->setWidth(13.82);
            $sheet->getColumnDimension('R')->setWidth(30.82);
            $sheet->getColumnDimension('S')->setWidth(30.82);
            $sheet->getColumnDimension('T')->setWidth(30.82);


            $i = 2;
            foreach($merged_results as $row)
            {
                $sheet->setCellValue('A' . $i, $i-1);
                $sheet->setCellValue('B' . $i, $row['status'] <= 1 ? '●' : '');
                $sheet->getStyle('B'. $i)->applyFromArray($center_style);
                //$sheet->setCellValue('C' . $i, $row['status'] == 2 ? '●' : '');
                //$sheet->getStyle('C'. $i)->applyFromArray($center_style);
                $sheet->setCellValue('C' . $i, $row['status'] >= 3 ? '●' : '');
                $sheet->getStyle('C'. $i)->applyFromArray($center_style);

                
                    
                $sheet->setCellValue('D' . $i, $row['confirm_text']);
                $sheet->getStyle('D'. $i)->applyFromArray($center_style);

                if($row['brand_other'] != 'OTHER')
                    $sheet->setCellValue('E' . $i, $row['brand']);
                else
                    $sheet->setCellValue('E' . $i, $row['brand_other']);
                $sheet->getStyle('E'. $i)->applyFromArray($center_style);

                if($row['photo1'] != '')
                {
                    grab_image(str_replace(' ', '%20', $row['photo1']), $conf::$upload_path . preg_replace('/[^A-Za-z0-9]/', '', $row['photo1']));

                    $objDrawing = new \PhpOffice\PhpSpreadsheet\Worksheet\Drawing();
                    $objDrawing->setName('photo1');
                    $objDrawing->setDescription('photo1');
                    $objDrawing->setPath($conf::$upload_path  . preg_replace('/[^A-Za-z0-9]/', '', $row['photo1']));
                    $objDrawing->setCoordinates('F' . $i);
                    $objDrawing->setWidthAndHeight(100, 100);
                    $objDrawing->setResizeProportional(true);
                    $objDrawing->setOffsetX(15);
                    $objDrawing->setOffsetY(30);
                    $objDrawing->setWorksheet($sheet);

                    $sheet->getRowDimension($i)->setRowHeight(120);

                    $sheet->getStyle('F'. $i)->applyFromArray($center_style);
                }

                if($row['photo2'] != '')
                {
                    grab_image(str_replace(' ', '%20', $row['photo2']), $conf::$upload_path . preg_replace('/[^A-Za-z0-9]/', '', $row['photo2']));

                    $objDrawing = new \PhpOffice\PhpSpreadsheet\Worksheet\Drawing();
                    $objDrawing->setName('photo2');
                    $objDrawing->setDescription('photo2');
                    $objDrawing->setPath($conf::$upload_path  . preg_replace('/[^A-Za-z0-9]/', '', $row['photo2']));
                    $objDrawing->setCoordinates('G' . $i);
                    $objDrawing->setWidthAndHeight(100, 100);
                    $objDrawing->setResizeProportional(true);
                    $objDrawing->setOffsetX(15);
                    $objDrawing->setOffsetY(30);
                    $objDrawing->setWorksheet($sheet);

                    $sheet->getRowDimension($i)->setRowHeight(120);

                    $sheet->getStyle('G'. $i)->applyFromArray($center_style);
                }

                if($row['photo3'] != '')
                {
                    grab_image(str_replace(' ', '%20', $row['photo3']), $conf::$upload_path . preg_replace('/[^A-Za-z0-9]/', '', $row['photo3']));

                    $objDrawing = new \PhpOffice\PhpSpreadsheet\Worksheet\Drawing();
                    $objDrawing->setName('photo3');
                    $objDrawing->setDescription('photo3');
                    $objDrawing->setPath($conf::$upload_path  . preg_replace('/[^A-Za-z0-9]/', '', $row['photo3']));
                    $objDrawing->setCoordinates('H' . $i);
                    $objDrawing->setWidthAndHeight(100, 100);
                    $objDrawing->setResizeProportional(true);
                    $objDrawing->setOffsetX(15);
                    $objDrawing->setOffsetY(30);
                    $objDrawing->setWorksheet($sheet);

                    $sheet->getRowDimension($i)->setRowHeight(120);

                    $sheet->getStyle('H'. $i)->applyFromArray($center_style);
                }
                
                $sheet->setCellValue('I'. $i, "ID: ". ($row['pid'] == 0 ? "" : $row['pid']) . "\n" . $row['code'] . "\n" . $row['brief'] . "\n" . $row['listing']);
                $sheet->getStyle('I'. $i)->getAlignment()->setWrapText(true);

                $sheet->setCellValue('J' . $i, $row['qty'] . " " . $row['unit']);
                $sheet->getStyle('J'. $i)->applyFromArray($center_style);

                $sheet->setCellValue('K' . $i, $row['backup_qty'] . " " . $row['unit']);
                $sheet->getStyle('K'. $i)->applyFromArray($center_style);

                // $sheet->setCellValue('L' . $i, ($row['srp'] != '' ? "₱ " . $row['srp'] : ''));
                // $sheet->getStyle('L'. $i)->applyFromArray($center_style);

                $sheet->setCellValue('L' . $i,  $row['date_needed']);
                $sheet->getStyle('L'. $i)->applyFromArray($center_style);

                $sheet->setCellValue('M' . $i,  "Which Pool: " . $row['which_pool'] . "\n" . "As Sample: " . $row['as_sample']);
                $sheet->getStyle('M'. $i)->applyFromArray($center_style);
                $sheet->getStyle('M'. $i)->getAlignment()->setWrapText(true);

                $sheet->setCellValue('N' . $i,  $row['shipping_way'] . "\n" . $row['shipping_number']);
                $sheet->getStyle('N'. $i)->applyFromArray($center_style);

                $sheet->setCellValue('O' . $i,  $row['date_send']);
                $sheet->getStyle('O'. $i)->applyFromArray($center_style);

                $sheet->setCellValue('P' . $i,  $row['eta']);
                $sheet->getStyle('P'. $i)->applyFromArray($center_style);

                $sheet->setCellValue('Q' . $i,  $row['arrive']);
                $sheet->getStyle('Q'. $i)->applyFromArray($center_style);

                $sheet->setCellValue('R'. $i, "Confirm Arrival: " . ($row['charge'] == 1 ? 'Y' : '').  "\n" . "Remarks: " . $row['remark'] );
                $sheet->getStyle('R'. $i)->getAlignment()->setWrapText(true);
                $sheet->getStyle('R'. $i)->applyFromArray($center_style);

                //$sheet->setCellValue('R'. $i, "Assignee: " . $row['test'] . "\n" . "Testing Result is Normal: " . ($row['check_t'] == 1 ? 'Y' : '').  "\n" . "Remarks: " . $row['remark_t'] );
                $sheet->setCellValue('S'. $i, "Testing Result is Normal: " . ($row['check_t'] == 1 ? 'Y' : '').  "\n" . "Remarks: " . $row['remark_t'] );
                $sheet->getStyle('S'. $i)->getAlignment()->setWrapText(true);
                $sheet->getStyle('S'. $i)->applyFromArray($center_style);

                //$sheet->setCellValue('S'. $i, "Assignee: " . $row['delivery'] . "\n" . "Delivery is OK: " . ($row['check_d'] == 1 ? 'Y' : '').  "\n" . "Remarks: " . $row['remark_d'] );
                $sheet->setCellValue('T'. $i, "Delivery is OK: " . ($row['check_d'] == 1 ? 'Y' : '').  "\n" . "Remarks: " . $row['remark_d'] );
                $sheet->getStyle('T'. $i)->getAlignment()->setWrapText(true);
                $sheet->getStyle('T'. $i)->applyFromArray($center_style);



                $i++;
            }
        
            $sheet->getStyle('A1:' . 'T1')->getFont()->setBold(true);
            $sheet->getStyle('A1:' . 'T' . --$i)->applyFromArray($styleArray);

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
        case "J":
            $location = "From Warehouse";
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

?>