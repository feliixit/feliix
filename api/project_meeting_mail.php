<?php

error_reporting(E_ALL);
ini_set('display_errors', 1);
$jwt = (isset($_COOKIE['jwt']) ?  $_COOKIE['jwt'] : null);
include_once 'config/core.php';
include_once 'libs/php-jwt-master/src/BeforeValidException.php';
include_once 'libs/php-jwt-master/src/ExpiredException.php';
include_once 'libs/php-jwt-master/src/SignatureInvalidException.php';
include_once 'libs/php-jwt-master/src/JWT.php';
use \Firebase\JWT\JWT;

$method = $_SERVER['REQUEST_METHOD'];


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
          //if(!$decoded->data->is_admin)
          //{
          //  http_response_code(401);
     
          //  echo json_encode(array("message" => "Access denied."));
          //  die();
          //}
      }
      // if decode fails, it means jwt is invalid
      catch (Exception $e){
      
          http_response_code(401);
     
        echo json_encode(array("message" => "Access denied."));
        die();
      }
}

      header('Access-Control-Allow-Origin: *');  

      include_once 'config/database.php';
      include_once 'mail.php';


      $database = new Database();
      $db = $database->getConnection();

      switch ($method) {
          case 'POST':
            $bid = (isset($_POST['bid']) ?  $_POST['bid'] : 0);
            $type = (isset($_POST['type']) ?  $_POST['type'] : "1");

            $query = "SELECT * from work_calendar_meetings where id = ".$bid;

            $stmt = $db->prepare( $query );
            $stmt->execute();

            $items = [];

            $id = 0;
            $subject = "";
            $message = "";
            $attendee = "";
            $location = "";
            $start_time = "";
            $end_time = "";
            $created_by = "";
           

            while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
               
    
                $id = $row['id'];
                $subject = $row['subject'];
                $message = $row['message'];
                $attendee = $row['attendee'];
                $location = $row['location'];
                $start_time = $row['start_time'];
                $start_time = substr($start_time, 0, -3);
                $end_time = $row['end_time'];
                $end_time = substr($end_time, 11, -3);
                $created_by = $row['created_by'];
             
                if(!empty($attendee ))
                    $items = GetUserInfo($row['attendee'], $created_by, $db);
            }

            foreach ($items as $item)
            {
                switch ($type){
                case "1":
                    send_meeting_notify_mail($item['username'], $item['email'], $subject, $created_by, $attendee, $start_time, $end_time, $message, $location);
                break;
                case "2":
                    send_meeting_modified_mail($item['username'], $item['email'], $subject, $created_by, $attendee, $start_time, $end_time, $message, $location);
                break;
                case "3":
                    send_meeting_delete_mail($item['username'], $item['email'], $subject, $created_by, $attendee, $start_time, $end_time, $message, $location);
                break;
                }
            }

            break;
          }
        

function GetUserInfo($users, $created_by, $db)
{
    $psq = "";
    $a_users = explode(",", $users);

    array_push($a_users, $created_by);

    foreach ($a_users as $value) {
        $psq .= "'" . $value . "',";
    }

    $psq = rtrim($psq, ",");

    $sql = "SELECT id, username, email FROM user WHERE username in (" . $psq . ")";

    $merged_results = array();

    $stmt = $db->prepare($sql);
    $stmt->execute();

    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        $merged_results[] = $row;
    }

    return $merged_results;
}


?>