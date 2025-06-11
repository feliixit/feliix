<?php
include_once 'config/core.php';
include_once 'config/conf.php';
require_once '../vendor/autoload.php';
include_once 'config/database.php';

include_once 'mail.php';

date_default_timezone_set('Asia/Taipei');

$database = new Database();
$db = $database->getConnection();

$database_sea = new Database_Sea();
$db_sea = $database_sea->getConnection();

$cached_loading = get_all_container_eta_arrived($db_sea);
$cached_air = get_all_air_eta_arrived($db_sea);

$sql = "SELECT pm.id, 
            pm.od_name,
            pm.status, 
            pm.task_id,
            pm.order_type,
            p.project_name,
            p.id as project_id,
            ps.id as stage_id,
            pm.serial_name,
            pot.status project_status,
            c_user.username AS created_by, 
            u_user.username AS updated_by,
            DATE_FORMAT(pm.created_at, '%Y-%m-%d %H:%i:%s') created_at, 
            DATE_FORMAT(pm.updated_at, '%Y-%m-%d %H:%i:%s') updated_at
            FROM od_main pm 
            left join project_other_task pot on pm.task_id = pot.id
            left join project_stages ps on pot.stage_id = ps.id
            LEFT JOIN user c_user ON pm.create_id = c_user.id 
            LEFT JOIN user u_user ON pm.updated_id = u_user.id 
            left join project_main p on ps.project_id = p.id
            where pm.status = 0 ";


$stmt = $db->prepare($sql);
$stmt->execute();
while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
    $items = get_all_items($row['id'], $db);

    $project_name = $row["project_name"];
    $serial_name = $row["serial_name"];
    $od_name = $row["od_name"];
    $od_id = $row["id"];

    $changes = [];

    foreach ($items as $item) {
        $shipping_number = $item['shipping_number'];
        $eta = $item['eta'];
        $arrived = $item['arrive'];
        $date_send = $item['date_send'];

        $shipping_way = $item['shipping_way'];

        if($shipping_number == '')
            continue;

        // search cached_loading by container_number
        if($shipping_way == 'sea')
        {
            foreach ($cached_loading as $loading) {
                
                if (strpos($loading['container_number'], $shipping_number) !== false)
                {
                    if($eta != $loading['eta_date'] || $arrived != $loading['date_arrive'] || $date_send != $loading['date_sent'])
                    {
                        update_item_sea($item, $loading['eta_date'], $loading['date_arrive'], $loading['date_sent'], $db);

                        // if not contain changes then added
                        if(!in_array($item, $changes))
                            $changes[] = $item;
                    }
                
                }
            }
        }

        if($shipping_way == 'air')
        {
            foreach ($cached_air as $loading) {
                
                if ($loading['id'] == $shipping_number)
                {
                    if($eta != $loading['eta_date'] || $arrived != $loading['date_arrive'])
                    {
                        update_item($item, $loading['eta_date'], $loading['date_arrive'], $db);

                        // if not contain changes then added
                        if(!in_array($item, $changes))
                            $changes[] = $item;
                    }
                
                }
            }
        }

    }

    if(count($changes) > 0)
    {
        order_notification('', 'access4', 'access1,access2,access3,access5', $project_name, $serial_name, $od_name, 'Order - Close Deal', '', 'batch', $changes, $od_id);
    }

}

function update_item_sea($item, $eta, $arrive, $date_send, $db)
{
    $sql = "UPDATE od_item SET eta = :eta, arrive = :arrive, date_send = :date_send WHERE id = :id";
    $stmt = $db->prepare($sql);
    $stmt->bindParam(':id', $item['id']);
    $stmt->bindParam(':eta', $eta);
    $stmt->bindParam(':arrive', $arrive);
    $stmt->bindParam(':date_send', $date_send);
    $stmt->execute();
}

function update_item($item, $eta, $arrive, $db)
{
    $sql = "UPDATE od_item SET eta = :eta, arrive = :arrive WHERE id = :id";
    $stmt = $db->prepare($sql);
    $stmt->bindParam(':id', $item['id']);
    $stmt->bindParam(':eta', $eta);
    $stmt->bindParam(':arrive', $arrive);

    $stmt->execute();
}

function get_all_items($od_id, $db)
{
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
                    srp,
                    date_needed,
                    shipping_way,
                    shipping_number,
                    shipping_vendor,
                    pid,
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
                    FROM od_item, 
                    (SELECT @a:=@a+1 serial_number, id FROM od_item, (SELECT @a:= 0) AS a WHERE status <> -1 and od_id=$od_id order by ABS(sn)) b
                    WHERE status <> -1 and od_id=$od_id and od_item.id = b.id
                    ";

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
        
        $confirm_text = "";
        $brand = $row['brand'];
        $brand_other = $row['brand_other'];
        $photo1 = ($row['photo1'] != '') ? 'https://storage.googleapis.com/feliiximg/' . $row['photo1'] : '';
        $photo2 = ($row['photo2'] != '') ? 'https://storage.googleapis.com/feliiximg/' . $row['photo2'] : '';
        $photo3 = ($row['photo3'] != '') ? 'https://storage.googleapis.com/feliiximg/' . $row['photo3'] : '';
        $code = $row['code'];
        $brief = $row['brief'];
        $listing = $row['listing'];
        $qty = $row['qty'];
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
        $test = $row['test'];
        $delivery = $row['delivery'];
        $final = $row['final'];

        $pid = $row['pid'];

        $serial_number = $row['serial_number'];

        $status = $row['status'];
        $notes = "";

        $notes_a = "";
        
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
            "pid" => $pid,
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
            "notes" => $notes,
            "notes_a" => $notes_a,
            "serial_number" => $serial_number,
        );
    }

    return $merged_results;
}


function get_all_container_eta_arrived($db) {
    $sql = "select 
                lo.id, container_number, 
                COALESCE(lo.eta_date, '') eta_date, 
                COALESCE(lo.date_arrive, '') date_arrive,
                COALESCE(lo.date_sent, '') date_sent
            FROM loading lo 
            WHERE lo.status = '' order by lo.id ";

    $stmt = $db->prepare($sql);
    $stmt->execute();
    $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
    return $result;
}

function get_all_air_eta_arrived($db) {
    $sql = "select id, flight_date eta_date, SUBSTRING(date_arrive, 1, 10) date_arrive from airship_records";

    $stmt = $db->prepare($sql);
    $stmt->execute();
    $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
    return $result;
}