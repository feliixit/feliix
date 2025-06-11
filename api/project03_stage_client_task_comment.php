<?php

error_reporting(E_ALL);
ini_set('display_errors', 1);
$jwt = (isset($_COOKIE['jwt']) ?  $_COOKIE['jwt'] : null);
include_once 'config/core.php';
include_once 'libs/php-jwt-master/src/BeforeValidException.php';
include_once 'libs/php-jwt-master/src/ExpiredException.php';
include_once 'libs/php-jwt-master/src/SignatureInvalidException.php';
include_once 'libs/php-jwt-master/src/JWT.php';

use Google\Cloud\Storage\StorageClient;
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

      $conf = new Conf();

      switch ($method) {
          case 'GET':
            $stage_id = (isset($_GET['stage_id']) ?  $_GET['stage_id'] : 0);
            $page = (isset($_GET['page']) ?  $_GET['page'] : "");
            $size = (isset($_GET['size']) ?  $_GET['size'] : "");

            $sql = "SELECT pm.id, f.id f_id, pm.type, pm.message, u.username, pm.created_at, f.message f_message, s.username f_username, f.created_at f_created_at FROM project_stage_client_task pm LEFT JOIN project_stage_client_task_comment f ON f.task_id = pm.id left join user u on u.id = pm.create_id left join user s on s.id = f.create_id  where pm.stage_id = " . $stage_id . " and pm.status <> -1 ";

            if(!empty($_GET['page'])) {
                $page = filter_input(INPUT_GET, 'page', FILTER_VALIDATE_INT);
                if(false === $page) {
                    $page = 1;
                }
            }

            $sql = $sql . " ORDER BY pm.id, f.id ";

            if(!empty($_GET['size'])) {
                $size = filter_input(INPUT_GET, 'size', FILTER_VALIDATE_INT);
                if(false === $size) {
                    $size = 10;
                }

                $offset = ($page - 1) * $size;

                $sql = $sql . " LIMIT " . $offset . "," . $size;
            }

            $merged_results = array();

            $stmt = $db->prepare( $sql );
            $stmt->execute();

            $num = 0;
            $id = 0;
            $message = "";
            $username = "";
            $created_at = "";
            $f_id = 0;
            $f_message = "";
            $f_username = "";
            $f_created_at = "";
            $attachments = [];
            $items = [];

            while($row = $stmt->fetch(PDO::FETCH_ASSOC)) {

                if($id != $row['id'] && $id != 0)
                {
                    $num = $num + 1;

                    $merged_results[] = array( 
                                            "num" => $num,
                                            "id" => $id,
                                            "message" => $message,
                                            "username" => $username,
                                            "created_at" => $created_at,
                                            "attachments" => $attachments,
                                            "items" => $items,
                    );

                    $items = [];

                }


                $id = $row['id'];
                $f_id = $row['f_id'];
                $created_at = $row['created_at'];
                $username = $row['username'];
                $message = $row['message'];
                $attachments = GetAttachment($id, 'client_attached', $db);
                $f_created_at = $row['f_created_at'];
                $f_username = $row['f_username'];
                $f_message = $row['f_message'];

                if($f_message != "")
                  $items[] = array('f_id' => $f_id,
                                 'f_attachments' => GetAttachment($f_id, 'client_reply_attached', $db),
                                 'f_message' => $f_message,
                                 'f_created_at' => $f_created_at,
                                 'f_username' => $f_username, );
            }

            if($id != 0)
            {
                $num = $num + 1;

                $merged_results[] = array(      
                                            "num" => $num,
                                            "id" => $id,
                                            "message" => $message,
                                            "username" => $username,
                                            "created_at" => $created_at,
                                            "attachments" => $attachments,
                                            "items" => $items,
                        );
            }


            while($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                $merged_results[] = $row;
            }

            echo json_encode($merged_results, JSON_UNESCAPED_SLASHES);

            break;

          case 'POST':
              // get database connection
            $uid = $user_id;
            $task_id = (isset($_POST['task_id']) ?  $_POST['task_id'] : 0);
            $stage_id = (isset($_POST['stage_id']) ?  $_POST['stage_id'] : 0);
            $project_id = (isset($_POST['project_id']) ?  $_POST['project_id'] : 0);
            $type = (isset($_POST['type']) ?  $_POST['type'] : '');
            $message = (isset($_POST['message']) ?  $_POST['message'] : '');

            $attached_file = (isset($_POST['attached_file']) ?  $_POST['attached_file'] : '[]');
            $attached_file_array = json_decode($attached_file, true);
         
            $query = "SELECT stage
                        FROM   project_stages
                                LEFT JOIN project_stage
                                        ON project_stage.id = project_stages.stage_id
                        WHERE  project_stages.project_id = " . $project_id . "
                        AND project_stages.stages_status_id = 1
                        ORDER  BY `sequence` DESC limit 1
                    ";

            $stmt = $db->prepare( $query );
            $stmt->execute();

            $stage = "";
          

            while($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                $stage = $row['stage'];
             }
        
             if($stage == 'Client')
             {
                 $query = "update project_main set last_client_stage_id = " . $stage_id . 
                                                " , last_client_created_id = '" . $user_id . "' ". 
                                                " , last_client_message = '" . substr($message, 0, 512) . "' ". 
                                                " , last_client_created_at = now() where id = " . $project_id;

                $stmt = $db->prepare($query);

                try {
                    // execute the query, also check if query was successful
                    if ($stmt->execute()) {
             
                    }
                    else
                    {
                        $arr = $stmt->errorInfo();
                        error_log($arr[2]);
                    }
                }
                catch (Exception $e)
                {
                    error_log($e->getMessage());
                }
             }
             
            $query = "INSERT INTO project_stage_client_task_comment
                SET
                    `task_id` = :task_id,
                    `type` = :type,
                    `message` = :message,
                 
                  
                    `create_id` = :create_id,
                    `created_at` = now()";
    
                // prepare the query
                $stmt = $db->prepare($query);
            
                // bind the values
                $stmt->bindParam(':task_id', $task_id);
                $stmt->bindParam(':type', $type);
                $stmt->bindParam(':message', $message);
               
      
                $stmt->bindParam(':create_id', $user_id);

                $last_id = 0;
                // execute the query, also check if query was successful
                try {
                    // execute the query, also check if query was successful
                    if ($stmt->execute()) {
                        $last_id = $db->lastInsertId();

                        $batch_type = "client_reply_attached";

                        for($j=0; $j < count($attached_file_array); $j++)
                        {
                            $key = "attached_file" . $j;
                            if (array_key_exists($key, $_FILES))
                            {
                                $update_name = SaveImage($key, $last_id, $batch_type, $user_id, $db, $conf);
                            }
                            
                        }
                        

                    }
                    else
                    {
                        $arr = $stmt->errorInfo();
                        error_log($arr[2]);
                    }
                }
                catch (Exception $e)
                {
                    error_log($e->getMessage());
                }


                 $returnArray = array('batch_id' => $last_id);
                $jsonEncodedReturnArray = json_encode($returnArray, JSON_PRETTY_PRINT);

                if($last_id != 0)
                    SendNotifyMail($last_id);

                echo $jsonEncodedReturnArray;
                
                break;

      }

