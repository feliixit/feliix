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
        $iq_id = (isset($_POST['iq_id']) ?  $_POST['iq_id'] : 0);
        $qid = (isset($_POST['qid']) ?  $_POST['qid'] : 0);
        $sn = (isset($_POST['sn']) ?  $_POST['sn'] : 0);

        $access7 = (isset($_POST['access7']) ?  $_POST['access7'] : false);

        if ($iq_id == 0) {
            http_response_code(401);
            echo json_encode(array("message" => "Failure at " . date("Y-m-d") . " " . date("h:i:sa") . " " . "Access denied."));
            die();
        }

        //$block_array = GetQuotationItems($qid, $db);

        
        try {

            //$sn = GetMaxSn($iq_id, $db);

            //for($i=0; $i<count($block_array); $i++) 
            //{
            //    $sn++;
                // insert quotation_page_type_block
                $query = "INSERT INTO iq_item (iq_id, 
                                                sn, 
                                                confirm, 
                                                brand, 
                                                brand_other, 
                                                photo1, 
                                                photo2, 
                                                photo3, 
                                                code, 
                                                brief, 
                                                listing, 
                                                qty, 
                                                srp, 
                                                date_needed, 
                                                pid, 
                                                v1, 
                                                v2, 
                                                v3, 
                                                `status`, 
                                                create_id,
                                                created_at)
                    SELECT 
                    " . $iq_id . ",
                    sn,
                    confirm,
                    brand,
                    brand_other,
                    photo1,
                    photo2,
                    photo3,
                    code,
                    brief,
                    listing,
                    qty,
                    srp,
                    date_needed,
                    pid,
                    v1,
                    v2,
                    v3,
                    0,
                    " . $user_id . ",
                    now()
                    from iq_item 
                    where iq_id = " . $qid;

                // prepare the query
                $stmt = $db->prepare($query);

                try {
                    // execute the query, also check if query was successful
                    if ($stmt->execute()) {
                        $block_id = $db->lastInsertId();
                    } else {
                        $arr = $stmt->errorInfo();
                        error_log($arr[2]);
                        $db->rollback();
                        http_response_code(501);
                        echo json_encode("Failure at " . date("Y-m-d") . " " . date("h:i:sa") . " " . $arr[2]);
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
