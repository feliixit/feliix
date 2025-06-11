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
$ids = (isset($_POST['ids']) ?  $_POST['ids'] : '');
$ids_str = $ids;
$brand = (isset($_POST['brand']) ?  $_POST['brand'] : '');

$od_id = $id;

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
            pid,
            v1,
            v2,
            v3,
            v4,
            shipping_way,
            shipping_number,
            shipping_vendor,
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
            `status`
            FROM od_item
            WHERE status <> -1 and od_id=$id and id in ($ids_str)";
            

        $query = $query . " order by ABS(sn) ";

        $stmt = $db->prepare($query);
        $stmt->execute();

        $date_needed_array = [];
        $currecny_array = [];

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
            
            $product = "";
            $product = GetProductMain($row['pid'], $row['v1'], $row['v2'], $row['v3'], $row['v4'], $db);

            $product_currency = GetProductCurrency($row['pid'], $db);
            

            if(in_array($product_currency, $currecny_array) == false)
                $currecny_array[] = $product_currency;


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

            if($date_needed != "" && in_array($date_needed, $date_needed_array) == false)
                $date_needed_array[] = $date_needed;

            $shipping_way = $row['shipping_way'];
            $shipping_number = $row['shipping_number'];
            $shipping_vendor = $row['shipping_vendor'];

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

            if($row['photo1'] == "" && $row['pid'] != "0")
            {
                $query = "SELECT photo1 FROM product_category WHERE id = $row[pid]";
                $stmt2 = $db->prepare($query);
                $stmt2->execute();
                while ($row2 = $stmt2->fetch(PDO::FETCH_ASSOC)) {
                    $photo1 = $row2['photo1'];
                }
            }

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
            "shipping_vendor" => $shipping_vendor,

            "product" => $product,

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
            );
        }

        $currency_string = "";
        sort($currecny_array);
        $currecny_string = implode(' ', $currecny_array);

        $od_name = "";
        $stage_id = 0;
        $serial_name = "";
        $project_name = "";

        $query = "SELECT pm.id, 
                pm.od_name,
                pm.status, 
                pm.task_id,
                pm.order_type,
                pm.task_type stage_id,
                pm.task_id project_id,
                pm.serial_name,
                c_user.username AS created_by, 
                u_user.username AS updated_by,
                DATE_FORMAT(pm.created_at, '%Y-%m-%d %H:%i:%s') created_at, 
                DATE_FORMAT(pm.updated_at, '%Y-%m-%d %H:%i:%s') updated_at
                FROM od_main pm 
              
                LEFT JOIN user c_user ON pm.create_id = c_user.id 
                LEFT JOIN user u_user ON pm.updated_id = u_user.id 
                where pm.status <> -1 ";

        if($id != 0){
            $query .= " and pm.id = $od_id ";
        }

        $query = $query . " order by pm.created_at desc ";


        $stmt = $db->prepare( $query );
        $stmt->execute();

        while($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $od_name = $row['od_name'];
            $task_id = $row['task_id'];
            $stage_id = $row['stage_id'];
            $project_name = GetTaskDetail($task_id, $stage_id, $db);
            $serial_name = $row['serial_name'];

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


            $sheet->setTitle("盛盛訂購單");


            $sheet->getStyle('A1:K300')->getAlignment()->setHorizontal('center');
            $sheet->getStyle('A1:K300')->getAlignment()->setVertical('center');


            $sheet->getColumnDimension('A')->setWidth(17.15);
            $sheet->getColumnDimension('B')->setWidth(20.00);
            $sheet->getColumnDimension('C')->setWidth(35.71);
            $sheet->getColumnDimension('D')->setWidth(40.82);
            $sheet->getColumnDimension('E')->setWidth(50.82);
            $sheet->getColumnDimension('F')->setWidth(20.72);
            $sheet->getColumnDimension('G')->setWidth(20.72);
            $sheet->getColumnDimension('H')->setWidth(25.72);
            $sheet->getColumnDimension('I')->setWidth(20.72);
            $sheet->getColumnDimension('J')->setWidth(20.72);
            $sheet->getColumnDimension('K')->setWidth(20.72);





            // header
            $sheet->setCellValue('B1', '訂          購          單');
            $sheet->mergeCells('B1:J2');
            $sheet->getRowDimension(1)->setRowHeight(28.2);
            $sheet->getRowDimension(2)->setRowHeight(28.2);
            $sheet->getStyle('B1:J2')->getFont()->setSize(48);
            $sheet->getStyle('B1:J2')->getFont()->setBold(true);
            $sheet->getStyle('B1:J2')->getFont()->setName('M+ 1c regular');


            $sheet->getRowDimension(3)->setRowHeight(28.2);


            $sheet->setCellValue('B4', 'To: ' . $brand);
            $sheet->mergeCells('B4:C4');
            $sheet->getRowDimension(4)->setRowHeight(28.2);
            $sheet->getStyle('B4:C4')->getFont()->setSize(24);
            $sheet->getStyle('B4:C4')->getFont()->setBold(true);
            $sheet->getStyle('B4:C4')->getFont()->setName('微軟正黑體');
            $sheet->getStyle('B4:C4')->getAlignment()->setHorizontal('left');


            $sheet->setCellValue('B5', '訂單編號:');
            $sheet->setCellValue('B6', '專案名稱:');
            $sheet->setCellValue('B7', '訂單日期:');
            $sheet->setCellValue('B8', '需求日期:');
            $sheet->getStyle('B5:B8')->getAlignment()->setHorizontal('left');
            $sheet->getStyle('B5:B8')->getFont()->setSize(20);
            $sheet->getStyle('B5:B8')->getFont()->setName('微軟正黑體');
            $sheet->getRowDimension(5)->setRowHeight(28.2);
            $sheet->getRowDimension(6)->setRowHeight(28.2);
            $sheet->getRowDimension(7)->setRowHeight(28.2);
            $sheet->getRowDimension(8)->setRowHeight(28.2);


            $sheet->setCellValue('C5', $serial_name);
            $sheet->mergeCells('C5:D5');
            $sheet->setCellValue('C6', $project_name);
            $sheet->mergeCells('C6:E6');
            $sheet->setCellValue('C7', date("Y/m/d"));
            $sheet->mergeCells('C7:D7');
            $sheet->setCellValue('C8', $date_needed_str);
            $sheet->mergeCells('C8:D8');
            $sheet->getStyle('C5:D8')->getAlignment()->setHorizontal('left');
            $sheet->getStyle('C5:D8')->getFont()->setSize(20);
            $sheet->getStyle('C5:D8')->getFont()->setName('M+ 1c regular');

            $sheet->getStyle('B4:J8')->applyFromArray($bold_border_style);


            $sheet->getRowDimension(9)->setRowHeight(28.2);


            // body title
            $sheet->setCellValue('B10', '項次' . "\n" . 'ITEM NO.');
            $sheet->setCellValue('C10', '圖示' . "\n" . 'IMAGE');
            $sheet->setCellValue('D10', '品名/型號' . "\n" . 'CODE');
            $sheet->setCellValue('E10', '顏色/規格' . "\n" . 'COLOR/SPEC');
            $sheet->setCellValue('F10', '數量' . "\n" . 'QTY');
            $sheet->setCellValue('G10', '單價' . "\n" . 'PRICE' . "\n" . '(' . $currecny_string . ')');
            $sheet->setCellValue('H10', '總價' . "\n" . 'AMOUNT' . "\n" . '(' . $currecny_string . ')');
            $sheet->setCellValue('I10', '交期' . "\n" . 'DELIVERY');
            $sheet->setCellValue('J10', '寄送地址');
            $sheet->setCellValue('K10', '海運或空運');

            $sheet->getStyle('B10:K10')->getFont()->setSize(20);
            $sheet->getStyle('B10:K10')->getFont()->setName('M+ 1c regular');
            $sheet->getStyle('B10:J10')->applyFromArray($boldandthin_border_style);
            $sheet->getStyle('K10:K10')->applyFromArray($bold_border_style);



            $sheet->getStyle('B10')->getAlignment()->setWrapText(true);
            $sheet->getStyle('C10')->getAlignment()->setWrapText(true);
            $sheet->getStyle('D10')->getAlignment()->setWrapText(true);
            $sheet->getStyle('E10')->getAlignment()->setWrapText(true);
            $sheet->getStyle('F10')->getAlignment()->setWrapText(true);
            $sheet->getStyle('G10')->getAlignment()->setWrapText(true);
            $sheet->getStyle('H10')->getAlignment()->setWrapText(true);
            $sheet->getStyle('I10')->getAlignment()->setWrapText(true);
            $sheet->getStyle('J10')->getAlignment()->setWrapText(true);
            $sheet->getStyle('K10')->getAlignment()->setWrapText(true);
            
            // body
            $i = 11;
            $ssit = "";
            $cfs = "";
            $dy = "";

            foreach($merged_results as $row)
            {
                $j = $i - 10;
                $sheet->setCellValue('B'. $i, $j);
                $sheet->getStyle('B'. $i)->getAlignment()->setWrapText(true);

                if($row['photo1'] != '')
                {

                    $row['photo1'] = urlencode($row['photo1']);
                    $row['photo1'] = str_replace('+', '%20', $row['photo1']);

                    grab_image($row['photo1'], $conf::$upload_path . preg_replace('/[^A-Za-z0-9]/', '', $row['photo1']));

                    $objDrawing = new \PhpOffice\PhpSpreadsheet\Worksheet\Drawing();
                    $objDrawing->setName('photo1');
                    $objDrawing->setDescription('photo1');
                    $objDrawing->setPath($conf::$upload_path  . preg_replace('/[^A-Za-z0-9]/', '', $row['photo1']));
                    $objDrawing->setCoordinates('C' . $i);
                    $objDrawing->setWidthAndHeight(100, 100);
                    $objDrawing->setResizeProportional(true);
                    $objDrawing->setOffsetX(80);
                    $objDrawing->setOffsetY(30);
                    $objDrawing->setWorksheet($sheet);

                    $sheet->getRowDimension($i)->setRowHeight(120);

                    $sheet->getStyle('C'. $i)->applyFromArray($center_style);
                }


                $sheet->setCellValue('D'. $i, ($row['pid'] != "0" ? "ID: " . $row['pid'] . "\n" : "") . $row['code']);
                $sheet->getStyle('D'. $i)->getAlignment()->setWrapText(true);

                $sheet->setCellValue('E'. $i, $row['brief'] . "\n" . $row['listing']);
                $sheet->getStyle('E'. $i)->getAlignment()->setWrapText(true);

                $qq = "";
                if($row['qty'] != "")
                {
                    $qq = intval($row['qty']);
                }
                if($row['backup_qty'] != "")
                {
                    $qq += intval($row['backup_qty']);
                }

                $sheet->setCellValue('F' . $i, $qq);
                $sheet->getStyle('F'. $i)->applyFromArray($center_style);


                $price = "";
                if($row['product'] != "") {
                    $price = $row['product'];

                    if($qq != '' && $price != '') {
                        $total_price_ntd = $total_price_ntd + $price * $qq;
                    }
                }

                $sheet->setCellValue('G' . $i, $price);
                $sheet->getStyle('G'. $i)->applyFromArray($center_style);

                $amount = "";
                if($qq != '' && $price != '') {
                    $amount = $price * $qq;
                }

                $sheet->setCellValue('H' . $i, $amount);
                $sheet->getStyle('H'. $i)->applyFromArray($center_style);

                $sheet->setCellValue('I' . $i, '');

                $vendor = "";
                if($row['shipping_vendor'] == 'ssit'){
                    $vendor = "盛盛";
                    $ssit = "1";
                }
                if($row['shipping_vendor'] == 'cfs'){
                    $vendor = "卡菲斯";
                    $cfs = "1";
                }
                if($row['shipping_vendor'] == 'dy'){
                    $vendor = "東渝";
                    $dy = "1";
                }
                $sheet->setCellValue('J' . $i, $vendor);
                $sheet->getStyle('J'. $i)->applyFromArray($center_style);

                $shipway = "";
                if($row['shipping_way'] == 'sea')
                    $shipway = "海運";
                if($row['shipping_way'] == 'air')
                    $shipway = "空運";
                $sheet->setCellValue('K' . $i, $shipway);
                $sheet->getStyle('K'. $i)->applyFromArray($center_style);

                $sheet->getStyle('E' . $i . ':E' . $i)->getAlignment()->setHorizontal('left');
                $sheet->getStyle('G' . $i . ':H' . $i)->getAlignment()->setHorizontal('right');
                $sheet->getStyle('B' . $i . ':K' . $i)->getFont()->setSize(18);
                $sheet->getStyle('B' . $i . ':K' . $i)->getFont()->setName('M+ 1c regular');
                $sheet->getStyle('B' . $i . ':J' . $i)->applyFromArray($boldandthin_border_style1);
                $sheet->getStyle('K' . $i . ':K' . $i)->applyFromArray($bold_border_style);

                $i++;
            }

            // footer_top
            $j = $i + 2;
            $sheet->mergeCells('B' . $i . ':E' . $j );

            $sheet->setCellValue('F' . $i, '小計' . " " . '(' . $currecny_string . ')');
            $sheet->getStyle('F'. $i)->applyFromArray($center_style);
            $sheet->mergeCells('G' . $i . ':J' . $i);

            if($total_price_ntd != '')
                $sheet->setCellValue('G' . $i, $total_price_ntd );

            $sheet->getRowDimension($i)->setRowHeight(28.2);
            $sheet->getStyle('B' . $i . ':K' . $i)->getFont()->setSize(20);
            $sheet->getStyle('B' . $i . ':K' . $i)->getFont()->setName('M+ 1c regular');
            $k = $i;
            $i++;


            $sheet->setCellValue('F' . $i, '稅 5%');
            $sheet->getStyle('F'. $i)->applyFromArray($center_style);
            $sheet->mergeCells('G' . $i . ':J' . $i);

            if($total_price_ntd != '')
                $sheet->setCellValue('G' . $i, $total_price_ntd * 0.05);

            $sheet->getRowDimension($i)->setRowHeight(28.2);
            $sheet->getStyle('B' . $i . ':K' . $i)->getFont()->setSize(20);
            $sheet->getStyle('B' . $i . ':K' . $i)->getFont()->setName('M+ 1c regular');
            $i++;


            $sheet->setCellValue('F' . $i, '總計' . " " . '(' . $currecny_string . ')');
            $sheet->getStyle('F'. $i)->applyFromArray($center_style);
            $sheet->mergeCells('G' . $i . ':J' . $i);

            if($total_price_ntd != '')
                $sheet->setCellValue('G' . $i, $total_price_ntd * 1.05);

            $sheet->getRowDimension($i)->setRowHeight(28.2);
            $sheet->getStyle('B' . $i . ':K' . $i)->getFont()->setSize(20);
            $sheet->getStyle('B' . $i . ':K' . $i)->getFont()->setBold(true);
            $sheet->getStyle('B' . $i . ':K' . $i)->getFont()->setName('M+ 1c regular');
            $sheet->getStyle('B' . $k . ':J' . $i)->applyFromArray($boldandthin_border_style);
            $i++;


            // footer_middle
            $sheet->getRowDimension($i)->setRowHeight(34.2);
            $sheet->setCellValue('B' . $i, '每箱請貼上麥頭:');
            $sheet->mergeCells('B' . $i . ':D' . $i);
            $sheet->getStyle('B' . $i)->getAlignment()->setHorizontal('left');
            $sheet->getStyle('B' . $i)->getFont()->setSize(18);
            $sheet->getStyle('B' . $i)->getFont()->setBold(true);
            $sheet->getStyle('B' . $i)->getFont()->setUnderline('double');
            $sheet->getStyle('B' . $i)->getFont()->setName('微軟正黑體');
            $sheet->getStyle('B' . $i . ':J' . $i)->applyFromArray($bold_border_style1);
            $i++;

            $sheet->getRowDimension($i)->setRowHeight(109.8);

            $short_brand = "";
            if(strtoupper($brand) == 'COLORS')
                $short_brand = "CL";
            if(strtoupper($brand) == 'DANCELIGHT')
                $short_brand = "DL";
            if(strtoupper($brand) == 'ELITES')
                $short_brand = "ET";
            if(strtoupper($brand) == 'EVERLIGHT')
                $short_brand = "EL";
            if(strtoupper($brand) == 'GAMMA')
                $short_brand = "GM";
            if(strtoupper($brand) == 'GENTECH')
                $short_brand = "GT";
            if(strtoupper($brand) == 'GLEDOPTO')
                $short_brand = "GD";
            if(strtoupper($brand) == 'HUANG GONG')
                $short_brand = "HG";
            if(strtoupper($brand) == 'LEDOUX')
                $short_brand = "LD";
            if(strtoupper($brand) == 'ROOSTER')
                $short_brand = "RT";
            if(strtoupper($brand) == 'SASUGAS')
                $short_brand = "SG";
            if(strtoupper($brand) == 'SEEDDESIGN')
                $short_brand = "SD";
            if(strtoupper($brand) == 'SHAN BEN')
                $short_brand = "SB";
            if(strtoupper($brand) == 'SHINE TOP')
                $short_brand = "ST";
            if(strtoupper($brand) == 'TAYAGI')
                $short_brand = "TYG";
            if(strtoupper($brand) == 'TONS')
                $short_brand = "TONS";
            if(strtoupper($brand) == 'WENHUI')
                $short_brand = "WH";
            if(strtoupper($brand) == 'XCELLENT')
                $short_brand = "XL";
            if(strtoupper($brand) == 'YUDA')
                $short_brand = "YD";


            $richText = new \PhpOffice\PhpSpreadsheet\RichText\RichText();
            $payable = $richText->createTextRun('FELIIX');
            $payable->getFont()->setBold(true);
            $payable->getFont()->setSize(22);
            $payable->getFont()->setName('M+ 1c regular');
            $payable = $richText->createTextRun("\n" . $serial_name . " " . $project_name . "\n" . 'C/NO. ' . $short_brand . "1 (2, 3, …)");
            $payable->getFont()->setSize(18);
            $payable->getFont()->setName('M+ 1c regular');
            $sheet->getCell('B' . $i)->setValue($richText);
            $sheet->mergeCells('B' . $i . ':D' . $i);
            $sheet->getStyle('B' . $i . ':J' . $i)->applyFromArray($bold_border_style1);
            $sheet->getStyle('B'. $i)->applyFromArray($center_style);
            $sheet->getStyle('B'. $i)->getAlignment()->setWrapText(true);
            $i++;

            if($cfs == "1"){
                $sheet->getRowDimension($i)->setRowHeight(34.2);
                $sheet->setCellValue('B' . $i, '請寄到卡菲斯:');
                $sheet->mergeCells('B' . $i . ':D' . $i);
                $sheet->getStyle('B' . $i)->getAlignment()->setHorizontal('left');
                $sheet->getStyle('B' . $i)->getFont()->setSize(18);
                $sheet->getStyle('B' . $i)->getFont()->setBold(true);
                $sheet->getStyle('B' . $i)->getFont()->setUnderline('double');
                $sheet->getStyle('B' . $i)->getFont()->setName('微軟正黑體');
                $sheet->getStyle('B' . $i . ':J' . $i)->applyFromArray($bold_border_style1);
                $i++;

                $sheet->getRowDimension($i)->setRowHeight(84);
                $sheet->setCellValue('B' . $i, '116 台北市文山區羅斯福路六段142巷82號一樓' . "\n" . '電話：02-2935-1589' . "\n" . '(寄件人: 盛盛國際 林巧雯)');
                $sheet->mergeCells('B' . $i . ':D' . $i);
                $sheet->getStyle('B' . $i . ':D' . $i)->getAlignment()->setHorizontal('left');
                $sheet->getStyle('B' . $i . ':D' . $i)->getFont()->setSize(18);
                $sheet->getStyle('B' . $i . ':D' . $i)->getFont()->setName('微軟正黑體');
                $sheet->getStyle('B' . $i . ':J' . $i)->applyFromArray($bold_border_style1);
                $sheet->getStyle('B'. $i)->getAlignment()->setWrapText(true);
                $i++;
            }

            if($ssit == "1"){
                $sheet->getRowDimension($i)->setRowHeight(34.2);
                $sheet->setCellValue('B' . $i, '請寄到盛盛:');
                $sheet->mergeCells('B' . $i . ':D' . $i);
                $sheet->getStyle('B' . $i)->getAlignment()->setHorizontal('left');
                $sheet->getStyle('B' . $i)->getFont()->setSize(18);
                $sheet->getStyle('B' . $i)->getFont()->setBold(true);
                $sheet->getStyle('B' . $i)->getFont()->setUnderline('double');
                $sheet->getStyle('B' . $i)->getFont()->setName('微軟正黑體');
                $sheet->getStyle('B' . $i . ':J' . $i)->applyFromArray($bold_border_style1);
                $i++;

                $sheet->getRowDimension($i)->setRowHeight(84);
                $sheet->setCellValue('B' . $i, '50089 台灣彰化市金馬路1段80之1號' . "\n" . '電話：04-7274745' . "\n" . '(寄件人: 盛盛國際 林巧雯)');
                $sheet->mergeCells('B' . $i . ':D' . $i);
                $sheet->getStyle('B' . $i . ':D' . $i)->getAlignment()->setHorizontal('left');
                $sheet->getStyle('B' . $i . ':D' . $i)->getFont()->setSize(18);
                $sheet->getStyle('B' . $i . ':D' . $i)->getFont()->setName('微軟正黑體');
                $sheet->getStyle('B' . $i . ':J' . $i)->applyFromArray($bold_border_style1);
                $sheet->getStyle('B'. $i)->getAlignment()->setWrapText(true);
                $i++;
            }

            if($dy == "1"){
                $sheet->getRowDimension($i)->setRowHeight(34.2);
                $sheet->setCellValue('B' . $i, '請寄到東渝:');
                $sheet->mergeCells('B' . $i . ':D' . $i);
                $sheet->getStyle('B' . $i)->getAlignment()->setHorizontal('left');
                $sheet->getStyle('B' . $i)->getFont()->setSize(18);
                $sheet->getStyle('B' . $i)->getFont()->setBold(true);
                $sheet->getStyle('B' . $i)->getFont()->setUnderline('double');
                $sheet->getStyle('B' . $i)->getFont()->setName('微軟正黑體');
                $sheet->getStyle('B' . $i . ':J' . $i)->applyFromArray($bold_border_style1);
                $i++;

                $sheet->getRowDimension($i)->setRowHeight(109.8);
                $sheet->setCellValue('B' . $i, '249005 新北市八里區文昌路89-3號' . "\n" . '電話：02-2292-3936' . "\n" . '收件人: Mia' . "\n" . '(寄件人: 盛盛國際 林巧雯)');
                $sheet->mergeCells('B' . $i . ':D' . $i);
                $sheet->getStyle('B' . $i . ':D' . $i)->getAlignment()->setHorizontal('left');
                $sheet->getStyle('B' . $i . ':D' . $i)->getFont()->setSize(18);
                $sheet->getStyle('B' . $i . ':D' . $i)->getFont()->setName('微軟正黑體');
                $sheet->getStyle('B' . $i . ':J' . $i)->applyFromArray($bold_border_style1);
                $sheet->getStyle('B'. $i)->getAlignment()->setWrapText(true);
                $i++;
            }


            $sheet->getRowDimension($i)->setRowHeight(34.2);
            $sheet->setCellValue('B' . $i, '請貼上菲律賓收件人:');
            $sheet->mergeCells('B' . $i . ':D' . $i);
            $sheet->getStyle('B' . $i)->getAlignment()->setHorizontal('left');
            $sheet->getStyle('B' . $i)->getFont()->setSize(18);
            $sheet->getStyle('B' . $i)->getFont()->setBold(true);
            $sheet->getStyle('B' . $i)->getFont()->setUnderline('double');
            $sheet->getStyle('B' . $i)->getFont()->setName('微軟正黑體');
            $sheet->getStyle('B' . $i . ':J' . $i)->applyFromArray($bold_border_style1);
            $i++;

            $sheet->getRowDimension($i)->setRowHeight(126.6);
            $richText = new \PhpOffice\PhpSpreadsheet\RichText\RichText();
            $payable = $richText->createTextRun('FELIIX INC' . "\n" . 'Address:');
            $payable->getFont()->setBold(true);
            $payable->getFont()->setSize(18);
            $payable->getFont()->setName('M+ 1c regular');
            $payable = $richText->createTextRun(' 664 7th Avenue corner, 7th St, Caloocan, 1405 Metro Manila' . "\n");
            $payable->getFont()->setSize(18);
            $payable->getFont()->setName('M+ 1c regular');
            $payable = $richText->createTextRun('Contact Person:');
            $payable->getFont()->setBold(true);
            $payable->getFont()->setSize(18);
            $payable->getFont()->setName('M+ 1c regular');
            $payable = $richText->createTextRun(" Lailani Ong 0938-7388808\n" . '                             Ronnie Paredes 0956-4082194');
            $payable->getFont()->setSize(18);
            $payable->getFont()->setName('M+ 1c regular');
            $sheet->getCell('B' . $i)->setValue($richText);
            $sheet->mergeCells('B' . $i . ':E' . $i);
            $sheet->getStyle('B' . $i . ':E' . $i)->getAlignment()->setHorizontal('left');
            $sheet->getStyle('B' . $i . ':E' . $i)->getFont()->setSize(18);
            $sheet->getStyle('B' . $i . ':E' . $i)->getFont()->setName('M+ 1c regular');
            $sheet->getStyle('B' . $i . ':J' . $i)->applyFromArray($bold_border_style2);
            $sheet->getStyle('B'. $i)->getAlignment()->setWrapText(true);
            $i++;


            $sheet->getRowDimension($i)->setRowHeight(28.2);
            $i++;


            // footer_bottom
            $k = $i + 6;
            $sheet->getStyle('B' . $i . ':B' . $k)->getAlignment()->setHorizontal('left');
            $sheet->getStyle('C' . $i . ':C' . $k)->getAlignment()->setHorizontal('left');
            $sheet->getStyle('B' . $i . ':C' . $k)->getFont()->setSize(20);
            $sheet->getStyle('B' . $i . ':B' . $k)->getFont()->setName('微軟正黑體');
            $sheet->getStyle('C' . $i . ':C' . $k)->getFont()->setName('M+ 1c regular');
            $sheet->getStyle('B' . $i . ':J' . $k)->applyFromArray($bold_border_style);


            $sheet->getRowDimension($i)->setRowHeight(28.2);
            $sheet->setCellValue('B' . $i, '訂購人:');  $sheet->setCellValue('C' . $i, '林巧雯  Ariel Lin');
            $i++;

            $sheet->getRowDimension($i)->setRowHeight(28.2);
            $sheet->setCellValue('B' . $i, '公司名稱: ');  $sheet->setCellValue('C' . $i, '盛盛國際有限公司 SSIT INC.');

            // invoice
            $objDrawing = new \PhpOffice\PhpSpreadsheet\Worksheet\Drawing();
            $objDrawing->setName('invoice');
            $objDrawing->setDescription('invoice');
            $objDrawing->setPath($conf::$upload_path ."invoice.png");
            $objDrawing->setCoordinates('H' . $i);
            $objDrawing->setWidthAndHeight(300, 300);
            $objDrawing->setResizeProportional(true);
            $objDrawing->setOffsetX(15);
            $objDrawing->setOffsetY(20);
            $objDrawing->setWorksheet($sheet);

            
            $i++;
            $sheet->getRowDimension($i)->setRowHeight(28.2);
            $sheet->setCellValue('B' . $i, '地址:');  $sheet->setCellValue('C' . $i, '50089 台灣彰化市金馬路1段80之1號');  


            $i++;
            $sheet->getRowDimension($i)->setRowHeight(28.2);
            $sheet->setCellValue('C' . $i, 'NO.80-1, SEC.1, JINMA RD., CHANGHUA CITY, 50089, TAIWAN (R.O.C.)');


            $i++;
            $sheet->getRowDimension($i)->setRowHeight(28.2);
            $sheet->setCellValue('B' . $i, '電話:');  $sheet->setCellValue('C' . $i, '04-7274745');  

            $i++;
            $sheet->getRowDimension($i)->setRowHeight(28.2);
            $sheet->setCellValue('B' . $i, '傳真:');  $sheet->setCellValue('C' . $i, '04-7286267');  


            $i++;
            $sheet->getRowDimension($i)->setRowHeight(28.2);
            $sheet->setCellValue('B' . $i, '統一編號:');  $sheet->setCellValue('C' . $i, '53474792');  


            $i++;

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

    //$image_file = urlencode($image_file);

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


function GetKey($str)
{
    if(trim($str) == '')
        return "";
    
    $obj = explode('=>', $str);

    return isset($obj[0]) ? $obj[0] : "";
}

function GetValue($str)
{
    if(trim($str) == '')
        return "";
    
    $obj = explode('=>', $str);

    return isset($obj[1]) ? trim($obj[1]) : "";
}

function GetProductCurrency($id, $db)
{
    $sql = "SELECT currency FROM product_category WHERE id = ". $id . " and STATUS <> -1";
    $currency = "";

    $stmt = $db->prepare( $sql );
    $stmt->execute();

    while($row = $stmt->fetch(PDO::FETCH_ASSOC)) {

        if($row['currency'] != '')
            $currency = $row['currency'];
    }

    return $currency;
}

function GetProductMain($id, $v1, $v2, $v3, $v4, $db)
{
    $sql = "SELECT * FROM product_category WHERE id = ". $id . " and STATUS <> -1";
    $merged_results = array();

    $stmt = $db->prepare( $sql );
    $stmt->execute();

    $price_ntd = '';

    while($row = $stmt->fetch(PDO::FETCH_ASSOC)) {

        if($row['price_ntd'] != '')
            $price_ntd = $row['price_ntd'];
       
        $product = GetProduct($id, $v1, $v2, $v3, $v4, $db);
        if($product != '')
            $price_ntd = $product;
    }

    return $price_ntd;
}

function GetProduct($id, $pv1, $pv2, $pv3, $pv4, $db){
    $sql = "SELECT *, CONCAT('https://storage.googleapis.com/feliiximg/' , photo) url FROM product WHERE product_id = ". $id . " and STATUS <> -1";

    $merged_results = array();

    $stmt = $db->prepare( $sql );
    $stmt->execute();

    while($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        $id = $row['id'];
        $k1 = GetKey($row['1st_variation']);
        $k2 = GetKey($row['2rd_variation']);
        $k3 = GetKey($row['3th_variation']);
        $k4 = GetKey($row['4th_variation']);
        $v1 = GetValue($row['1st_variation']);
        $v2 = GetValue($row['2rd_variation']);
        $v3 = GetValue($row['3th_variation']);
        $v4 = GetValue($row['4th_variation']);
        $checked = '';
        $code = $row['code'];
        $price = $row['price'];
        $price_ntd = $row['price_ntd'];
        $price_org = $row['price'];
        $price_ntd_org = $row['price_ntd'];
        $price_change = $row['price_change'];
        $price_ntd_change = $row['price_ntd_change'];
        $status = $row['enabled'];
        $photo = trim($row['photo']);
        if($photo != '')
            $url = $row['url'];
        else
            $url = '';

        $quoted_price = $row['quoted_price'];
        $quoted_price_change = $row['quoted_price_change'];

        $merged_results[] = array(  "id" => $id, 
                                    "k1" => $k1, 
                                    "k2" => $k2, 
                                    "k3" => $k3, 
                                    "k4" => $k4,
                                    "v1" => $v1, 
                                    "v2" => $v2, 
                                    "v3" => $v3, 
                                    "v4" => $v4,
                                    "checked" => $checked, 
                                    "code" => $code, 
                                    "price" => $price, 
                                    "price_ntd" => $price_ntd, 
                                    "price_org" => $price_org, 
                                    "price_ntd_org" => $price_ntd_org, 
                                    "price_change" => $price_change, 
                                    "price_ntd_change" => $price_ntd_change, 
                                    "status" => $status, 
                                    "url" => $url, 
                                    "photo" => $photo, 

                                    "quoted_price" => $quoted_price, 
                                    "quoted_price_org" => $quoted_price, 
                                    "quoted_price_change" => substr($quoted_price_change, 0, 10), 
                                   
                                    "file" => array( "value" => ''),
                                   
            );
    }

    // find in merged_results with v1, v2 and v3
    $price_ntd = "";
    foreach($merged_results as $item) {
        if($item['v1'] == $pv1 && $item['v2'] == $pv2 && $item['v3'] == $pv3 && $item['v4'] == $pv4) {
            if($item['price_ntd'] != '')
                $price_ntd = $item['price_ntd'];
        }
    }
    
    return $price_ntd;
}


function GetTaskDetail($task_id, $task_type, $db)
{
    $title = "";
    $table = "project_other_task_l";
    if($task_type == 'LT')
        $table = "project_other_task_l";
    
    if($task_type == 'SLS')
        $table = "project_other_task_sl";

    if($task_type == 'OS')
        $table = "project_other_task_o";

        if($task_type == 'SVC')
        $table = "project_other_task_sv";

    $query = "select title from " . $table . " where id = " . $task_id;
    $stmt = $db->prepare( $query );
    $stmt->execute();
    $row = $stmt->fetch(PDO::FETCH_ASSOC);
    $title = $row['title'];
  
    return $title;
}

?>