<?php
error_reporting(E_ERROR | E_PARSE);

header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: *");
header("Access-Control-Max-Age: 3600");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

$jwt = (isset($_COOKIE['jwt']) ?  $_COOKIE['jwt'] : null);
include_once 'config/core.php';
include_once 'config/conf.php';
include_once 'libs/php-jwt-master/src/BeforeValidException.php';
include_once 'libs/php-jwt-master/src/ExpiredException.php';
include_once 'libs/php-jwt-master/src/SignatureInvalidException.php';
include_once 'libs/php-jwt-master/src/JWT.php';

require_once '../vendor/autoload.php';

use Google\Cloud\Storage\StorageClient;
use \Firebase\JWT\JWT;

if ( !isset( $jwt ) ) {
    http_response_code(401);

    echo json_encode(array("message" => "Access denied."));
    die();
}
else
{
    try {
        // decode jwt
        $decoded = JWT::decode($jwt, $key, array('HS256'));

        $user_id = $decoded->data->id;
        $username = $decoded->data->username;

    }
        // if decode fails, it means jwt is invalid
    catch (Exception $e){

        http_response_code(401);

        echo json_encode(array("message" => "Access denied."));
        die();
    }
}

include_once 'config/database.php';
$database = new Database();
$db = $database->getConnection();
$conf = new Conf();

$uid = $user_id;

$id = (isset($_POST['id']) ?  $_POST['id'] : 0);
$stage_id = (isset($_POST['stage_id']) ?  $_POST['stage_id'] : 0);
$uid = $user_id;
$down_payment_date = (isset($_POST['down_payment_date']) ?  $_POST['down_payment_date'] : '');
$account_executive = (isset($_POST['account_executive']) ?  $_POST['account_executive'] : '');
$pic = (isset($_POST['pic']) ?  $_POST['pic'] : '');
$quotation = (isset($_POST['quotation']) ?  $_POST['quotation'] : '');
$client_name = (isset($_POST['client_name']) ?  $_POST['client_name'] : '');
$contact_person = (isset($_POST['contact_person']) ?  $_POST['contact_person'] : '');
$contact_number = (isset($_POST['contact_number']) ?  $_POST['contact_number'] : '');
$delivery_address_within = (isset($_POST['delivery_address_within']) ?  $_POST['delivery_address_within'] : '');
$delivery_address_outside = (isset($_POST['delivery_address_outside']) ?  $_POST['delivery_address_outside'] : '');
$exact_delivery_address = (isset($_POST['exact_delivery_address']) ?  $_POST['exact_delivery_address'] : '');
$detail_delivery_address = (isset($_POST['detail_delivery_address']) ?  $_POST['detail_delivery_address'] : '');
$attached_layout = (isset($_POST['attached_layout']) ?  $_POST['attached_layout'] : '');
$delivery_permit = (isset($_POST['delivery_permit']) ?  $_POST['delivery_permit'] : '');
$work_permit = (isset($_POST['work_permit']) ?  $_POST['work_permit'] : '');
$permit_processing_note = (isset($_POST['permit_processing_note']) ?  $_POST['permit_processing_note'] : '');
$other_request = (isset($_POST['other_request']) ?  $_POST['other_request'] : '');
$date_of_delivery = (isset($_POST['date_of_delivery']) ?  $_POST['date_of_delivery'] : '');
$client_deadline = (isset($_POST['client_deadline']) ?  $_POST['client_deadline'] : '');
$delivery_1st = (isset($_POST['delivery_1st']) ?  $_POST['delivery_1st'] : '');
$delivery_1st_items = (isset($_POST['delivery_1st_items']) ?  $_POST['delivery_1st_items'] : '');
$delivery_2nd = (isset($_POST['delivery_2nd']) ?  $_POST['delivery_2nd'] : '');
$delivery_2nd_items = (isset($_POST['delivery_2nd_items']) ?  $_POST['delivery_2nd_items'] : '');
$os_delivery_only = (isset($_POST['os_delivery_only']) ?  $_POST['os_delivery_only'] : '');
$os_delivery_install = (isset($_POST['os_delivery_install']) ?  $_POST['os_delivery_install'] : '');
$lt_delivery_only = (isset($_POST['lt_delivery_only']) ?  $_POST['lt_delivery_only'] : '');
$lt_delivery_install = (isset($_POST['lt_delivery_install']) ?  $_POST['lt_delivery_install'] : '');
$delivery_install = (isset($_POST['delivery_install']) ?  $_POST['delivery_install'] : '');
$scope_attached_layout = (isset($_POST['scope_attached_layout']) ?  $_POST['scope_attached_layout'] : '');
$timeline_check = (isset($_POST['timeline_check']) ?  $_POST['timeline_check'] : '');
$timeline = (isset($_POST['timeline']) ?  $_POST['timeline'] : '');
$data_check = (isset($_POST['data_check']) ?  $_POST['data_check'] : '');
$data = (isset($_POST['data']) ?  $_POST['data'] : '');
$electrical_check = (isset($_POST['electrical_check']) ?  $_POST['electrical_check'] : '');
$electrical = (isset($_POST['electrical']) ?  $_POST['electrical'] : '');
$flooring_check = (isset($_POST['flooring_check']) ?  $_POST['flooring_check'] : '');
$flooring = (isset($_POST['flooring']) ?  $_POST['flooring'] : '');
$type_and_ceiling = (isset($_POST['type_and_ceiling']) ?  $_POST['type_and_ceiling'] : '');
$painting_check = (isset($_POST['painting_check']) ?  $_POST['painting_check'] : '');
$painting = (isset($_POST['painting']) ?  $_POST['painting'] : '');
$ceiling_electrical_check = (isset($_POST['ceiling_electrical_check']) ?  $_POST['ceiling_electrical_check'] : '');
$ceiling_electrical = (isset($_POST['ceiling_electrical']) ?  $_POST['ceiling_electrical'] : '');
$manpower_check = (isset($_POST['manpower_check']) ?  $_POST['manpower_check'] : '');
$manpower = (isset($_POST['manpower']) ?  $_POST['manpower'] : '');
$materials_check = (isset($_POST['materials_check']) ?  $_POST['materials_check'] : '');
$materials = (isset($_POST['materials']) ?  $_POST['materials'] : '');
$trucking_services = (isset($_POST['trucking_services']) ?  $_POST['trucking_services'] : '');
$purchasing_of_special_products_check = (isset($_POST['purchasing_of_special_products_check']) ?  $_POST['purchasing_of_special_products_check'] : '');
$purchasing_of_special_products = (isset($_POST['purchasing_of_special_products']) ?  $_POST['purchasing_of_special_products'] : '');
$tools_check = (isset($_POST['tools_check']) ?  $_POST['tools_check'] : '');
$tools = (isset($_POST['tools']) ?  $_POST['tools'] : '');

