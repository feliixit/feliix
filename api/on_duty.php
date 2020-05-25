<?php
error_reporting(0);
//header("Access-Control-Allow-Origin: https://feliix.myvnc.com");
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: POST");
header("Access-Control-Max-Age: 3600");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

$jwt = (isset($_POST['jwt']) ?  $_POST['jwt'] : null);
$today = (isset($_POST['today']) ?  $_POST['today'] : '');
$type = (isset($_POST['type']) ?  $_POST['type'] : '');
$location = (isset($_POST['location']) ?  $_POST['location'] : '');
$explan = (isset($_POST['explan']) ?  $_POST['explan'] : '');
$remark = (isset($_POST['remark']) ?  $_POST['remark'] : '');
$duty_time = (isset($_POST['time']) ?  $_POST['time'] : '');
$latitude = (isset($_POST['latitude']) ?  $_POST['latitude'] : 0.0);
$longitude = (isset($_POST['longitude']) ?  $_POST['longitude'] : 0.0);
$piclatitude = (isset($_POST['piclatitude']) ?  $_POST['piclatitude'] : 0.0);
$piclongitude = (isset($_POST['piclongitude']) ?  $_POST['piclongitude'] : 0.0);
$photo_time = (isset($_POST['photo_time']) ?  $_POST['photo_time'] : '');
$photo_gps = (isset($_POST['photo_gps']) ?  $_POST['photo_gps'] : '');

include_once 'config/core.php';
include_once 'libs/php-jwt-master/src/BeforeValidException.php';
include_once 'libs/php-jwt-master/src/ExpiredException.php';
include_once 'libs/php-jwt-master/src/SignatureInvalidException.php';
include_once 'libs/php-jwt-master/src/JWT.php';

include_once 'config/database.php';
include_once 'objects/on_duty.php';
include_once 'config/conf.inc';

$database = new Database();
$db = $database->getConnection();

$onduty = new OnDuty($db);


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

        $filename = "";
        $s_lat = 0.0;
        $s_lng = 0.0;
        $s_time = "";


        try {
            if (isset($_FILES['file']['name'])) {
                $conf = new Conf();
                $key = "myKey";
                $time = time();
                $hash = hash_hmac('sha256', $time, $key);
                $ext = pathinfo($_FILES['file']['name'], PATHINFO_EXTENSION);
                $filename = $time . $hash . "." . $ext;
                if (move_uploaded_file($_FILES["file"]["tmp_name"], $conf::$upload_path . $filename)) {
                        $result = triphoto_getGPS($conf::$upload_path . $filename);

                    $s_lat = (is_float($result['latitude']) ? $result['latitude'] : 0.0);
                    $s_lng = (is_float($result['longitude']) ? $result['longitude'] : 0.0);
                    $s_time = $result['time'];
                }
            }
        }catch (Exception $e){

            //http_response_code(401);

            //echo json_encode(array("message" => "Access denied."));
            //die();
        }

        $onduty->uid = $user_id;
        $onduty->duty_date = $today;
        $onduty->duty_type = $type;
        $onduty->location = $location;
        $onduty->remark = $remark;
        $onduty->duty_time = $duty_time;
        $onduty->explain = $explan;
        $onduty->pic_url = $filename;
        $onduty->pic_time = $photo_time;
        $onduty->lat = $latitude;
        $onduty->lng = $longitude;
        $onduty->pic_lat = $piclatitude;
        $onduty->pic_lng = $piclongitude;
        $onduty->pic_server_time = $s_time;
        $onduty->pic_server_lat = $s_lat;
        $onduty->pic_server_lng = $s_lng;

        $id = $onduty->create();

        if(empty($id))
        {
            http_response_code(200);
            echo json_encode(array("message" => "Punch Fail at" . date("Y-m-d") . " " . date("h:i:sa")));
        }else
        {
            http_response_code(200);
            echo json_encode(array("message" => "Punch Success at " . date("Y-m-d") . " " . date("h:i:sa")));
        }

    }
        // if decode fails, it means jwt is invalid
    catch (Exception $e){

        http_response_code(401);

        echo json_encode(array("message" => "Access denied."));

    }
}

function triphoto_getGPS($fileName)
{
    //get the EXIF
    try{
        $exif = exif_read_data($fileName);
    }catch (Exception $e)
    {
        $result['latitude'] = 0.0;
        $result['longitude'] = 0.0;
        $result['time'] = "";
        return $result;
    }

    try{
        if($exif["GPSLatitudeRef"] == 'S')
        {
            $es=1;
        }
    }catch (Exception $e)
    {
        $result['latitude'] = 0.0;
        $result['longitude'] = 0.0;
        $result['time'] = "";
        return $result;
    }

    //get the Hemisphere multiplier
    $LatM = 1; $LongM = 1;
    if($exif["GPSLatitudeRef"] == 'S')
    {
        $LatM = -1;
    }
    if($exif["GPSLongitudeRef"] == 'W')
    {
        $LongM = -1;
    }

    //get the GPS data
    $gps['LatDegree']=$exif["GPSLatitude"][0];
    $gps['LatMinute']=$exif["GPSLatitude"][1];
    $gps['LatgSeconds']=$exif["GPSLatitude"][2];
    $gps['LongDegree']=$exif["GPSLongitude"][0];
    $gps['LongMinute']=$exif["GPSLongitude"][1];
    $gps['LongSeconds']=$exif["GPSLongitude"][2];

    //convert strings to numbers
    foreach($gps as $key => $value)
    {
        $pos = strpos($value, '/');
        if($pos !== false)
        {
            $temp = explode('/',$value);
            if(!is_null($temp[1]) && $temp[1] != 0)
                $gps[$key] = $temp[0] / $temp[1];
            else
                $gps[$key] = 0.0;
        }
    }

    //calculate the decimal degree
    $result['latitude'] = $LatM * ($gps['LatDegree'] + ($gps['LatMinute'] / 60) + ($gps['LatgSeconds'] / 3600));
    $result['longitude'] = $LongM * ($gps['LongDegree'] + ($gps['LongMinute'] / 60) + ($gps['LongSeconds'] / 3600));
    $result['time'] = $exif["DateTimeOriginal"];

    return $result;

}