<?php
class ApplyForLeave
{
    // database connection and table name
    private $conn;
    private $table_name = "apply_for_leave";

    // object properties
    public $id;
    public $uid;
    public $start_date;
    public $start_time;
    public $end_date;
    public $end_time;
    public $leave_type;
    public $leave;
    public $pic_url;
    public $reason;
    public $approval_id;
    public $approval_at;
    public $reject_reason;
    public $reject_at;
    public $re_approval_id;
    public $re_approval_at;
    public $re_reject_reason;
    public $re_reject_at;
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
                (`uid`, `start_date`, `start_time`, `end_date`, `end_time`, `leave_type`, `leave`, `pic_url`, `reason`, `created_at`) 
                VALUES (:uid, :start_date, :start_time, :end_date, :end_time, :leave_type, :leave, :pic_url, :reason, now())";

        // prepare the query
        $stmt = $this->conn->prepare($query);

            // sanitize
            $this->uid = (int) $this->uid;
            $this->start_date = htmlspecialchars(strip_tags($this->start_date));
            $this->start_time = htmlspecialchars(strip_tags($this->start_time));

            $this->end_date = htmlspecialchars(strip_tags($this->end_date));
            $this->end_time = htmlspecialchars(strip_tags($this->end_time));
            $this->leave_type = htmlspecialchars(strip_tags($this->leave_type));

            $this->leave = (int) $this->leave;
           
            $this->pic_url = htmlspecialchars(strip_tags($this->pic_url));
            $this->reason = htmlspecialchars(strip_tags($this->reason));
           
            // bind the values
            $stmt->bindParam(':uid', $this->uid);
            $stmt->bindParam(':start_date', $this->start_date);
            $stmt->bindParam(':start_time', $this->start_time);

            $stmt->bindParam(':end_date', $this->end_date);
            $stmt->bindParam(':end_time', $this->end_time);
            $stmt->bindParam(':leave_type', $this->leave_type);

            $stmt->bindParam(':leave', $this->leave);

            $stmt->bindParam(':pic_url', $this->pic_url);
            $stmt->bindParam(':reason', $this->reason);

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