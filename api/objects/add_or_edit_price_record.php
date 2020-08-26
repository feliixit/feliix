<?php
class PriceRecord
{
    // database connection and table name
    private $conn;
    private $table_name = "price_record";

    // object properties
    public $id;
    public $category;
    public $sub_category;
    public $related_account;
    public $details;
    public $pic_url;
    public $payee;
    public $paid_date;
    public $cash_in;
    public $cash_out;
    public $remarks;
    public $is_locked;
    public $is_enabled;
    public $is_marked;
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
                set account = :account, category = :category, sub_category = :sub_category, related_account = :related_account, details = :details, pic_url = :pic_url , payee = :payee, cash_in = :cash_in, cash_out = :cash_out, remarks = :remarks, is_locked = :is_locked, is_enabled = : is_enabled, is_marked = :is_marked, update_at = now(), updated_by = :updated_by where id = :id";

        // prepare the query
        $stmt = $this->conn->prepare($query);

        // bind the values
        $this->id = (int)$this->id;
        $this->account = (int)$this->account;
        $this->category = htmlspecialchars(strip_tags($this->category));
        $this->sub_category = htmlspecialchars(strip_tags($this->sub_category));
        $this->related_account = htmlspecialchars(strip_tags($this->related_account));

        $this->details = htmlspecialchars(strip_tags($this->details));
        $this->pic_url = htmlspecialchars(strip_tags($this->pic_url));
        $this->payee = htmlspecialchars(strip_tags($this->payee));
        $this->cash_in = (int)$this->cash_in;
        $this->cash_out = (int)$this->cash_out;
        $this->remarks = htmlspecialchars(strip_tags($this->remarks));
        $this->updated_by = htmlspecialchars(strip_tags($this->updated_by));
        $locked = filter_var($this->is_locked,FILTER_VALIDATE_BOOLEAN);
        $enabled = filter_var($this->is_enabled,FILTER_VALIDATE_BOOLEAN);
        $marked = filter_var($this->is_marked,FILTER_VALIDATE_BOOLEAN);


        // bind the values
        $stmt->bindParam(':id', $this->id);
        $stmt->bindParam(':account', $this->account);
        $stmt->bindParam(':category', $this->category);
        $stmt->bindParam(':sub_category', $this->sub_category);
        $stmt->bindParam(':related_account', $this->related_account);

        $stmt->bindParam(':details', $this->details);
        $stmt->bindParam(':pic_url', $this->pic_url);
        $stmt->bindParam(':payee', $this->payee);
        $stmt->bindParam(':paid_date', $this->paid_date);

        $stmt->bindParam(':cash_in', $this->cash_in);

        $stmt->bindParam(':cash_out', $this->cash_out);
        $stmt->bindParam(':remarks', $this->remarks);
        $stmt->bindParam(':is_locked', $locked);
        $stmt->bindParam(':is_enabled', $enabled);
        $stmt->bindParam(':is_marked', $marked);
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
                (`account`,`category`, `sub_category`, `related_account`, `details`, `pic_url`, `payee`, `paid_date`, `cash_in`, `cash_out`, `remarks`,`is_locked`,`is_enabled`,`is_marked`,`created_at`,`created_by`) 
                VALUES (:account,:category, :sub_category, :related_account, :details, :pic_url, :payee, :paid_date, :cash_in, :cash_out, :remarks, :is_locked, :is_enabled,:is_marked, now(),:created_by)";

        // prepare the query
        $stmt = $this->conn->prepare($query);

            // sanitize

            $this->account = (int)$this->account;
            $this->category = htmlspecialchars(strip_tags($this->category));
            $this->sub_category = htmlspecialchars(strip_tags($this->sub_category));
            $this->related_account = htmlspecialchars(strip_tags($this->related_account));

            $this->details = htmlspecialchars(strip_tags($this->details));
            $this->pic_url = htmlspecialchars(strip_tags($this->pic_url));
            $this->payee = htmlspecialchars(strip_tags($this->payee));
            $this->cash_in = (int)$this->cash_in;
            $this->cash_out = (int)$this->cash_out;
            $this->remarks = htmlspecialchars(strip_tags($this->remarks));
            $locked = filter_var($this->is_locked,FILTER_VALIDATE_INT );
            $enabled = filter_var($this->is_enabled,FILTER_VALIDATE_INT );
            $marked = filter_var($this->is_marked,FILTER_VALIDATE_INT);
        $this->created_by = htmlspecialchars(strip_tags($this->created_by));

           
            // bind the values
            $stmt->bindParam(':account', $this->account);
            $stmt->bindParam(':category', $this->category);
            $stmt->bindParam(':sub_category', $this->sub_category);
            $stmt->bindParam(':related_account', $this->related_account);

            $stmt->bindParam(':details', $this->details);
            $stmt->bindParam(':pic_url', $this->pic_url);
            $stmt->bindParam(':payee', $this->payee);
            $stmt->bindParam(':paid_date', $this->paid_date);

            $stmt->bindParam(':cash_in', $this->cash_in);

            $stmt->bindParam(':cash_out', $this->cash_out);
            $stmt->bindParam(':remarks', $this->remarks);
            $stmt->bindParam(':is_locked', $locked);
            $stmt->bindParam(':is_enabled', $enabled);
            $stmt->bindParam(':is_marked',  $marked);
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