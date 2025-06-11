<?php

error_reporting(E_ALL);
ini_set('display_errors', 1);
$jwt = (isset($_COOKIE['jwt']) ?  $_COOKIE['jwt'] : null);
include_once 'config/core.php';
include_once 'libs/php-jwt-master/src/BeforeValidException.php';
include_once 'libs/php-jwt-master/src/ExpiredException.php';
include_once 'libs/php-jwt-master/src/SignatureInvalidException.php';
include_once 'libs/php-jwt-master/src/JWT.php';
require_once '../vendor/autoload.php';


use \Firebase\JWT\JWT;
use Google\Cloud\Storage\StorageClient;

$method = $_SERVER['REQUEST_METHOD'];


if (!isset($jwt)) {
    http_response_code(401);

    echo json_encode(array("message" => "Failure at " . date("Y-m-d") . " " . date("h:i:sa") . " " . "Access denied."));
    die();
} else {
    try {
        // decode jwt
        $decoded = JWT::decode($jwt, $key, array('HS256'));
        $user_id = $decoded->data->id;
        $user_name = $decoded->data->username;
        //if(!$decoded->data->is_admin)
        //{
        //  http_response_code(401);

        //  echo json_encode(array("message" => "Access denied."));
        //  die();
        //}
    }
    // if decode fails, it means jwt is invalid
    catch (Exception $e) {

        http_response_code(401);

        echo json_encode(array("message" => "Failure at " . date("Y-m-d") . " " . date("h:i:sa") . " " . "Access denied."));
        die();
    }
}

header('Access-Control-Allow-Origin: *');

include_once 'config/database.php';

switch ($method) {

    case 'POST':

        $database = new Database();
        $db = $database->getConnection();
        $db->beginTransaction();
        $conf = new Conf();

        $jwt = (isset($_POST['jwt']) ?  $_POST['jwt'] : null);
        $id = (isset($_POST['id']) ?  $_POST['id'] : 0);
        $rid = (isset($_POST['rid']) ?  $_POST['rid'] : 0);
        $quotation_id = (isset($_POST['quotation_id']) ?  $_POST['quotation_id'] : 0);

        $block = (isset($_POST['block']) ? $_POST['block'] : []);
        $block_array = json_decode($block,true);

        try{

        if ($id == 0) {
            http_response_code(401);
            echo json_encode(array("message" => "Failure at " . date("Y-m-d") . " " . date("h:i:sa") . " " . "Access denied."));
            die();
        }

        for($i = 0; $i < count($block_array["block"]); $i++)
        {
            if($block_array["block"][$i]["id"] == $id)
            {
                // recalculate the total
                $total = 0;
                for($j = 0; $j < count($block_array["block"][$i]["details"]); $j++)
                {
                    if($block_array["block"][$i]["details"][$j]["total"] != '')
                    {
                        $total += $block_array["block"][$i]["details"][$j]["total"];
                    }
                }

                $block_array["block"][$i]["unit_cost"] = $total;
                if($block_array["block"][$i]["qty"] != '' && $block_array["block"][$i]["ratio"] != '' && $block_array["block"][$i]["discount"] != '' && $block_array["block"][$i]["unit_cost"] != '')
                    $block_array["block"][$i]["total"] = number_format($total * $block_array["block"][$i]["qty"] * $block_array["block"][$i]["ratio"] * (100- $block_array["block"][$i]["discount"]) / 100, 2, '.', '');
            }
        }

        $json = json_encode($block_array["block"]);

        if(if_existed($db, "SELECT * FROM quotation_eng_consumable WHERE id = " . $rid))
        {
            // quotation_page
            $query = "update quotation_eng_consumable
                        set block = :block, updated_id = :user_id, updated_at = now()
                      WHERE
                      `id` = :id";

                      // prepare the query
            $stmt = $db->prepare($query);

            // bind the values
            $stmt->bindParam(':id', $rid);
            $stmt->bindParam(':block', $json);
            $stmt->bindParam(':user_id', $user_id);
        }
        else
        {
            // quotation_page
            $query = "insert into quotation_eng_consumable
                        (quotation_id, title, block, status, create_id, created_at)
                        values
                        (:quotation_id, 'General Requirements',  :block, 0, :user_id, now())";

                        // prepare the query
            $stmt = $db->prepare($query);

            // bind the values
            $stmt->bindParam(':quotation_id', $quotation_id);
            $stmt->bindParam(':block', $json);
            $stmt->bindParam(':user_id', $user_id);
        }

            try {
                // execute the query, also check if query was successful
                if (!$stmt->execute()) {
                    $arr = $stmt->errorInfo();
                    error_log($arr[2]);
                    $db->rollback();
                    http_response_code(501);
                    echo json_encode(array("Failure at " . date("Y-m-d") . " " . date("h:i:sa") . " " . $arr[2]));
                    die();
                }
            } catch (Exception $e) {
                error_log($e->getMessage());
                $db->rollback();
                http_response_code(501);
                echo json_encode(array("Failure at " . date("Y-m-d") . " " . date("h:i:sa") . " " . $e->getMessage()));
                die();
            }


            
            $db->commit();

            http_response_code(200);
            echo json_encode(array("message" => "Success at " . date("Y-m-d") . " " . date("h:i:sa")));
        } catch (Exception $e) {

            error_log($e->getMessage());
            $db->rollback();
            http_response_code(501);
            echo json_encode(array("Failure at " . date("Y-m-d") . " " . date("h:i:sa") . " " . $e->getMessage()));
            die();
        }
        break;
}

function if_existed($db, $query)
{
    $stmt = $db->prepare($query);
    $stmt->execute();
    $num = $stmt->rowCount();
    if ($num > 0) {
        return true;
    } else {
        return false;
    }
}