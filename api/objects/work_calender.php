<?php
class WorkCalenderMain
{
    // database connection and table name
    private $conn;
    private $table_name = "work_calendar_main";

    // object properties
    public $id;
    public $title;
    public $start_time;
    public $end_time;
    public $color;
    public $text_color;
    public $project;
    public $sales_executive;
    public $project_in_charge;
    public $installer_needed;
    public $installer_needed_location;
    public $things_to_bring;
    public $things_to_bring_location;
    public $products_to_bring;
    public $products_to_bring_files;
    public $service;
    public $driver;
    public $back_up_driver;
    public $photoshoot_request;
    public $notes;
    public $is_enabled;
    public $created_at;
    public $updated_at;
    public $deleted_at;
    public $created_by;
    public $updated_by;
    public $deleted_by;
    // constructor
    public function __construct($db)
    {
        $this->conn = $db;
    }

    function update()
    {
        $query = "UPDATE " . $this->table_name . "
                set title = :title, start_time = :start_time, 
                end_time = :end_time, color = :color, 
                text_color = :text_color, project = :project , sales_executive = :sales_executive, 
                project_in_charge = :project_in_charge, installer_needed = :installer_needed, installer_needed_location = :installer_needed_location, 
                things_to_bring = :things_to_bring, things_to_bring_location = :things_to_bring_location, 
                products_to_bring = :products_to_bring, products_to_bring_files = :products_to_bring_files,
                service = :service, driver = :driver, back_up_driver = :back_up_driver,
                photoshoot_request = :photoshoot_request, notes = :notes, 
                updated_at = now(), updated_by = :updated_by where id = :id";

        // prepare the query
        $stmt = $this->conn->prepare($query);

        // bind the values
        $this->id = (int)$this->id;
        $this->title = htmlspecialchars(strip_tags($this->title));
        $this->start_time = htmlspecialchars(strip_tags($this->start_time));
        $this->end_time = htmlspecialchars(strip_tags($this->end_time));
        $this->color = htmlspecialchars(strip_tags($this->color));

        $this->text_color = htmlspecialchars(strip_tags($this->text_color));
        $this->project = htmlspecialchars(strip_tags($this->project));
        $this->sales_executive = htmlspecialchars(strip_tags($this->sales_executive));
        $this->project_in_charge = htmlspecialchars(strip_tags($this->project_in_charge));
        
        $this->installer_needed = htmlspecialchars(strip_tags($this->installer_needed));
        $this->installer_needed_location = htmlspecialchars(strip_tags($this->installer_needed_location));
        $this->things_to_bring = htmlspecialchars(strip_tags($this->things_to_bring));
        $this->things_to_bring_location = htmlspecialchars(strip_tags($this->things_to_bring_location));
        $this->products_to_bring = htmlspecialchars(strip_tags($this->products_to_bring));
        $this->products_to_bring_files = htmlspecialchars(strip_tags($this->products_to_bring_files));
        
        $this->service = htmlspecialchars(strip_tags($this->service));
        $this->driver = htmlspecialchars(strip_tags($this->driver));
        $this->back_up_driver = htmlspecialchars(strip_tags($this->back_up_driver));
        $photoshoot = filter_var($this->photoshoot_request,FILTER_VALIDATE_INT);
        $this->notes = htmlspecialchars(strip_tags($this->notes));
        $this->updated_by = htmlspecialchars(strip_tags($this->updated_by));
        

        // bind the values
        $stmt->bindParam(':id', $this->id);
        $stmt->bindParam(':title', $this->title);
        $stmt->bindParam(':start_time', $this->start_time);
        $stmt->bindParam(':end_time', $this->end_time);
        $stmt->bindParam(':color', $this->color);

        $stmt->bindParam(':text_color', $this->text_color);
        $stmt->bindParam(':project', $this->project);
        $stmt->bindParam(':sales_executive', $this->sales_executive);
        $stmt->bindParam(':project_in_charge', $this->project_in_charge);

        $stmt->bindParam(':installer_needed', $this->installer_needed);
        $stmt->bindParam(':installer_needed_location', $this->installer_needed_location);
        $stmt->bindParam(':things_to_bring', $this->things_to_bring);
        $stmt->bindParam(':things_to_bring_location', $this->things_to_bring_location);
        $stmt->bindParam(':products_to_bring', $this->products_to_bring);
        $stmt->bindParam(':products_to_bring_files', $this->products_to_bring_files);
        $stmt->bindParam(':service', $this->service);
        
        $stmt->bindParam(':driver', $this->driver);
        $stmt->bindParam(':back_up_driver', $this->back_up_driver);
        
        $stmt->bindParam(':photoshoot_request',  $photoshoot);
        $stmt->bindParam(':notes', $this->notes);
        $stmt->bindParam(':updated_by', $this->updated_by);

    try {
        // execute the query, also check if query was successful
            if ($stmt->execute()) {
                return true;
            }
            else
            {
                $arr = $stmt->errorInfo();
                error_log($arr[2]);
                return false;
            }
        }
        catch (Exception $e)
        {
            error_log($e->getMessage());
            return false;
        }
    }

