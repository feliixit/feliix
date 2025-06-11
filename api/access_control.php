<?php
error_reporting(0);
//header("Access-Control-Allow-Origin: https://feliix.myvnc.com");
header("Access-Control-Allow-Origin: *");
header("Content-Type: multipart/form-data; charset=UTF-8");
header("Access-Control-Allow-Methods: POST");
header("Access-Control-Max-Age: 3600");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

$jwt = (isset($_POST['jwt']) ?  $_POST['jwt'] : '');
$action = (isset($_POST['action']) ?  $_POST['action'] : 1);

$payess1 = (isset($_POST['payess1']) ?  $_POST['payess1'] : '');
$payess2 = (isset($_POST['payess2']) ?  $_POST['payess2'] : '');
$payess3 = (isset($_POST['payess3']) ?  $_POST['payess3'] : '');
$payess4 = (isset($_POST['payess4']) ?  $_POST['payess4'] : '');
$payess5 = (isset($_POST['payess5']) ?  $_POST['payess5'] : '');
$payess6 = (isset($_POST['payess6']) ?  $_POST['payess6'] : '');
$payess7 = (isset($_POST['payess7']) ?  $_POST['payess7'] : '');
$payess8 = (isset($_POST['payess8']) ?  $_POST['payess8'] : '');

$access1 = (isset($_POST['access1']) ?  $_POST['access1'] : '');
$access2 = (isset($_POST['access2']) ?  $_POST['access2'] : '');
$access3 = (isset($_POST['access3']) ?  $_POST['access3'] : '');
$access4 = (isset($_POST['access4']) ?  $_POST['access4'] : '');
$access5 = (isset($_POST['access5']) ?  $_POST['access5'] : '');
$access6 = (isset($_POST['access6']) ?  $_POST['access6'] : '');
$access7 = (isset($_POST['access7']) ?  $_POST['access7'] : '');
$knowledge = (isset($_POST['knowledge']) ?  $_POST['knowledge'] : '');
$vote1 = (isset($_POST['vote1']) ?  $_POST['vote1'] : '');
$vote2 = (isset($_POST['vote2']) ?  $_POST['vote2'] : '');
$schedule_confirm = (isset($_POST['schedule_confirm']) ?  $_POST['schedule_confirm'] : '');
$halfday = (isset($_POST['halfday']) ?  $_POST['halfday'] : '');
$tag_management = (isset($_POST['tag_management']) ?  $_POST['tag_management'] : '');
$soa = (isset($_POST['soa']) ?  $_POST['soa'] : '');
$transmittal = (isset($_POST['transmittal']) ?  $_POST['transmittal'] : '');
$edit_emp = (isset($_POST['edit_emp']) ?  $_POST['edit_emp'] : '');
$edit_basic = (isset($_POST['edit_basic']) ?  $_POST['edit_basic'] : '');
$office_items = (isset($_POST['office_items']) ?  $_POST['office_items'] : '');
$office_item_approve = (isset($_POST['office_item_approve']) ?  $_POST['office_item_approve'] : '');
$office_item_release = (isset($_POST['office_item_release']) ?  $_POST['office_item_release'] : '');
$limited_access = (isset($_POST['limited_access']) ?  $_POST['limited_access'] : '');
$inventory_checker = (isset($_POST['inventory_checker']) ?  $_POST['inventory_checker'] : '');
$inventory_approver = (isset($_POST['inventory_approver']) ?  $_POST['inventory_approver'] : '');
$frozen_office = (isset($_POST['frozen_office']) ?  $_POST['frozen_office'] : '');
$quotation_control = (isset($_POST['quotation_control']) ?  $_POST['quotation_control'] : '');
$cost_lighting = (isset($_POST['cost_lighting']) ?  $_POST['cost_lighting'] : '');
$cost_furniture = (isset($_POST['cost_furniture']) ?  $_POST['cost_furniture'] : '');
$leadership_assessment = (isset($_POST['leadership_assessment']) ?  $_POST['leadership_assessment'] : '');
$special_agreement = (isset($_POST['special_agreement']) ?  $_POST['special_agreement'] : '');
$for_user = (isset($_POST['for_user']) ?  $_POST['for_user'] : '');
$for_profile = (isset($_POST['for_profile']) ?  $_POST['for_profile'] : '');
$product_edit = (isset($_POST['product_edit']) ?  $_POST['product_edit'] : '');
$product_duplicate = (isset($_POST['product_duplicate']) ?  $_POST['product_duplicate'] : '');
$product_delete = (isset($_POST['product_delete']) ?  $_POST['product_delete'] : '');
$inventory_modify = (isset($_POST['inventory_modify']) ?  $_POST['inventory_modify'] : '');

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

