<?php
error_reporting(0);
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

include_once 'config/core.php';
include_once 'libs/php-jwt-master/src/BeforeValidException.php';
include_once 'libs/php-jwt-master/src/ExpiredException.php';
include_once 'libs/php-jwt-master/src/SignatureInvalidException.php';
include_once 'libs/php-jwt-master/src/JWT.php';

include_once 'config/database.php';
include_once 'objects/apply_for_leave.php';
include_once 'config/conf.php';

$database = new Database();
$db = $database->getConnection();

$afl = new ApplyForLeave($db);


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

        try {
            if (isset($_FILES['file']['name'])) {
                $conf = new Conf();
                $key = "myKey";
                $time = time();
                $hash = hash_hmac('sha256', $time, $key);
                $ext = pathinfo($_FILES['file']['name'], PATHINFO_EXTENSION);
                $filename = $time . $hash . "." . $ext;
                if (move_uploaded_file($_FILES["file"]["tmp_name"], $conf::$upload_path . $filename)) {
                        echo "done";
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
        $afl->type = $type;
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

function compress_image($source_url, $destination_url, $quality)
{
    $info = getimagesize($source_url);
    if ($info['mime'] == 'image/jpeg') $image = imagecreatefromjpeg($source_url);
    elseif ($info['mime'] == 'image/gif') $image = imagecreatefromgif($source_url);
    elseif ($info['mime'] == 'image/png') $image = imagecreatefrompng($source_url);
    imagejpeg($image, $destination_url, $quality);
    //echo "Image uploaded successfully.";
}