    // create new price record
    function create()
    {

        $last_id = 0;
        // insert query
        $query = "INSERT INTO " . $this->table_name . "
                (`title`,`start_time`, `end_time`, `color`, `text_color`, `project`, `sales_executive`, `project_in_charge`, `installer_needed`, `installer_needed_location`, `things_to_bring`,`things_to_bring_location`,`products_to_bring`,`products_to_bring_files`,`service`,`driver`,`back_up_driver`,`photoshoot_request`,`notes`,`is_enabled`,`created_at`,`created_by`) 
                VALUES (:title,:start_time, :end_time, :color, :text_color, :project, :sales_executive, :project_in_charge, :installer_needed, :installer_needed_location, :things_to_bring, :things_to_bring_location, :products_to_bring, :products_to_bring_files, :service, :driver, :back_up_driver, :photoshoot_request, :notes, 1, now(),:created_by)";

        // prepare the query
        $stmt = $this->conn->prepare($query);

            // sanitize

            $this->title = htmlspecialchars(strip_tags($this->title));
            $this->start_time = htmlspecialchars(strip_tags($this->start_time));
            $this->end_time = htmlspecialchars(strip_tags($this->end_time));
            $this->color = htmlspecialchars(strip_tags($this->color));
    
            $this->text_color = htmlspecialchars(strip_tags($this->text_color));
            $this->project = htmlspecialchars(strip_tags($this->project));
            $this->sales_executive = htmlspecialchars(strip_tags($this->sales_executive));
            $this->project_in_charge = htmlspecialchars(strip_tags($this->project_in_charge));
            
            $this->installer_needed = htmlspecialchars(strip_tags($this->installer_needed));
            $this->installer_needed_location = htmlspecialchars(strip_tags($this->installer_needed_location));
            $this->things_to_bring = htmlspecialchars(strip_tags($this->things_to_bring));
            $this->things_to_bring_location = htmlspecialchars(strip_tags($this->things_to_bring_location));
            $this->products_to_bring = htmlspecialchars(strip_tags($this->products_to_bring));
            $this->products_to_bring_files = htmlspecialchars(strip_tags($this->products_to_bring_files));
            
            $this->service = htmlspecialchars(strip_tags($this->service));
            $this->driver = htmlspecialchars(strip_tags($this->driver));
            $this->back_up_driver = htmlspecialchars(strip_tags($this->back_up_driver));
            $photoshoot = filter_var($this->photoshoot_request,FILTER_VALIDATE_INT);
            $this->notes = htmlspecialchars(strip_tags($this->notes));
            $this->created_by = htmlspecialchars(strip_tags($this->created_by));

           
            // bind the values
            $stmt->bindParam(':title', $this->title);
            $stmt->bindParam(':start_time', $this->start_time);
            $stmt->bindParam(':end_time', $this->end_time);
            $stmt->bindParam(':color', $this->color);
    
            $stmt->bindParam(':text_color', $this->text_color);
            $stmt->bindParam(':project', $this->project);
            $stmt->bindParam(':sales_executive', $this->sales_executive);
            $stmt->bindParam(':project_in_charge', $this->project_in_charge);
    
            $stmt->bindParam(':installer_needed', $this->installer_needed);
            $stmt->bindParam(':installer_needed_location', $this->installer_needed_location);
            $stmt->bindParam(':things_to_bring', $this->things_to_bring);
            $stmt->bindParam(':things_to_bring_location', $this->things_to_bring_location);
            $stmt->bindParam(':products_to_bring', $this->products_to_bring);
            $stmt->bindParam(':products_to_bring_files', $this->products_to_bring_files);
            $stmt->bindParam(':service', $this->service);
            
            $stmt->bindParam(':driver', $this->driver);
            $stmt->bindParam(':back_up_driver', $this->back_up_driver);
            
            $stmt->bindParam(':photoshoot_request',  $photoshoot);
            $stmt->bindParam(':notes', $this->notes);
            $stmt->bindParam(':created_by', $this->created_by);
            

        try {
            // execute the query, also check if query was successful
            if ($stmt->execute()) {
                $last_id = $this->conn->lastInsertId();
            }
            else
            {
                $arr = $stmt->errorInfo();
                error_log($arr[2]);
                return $arr;
            }
        }
        catch (Exception $e)
        {
            error_log($e->getMessage());
        }


        return $last_id;

    }
    function delete()
    {
        $query = "UPDATE " . $this->table_name . "
                set is_enabled = 0, deleted_at = now(), deleted_by = :deleted_by where id = :id";

        // prepare the query
        $stmt = $this->conn->prepare($query);

        // bind the values
        $this->id = (int)$this->id;
        $this->deleted_by = htmlspecialchars(strip_tags($this->deleted_by));



        // bind the values
        $stmt->bindParam(':id', $this->id);

        $stmt->bindParam(':deleted_by', $this->deleted_by);

        try {
            // execute the query, also check if query was successful
            if ($stmt->execute()) {
                return true;
            }
            else
            {
                $arr = $stmt->errorInfo();
                error_log($arr[2]);
                return false;
            }
        }
        catch (Exception $e)
        {
            error_log($e->getMessage());
            return false;
        }
    }
}

