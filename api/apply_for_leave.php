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

        $user_id = $decoded->data->id;
        $apartment_id = $decoded->data->apartment_id;

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

        if($startYear != $endYear)
        {
            http_response_code(401);
            echo json_encode(array("message" => "Leave accross years should be divided into 2 leave applications, leave this year and leave next year."));
            die();
        }

        // leave credit!
        $al_credit = 0;
        $sl_credit = 0;
        $manager_leave = 0;
        $head_of_department = 0; // leave apply without approval

        $query = "SELECT is_manager, annual_leave, sick_leave, manager_leave, head_of_department from user where id = " . $user_id;

        $stmt = $db->prepare( $query );
        $stmt->execute();

        while($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $is_manager = $row['is_manager'];
            $al_credit = $row['annual_leave'];
            $sl_credit = $row['sick_leave'];
            $manager_leave = $row['manager_leave'];
            $head_of_department  = $row['head_of_department'];
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

        if($is_manager == "1")
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

        $query = "SELECT apply_date, apply_period, a.leave_type  from `leave` l LEFT JOIN `apply_for_leave` a ON l.apply_id = a.id WHERE a.uid = " . $user_id . " and a.status = 0 and SUBSTRING(apply_date, 1, 4) = '" . $startYear . "'";
        $stmt = $db->prepare( $query );
        $stmt->execute();
        while($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $apply_date = $row['apply_date'];
            $apply_period = $row['apply_period'];

            if($row['leave_type'] == 'A')
                $al_credit -= 0.5;

            if($row['leave_type'] == 'B')
                $sl_credit -= 0.5;

            if($is_manager == "1")
                $manager_leave -= 0.5;

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
        $query = "SELECT from_date FROM holiday";
        $stmt = $db->prepare( $query );
        $stmt->execute();
        while($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $from_date = $row['from_date'];

            array_push($holiday, $from_date . " A");
            array_push($holiday, $from_date . " P");
        }

        // 3. exclude holiday
        $result = array_diff($leaves, $holiday);

        if($leave_type == 'A')
            $al_credit -= count($result) * 0.5;
        if($leave_type == 'B')
            $sl_credit -= count($result) * 0.5;
        if($is_manager == "1")
                $manager_leave -= count($result) * 0.5;

        if($is_manager == "1" && $manager_leave < 0 && $leave_type != 'C' && $leave_type != 'D')
        {
            http_response_code(401);

            echo json_encode(array("message" => "Apply over yearly credit."));
            die();
        }

        if($is_manager != "1" && $leave_type != 'C' && $leave_type != 'D' && ($sl_credit < 0 || $al_credit < 0))
        {
            http_response_code(401);

            echo json_encode(array("message" => "Apply over yearly credit."));
            die();
        }

        $leave = count($result) * 0.5;

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
        $afl->reason = $reason;
       

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
            }

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
            }

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