$attached_layout_file = (isset($_POST['attached_layout_file']) ?  $_POST['attached_layout_file'] : '[]');
$attached_layout_file_array = json_decode($attached_layout_file, true);

$scope_attached_layout_file = (isset($_POST['scope_attached_layout_file']) ?  $_POST['scope_attached_layout_file'] : '[]');
$scope_attached_layout_file_array = json_decode($scope_attached_layout_file, true);

$other_attached_layout_file = (isset($_POST['other_attached_layout_file']) ?  $_POST['other_attached_layout_file'] : '[]');
$other_attached_layout_file_array = json_decode($other_attached_layout_file, true);

$delivery_address_within = formatCheckBox($delivery_address_within);
$delivery_address_outside = formatCheckBox($delivery_address_outside);
$attached_layout = formatCheckBox($attached_layout);
$delivery_permit = formatCheckBox($delivery_permit);
$work_permit = formatCheckBox($work_permit);
$delivery_1st = formatCheckBox($delivery_1st);
$delivery_2nd = formatCheckBox($delivery_2nd);
$os_delivery_only = formatCheckBox($os_delivery_only);
$os_delivery_install = formatCheckBox($os_delivery_install);
$lt_delivery_only = formatCheckBox($lt_delivery_only);
$lt_delivery_install = formatCheckBox($lt_delivery_install);
$delivery_install = formatCheckBox($delivery_install);
$scope_attached_layout = formatCheckBox($scope_attached_layout);
$timeline_check = formatCheckBox($timeline_check);
$data_check = formatCheckBox($data_check);
$electrical_check = formatCheckBox($electrical_check);
$flooring_check = formatCheckBox($flooring_check);
$painting_check = formatCheckBox($painting_check);
$ceiling_electrical_check = formatCheckBox($ceiling_electrical_check);
$manpower_check = formatCheckBox($manpower_check);
$materials_check = formatCheckBox($materials_check);
$trucking_services = formatCheckBox($trucking_services);
$purchasing_of_special_products_check = formatCheckBox($purchasing_of_special_products_check);
$tools_check = formatCheckBox($tools_check);