class WorkCalenderDetails
{
    // database connection and table name
    private $conn;
    private $table_name = "work_calendar_details";

    // object properties
    public $id;
    public $work_calendar_main_id;
    public $location;
    public $agenda;
    public $appoint_time;
    public $end_time;
    public $is_enabled;
    public $created_at;
    public $updated_at;
    public $deleted_at;
    public $created_by;
    public $updated_by;
    public $deleted_by;
    // constructor
    public function __construct($db)
    {
        $this->conn = $db;
    }

    function update()
    {
        $query = "UPDATE " . $this->table_name . "
                set location = :location, agenda = :agenda, 
                appoint_time = :appoint_time, end_time = :end_time,  
                updated_at = now(), updated_by = :updated_by where id = :id";

        // prepare the query
        $stmt = $this->conn->prepare($query);

        // bind the values
        $this->id = (int)$this->id;
        $this->location = htmlspecialchars(strip_tags($this->location));
        $this->agenda = htmlspecialchars(strip_tags($this->agenda));
        $this->appoint_time = htmlspecialchars(strip_tags($this->appoint_time));
        $this->end_time = htmlspecialchars(strip_tags($this->end_time));
       
        $this->updated_by = htmlspecialchars(strip_tags($this->updated_by));
        

        // bind the values
        $stmt->bindParam(':id', $this->id);
        $stmt->bindParam(':location', $this->location);
        $stmt->bindParam(':agenda', $this->agenda);
        $stmt->bindParam(':appoint_time', $this->appoint_time);
        $stmt->bindParam(':end_time', $this->end_time);
        
        $stmt->bindParam(':updated_by', $this->updated_by);

    try {
        // execute the query, also check if query was successful
            if ($stmt->execute()) {
                return true;
            }
            else
            {
                $arr = $stmt->errorInfo();
                error_log($arr[2]);
                return false;
            }
        }
        catch (Exception $e)
        {
            error_log($e->getMessage());
            return false;
        }
    }

