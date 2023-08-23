<?php

class WorkCalenderMeetings
{
    // database connection and table name
    private $conn;
    private $table_name = "work_calendar_meetings";

    // object properties
    public $id;
    public $subject;
    public $project_name;
    public $message;
    public $attendee;
    public $location;
    public $start_time;
    public $end_time;
    public $is_enabled;
    public $created_at;
    public $updated_at;
    public $deleted_at;
    public $created_by;
    public $updated_by;
    public $deleted_by;

    public $color;
    public $color_other;
    public $text_color;

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
                project_name = :project_name, 
                message = :message, 
                attendee = :attendee,
                location = :location,
                start_time = :start_time, 
                end_time = :end_time, 
                color = :color,
                color_other = :color_other,
                text_color = :text_color,
                updated_at = now(), updated_by = :updated_by where id = :id";

        // prepare the query
        $stmt = $this->conn->prepare($query);

        // bind the values
        $this->id = (int)$this->id;
        $this->subject = htmlspecialchars(strip_tags($this->subject)); 
        $this->project_name = htmlspecialchars(strip_tags($this->project_name)); 
        $this->message = htmlspecialchars(strip_tags($this->message)); 
        $this->attendee = htmlspecialchars(strip_tags($this->attendee)); 
        $this->location = htmlspecialchars(strip_tags($this->location)); 
        $this->start_time = htmlspecialchars(strip_tags($this->start_time)); 
        $this->end_time = htmlspecialchars(strip_tags($this->end_time)); 
        $this->updated_by = htmlspecialchars(strip_tags($this->updated_by));

        $this->color = htmlspecialchars(strip_tags($this->color));
        $this->color_other = htmlspecialchars(strip_tags($this->color_other));
        $this->text_color = htmlspecialchars(strip_tags($this->text_color));
        

        // bind the values
        $stmt->bindParam(':id', $this->id);
        $stmt->bindParam(':subject', $this->subject);
        $stmt->bindParam(':project_name', $this->project_name);
        $stmt->bindParam(':message', $this->message);
        $stmt->bindParam(':attendee', $this->attendee);
        $stmt->bindParam(':location', $this->location);
        $stmt->bindParam(':start_time', $this->start_time);
        $stmt->bindParam(':end_time', $this->end_time);
        $stmt->bindParam(':color', $this->color);
        $stmt->bindParam(':color_other', $this->color_other);
        $stmt->bindParam(':text_color', $this->text_color);
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
                (`subject`,`project_name`,`message`,`attendee`,`location`,`start_time`,`end_time`,`is_enabled`,`created_at`,`created_by`, `color`, `color_other`, `text_color`) 
                VALUES (:subject, :project_name, :message, :attendee, :location, :start_time, :end_time, 1, now(), :created_by, :color, :color_other, :text_color)";

        // prepare the query
        $stmt = $this->conn->prepare($query);

            // sanitize

            $stmt->bindParam(':subject', $this->subject);
            $stmt->bindParam(':project_name', $this->project_name);
            $stmt->bindParam(':message', $this->message);
            $stmt->bindParam(':attendee', $this->attendee);
            $stmt->bindParam(':location', $this->location);
            $stmt->bindParam(':start_time', $this->start_time);
            $stmt->bindParam(':end_time', $this->end_time);
            $this->created_by = htmlspecialchars(strip_tags($this->created_by));
            $stmt->bindParam(':created_by', $this->created_by);
            $stmt->bindParam(':color', $this->color);
            $stmt->bindParam(':color_other', $this->color_other);
            $stmt->bindParam(':text_color', $this->text_color);

           
            // bind the values
            // $stmt->bindParam(':subject', $this->subject);
            // $stmt->bindParam(':project_name', $this->project_name);
            // $stmt->bindParam(':message', $this->message);
            // $stmt->bindParam(':attendee', $this->attendee);
            // $stmt->bindParam(':location', $this->location);
            // $stmt->bindParam(':start_time', $this->start_time);
            // $stmt->bindParam(':end_time', $this->end_time);
            // $stmt->bindParam(':created_by', $this->created_by);
            

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