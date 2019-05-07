<?php
/* 
	name: m_chargify_statements
	date: 2019-01-04
	auth: VVenning
	desc: limited CRUD for chargify_statements table.  Create, Update, Retrieve, Deactivate

*/

class m_chargify_statements
{
    public $id;
    public $event_id;
    public $customer_id;
    
    public $statement_id;
    public $closed_at;
    public $created_at;
    public $opened_at;
    public $settled_at;
    public $subscription_id;
    public $updated_at;
    public $starting_balance_in_cents;
    public $ending_balance_in_cents;
    public $total_in_cents;
    public $memo;
    public $created;

    public $arr_statements;
    public $last_inserted_id;

    public $conn;

    public function __construct()
    {
    //  
    }

    public function __destruct()
    {
    //  
    }

//  name: select_all
//  date: 2019-01-04
//  auth: VVenning
//  desc: select_all for chargify_statements table
    public function select_all($active=null)
    {
        $sql  = "SELECT * FROM ".CHARGIFY_STATEMENTS_TABLE;

        $stmt = $this->conn->prepare($sql);

        if($stmt)
        {
            if($active === null)
            {
            //  bind no params
            }
            else
            {
                $stmt->bind_param("i", $active);
            }

            if(!$stmt->execute())
            {
                $this->log_database_error("Execute failed for select_all()");
            } 
            else
            {
                $res = $stmt->get_result();
                while ($row = $res->fetch_assoc())
                {
                    $data[] = $row;
                }
                return $data;
                $stmt->close();
            }
        }
        else
        {
            $this->log_database_error("Prepare failed for select_all()");
        }
    }


//	name: select_by_id
//  date: 2019-01-04
//  auth: VVenning
//  desc: select_by_id for chargify_statements table
	public function select_by_id($id=null)
	{
        $sql  = "SELECT * FROM ".CHARGIFY_STATEMENTS_TABLE." ";

        if($id == null) { $id = $this->id; }
        if($id == null) { return false;    } else { $sql .= "WHERE id = ? "; }
        
        $stmt = $this->conn->prepare($sql);
        if($stmt)
        {
            $stmt->bind_param("i", $id);
            if(!$stmt->execute())
            {
                $this->log_database_error("Execute failed for select_by_id()");
            } 
            else
            {
                $res = $stmt->get_result();
                while ($row = $res->fetch_assoc())
                {
                    $data[] = $row;
                }
                return $data;
                $stmt->close();
            }
        }
        else
        {
            $this->log_database_error("Prepare failed for select_by_id()");
        }
	}

//  name: select_by_statement_id
//  date: 2019-01-06
//  auth: VVenning
//  desc: select_by_statement_id for chargify_statements table
    public function select_by_statement_id($statement_id=null)
    {
        $sql  = "SELECT * FROM ".CHARGIFY_STATEMENTS_TABLE." ";

        if($statement_id == null)
        {
            $statement_id = $this->statement_id;
        }

        if($statement_id == null)
        {
            return false;
        }
        else
        {
            $sql .= "WHERE statement_id = ? ";
        }
        
        $stmt = $this->conn->prepare($sql);

        if($stmt)
        {
            $stmt->bind_param("i", $statement_id);

            if(!$stmt->execute())
            {
                $this->log_database_error("Execute failed for select_by_statement_id()");
            } 
            else
            {
                $res = $stmt->get_result();
                while ($row = $res->fetch_assoc())
                {
                    $data[] = $row;
                }
                return $data;
                $stmt->close();
            }
        }
        else
        {
            $this->log_database_error("Prepare failed for select_by_statement_id()");
        }

    }

//	name: insert
//	date: 2019-01-04
//	auth: VVenning
//	desc: insert for users table
	public function insert()
	{
        $this->database_error = null;

        if ($this->statement_id == null)
        {
            return false;
        }

        $stmt = $this->conn->prepare("INSERT INTO ".CHARGIFY_STATEMENTS_TABLE." (customer_id, event_id, statement_id, closed_at, created_at, opened_at, settled_at, subscription_id, updated_at, starting_balance_in_cents, ending_balance_in_cents, total_in_cents, memo, created) VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?,?)" );

        if($stmt)
        {
            $this->created = date("Y-m-d h:i:s", time());

            $stmt->bind_param("iiissssisiiiss", $this->customer_id, $this->event_id, $this->statement_id, $this->closed_at, $this->created_at, $this->opened_at, $this->settled_at, $this->subscription_id, $this->updated_at, $this->starting_balance_in_cents, $this->ending_balance_in_cents, $this->total_in_cents, $this->memo, $this->created);

            if(!$stmt->execute())
            {
                $this->log_database_error("Execute failed for insert()");
            } 
            else
            {
                $this->last_inserted_id = $this->conn->insert_id;
            }
            $stmt->close();
        }
        else
        {
            $this->log_database_error("Prepare failed for insert()");
        }
	}

//	name: update
//	date: 
//	auth: VVenning
//	desc: 
	public function update($arrFieldsToBeUpdated=null)
	{
    //  
	}


//	name: deactivate
//	date: 
//	auth: VVenning
//	desc: 
	public function deactivate()
	{
		//
	}

//	name: log_database_error
//	date: 
//	auth: VVenning
//	desc: 
    public function log_database_error($msg)
    {
        $the_time = date("Y-m-d h:i:s", time());
        $err_log = fopen("DatabaseErrLog.txt", "a") or die("Unable to open file!");
        $err_txt  = $the_time." ".CHARGIFY_STATEMENTS_TABLE." - ";
        $err_txt .= $msg;
        $err_txt .= ": (" . $this->conn->errno . ") " . $this->conn->error;
        fwrite($err_log, $err_txt.PHP_EOL);
        fclose($this->err_log);
    }
}

?>