    // create new price record
    function create()
    {

        $last_id = 0;
        // insert query
        $query = "INSERT INTO " . $this->table_name . "
                (`work_calendar_main_id`,`location`, `agenda`, `appoint_time`, `end_time`,`is_enabled`,`created_at`,`created_by`) 
                VALUES (:work_calendar_main_id,:location, :agenda, :appoint_time, :end_time, 1, now(),:created_by)";

        // prepare the query
        $stmt = $this->conn->prepare($query);

            // sanitize

            $this->work_calendar_main_id = htmlspecialchars(strip_tags($this->work_calendar_main_id));
            $this->location = htmlspecialchars(strip_tags($this->location));
            $this->agenda = htmlspecialchars(strip_tags($this->agenda));
            $this->appoint_time = htmlspecialchars(strip_tags($this->appoint_time));
            $this->end_time = htmlspecialchars(strip_tags($this->end_time));
            
            $this->created_by = htmlspecialchars(strip_tags($this->created_by));

           
            // bind the values
            $stmt->bindParam(':work_calendar_main_id', $this->work_calendar_main_id);
            $stmt->bindParam(':location', $this->location);
            $stmt->bindParam(':agenda', $this->agenda);
            $stmt->bindParam(':appoint_time', $this->appoint_time);
            $stmt->bindParam(':end_time', $this->end_time);
            $stmt->bindParam(':created_by', $this->created_by);
            

        try {
            // execute the query, also check if query was successful
            if ($stmt->execute()) {
                $last_id = $this->conn->lastInsertId();
            }
            else
            {
                $arr = $stmt->errorInfo();
                error_log($arr[2]);
                return $arr;
            }
        }
        catch (Exception $e)
        {
            error_log($e->getMessage());
        }


        return $last_id;

    }
    function delete()
    {
        $query = "UPDATE " . $this->table_name . "
                set is_enabled = 0, deleted_at = now(), deleted_by = :deleted_by where id = :id";

        // prepare the query
        $stmt = $this->conn->prepare($query);

        // bind the values
        $this->id = (int)$this->id;
        $this->deleted_by = htmlspecialchars(strip_tags($this->deleted_by));



        // bind the values
        $stmt->bindParam(':id', $this->id);

        $stmt->bindParam(':deleted_by', $this->deleted_by);

        try {
            // execute the query, also check if query was successful
            if ($stmt->execute()) {
                return true;
            }
            else
            {
                $arr = $stmt->errorInfo();
                error_log($arr[2]);
                return false;
            }
        }
        catch (Exception $e)
        {
            error_log($e->getMessage());
            return false;
        }
    }
}

class WorkCalenderMessages
{
    // database connection and table name
    private $conn;
    private $table_name = "work_calendar_messages";

    // object properties
    public $id;
    public $message;
    public $is_enabled;
    public $created_at;
    public $updated_at;
    public $deleted_at;
    public $created_by;
    public $updated_by;
    public $deleted_by;
    // constructor
    public function __construct($db)
    {
        $this->conn = $db;
    }

    function update()
    {
        $query = "UPDATE " . $this->table_name . "
                set message = :message, 
                updated_at = now(), updated_by = :updated_by where id = :id";

        // prepare the query
        $stmt = $this->conn->prepare($query);

        // bind the values
        $this->id = (int)$this->id;
        $this->message = htmlspecialchars(strip_tags($this->message)); 
        $this->updated_by = htmlspecialchars(strip_tags($this->updated_by));
        

        // bind the values
        $stmt->bindParam(':id', $this->id);
        $stmt->bindParam(':message', $this->message);
        $stmt->bindParam(':updated_by', $this->updated_by);

    try {
        // execute the query, also check if query was successful
            if ($stmt->execute()) {
                return true;
            }
            else
            {
                $arr = $stmt->errorInfo();
                error_log($arr[2]);
                return false;
            }
        }
        catch (Exception $e)
        {
            error_log($e->getMessage());
            return false;
        }
    }

    // create new price record
    function create()
    {

        $last_id = 0;
        // insert query
        $query = "INSERT INTO " . $this->table_name . "
                (`message`,`is_enabled`,`created_at`,`created_by`) 
                VALUES (:message, 1, now(),:created_by)";

        // prepare the query
        $stmt = $this->conn->prepare($query);

            // sanitize

            $this->message = htmlspecialchars(strip_tags($this->message));
            $this->created_by = htmlspecialchars(strip_tags($this->created_by));

           
            // bind the values
            $stmt->bindParam(':message', $this->message);
            $stmt->bindParam(':created_by', $this->created_by);
            

        try {
            // execute the query, also check if query was successful
            if ($stmt->execute()) {
                $last_id = $this->conn->lastInsertId();
            }
            else
            {
                $arr = $stmt->errorInfo();
                error_log($arr[2]);
                return $arr;
            }
        }
        catch (Exception $e)
        {
            error_log($e->getMessage());
        }


        return $last_id;

    }
    function delete()
    {
        $query = "UPDATE " . $this->table_name . "
                set is_enabled = 0, deleted_at = now(), deleted_by = :deleted_by where id = :id";

        // prepare the query
        $stmt = $this->conn->prepare($query);

        // bind the values
        $this->id = (int)$this->id;
        $this->deleted_by = htmlspecialchars(strip_tags($this->deleted_by));



        // bind the values
        $stmt->bindParam(':id', $this->id);

        $stmt->bindParam(':deleted_by', $this->deleted_by);

        try {
            // execute the query, also check if query was successful
            if ($stmt->execute()) {
                return true;
            }
            else
            {
                $arr = $stmt->errorInfo();
                error_log($arr[2]);
                return false;
            }
        }
        catch (Exception $e)
        {
            error_log($e->getMessage());
            return false;
        }
    }
}
?>