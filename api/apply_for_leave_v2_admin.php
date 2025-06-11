<?php
//error_reporting(0);
//header("Access-Control-Allow-Origin: https://feliix.myvnc.com");
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: POST");
header("Access-Control-Max-Age: 3600");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

$jwt = (isset($_POST['jwt']) ?  $_POST['jwt'] : null);
$leave_type = (isset($_POST['leave_type']) ?  $_POST['leave_type'] : '');
$type = (isset($_POST['type']) ?  $_POST['type'] : '');
$start_date = (isset($_POST['start_date']) ?  $_POST['start_date'] : '');
$start_time = (isset($_POST['start_time']) ?  $_POST['start_time'] : '');
$end_date = (isset($_POST['end_date']) ?  $_POST['end_date'] : '');
$end_time = (isset($_POST['end_time']) ?  $_POST['end_time'] : '');
$leave = (isset($_POST['leave']) ?  $_POST['leave'] : 0);
$reason = (isset($_POST['reason']) ?  $_POST['reason'] : '');

$uid = (isset($_POST['uid']) ?  $_POST['uid'] : 0);

// parameter for check
$is_manager = (isset($_POST['is_manager']) ?  $_POST['is_manager'] : '');
$timeStart = (isset($_POST['timeStart']) ?  $_POST['timeStart'] : '');
$amStart = (isset($_POST['amStart']) ?  $_POST['amStart'] : '');
$timeEnd = (isset($_POST['timeEnd']) ?  $_POST['timeEnd'] : '');
$amEnd = (isset($_POST['amEnd']) ?  $_POST['amEnd'] : '');
$leave_type = (isset($_POST['leave_type']) ?  $_POST['leave_type'] : '');

include_once 'config/core.php';
include_once 'libs/php-jwt-master/src/BeforeValidException.php';
include_once 'libs/php-jwt-master/src/ExpiredException.php';
include_once 'libs/php-jwt-master/src/SignatureInvalidException.php';
include_once 'libs/php-jwt-master/src/JWT.php';

include_once 'config/database.php';
include_once 'objects/apply_for_leave.php';
include_once 'objects/leave.php';
include_once 'config/conf.php';

include_once 'mail.php';

$database = new Database();
$db = $database->getConnection();

