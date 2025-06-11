<?php

use Google\Service\BigtableAdmin\Split;

include_once 'config/core.php';
include_once 'config/conf.php';
require_once '../vendor/autoload.php';
include_once 'config/database.php';

$database = new Database();
$db = $database->getConnection();

$sql = "truncate table product_category_tags_index";
$stmt = $db->prepare( $sql );
$stmt->execute();

$sql = "SELECT id, tags, attributes, variation_mode FROM product_category where `status` <> -1";

$stmt = $db->prepare( $sql );
$stmt->execute();

while($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
    $id = $row['id'];
    $tags = explode(',', $row['tags']);
    $attributes = json_decode($row['attributes'], true);
    $variation_mode = $row['variation_mode'];

    $sql = "insert into product_category_tags_index (pid, `type`, `key`, `value`) values (:product_category_id, 0, :tag, '')";
    $stmt2 = $db->prepare( $sql );

    foreach ($tags as $tag) {
        $stmt2->bindParam(':product_category_id', $id);
        $stmt2->bindParam(':tag', $tag);
        $stmt2->execute();

        if($stmt2->errorInfo()[0] != "00000") {
            echo $stmt2->errorInfo()[2];
        }
    }

    foreach ($attributes as $att) {
        $key = $att['category'];
        $value = $att['value'];
        $watt = 0;
        if($value != "") {
            if($key == 'Wattage')
            {
                if (preg_match_all('/\b(\d+(\.\d+)?)\s*W?\b/i', $value, $matches)) {
                    $watt = max($matches[1]); // 取最大數值，確保獲取主要的功率數值
                }
            }

            $sql = "insert into product_category_tags_index (pid, `type`, `key`, `value`, `watt`) values (:product_category_id, 1, :key, :value, :watt)";
            $stmt2 = $db->prepare( $sql );
            $stmt2->bindParam(':product_category_id', $id);
            $stmt2->bindParam(':key', $key);
            $stmt2->bindParam(':value', $value);
            $stmt2->bindParam(':watt', $watt);
            $stmt2->execute();

            if($stmt2->errorInfo()[0] != "00000") {
                echo $stmt2->errorInfo()[2];
            }
        }
    }
}

