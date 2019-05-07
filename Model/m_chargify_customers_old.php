<?php
/* 
	name: m_chargify_customers
	date: 2018-12-31
	auth: VVenning
	desc: limited CRUD for chargify_customers table.  Create, Update, Retrieve, Deactivate


*/

class m_chargify_customers
{
    public $id;
    public $event_id;
 
    public $customer_id;
    public $first_name;
    public $last_name;
    public $reference;
    public $organization;
    public $address;
    public $address_2;
    public $city;
    public $state;
    public $zip;
    public $country;
    public $email;
    public $phone;
    public $created;
    public $modifed;
    public $active;

    public $arr_customers;
    public $last_inserted_id;

    public $conn;

    public function __construct()
    {
  
    }


    public function __destruct()
    {


    }

//  name: select_all
//  date: 2018-12-31
//  auth: VVenning
//  desc: select_all for chargify_customers table
    public function select_all($active=null)
    {

        $sql  = "SELECT * FROM ".CHARGIFY_CUSTOMERS_TABLE." ";

        if($active == null)
        {
        //  No WHERE clause
        }
        else
        {
            $sql .= "WHERE active = ? ";
        }

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
//  date: 2018-12-31
//  auth: VVenning
//  desc: select_by_id for chargify_customers table
	public function select_by_id($id=null)
	{
        $sql  = "SELECT * FROM ".CHARGIFY_CUSTOMERS_TABLE." ";

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

//  name: select_by_customer_id
//  date: 2018-12-31
//  auth: VVenning
//  desc: select_by_customer_id for chargify_customers table
    public function select_by_customer_id($customer_id=null)
    {
        $sql  = "SELECT * FROM ".CHARGIFY_CUSTOMERS_TABLE." ";

        if($customer_id == null)
        {
            $customer_id = $this->customer_id;
        }

        if($customer_id == null)
        {
            return false;
        }
        else
        {
            $sql .= "WHERE customer_id = ? ";
        }
        
        $stmt = $this->conn->prepare($sql);

        if($stmt)
        {
            $stmt->bind_param("i", $customer_id);

            if(!$stmt->execute())
            {
                $this->log_database_error("Execute failed for select_by_customer_id()");
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
            $this->log_database_error("Prepare failed for select_by_customer_id()");
        }

    }

//  name: select_by_organization
//  date: 2018-12-31
//  auth: VVenning
//  desc: select by organization name LIKE
    public function select_by_organization($organization=null)
    {
        $sql  = "SELECT * FROM ".CHARGIFY_CUSTOMERS_TABLE." ";

        if($organization == null)
        {
            $organization = $this->organization;
        }

        if($organization == null)
        {
            return false;
        }
        else
        {
            $sql .= "WHERE organization LIKE '%?%'";
        }
        
        $stmt = $this->conn->prepare($sql);

        if($stmt)
        {
            $stmt->bind_param("s", $organization);

            if(!$stmt->execute())
            {
                $this->log_database_error("Execute failed for select_by_organization()");
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
            $this->log_database_error("Prepare failed for select_by_organization()");
        }
    }


//	name: select_by_email_like
//	date: 2018-12-31
//	auth: VVenning
//	desc: select by email 
	public function select_by_email_like($email=null)
	{
        $sql  = "SELECT * FROM ".CHARGIFY_CUSTOMERS_TABLE." ";

        if($email == null)
        {
            $email = $this->email;
        }

        if($email == null)
        {
            return false;
        }
        else
        {
            $sql .= "WHERE email LIKE '%?%'";
        }
        
        $stmt = $this->conn->prepare($sql);

        if($stmt)
        {
            $stmt->bind_param("s", $email);

            if(!$stmt->execute())
            {
                $this->log_database_error("Execute failed for select_by_email_like()");
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
            $this->log_database_error("Prepare failed for select_by_email_like()");
        }

	}


//	name: insert
//	date: 2018-12-31
//	auth: VVenning
//	desc: insert for users table
	public function insert()
	{
        $this->created = date("Y-m-d h:i:s", time());

        $this->database_error = null;

        if ($this->customer_id == null)
        {
            return false;
        }

	//  password, first_name, last_name, email, screen_name, group_id,partner_id, phone_number, phone_number_ext,group_id, partner_id
    //  2018-09-24 1354 VVenning - Add dont_send_email to insert
    //  2018-10-03 1202 VVenning - Add client_groups to insert
        $stmt = $this->conn->prepare("INSERT INTO ".CHARGIFY_CUSTOMERS_TABLE." (customer_id,event_id,first_name,last_name,reference,organization,address,address_2,city,state,zip,country,email,phone,created,modifed,active) VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?)" );

        if($stmt)
        {
            $stmt->bind_param("iisssssssssssssss", $this->customer_id, $this->event_id, $this->first_name, $this->last_name, $this->reference, $this->organization, $this->address, $this->address_2, $this->city, $this->state, $this->zip, $this->country, $this->email, $this->phone, $this->created, $this->modifed, $this->active);

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
//	date: 2018-05-29
//	auth: VVenning
//	desc: update for users table.  I'm still thinking about this philosophically
//  pass an array of the fields to be updated
	public function update($arrFieldsToBeUpdated=null)
	{
        $this->database_error = null;
        
        $sql = "UPDATE ".CHARGIFY_CUSTOMERS_TABLE."SET ";

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

            $stmt->bind_param("ississssssssssss", $this->id, $this->customer_id, $this->first_name, $this->last_name, $this->reference, $this->organization, $this->address, $this->address_2, $this->city, $this->state, $this->zip, $this->country, $this->email, $this->phone, $this->created, $this->modifed, $this->active);

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
        $err_txt  = $the_time." ".CHARGIFY_PRODUCTS_TABLE." - ";
        $err_txt .= $msg;
        $err_txt .= ": (" . $this->conn->errno . ") " . $this->conn->error;
        fwrite($err_log, $err_txt.PHP_EOL);
        fclose($this->err_log);
    }

}

?>
