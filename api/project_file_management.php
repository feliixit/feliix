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


      $database = new Database();
      $db = $database->getConnection();

      switch ($method) {
          case 'GET':
            $pid = (isset($_GET['pid']) ?  $_GET['pid'] : "");
            $page = (isset($_GET['page']) ?  $_GET['page'] : "");
            $size = (isset($_GET['size']) ?  $_GET['size'] : "");
            $keyword = (isset($_GET['keyword']) ?  $_GET['keyword'] : "");

            $sql = "SELECT pm.id project_id, pac.id pac_id, pad.id pad_id, prf.id prf_id, ps.id stage_id, 
                    psc.id stage_client_id, 
                    pot.id stage_other_id, 
                    pot.id other_task_id, potr.id other_task_id_r, 
                    potm.id other_task_msg_id, potmr.id other_task_msg_id_r,
                    potmp.id other_task_msg_reply_id, potmpr.id other_task_msg_reply_id_r
                    FROM project_main pm
                    LEFT JOIN project_action_comment pac ON pm.id = pac.project_id
                    LEFT JOIN project_action_detail pad ON pm.id = pad.project_id
                    LEFT JOIN project_proof prf ON pm.id = prf.project_id
                    left join project_stages ps ON pm.id = ps.project_id
                    LEFT JOIN project_stage_client psc ON ps.id = psc.stage_id
                    LEFT JOIN project_other_task pot ON ps.id = pot.stage_id
                    LEFT JOIN project_other_task_r potr ON ps.id = potr.stage_id
                    LEFT JOIN project_other_task_message potm ON pot.id = potm.task_id
                    LEFT JOIN project_other_task_message_r potmr ON pot.id = potmr.task_id
                    LEFT JOIN project_other_task_message_reply potmp ON potm.id = potmp.message_id
                    LEFT JOIN project_other_task_message_reply_r potmpr ON potmr.id = potmpr.message_id where pm.id = " . $pid . " and pm.status <> -1 ";

            if(!empty($_GET['page'])) {
                $page = filter_input(INPUT_GET, 'page', FILTER_VALIDATE_INT);
                if(false === $page) {
                    $page = 1;
                }
            }

            $sql = $sql . " ORDER BY pm.id ";

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

            // project02?p=$pid
            $comment = [];              // pack_id
            $action_detail = [];        // pad_id
            $proof = [];                // prf_id

            // project03_client?sid=$stage_id
            $additional = [];           // prf_id

            // project03_other?sid=$stage_id
            $other_task = [];           // other_task_id
            $other_task_r = [];         // other_task_id_r
            $other_task_msg = [];           // other_task_msg_id
            $other_task_msg_r = [];         // other_task_msg_id_r
            $other_task_msg_rep = [];           // other_task_msg_rep_id
            $other_task_msg_rep_r = [];         // other_task_msg_rep_id_r

            $project_id = 0;
            $pac_id = 0;
            $pad_id = 0;
            $prf_id = 0;
            $stage_id = 0;
            $stage_client_id = 0;
            $stage_other_id = 0;
            $other_task_id = 0;
            $other_task_id_r = 0;
            $other_task_msg_id = 0;
            $other_task_msg_id_r = 0;
            $other_task_msg_reply_id = 0;
            $other_task_msg_reply_id_r = 0;


            while($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                $project_id = $row['project_id'];
                $pac_id = $row['pac_id'];
                $pad_id = $row['pad_id'];
                $prf_id = $row['prf_id'];
                $stage_id = $row['stage_id'];
                $stage_client_id = $row['stage_client_id'];
                $stage_other_id = $row['stage_other_id'];
                $other_task_id = $row['other_task_id'];
                $other_task_id_r = $row['other_task_id_r'];
                $other_task_msg_id = $row['other_task_msg_id'];
                $other_task_msg_id_r = $row['other_task_msg_id_r'];
                $other_task_msg_reply_id = $row['other_task_msg_reply_id'];
                $other_task_msg_reply_id_r = $row['other_task_msg_reply_id_r'];

                if($pac_id != null)
                {
                    if (!in_array(GetComment($pac_id, $db), $comment))
                    {
                        $array[] = $value; 
                    }
                }

            }

            echo json_encode($merged_results, JSON_UNESCAPED_SLASHES);

            break;


      }


      function GetComment($pac_id, $db)
      {
        $sql = "select pmsgrp.id replay_id, pmsgrp.comment reply, pmsgrp.`status` reply_status, r.username replyer, r.pic_url replyer_pic, pmsgrp.created_at reply_date, COALESCE(p.username, '') updator, COALESCE(pmsgrp.updated_at, '') update_date,
            COALESCE(h.filename, '') filename, COALESCE(h.gcp_name, '') gcp_name
            from project_action_comment pmsgrp 
            LEFT JOIN user r ON r.id = pmsgrp.create_id 
            LEFT JOIN user p ON p.id = pmsgrp.updated_id 
            LEFT JOIN gcp_storage_file h ON h.batch_id = pmsgrp.id AND h.batch_type = 'comment' 
            where pmsgrp.id = " . $pac_id . " order by pmsgrp.created_at ";

            $merged_results = array();

            $stmt = $db->prepare($sql);
            $stmt->execute();

            $replay_id = 0;
            $reply = "";
            $reply_status = "";
            $replyer = "";
            $replyer_pic = "";
            $reply_date = "";
            $gcp_name = "";
            $filename = "";
            $items = [];
            $updator = "";
            $update_date = "";

            while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            

                $replay_id = $row['replay_id'];
                $reply = $row['reply'];
                $reply_status = $row['reply_status'];
                $replyer = $row['replyer'];
                $replyer_pic = $row['replyer_pic'];
                $reply_date = $row['reply_date'];

                $updator = $row['updator'];
                $update_date = $row['update_date'];
            
                $gcp_name = $row['gcp_name'];
                $filename = $row['filename'];
            
                $merged_results[] = array(
                    "message_id" => $replay_id,

                    "message" => $reply,
                    "message_status" => $reply_status,
                    "messager" => $replyer,
                    "messager_pic" => $replyer_pic,
                    
                    "message_date" => explode(" ", $reply_date)[0],
                    "message_time" => explode(" ", $reply_date)[1],

                    "updator" => $updator,
                    "update_date" => $update_date,
                
                    "gcp_name" => $gcp_name,
                    "filename" => $filename,
                );
            }

            return $merged_results;
      }

?>
