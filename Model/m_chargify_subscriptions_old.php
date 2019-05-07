<?php
/* 
	name: m_chargify_subscriptions
	date: 2019-01-11
	auth: VVenning
	desc: limited CRUD for chargify_subscriptions table.  Create, Update, Retrieve, Deactivate

*/

class m_chargify_subscriptions
{
    public $id;
    public $event_id;
    public $customer_id;
    public $subscription_id;
    public $state;
    public $balance_in_cents;
    public $created;

    public $arr_sites;
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
//  date: 2019-01-11
//  auth: VVenning
//  desc: select_all for chargify_subscriptions table
    public function select_all()
    {
        $sql  = "SELECT * FROM ".CHARGIFY_SUBSCRIPTIONS_TABLE;

        $stmt = $this->conn->prepare($sql);

        if($stmt)
        {
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
//  date: 2019-01-11
//  auth: VVenning
//  desc: select_by_id for chargify_subscriptions table
	public function select_by_id($id=null)
	{
        $sql  = "SELECT * FROM ".CHARGIFY_SUBSCRIPTIONS_TABLE." ";

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


//  name: select_by_subscription_id
//  date: 2019-01-11
//  auth: VVenning
//  desc: select_by_event_id for chargify_subscriptions table
    public function select_by_subscription_id($subscription_id=null)
    {
        $sql  = "SELECT * FROM ".CHARGIFY_SUBSCRIPTIONS_TABLE." ";

        if($subscription_id == null)
        {
            $subscription_id = $this->subscription_id;
        }

        if($subscription_id == null)
        {
            return false;
        }
        else
        {
            $sql .= "WHERE subscription_id = ? ";
        }
        
        $stmt = $this->conn->prepare($sql);

        if($stmt)
        {
            $stmt->bind_param("i", $subscription_id);

            if(!$stmt->execute())
            {
                $this->log_database_error("Execute failed for select_by_subscription_id()");
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
            $this->log_database_error("Prepare failed for select_by_subscription_id()");
        }

    }

//	name: insert
//	date: 2019-01-11
//	auth: VVenning
//	desc: insert for chargify_subscriptions table
	public function insert()
	{
        $this->database_error = null;

        $stmt = $this->conn->prepare("INSERT INTO ".CHARGIFY_SUBSCRIPTIONS_TABLE." (customer_id, event_id, subscription_id, state, balance_in_cents, created) VALUES (?,?,?,?,?,?)" );

        if($stmt)
        {
            $this->created = date("Y-m-d h:i:s", time());

            $stmt->bind_param("iiisis", $this->customer_id, $this->event_id, $this->subscription_id, $this->state, $this->balance_in_cents, $this->created);

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
        $err_txt  = $the_time." ".CHARGIFY_SUBSCRIPTIONS_TABLE." - ";
        $err_txt .= $msg;
        $err_txt .= ": (" . $this->conn->errno . ") " . $this->conn->error;
        fwrite($err_log, $err_txt.PHP_EOL);
        fclose($this->err_log);
    }
}

?>
