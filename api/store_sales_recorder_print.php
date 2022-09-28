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
use \Firebase\JWT\JWT;
 
// files needed to connect to database
include_once 'config/database.php';

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\IOFactory;
 
 include_once 'config/conf.php';
// get database connection
$database = new Database();
$db = $database->getConnection();

$mail_ip= "https://storage.googleapis.com/feliiximg/";
$files = array();
$explode_row = array();
// get posted data
$data = json_decode(file_get_contents("php://input"));
 
// get jwt
$jwt = (isset($_COOKIE['jwt']) ?  $_COOKIE['jwt'] : null);

$start_date = (isset($_POST['start_date']) ?  $_POST['start_date'] : '');
$end_date = (isset($_POST['end_date']) ?  $_POST['end_date'] : '');

$start_date = str_replace('/', '-', $start_date);
$end_date = str_replace('/', '-', $end_date);

$keyword = (isset($_POST['keyword']) ?  $_POST['keyword'] : '');

// if jwt is not empty
if($jwt){ 
 
    // if decode succeed, show user details
    try {
 
        // decode jwt
        $decoded = JWT::decode($jwt, $key, array('HS256'));

        // response in json format
            http_response_code(200);

            $merged_results = array();

            $query = "SELECT  0 as is_checked, 
                                ss.id, 
                                ss.sales_date, 
                                ss.sales_name, 
                                ss.customer_name,
                                ss.discount,
                                ss.invoice,
                                ss.remark,
                                ss.payment_method,
                                ss.teminal,
                                ss.`status`,
                                DATE_FORMAT(ss.crt_time ,'%Y-%m-%d') crt_time
                                from store_sales ss
                                left join store_sales_record sr 
                                on sr.sales_id = ss.id
                                where 1=1 ";

        if($start_date!='') {
            $query = $query . " and ss.sales_date >= '$start_date' ";
        }

        if($end_date!='') {
            $query = $query . " and ss.sales_date <= '$end_date' ";
        }

        if($keyword != '')
            $query .= " AND (ss.sales_name like '%" . $keyword . "%' or ss.customer_name like '%" . $keyword . "%' or ss.remark like '%" . $keyword . "%' or sr.product_name like '%" . $keyword . "%') ";

        $query .= "group by
        ss.id, 
        ss.sales_date, 
        ss.sales_name, 
        ss.customer_name,
        ss.discount,
        ss.invoice,
        ss.remark,
        ss.payment_method,
        ss.teminal,
        ss.`status`,
        ss.crt_time ";
        
        $query .= " order by ss.sales_date ";

        $stmt = $db->prepare($query);
        $stmt->execute();
    
        $merged_results = [];
        
        $items = [];
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
    
            $id = $row['id'];
            $sales_date = $row['sales_date'];
            $sales_name = $row['sales_name'];
            $customer_name = $row['customer_name'];
            $discount = $row['discount'];
            $invoice = $row['invoice'];
            $remark = $row['remark'];
            $payment_method = $row['payment_method'];
            $teminal = $row['teminal'];
            $status = $row['status'];
            $crt_time = $row['crt_time'];

            $items = GetSalesDetail($id, $db);
           
            $merged_results[] = array( 
                "is_checked" => 0,
                "id" => $id,
                "sales_date" => $sales_date,
                "sales_name" => $sales_name,
                "customer_name" => $customer_name,
                "discount" => $discount,
                "invoice" => $invoice,
                "remark" => $remark,
                "payment_method" => GetPayment($payment_method),
                "teminal" => $teminal,
                "status" => $status,
                "payment" => $items,
                "total_amount" => GetAmount($items),
                "crt_time"=> $crt_time,
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

                'alignment' => array(
                    'vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_TOP,
                ),
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
            $sheet->setTitle("Sheet 1");


            $sheet->setCellValue('A1', 'Sales Date');
            $sheet->setCellValue('B1', 'Sales Name');
            $sheet->setCellValue('C1', 'Customer Name');
            $sheet->setCellValue('D1', 'Product Name');
            $sheet->setCellValue('E1', 'Quantity');
            $sheet->setCellValue('F1', 'Price');
            $sheet->setCellValue('G1', 'Amount');
            $sheet->setCellValue('H1', 'Total Amount');
            $sheet->setCellValue('I1', 'Discount');
            $sheet->setCellValue('J1', 'Net Amount');
            $sheet->setCellValue('K1', 'Invoice');
            $sheet->setCellValue('L1', 'Payment Method');
            $sheet->setCellValue('M1', 'Terminal');
            $sheet->setCellValue('N1', 'Remark');


            $i = 2;

            foreach ($merged_results as $measure)
            {
                if(count($measure["payment"]) > 1)
                    $j = $i;
            
                foreach($measure["payment"] as $rec)
                {
                    $sheet->setCellValue('A' . $i, $measure["sales_date"]);
                    $sheet->setCellValue('B' . $i, $measure["sales_name"]);
                    $sheet->setCellValue('C' . $i, $measure["customer_name"]);
                    $sheet->setCellValue('D' . $i, $rec["product_name"]);
                    $sheet->setCellValue('E' . $i, $rec["qty"]);
                    $sheet->setCellValue('F' . $i, $rec["price"]);
                    $sheet->setCellValue('G' . $i, $rec["free"] == "" ? $rec['qty'] * $rec['price'] : "FREE");
                    $sheet->setCellValue('H' . $i, $measure["total_amount"]);
                    $sheet->setCellValue('I' . $i, $measure["discount"]);
                    $sheet->setCellValue('J' . $i, $measure["total_amount"] - $measure["discount"]);
                    $sheet->setCellValue('K' . $i, $measure["invoice"]);
                    $sheet->setCellValue('L' . $i, $measure["payment_method"]);
                    $sheet->setCellValue('M' . $i, $measure["teminal"]);
                    $sheet->setCellValue('N' . $i, $measure["remark"]);
              
                    $sheet->getStyle('A'. $i. ':' . 'N' . $i)->applyFromArray($styleArray);
                    $i++;
                }
            
                if(count($measure["payment"]) > 1)
                {
                    $sheet->mergeCells('A' . $j . ':A' . ($i -1));
                    $sheet->mergeCells('B' . $j . ':B' . ($i -1));
                    $sheet->mergeCells('C' . $j . ':C' . ($i -1));
                    $sheet->mergeCells('H' . $j . ':H' . ($i -1));
                    $sheet->mergeCells('I' . $j . ':I' . ($i -1));
                    $sheet->mergeCells('J' . $j . ':J' . ($i -1));
                    $sheet->mergeCells('K' . $j . ':K' . ($i -1));
                    $sheet->mergeCells('L' . $j . ':L' . ($i -1));
                    $sheet->mergeCells('M' . $j . ':M' . ($i -1));
                    $sheet->mergeCells('N' . $j . ':N' . ($i -1));
                }
            
            }
            
            $sheet->getStyle('A1:' . 'N1')->getFont()->setBold(true);
            $sheet->getStyle('A1:' . 'N' . --$i)->applyFromArray($styleArray);
            
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




function GetSalesDetail($sales_id, $db){
    $query = "
            SELECT 0 as is_checked, id, product_name, qty, price, free, DATE_FORMAT(crt_time, '%Y/%m/%d') crt_time
                FROM store_sales_record
            WHERE  sales_id = " . $sales_id . "
            AND `status` <> -1 
    ";

    // prepare the query
    $stmt = $db->prepare($query);
    $stmt->execute();

    $merged_results = [];

    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        $is_checked = $row['is_checked'];
        $id = $row['id'];
        $qty = $row['qty'] == 0 ? "" : $row['qty'];
        $price = $row['price'] == 0 ? "" : $row['price'];
        $free = $row['free'] == "" ? "" : $row['free'];
        $product_name = $row['product_name'] == "" ? "" : $row['product_name'];
        $crt_time = $row['crt_time'] == "" ? "" : $row['crt_time'];
    
       
        $merged_results[] = array(
            "is_checked" => $is_checked,
            "id" => $id,
            "qty" => $qty,
            "price" => $price,
            "free" => $free,
            "product_name" => $product_name,
           "crt_time" => $crt_time,
        );
    }

    return $merged_results;
}

function GetAmount($array)
{
    $amount = 0;

    foreach($array as $item) {
        if($item['free'] == '')
            $amount += ($item['qty'] == "" ? 0 : $item['qty']) * ($item['price'] == "" ? 0 : $item['price']);
    }

    return $amount;
}



function GetPayment($loc)
{
    $location = "";
    switch ($loc) {
        case "cash":
            $location = "Cash";
            break;
        case "visa":
            $location = "Visa Card";
            break;
        case "master":
            $location = "Master Card";
            break;
        case "jcb":
            $location = "JCB Card";
            break;
        case "debit":
            $location = "Debit Card";
            break;
        default:
            $location = "";
            break;
    }

    return $location;
}

?>