try{
    $query = "insert into project_a_meeting
    SET
        `stage_id` = :stage_id,
        `down_payment_date` = :down_payment_date,
        `account_executive` = :account_executive,
        `pic` = :pic,
        `quotation` = :quotation,
        `client_name` = :client_name,
        `contact_person` = :contact_person,
        `contact_number` = :contact_number,
        `delivery_address_within` = :delivery_address_within,
        `delivery_address_outside` = :delivery_address_outside,
        `exact_delivery_address` = :exact_delivery_address,
        `detail_delivery_address` = :detail_delivery_address,
        `attached_layout` = :attached_layout,
        `delivery_permit` = :delivery_permit,
        `work_permit` = :work_permit,
        `permit_processing_note` = :permit_processing_note,
        `other_request` = :other_request,
        `date_of_delivery` = :date_of_delivery,
        `client_deadline` = :client_deadline,
        `delivery_1st` = :delivery_1st,
        `delivery_1st_items` = :delivery_1st_items,
        `delivery_2nd` = :delivery_2nd,
        `delivery_2nd_items` = :delivery_2nd_items,
        `os_delivery_only` = :os_delivery_only,
        `os_delivery_install` = :os_delivery_install,
        `lt_delivery_only` = :lt_delivery_only,
        `lt_delivery_install` = :lt_delivery_install,
        `delivery_install` = :delivery_install,
        `scope_attached_layout` = :scope_attached_layout,
        `timeline_check` = :timeline_check,
        `timeline` = :timeline,
        `data_check` = :data_check,
        `data` = :data,
        `electrical_check` = :electrical_check,
        `electrical` = :electrical,
        `flooring_check` = :flooring_check,
        `flooring` = :flooring,
        `type_and_ceiling` = :type_and_ceiling,
        `painting_check` = :painting_check,
        `painting` = :painting,
        `ceiling_electrical_check` = :ceiling_electrical_check,
        `ceiling_electrical` = :ceiling_electrical,
        `manpower_check` = :manpower_check,
        `manpower` = :manpower,
        `materials_check` = :materials_check,
        `materials` = :materials,
        `trucking_services` = :trucking_services,
        `purchasing_of_special_products_check` = :purchasing_of_special_products_check,
        `purchasing_of_special_products` = :purchasing_of_special_products,
        `tools_check` = :tools_check,
        `tools` = :tools,
        `status` = 1,
        `create_id` = :create_id,
        `created_at` = NOW()
    ";

    // prepare the query
    $stmt = $db->prepare($query);
    $stmt->bindParam(':stage_id', $stage_id);
    $stmt->bindParam(':down_payment_date', $down_payment_date);
    $stmt->bindParam(':account_executive', $account_executive);
    $stmt->bindParam(':pic', $pic);
    $stmt->bindParam(':quotation', $quotation);
    $stmt->bindParam(':client_name', $client_name );
    $stmt->bindParam(':contact_person', $contact_person );
    $stmt->bindParam(':contact_number', $contact_number );
    $stmt->bindParam(':delivery_address_within', $delivery_address_within );
    $stmt->bindParam(':delivery_address_outside', $delivery_address_outside );
    $stmt->bindParam(':exact_delivery_address', $exact_delivery_address );
    $stmt->bindParam(':detail_delivery_address', $detail_delivery_address );
    $stmt->bindParam(':attached_layout', $attached_layout );
    $stmt->bindParam(':delivery_permit', $delivery_permit );
    $stmt->bindParam(':work_permit', $work_permit );
    $stmt->bindParam(':permit_processing_note', $permit_processing_note );
    $stmt->bindParam(':other_request', $other_request );
    $stmt->bindParam(':date_of_delivery', $date_of_delivery );
    $stmt->bindParam(':client_deadline', $client_deadline );
    $stmt->bindParam(':delivery_1st', $delivery_1st );
    $stmt->bindParam(':delivery_1st_items', $delivery_1st_items );
    $stmt->bindParam(':delivery_2nd', $delivery_2nd );
    $stmt->bindParam(':delivery_2nd_items', $delivery_2nd_items );
    $stmt->bindParam(':os_delivery_only', $os_delivery_only );
    $stmt->bindParam(':os_delivery_install', $os_delivery_install );
    $stmt->bindParam(':lt_delivery_only', $lt_delivery_only );
    $stmt->bindParam(':lt_delivery_install', $lt_delivery_install );
    $stmt->bindParam(':delivery_install', $delivery_install );
    $stmt->bindParam(':scope_attached_layout', $scope_attached_layout );
    $stmt->bindParam(':timeline_check', $timeline_check );
    $stmt->bindParam(':timeline', $timeline );
    $stmt->bindParam(':data_check', $data_check );
    $stmt->bindParam(':data', $data );
    $stmt->bindParam(':electrical_check', $electrical_check );
    $stmt->bindParam(':electrical', $electrical );
    $stmt->bindParam(':flooring_check', $flooring_check );
    $stmt->bindParam(':flooring', $flooring );
    $stmt->bindParam(':type_and_ceiling', $type_and_ceiling );
    $stmt->bindParam(':painting_check', $painting_check );
    $stmt->bindParam(':painting', $painting );
    $stmt->bindParam(':ceiling_electrical_check', $ceiling_electrical_check );
    $stmt->bindParam(':ceiling_electrical', $ceiling_electrical );
    $stmt->bindParam(':manpower_check', $manpower_check );
    $stmt->bindParam(':manpower', $manpower );
    $stmt->bindParam(':materials_check', $materials_check );
    $stmt->bindParam(':materials', $materials );
    $stmt->bindParam(':trucking_services', $trucking_services );
    $stmt->bindParam(':purchasing_of_special_products_check', $purchasing_of_special_products_check );
    $stmt->bindParam(':purchasing_of_special_products', $purchasing_of_special_products );
    $stmt->bindParam(':tools_check', $tools_check );
    $stmt->bindParam(':tools', $tools );
   

    $stmt->bindParam(':create_id', $uid);

    $last_id = 0;
    if (!$stmt->execute())
    {
        $arr = $stmt->errorInfo();

        http_response_code(501);
        echo json_encode(array("Failure at " . date("Y-m-d") . " " . date("h:i:sa") . " " . $arr[2]));

        die();
    }
    else
        $last_id = $db->lastInsertId();

    $batch_id = $last_id;
    $batch_type = "attached_layout";

    if($attached_layout == "true" || $attached_layout == "1" || $attached_layout == "t")
    {
        for($j=0; $j < count($attached_layout_file_array); $j++)
        {
            if($attached_layout_file_array[$j]['checked'] == "true")
            {
                $key = "attached_layout_file_" . $j;
                if (array_key_exists($key, $_FILES))
                {
                    $update_name = SaveImage($key, $batch_id, $batch_type, $user_id, $db, $conf);
                }
            }
        }
    }

    $batch_type = "scope_attached_layout";

    if($scope_attached_layout == "true" || $scope_attached_layout == "1" || $scope_attached_layout == "t")
    {
        for($j=0; $j < count($scope_attached_layout_file_array); $j++)
        {
            if($scope_attached_layout_file_array[$j]['checked'] == "true")
            {
                $key = "scope_attached_layout_file_" . $j;
                if (array_key_exists($key, $_FILES))
                {
                    $update_name = SaveImage($key, $batch_id, $batch_type, $user_id, $db, $conf);
                }
            }
        }
    }

    $batch_type = "other_attached_layout";

    for($j=0; $j < count($other_attached_layout_file_array); $j++)
    {
        if($other_attached_layout_file_array[$j]['checked'] == "true")
        {
            $key = "other_attached_layout_file_" . $j;
            if (array_key_exists($key, $_FILES))
            {
                $update_name = SaveImage($key, $batch_id, $batch_type, $user_id, $db, $conf);
            }
        }
    }
    

    http_response_code(200);
    echo json_encode(array("message" => "Success at " . date("Y-m-d") . " " . date("h:i:sa")));
}
catch (Exception $e)
{
    http_response_code(501);
    echo json_encode(array("Failure at " . date("Y-m-d") . " " . date("h:i:sa") . " " . $e->getMessage()));
}



