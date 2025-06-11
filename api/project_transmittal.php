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


      $database = new Database();
      $db = $database->getConnection();

      switch ($method) {
          case 'GET':
            $pid = (isset($_GET['pid']) ?  $_GET['pid'] : "");


            $sql = "SELECT pm.id, pm.title comment, pm.followup, u.username, pm.created_at 
            FROM transmittal pm left join user u on u.id = pm.create_id 
            where pm.project_id = " . $pid . " and pm.kind = '' and pm.status <> -1 ";


            $merged_results = array();

            $stmt = $db->prepare( $sql );
            $stmt->execute();

            $id = 0;
            $comment = "";
            $followup = "";
            $filename = "";
            $gcp_name = "";
            $username = "";
            $created_at = "";
            $items = [];

            while($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                $id = $row['id'];
                $created_at = $row['created_at'];
                $username = $row['username'];
                $comment = $row['comment'];
                $followup = $row['followup'];

                $items = GetRecentFiles($id, $db);

                $merged_results[] = array(
                    "id" => $id,
                    "comment" => $comment,
                    "followup" => $followup,
                    "username" => $username,
                    "created_at" => $created_at,
                    "items" => $items
                );
            }

            echo json_encode($merged_results, JSON_UNESCAPED_SLASHES);

            break;

      }



      function GetRecentFiles($pid, $db){
        // $sql = "SELECT f.id, COALESCE(f.filename, '') filename, COALESCE(f.bucketname, '') bucket, COALESCE(f.gcp_name, '') gcp_name, u.username, pm.created_at FROM transmittal pm LEFT JOIN gcp_storage_file f ON f.batch_id = pm.id left join user u on u.id = f.create_id  AND f.batch_type = 'transmittal' where pm.id = " . $pid . " and pm.status <> -1 and f.status <> -1";
        $sql = "SELECT f.id, COALESCE(f.filename, '') filename, COALESCE(f.bucketname, '') bucket, COALESCE(f.gcp_name, '') gcp_name, (select username from user where id = f.create_id) username, f.created_at FROM transmittal pm  LEFT JOIN gcp_storage_file f ON f.batch_id = pm.id AND f.batch_type = 'transmittal' where pm.id = " . $pid . " and pm.status <> -1 and f.status <> -1";
        $stmt = $db->prepare( $sql );
    
        $stmt->execute();
    
        $result = [];
    
        while($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $result[] = array(
                "id" => $row['id'],
                "filename" => $row['filename'],
                "bucket" => $row['bucket'],
                "gcp_name" => $row['gcp_name'],
                "username" => $row['username'],
                "created_at" => $row['created_at'],
            );
        }
    
        return $result;
    }
    
?>
