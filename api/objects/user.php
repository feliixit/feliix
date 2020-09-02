<?php
// 'user' object
class User{
 
    // database connection and table name
    private $conn;
    private $table_name = "user";
 
    // object properties
    public $id;
    public $username;
    public $email;
    public $password;
    public $status;
    public $is_admin;
    public $pic_url;
    public $need_punch;
    public $apartment_id;
    public $title_id;
    public $department;
    public $position;
    public $head_of_department;
    public $annual_leave;
    public $sick_leave;
    public $manager_leave;
    public $is_manager;
    public $test_manger;
    public $is_viewer;
 
    // constructor
    public function __construct($db){
        $this->conn = $db;
    }
 
    // create new user record
    function create(){
    
        // insert query
        $query = "INSERT INTO " . $this->table_name . "
                SET
                    username = :username,
                    email = :email,
                    password = :password,
                    status = :status,
                    need_punch = :need_punch,
                    head_of_department = :head_of_department,
                    annual_leave = :annual_leave,
                    sick_leave = :sick_leave,
                    manager_leave = :manager_leave,
                    is_manager = :is_manager,
                    test_manager = :test_manager,
                    apartment_id = :apartment_id,
                    title_id = :title_id,
                    pic_url = :pic_url,
                    is_admin = :is_admin,
                    is_viewer = :is_viewer";
    
        // prepare the query
        $stmt = $this->conn->prepare($query);
    
        // sanitize
        $this->username=htmlspecialchars(strip_tags($this->username));
        $this->email=htmlspecialchars(strip_tags($this->email));
        $this->password=htmlspecialchars(strip_tags($this->password));
        $this->status = ($this->status ? $this->status : 0);
        $this->need_punch = ($this->need_punch ? $this->need_punch : 0);

        $this->head_of_department = ($this->head_of_department ? $this->head_of_department : 0);
        $this->annual_leave = ($this->annual_leave ? $this->annual_leave : 0);
        $this->sick_leave = ($this->sick_leave ? $this->sick_leave : 0);
        $this->manager_leave = ($this->manager_leave ? $this->manager_leave : 0);
        $this->is_manager = ($this->is_manager ? $this->is_manager : 0);

        $this->is_admin = ($this->is_admin ? $this->is_admin : '0');
        $this->apartment_id = ($this->apartment_id ? $this->apartment_id : '0');
        $this->title_id = ($this->title_id ? $this->title_id : '0');
        $this->is_viewer = ($this->is_viewer ? $this->is_viewer : '0');
    
        // bind the values
        $stmt->bindParam(':username', $this->username);
        $stmt->bindParam(':email', $this->email);
        $stmt->bindParam(':status', $this->status);
        $stmt->bindParam(':pic_url', $this->pic_url);
        $stmt->bindParam(':is_admin', $this->is_admin);
        $stmt->bindParam(':need_punch', $this->need_punch);

        $stmt->bindParam(':head_of_department', $this->head_of_department);
        $stmt->bindParam(':annual_leave', $this->annual_leave);
        $stmt->bindParam(':sick_leave', $this->sick_leave);
        $stmt->bindParam(':manager_leave', $this->manager_leave);
        $stmt->bindParam(':is_manager', $this->is_manager);
        $stmt->bindParam(':test_manager', $this->test_manager);

        $stmt->bindParam(':apartment_id', $this->apartment_id);
        $stmt->bindParam(':title_id', $this->title_id);
        $stmt->bindParam(':is_viewer', $this->is_viewer);
    
        // hash the password before saving to database
        $password_hash = password_hash($this->password, PASSWORD_BCRYPT);
        $stmt->bindParam(':password', $password_hash);
    
        // execute the query, also check if query was successful
        if($stmt->execute()){
            return true;
        }
    
        return false;
    }

    // check if given email exist in the database
    function userExists(){
    
        // query to check if email exists
        $query = "SELECT id, username, password
                FROM " . $this->table_name . "
                WHERE username = ?
                LIMIT 0,1";
    
        // prepare the query
        $stmt = $this->conn->prepare( $query );
    
        // sanitize
        $this->username=htmlspecialchars(strip_tags($this->username));
    
        // bind given email value
        $stmt->bindParam(1, $this->username);
    
        // execute the query
        $stmt->execute();
    
        // get number of rows
        $num = $stmt->rowCount();
    
        // if email exists, assign values to object properties for easy access and use for php sessions
        if($num>0){
    
            // get record details / values
            $row = $stmt->fetch(PDO::FETCH_ASSOC);
    
            // assign values to object properties
            $this->id = $row['id'];
            $this->username = $row['username'];
            $this->password = $row['password'];
            $this->status = $row['status'];
    
            // return true because email exists in the database
            return true;
        }
    
        // return false if email does not exist in the database
        return false;
    }

