<?php
/* 
	name: m_chargify_products
	date: 2019-01-11
	auth: VVenning
	desc: limited CRUD for chargify_products table.  Create, Update, Retrieve, Deactivate


//  2019-03-11 1153 VVenning - These three should have already been declared
//  2019-03-11 1156 VVenning - Begin 31 new variables
//  2019-03-12 1316 VVenning - Added processing for 31 new columns - A
//  2019-03-12 1316 VVenning - Added processing for 31 new columns - B

*/

class m_chargify_products
{
    public $id;
    public $event_id;
    public $customer_id;
    public $product_id;
    public $subdomain;
    public $created;

//  2019-03-11 1153 VVenning - These three should have already been declared
    public $product_name;
    public $product_family_id;
    public $product_family_name;

//  2019-03-11 1156 VVenning - Begin 31 new variables
    public $handle;
    public $description;
    public $accounting_code;
    public $request_credit_card;
    public $expiration_interval;
    public $expiration_interval_unit;
    public $created_at;
    public $updated_at;
    public $price_in_cents;
    public $interval;
    public $interval_unit;
    public $initial_charge_in_cents;
    public $trial_price_in_cents;
    public $trial_interval;
    public $trial_interval_unit;
    public $archived_at;
    public $require_credit_card;
    public $return_params;
    public $taxable;
    public $return_url;
    public $update_return_url;
    public $tax_code;
    public $initial_charge_after_trial;
    public $version_number;
    public $update_return_params;
    public $price_point_id;
    public $price_point_handle;    

    public $product_family_description;
    public $product_family_handle;
    public $product_family_accounting_code;
    public $public_signup_pages_id;
    public $public_signup_pages_return_url;
    public $public_signup_pages_return_params;
    public $public_signup_pages_url;
//  2019-03-11 1156 VVenning - End 31 new variables

    public $arr_products;
    public $last_inserted_id;
    public $err_log;
    public $database_error;

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
//  desc: select_all for chargify_products table
    public function select_all()
    {
        $sql  = "SELECT * FROM ".CHARGIFY_PRODUCTS_TABLE;

        $stmt = $this->conn->prepare($sql);

        if($stmt)
        {
            if(!$stmt->execute())
            {
                $this->log_database_error("File: m_chargify_products.php.  Message: Execute failed for function select_all().  MySQL_Error: ".$stmt->error);
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
            $this->log_database_error("File: m_chargify_products.php.  Message: Prepare failed for function select_all().");
        }
    }


//	name: select_by_id
//  date: 2019-01-11
//  auth: VVenning
//  desc: select_by_id for chargify_products table
	public function select_by_id($id=null)
	{
        $sql  = "SELECT * FROM ".CHARGIFY_PRODUCTS_TABLE." ";

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
                $this->log_database_error("File: m_chargify_products.php.  Message: Execute failed for function select_by_id().  MySQL_Error: ".$stmt->error);
            } 
            else
            {
                $res = $stmt->get_result();
                while ($row = $res->fetch_assoc())
                {
                    $data[] = $row;
                }
            }
            return $data;
            $stmt->close();
        }
        else
        {
            $this->log_database_error("File: m_chargify_products.php.  Message: Prepare failed for function select_by_id().");
        }
	}


//  name: select_by_product_id
//  date: 2019-01-11
//  auth: VVenning
//  desc: select_by_event_id for chargify_products table
    public function select_by_product_id($product_id=null)
    {
        $sql  = "SELECT * FROM ".CHARGIFY_PRODUCTS_TABLE." ";

        if($product_id == null)
        {
            $product_id = $this->product_id;
        }

        if($product_id == null)
        {
            return false;
        }
        else
        {
            $sql .= "WHERE product_id = ? ";
        }
        
        $stmt = $this->conn->prepare($sql);

        if($stmt)
        {
            $stmt->bind_param("i", $product_id);

            if(!$stmt->execute())
            {
                $this->log_database_error("File: m_chargify_products.php.  Message: Execute failed for function select_by_product_id().  MySQL_Error: ".$stmt->error);
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
            $this->log_database_error("File: m_chargify_products.php.  Message: Prepare failed for function select_by_product_id().");
        }

    }

//	name: insert
//	date: 2019-01-11
//	auth: VVenning
//	desc: insert for chargify_products table
	public function insert()
	{
	//  if can't open file, log

        $this->database_error = null;
    //  2019-03-12 1316 VVenning - Added processing for 31 new columns - A
        $stmt = $this->conn->prepare("INSERT INTO ".CHARGIFY_PRODUCTS_TABLE." (`customer_id`, `event_id`, `product_id`, `product_name`, `handle`, `description`, `accounting_code`, `request_credit_card`, `expiration_interval`, `expiration_interval_unit`, `created_at`, `updated_at`, `price_in_cents`, `interval`, `interval_unit`, `initial_charge_in_cents`, `trial_price_in_cents`, `trial_interval`, `trial_interval_unit`, `archived_at`, `require_credit_card`, `return_params`, `taxable`, `update_return_url`, `tax_code`, `initial_charge_after_trial`, `version_number`, `update_return_params`, `product_family_id`, `product_family_name`, `product_family_description`, `product_family_handle`, `product_family_accounting_code`, `public_signup_pages_id`, `public_signup_pages_return_url`, `public_signup_pages_return_params`, `public_signup_pages_url`, `created`, `return_url`,`price_point_id`, `price_point_handle`) 
            VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?)" );


        if($stmt)
        {
            $this->created = date("Y-m-d h:i:s", time());

    //  2019-03-12 1316 VVenning - Added processing for 31 new columns - B
            $stmt->bind_param("iiisssssssssiisiiisssssssisssssssssssssss", $this->customer_id, $this->event_id, $this->product_id, $this->product_name, $this->handle, $this->description, $this->accounting_code, $this->request_credit_card, $this->expiration_interval, $this->expiration_interval_unit, $this->created_at, $this->updated_at, $this->price_in_cents, $this->interval, $this->interval_unit, $this->initial_charge_in_cents, $this->trial_price_in_cents, $this->trial_interval, $this->trial_interval_unit, $this->archived_at, $this->require_credit_card, $this->return_params, $this->taxable, $this->update_return_url, $this->tax_code, $this->initial_charge_after_trial, $this->version_number, $this->update_return_params, $this->product_family_id, $this->product_family_name, $this->product_family_description, $this->product_family_handle, $this->product_family_accounting_code, $this->public_signup_pages_id, $this->public_signup_pages_return_url, $this->public_signup_pages_return_params, $this->public_signup_pages_url, $this->created, $this->return_url, $this->price_point_id,  $this->price_point_handle);

            if(!$stmt->execute())
            {
                $this->log_database_error("File: m_chargify_products.php.  Message: Execute failed for function insert().  MySQL_Error: ".$stmt->error);
            } 
            else
            {  
                $this->last_inserted_id = $this->conn->insert_id;
            }
            $stmt->close();
        }
        else
        {
            $this->log_database_error("File: m_chargify_products.php.  Message: Prepare failed for function insert().");
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
        $log_prefix = date("Y-m-d_", time());
        $the_time   = date("Y-m-d h:i:s", time());
        $err_log    = fopen("logs/".$log_prefix."DatabaseErrLog.txt", "a") or die("Unable to open file!");
        $err_txt    = $the_time." Table: ".CHARGIFY_PRODUCTS_TABLE." - ";
        $err_txt   .= $msg;
        $err_txt   .= "  MySQL_Error: (" . $this->conn->errno . ") " . $this->conn->error;
        fwrite($err_log, $err_txt.PHP_EOL);
        fclose($this->err_log);
    }
}

?>
