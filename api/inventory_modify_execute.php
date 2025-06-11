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

// include_once 'mail.php';

use \Firebase\JWT\JWT;
use Google\Cloud\Storage\StorageClient;

$method = $_SERVER['REQUEST_METHOD'];

define("ON_HAND", 0);
define('LOST', 1);
define("DELIVERED_TO_CLIENT", 2);
define("SCRAPPED", 3);
define("VOIDED", -1);


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

            $request_no = (isset($_POST['request_no']) ?  $_POST['request_no'] : '');            
            $items = (isset($_POST['items']) ?  $_POST['items'] : '[]');
            $reason = (isset($_POST['reason']) ?  $_POST['reason'] : '');
            $which_pool = (isset($_POST['which_pool']) ?  $_POST['which_pool'] : '');
            $related_project = (isset($_POST['related_project']) ?  $_POST['related_project'] : 0);
            $as_sample = (isset($_POST['as_sample']) ?  $_POST['as_sample'] : '');
            $location = (isset($_POST['location']) ?  $_POST['location'] : 1);
            $stage = (isset($_POST['stage']) ?  $_POST['stage'] : 1);
            $id = (isset($_POST['id']) ?  $_POST['id'] : '0');
            $notes = (isset($_POST['notes']) ?  $_POST['notes'] : '');
            $receiver = (isset($_POST['receiver']) ?  $_POST['receiver'] : 0);

            // if($related_project == "")
            // {
            //     $related_project = 0;
            // }

            // if($receiver == "")
            // {
            //     $receiver = 0;
            // }

            $tracking_status = 0;

            $last_id = $id;

            try {
                $query = "update inventory_modify
                set    

                reason = :reason,
                note_1 = :notes, ";

                if($receiver != "" && $receiver != 0)
                {
                    $query .= " receive_id = :receiver, ";
                }

                $query .= "
                which_pool = :which_pool,
                as_sample = :as_sample,
                location = :location, ";

                if($related_project == "")
                {
                    $related_project = 0;
                }
                
                // if($related_project != "" && $related_project != 0)
                $query .= " project_id = :related_project, ";

                $query .= "
                listing = :items,
                updated_id = :updated_id,
                status = :stage,
                updated_at = now()
                where id = :id";


                $items_array = json_decode($items, true);

                if($reason == "Deliver Item(s) to Client")
                {
                    for($i = 0; $i < count($items_array); $i++)
                    {
                        $items_array[$i]['status'] = DELIVERED_TO_CLIENT;
                        $items_array[$i]['status_text'] = "Deliver to Client";
                    }

                    $tracking_status = DELIVERED_TO_CLIENT;
                }
                
                if($reason == "Return Item(s) from Client to Inventory System")
                {
                    for($i = 0; $i < count($items_array); $i++)
                    {
                        $items_array[$i]['status'] = ON_HAND;
                        $items_array[$i]['status_text'] = "On Hand";
                    }

                    $tracking_status = ON_HAND;
                }

                if($reason == "Void Tracking Code of Item(s)")
                {
                    for($i = 0; $i < count($items_array); $i++)
                    {
                        $items_array[$i]['status'] = VOIDED;
                        $items_array[$i]['status_text'] = "Voided";
                    }

                    $tracking_status = VOIDED;
                }

                if($reason == "Item(s) Lost")
                {
                    for($i = 0; $i < count($items_array); $i++)
                    {
                        $items_array[$i]['status'] = LOST;
                        $items_array[$i]['status_text'] = "Lost";
                    }

                    $tracking_status = LOST;
                }

                if($reason == "Item(s) Scrapped")
                {
                    for($i = 0; $i < count($items_array); $i++)
                    {
                        $items_array[$i]['status'] = SCRAPPED;
                        $items_array[$i]['status_text'] = "Scrapped";
                    }

                    $tracking_status = SCRAPPED;
                }

                if($reason == "Change Inventory Pool or Related Project of Item(s)")
                {
                    $project_name = getProjectName($db, $related_project);
                    for($i = 0; $i < count($items_array); $i++)
                    {
                        $items_array[$i]['project_id'] = $related_project;
                        $items_array[$i]['which_pool'] = $which_pool;
                        $items_array[$i]['project_name'] = $project_name;
                    }

                }

                if($reason == "Change Location of Item(s)")
                {
                    for($i = 0; $i < count($items_array); $i++)
                    {
                        $items_array[$i]['location'] = $location;
                        $items_array[$i]['receive_id'] = $receiver;
                    }
                }

                if($reason == "Change Sample Status of Item(s)")
                {
                    for($i = 0; $i < count($items_array); $i++)
                    {
                        $items_array[$i]['as_sample'] = $as_sample;
                    }
                }

                for($i = 0; $i < count($items_array); $i++)
                {
                    $ver = getHistoryRecordByBarcode($db, $items_array[$i]['barcode']);
                    $items_array[$i]['version'] = $ver;
                    $items_array[$i]['updated_at'] = date("Y-m-d H:i:s");
                    $items_array[$i]['updated_by'] = $user_name;
                }
            
                // prepare the query
                $stmt = $db->prepare($query);

                // bind the values
                $stmt->bindParam(':reason', $reason);
                $stmt->bindParam(':notes', $notes);

                if($receiver != "" && $receiver != 0)
                    $stmt->bindParam(':receiver', $receiver);

                $stmt->bindParam(':which_pool', $which_pool);
                $stmt->bindParam(':as_sample', $as_sample);
                $stmt->bindParam(':location', $location);

                //if($related_project != "" && $related_project != 0)
                    $stmt->bindParam(':related_project', $related_project);
             
                $items_changed = json_encode($items_array);

                $stmt->bindParam(':items', $items_changed);
                $stmt->bindParam(':updated_id', $user_id);
                $stmt->bindParam(':stage', $stage);
                $stmt->bindParam(':id', $id);
                
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

                // order_tracking_item
                $tracking_code = array();
                for($i = 0; $i < count($items_array); $i++)
                {
                    $tracking_code[] = $items_array[$i]['id'];
                }

                
                $tracking_code_str = implode(",", $tracking_code);
                $tracking_sql = "update order_tracking_item set `status` = :status, updated_at = now(), updated_id = :updated_id ";

                if($reason == "Change Inventory Pool or Related Project of Item(s)")
                {
                    $tracking_sql .= ", project_id = :project_id, which_pool = :which_pool ";
                }

                if($reason == "Change Location of Item(s)")
                {
                    $tracking_sql .= ", `location` = :location, receive_id = :receive_id ";
                }

                if($reason == "Change Sample Status of Item(s)")
                {
                    $tracking_sql .= ", as_sample = :as_sample ";
                }

                $tracking_sql .= "  where id in (" . $tracking_code_str . ")";

                try {
                    $stmt = $db->prepare($tracking_sql);
                    $stmt->bindParam(':status', $tracking_status);
                    $stmt->bindParam(':updated_id', $user_id);

                    if($reason == "Change Inventory Pool or Related Project of Item(s)")
                    {
                        if($related_project == 0)
                        {
                            $related_projec_minus = -1;
                            $stmt->bindParam(':project_id', $related_projec_minus);
                        }
                        else
                        {
                            $stmt->bindParam(':project_id', $related_project);
                        }
                        
                        $stmt->bindParam(':which_pool', $which_pool);
                    }

                    if($reason == "Change Location of Item(s)")
                    {
                        $stmt->bindParam(':location', $location);
                        $stmt->bindParam(':receive_id', $receiver);
                    }

                    if($reason == "Change Sample Status of Item(s)")
                    {
                        $stmt->bindParam(':as_sample', $as_sample);
                    }

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

                $batch_id = $id;
                $batch_type = "inventory_modify";

                try {
                    // count if there is any file
                    if(isset($_FILES['transmittal_file']['name']))
                    {
                        $total = count($_FILES['transmittal_file']['name']);
                    }
                    else
                    {
                        $total = 0;
                    }
                    // Loop through each file
                    for( $i=0 ; $i < $total ; $i++ ) {

                        if(isset($_FILES['transmittal_file']['name'][$i]))
                        {
                            $image_name = $_FILES['transmittal_file']['name'][$i];
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

                                $file_size = filesize($_FILES['transmittal_file']['tmp_name'][$i]);
                                $size = 0;

                                $obj = $bucket->upload(
                                    fopen($_FILES['transmittal_file']['tmp_name'][$i], 'r'),
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


                                    $message = 'Uploaded';
                                    $code = 0;
                                    $upload_id = $last_id;
                                    $image = $image_name;
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

                    }
                } catch (Exception $e) {
                    $db->rollback();
                    http_response_code(501);
                    echo json_encode(array("Failure at " . date("Y-m-d") . " " . date("h:i:sa") . " Error uploading, Please use laptop to upload again."));
                    die();
                }

                // // items to delete
                // for ($i = 0; $i < count($items_array); $i++) {
                //     $query = "DELETE FROM gcp_storage_file
                //         WHERE
                //             `id` = :_id";

                //     // prepare the query
                //     $stmt = $db->prepare($query);

                //     // bind the values
                //     $stmt->bindParam(':_id', $items_array[$i]);

                //     try {
                //         // execute the query, also check if query was successful
                //         if (!$stmt->execute()) {
                //             $arr = $stmt->errorInfo();
                //             error_log($arr[2]);
                //             $db->rollback();
                //             http_response_code(501);
                //             echo json_encode(array("Failure at " . date("Y-m-d") . " " . date("h:i:sa") . " " . $arr[2]));
                //             die();
                //         }
                //     } catch (Exception $e) {
                //         error_log($e->getMessage());
                //         $db->rollback();
                //         http_response_code(501);
                //         echo json_encode(array("Failure at " . date("Y-m-d") . " " . date("h:i:sa") . " " . $e->getMessage()));
                //         die();
                //     }
                // }

                // insert into inventory_modify_history
                $rec = getHistoryRecord($db, $items);

                $items_a = json_decode($items, true);

                for($i = 0; $i < count($items_a); $i++)
                {
                    $item_id = $items_a[$i]['id'];
                    $version = 0;
                    $barcode = $items_a[$i]['barcode'];
                    $item_str = json_encode($items_a[$i]);

                    // if exists in $rec
                    $vs = null;
                    foreach($rec as $r)
                    {
                        if($r['id'] == $item_id)
                        {
                            $vs = $r['version'];
                            break;
                        }
                    }

                    if($vs != null)
                    {
                        $version = $vs;
                    }

                    if($vs == null)
                    {
                            $query = "INSERT INTO inventory_modify_history
                            SET
                                request_id = :id,
                                reason = '',
                                item_id = :item_id,
                                barcode = :barcode,
                                listing = :listing,
                                receive_id = 0,
                                which_pool = '',
                                as_sample = '',
                                `location` = 0,
                                project_id = 0,
                                `version` = 0,
                                create_id = 0,
                                created_at = now()";
                            // prepare the query
                            $stmt = $db->prepare($query);
                            // bind the values
                            $stmt->bindParam(':id', $id);
                            $stmt->bindParam(':item_id', $item_id);
                            $stmt->bindParam(':listing', $item_str);
                            $stmt->bindParam(':barcode', $barcode);
                            $stmt->execute();
                    }
                    else
                    {
                            
                        $version += 1;

                        $query = "INSERT INTO inventory_modify_history
                            SET
                                request_id = :request_id,
                                reason = :reason,
                                item_id = :item_id,
                                barcode = :barcode,
                                listing = :listing,
                                receive_id = :receiver,
                                which_pool = :which_pool,
                                as_sample = :as_sample,
                                `location` = :location,
                                project_id = :related_project,
                                `version` = :version,
                                create_id = :create_id,
                                created_at = now()";

                            if($related_project == "")
                            {
                                $related_project = 0;
                            }

                            if($receiver == "")
                            {
                                $receiver = 0;
                            }
                                
                            // prepare the query
                            $stmt = $db->prepare($query);
                            // bind the values
                            $stmt->bindParam(':request_id', $id);
                            $stmt->bindParam(':reason', $reason);
                            $stmt->bindParam(':item_id', $item_id);
                            $stmt->bindParam(':barcode', $barcode);
                            $stmt->bindParam(':listing', $item_str);
                            $stmt->bindParam(':receiver', $receiver);
                            $stmt->bindParam(':which_pool', $which_pool);
                            $stmt->bindParam(':as_sample', $as_sample);
                            $stmt->bindParam(':location', $location);
                            $stmt->bindParam(':related_project', $related_project);
                            $stmt->bindParam(':version', $version);
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
                    }

                    //$items_a[$i]['version'] = $version;
                }
                

                // update products qty
                $items_array = json_decode($items, true);
                $product_items_array = array();

                $v1 = "";
                $v2 = "";
                $v3 = "";
                $v4 = "";

                // split items_array with product id
                for($i = 0; $i < count($items_array); $i++)
                {
                    $array_to_insert = $items_array[$i];

                    $v1 = $items_array[$i]['v1'];
                    $v2 = $items_array[$i]['v2'];
                    $v3 = $items_array[$i]['v3'];
                    $v4 = $items_array[$i]['v4'];

                    $product_id = $items_array[$i]['product_id'];

                    $pkey = $product_id . "_" . $v1 . "_" . $v2 . "_" . $v3 . "_" . $v4;
                    
                    if(!array_key_exists($pkey, $product_items_array))
                    {
                        $product_items_array[$pkey] = array();
                    }

                    array_push($product_items_array[$pkey], $array_to_insert);
                }

                $product_id = 0;

                $project_qty = 0;
                $project_s_qty = 0;
                $stock_qty = 0;
                $stock_s_qty = 0;

                $v1 = "";
                $v2 = "";
                $v3 = "";
                $v4 = "";

                for($j = 0; $j < count($product_items_array); $j++)
                {
                    $product_key = array_keys($product_items_array)[$j];

                    $product_id = explode("_", $product_key)[0];
                    $v1 = explode("_", $product_key)[1];
                    $v2 = explode("_", $product_key)[2];
                    $v3 = explode("_", $product_key)[3];
                    $v4 = explode("_", $product_key)[4];

                    $stock_sql = "";

                    $org_which_pool = "";
                    $org_as_sample = "";
                    $org_product_id = 0;

                    $items_array = $product_items_array[$product_key];

                    $tracking_code = array();

                    $stock_qty = 0;
                    $stock_s_qty = 0;
                    $project_qty = 0;
                    $project_s_qty = 0;

                    for($i = 0; $i < count($items_array); $i++)
                    {
                        $stock_sql = "";

                        $v1 = $items_array[$i]['v1'];
                        $v2 = $items_array[$i]['v2'];
                        $v3 = $items_array[$i]['v3'];
                        $v4 = $items_array[$i]['v4'];

                        $org_which_pool = $items_array[$i]['which_pool'];
                        $org_as_sample = $items_array[$i]['as_sample'];
                        $org_product_id = $items_array[$i]['product_id'];

                        $tracking_code[] = $items_array[$i]['barcode'];

                        if($reason == "Deliver Item(s) to Client")
                        {
                            $which_pool = $items_array[$i]['which_pool'];
                            $as_sample = $items_array[$i]['as_sample'];
                        }
                        
                        if($reason == "Return Item(s) from Client to Inventory System")
                        {
                            $which_pool = $items_array[$i]['which_pool'];
                            $as_sample = $items_array[$i]['as_sample'];
                        }

                        if($reason == "Void Tracking Code of Item(s)")
                        {
                            $which_pool = $items_array[$i]['which_pool'];
                            $as_sample = $items_array[$i]['as_sample'];
                        }

                        if($reason == "Item(s) Lost")
                        {
                            $which_pool = $items_array[$i]['which_pool'];
                            $as_sample = $items_array[$i]['as_sample'];
                        }

                        if($reason == "Item(s) Scrapped")
                        {
                            $which_pool = $items_array[$i]['which_pool'];
                            $as_sample = $items_array[$i]['as_sample'];
                        }


                        if($which_pool == 'Project Pool')
                        {
                            if($as_sample == "Yes")
                            {
                                if($reason == "Deliver Item(s) to Client")
                                {
                                    $stock_sql = "project_s_qty = project_s_qty - 1";
                                    $project_s_qty--;
                                }
                                
                                if($reason == "Return Item(s) from Client to Inventory System")
                                {
                                    $stock_sql = "project_s_qty = project_s_qty + 1";
                                    $project_s_qty++;
                                }

                                if($reason == "Void Tracking Code of Item(s)")
                                {
                                    $stock_sql = "project_s_qty = project_s_qty - 1";
                                    $project_s_qty--;
                                }

                                if($reason == "Item(s) Lost")
                                {
                                    $stock_sql = "project_s_qty = project_s_qty - 1";
                                    $project_s_qty--;
                                }

                                if($reason == "Item(s) Scrapped")
                                {
                                    $stock_sql = "project_s_qty = project_s_qty - 1";
                                    $project_s_qty--;
                                }
                            }
                            
                            if($as_sample == "No")
                            {
                                if($reason == "Deliver Item(s) to Client")
                                {
                                    $stock_sql = "project_qty = project_qty - 1";
                                    $project_qty--;
                                }
                                
                                if($reason == "Return Item(s) from Client to Inventory System")
                                {
                                    $stock_sql = "project_qty = project_qty + 1";
                                    $project_qty++;
                                }

                                if($reason == "Void Tracking Code of Item(s)")
                                {
                                    $stock_sql = "project_qty = project_qty - 1";
                                    $project_qty--;
                                }

                                if($reason == "Item(s) Lost")
                                {
                                    $stock_sql = "project_qty = project_qty - 1";
                                    $project_qty--;
                                }

                                if($reason == "Item(s) Scrapped")
                                {
                                    $stock_sql = "project_qty = project_qty - 1";
                                    $project_qty--;
                                }
                            }
                        }
                        
                        if($which_pool == 'Stock Pool')
                        {
                            if($as_sample == "Yes")
                            {
                                if($reason == "Deliver Item(s) to Client")
                                {
                                    $stock_sql = "stock_s_qty = stock_s_qty - 1";
                                    $stock_s_qty--;
                                }
                                
                                if($reason == "Return Item(s) from Client to Inventory System")
                                {
                                    $stock_sql = "stock_s_qty = stock_s_qty + 1";
                                    $stock_s_qty++;
                                }

                                if($reason == "Void Tracking Code of Item(s)")
                                {
                                    $stock_sql = "stock_s_qty = stock_s_qty - 1";
                                    $stock_s_qty--;
                                }

                                if($reason == "Item(s) Lost")
                                {
                                    $stock_sql = "stock_s_qty = stock_s_qty - 1";
                                    $stock_s_qty--;
                                }

                                if($reason == "Item(s) Scrapped")
                                {
                                    $stock_sql = "stock_s_qty = stock_s_qty - 1";
                                    $stock_s_qty--;
                                }
                            }
                            
                            if($as_sample == "No")
                            {
                                if($reason == "Deliver Item(s) to Client")
                                {
                                    $stock_sql = "stock_qty = stock_qty - 1";
                                    $stock_qty--;
                                }
                                
                                if($reason == "Return Item(s) from Client to Inventory System")
                                {
                                    $stock_sql = "stock_qty = stock_qty + 1";
                                    $stock_qty++;
                                }

                                if($reason == "Void Tracking Code of Item(s)")
                                {
                                    $stock_sql = "stock_qty = stock_qty - 1";
                                    $stock_qty--;
                                }

                                if($reason == "Item(s) Lost")
                                {
                                    $stock_sql = "stock_qty = stock_qty - 1";
                                    $stock_qty--;
                                }

                                if($reason == "Item(s) Scrapped")
                                {
                                    $stock_sql = "stock_qty = stock_qty - 1";
                                    $stock_qty--;
                                }
                            }
                        }

                        if($stock_sql != "")
                        {
                            $sql = "update product_category set " . $stock_sql . " where id = :pid "; 
                            $stmt = $db->prepare($sql);
                            $stmt->bindParam(':pid', $product_id);
                            $stmt->execute();
                        }
                        

                        if($reason == "Change Inventory Pool or Related Project of Item(s)")
                        {
                            if($org_which_pool == "Project Pool" && $which_pool == "Stock Pool")
                            {
                                if($org_as_sample == "Yes")
                                {
                                    $stock_sql = "project_s_qty = project_s_qty - 1, stock_s_qty = stock_s_qty + 1";
                                }
                                
                                if($org_as_sample == "No")
                                {
                                    $stock_sql = "project_qty = project_qty - 1, stock_qty = stock_qty + 1";
                                }
                            }

                            if($org_which_pool == "Stock Pool" && $which_pool == "Project Pool")
                            {
                                if($org_as_sample == "Yes")
                                {
                                    $stock_sql = "stock_s_qty = stock_s_qty - 1, project_s_qty = project_s_qty + 1";
                                }
                                
                                if($org_as_sample == "No")
                                {
                                    $stock_sql = "stock_qty = stock_qty - 1, project_qty = project_qty + 1";
                                }
                            }

                            $sql = "update product_category set " . $stock_sql . " where id = :pid ";
                            $stmt = $db->prepare($sql);
                            $stmt->bindParam(':pid', $product_id);
                            $stmt->execute();
                        }

                        if($reason == "Change Sample Status of Item(s)")
                        {
                            if($org_which_pool == "Project Pool")
                            {
                                if($org_as_sample == "Yes" && $as_sample == "No")
                                {
                                    $stock_sql = "project_s_qty = project_s_qty - 1, project_qty = project_qty + 1";
                                }
                                if($org_as_sample == "No" && $as_sample == "Yes")
                                {
                                    $stock_sql = "project_qty = project_qty - 1, project_s_qty = project_s_qty + 1";
                                }
                            }

                            if($org_which_pool == "Stock Pool")
                            {
                                if($org_as_sample == "Yes" && $as_sample == "No")
                                {
                                    $stock_sql = "stock_s_qty = stock_s_qty - 1, stock_qty = stock_qty + 1";
                                }
                                if($org_as_sample == "No" && $as_sample == "Yes")
                                {
                                    $stock_sql = "stock_qty = stock_qty - 1, stock_s_qty = stock_s_qty + 1";
                                }
                            }

                            $sql = "update product_category set " . $stock_sql . " where id = :pid ";
                            $stmt = $db->prepare($sql);
                            $stmt->bindParam(':pid', $product_id);
                            $stmt->execute();
                        }
                        
                    }

                    // insert into inventory_modify_history
                    if($reason == "Deliver Item(s) to Client")
                    {
                        $affected_sign = "Deduct";
                        $reason_str = 'Deliver Item(s) to Client. Status changed to "Deliver Item(s) to Client".';
                    }
                    
                    if($reason == "Return Item(s) from Client to Inventory System")
                    {
                        $affected_sign = "Add";
                        $reason_str = 'Return Item(s) from Client to Inventory System. Status changed to "On Hand".';
                    }

                    if($reason == "Void Tracking Code of Item(s)")
                    {
                        $affected_sign = "Deduct";
                        $reason_str = 'Void Tracking Code of Item(s). Status changed to "Voided".';
                    }

                    if($reason == "Item(s) Lost")
                    {
                        $affected_sign = "Deduct";
                        $reason_str = 'Item(s) Lost. Status changed to "Lost".';
                    }

                    if($reason == "Item(s) Scrapped")
                    {
                        $affected_sign = "Deduct";
                        $reason_str = 'Item(s) Scrapped. Status changed to "Scrapped".';
                    }

                    if($reason == "Change Inventory Pool or Related Project of Item(s)")
                    {
                        $affected_sign = "";
                        $reason_str = 'Change Inventory Pool or Related Project of Item(s)';
                    }

                    if($reason == "Change Location of Item(s)")
                    {
                        $affected_sign = "";
                        $reason_str = 'Change Location of Item(s)';
                    }

                    if($reason == "Change Sample Status of Item(s)")
                    {
                        $affected_sign = "";
                        $reason_str = 'Change Sample Status of Item(s)';
                    }

                    $affected_qty = abs($stock_qty) + abs($stock_s_qty) + abs($project_qty) + abs($project_s_qty);
                    $affected_tracking_code = implode('","', $tracking_code);

                    $affected_tracking_code = '["' . $affected_tracking_code . '"]';

                    if($related_project == "")
                    {
                        $related_project = 0;
                    }
                    
                    $query = "INSERT INTO inventory_change_history
                    SET
                        item_id = :item_id,
                        modify_history_id = :modify_history_id,
                        pid = :pid,
                        v1 = :v1,
                        v2 = :v2,
                        v3 = :v3,
                        v4 = :v4,
                        related_record = :related_record,
                        reason = :reason,
                        new_related_project = :new_related_project,
                        affected_qty = :affected_qty,
                        affected_sign = :affected_sign,
                        affected_tracking = :affected_tracking,
                        create_id = :create_id,
                        created_at = now()";

                    // prepare the query
                    $stmt = $db->prepare($query);
                    // bind the values
                    $stmt->bindParam(':item_id', $id);
                    $stmt->bindParam(':modify_history_id', $last_id);
                    $stmt->bindParam(':pid', $product_id);
                    $stmt->bindParam(':v1', $v1);
                    $stmt->bindParam(':v2', $v2);
                    $stmt->bindParam(':v3', $v3);
                    $stmt->bindParam(':v4', $v4);
                
                    $stmt->bindParam(':reason', $reason);

                    $stmt->bindParam(':related_record', $request_no);
                    $stmt->bindParam(':new_related_project', $related_project);
                    $stmt->bindParam(':affected_qty', $affected_qty);
                    $stmt->bindParam(':affected_sign', $affected_sign);

                    $stmt->bindParam(':affected_tracking', $affected_tracking_code);
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
                }
                

                http_response_code(200);
                echo json_encode(array("message" => "Success at " . date("Y-m-d") . " " . date("h:i:sa")));
            } catch (Exception $e) {
                
                error_log($e->getMessage());
                $db->rollback();
                http_response_code(501);
                echo json_encode(array("Failure at " . date("Y-m-d") . " " . date("h:i:sa") . " " . $e->getMessage()));
                die();
            }

            $db->commit();

            break;
        }
        

function getHistoryRecordByBarcode($db, $barcode)
{
    $query = "SELECT * FROM inventory_modify_history WHERE barcode = :barcode ORDER BY version DESC LIMIT 1";
    $stmt = $db->prepare($query);
    $stmt->bindParam(':barcode', $barcode);
    $stmt->execute();
    $row = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($row) {
        return $row['version'];
    } else {
        return 0;
    }
}


// get the previous recode
function getHistoryRecord($db, $items)
{
    $item_ids = array();
    $items = json_decode($items, true);
    foreach ($items as $item) {
        $item_ids[] = $item['id'];
    }
    $item_id_str = implode(',', $item_ids);
    $query = "SELECT * FROM inventory_modify_history WHERE item_id in (" . $item_id_str . ") order by item_id, version desc";
    $stmt = $db->prepare($query);

    $stmt->execute();
    $row = $stmt->fetch(PDO::FETCH_ASSOC);
    
    // if no record found, insert one and return false
    $items_with_largest_version = array();

    if ($row) {
        // get each item id with largest version
        while ($row) {
            $item_id = $row['item_id'];
            if (!isset($items_with_largest_version[$item_id]) || $row['version'] > $items_with_largest_version[$item_id]['version']) {
                $items_with_largest_version[$item_id] = array(
                    'id' => $item_id,
                    'version' => $row['version']
                );
            }
            $row = $stmt->fetch(PDO::FETCH_ASSOC);
        }

        return $items_with_largest_version;
    } else {

        // for($i = 0; $i < count($items); $i++)
        // {
        //     $item_id = $items[$i]['id'];

        //     $query = "INSERT INTO inventory_modify_history
        //     SET
        //         request_id = :id,
        //         reason = '',
        //         item_id = :item_id,
        //         receive_id = 0,
        //         which_pool = '',
        //         as_sample = '',
        //         `location` = 0,
        //         project_id = 0,
        //         `version` = 0,
        //         create_id = 0,
        //         created_at = now()";
        //     // prepare the query
        //     $stmt = $db->prepare($query);
        //     // bind the values
        //     $stmt->bindParam(':id', $id);
        //     $stmt->bindParam(':item_id', $item_id);
        //     $stmt->execute();
        // }
        

        return $items_with_largest_version;
    }
}

function getProjectName($db, $id)
{
    $project_name = "";
    $query = "SELECT project_name FROM project_main WHERE id = :id";
    $stmt = $db->prepare($query);
    $stmt->bindParam(':id', $id);
    $stmt->execute();
    $row = $stmt->fetch(PDO::FETCH_ASSOC);
    if ($row) {
        $project_name = $row['project_name'];
    }
    return $project_name;
}

        ?>