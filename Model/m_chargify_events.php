<?php
/* 
	name: m_chargify_events
	date: 2019-01-07
	auth: VVenning
	desc: limited CRUD for chargify_events table.  Create, Update, Retrieve, Deactivate

*/

class m_chargify_events
{
    public $id;
    public $event_id;
    public $customer_id;
    public $statement_id;
    public $statement_events_id;
    public $events_key;
    public $events_message;
    public $created;

    public $arr_events;
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
//  date: 2019-01-07
//  auth: VVenning
//  desc: select_all for chargify_events table
    public function select_all($active=null)
    {
        $sql  = "SELECT * FROM ".CHARGIFY_EVENTS_TABLE;

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
//  date: 2019-01-07
//  auth: VVenning
//  desc: select_by_id for chargify_events table
	public function select_by_id($id=null)
	{
        $sql  = "SELECT * FROM ".CHARGIFY_EVENTS_TABLE." ";

        if($id == null)
        {
            $id = $this->id;
        }

        if($id == null)
        {
            return false;
        }
        else
        {
            $sql .= "WHERE id = ? ";
        }
        
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

//  name: select_by_statement_events_id
//  date: 2019-01-06
//  auth: VVenning
//  desc: select_by_statement_events_id for chargify_events table
    public function select_by_statement_events_id($statement_events_id=null)
    {
        $sql  = "SELECT * FROM ".CHARGIFY_EVENTS_TABLE." ";

        if($statement_events_id == null)
        {
            $statement_events_id = $this->statement_events_id;
        }

        if($statement_events_id == null)
        {
            return false;
        }
        else
        {
            $sql .= "WHERE statement_events_id = ? ";
        }
        
        $stmt = $this->conn->prepare($sql);

        if($stmt)
        {
            $stmt->bind_param("i", $statement_events_id);

            if(!$stmt->execute())
            {
                $this->log_database_error("Execute failed for select_by_statement_events_id()");
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
            $this->log_database_error("Prepare failed for select_by_statement_events_id()");
        }

    }

//  name: select_by_statement_id
//  date: 2019-01-06
//  auth: VVenning
//  desc: select_by_statement_id for chargify_events table
    public function select_by_statement_id($statement_id=null)
    {
        $sql  = "SELECT * FROM ".CHARGIFY_EVENTS_TABLE." ";

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

                while ($row = $res->fetch_assoc()){
            
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

//  name: select_by_event_id
//  date: 2019-01-06
//  auth: VVenning
//  desc: select_by_event_id for chargify_events table
    public function select_by_event_id($event_id=null)
    {
        $sql  = "SELECT * FROM ".CHARGIFY_EVENTS_TABLE." ";

        if($event_id == null)
        {
            $event_id = $this->event_id;
        }

        if($event_id == null)
        {
            return false;
        }
        else
        {
            $sql .= "WHERE event_id = ? ";
        }
        
        $stmt = $this->conn->prepare($sql);

        if($stmt)
        {

            $stmt->bind_param("i", $event_id);

            if(!$stmt->execute())
            {
                $this->log_database_error("Execute failed for select_by_event_id()");
            } 
            else
            {  
                $res = $stmt->get_result();

                while ($row = $res->fetch_assoc()){
            
                    $data[] = $row;
                }

                return $data;
                
                $stmt->close();
            }
        }
        else
        {
            $this->log_database_error("Execute failed for select_by_event_id()");
        }

    }

//	name: insert
//	date: 2019-01-07
//	auth: VVenning
//	desc: insert for chargify_events table
	public function insert()
	{
        $this->created = date("Y-m-d h:i:s", time());

        $this->database_error = null;

        if ($this->statement_id == null)
        {
            return false;
        }

        $stmt = $this->conn->prepare("INSERT INTO ".CHARGIFY_EVENTS_TABLE." (customer_id, event_id, statement_id, statement_events_id, events_key, events_message, created) VALUES (?,?,?,?,?,?,?)" );

        if($stmt)
        {
            $stmt->bind_param("iiiisss", $this->customer_id, $this->event_id, $this->statement_id, $this->statement_events_id, $this->events_key, $this->events_message, $this->created);

            if(!$stmt->execute())
            {
                $this->log_database_error("Execute failed for INSERT");
            } 
            else
            {  
                $this->last_inserted_id = $this->conn->insert_id;
            }
            $stmt->close();
        }
        else
        {
            $this->log_database_error("Prepare failed for INSERT");
        }
	}

    //	name: log_database_error
    //	date: 
    //	auth: VVenning
    //	desc: 
    public function log_database_error($msg)
    {
        $the_time = date("Y-m-d h:i:s", time());
        $err_log = fopen("DatabaseErrLog.txt", "a") or die("Unable to open file!");
        $err_txt  = $the_time." ".CHARGIFY_EVENTS_TABLE." - ";
        $err_txt .= $msg;
        $err_txt .= ": (" . $this->conn->errno . ") " . $this->conn->error;
        fwrite($err_log, $err_txt.PHP_EOL);
        fclose($this->err_log);
    }
}

?>
