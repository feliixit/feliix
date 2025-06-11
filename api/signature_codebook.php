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

      $page = (isset($_GET['page']) ?  $_GET['page'] : "");
      $size = (isset($_GET['size']) ?  $_GET['size'] : "");

      $d = (isset($_GET['d']) ?  $_GET['d'] : "");
      $c = (isset($_GET['c']) ?  $_GET['c'] : "");
      $c = urldecode($c);
      $t = (isset($_GET['t']) ?  $_GET['t'] : "");
      $t = urldecode($t);
      $k = (isset($_GET['k']) ?  $_GET['k'] : "");
      $k = urldecode($k);
      $tag_array = json_decode($t, true);
      $b = (isset($_GET['b']) ?  $_GET['b'] : "");
      $b = urldecode($b);

      $of1 = (isset($_GET['of1']) ?  $_GET['of1'] : '');
      $ofd1 = (isset($_GET['ofd1']) ?  $_GET['ofd1'] : '');
      $of2 = (isset($_GET['of2']) ?  $_GET['of2'] : '');
      $ofd2 = (isset($_GET['ofd2']) ?  $_GET['ofd2'] : '');

      $database = new Database();
      $db = $database->getConnection();

      switch ($method) {
          case 'GET':
            $merged_results = array();

            $query_cnt = "SELECT count(*) cnt FROM signature_codebook p  WHERE  p.STATUS <> -1 ";

            // product main
            $sql = "SELECT p.*, cu.username created_name, uu.username updated_name FROM signature_codebook p left join `user` cu on cu.id = p.create_id left join `user` uu on uu.id = p.updated_id WHERE  p.STATUS <> -1";

            if (!empty($_GET['page'])) {
                $page = filter_input(INPUT_GET, 'page', FILTER_VALIDATE_INT);
                if (false === $page) {
                    $page = 1;
                }
            }

            if($d != "" && $d != "0")
            {
                $sql = $sql . " and p.id = " . $d . " ";
                $query_cnt = $query_cnt . " and p.id = " . $d . " ";
            }

            if($c != "")
            {
                $sql = $sql . " and p.code like '%" . $c . "%' ";
                $query_cnt = $query_cnt . " and p.code like '%" . $c . "%' ";
            }

            $tag_sql = "";
            if($tag_array != null)
            {
                for ($i = 0; $i < count($tag_array); $i++) {
                    $tag_sql = $tag_sql . " p.tags like '%" . $tag_array[$i] . "%' and ";
                }
            }

            if($tag_sql != "")
            {
                $tag_sql = substr($tag_sql, 0, -4);

                $sql = $sql . " and (" . $tag_sql . ") ";
                $query_cnt = $query_cnt . " and (" . $tag_sql . ") ";
            }

            if($b != "")
            {
                $sql = $sql . " and p.brand = '" . $b . "' ";
                $query_cnt = $query_cnt . " and p.brand = '" . $b . "' ";
            }

            if($k != "")
            {
                $sql = $sql . " and (p.name like '%" . $k . "%' or p.position like '%" . $k . "%' or p.phone like '%" . $k . "%' or p.email like '%" . $k . "%') ";
                $query_cnt = $query_cnt . " and (p.name like '%" . $k . "%' or p.position like '%" . $k . "%' or p.phone like '%" . $k . "%' or p.email like '%" . $k . "%') ";
            }
    
            
$sOrder = "";
if($of1 != "" && $of1 != "0")
{
    switch ($of1)
    {   
        case 1:
            if($ofd1 == 2)
                $sOrder = "p.id desc";
            else
                $sOrder = "p.id ";
            break;  
        case 2:
            if($ofd1 == 2)
                $sOrder = "Coalesce(p.created_at, '0000-00-00') desc";
            else
                $sOrder = "Coalesce(p.created_at, '9999-99-99') ";
            break;  
        case 3:
            if($ofd1 == 2)
                $sOrder = "Coalesce(p.updated_at, '0000-00-00') desc";
            else
                $sOrder = "Coalesce(p.updated_at, '9999-99-99') ";
            break;  
        case 4:
            if($ofd1 == 2)
                $sOrder = "Coalesce(p.srp_max, 0.00) desc";
            else
                $sOrder = "Coalesce(p.srp_min, 99999999.99) ";
            break;  

        case 5:
            if($ofd1 == 2)
                $sOrder = "Coalesce(p.qp_max, 0.00) desc";
            else
                $sOrder = "Coalesce(p.qp_min, 99999999.99) ";
            break;  
        
        default:
    }
}

if($of2 != "" && $of2 != "0" && $sOrder != "")
{
    switch ($of2)
    {
        case 1:
            if($ofd2 == 2)
                $sOrder .= ", p.id desc";
            else
                $sOrder .= ", p.id";
            break;  
        case 2:
            if($ofd2 == 2)
                $sOrder .= ", Coalesce(p.created_at, '0000-00-00') desc";
            else
                $sOrder .= ", Coalesce(p.created_at, '9999-99-99') ";
            break;  
        case 3:
            if($ofd2 == 2)
                $sOrder .= ", Coalesce(p.updated_at, '0000-00-00') desc";
            else
                $sOrder .= ", Coalesce(p.updated_at, '9999-99-99') ";
            break;  
        case 4:
            if($ofd2 == 2)
                $sOrder .= ", Coalesce(p.srp_max, 0.00) desc";
            else
                $sOrder .= ", Coalesce(p.srp_min, 99999999.99) ";
            break;  
        case 5:
            if($ofd2 == 2)
                $sOrder .= ", Coalesce(p.qp_max, 0.00) desc";
            else
                $sOrder .= ", Coalesce(p.qp_min, 99999999.99) ";
            break;  
     
        default:
    }
}


if($of2 != "" && $of2 != "0" && $sOrder == "")
{
    switch ($of2)
    {
        case 1:
            if($ofd2 == 2)
                $sOrder = "p.id desc";
            else
                $sOrder = "p.id";
            break;  
        case 2:
            if($ofd2 == 2)
                $sOrder = "Coalesce(p.created_at, '0000-00-00') desc";
            else
                $sOrder = "Coalesce(p.created_at, '9999-99-99') ";
            break;  
        case 3:
            if($ofd2 == 2)
                $sOrder = "Coalesce(p.updated_at, '0000-00-00') desc";
            else
                $sOrder = "Coalesce(p.updated_at, '9999-99-99') ";
            break;  
        case 4:
            if($ofd2 == 2)
                $sOrder = "Coalesce(p.srp_max, 0.00) desc";
            else
                $sOrder = "Coalesce(p.srp_min, 99999999.99) ";
            break;  
        case 5:
            if($ofd2 == 2)
                $sOrder = "Coalesce(p.qp_max, 0.00) desc";
            else
                $sOrder = "Coalesce(p.qp_min, 99999999.99) ";
            break;  
     
        default:
    }
}

if($sOrder != "")
    $sql = $sql . " order by  " . $sOrder;
else
    $sql = $sql . " ORDER BY p.name asc ";
    
            if (!empty($_GET['size'])) {
                $size = filter_input(INPUT_GET, 'size', FILTER_VALIDATE_INT);
                if (false === $size) {
                    $size = 10;
                }
    
                $offset = ($page - 1) * $size;
    
                $sql = $sql . " LIMIT " . $offset . "," . $size;
            }


            $cnt = 0;
            $stmt_cnt = $db->prepare( $query_cnt );
            $stmt_cnt->execute();
            while($row = $stmt_cnt->fetch(PDO::FETCH_ASSOC)) {
                $cnt = $row['cnt'];
            }

            $stmt = $db->prepare( $sql );
            $stmt->execute();

            

            while($row = $stmt->fetch(PDO::FETCH_ASSOC)) {

                $id = '';
                $name = '';
                $position = '';
                $phone = '';
                $email = '';

                $create_id = '';
                $created_at = '';
                $update_id = '';
                $updated_at = '';

                $created_name = '';
                $updated_name = '';

                $status = '';

                $pic_url = '';
                $url = '';

                $id = $row['id'];

                $name = $row['name'];
                $position = $row['position'];
                $phone = $row['phone'];
                $email = $row['email'];

             
                $status = $row['status'];
                $create_id = $row['create_id'];
                $created_at = $row['created_at'];
                $updated_id = $row['updated_id'];
                $updated_at = $row['updated_at'];

                $created_name = $row['created_name'];
                $updated_name = $row['updated_name'];

                $pic_url = $row['pic_url'];

                if($pic_url != '')
                    $url = 'https://storage.googleapis.com/feliiximg/' . $pic_url;

                $merged_results[] = array( "id" => $id,
                                    "name" => $name,
                                    "position" => $position,
                                    "phone" => $phone,
                                    "email" => $email,
                                    "pic_url" => $pic_url,
                                    "url" => $url,
                                    "status" => $status,
                                    "create_id" => $create_id,
                                    "created_at" => $created_at,
                                    "updated_id" => $updated_id,
                                    "updated_at" => $updated_at,
                                    "created_name" => $created_name,
                                    "updated_name" => $updated_name,
                                    "cnt" => $cnt
                                    );

            }



            echo json_encode($merged_results, JSON_UNESCAPED_SLASHES);

            break;

}



?>
