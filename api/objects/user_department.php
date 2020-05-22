<?php
// 'user' object
class UseDepartment{
 
    // database connection and table name
    private $conn;
    private $table_name = "user_department";
 
    // object properties
    public $id;
    public $department;
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
                    department = :department
                  ";
    
        // prepare the query
        $stmt = $this->conn->prepare($query);
    
        // sanitize
        $this->department=htmlspecialchars(strip_tags($this->department));
     
        // bind the values
        $stmt->bindParam(':department', $this->department);
     

    
        // execute the query, also check if query was successful
        if($stmt->execute()){
            return true;
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
                    department = :department
                WHERE id = :id";
    
        // prepare the query
        $stmt = $this->conn->prepare($query);
    
        // sanitize
        $this->status=htmlspecialchars(strip_tags($this->department));
       
    
        // bind the values from the form
        $stmt->bindParam(':department', $this->department);
       

        // unique ID of record to be edited
        $stmt->bindParam(':id', $this->id);
    
        // execute the query
        if($stmt->execute()){
            return true;
        }
    
        return false;
    }

 
}