<?php

include_once 'config/database.php';

function IsCreator($pid, $user_id)
{
    $database = new Database();
    $db = $database->getConnection();
    
    $merged_results = "0";

    $query = "SELECT 1 FROM project_main pm where pm.id = " . $pid . " and pm.create_id = " . $user_id;

    $stmt = $db->prepare( $query );
    $stmt->execute();

    while($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        $merged_results = "1";
    }
    return $merged_results;
}