$afl = new ApplyForLeave($db);
$le = new Leave($db);

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

        $user_id = $uid;

        $data = userCanLogin($uid, $db);

        $apartment_id = $data['apartment_id'];

        $user_name = $data['username'];
        $user_department = $data['department'];

        $leaves = array();
        $applied = array();
        $holiday = array();

        if($timeStart == '' && $timeEnd == '')
        {
            http_response_code(401);
            echo json_encode(array("message" => "Apply Date not valid."));
            die();
        }

        if($timeStart > $timeEnd)
        {
            http_response_code(401);
            echo json_encode(array("message" => "Apply Date not valid."));
            die();
        }

        $startYear = substr($timeStart, 0, 4);
        $endYear = substr($timeEnd, 0, 4);

        // leave credit!
        $sil_credit = 0;
        $vl_sl_credit = 0;
        $vl_credit = 0;
        $sl_credit = 0;
        $halfday_credit = 0;
        $leave_level = 0; 
        $head_of_department = 0; // leave apply without approval

        $query = "SELECT leave_level, sil, vl_sl, vl, sl, halfday, head_of_department from user where id = " . $user_id;

        $stmt = $db->prepare( $query );
        $stmt->execute();

        while($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $sil_credit = $row['sil'];
            $vl_sl_credit = $row['vl_sl'];
            $vl_credit = $row['vl'];
            $sl_credit = $row['sl'];
            $leave_level  = $row['leave_level'];
            $halfday_credit  = $row['halfday'];

            $head_of_department  = $row['head_of_department'];
        }

        // 20201130 is manager can leave across year
        if(($startYear != $endYear) && ($leave_level == "A"))
        {
            http_response_code(401);
            echo json_encode(array("message" => "Leave accross years should be divided into 2 leave applications, leave this year and leave next year."));
            die();
        }

        // 1. Check if history have the same day
        $begin = new DateTime($timeStart);
        $end = new DateTime($timeEnd);


        $interval = DateInterval::createFromDateString('1 day');
        $period = new DatePeriod($begin, $interval, $end);

        foreach ($period as $dt) {
            array_push($leaves, $dt->format("Ymd") . " A");
            array_push($leaves, $dt->format("Ymd") . " P");
        }

        array_push($leaves, $end->format("Ymd") . " A");

        if($leave_level == "B" || $leave_level == "C" || $leave_type == "H" || $leave_type == "U")
        {
            if($amStart == "P")
                unset($leaves[0]);

            if($amEnd == "P")
                array_push($leaves, $end->format("Ymd") . " P");
        }
        else
        {
            array_push($leaves, $end->format("Ymd") . " P");
        }

        if($leave_level == "B" || $leave_level == "C")
        {
            $headPeriodStart = date("Y-m-d",strtotime("last year Dec 1st"));
            $headPeriodEnd = date("Y-m-d",strtotime("this year Nov 30"));

            $tailPeriodStart = date("Y-m-d",strtotime("this year Dec 1st"));
            $tailPeriodEnd = date("Y-m-d",strtotime("next year Nov 30"));

            if($timeStart > $headPeriodEnd)
                $query = "SELECT apply_date, apply_period, a.leave_type  from `leave` l LEFT JOIN `apply_for_leave` a ON l.apply_id = a.id where a.uid = " . $user_id . " and a.status in (0, 1) and apply_date >= '" . str_replace('-', '', $tailPeriodStart) . "' and apply_date <= '" . str_replace('-', '', $tailPeriodEnd) . "' ";
            else
                $query = "SELECT apply_date, apply_period, a.leave_type  from `leave` l LEFT JOIN `apply_for_leave` a ON l.apply_id = a.id where a.uid = " . $user_id . " and a.status in (0, 1) and apply_date >= '" . str_replace('-', '', $headPeriodStart) . "' and apply_date <= '" . str_replace('-', '', $headPeriodEnd) . "' ";
        }
        else
            $query = "SELECT apply_date, apply_period, a.leave_type  from `leave` l LEFT JOIN `apply_for_leave` a ON l.apply_id = a.id WHERE a.uid = " . $user_id . " and a.status in (0, 1) and SUBSTRING(apply_date, 1, 4) = '" . $startYear . "'";
            
        $stmt = $db->prepare( $query );
        $stmt->execute();
        while($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $apply_date = $row['apply_date'];
            $apply_period = $row['apply_period'];

            if($row['leave_type'] == 'N')
            {
                if($sil_credit > 0)
                    $sil_credit -= 0.5;
                else if($vl_credit > 0)
                    $vl_credit -= 0.5;
                else
                    $vl_sl_credit -= 0.5;
            }
            
            if($row['leave_type'] == 'S')
            {
                if($sil_credit > 0)
                    $sil_credit -= 0.5;
                else if($sl_credit > 0)
                    $sl_credit -= 0.5;
                else
                    $vl_sl_credit -= 0.5;
            }

            if($row['leave_type'] == 'H')
            {
                if($halfday_credit > 0)
                    $halfday_credit -= 0.5;
          
            }
        
            array_push($applied, $apply_date . " " . $apply_period);
        }

        $inter = array_intersect($leaves, $applied);
        if(count($inter) > 0)
        {
            http_response_code(401);

            echo json_encode(array("message" => "Duplicate apply."));
            die();
        }

        // 2. over credit
        $query = "SELECT from_date FROM holiday where location = 'Philippines' ";
        $stmt = $db->prepare( $query );
        $stmt->execute();
        while($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $from_date = $row['from_date'];

            array_push($holiday, $from_date . " A");
            array_push($holiday, $from_date . " P");
        }


        // 3. exclude holiday
        $result = array_diff($leaves, $holiday);

        $sil_consume = 0;
        $vl_consume = 0;
        $sl_consume = 0;
        $vl_sl_consume = 0;
        $ul_consume = 0;
        $halfday_consume = 0;

        for($i=0; $i<count($result); $i++)
        {
        
            if($leave_type == 'N')
            {
                if($sil_credit > 0)
                {
                    $sil_credit -= 0.5;
                    $sil_consume += 0.5;
                }
                else if($vl_credit > 0)
                {
                    $vl_credit -= 0.5;
                    $vl_consume += 0.5;
                }
                else
                {
                    $vl_sl_credit -= 0.5;
                    $vl_sl_consume += 0.5;
                }
            }
            
            if($leave_type == 'S')
            {
                if($sil_credit > 0)
                {
                    $sil_credit -= 0.5;
                    $sil_consume += 0.5;
                }
                else if($sl_credit > 0)
                {
                    $sl_credit -= 0.5;
                    $sl_consume += 0.5;
                }
                else
                {
                    $vl_sl_credit -= 0.5;
                    $vl_sl_consume += 0.5;
                }
            }

            if($leave_type == 'U')
            {
                $ul_consume += 0.5;
            }
            
            if($leave_type == 'H')
            {
                $halfday_credit -= 0.5;
                $halfday_consume += 0.5;
            }
        }

        if($halfday_consume < 0)
        {
            http_response_code(401);

            echo json_encode(array("message" => "Your yearly credit of manager halfday planning is not enough."));
            die();
        }

        if($sil_credit < 0 || $vl_sl_credit < 0 || $vl_credit < 0 || $sl_credit < 0)
        {
            http_response_code(401);

            echo json_encode(array("message" => "Leave credit is not enough."));
            die();
        }

        $leave = count($result) * 0.5;

        // 4. sliding window checking
        $second_flag = false;
        if(count($result) * 0.5 == 1)
        {
            $count = 0;
            $begin = new DateTime($timeStart);
            $head = CounterSlideWindow($holiday, $applied, $begin, 2, $count);

            $end = new DateTime($timeEnd);
            $tail = SlideWindow($holiday, $applied, $end, 2, $count);

            if($head > 1 || $tail > 1)
                $second_flag = true;
        }

        if(count($result) * 0.5 == 2)
        {
            $count = 0;
            $begin = new DateTime($timeStart);
            $head = CounterSlideWindow($holiday, $applied, $begin, 1, $count);

            $end = new DateTime($timeEnd);
            $tail = SlideWindow($holiday, $applied, $end, 1, $count);

            if($head > 0 || $tail > 0)
                $second_flag = true;
        }
      
        // now you can apply
        $filename = "";

        try {
            if (isset($_FILES['file']['name'])) {
                $conf = new Conf();
                $key = "myKey";
                $time = time();
                $hash = hash_hmac('sha256', $time, $key);
                $ext = pathinfo($_FILES['file']['name'], PATHINFO_EXTENSION);
                $filename = $time . $hash . "." . $ext;
                if (move_uploaded_file($_FILES["file"]["tmp_name"], $conf::$upload_path . $filename)) {
                    //    echo "done";
                }
                // certificate doesn't need compress.
                // compress_image($conf::$upload_path . $filename, $conf::$upload_path . $filename, 60);
            }
        }catch (Exception $e){

            //http_response_code(401);

            //echo json_encode(array("message" => "Access denied."));
            //die();
        }

        $afl->uid = $user_id;
        $afl->leave_type = $leave_type;
        $afl->start_date = $start_date;
        $afl->start_time = $start_time;
        $afl->end_date = $end_date;
        $afl->end_time = $end_time;
        $afl->pic_url = $filename;
        $afl->leave = $leave;

        $afl->leave_level = $leave_level;
        $afl->sil = $sil_consume;
        $afl->vl = $vl_consume;
        $afl->sl = $sl_consume;
        $afl->vl_sl = $vl_sl_consume;
        $afl->ul = $ul_consume;
        $afl->halfday = $halfday_consume;

        if($second_flag == true)
            $afl->too_many = "Y";
        else
            $afl->too_many = "";
            
        $afl->reason = $reason;

        $leav_msg = "[" . $user_department . "] " . $user_name . " apply leave from " . $start_date . " " . $start_time . " to " . $end_date . " " . $end_time;
       

        $id = $afl->create();

        if(empty($id))
        {
            http_response_code(401);
            echo json_encode(array("message" => "Apply Fail at" . date("Y-m-d") . " " . date("h:i:sa")));
        }else
        {
            foreach ($result as &$value) {
                $leaf = explode(" ", $value);

                $le->uid = $user_id;
                $le->apply_id = $id;
                $le->apply_date = $leaf[0];
                $le->apply_period = $leaf[1];
                $le->duration = 0.5;
                $le->leave_type = $leave_type;
                $res = $le->create();
                if(empty($res))
                {
                    http_response_code(401);
                    echo json_encode(array("message" => "Apply Fail at" . date("Y-m-d") . " " . date("h:i:sa")));
                }
            }

            // Apply without approval
            if($leave_type == 'D' || $head_of_department == 1)
            {
                $ret = false;
                $ret = $afl->approval($id, $user_id);
                if(!$ret)
                {
                    http_response_code(401);
                    echo json_encode(array("message" => "Apply Fail at" . date("Y-m-d") . " " . date("h:i:sa")));
                }

                $ret = $afl->re_approval($id, $user_id);
                if(!$ret)
                {
                    http_response_code(401);
                    echo json_encode(array("message" => "Apply Fail at" . date("Y-m-d") . " " . date("h:i:sa")));
                }

                http_response_code(200);
                echo json_encode(array("message" => "Apply Success at " . date("Y-m-d") . " " . date("h:i:sa")));
                die();
            }

            // now decide who get mail
            $who_get_mail = 1;

            // first approver goes to second approver
            $query = "select * from leave_flow where flow = 1 and apartment_id = " . $apartment_id . " and uid = " . $user_id;

            $stmt = $db->prepare( $query );
            $stmt->execute();

            $first_approver = 0;
            while($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                $first_approver = 1;
            }

            if($first_approver == 1)
            {
                $ret = false;
                $ret = $afl->approval($id, $user_id);
                if(!$ret)
                {
                    http_response_code(401);
                    echo json_encode(array("message" => "Apply Fail at" . date("Y-m-d") . " " . date("h:i:sa")));
                }

                $who_get_mail = 2;
            }

            // send mail to approver
            $mail_name = '';
            $mail_email = '';
            $mail_id = '';

            $first_name = '';
            $first_email = '';
            $first_uid = 0;

            $second_name = '';
            $second_email = '';
            $second_uid = 0;

            $query = "select uid, username, email, flow from leave_flow lf LEFT JOIN user u ON lf.uid = u.id where u.STATUS = 1 and lf.apartment_id = " . $apartment_id . " order by flow";

            $stmt = $db->prepare( $query );
            $stmt->execute();
            while($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                if($row['flow'] == 1)
                {
                    $first_name = $row['username'];
                    $first_email = $row['email'];
                    $first_uid = $row['uid'];
                }

                if($row['flow'] == 2)
                {
                    $second_name = $row['username'];
                    $second_email = $row['email'];
                    $second_uid = $row['uid'];
                }
            }

            // if first approver leave
            $query = "SELECT * FROM `leave` WHERE apply_date >= DATE_FORMAT(NOW(), '%Y%m%d') AND apply_date <=  DATE_FORMAT(NOW(), '%Y%m%d') AND uid = " . $first_uid . " AND STATUS <> -1 limit 1";

            $stmt = $db->prepare( $query );
            $stmt->execute();


            $mail_name = $first_name;
            $mail_email = $first_email;
            $mail_id = $first_uid;


            while($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                $mail_name = $second_name;
                $mail_email = $second_email;
                $mail_id = $second_uid;

                // $ret = false;
                // $ret = $afl->approval($id, $user_id);
                // if(!$ret)
                // {
                //     http_response_code(401);
                //     echo json_encode(array("message" => "Apply Fail at" . date("Y-m-d") . " " . date("h:i:sa")));
                // }

            }

            if($who_get_mail == 2){
                $mail_name = $second_name;
                $mail_email = $second_email;
                $mail_id = $second_uid;
            }

            $date = new DateTime();

            $leaver = "";
            $department = "";
            $app_time = "";
            $leave_type = "";
            $start_time = "";
            $end_time = "";
            $leave_length = 0;
            $reason = "";
            $imgurl = "";

            // first approver goes to second approver
            $query = "SELECT u.username, d.department, a.created_at, a.leave_type, a.`leave`, a.start_date, a.start_time, a.end_date, a.end_time, a.reason, a.pic_url  from `user` u LEFT JOIN `apply_for_leave` a ON u.id = a.uid LEFT JOIN user_department d ON d.id = u.apartment_id WHERE a.id = ". $id . " AND u.`status` <> -1 ";

            $stmt = $db->prepare( $query );
            $stmt->execute();

            while($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                $leaver = $row['username'];
                $department = $row['department'];
                $app_time = $row['created_at'];
                $leave_type = getLeaveType($row['leave_type']);
                $start_time = formateDate($row['start_date']) . " " . $row['start_time'];
                $end_time = formateDate($row['end_date']) . " " . $row['end_time'];
                $leave_length = $row['leave'];
                $reason = $row['reason'];
                $imgurl = $row['pic_url'];
            }

            $par_approve = "leave_id=". $id . "&uid=" . $mail_id. "&action=approve&time=" . $date->getTimestamp();
            $par_reject = "leave_id=". $id . "&uid=" . $mail_id. "&action=reject&time" . $date->getTimestamp();

            $conf = new Conf();

            $appove_hash = $conf::$mail_ip . "api/leave_record_approval_hash?p=" . base64url_encode(passport_encrypt($par_approve));
            $reject_hash = $conf::$mail_ip . "api/leave_record_approval_hash?p=" . base64url_encode(passport_encrypt($par_reject));

            sendMail($mail_name, $mail_email, $appove_hash, $reject_hash, $leav_msg, $leaver, $department, $app_time, $leave_type, $start_time, $end_time, $leave_length, $reason, $imgurl);


            http_response_code(200);
            echo json_encode(array("message" => "Apply Success at " . date("Y-m-d") . " " . date("h:i:sa")));
        }

    }
        // if decode fails, it means jwt is invalid
    catch (Exception $e){

        http_response_code(401);

        echo json_encode(array("message" => "Access denied."));

    }
}


