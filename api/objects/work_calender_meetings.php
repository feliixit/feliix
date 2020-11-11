<?php

class WorkCalenderMeetings
{
    // database connection and table name
    private $conn;
    private $table_name = "work_calendar_meetings";

    // object properties
    public $id;
    public $subject;
    public $message;
    public $attendee;
    public $start_time;
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
                set 
                subject = :subject, 
                message = :message, 
                attendee = :attendee,
                start_time = :start_time, 
                end_time = :end_time, 
                updated_at = now(), updated_by = :updated_by where id = :id";

        // prepare the query
        $stmt = $this->conn->prepare($query);

        // bind the values
        $this->id = (int)$this->id;
        $this->subject = htmlspecialchars(strip_tags($this->subject)); 
        $this->message = htmlspecialchars(strip_tags($this->message)); 
        $this->attendee = htmlspecialchars(strip_tags($this->attendee)); 
        $this->start_time = htmlspecialchars(strip_tags($this->start_time)); 
        $this->end_time = htmlspecialchars(strip_tags($this->end_time)); 
        $this->updated_by = htmlspecialchars(strip_tags($this->updated_by));
        

        // bind the values
        $stmt->bindParam(':id', $this->id);
        $stmt->bindParam(':subject', $this->subject);
        $stmt->bindParam(':message', $this->message);
        $stmt->bindParam(':attendee', $this->attendee);
        $stmt->bindParam(':start_time', $this->start_time);
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
                (`subject`,`message`,`attendee`,`start_time`,`end_time`,`is_enabled`,`created_at`,`created_by`) 
                VALUES (:subject, :message, :attendee, :start_time, :end_time, 1, now(), :created_by)";

        // prepare the query
        $stmt = $this->conn->prepare($query);

            // sanitize

            $stmt->bindParam(':subject', $this->subject);
            $stmt->bindParam(':message', $this->message);
            $stmt->bindParam(':attendee', $this->attendee);
            $stmt->bindParam(':start_time', $this->start_time);
            $stmt->bindParam(':end_time', $this->end_time);
            $this->created_by = htmlspecialchars(strip_tags($this->created_by));

           
            // bind the values
            $stmt->bindParam(':subject', $this->subject);
            $stmt->bindParam(':message', $this->message);
            $stmt->bindParam(':attendee', $this->attendee);
            $stmt->bindParam(':start_time', $this->start_time);
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
?>