<?php
/* 
	name: m_chargify_customers
	date: 2018-12-31
	auth: VVenning
	desc: limited CRUD for chargify_customers table.  Create, Update, Retrieve, Deactivate
//  2019-03-12 0923 VVenning - Declared seven new variables
//  2019-03-12 0923 VVenning - Added seven new variables to insert, portal_invite_last_sent_at, portal_invite_last_accepted_at, verified, portal_customer_created_at, vat_number, cc_emails, tax_exempt
//  2019-04-03 1119 VVenning - Added two new variables.   `created_at`,`updated_at`
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

//  2019-03-12 0923 VVenning - Declared seven new variables
    public $portal_invite_last_sent_at;
    public $portal_invite_last_accepted_at;
    public $verified;
    public $portal_customer_created_at;
    public $vat_number;
    public $cc_emails;
    public $tax_exempt;
    public $created_at;
    public $updated_at;
    public $parent_id;

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
                $this->log_database_error("File: m_chargify_customers.php.  Message: Execute failed for function select_all().  MySQL_Error: ".$stmt->error);
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
            $this->log_database_error("File: m_chargify_customers.php.  Message: Prepare failed for function select_all().");
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
                $this->log_database_error("File: m_chargify_customers.php.  Message: Execute failed for function select_by_id().  MySQL_Error: ".$stmt->error);
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
            $this->log_database_error("File: m_chargify_customers.php.  Message: Prepare failed for function select_by_id().");
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
                $this->log_database_error("File: m_chargify_customers.php.  Message: Execute failed for function select_by_customer_id().  MySQL_Error: ".$stmt->error);
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
            $this->log_database_error("File: m_chargify_customers.php.  Message: Prepare failed for function select_by_customer_id().");
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
                $this->log_database_error("File: m_chargify_customers.php.  Message: Execute failed for function select_by_organization().  MySQL_Error: ".$stmt->error);
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
            $this->log_database_error("File: m_chargify_customers.php.  Message: Prepare failed for function select_by_organization().");
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
                $this->log_database_error("File: m_chargify_customers.php.  Message: Execute failed for function select_by_email_like().  MySQL_Error: ".$stmt->error);
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
            $this->log_database_error("File: m_chargify_customers.php.  Message: Prepare failed for function  select_by_email_like().");
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
    //  2019-03-12 0923 VVenning - Added seven new variables to insert, portal_invite_last_sent_at, portal_invite_last_accepted_at, verified, portal_customer_created_at, vat_number, cc_emails, tax_exempt
    //  2019-04-03 1119 VVenning - Added two new variables.   `created_at`,`updated_at`
        $stmt = $this->conn->prepare("INSERT INTO ".CHARGIFY_CUSTOMERS_TABLE." (`customer_id`, `event_id`, `first_name`, `last_name`, `reference`, `organization`, `address`, `address_2`, `city`, `state`, `zip`, `country`, `email`, `phone`, `created`, `modifed`, `active`, `portal_invite_last_sent_at`, `portal_invite_last_accepted_at`, `verified`, `portal_customer_created_at`, `vat_number`, `cc_emails`, `tax_exempt`, `created_at`,`updated_at`,`parent_id`) VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?)" );

        if($stmt)
        {
            $stmt->bind_param("iisssssssssssssssssssssssss", $this->customer_id, $this->event_id, $this->first_name, $this->last_name, $this->reference, $this->organization, $this->address, $this->address_2, $this->city, $this->state, $this->zip, $this->country, $this->email, $this->phone, $this->created, $this->modifed, $this->active, $this->portal_invite_last_sent_at, $this->portal_invite_last_accepted_at, $this->verified, $this->portal_customer_created_at, $this->vat_number, $this->cc_emails, $this->tax_exempt, $this->created_at, $this->updated_at, $this->parent_id);

            if(!$stmt->execute())
            {
                $this->log_database_error("File: m_chargify_customers.php.  Message: Execute failed for function insert().".PHP_EOL."MySQL_Error: ".$stmt->error);
            } 
            else
            {
                $this->last_inserted_id = $this->conn->insert_id;
            }
            $stmt->close();
        }
        else
        {
            $this->log_database_error("File: m_chargify_customers.php.  Message: Prepare failed for function insert().");
        }
	}

//	name: update
//	date: 2018-05-29
//	auth: VVenning
//	desc: update for chargify_customers table.  I'm still thinking about this philosophically
//  pass an array of the fields to be updated.  Goota pass datatype too.
	public function update($arrFieldsToBeUpdated=null)
	{
        $this->database_error = null;
        
        $sql = "UPDATE ".CHARGIFY_CUSTOMERS_TABLE."SET ";

        if($arrFieldsToBeUpdated==null && is_numeric($this->id))
        {
        //  update everything
            $sql .= "first_name = ?, last_name = ?, reference = ?, organization = ?, address = ?, address_2 = ?, city = ?, state = ?, zip = ?, country = ?, email = ?, phone = ?, created = ?, modifed = ?, active = ?, portal_invite_last_sent_at = ?, portal_invite_last_accepted_at = ?, verified = ?, portal_customer_created_at = ?, vat_number = ?, cc_emails = ?, tax_exempt = ?";
        }
        else
        {
            foreach($arrFieldsToBeUpdated as $field)
            {
                if(key($field) != 'id')
                {
                    $sql .= key($field)." = ?, ";
                }
            }
            $sql = rtrim($sql, ", ");
        }

        $sql .= " WHERE id = ?";

        $stmt = $this->conn->prepare($sql);

        if($stmt)
        {
            $this->modified = date("Y-m-d h:i:s", time());

            if($arrFieldsToBeUpdated==null)
            {

                $stmt->bind_param("ississsssssssssssssssss", $this->id, $this->first_name, $this->last_name, $this->reference, 
                    $this->organization, $this->address, $this->address_2, $this->city, 
                    $this->state, $this->zip, $this->country, $this->email, 
                    $this->phone, $this->created, $this->modifed, $this->active, 
                    $this->portal_invite_last_sent_at, $this->portal_invite_last_accepted_at, $this->verified, $this->portal_customer_created_at, 
                    $this->vat_number, $this->cc_emails, $this->tax_exempt);
            }
            else
            {
                $str_param = "";

                foreach($arrFieldsToBeUpdated as $field)
                {
                    if($field == 'id' || $field == 'reference')
                    {
                        $str_param .= "i";
                    }
                    else
                    {
                        $str_param .= "s";
                    }

                }

            }


            if(!$stmt->execute())
            {
                $this->log_database_error("File: m_chargify_customers.php.  Message: Execute failed for function update().  MySQL_Error: ".$stmt->error);
            } 
        
            $stmt->close();
        }
        else
        {
            $this->log_database_error("File: m_chargify_customers.php.  Message: Prepare failed for function update().");
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
        $log_prefix = date("Y-m-d_", time());
        $the_time   = date("Y-m-d h:i:s", time());
        $err_log    = fopen("logs/".$log_prefix."DatabaseErrLog.txt", "a") or die("Unable to open file!");
        $err_txt    = $the_time." Table: ".CHARGIFY_CUSTOMERS_TABLE." - ";
        $err_txt   .= $msg;
        $err_txt   .= "  MySQL_error: (" . $this->conn->errno . ") " . $this->conn->error;
        fwrite($err_log, $err_txt.PHP_EOL);
        fclose($err_log);
    }

}

?>
