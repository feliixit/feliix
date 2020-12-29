<?php
error_reporting(E_ALL);

include_once 'api/config/database.php';

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

$id = (isset($_GET['id']) ?  $_GET['id'] : 0);

$sql = "select DAYNAME(start_time) weekday, DATE_FORMAT(start_time,'%d %M %Y') start_time, title, sales_executive, 
		project_in_charge, installer_needed, things_to_bring, products_to_bring, service, driver, 
		back_up_driver, photoshoot_request, notes, location, agenda, DATE_FORMAT(appoint_time, '%I:%s %p') appoint_time, 
		DATE_FORMAT(detail.end_time, '%I:%s %p') end_time
		from work_calendar_main main 
		left join work_calendar_details detail on detail.main_id = main.id where main.id = $id ";

    $stmt = $db->prepare( $sql );
    $stmt->execute();

    $weekday = '';
    $start_time = '';
    $title = '';
    $sales_executive = '';
    $project_in_charge = '';
    $installer_needed = '';
    $things_to_bring = '';
    $products_to_bring = '';
    $service = '';
    $driver = '';
    $back_up_driver = '';
    $photoshoot_request = '';
    $notes = '';

    $location = '';
    $agenda = '';
    $appoint_time = '';
    $end_time = '';

while($row = $stmt->fetch(PDO::FETCH_ASSOC)) 
{
    $weekday = $row['weekday'];
    $start_time = $row['start_time'];
    $title = $row['title'];
    $sales_executive = $row['sales_executive'];
    $project_in_charge = $row['project_in_charge'];
    $installer_needed = $row['installer_needed'];
    $things_to_bring = $row['things_to_bring'];
    $products_to_bring = $row['products_to_bring'];
    $service = $row['service'];
    $driver = $row['driver'];
    $back_up_driver = $row['back_up_driver'];
    $photoshoot_request = $row['photoshoot_request'];
    $notes = $row['notes'];


    $location = $row['location'];
    $agenda = $row['agenda'];
    $appoint_time = $row['appoint_time'];
    $end_time = $row['end_time'];

    break;
}

// Creating the new document...
$phpWord = new PhpOffice\PhpWord\PhpWord();

/* Note: any element you append to a document must reside inside of a Section. */

// Adding an empty Section to the document...
$section = $phpWord->addSection();
// Adding Text element to the Section having font styled by default...
$section->addText($weekday . ", " . $start_time . " Schedule");

$section->addText("");

$table = $section->addTable('table', [
    'borderSize' => 6, 
    'borderColor' => 'F73605', 
    'afterSpacing' => 0, 
    'Spacing'=> 0, 
    'cellMargin'=> 0
]);

$table->addRow();
$table->addCell(2000, ['borderSize' => 6])->addText("Date:", array('bold' => true));
$table->addCell(8500, ['borderSize' => 6])->addText($start_time);

$table->addRow();
$table->addCell(2000, ['borderSize' => 6])->addText("Project:", array('bold' => true));
$table->addCell(8500, ['borderSize' => 6])->addText($title);

$table->addRow();
$table->addCell(2000, ['borderSize' => 6])->addText("Sales Executive:", array('bold' => true));
$table->addCell(8500, ['borderSize' => 6])->addText($sales_executive);

$table->addRow();
$table->addCell(2000, ['borderSize' => 6])->addText("project_in_charge:", array('bold' => true));
$table->addCell(8500, ['borderSize' => 6])->addText($project_in_charge);

$table->addRow();
$table->addCell(2000, ['borderSize' => 6])->addText("Installer needed:", array('bold' => true));
$table->addCell(8500, ['borderSize' => 6])->addText($installer_needed);

$table->addRow();
$table->addCell(2000, ['borderSize' => 6])->addText("Things to Bring:", array('bold' => true));
$table->addCell(8500, ['borderSize' => 6])->addText($things_to_bring);

$table->addRow();
$table->addCell(2000, ['borderSize' => 6])->addText("Products to Bring:", array('bold' => true));
$table->addCell(8500, ['borderSize' => 6])->addText($products_to_bring);

$table->addRow();
$table->addCell(2000, ['borderSize' => 6])->addText("Service:", array('bold' => true));
$table->addCell(8500, ['borderSize' => 6])->addText(getService($service));

