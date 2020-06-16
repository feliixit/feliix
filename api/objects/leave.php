<?php
class Leave
{
    // database connection and table name
    private $conn;
    private $table_name = "`leave`";

    // object properties
    public $id;
    public $uid;
    public $apply_date;
    public $apply_id;
    public $apply_period;
    public $leave_type;
    public $duration;

    public $created_at;
    public $updated_at;
    public $status;

    // constructor
    public function __construct($db)
    {
        $this->conn = $db;
    }

    // create new user record
    function create()
    {

        $last_id = 0;
        // insert query
        $query = "INSERT INTO " . $this->table_name . "
                (`uid`, `apply_id`, `apply_date`, `apply_period`, `leave_type`, `duration`, `created_at`, `status`) 
                VALUES (:uid, :apply_id, :apply_date, :apply_period, :leave_type, :duration, now(), 0)";

        // prepare the query
        $stmt = $this->conn->prepare($query);

            // sanitize
            $this->uid = (int) $this->uid;
            $this->apply_id = htmlspecialchars(strip_tags($this->apply_id));
            $this->apply_date = htmlspecialchars(strip_tags($this->apply_date));

            $this->apply_period = htmlspecialchars(strip_tags($this->apply_period));
            $this->leave_type = htmlspecialchars(strip_tags($this->leave_type));

            $this->duration = (float) $this->duration;
           
            // bind the values
            $stmt->bindParam(':uid', $this->uid);
            $stmt->bindParam(':apply_id', $this->apply_id);
            $stmt->bindParam(':apply_date', $this->apply_date);

            $stmt->bindParam(':apply_period', $this->apply_period);
            $stmt->bindParam(':leave_type', $this->leave_type);

            $stmt->bindParam(':duration', $this->duration);

        try {
            // execute the query, also check if query was successful
            if ($stmt->execute()) {
                $last_id = $this->conn->lastInsertId();
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
        }


        return $last_id;

    }
}