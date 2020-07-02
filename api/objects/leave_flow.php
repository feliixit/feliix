<?php
// 'user' object
class UseTitle{
 
    // database connection and table name
    private $conn;
    private $table_name = "leave_flow";
 
    // object properties
    public $id;
    public $uid;
    public $apartment_id;
    public $flow;
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
                    uid = :uid,
                    flow = :flow
                    apartment_id = :apartment_id
                  ";
    
        // prepare the query
        $stmt = $this->conn->prepare($query);
    
        // sanitize
        //$this->apartment_id=htmlspecialchars(strip_tags($this->apartment_id));
        //$this->title=htmlspecialchars(strip_tags($this->title));
     
        // bind the values
        $stmt->bindParam(':apartment_id', $this->apartment_id);
        $stmt->bindParam(':uid', $this->uid);
        $stmt->bindParam(':flow', $this->flow);

    
        // execute the query, also check if query was successful
        if($stmt->execute()){
            return true;
        }
    
        return false;
    }


    // update a user record
    public function delete(){

        $query = "DELETE FROM " . $this->table_name . "
                
                WHERE id = :id";

        // prepare the query
        $stmt = $this->conn->prepare($query);

        // bind the values from the form
        // unique ID of record to be edited
        $stmt->bindParam(':id', $this->id);

        // execute the query
        if($stmt->execute()){
            return true;
        }

        return false;
    }

 
}