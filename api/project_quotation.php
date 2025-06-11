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
          $username = $decoded->data->username;
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
            $page = (isset($_GET['page']) ?  $_GET['page'] : "");
            $size = (isset($_GET['size']) ?  $_GET['size'] : "");
            $keyword = (isset($_GET['keyword']) ?  $_GET['keyword'] : "");

            $sql = "
                    select *
                        from (
                        SELECT 'f' type, pm.id, pm.remark comment, COALESCE(f.filename, '') filename, COALESCE(f.bucketname, '') bucket, COALESCE(f.gcp_name, '') gcp_name, u.username, pm.created_at, pm.final_quotation, '' pageless 
                        FROM project_quotation pm 
                        left join user u on u.id = pm.create_id 
                        LEFT JOIN gcp_storage_file f ON f.batch_id = pm.id AND f.batch_type = 'quote' 
                        where project_id =  " . $pid . "  and pm.status <> -1 ";

            $sql = $sql . "
                        union 
                        select 'p' type, pm.id, pm.title comment, pm.title filename, '' bucket, '' gcp_name, u.username, pm.created_at, '' final_quotation, pageless
                        from quotation pm
                        left join user u on u.id = pm.create_id 
                        where pm.project_id = " . $pid . " and pm.status <> -1 ";
if(is_quotation_control($db, $username) == false)
{
    $sql = $sql . " and pm.can_view = '' ";
}
            $sql = $sql . "
                        ) a
                    ORDER BY a.created_at desc ";


            $merged_results = array();

            $stmt = $db->prepare( $sql );
            $stmt->execute();

            $id = 0;
            $comment = "";
            $filename = "";
            $gcp_name = "";
            $username = "";
            $created_at = "";
            $final_quotation = "";
            $type = "";
            $pageless = "";
            $items = [];

            while($row = $stmt->fetch(PDO::FETCH_ASSOC)) {

                if($id != $row['id'] && $id != 0)
                {
                    $merged_results[] = array( "id" => $id,
                        "type" => $type,
                        "comment" => $comment,
                        "items" => $items,
                        "username" => $username,
                        "created_at" => $created_at,
                        "final_quotation" => $final_quotation,
                        "pageless" => $pageless,
                        
                    );

                    $items = [];

                }

                $id = $row['id'];
                $created_at = $row['created_at'];
                $username = $row['username'];
                $gcp_name = $row['gcp_name'];
                $filename = $row['filename'];
                $bucket = $row['bucket'];
                $comment = $row['comment'];
                $final_quotation = $row['final_quotation'];
                $type = $row['type'];
                $pageless = $row['pageless'];

                if($filename != "")
                  $items[] = array('filename' => $filename,
                                 'gcp_name' => $gcp_name,
                                 'bucket' => $bucket );
            }

            if($id != 0)
            {
                $merged_results[] = array("id" => $id,"type" => $type,
                "comment" => $comment,
                                                "items" => $items,
                                                "username" => $username,
                                                "created_at" => $created_at,
                                                "final_quotation" => $final_quotation,
                                                "pageless" => $pageless,
                        );
            }


            while($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                $merged_results[] = $row;
            }

            echo json_encode($merged_results, JSON_UNESCAPED_SLASHES);

            break;

      }


function is_quotation_control($db, $user_name)
{
    $access = false;

    $query = "SELECT * FROM access_control WHERE quotation_control LIKE '%" . $user_name . "%' ";
    $stmt = $db->prepare( $query );
    $stmt->execute();
    while($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        $access = true;
    }
    return $access;
}

?>
