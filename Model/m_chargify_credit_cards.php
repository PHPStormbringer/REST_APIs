<?php
/* 
	name: m_chargify_credit_cards
	date: 2019-03-11
	auth: VVenning
	desc: limited CRUD for chargify_credit_cards table.  Create, Update, Retrieve, Deactivate

*/

class m_chargify_credit_cards
{
    public $id;
    public $customer_id;
    public $event_id;
    public $credit_cards_id;
    public $firstname;
    public $lastname;
    public $masked_card_number;
    public $card_type;
    public $expiration_month;
    public $expiration_year;
    public $current_vault;
    public $vault_token;
    public $billing_address;
    public $billing_address_2;
    public $billing_city;
    public $billing_state;
    public $billing_zip;
    public $billing_country;
    public $customer_vault_token;
    public $payment_type;
    public $disabled;
    public $created;

    public $arr_credit_cards;
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
//  date: 2019-03-11
//  auth: VVenning
//  desc: select_all for chargify_credit_cards table
    public function select_all()
    {
        $sql  = "SELECT * FROM ".CHARGIFY_CREDIT_CARDS_TABLE;

        $stmt = $this->conn->prepare($sql);

        if($stmt)
        {
            if(!$stmt->execute())
            {
                $this->log_database_error("File: m_chargify_credit_cards.php.  Message: Execute failed for function select_all().  MySQL_Error: ".$stmt->error);
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
            $this->log_database_error("File: m_chargify_credit_cards.php.  Message: Prepare failed for function select_all()");
        }
    }


//	name: select_by_id
//  date: 2019-03-11
//  auth: VVenning
//  desc: select_by_id for chargify_credit_cards table
	public function select_by_id($id=null)
	{
        $sql  = "SELECT * FROM ".CHARGIFY_CREDIT_CARDS_TABLE." ";

        if($id == null) { $id = $this->id; }
        if($id == null) { return false;    } else { $sql .= "WHERE id = ? "; }
        
        $stmt = $this->conn->prepare($sql);

        if($stmt)
        {
            $stmt->bind_param("i", $id);

            if(!$stmt->execute())
            {
                $this->log_database_error("File: m_chargify_credit_cards.php.  Message: Execute failed for function select_by_id().  MySQL_Error: ".$stmt->error);
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
            $this->log_database_error("File: m_chargify_credit_cards.php.  Message: Prepare failed for function select_by_id()");
        }
	}


//  name: select_by_credit_cards_id
//  date: 2019-03-11
//  auth: VVenning
//  desc: select_by_event_id for chargify_credit_cards table
    public function select_by_credit_cards_id($credit_cards_id=null)
    {
        $sql  = "SELECT * FROM ".CHARGIFY_CREDIT_CARDS_TABLE." ";

        if($credit_cards_id == null)
        {
            $credit_cards_id = $this->credit_cards_id;
        }

        if($credit_cards_id == null)
        {
            return false;
        }
        else
        {
            $sql .= "WHERE credit_cards_id = ? ";
        }
        
        $stmt = $this->conn->prepare($sql);

        if($stmt)
        {
            $stmt->bind_param("i", $credit_cards_id);

            if(!$stmt->execute())
            {
                $this->log_database_error("File: m_chargify_credit_cards.php.  Message: Execute failed for function select_by_credit_cards_id().  MySQL_Error: ".$stmt->error);
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
            $this->log_database_error("File: m_chargify_credit_cards.php.  Message: Prepare failed for function select_by_credit_cards_id()");
        }

    }

//	name: insert
//	date: 2019-03-11
//	auth: VVenning
//	desc: insert for chargify_credit_cards table
	public function insert()
	{
        $this->database_error = null;

        $stmt = $this->conn->prepare("INSERT INTO ".CHARGIFY_CREDIT_CARDS_TABLE." (customer_id, event_id, credit_card_id, first_name, last_name, masked_card_number, card_type, expiration_month, expiration_year, current_vault, vault_token, billing_address, billing_address_2, billing_city, billing_state, billing_zip, billing_country, customer_vault_token, payment_type, disabled, created) VALUES (?,?,?, ?,?,?, ?,?,?, ?,?,?, ?,?,?, ?,?,?, ?,?,?)" );

        if($stmt)
        {
            $this->created = date("Y-m-d h:i:s", time());

            $stmt->bind_param("iiissssssssssssssssss", $this->customer_id, $this->event_id, $this->credit_cards_id, $this->firstname, $this->lastname, $this->masked_card_number, $this->card_type, $this->expiration_month, $this->expiration_year, $this->current_vault, $this->vault_token, $this->billing_address, $this->billing_address_2, $this->billing_city, $this->billing_state, $this->billing_zip, $this->billing_country, $this->customer_vault_token, $this->payment_type, $this->disabled, $this->created);

            if(!$stmt->execute())
            {
                $this->log_database_error("File: m_chargify_credit_cards.php.  Message: Execute failed for function insert().  MySQL_Error: ".$stmt->error);
            } 
            else
            {
                $this->last_inserted_id = $this->conn->insert_id;
            }
            $stmt->close();
        }
        else
        {
            $this->log_database_error("File: m_chargify_credit_cards.php.  Message: Prepare failed for function insert().");
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
        $err_log = fopen("logs/DatabaseErrLog.txt", "a") or die("Unable to open file!");
        $err_txt  = $the_time." Table:".CHARGIFY_CREDIT_CARDS_TABLE." - ";
        $err_txt .= $msg;
        $err_txt .= ": (" . $this->conn->errno . ") " . $this->conn->error;
        fwrite($err_log, $err_txt.PHP_EOL);
        fclose($err_log);
    }
}

?>
