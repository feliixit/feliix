<?php

function triphoto_getGPS($fileName, $assoc = false)
{
    //get the EXIF
    $exif = exif_read_data($fileName);

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
        $gps[$key] = $temp[0] / $temp[1];
    }
    }

    //calculate the decimal degree
    $result['latitude'] = $LatM * ($gps['LatDegree'] + ($gps['LatMinute'] / 60) + ($gps['LatgSeconds'] / 3600));
    $result['longitude'] = $LongM * ($gps['LongDegree'] + ($gps['LongMinute'] / 60) + ($gps['LongSeconds'] / 3600));

    if($assoc)
    {
    return $result;
    }

    return json_encode($result);
}

if(isset($_FILES['file']['name'])) 
{
    $key = "myKey";
    $time = time();
    $hash = hash_hmac('sha256', $time, $key);
    $ext = pathinfo($_FILES['file']['name'], PATHINFO_EXTENSION);
    $filename = $time . $hash . "." . $ext;
    // if (move_uploaded_file($_FILES["file"]["tmp_name"], "/var/www/html/img/" . $filename)) {
    if (move_uploaded_file($_FILES["file"]["tmp_name"], "C:/xampp/htdocs/img/" . $filename)) {
        echo triphoto_getGPS("C:/xampp/htdocs/img/" . $filename);
    }
}