function SendNotifyMail($bid)
{
    $database = new Database();
    $db = $database->getConnection();

    $stage_id = 0;

    $sql = "SELECT ps.id,
                p.create_id,
                p.project_name,
                pm.message,
                pc.category,
                user.username, 
                DATE_FORMAT(pm.created_at, '%Y-%m-%d %T') created_at
            from project_stage_client_task_comment pm
            LEFT JOIN project_stage_client_task pst ON pst.id = pm.task_id
            LEFT JOIN project_stages ps ON pst.stage_id = ps.id
            LEFT JOIN project_main p ON ps.project_id = p.id
            LEFT JOIN project_category pc ON p.catagory_id = pc.id
            LEFT JOIN user ON pm.create_id = user.id 
            WHERE pm.id  = " . $bid . "  ";

    $stmt = $db->prepare( $sql );
    $stmt->execute();

    $project_name = "";
    $username = "";
    $created_at = "";
    $project_creator_id = "";
    $message = "";
    $category = "";


    while($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        $project_name = $row['project_name'];
        $username = $row['username'];
        $created_at = $row['created_at'];
        $project_creator_id = $row['create_id'];
        $message = $row['message'];
        $category = $row['category'];
        $stage_id = $row['id'];
    }

    project03_stage_client_task_notify_mail($project_name, $username, $created_at, $project_creator_id, $message, $category, $stage_id);

}