    function userCanLogin(){
        // query to check if email exists
        $query = "SELECT user.id, username, password, user.status, is_admin, need_punch, COALESCE(department, '') department, 
                apartment_id, title_id, COALESCE(title, '') title, annual_leave, sick_leave, COALESCE(is_manager, 0) is_manager, COALESCE(test_manager, '0') test_manager, manager_leave, user_title.head_of_department,user.is_viewer
                FROM " . $this->table_name . "
                LEFT JOIN user_department ON user.apartment_id = user_department.id 
                LEFT JOIN user_title ON user.title_id = user_title.id
                WHERE email = ? 
                LIMIT 0,1";
    
        // prepare the query
        $stmt = $this->conn->prepare( $query );
    
        // sanitize
        $this->email=htmlspecialchars(strip_tags($this->email));
    
        // bind given email value
        $stmt->bindParam(1, $this->email);
    
        // execute the query
        $stmt->execute();
    
        // get number of rows
        $num = $stmt->rowCount();
    
        // if email exists, assign values to object properties for easy access and use for php sessions
        if($num>0){
    
            // get record details / values
            $row = $stmt->fetch(PDO::FETCH_ASSOC);
    
            // assign values to object properties
            $this->id = $row['id'];
            $this->username = $row['username'];
            $this->password = $row['password'];
            $this->status = $row['status'];
            $this->is_admin = $row['is_admin'];
            $this->department = $row['department'];
            $this->position = $row['title'];
            $this->head_of_department = $row['head_of_department'];
            $this->sick_leave = $row['sick_leave'];
            $this->manager_leave = $row['manager_leave'];
            $this->annual_leave = $row['annual_leave'];
            $this->title_id = $row['title_id'];
            $this->apartment_id = $row['apartment_id'];
            $this->is_manager = $row['is_manager'];
            $this->test_manager = $row['test_manager'];
            $this->is_viewer = $row['is_viewer'];
            // return true because email exists in the database
            return true;
        }
    
        // return false if email does not exist in the database
        return false;
    }
    
    // check if given email exist in the database
    function emailExists(){
    
        // query to check if email exists
        $query = "SELECT id, username, password
                FROM " . $this->table_name . "
                WHERE email = ?
                LIMIT 0,1";
    
        // prepare the query
        $stmt = $this->conn->prepare( $query );
    
        // sanitize
        $this->email=htmlspecialchars(strip_tags($this->email));
    
        // bind given email value
        $stmt->bindParam(1, $this->email);
    
        // execute the query
        $stmt->execute();
    
        // get number of rows
        $num = $stmt->rowCount();
    
        // if email exists, assign values to object properties for easy access and use for php sessions
        if($num>0){
    
            // get record details / values
            $row = $stmt->fetch(PDO::FETCH_ASSOC);
    
            // assign values to object properties
            $this->id = $row['id'];
            $this->username = $row['username'];
            $this->password = $row['password'];
    
            // return true because email exists in the database
            return true;
        }
    
        // return false if email does not exist in the database
        return false;
    }

    // update a user record
    public function delete(){

        $query = "delete from " . $this->table_name . "
                
                WHERE id = :id";

        // prepare the query
        $stmt = $this->conn->prepare($query);

        // bind the values from the form
        //$stmt->bindParam(':status', $a = -1);

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
                    username = :username,
                    email = :email,
                    need_punch = :need_punch,

                    head_of_department = :head_of_department,
                    annual_leave = :annual_leave,
                    is_manager = :is_manager,
                    test_manager = :test_manager,
                    sick_leave = :sick_leave,
                    manager_leave = :manager_leave,

                    apartment_id = :apartment_id,
                    title_id = :title_id,
                    status = :status,
                    is_admin = :is_admin,
                    is_viewer = :is_viewer
                WHERE id = :id";
    
        // prepare the query
        $stmt = $this->conn->prepare($query);
    
        // sanitize
        $this->status=htmlspecialchars(strip_tags($this->status));
        $this->is_admin=htmlspecialchars(strip_tags($this->is_admin));
    
        // bind the values from the form
        $stmt->bindParam(':status', $this->status);
        $stmt->bindParam(':is_admin', $this->is_admin);
        $stmt->bindParam(':username', $this->username);
        $stmt->bindParam(':email', $this->email);
        $stmt->bindParam(':need_punch', $this->need_punch);

        $stmt->bindParam(':head_of_department', $this->head_of_department);
        $stmt->bindParam(':annual_leave', $this->annual_leave);
        $stmt->bindParam(':is_manager', $this->is_manager);
        $stmt->bindParam(':test_manager', $this->test_manager);
        $stmt->bindParam(':sick_leave', $this->sick_leave);

        $stmt->bindParam(':manager_leave', $this->manager_leave);
        
        $stmt->bindParam(':apartment_id', $this->apartment_id);
        $stmt->bindParam(':title_id', $this->title_id);
        $stmt->bindParam(':is_viewer', $this->is_viewer);
        // unique ID of record to be edited
        $stmt->bindParam(':id', $this->id);
    
        // execute the query
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
    public function update(){
    
        // if password needs to be updated
        $password_set=!empty($this->password) ? ", password = :password" : "";
    
        // if no posted password, do not update the password
        $query = "UPDATE " . $this->table_name . "
                SET
                    username = :username,
                    email = :email
                    {$password_set}
                WHERE id = :id";
    
        // prepare the query
        $stmt = $this->conn->prepare($query);
    
        // sanitize
        $this->username=htmlspecialchars(strip_tags($this->username));
        $this->email=htmlspecialchars(strip_tags($this->email));
    
        // bind the values from the form
        $stmt->bindParam(':username', $this->username);
        $stmt->bindParam(':email', $this->email);
    
        // hash the password before saving to database
        if(!empty($this->password)){
            $this->password=htmlspecialchars(strip_tags($this->password));
            $password_hash = password_hash($this->password, PASSWORD_BCRYPT);
            $stmt->bindParam(':password', $password_hash);
        }
    
        // unique ID of record to be edited
        $stmt->bindParam(':id', $this->id);
    
        // execute the query
        if($stmt->execute()){
            return true;
        }
    
        return false;
    }
 
}