function SaveImage($type, $batch_id, $batch_type, $user_id, $db, $conf)
{
    try {
        if($_FILES[$type]['name'] == null)
            return "";
        // Loop through each file

        if(isset($_FILES[$type]['name']))
        {
            $image_name = $_FILES[$type]['name'];
            $valid_extensions = array("jpg","jpeg","png","gif","pdf","docx","doc","xls","xlsx","ppt","pptx","zip","rar","7z","txt","dwg","skp","psd","evo","dwf","bmp");
            $extension = pathinfo($image_name, PATHINFO_EXTENSION);
            if (in_array(strtolower($extension), $valid_extensions)) 
            {
                //$upload_path = 'img/' . time() . '.' . $extension;

                $storage = new StorageClient([
                    'projectId' => 'predictive-fx-284008',
                    'keyFilePath' => $conf::$gcp_key
                ]);

                $bucket = $storage->bucket('feliiximg');

                $upload_name = time() . '_' . pathinfo($image_name, PATHINFO_FILENAME) . '.' . $extension;

                $file_size = filesize($_FILES[$type]['tmp_name']);
                $size = 0;

                $obj = $bucket->upload(
                    fopen($_FILES[$type]['tmp_name'], 'r'),
                    ['name' => $upload_name]);

                $info = $obj->info();
                $size = $info['size'];

                if($size == $file_size && $file_size != 0 && $size != 0)
                {
                    $query = "INSERT INTO gcp_storage_file
                    SET
                        batch_id = :batch_id,
                        batch_type = :batch_type,
                        filename = :filename,
                        gcp_name = :gcp_name,

                        create_id = :create_id,
                        created_at = now()";

                    // prepare the query
                    $stmt = $db->prepare($query);
                
                    // bind the values
                    $stmt->bindParam(':batch_id', $batch_id);
                    $stmt->bindParam(':batch_type', $batch_type);
                    $stmt->bindParam(':filename', $image_name);
                    $stmt->bindParam(':gcp_name', $upload_name);
        
                    $stmt->bindParam(':create_id', $user_id);

                    try {
                        // execute the query, also check if query was successful
                        if ($stmt->execute()) {
                            $last_id = $db->lastInsertId();
                        }
                        else
                        {
                            $arr = $stmt->errorInfo();
                            error_log($arr[2]);
                        }
                    }
                    catch (Exception $e)
                    {
                        error_log($e->getMessage());
                        $db->rollback();
                        http_response_code(501);
                        echo json_encode(array("Failure at " . date("Y-m-d") . " " . date("h:i:sa") . " " . $e->getMessage()));
                        die();
                    }

                    return $upload_name;
                }
                else
                {
                    $message = 'There is an error while uploading file';
                    $db->rollback();
                    http_response_code(501);
                    echo json_encode(array("Failure at " . date("Y-m-d") . " " . date("h:i:sa") . " " . $message));
                    die();
                    
                }
            }
            else
            {
                $message = 'Only Images or Office files allowed to upload';
                $db->rollback();
                http_response_code(501);
                echo json_encode(array("Failure at " . date("Y-m-d") . " " . date("h:i:sa") . " " . $message));
                die();
            }
        }

        
    } catch (Exception $e) {
        $db->rollback();
        http_response_code(501);
        echo json_encode(array("Failure at " . date("Y-m-d") . " " . date("h:i:sa") . " Error uploading, Please use laptop to upload again."));
        die();
    }
}



function formatCheckBox($check)
{
    $ret = "0";
    if($check == true || $check == "true" || $check == "1")
    {
        $ret = "1";
    }

    return $ret;
}