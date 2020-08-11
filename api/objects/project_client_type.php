<?php
// 'user' object
class ProjectClientType{
 
    // database connection and table name
    private $conn;
    private $table_name = "project_client_type";
 
    // object properties
    public $id;
    public $client_type;
    public $class_name;
    public $status;
 
 
    // constructor
    public function __construct($db){
        $this->conn = $db;
    }
 
    // create new user record
    function create(){
    
        // insert query
        $query = "INSERT INTO " . $this->table_name . "
                SET
                    client_type = :client_type,
                    class_name = :class_name
                  ";
    
        // prepare the query
        $stmt = $this->conn->prepare($query);
    
        // sanitize
        $this->client_type=htmlspecialchars(strip_tags($this->client_type));
     
        // bind the values
        $stmt->bindParam(':client_type', $this->client_type);
        $stmt->bindParam(':class_name', $this->class_name);

    
        // execute the query, also check if query was successful
        if($stmt->execute()){
            return true;
        }
        else
        {
            $arr = $stmt->errorInfo();
            error_log($arr[2]);
        }
    
        return false;
    }


    // update a user record
    public function delete(){

        $query = "UPDATE " . $this->table_name . "
                SET
                    status = :status
                WHERE id = :id";

        // prepare the query
        $stmt = $this->conn->prepare($query);

        // bind the values from the form
        $stmt->bindParam(':status', $a = -1);

        // unique ID of record to be edited
        $stmt->bindParam(':id', $this->id);

        // execute the query
        if($stmt->execute()){
            return true;
        }

        return false;
    }

    // update a user record
    public function updateStatus(){

        $query = "UPDATE " . $this->table_name . "
                SET
                    client_type = :client_type,
                    class_name = :class_name
                WHERE id = :id";
    
        // prepare the query
        $stmt = $this->conn->prepare($query);
    
        // sanitize
        $this->status=htmlspecialchars(strip_tags($this->client_type));
       
    
        // bind the values from the form
        $stmt->bindParam(':client_type', $this->client_type);
        $stmt->bindParam(':class_name', $this->class_name);

        // unique ID of record to be edited
        $stmt->bindParam(':id', $this->id);
    
        // execute the query
        if($stmt->execute()){
            return true;
        }
    
        return false;
    }

 
}