$table->addRow();
$table->addCell(2000, ['borderSize' => 6])->addText("Driver:", array('bold' => true));
$table->addCell(8500, ['borderSize' => 6])->addText(getDriver($driver));

$table->addRow();
$table->addCell(2000, ['borderSize' => 6])->addText("Back-up Driver:", array('bold' => true));
$table->addCell(8500, ['borderSize' => 6])->addText(getDriver($back_up_driver));

$table->addRow();
$table->addCell(2000, ['borderSize' => 6])->addText("Photoshoot Request:", array('bold' => true));
$table->addCell(8500, ['borderSize' => 6])->addText(getRequest($photoshoot_request));

$table->addRow();
$table->addCell(2000, ['borderSize' => 6])->addText("Note/s:", array('bold' => true));
$table->addCell(8500, ['borderSize' => 6])->addText($notes);

$section->addText("");

$table1 = $section->addTable('table1', [
    'borderSize' => 6, 
    'borderColor' => 'F73605', 
    'afterSpacing' => 0, 
    'Spacing'=> 0, 
    'cellMargin'=> 0,
    'align' => 'center'
]);

$table1->addRow();
$table1->addCell(2600, ['borderSize' => 6])->addText("Location", array('bold' => true, 'align' => 'center'));
$table1->addCell(2600, ['borderSize' => 6])->addText("Agenda", array('bold' => true, 'align' => 'center'));
$table1->addCell(2600, ['borderSize' => 6])->addText("Appoint Time", array('bold' => true, 'align' => 'center'));
$table1->addCell(2600, ['borderSize' => 6])->addText("End Time", array('bold' => true, 'align' => 'center'));


    $table1->addRow();
    $table1->addCell(2600, ['borderSize' => 6])->addText($location, array('align' => 'center'));
    $table1->addCell(2600, ['borderSize' => 6])->addText($agenda, array('align' => 'center'));
    $table1->addCell(2600, ['borderSize' => 6])->addText($appoint_time, array( 'align' => 'center'));
    $table1->addCell(2600, ['borderSize' => 6])->addText($end_time, array( 'align' => 'center'));

while($row = $stmt->fetch(PDO::FETCH_ASSOC)) 
{
    $location = $row['location'];
    $agenda = $row['agenda'];
    $appoint_time = $row['appoint_time'];
    $end_time = $row['end_time'];

    $table1->addRow();
    $table1->addCell(2600, ['borderSize' => 6])->addText($location, array('align' => 'center'));
    $table1->addCell(2600, ['borderSize' => 6])->addText($agenda, array('align' => 'center'));
    $table1->addCell(2600, ['borderSize' => 6])->addText($appoint_time, array( 'align' => 'center'));
    $table1->addCell(2600, ['borderSize' => 6])->addText($end_time, array( 'align' => 'center'));

}

// Adding Text element with font customized using explicitly created font style object...
$fontStyle = new \PhpOffice\PhpWord\Style\Font();
$fontStyle->setBold(true);
$fontStyle->setName('Tahoma');
$fontStyle->setSize(13);
// $myTextElement = $section->addText('"Believe you can and you\'re halfway there." (Theodor Roosevelt)');
//$myTextElement->setFontStyle($fontStyle);

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


    function getService($type){
        $leave_type = '';
    
        if($type =="1")
            $leave_type = "innova";
        if($type =="2")
            $leave_type = "avanza gold";
        if($type =="3")
            $leave_type = "avanza gray";
        if($type =="4")
            $leave_type = "L3001";
        if($type =="5")
            $leave_type = "L3002";
        if($type =="6")
            $leave_type = "Grab";
        
        return $leave_type;
    }

    function getDriver($type){
        $leave_type = '';
    
        if($type =="1")
            $leave_type = "MG";
        if($type =="2")
            $leave_type = "AY";
        if($type =="3")
            $leave_type = "EV";
        if($type =="4")
            $leave_type = "JB";
        if($type =="5")
            $leave_type = "MA";
    
        return $leave_type;
    }

    function getRequest($type){
        $leave_type = '';
    
        if($type =="0")
            $leave_type = "No";
        if($type =="1")
            $leave_type = "Yes";
        
        return $leave_type;
    }
    
?>
