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

            // comment
            $sql = "SELECT bucketname, filename, gcp_name, username, gcp_storage_file.created_at
                                FROM project_main pm
                    JOIN project_action_comment pac ON pm.id = pac.project_id
                                join gcp_storage_file on batch_id = pac.id and batch_type = 'comment'
                    join user on user.id = gcp_storage_file.create_id
                    where pm.id =" . $pid . " and pm.status <> -1";

            $stmt = $db->prepare($sql);
            $stmt->execute();

            while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                $reply = $row['username'];
                $reply_date = $row['created_at'];

                $updator = $row['updator'];
                $update_date = $row['update_date'];

                $gcp_name = $row['gcp_name'];
                $filename = $row['filename'];
                $bucket = $row['bucketname'];

                $merged_results[] = array(
                    "messager" => $reply,
                    "message_date" => explode(" ", $reply_date)[0],
                    "message_time" => explode(" ", $reply_date)[1],
                    "gcp_name" => $gcp_name,
                    "filename" => $filename,
                    "url" => 'project02?p=' . $pid,
                    "stage" => 'Main Page',
                    "bucket" => $bucket,
                );
            }

            // additional detail
            $sql = "SELECT bucketname, filename, gcp_name, username, gcp_storage_file.created_at
                                FROM project_main pm
                    JOIN project_action_detail pac ON pm.id = pac.project_id
                                join gcp_storage_file on batch_id = pac.id and batch_type = 'action_detail'
                    join user on user.id = gcp_storage_file.create_id
                    where pm.id =" . $pid . " and pm.status <> -1";

            $stmt = $db->prepare($sql);
            $stmt->execute();

            while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                $reply = $row['username'];
                $reply_date = $row['created_at'];

                $updator = $row['updator'];
                $update_date = $row['update_date'];

                $gcp_name = $row['gcp_name'];
                $filename = $row['filename'];
                $bucket = $row['bucketname'];

                $merged_results[] = array(
                    "messager" => $reply,
                    "message_date" => explode(" ", $reply_date)[0],
                    "message_time" => explode(" ", $reply_date)[1],
                    "gcp_name" => $gcp_name,
                    "filename" => $filename,
                    "url" => 'project02?p=' . $pid,
                    "stage" => 'Main Page',
                    "bucket" => $bucket,
                );
            }

            // downpayment
            $sql = "SELECT bucketname, filename, gcp_name, username, gcp_storage_file.created_at
                                FROM project_main pm
                    JOIN project_proof pac ON pm.id = pac.project_id
                                join gcp_storage_file on batch_id = pac.id and batch_type = 'proof'
                    join user on user.id = gcp_storage_file.create_id
                    where pm.id =" . $pid . " and pm.status <> -1";

            $stmt = $db->prepare($sql);
            $stmt->execute();

            while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                $reply = $row['username'];
                $reply_date = $row['created_at'];

                $updator = $row['updator'];
                $update_date = $row['update_date'];

                $gcp_name = $row['gcp_name'];
                $filename = $row['filename'];
                $bucket = $row['bucketname'];

                $merged_results[] = array(
                    "messager" => $reply,
                    "message_date" => explode(" ", $reply_date)[0],
                    "message_time" => explode(" ", $reply_date)[1],
                    "gcp_name" => $gcp_name,
                    "filename" => $filename,
                    "url" => 'project02?p=' . $pid,
                    "stage" => 'Downpayment',
                    "bucket" => $bucket,
                );
            }


            // stages
            $sql = "SELECT bucketname, filename, gcp_name, username, gcp_storage_file.created_at
                                FROM project_main pm
                    JOIN project_proof pac ON pm.id = pac.project_id
                                join gcp_storage_file on batch_id = pac.id and batch_type = 'proof'
                    join user on user.id = gcp_storage_file.create_id
                    where pm.id =" . $pid . " and pm.status <> -1";

            $stmt = $db->prepare($sql);
            $stmt->execute();

            while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                $reply = $row['username'];
                $reply_date = $row['created_at'];

                $updator = $row['updator'];
                $update_date = $row['update_date'];

                $gcp_name = $row['gcp_name'];
                $filename = $row['filename'];
                $bucket = $row['bucketname'];

                $merged_results[] = array(
                    "messager" => $replyer,
                    "message_date" => explode(" ", $reply_date)[0],
                    "message_time" => explode(" ", $reply_date)[1],
                    "gcp_name" => $gcp_name,
                    "filename" => $filename,
                    "url" => 'project02?p=' . $pid,
                    "stage" => 'Downpayment',
                    "bucket" => $bucket,
                );
            }

            // loop for stages_status_id
            $sql = "SELECT ps.id, pst.stage
                            FROM project_main pm
                            JOIN project_stages ps ON pm.id = ps.project_id
                            JOIN project_stage pst ON ps.stage_id = pst.id
                    where pm.id = " . $pid . " and pm.status <> -1 ";

            while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                $pac_id = $row['id'];
                $stage = $row['stage'];

                $merged_results[] = GetAdditional($pac_id, $db, $pid, $stage);
            }


            $sql = "SELECT pm.id project_id, pst.stage, pac.id pac_id, pad.id pad_id, prf.id prf_id, ps.id stage_id, 
                    psc.id stage_client_id, 
                    pot.id stage_other_id, 
                    pot.id other_task_id, potr.id other_task_id_r, 
                    potm.id other_task_msg_id, potmr.id other_task_msg_id_r,
                    potmp.id other_task_msg_reply_id, potmpr.id other_task_msg_reply_id_r
                    FROM project_main pm
                    LEFT JOIN project_action_comment pac ON pm.id = pac.project_id
                    LEFT JOIN project_action_detail pad ON pm.id = pad.project_id
                    LEFT JOIN project_proof prf ON pm.id = prf.project_id
                    left JOIN project_stages ps ON pm.id = ps.project_id
                    LEFT JOIN project_stage pst ON ps.stage_id = pst.id
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
            $stage = '';

            $merged_results = [];

            while($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                $project_id = $row['project_id'];
                $pac_id = $row['pac_id'];
                $pad_id = $row['pad_id'];
                $prf_id = $row['prf_id'];
                $stage_id = $row['stage_id'];
                $stage = $row['stage'];
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
                    if (!in_array($pac_id, $comment))
                    {
                        array_push($comment, $pac_id);
                        $arr = GetComment($pac_id, $db, $pid, "Main Page");
                        if(count($arr) > 0)
                            array_merge($merged_results, $arr);
                    }
                }

                if($pad_id != null)
                {
                    if (!in_array($pad_id, $action_detail))
                    {
                        array_push($action_detail, $pad_id);
                        $arr = GetActionDetail($pad_id, $db, $pid, "Main Page");
                        if(count($arr) > 0)
                            $merged_results = array_merge($merged_results, $arr);
                    }
                }
                // Dennis Lin 2、Thalassa Wren Benzon 4、 Kristel Tan 6、 Glendon Wendell Co 41、 Kuan 3
                if($user_id == 2 || $user_id == 3 || $user_id == 4 || $user_id == 6 || $user_id == 41)
                {
                    if($prf_id != null)
                    {
                        if (!in_array($prf_id, $proof))
                        {
                            array_push($proof, $prf_id);
                            $arr = GetProof($prf_id, $db, $pid, "Downpayment");
                            if(count($arr) > 0)
                                $merged_results = array_merge($merged_results, $arr);
                        }
                    }
                }

                if($stage_client_id != null)
                {
                    if (!in_array($stage_client_id, $additional))
                    {
                        array_push($additional, $stage_client_id);
                        $arr = GetAdditional($stage_client_id, $db, $stage_id, $stage);
                        if(count($arr) > 0)
                            $merged_results = array_merge($merged_results, $arr);
                    }
                }

                if($other_task_id != null)
                {
                    if (!in_array($other_task_id, $other_task))
                    {
                        array_push($other_task, $other_task_id);
                        $arr = GetOtherTask($other_task_id, $db, $stage_id, $stage);
                        if(count($arr) > 0)
                            $merged_results = array_merge($merged_results, $arr);
                    }
                }

                if($other_task_id_r != null)
                {
                    if (!in_array($other_task_id_r, $other_task_r))
                    {
                        array_push($other_task_r, $other_task_id_r);
                        $arr = GetOtherTaskR($other_task_id_r, $db, $stage_id, $stage);
                        if(count($arr) > 0)
                            $merged_results = array_merge($merged_results, $arr);
                    }
                }

                if($other_task_msg_id != null)
                {
                    if (!in_array($other_task_msg_id, $other_task_msg))
                    {
                        array_push($other_task_msg, $other_task_msg_id);
                        $arr = GetOtherTaskMsg($other_task_msg_id, $db, $stage_id, $stage);
                        if(count($arr) > 0)
                            $merged_results = array_merge($merged_results, $arr);
                    }
                }

                if($other_task_msg_id_r != null)
                {
                    if (!in_array($other_task_msg_id_r, $other_task_msg_r))
                    {
                        array_push($other_task_msg_r, $other_task_msg_id_r);
                        $arr = GetOtherTaskMsgR($other_task_msg_id_r, $db, $stage_id, $stage);
                        if(count($arr) > 0)
                            $merged_results = array_merge($merged_results, $arr);
                    }
                }

                if($other_task_msg_reply_id != null)
                {
                    if (!in_array($other_task_msg_reply_id, $other_task_msg_rep))
                    {
                        array_push($other_task_msg_rep, $other_task_msg_reply_id);
                        $arr = GetOtherTaskMsgRep($other_task_msg_reply_id, $db, $stage_id, $stage);
                        if(count($arr) > 0)
                            $merged_results = array_merge($merged_results, $arr);
                    }
                }

                if($other_task_msg_reply_id_r != null)
                {
                    if (!in_array($other_task_msg_reply_id_r, $other_task_msg_rep_r))
                    {
                        array_push($other_task_msg_rep_r, $other_task_msg_reply_id_r);
                        $arr = GetOtherTaskMsgRepR($other_task_msg_reply_id_r, $db, $stage_id, $stage);
                        if(count($arr) > 0)
                            $merged_results = array_merge($merged_results, $arr);
                    }
                }

            }

            $return_result = [];

            if($keyword == '')
            {
                echo json_encode($merged_results, JSON_UNESCAPED_SLASHES);
            }
            else
            {
                foreach ($merged_results as &$value) {
                    if(preg_match("/{$keyword}/i", $value['filename']) || preg_match("/{$keyword}/i", $value['messager']) || preg_match("/{$keyword}/i", $value['stage']) || preg_match("/{$keyword}/i", $value['message_date']) || preg_match("/{$keyword}/i", $value['message_time']))
                    {
                        $return_result[] = $value;
                    }
                }

                echo json_encode($return_result, JSON_UNESCAPED_SLASHES);
            }
            break;


      }


      function GetComment($pac_id, $db, $pid, $stage)
      {
        $sql = "select pmsgrp.id replay_id, pmsgrp.comment reply, pmsgrp.`status` reply_status, r.username replyer, r.pic_url replyer_pic, pmsgrp.created_at reply_date, COALESCE(p.username, '') updator, COALESCE(pmsgrp.updated_at, '') update_date,
            COALESCE(h.filename, '') filename, COALESCE(h.gcp_name, '') gcp_name, bucketname 
            from project_action_comment pmsgrp 
            JOIN user r ON r.id = pmsgrp.create_id 
            left JOIN user p ON p.id = pmsgrp.updated_id 
            JOIN gcp_storage_file h ON h.batch_id = pmsgrp.id AND h.batch_type = 'comment' 
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
            $updator = "";
            $update_date = "";
            $bucket = "";

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
                $bucket = $row['bucketname'];
            
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

                    "url" => 'project02?p=' . $pid,

                    "stage" => $stage,
                    "bucket" => $bucket,
                );
            }

            return $merged_results;
      }

      function GetActionDetail($pac_id, $db, $pid, $stage)
      {
        $sql = "select pmsgrp.id replay_id, pmsgrp.detail_desc reply, pmsgrp.`status` reply_status, r.username replyer, r.pic_url replyer_pic, pmsgrp.created_at reply_date, COALESCE(p.username, '') updator, COALESCE(pmsgrp.updated_at, '') update_date,
            COALESCE(h.filename, '') filename, COALESCE(h.gcp_name, '') gcp_name, bucketname
            from project_action_detail pmsgrp 
            JOIN user r ON r.id = pmsgrp.create_id 
            left JOIN user p ON p.id = pmsgrp.updated_id 
            JOIN gcp_storage_file h ON h.batch_id = pmsgrp.id AND h.batch_type = 'action_detail'
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

                $bucket = $row['bucketname'];
            
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

                    "url" => 'project02?p=' . $pid,

                    "stage" => $stage,
                    "bucket" => $bucket,
                );
            }

            return $merged_results;
      }

      function GetProof($pac_id, $db, $pid, $stage)
      {
        $sql = "select pmsgrp.id replay_id, '' reply, pmsgrp.`status` reply_status, r.username replyer, r.pic_url replyer_pic, pmsgrp.created_at reply_date, COALESCE(p.username, '') updator, COALESCE(pmsgrp.updated_at, '') update_date,
            COALESCE(h.filename, '') filename, COALESCE(h.gcp_name, '') gcp_name, bucketname
            from project_proof pmsgrp 
            JOIN user r ON r.id = pmsgrp.create_id 
            left JOIN user p ON p.id = pmsgrp.updated_id 
            JOIN gcp_storage_file h ON h.batch_id = pmsgrp.id AND h.batch_type = 'proof' 
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

                $bucket = $row['bucketname'];
            
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

                    "url" => "project02?p=" . $pid,

                    "stage" => $stage,
                    "bucket" => $bucket,
                );
            }

            return $merged_results;
      }

      function GetAdditional($pac_id, $db, $pid, $stage)
      {
        $sql = "select  bucketname, filename, gcp_name, username, gcp_storage_file.created_at
            from project_stage_client pm 
            join gcp_storage_file on batch_id = pac.id and batch_type = 'additional'
            join user on user.id = gcp_storage_file.create_id
            where pm.id = " . $pac_id;

            $merged_results = array();

            $stmt = $db->prepare($sql);
            $stmt->execute();

            while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                $reply = $row['username'];
                $reply_date = $row['created_at'];

                $updator = $row['updator'];
                $update_date = $row['update_date'];

                $gcp_name = $row['gcp_name'];
                $filename = $row['filename'];
                $bucket = $row['bucketname'];
            
                $merged_results[] = array(
                    "messager" => $reply,
                    "message_date" => explode(" ", $reply_date)[0],
                    "message_time" => explode(" ", $reply_date)[1],
                    "gcp_name" => $gcp_name,
                    "filename" => $filename,
                    "url" => 'project02?p=' . $pid,
                    "stage" => $stage,
                    "bucket" => $bucket,
                );
            }

            return $merged_results;
        }


    function GetOtherTask($pac_id, $db, $pid, $stage)
      {
        $sql = "select pmsgrp.id replay_id, '' reply, pmsgrp.`status` reply_status, r.username replyer, r.pic_url replyer_pic, pmsgrp.created_at reply_date, COALESCE(p.username, '') updator, COALESCE(pmsgrp.updated_at, '') update_date,
            COALESCE(h.filename, '') filename, COALESCE(h.gcp_name, '') gcp_name, bucketname
            from project_other_task pmsgrp 
            JOIN user r ON r.id = pmsgrp.create_id 
            left JOIN user p ON p.id = pmsgrp.updated_id 
            JOIN gcp_storage_file h ON h.batch_id = pmsgrp.id AND h.batch_type = 'other_task'
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
            $bucket = "";
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

                $bucket = $row['bucketname'];
            
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
                    "bucket" => $bucket,

                    "page_name" => "Main Page",

                    "url" => 'project03_other?sid=' . $pid,

                    "stage" => $stage,
                    "bucket" => $bucket,
                );
            }

            return $merged_results;
      }


      function GetOtherTaskR($pac_id, $db, $pid, $stage)
      {
        $sql = "select pmsgrp.id replay_id, '' reply, pmsgrp.`status` reply_status, r.username replyer, r.pic_url replyer_pic, pmsgrp.created_at reply_date, COALESCE(p.username, '') updator, COALESCE(pmsgrp.updated_at, '') update_date,
            COALESCE(h.filename, '') filename, COALESCE(h.gcp_name, '') gcp_name, bucketname
            from project_other_task_r pmsgrp 
            JOIN user r ON r.id = pmsgrp.create_id 
            left JOIN user p ON p.id = pmsgrp.updated_id 
            JOIN gcp_storage_file h ON h.batch_id = pmsgrp.id AND h.batch_type = 'other_task_r'
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
            $bucket = "";
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

                $bucket = $row['bucketname'];
            
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
                    "bucket" => $bucket,

                    "page_name" => "Main Page",

                    "url" => 'project03_other?sid=' . $pid,

                    "stage" => $stage,
                    "bucket" => $bucket,
                );
            }

            return $merged_results;
      }

      function GetOtherTaskMsg($pac_id, $db, $pid, $stage)
      {
        $sql = "select pmsgrp.id replay_id, '' reply, pmsgrp.`status` reply_status, r.username replyer, r.pic_url replyer_pic, pmsgrp.created_at reply_date, COALESCE(p.username, '') updator, COALESCE(pmsgrp.updated_at, '') update_date,
            COALESCE(h.filename, '') filename, COALESCE(h.gcp_name, '') gcp_name, bucketname
            from project_other_task_message pmsgrp 
            JOIN user r ON r.id = pmsgrp.create_id 
            left JOIN user p ON p.id = pmsgrp.updated_id 
            JOIN gcp_storage_file h ON h.batch_id = pmsgrp.id AND h.batch_type = 'other_task_msg'
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
            $bucket = "";
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

                $bucket = $row['bucketname'];
            
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
                    "bucket" => $bucket,

                    "page_name" => "Main Page",

                    "url" => 'project03_other?sid=' . $pid,

                    "stage" => $stage,
                    "bucket" => $bucket,
                );
            }

            return $merged_results;
      }

      function GetOtherTaskMsgR($pac_id, $db, $pid, $stage)
      {
        $sql = "select h.id replay_id, pmsgrp.message reply, h.`status` reply_status, r.username replyer, r.pic_url replyer_pic, h.created_at reply_date, COALESCE(p.username, '') updator, COALESCE(p.updated_at, '') update_date,
            COALESCE(h.filename, '') filename, COALESCE(h.gcp_name, '') gcp_name, bucketname
            from project_other_task_message_r pmsgrp 
            JOIN gcp_storage_file h ON h.batch_id = pmsgrp.id AND h.batch_type = 'other_task_msg_r'
            JOIN user r ON r.id = pmsgrp.create_id 
            left JOIN user p ON p.id = pmsgrp.updated_id 
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
            $bucket = "";
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

                $bucket = $row['bucketname'];
            
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
                    "bucket" => $bucket,

                    "page_name" => "Main Page",

                    "url" => 'project03_other?sid=' . $pid,

                    "stage" => $stage,
                    "bucket" => $bucket,
                );
            }

            return $merged_results;
      }

      function GetOtherTaskMsgRep($pac_id, $db, $pid, $stage)
      {
        $sql = "select h.id replay_id, pmsgrp.message reply, h.`status` reply_status, r.username replyer, r.pic_url replyer_pic, h.created_at reply_date, COALESCE(p.username, '') updator, COALESCE(p.updated_at, '') update_date,
            COALESCE(h.filename, '') filename, COALESCE(h.gcp_name, '') gcp_name, bucketname
            from project_other_task_message_reply pmsgrp 
            JOIN gcp_storage_file h ON h.batch_id = pmsgrp.id AND h.batch_type = 'other_task_msg_rep'
            JOIN user r ON r.id = pmsgrp.create_id 
            left JOIN user p ON p.id = pmsgrp.updated_id 
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
            $bucket = "";
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

                $bucket = $row['bucketname'];
            
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
                    "bucket" => $bucket,

                    "page_name" => "Main Page",

                    "url" => 'project03_other?sid=' . $pid,

                    "stage" => $stage,
                    "bucket" => $bucket,
                );
            }

            return $merged_results;
      }

      function GetOtherTaskMsgRepR($pac_id, $db, $pid, $stage)
      {
        $sql = "select h.id replay_id, pmsgrp.message reply, h.`status` reply_status, r.username replyer, r.pic_url replyer_pic, h.created_at reply_date, COALESCE(p.username, '') updator, COALESCE(p.updated_at, '') update_date,
        COALESCE(h.filename, '') filename, COALESCE(h.gcp_name, '') gcp_name, bucketname
            from project_other_task_message_reply_r pmsgrp 
            JOIN gcp_storage_file h ON h.batch_id = pmsgrp.id AND h.batch_type = 'other_task_msg_rep_r'
            JOIN user r ON r.id = h.create_id 
            left JOIN user p ON p.id = h.updated_id 
            where pmsgrp.id = " . $pac_id;

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
            $bucket = "";
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

                $bucket = $row['bucketname'];
            
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
                    "bucket" => $bucket,

                    "page_name" => "Main Page",

                    "url" => 'project03_other?sid=' . $pid,

                    "stage" => $stage,
        
                );
            }

            return $merged_results;
      }
    

?>
