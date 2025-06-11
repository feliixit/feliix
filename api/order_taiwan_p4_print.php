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
$order_type = (isset($_POST['order_type']) ?  $_POST['order_type'] : '');
$serial_name = (isset($_POST['serial_name']) ?  $_POST['serial_name'] : '');
$od_name = (isset($_POST['od_name']) ?  $_POST['od_name'] : '');
$project_name = (isset($_POST['project_name']) ?  $_POST['project_name'] : '');
$url = (isset($_POST['url']) ?  $_POST['url'] : '');

$kind = (isset($_POST['kind']) ?  $_POST['kind'] : 'PROJECT');
$link = (isset($_POST['link']) ?  $_POST['link'] : '');

$items = (isset($_POST['items']) ?  $_POST['items'] : '[]');
$items_array = json_decode($items,true);

$items_str = implode(",", $items_array);

$serial_mapping = (isset($_POST['serial']) ?  $_POST['serial'] : '[]');
$mapping  = json_decode($serial_mapping,true);

$conf = new Conf();

$user_id = 0;

// if jwt is not empty
if($jwt){
 
    // if decode succeed, show user details
    try {
 
        // decode jwt
        $decoded = JWT::decode($jwt, $key, array('HS256'));
        $GLOBALS["user_id"] = $decoded->data->id;

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
            `status`,
            pid
            FROM od_item
            WHERE status <> -1 and od_id=$id and id in ($items_str)";
            

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

        $status = $row['status'];

        $pid = $row['pid'];

        $notes = GetNotes($row['id'], $db);
        $notes_a = GetNotesA($row['id'], $db);

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
        "status" => $status,
        "confirm_text" => $confirm_text,
        "pid" => $pid,
        "notes" => $notes,
            "notes_a" => $notes_a,
            
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

            $normal_style = array(
                'alignment' => array(
                    'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_LEFT,
                    'vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_TOP,
                )
            );

            $order_style = array(
                'font' => array(
                    'bold' => true,
                  ),
                'alignment' => array(
                    'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
                    'vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER,
                ),
                'fill' => array(
                'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                'startColor' => array('argb' => '94BABB')
            )
            );

            $serial_style = array(
                'font' => array(
                    'bold' => true,
                  ),
                'alignment' => array(
                    'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
                    'vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER,
                ),
                'fill' => array(
                'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                'startColor' => array('argb' => 'D7E7E8')
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

            $sheet->setCellValue('A1', $order_type);
            $sheet->mergeCells('A1:B1');
            $sheet->getStyle('A1')->applyFromArray($order_style);

            $sheet->setCellValue('C1', $serial_name . " " . $od_name);
            $sheet->mergeCells('C1:E1');
            $sheet->getCell('C1')->getHyperlink()->setUrl($url);
            $sheet->getStyle('C1')->applyFromArray($serial_style);

            $sheet->setCellValue('G1', $kind);
            $sheet->getStyle('G1')->applyFromArray($order_style);

            $sheet->setCellValue('H1', $project_name);
            $sheet->mergeCells('H1:J1');
            $sheet->getCell('H1')->getHyperlink()->setUrl($link);
            $sheet->getStyle('H1')->applyFromArray($serial_style);

            $sheet->getStyle('A2:I300')->getAlignment()->setHorizontal('center');
            $sheet->getStyle('A2:I300')->getAlignment()->setVertical('center');

            $sheet->getStyle('J2:J2')->getAlignment()->setHorizontal('center');
            $sheet->getStyle('J2:J2')->getAlignment()->setVertical('center');
            $sheet->getStyle('J2:J300')->getAlignment()->setHorizontal('left');
            $sheet->getStyle('J2:J300')->getAlignment()->setVertical('top');

            $sheet->getStyle('K2:T300')->getAlignment()->setHorizontal('center');
            $sheet->getStyle('K2:T300')->getAlignment()->setVertical('center');


            $sheet->setCellValue('A2', '#');
            // $sheet->setCellValue('B2', 'Preliminary');
            // $sheet->setCellValue('C2', 'For Approval');
            // $sheet->setCellValue('D2', 'Approved');
            $sheet->setCellValue('B2', 'Status');
            $sheet->setCellValue('C2', 'Brand');
            $sheet->setCellValue('D2', 'Photo 1');
            $sheet->setCellValue('E2', 'Photo 2');
            $sheet->setCellValue('F2', 'Photo 3');
            $sheet->setCellValue('G2', 'Description');
            $sheet->setCellValue('H2', 'Qty Needed');
            $sheet->setCellValue('I2', 'Backup Qty');
            // $sheet->setCellValue('M2', 'Amount');
            // $sheet->setCellValue('N2', 'Date Needed by Client');
            // $sheet->setCellValue('O2', 'Shipping Way');
            // $sheet->setCellValue('P2', 'ETA');
            // $sheet->setCellValue('Q2', 'Arrival Date');
            // $sheet->setCellValue('R2', 'Warehouse In Charge');
            // $sheet->setCellValue('S2', 'Testing');
            // $sheet->setCellValue('T2', 'Delivery');
            // $sheet->setCellValue('M2', 'Date Needed by Client');
            // $sheet->setCellValue('N2', 'Shipping Way');
            // $sheet->setCellValue('O2', 'Date Send');
            // $sheet->setCellValue('P2', 'ETA');
            // $sheet->setCellValue('Q2', 'Arrival Date');
            // $sheet->setCellValue('R2', 'Warehouse In Charge');
            // $sheet->setCellValue('S2', 'Testing');
            // $sheet->setCellValue('T2', 'Delivery');
            $sheet->setCellValue('J2', 'Date Needed');
            $sheet->setCellValue('K2', 'Notes');
            $sheet->setCellValue('L2', 'Notes (Only for Approved Stage)');

            $sheet->getColumnDimension('A')->setWidth(4.82);
            // $sheet->getColumnDimension('B')->setWidth(12.82);
            // $sheet->getColumnDimension('C')->setWidth(12.82);
            // $sheet->getColumnDimension('D')->setWidth(12.82);
            $sheet->getColumnDimension('B')->setWidth(22.82);
            $sheet->getColumnDimension('C')->setWidth(13.82);
            $sheet->getColumnDimension('D')->setWidth(18.82);
            $sheet->getColumnDimension('E')->setWidth(18.82);
            $sheet->getColumnDimension('F')->setWidth(18.82);
            $sheet->getColumnDimension('G')->setWidth(40.82);
            $sheet->getColumnDimension('H')->setWidth(15.82);
            $sheet->getColumnDimension('I')->setWidth(15.82);
            // $sheet->getColumnDimension('M')->setWidth(15.82);
            // $sheet->getColumnDimension('N')->setWidth(20.82);
            // $sheet->getColumnDimension('O')->setWidth(22.82);
            // $sheet->getColumnDimension('P')->setWidth(13.82);
            // $sheet->getColumnDimension('Q')->setWidth(13.82);
            // $sheet->getColumnDimension('R')->setWidth(30.82);
            // $sheet->getColumnDimension('S')->setWidth(30.82);
            // $sheet->getColumnDimension('T')->setWidth(30.82);
            // $sheet->getColumnDimension('M')->setWidth(20.82);
            // $sheet->getColumnDimension('N')->setWidth(22.82);
            // $sheet->getColumnDimension('O')->setWidth(13.82);
            // $sheet->getColumnDimension('P')->setWidth(13.82);
            // $sheet->getColumnDimension('Q')->setWidth(13.82);
            $sheet->getColumnDimension('J')->setWidth(30.82);
            $sheet->getColumnDimension('K')->setWidth(30.82);
            $sheet->getColumnDimension('L')->setWidth(30.82);

            $i = 3;
            foreach($merged_results as $row)
            {
                // find id to serial in $mapping
                $id = $row['id'];
                $sn = "";
                foreach($mapping as $m)
                {
                    if($m['id'] == $id)
                    {
                        $sn = $m['serial_number'];
                        break;
                    }
                }
                $sheet->setCellValue('A' . $i, $sn);
                // $sheet->setCellValue('B' . $i, $row['status'] <= 1 ? '●' : '');
                // $sheet->getStyle('B'. $i)->applyFromArray($center_style);
                // $sheet->setCellValue('C' . $i, $row['status'] == 2 ? '●' : '');
                // $sheet->getStyle('C'. $i)->applyFromArray($center_style);
                // $sheet->setCellValue('D' . $i, $row['status'] >= 3 ? '●' : '');
                // $sheet->getStyle('D'. $i)->applyFromArray($center_style);

                
                    
                $sheet->setCellValue('B' . $i, $row['confirm_text']);
                $sheet->getStyle('B'. $i)->applyFromArray($center_style);

                if($row['brand_other'] != 'OTHER')
                    $sheet->setCellValue('C' . $i, $row['brand']);
                else
                    $sheet->setCellValue('C' . $i, $row['brand_other']);
                $sheet->getStyle('C'. $i)->applyFromArray($center_style);

                if($row['photo1'] != '')
                {
                    grab_image(str_replace(' ', '%20', $row['photo1']), $conf::$upload_path . preg_replace('/[^A-Za-z0-9]/', '', $row['photo1']));

                    $objDrawing = new \PhpOffice\PhpSpreadsheet\Worksheet\Drawing();
                    $objDrawing->setName('photo1');
                    $objDrawing->setDescription('photo1');
                    $objDrawing->setPath($conf::$upload_path  . preg_replace('/[^A-Za-z0-9]/', '', $row['photo1']));
                    $objDrawing->setCoordinates('D' . $i);
                    $objDrawing->setWidthAndHeight(100, 100);
                    $objDrawing->setResizeProportional(true);
                    $objDrawing->setOffsetX(15);
                    $objDrawing->setOffsetY(30);
                    $objDrawing->setWorksheet($sheet);

                    $sheet->getRowDimension($i)->setRowHeight(120);

                    $sheet->getStyle('D'. $i)->applyFromArray($center_style);
                }

                if($row['photo2'] != '')
                {
                    grab_image(str_replace(' ', '%20', $row['photo2']), $conf::$upload_path . preg_replace('/[^A-Za-z0-9]/', '', $row['photo2']));

                    $objDrawing = new \PhpOffice\PhpSpreadsheet\Worksheet\Drawing();
                    $objDrawing->setName('photo2');
                    $objDrawing->setDescription('photo2');
                    $objDrawing->setPath($conf::$upload_path  . preg_replace('/[^A-Za-z0-9]/', '', $row['photo2']));
                    $objDrawing->setCoordinates('E' . $i);
                    $objDrawing->setWidthAndHeight(100, 100);
                    $objDrawing->setResizeProportional(true);
                    $objDrawing->setOffsetX(15);
                    $objDrawing->setOffsetY(30);
                    $objDrawing->setWorksheet($sheet);

                    $sheet->getRowDimension($i)->setRowHeight(120);

                    $sheet->getStyle('E'. $i)->applyFromArray($center_style);
                }

                if($row['photo3'] != '')
                {
                    grab_image(str_replace(' ', '%20', $row['photo3']), $conf::$upload_path . preg_replace('/[^A-Za-z0-9]/', '', $row['photo3']));

                    $objDrawing = new \PhpOffice\PhpSpreadsheet\Worksheet\Drawing();
                    $objDrawing->setName('photo3');
                    $objDrawing->setDescription('photo3');
                    $objDrawing->setPath($conf::$upload_path  . preg_replace('/[^A-Za-z0-9]/', '', $row['photo3']));
                    $objDrawing->setCoordinates('F' . $i);
                    $objDrawing->setWidthAndHeight(100, 100);
                    $objDrawing->setResizeProportional(true);
                    $objDrawing->setOffsetX(15);
                    $objDrawing->setOffsetY(30);
                    $objDrawing->setWorksheet($sheet);

                    $sheet->getRowDimension($i)->setRowHeight(120);

                    $sheet->getStyle('F'. $i)->applyFromArray($center_style);
                }
                
                $sheet->setCellValue('G'. $i, "ID: ". ($row['pid'] == 0 ? "" : $row['pid']) . "\n" . $row['code'] . "\n" . $row['brief'] . "\n" . $row['listing']);
                $sheet->getStyle('G'. $i)->getAlignment()->setWrapText(true);
                $sheet->getStyle('G'. $i)->applyFromArray($normal_style);

                $sheet->setCellValue('H' . $i, $row['qty'] . " " . $row['unit']);
                $sheet->getStyle('H'. $i)->applyFromArray($center_style);

                $sheet->setCellValue('I' . $i, $row['backup_qty'] . " " . $row['unit']);
                $sheet->getStyle('I'. $i)->applyFromArray($center_style);

                // $sheet->setCellValue('M' . $i, ($row['srp'] != '' ? "₱ " . $row['srp'] : ''));
                // $sheet->getStyle('M'. $i)->applyFromArray($center_style);

                $sheet->setCellValue('J' . $i,  $row['date_needed']);
                $sheet->getStyle('J'. $i)->applyFromArray($center_style);

                // $sheet->setCellValue('N' . $i,  $row['shipping_way'] . "\n" . $row['shipping_number']);
                // $sheet->getStyle('N'. $i)->applyFromArray($center_style);

                // $sheet->setCellValue('O' . $i,  $row['date_send']);
                // $sheet->getStyle('O'. $i)->applyFromArray($center_style);

                // $sheet->setCellValue('P' . $i,  $row['eta']);
                // $sheet->getStyle('P'. $i)->applyFromArray($center_style);

                // $sheet->setCellValue('Q' . $i,  $row['arrive']);
                // $sheet->getStyle('Q'. $i)->applyFromArray($center_style);

                // $sheet->setCellValue('R'. $i, "Confirm Arrival: " . ($row['charge'] == 1 ? 'Y' : '').  "\n" . "Remarks: " . $row['remark'] );
                // $sheet->getStyle('R'. $i)->getAlignment()->setWrapText(true);
                // $sheet->getStyle('R'. $i)->applyFromArray($center_style);

                // //$sheet->setCellValue('R'. $i, "Assignee: " . $row['test'] . "\n" . "Testing Result is Normal: " . ($row['check_t'] == 1 ? 'Y' : '').  "\n" . "Remarks: " . $row['remark_t'] );
                $richText = new \PhpOffice\PhpSpreadsheet\RichText\RichText();
                foreach($row['notes'] as $note)
                {
                    $richText->createText("•" . $note['message'] . "\n");

                    $attach = $note['attachs'];
                    $att_str = "";
                    if(count($attach) > 0)
                    {
                        foreach($attach as $a)
                        {
                            $att_str .= $a['filename'] . "\n";
                        }
                    }

                    if($att_str != "")
                    {
                        $payable = $richText->createTextRun($att_str);
                        $payable->getFont()->setBold(true);
                        //$payable->getFont()->setItalic(true);
                        $payable->getFont()->getColor()->setARGB("25A2B8");
                    }

                    $richText->createText("(" . $note['username'] . " at " . $note['created_at']  . ")\n\n");
                }
                
                $sheet->setCellValue("K". $i, $richText );
                $sheet->getStyle('K'. $i)->getAlignment()->setWrapText(true);
                $sheet->getStyle('K'. $i)->applyFromArray($normal_style);

                $richText_a = new \PhpOffice\PhpSpreadsheet\RichText\RichText();
                foreach($row['notes_a'] as $note)
                {
                    $richText_a->createText("•" . $note['message'] . "\n");

                    $attach = $note['attachs'];
                    $att_str = "";
                    if(count($attach) > 0)
                    {
                        foreach($attach as $a)
                        {
                            $att_str .= $a['filename'] . "\n";
                        }
                    }

                    if($att_str != "")
                    {
                        $payable = $richText_a->createTextRun($att_str);
                        $payable->getFont()->setBold(true);
                        //$payable->getFont()->setItalic(true);
                        $payable->getFont()->getColor()->setARGB("25A2B8");
                    }

                    $richText_a->createText("(" . $note['username'] . " at " . $note['created_at']  . ")\n\n");
                }
               
               
                foreach($row['notes_a'] as $note)
                {
                    $notes_a .= "•" . $note['message'] . "\n(" .  $note['username'] . " at " . $note['created_at']  . ")\n\n";
                }
              
                $sheet->setCellValue('L'. $i, $richText_a );
                $sheet->getStyle('L'. $i)->getAlignment()->setWrapText(true);
                $sheet->getStyle('L'. $i)->applyFromArray($normal_style);

                // //$sheet->setCellValue('S'. $i, "Assignee: " . $row['delivery'] . "\n" . "Delivery is OK: " . ($row['check_d'] == 1 ? 'Y' : '').  "\n" . "Remarks: " . $row['remark_d'] );
                // $sheet->setCellValue('T'. $i, "Delivery is OK: " . ($row['check_d'] == 1 ? 'Y' : '').  "\n" . "Remarks: " . $row['remark_d'] );
                // $sheet->getStyle('T'. $i)->getAlignment()->setWrapText(true);
                // $sheet->getStyle('T'. $i)->applyFromArray($center_style);

                // $richText = new \PhpOffice\PhpSpreadsheet\RichText\RichText();
                // $richText->createText('This invoice is ');
                // $payable = $richText->createTextRun('payable within thirty days after the end of the month');
                // $payable->getFont()->setBold(true);
                // $payable->getFont()->setItalic(true);
                // $payable->getFont()->setColor( new \PhpOffice\PhpSpreadsheet\Style\Color( \PhpOffice\PhpSpreadsheet\Style\Color::COLOR_DARKGREEN ) );
                // $richText->createText(', unless specified otherwise on the invoice.');
                // $sheet->setCellValue('M'. $i, $richText );

                $i++;
            }
        
            $sheet->getStyle('A2:' . 'L2')->getFont()->setBold(true);
            $sheet->getStyle('A2:' . 'L' . --$i)->applyFromArray($styleArray);

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


function GetNotes($id, $db){
    $query = "
            SELECT n.id,
                n.status,
                n.message,
                n.create_id,
                u.username,
                n.created_at
            FROM   od_message n
            left join user u on n.create_id = u.id
            WHERE  n.item_id = " . $id . "
            ORDER BY n.id
    ";

    // prepare the query
    $stmt = $db->prepare($query);
    $stmt->execute();

    $merged_results = [];

    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        $id = $row['id'];
        $status = $row['status'];
        $message = $row['message'];
        $create_id = $row['create_id'];
        $username = $row['username'];

        $created_at = $row['created_at'];
      
        $attachs = [];
        $got_it = [];
        $i_got_it = false;

        $attachs = GetAttach($id, $db);
        $got_it = GetGotIt($id, $db);

        foreach ($got_it as $g) {
            if ($g['uid'] == $GLOBALS["user_id"]) {
                $i_got_it = true;
                break;
            }
        }

        if($GLOBALS["user_id"] == $row["create_id"])
            $i_got_it = true;
    
        $merged_results[] = array(
            "id" => $id,
            "status" => $status,
            "message" => $message,
            "create_id" => $create_id,
            "username" => $username,
            "created_at" => $created_at,
            "attachs" => $attachs,
            "got_it" => $got_it,
            "i_got_it" => $i_got_it,
        );
    }

    return $merged_results;
}



function GetAttach($id, $db)
{
    $sql = "select COALESCE(filename, '') filename, COALESCE(gcp_name, '') gcp_name
            from gcp_storage_file where batch_id = " . $id . " and batch_type = 'od_message' 
            order by created_at ";

    $stmt = $db->prepare($sql);
    $stmt->execute();

    $result = [];

    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        $result[] = array(
            "filename" => $row['filename'],
            "gcp_name" => $row['gcp_name'],
        );
    }

    return $result;
}


function GetGotIt($msg_id, $db)
{
    $sql = "select  u.id uid, u.username username
            from od_got_it g
            LEFT JOIN user u ON u.id = g.create_id
            where g.message_id = " . $msg_id . " order by g.created_at";

    $stmt = $db->prepare($sql);
    $stmt->execute();

    $got_it = [];

    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        $got_it[] = array(
            "uid" => $row['uid'],
            "username" => $row['username'],
        );
    }

    return $got_it;
}


function GetNotesA($id, $db){
    $query = "
            SELECT n.id,
                n.status,
                n.message,
                n.create_id,
                u.username,
                n.created_at
            FROM   od_message_a n
            left join user u on n.create_id = u.id
            WHERE  n.item_id = " . $id . "
            ORDER BY n.id
    ";

    // prepare the query
    $stmt = $db->prepare($query);
    $stmt->execute();

    $merged_results = [];

    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        $id = $row['id'];
        $status = $row['status'];
        $message = $row['message'];
        $create_id = $row['create_id'];
        $username = $row['username'];

        $created_at = $row['created_at'];
      
        $attachs = [];
        $got_it = [];
        $i_got_it = false;

        $attachs = GetAttachA($id, $db);
        $got_it = GetGotItA($id, $db);

        foreach ($got_it as $g) {
            if ($g['uid'] == $GLOBALS["user_id"]) {
                $i_got_it = true;
                break;
            }
        }

        if($GLOBALS["user_id"] == $row["create_id"])
            $i_got_it = true;
    
        $merged_results[] = array(
            "id" => $id,
            "status" => $status,
            "message" => $message,
            "create_id" => $create_id,
            "username" => $username,
            "created_at" => $created_at,
            "attachs" => $attachs,
            "got_it" => $got_it,
            "i_got_it" => $i_got_it,
        );
    }

    return $merged_results;
}



function GetAttachA($id, $db)
{
    $sql = "select COALESCE(filename, '') filename, COALESCE(gcp_name, '') gcp_name
            from gcp_storage_file where batch_id = " . $id . " and batch_type = 'od_message_a' 
            order by created_at ";

    $stmt = $db->prepare($sql);
    $stmt->execute();

    $result = [];

    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        $result[] = array(
            "filename" => $row['filename'],
            "gcp_name" => $row['gcp_name'],
        );
    }

    return $result;
}


function GetGotItA($msg_id, $db)
{
    $sql = "select  u.id uid, u.username username
            from od_got_it_a g
            LEFT JOIN user u ON u.id = g.create_id
            where g.message_id = " . $msg_id . " order by g.created_at";

    $stmt = $db->prepare($sql);
    $stmt->execute();

    $got_it = [];

    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        $got_it[] = array(
            "uid" => $row['uid'],
            "username" => $row['username'],
        );
    }

    return $got_it;
}


?>