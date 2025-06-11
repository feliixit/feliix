<?php
error_reporting(0);
//header("Access-Control-Allow-Origin: https://feliix.myvnc.com");
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: *");
header("Access-Control-Max-Age: 3600");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

$id = (isset($_GET['id']) ?  $_GET['id'] : 0);
$type = (isset($_GET['type']) ?  $_GET['type'] : 0);
$confirm = (isset($_GET['confirm']) ?  $_GET['confirm'] : '');
$jwt = (isset($_COOKIE['jwt']) ?  $_COOKIE['jwt'] : null);
$pg = (isset($_GET['pg']) ?  $_GET['pg'] : 0);
$user_id = 0;


include_once 'config/core.php';
include_once 'libs/php-jwt-master/src/BeforeValidException.php';
include_once 'libs/php-jwt-master/src/ExpiredException.php';
include_once 'libs/php-jwt-master/src/SignatureInvalidException.php';
include_once 'libs/php-jwt-master/src/JWT.php';

include_once 'config/database.php';
include_once 'config/conf.php';
require_once '../vendor/autoload.php';

$database = new Database();
$db = $database->getConnection();
$conf = new Conf();

use Google\Cloud\Storage\StorageClient;

use \Firebase\JWT\JWT;

if (!isset($jwt)) {
    http_response_code(401);

    echo json_encode(array("message" => "Access denied1."));
    die();
} else {

    // decode jwt
    $decoded = JWT::decode($jwt, $key, array('HS256'));
    $GLOBALS["user_id"] = $decoded->data->id;

    $merged_results = array();
    

    $query = "SELECT od_item.id, b.serial_number,
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
                    shipping_vendor,
                    pid,
                    v1,
                    v2,
                    v3,
                    v4,
                    ps_var,
                    eta,
                    date_send,
                    arrive,
                    remark,
                    remark_t,
                    remark_d,
                    check_t,
                    check_d,
                    charge,
                    photo4,
                    photo5,
                    photo4_name,
                    photo5_name,
                    test,
                    delivery,
                    final,
                    btn2,
                    `which_pool`,
                    `as_sample`,
                    `status`,
                    test_updated_name,
                    test_updated_at,
                    delivery_updated_name,
                    delivery_updated_at,
                    normal,
                    status_at,
                    received_list
                    FROM od_item, 
                    (SELECT @a:=@a+1 serial_number, id FROM od_item, (SELECT @a:= 0) AS a WHERE status <> -1 and od_id=$id order by ABS(sn)) b
                    WHERE status <> -1 and od_id=$id and od_item.id = b.id
                    ";
                    

    if($confirm != '')
        $query = $query . " and confirm = '$confirm' ";

    if($pg != 0)
    {
        if($pg == 1)
            $query = $query . " and status <= $pg ";
        else if($pg == 2)
            $query = $query . " and status = $pg ";
        else if($pg == 3)
            $query = $query . " and status >= $pg ";
    }
        


    $query = $query . " order by ABS(sn) ";

    $stmt = $db->prepare($query);
    $stmt->execute();

    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        $id = $row['id'];
        $sn = $row['sn'];
        $confirm = $row['confirm'];

        // send to tw for note
        if($row['status'] == 1)
            $confirm = "W";
        // for approval
        if($row['status'] == 2)
            $confirm = "F";
        
        $confirm_text = GetConfirmText($confirm, $db);
        $brand = $row['brand'];
        $brand_other = $row['brand_other'];
        $photo1 = ($row['photo1'] != '') ? 'https://storage.googleapis.com/feliiximg/' . $row['photo1'] : '';
        $photo2 = ($row['photo2'] != '') ? 'https://storage.googleapis.com/feliiximg/' . $row['photo2'] : '';
        $photo3 = ($row['photo3'] != '') ? 'https://storage.googleapis.com/feliiximg/' . $row['photo3'] : '';
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
        $photo4 = ($row['photo4'] != '') ? 'https://storage.googleapis.com/feliiximg/' . $row['photo4'] : '';
        $photo5 = ($row['photo5'] != '') ? 'https://storage.googleapis.com/feliiximg/' . $row['photo5'] : '';
        $photo4_name = $row['photo4_name'];
        $photo5_name = $row['photo5_name'];
        $test = $row['test'];
        $delivery = $row['delivery'];
        $final = $row['final'];

        $pid = $row['pid'];

        $v1 = $row['v1'];
        $v2 = $row['v2'];
        $v3 = $row['v3'];
        $v4 = $row['v4'];

        $ps_var = json_decode($row['ps_var'] == null ? "[]" : $row['ps_var'], true);

        $received_list = json_decode($row['received_list'] == null ? "[]" : $row['received_list'], true);

        $serial_number = $row['serial_number'];

        $test_updated_name = $row['test_updated_name'];
        $test_updated_at = $row['test_updated_at'];
        $delivery_updated_name = $row['delivery_updated_name'];
        $delivery_updated_at = $row['delivery_updated_at'];

        $btn2 = $row['btn2'];
        $which_pool = $row['which_pool'];
        $as_sample = $row['as_sample'];

        $status = $row['status'];

        $normal = $row['normal'];

        if($row['status_at'] != null)
            $status_at = date_format(date_create($row['status_at']), "Y-m-d");
        else
            $status_at = "";
        $date_send = $row['date_send'];

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
            "shipping_vendor" => $shipping_vendor,
            "pid" => $pid,
            "v1" => $v1,
            "v2" => $v2,
            "v3" => $v3,
            "v4" => $v4,
            "ps_var" => $ps_var,
            "eta" => $eta,
            "date_send" => $date_send, 
            "arrive" => $arrive,
            "remark" => $remark,
            "remark_t" => $remark_t,
            "remark_d" => $remark_d,
            "check_t" => $check_t,
            "check_d" => $check_d,
            "charge" => $charge,
            "photo4" => $photo4,
            "photo5" => $photo5,
            "photo4_name" => $photo4_name,
            "photo5_name" => $photo5_name,
            "test" => $test,
            "delivery" => $delivery,
            "final" => $final,
            "status" => $status,
            "btn2" => $btn2,
            "which_pool" => $which_pool,
            "as_sample" => $as_sample,
            "test_updated_name" => $test_updated_name,
            "test_updated_at" => $test_updated_at,
            "delivery_updated_name" => $delivery_updated_name,
            "delivery_updated_at" => $delivery_updated_at,
            "confirm_text" => $confirm_text,
            "notes" => $notes,
            "notes_a" => $notes_a,
            "serial_number" => $serial_number,
            "normal" => $normal,
            "status_at" => $status_at,
            "date_send" => $date_send,
            "received_list" => $received_list,
        );
    }



    echo json_encode($merged_results, JSON_UNESCAPED_SLASHES);
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
