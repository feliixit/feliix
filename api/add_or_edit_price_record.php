<?php
error_reporting(0);
//header("Access-Control-Allow-Origin: https://feliix.myvnc.com");
header("Access-Control-Allow-Origin: *");
header("Content-Type: multipart/form-data; charset=UTF-8");
header("Access-Control-Allow-Methods: POST");
header("Access-Control-Max-Age: 3600");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

$jwt = (isset($_POST['jwt']) ?  $_POST['jwt'] : '');
$action = (isset($_POST['action']) ?  $_POST['action'] : 4);
$id = (isset($_POST['id']) ?  $_POST['id'] : 0);
$account = (isset($_POST['account']) ?  $_POST['account']: 0);
$category = (isset($_POST['category']) ?  $_POST['category'] : '');
$sub_category = (isset($_POST['sub_category']) ?  $_POST['sub_category'] : '');
$related_account = (isset($_POST['related_account']) ?  $_POST['related_account'] : 0);
$details = (isset($_POST['details']) ?  $_POST['details'] : '');
$pic_url = (isset($_POST['pic_url']) ?  $_POST['pic_url'] : '');
$payee = (isset($_POST['payee']) ?  $_POST['payee'] : '');
$paid_date = (isset($_POST['paid_date']) ?  $_POST['paid_date'] : '');
$cash_in = (isset($_POST['cash_in']) ?  $_POST['cash_in'] : 0);
$cash_out = (isset($_POST['cash_out']) ?  $_POST['cash_out'] : 0);
$remarks = (isset($_POST['remarks']) ?  $_POST['remarks'] : '');
$is_locked = (isset($_POST['is_locked']) ?  (int)$_POST['is_locked'] : 0);
$is_enabled = (isset($_POST['is_enabled']) ?  (int)$_POST['is_enabled'] : 1);
$is_marked = (isset($_POST['is_marked']) ?  (int)$_POST['is_marked'] : 0);
$created_at = (isset($_POST['create_at']) ?  $_POST['create_at'] : '');
$updated_at = (isset($_POST['update_at']) ?  $_POST['update_at'] : '');
$start_date = (isset($_POST['start_date']) ?  $_POST['start_date'] : '2020/08/08');
$end_date = (isset($_POST['end_date']) ?  $_POST['end_date'] : '');
$merged_results = array();
include_once 'config/core.php';
include_once 'libs/php-jwt-master/src/BeforeValidException.php';
include_once 'libs/php-jwt-master/src/ExpiredException.php';
include_once 'libs/php-jwt-master/src/SignatureInvalidException.php';
include_once 'libs/php-jwt-master/src/JWT.php';

include_once 'config/database.php';
include_once 'objects/add_or_edit_price_record.php';
include_once 'config/conf.php';

$database = new Database();
$db = $database->getConnection();

$priceRecord = new PriceRecord($db);
//$le = new Leave($db);

