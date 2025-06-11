<?php
error_reporting(E_ALL);
//header("Access-Control-Allow-Origin: https://feliix.myvnc.com");
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: POST");
header("Access-Control-Max-Age: 3600");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

$jwt = (isset($_POST['jwt']) ?  $_POST['jwt'] : null);

$quotation_id = isset($_POST['quotation_id']) ? $_POST['quotation_id'] : 0;


$pixa = isset($_POST['pixa']) ? $_POST['pixa'] : 0;
$show = isset($_POST['show']) ? $_POST['show'] : '';
$contact = isset($_POST['contact']) ? $_POST['contact'] : '';

$pageless = isset($_POST['pageless']) ? $_POST['pageless'] : '';

include_once 'config/core.php';
include_once 'libs/php-jwt-master/src/BeforeValidException.php';
include_once 'libs/php-jwt-master/src/ExpiredException.php';
include_once 'libs/php-jwt-master/src/SignatureInvalidException.php';
include_once 'libs/php-jwt-master/src/JWT.php';

include_once 'config/database.php';
include_once 'config/conf.php';
require_once '../vendor/autoload.php';

$database = new Database();
$db = $database->getConnection();

$conf = new Conf();

use \Firebase\JWT\JWT;
use Google\Cloud\Storage\StorageClient;

if ( !isset( $jwt ) ) {
    http_response_code(401);

    echo json_encode(array("message" => "Failure at " . date("Y-m-d") . " " . date("h:i:sa") . " Access denied."));
    die();
}
else
{
    try {
        // decode jwt
        $decoded = JWT::decode($jwt, $key, array('HS256'));

        $user_id = $decoded->data->id;
  
        // now you can apply
        $uid = $user_id;

        // quotation_page
        $query = "UPDATE transmittal set pixa_t = :pixa_t, show_t = :show_t, pageless = :pageless, contact = :contact, updated_id = :updated_id, updated_at = now()
                WHERE
                `id` = :quotation_id";

        // prepare the query
        $stmt = $db->prepare($query);

        // bind the values
        $stmt->bindParam(':pixa_t', $pixa);
        $stmt->bindParam(':show_t', $show);
        $stmt->bindParam(':pageless', $pageless);
        $stmt->bindParam(':contact', $contact);
        $stmt->bindParam(':quotation_id', $quotation_id);
        $stmt->bindParam(':updated_id', $uid);

        try {
        // execute the query, also check if query was successful
        if (!$stmt->execute()) {
            $arr = $stmt->errorInfo();
            error_log($arr[2]);

            http_response_code(501);
            echo json_encode(array("Failure at " . date("Y-m-d") . " " . date("h:i:sa") . " " . $arr[2]));
            die();
        }
        } catch (Exception $e) {
        error_log($e->getMessage());

        http_response_code(501);
        echo json_encode(array("Failure at " . date("Y-m-d") . " " . date("h:i:sa") . " " . $e->getMessage()));
        die();
        }
        
        http_response_code(200);
        echo json_encode(array("message" => "Success at " . date("Y-m-d") . " " . date("h:i:sa"), "id" => $quotation_id));
        
    }
    catch (Exception $e){

        error_log($e->getMessage());
        http_response_code(501);
        echo json_encode(array("Failure at " . date("Y-m-d") . " " . date("h:i:sa") . $e->getMessage()));
        die();

    }
}