function GetAttachment($id, $type, $db)
{

    $sql = "select h.id, 
                COALESCE(h.filename, '') filename, 
                COALESCE(h.gcp_name, '') gcp_name
            from gcp_storage_file h where h.batch_type = '" . $type . "'
            and h.batch_id = " . $id . " and h.`status` <> -1 order by h.id";

    $items = array();

    $stmt = $db->prepare($sql);
    $stmt->execute();

    $is_checked = "";
    $gcp_name = "";
    $filename = "";
   

    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        $id = $row['id'];
        $gcp_name = $row["gcp_name"];
        $filename = $row["filename"];

        if ($filename != "")
            $items[] = array(
                'id' => $id,
                'checked' => true,
                'file' => null,
                'gcp_name' => $gcp_name,
                'name' => $filename,
            );
    }

    return $items;

}


function SaveImage($type, $batch_id, $batch_type, $user_id, $db, $conf)
{
    try {
        if($_FILES[$type]['name'] == null)
            return "";
        // Loop through each file

        if(isset($_FILES[$type]['name']))
        {
            $image_name = $_FILES[$type]['name'];
            $valid_extensions = array("jpg","jpeg","png","gif","pdf","docx","doc","xls","xlsx","ppt","pptx","zip","rar","7z","txt","dwg","skp","psd","evo","dwf","bmp");
            $extension = pathinfo($image_name, PATHINFO_EXTENSION);
            if (in_array(strtolower($extension), $valid_extensions)) 
            {
                //$upload_path = 'img/' . time() . '.' . $extension;

                $storage = new StorageClient([
                    'projectId' => 'predictive-fx-284008',
                    'keyFilePath' => $conf::$gcp_key
                ]);

                $bucket = $storage->bucket('feliiximg');

                $upload_name = time() . '_' . pathinfo($image_name, PATHINFO_FILENAME) . '.' . $extension;

                $file_size = filesize($_FILES[$type]['tmp_name']);
                $size = 0;

                $obj = $bucket->upload(
                    fopen($_FILES[$type]['tmp_name'], 'r'),
                    ['name' => $upload_name]);

                $info = $obj->info();
                $size = $info['size'];

                if($size == $file_size && $file_size != 0 && $size != 0)
                {
                    $query = "INSERT INTO gcp_storage_file
                    SET
                        batch_id = :batch_id,
                        batch_type = :batch_type,
                        filename = :filename,
                        gcp_name = :gcp_name,

                        create_id = :create_id,
                        created_at = now()";

                    // prepare the query
                    $stmt = $db->prepare($query);
                
                    // bind the values
                    $stmt->bindParam(':batch_id', $batch_id);
                    $stmt->bindParam(':batch_type', $batch_type);
                    $stmt->bindParam(':filename', $image_name);
                    $stmt->bindParam(':gcp_name', $upload_name);
        
                    $stmt->bindParam(':create_id', $user_id);

                    try {
                        // execute the query, also check if query was successful
                        if ($stmt->execute()) {
                            $last_id = $db->lastInsertId();
                        }
                        else
                        {
                            $arr = $stmt->errorInfo();
                            error_log($arr[2]);
                        }
                    }
                    catch (Exception $e)
                    {
                        error_log($e->getMessage());
                        $db->rollback();
                        http_response_code(501);
                        echo json_encode(array("Failure at " . date("Y-m-d") . " " . date("h:i:sa") . " " . $e->getMessage()));
                        die();
                    }

                    return $upload_name;
                }
                else
                {
                    $message = 'There is an error while uploading file';
                    $db->rollback();
                    http_response_code(501);
                    echo json_encode(array("Failure at " . date("Y-m-d") . " " . date("h:i:sa") . " " . $message));
                    die();
                    
                }
            }
            else
            {
                $message = 'Only Images or Office files allowed to upload';
                $db->rollback();
                http_response_code(501);
                echo json_encode(array("Failure at " . date("Y-m-d") . " " . date("h:i:sa") . " " . $message));
                die();
            }
        }

        
    } catch (Exception $e) {
        $db->rollback();
        http_response_code(501);
        echo json_encode(array("Failure at " . date("Y-m-d") . " " . date("h:i:sa") . " Error uploading, Please use laptop to upload again."));
        die();
    }
}


?>