use \Firebase\JWT\JWT;
if ( !isset( $jwt ) ) {
    http_response_code(401);

    echo json_encode(array("message" => "Access denied1."));
    die();
}
else
{
    if($action == 1){
        //select all
        try{
            $query = "SELECT * from price_record where is_enabled = true";

            $stmt = $db->prepare( $query );
            $stmt->execute();

            while($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                $merged_results[] = $row;
            }
            echo json_encode($merged_results, JSON_UNESCAPED_SLASHES);
        }
        catch(Exception $e){
            http_response_code(401);

            echo json_encode(array("message" => ".$e."));
        }
    }
    else if($action == 2) {
        try {
            // decode jwt
            //$key = 'myKey';
            //$decoded = JWT::decode($jwt, $key, array('HS256'));

            $priceRecord->account = $account;
            $priceRecord->category = $category;
            $priceRecord->sub_category = $sub_category;
            $priceRecord->related_account = $related_account;
            $priceRecord->details = $details;
            $priceRecord->pic_url = $pic_url;
            $priceRecord->payee = $payee;
            $priceRecord->paid_date = $paid_date;
            $priceRecord->cash_in = $cash_in;
            $priceRecord->cash_out = $cash_out;
            $priceRecord->remarks = $remarks;
            $priceRecord->is_locked = $is_locked;
            $priceRecord->is_enabled = $is_enabled;
            $priceRecord->is_marked = $is_marked;
            $priceRecord->created_at = $created_at;
            $priceRecord->updated_at = $updated_at;
            $arr = $priceRecord->create();

            http_response_code(200);
            echo json_encode(array($arr));
            //echo json_encode(array("message" => " Add success at " . date("Y-m-d") . " " . date("h:i:sa")));

        } // if decode fails, it means jwt is invalid
        catch (Exception $e) {

            http_response_code(401);

            echo json_encode(array("message" => "Access denied."));

        }
    }else if($action == 3){
        //update
        try {
            // decode jwt
            //$key = 'myKey';
            //$decoded = JWT::decode($jwt, $key, array('HS256'));
            $priceRecord->id = $id;
            $priceRecord->category = $category;
            $priceRecord->sub_category = $sub_category;
            $priceRecord->related_account = $related_account;
            $priceRecord->details = $details;
            $priceRecord->pic_url = $pic_url;
            $priceRecord->payee = $payee;
            $priceRecord->paid_date = $paid_date;
            $priceRecord->cash_in = $cash_in;
            $priceRecord->cash_out = $cash_out;
            $priceRecord->remarks = $remarks;
            $priceRecord->is_locked = $is_locked;
            $priceRecord->is_enabled = $is_enabled;
            $priceRecord->is_marked = $is_marked;
            $priceRecord->created_at = $created_at;
            $priceRecord->updated_at = $updated_at;
            $arr = $priceRecord->update();

            http_response_code(200);
            echo json_encode(array($arr));
            echo json_encode(array("message" => " Update success at " . date("Y-m-d") . " " . date("h:i:sa")));

        } // if decode fails, it means jwt is invalid
        catch (Exception $e) {

            http_response_code(401);

            echo json_encode(array("message" => "Access denied."));
        }
    }else if($action == 4) {
        //select by date
        try{
            $query = "SELECT * from price_record where is_enabled = true ";
            if($start_date!='') {
                $query = $query . " and paid_date >= '$start_date' ";
            }

            if($end_date!='') {
                $query = $query . " and paid_date <= '$end_date' ";
            }
            
            if($category!='') {
                $query = $query . " and category <= '$category' ";
            }
            
            if($sub_category!='') {
                $query = $query . " and sub_category <= '$sub_category' ";
            }
            $query = $query . " order by created_at desc ";
            $stmt = $db->prepare( $query );
            $stmt->execute();
            while($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                $merged_results[] = $row;
            }
            echo json_encode($merged_results, JSON_UNESCAPED_SLASHES);
        }
        catch(Exception $e){
            http_response_code(401);

            echo json_encode(array("message" => ".$e."));
        }
    }else if($action == 5) {
        //get members
        try{
            $query = "SELECT * from user";
            $stmt = $db->prepare( $query );
            $stmt->execute();

            while($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                $merged_results[] = $row;
            }
            echo json_encode($merged_results, JSON_UNESCAPED_SLASHES);
        }
        catch(Exception $e){
            http_response_code(401);

            echo json_encode(array("message" => ".$e."));
        }
    }else if($action == 6){
        //select by id
        try{
            $query = "SELECT * from price_record where id = ".$id;
            $stmt = $db->prepare( $query );
            $stmt->execute();
            while($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                $merged_results[] = $row;
            }
            echo json_encode($merged_results, JSON_UNESCAPED_SLASHES);
        }
        catch(Exception $e){
            http_response_code(401);

            echo json_encode(array("message" => ".$e."));
        }
    }
}