function getLeaveType($type){
    $leave_type = '';

    if($type =="A")
        $leave_type = "Service Incentive Leave";
    if($type =="N")
        $leave_type = "Vaction Leave";
    if($type =="B" || $type =="S")
        $leave_type = "Sick Leave";
    if($type =="C" || $type =="U")
        $leave_type = "Unpaid Leave";
    if($type =="D")
        $leave_type = "Absence";
    if($type =="H")
        $leave_type = "Manager Halfday Planning";
    
    return $leave_type;
}

function formateDate($_date){
    return substr($_date, 0, 4)."/".substr($_date, 4, 2)."/".substr($_date, 6, 2);
}

function CounterSlideWindow($holiday, $applied, $date, $n, $count)
{
    if($n == 0)
        return $count;

    $leaves = array();
    $check_date = $date->modify('-1 day');

    array_push($leaves, $check_date->format("Ymd") . " A");
    array_push($leaves, $check_date->format("Ymd") . " P");

    $inter = array();
    $inter = array_intersect($leaves, $applied);
    if(count($inter) > 0)
    {
        $count++;
    }

    $holi = array();
    $holi = array_intersect($leaves, $holiday);
    if(count($holi) > 0)
        $count = CounterSlideWindow($holiday, $applied, $check_date, $n, $count);
    else
        $count = CounterSlideWindow($holiday, $applied, $check_date, $n - 1, $count);

    return $count;
}

