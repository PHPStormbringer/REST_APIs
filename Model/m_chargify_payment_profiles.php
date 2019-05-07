<?php
/* 
	name: m_chargify_payment_profiles
	date: 2019-01-04
	auth: VVenning
	desc: limited CRUD for chargify_payment_profiles table.  Create, Update, Retrieve, Deactivate


*/

class m_chargify_payment_profiles
{
    public $id;
    public $event_id;
    public $customer_id;

    public $billing_id;
    public $billing_first_name;
    public $billing_last_name;
    public $billing_address;
    public $billing_address_2;
    public $billing_city;
    public $billing_state;
    public $billing_zip;
    public $billing_country;
    public $billing_created;

    public $arr_payment_profiles;
    public $last_inserted_id;
    public $err_log;

    public $conn;

    public function __construct()
    {
  
    }


    public function __destruct()
    {


    }

//  name: select_all
//  date: 2019-01-04
//  auth: VVenning
//  desc: select_all for chargify_payment_profiles table
    public function select_all($active=null)
    {
        $sql  = "SELECT * FROM ".CHARGIFY_PAYMENT_PROFILES_TABLE;

        $stmt = $this->conn->prepare($sql);

        if($stmt)
        {
            if    ($active === null)
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
//  desc: select_by_id for chargify_payment_profiles table
	public function select_by_id($id=null)
	{
        $sql  = "SELECT * FROM ".CHARGIFY_PAYMENT_PROFILES_TABLE." ";

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

//  name: select_by_billing_id
//  date: 2019-01-04
//  auth: VVenning
//  desc: select_by_billing_id for chargify_payment_profiles table
    public function select_by_billing_id($billing_id=null)
    {
        $sql  = "SELECT * FROM ".CHARGIFY_PAYMENT_PROFILES_TABLE." ";

        if($billing_id == null)
        {
            $billing_id = $this->billing_id;
        }

        if($billing_id == null)
        {
            return false;
        }
        else
        {
            $sql .= "WHERE billing_id = ? ";
        }
        
        $stmt = $this->conn->prepare($sql);

        if($stmt)
        {
            $stmt->bind_param("i", $billing_id);

            if(!$stmt->execute())
            {
                $this->log_database_error("Execute failed for select_by_billing_id()");
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
            $this->log_database_error("Prepare failed for select_by_billing_id()");
        }
    }


//	name: insert
//	date: 2019-01-04
//	auth: VVenning
//	desc: insert for users table
	public function insert()
	{
        $this->created = date("Y-m-d h:i:s", time());
        $this->database_error = null;

        if ($this->billing_id == null)
        {
            return false;
        }

	//  customer_id, event_id,billing_id,billing_first_name,billing_last_name,billing_address,billing_address_2,billing_city,billing_state,billing_zip,billing_country,billing_created
        $stmt = $this->conn->prepare("INSERT INTO ".CHARGIFY_PAYMENT_PROFILES_TABLE." (customer_id, event_id, billing_id, billing_first_name, billing_last_name, billing_address, billing_address_2, billing_city, billing_state, billing_zip, billing_country, billing_created) VALUES (?,?,?,?,?,?,?,?,?,?,?,?)" );

        if($stmt)
        {
            $stmt->bind_param("iiisssssssss", $this->customer_id, $this->event_id,$this->billing_id, $this->billing_first_name, $this->billing_last_name, $this->billing_address, $this->billing_address_2, $this->billing_city, $this->billing_state, $this->billing_zip, $this->billing_country, $this->billing_created);

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
//	date: 2019-01-15
//	auth: VVenning+
//	desc: update for users table.  I'm still thinking about this philosophically
//  pass an array of the fields to be updated
	public function update($arrFieldsToBeUpdated=null)
	{
        $this->modified = date("Y-m-d h:i:s", time());

        $this->database_error = null;
        
        $sql = "UPDATE ".CHARGIFY_PAYMENT_PROFILES_TABLE."SET ";

        if($arrFieldsToBeUpdated==null)
        {
        //  update everything
            $sql .= "first_name = ?, last_name = ?, reference = ?, organization = ?, address = ?, address_2 = ?, city = ?, state = ?, zip = ?, country = ?, email = ?, phone = ?, created = ?, modifed = ?, active = ?";
        }
        else
        {

            foreach($arrFieldsToBeUpdated as $field)
            {
                $sql .= $field." = ?, ";
            }
            $sql = rtrim($sql, ",");
        }

        $stmt = $this->conn->prepare($sql);

        if($stmt)
        {
            $this->modified = date("Y-m-d h:i:s", time());
            $stmt->bind_param("ississssssssssss", $this->id, $this->billing_id, $this->first_name, $this->last_name, $this->reference, $this->organization, $this->address, $this->address_2, $this->city, $this->state, $this->zip, $this->country, $this->email, $this->phone, $this->created, $this->modifed, $this->active);
            if(!$stmt->execute())
            {
                $this->log_database_error("Execute failed for update()");
            } 
  
            $stmt->close();
        }
        else
        {
            $this->log_database_error("Prepare failed for update()");
        }

	}


//	name: deactivate
//	date: 2018-05-29
//	auth: VVenning
//	desc: make a user inactive.  This changes a single value
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
        $err_txt  = $the_time." ".CHARGIFY_PAYMENT_PROFILES_TABLE." - ";
        $err_txt .= $msg;
        $err_txt .= ": (" . $this->conn->errno . ") " . $this->conn->error;
        fwrite($err_log, $err_txt.PHP_EOL);
        fclose($this->err_log);
    }
}

?>
