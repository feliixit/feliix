<?php
// 'user' object
class ProjectStage{
 
    // database connection and table name
    private $conn;
    private $table_name = "project_stage";
 
    // object properties
    public $id;
    public $stage;
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
                    stage = :stage
                  ";
    
        // prepare the query
        $stmt = $this->conn->prepare($query);
    
        // sanitize
        $this->stage=htmlspecialchars(strip_tags($this->stage));
     
        // bind the values
        $stmt->bindParam(':stage', $this->stage);
     

    
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
                    stage = :stage
                WHERE id = :id";
    
        // prepare the query
        $stmt = $this->conn->prepare($query);
    
        // sanitize
        $this->status=htmlspecialchars(strip_tags($this->stage));
       
    
        // bind the values from the form
        $stmt->bindParam(':stage', $this->stage);
       

        // unique ID of record to be edited
        $stmt->bindParam(':id', $this->id);
    
        // execute the query
        if($stmt->execute()){
            return true;
        }
    
        return false;
    }

 
}