function SlideWindow($holiday, $applied, $date, $n, $count)
{
    if($n == 0)
        return $count;

    $leaves = array();
    

    $check_date = $date->modify('1 day');

    array_push($leaves, $check_date->format("Ymd") . " A");
    array_push($leaves, $check_date->format("Ymd") . " P");

    $inter = array();
    $inter = array_intersect($leaves, $applied);
    if(count($inter) > 0)
    {
        $count++;
    }

    $holi = array();
    $holi = array_intersect($leaves, $holiday);
    if(count($holi) > 0)
        $count = SlideWindow($holiday, $applied, $check_date, $n, $count);
    else
        $count = SlideWindow($holiday, $applied, $check_date, $n - 1, $count);

    return $count;
}


function userCanLogin($uid, $db){
    // query to check if email exists
    $query = "SELECT user.id, username, password, user.status, is_admin, need_punch, COALESCE(department, '') department, 
            apartment_id, title_id, COALESCE(title, '') title, annual_leave, sick_leave, COALESCE(is_manager, 0) is_manager, COALESCE(test_manager, '0') test_manager, manager_leave, user_title.head_of_department,user.is_viewer, user.pic_url, user.leave_level
            FROM user
            LEFT JOIN user_department ON user.apartment_id = user_department.id 
            LEFT JOIN user_title ON user.title_id = user_title.id
            WHERE user.id = ? ";

    $data = array();

    // prepare the query
    $stmt = $db->prepare( $query );

    // bind given email value
    $stmt->bindParam(1, $uid);

    // execute the query
    $stmt->execute();

    // get number of rows
    $num = $stmt->rowCount();

    // if email exists, assign values to object properties for easy access and use for php sessions
    if($num>0){
        $data = $stmt->fetch(PDO::FETCH_ASSOC);
    }

    // return false if email does not exist in the database
    return $data;
}