use \Firebase\JWT\JWT;

if (!isset($jwt)) {
    http_response_code(401);

    echo json_encode(array("message" => "Access denied."));
    die();
} else {
    if ($action == 1) {
        //select all
        try {
            $query = "SELECT payess1, payess2, payess3, salary payess4, salary_mgt payess5, salary_slip_mgt payess6, 
                            payess7, payess8, access1, access2, access3, access4, access5, access6, access7, knowledge, 
                            vote1, vote2, schedule_confirm, halfday, tag_management, soa, transmittal, 
                            edit_emp, edit_basic, office_items, office_item_approve, office_item_release, limited_access, 
                            inventory_checker, inventory_approver, frozen_office, quotation_control, cost_lighting, cost_furniture, 
                            leadership_assessment, special_agreement, for_user, for_profile, product_edit, product_duplicate, product_delete,
                            inventory_modify
                            from access_control where id = 1";

            $stmt = $db->prepare($query);
            $stmt->execute();

            while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                $merged_results[] = $row;
            }
            echo json_encode($merged_results, JSON_UNESCAPED_SLASHES);
        } catch (Exception $e) {
            http_response_code(401);

            echo json_encode(array("message" => ".$e."));
        }
    } else if ($action == 3) {
        //update
        try {
            $query = "UPDATE access_control
                        set payess1 = :payess1, 
                            payess2 = :payess2, 
                            payess3 = :payess3,
                            salary = :salary,
                            salary_mgt = :salary_mgt,
                            salary_slip_mgt = :salary_slip_mgt,
                            payess7 = :payess7,
                            payess8 = :payess8,
                            access1 = :access1,
                            access2 = :access2,
                            access3 = :access3,
                            access4 = :access4,
                            access5 = :access5,
                            access6 = :access6,
                            access7 = :access7,
                            knowledge = :knowledge,
                            vote1 = :vote1,
                            vote2 = :vote2,
                            schedule_confirm = :schedule_confirm,
                            halfday = :halfday,
                            tag_management = :tag_management,
                            soa = :soa,
                            transmittal = :transmittal,
                            edit_emp = :edit_emp,
                            edit_basic = :edit_basic,
                            office_items = :office_items,
                            office_item_approve = :office_item_approve,
                            office_item_release = :office_item_release,
                            limited_access = :limited_access,
                            inventory_checker = :inventory_checker,
                            inventory_approver = :inventory_approver,
                            frozen_office = :frozen_office,
                            quotation_control = :quotation_control,
                            cost_lighting = :cost_lighting,
                            cost_furniture = :cost_furniture,
                            leadership_assessment = :leadership_assessment,
                            special_agreement = :special_agreement,
                            for_user = :for_user,
                            for_profile = :for_profile,
                            product_edit = :product_edit,
                            product_duplicate = :product_duplicate,
                            product_delete = :product_delete,
                            inventory_modify = :inventory_modify
                        where id = :id";

            // prepare the query
            $stmt = $db->prepare($query);

            // bind the values
            $id = 1;

            $payess1 = htmlspecialchars(strip_tags($payess1));
            $payess2 = htmlspecialchars(strip_tags($payess2));
            $payess3 = htmlspecialchars(strip_tags($payess3));
            $payess4 = htmlspecialchars(strip_tags($payess4));
            $payess5 = htmlspecialchars(strip_tags($payess5));
            $payess6 = htmlspecialchars(strip_tags($payess6));
            $payess7 = htmlspecialchars(strip_tags($payess7));
            $payess8 = htmlspecialchars(strip_tags($payess8));
            $access1 = htmlspecialchars(strip_tags($access1));
            $access2 = htmlspecialchars(strip_tags($access2));
            $access3 = htmlspecialchars(strip_tags($access3));
            $access4 = htmlspecialchars(strip_tags($access4));
            $access5 = htmlspecialchars(strip_tags($access5));
            $access6 = htmlspecialchars(strip_tags($access6));
            $access7 = htmlspecialchars(strip_tags($access7));
            $knowledge = htmlspecialchars(strip_tags($knowledge));
            $vote1 = htmlspecialchars(strip_tags($vote1));
            $vote2 = htmlspecialchars(strip_tags($vote2));
            $schedule_confirm = htmlspecialchars(strip_tags($schedule_confirm));
            $halfday = htmlspecialchars(strip_tags($halfday));
            $tag_management = htmlspecialchars(strip_tags($tag_management));
            $soa = htmlspecialchars(strip_tags($soa));
            $transmittal = htmlspecialchars(strip_tags($transmittal));
            $edit_emp = htmlspecialchars(strip_tags($edit_emp));
            $edit_basic = htmlspecialchars(strip_tags($edit_basic));
            $office_items = htmlspecialchars(strip_tags($office_items));
            $office_item_approve = htmlspecialchars(strip_tags($office_item_approve));
            $office_item_release = htmlspecialchars(strip_tags($office_item_release));
            $limited_access = htmlspecialchars(strip_tags($limited_access));
            $inventory_checker = htmlspecialchars(strip_tags($inventory_checker));
            $inventory_approver = htmlspecialchars(strip_tags($inventory_approver));
            $frozen_office = htmlspecialchars(strip_tags($frozen_office));
            $quotation_control = htmlspecialchars(strip_tags($quotation_control));
            $cost_lighting = htmlspecialchars(strip_tags($cost_lighting));
            $cost_furniture = htmlspecialchars(strip_tags($cost_furniture));
            $leadership_assessment = htmlspecialchars(strip_tags($leadership_assessment));
            $special_agreement = htmlspecialchars(strip_tags($special_agreement));
            $for_user = htmlspecialchars(strip_tags($for_user));
            $for_profile = htmlspecialchars(strip_tags($for_profile));
            $product_edit = htmlspecialchars(strip_tags($product_edit));
            $product_duplicate = htmlspecialchars(strip_tags($product_duplicate));
            $product_delete = htmlspecialchars(strip_tags($product_delete));
            $inventory_modify = htmlspecialchars(strip_tags($inventory_modify));

            // bind the values
            $stmt->bindParam(':id', $id);
            $stmt->bindParam(':payess1', $payess1);
            $stmt->bindParam(':payess2', $payess2);
            $stmt->bindParam(':payess3', $payess3);
            $stmt->bindParam(':salary', $payess4);
            $stmt->bindParam(':salary_mgt', $payess5);
            $stmt->bindParam(':salary_slip_mgt', $payess6);
            $stmt->bindParam(':payess7', $payess7);
            $stmt->bindParam(':payess8', $payess8);
            $stmt->bindParam(':access1', $access1);
            $stmt->bindParam(':access2', $access2);
            $stmt->bindParam(':access3', $access3);
            $stmt->bindParam(':access4', $access4);
            $stmt->bindParam(':access5', $access5);
            $stmt->bindParam(':access6', $access6);
            $stmt->bindParam(':access7', $access7);
            $stmt->bindParam(':knowledge', $knowledge);
            $stmt->bindParam(':vote1', $vote1);
            $stmt->bindParam(':vote2', $vote2);
            $stmt->bindParam(':schedule_confirm', $schedule_confirm);
            $stmt->bindParam(':halfday', $halfday);
            $stmt->bindParam(':tag_management', $tag_management);
            $stmt->bindParam(':soa', $soa);
            $stmt->bindParam(':transmittal', $transmittal);
            $stmt->bindParam(':edit_emp', $edit_emp);
            $stmt->bindParam(':edit_basic', $edit_basic);
            $stmt->bindParam(':office_items', $office_items);
            $stmt->bindParam(':office_item_approve', $office_item_approve);
            $stmt->bindParam(':office_item_release', $office_item_release);
            $stmt->bindParam(':limited_access', $limited_access);
            $stmt->bindParam(':inventory_checker', $inventory_checker);
            $stmt->bindParam(':inventory_approver', $inventory_approver);
            $stmt->bindParam(':frozen_office', $frozen_office);
            $stmt->bindParam(':quotation_control', $quotation_control);
            $stmt->bindParam(':cost_lighting', $cost_lighting);
            $stmt->bindParam(':cost_furniture', $cost_furniture);
            $stmt->bindParam(':leadership_assessment', $leadership_assessment);
            $stmt->bindParam(':special_agreement', $special_agreement);
            $stmt->bindParam(':for_user', $for_user);
            $stmt->bindParam(':for_profile', $for_profile);
            $stmt->bindParam(':product_edit', $product_edit);
            $stmt->bindParam(':product_duplicate', $product_duplicate);
            $stmt->bindParam(':product_delete', $product_delete);
            $stmt->bindParam(':inventory_modify', $inventory_modify);

            try {
                // execute the query, also check if query was successful
                if ($stmt->execute()) {
                    return true;
                } else {
                    $arr = $stmt->errorInfo();
                    error_log($arr[2]);
                    return false;
                }
            } catch (Exception $e) {
                error_log($e->getMessage());
                return false;
            }

            http_response_code(200);
            echo json_encode(array($arr));
            echo json_encode(array("message" => " Update success at " . date("Y-m-d") . " " . date("h:i:sa")));
        } // if decode fails, it means jwt is invalid
        catch (Exception $e) {

            http_response_code(401);

            echo json_encode(array("message" => "Access denied."));
        }
    }
}
