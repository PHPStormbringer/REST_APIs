<?php
/* 
	name: m_chargify_transactions
	date: 2019-01-07
	auth: VVenning
	desc: limited CRUD for chargify_transactions table.  Create, Update, Retrieve, Deactivate

*/

class m_chargify_transactions
{
    public $id;
    public $customer_id;
    public $event_id;
    public $statement_id;
    public $transaction_id;
    public $subscription_id;
    public $type;
    public $kind;
    public $transaction_type;
    public $success;
    public $amount_in_cents;
    public $memo;
    public $created_at;
    public $starting_balance_in_cents;
    public $ending_balance_in_cents;
    public $gateway_transaction_id;
    public $gateway_order_id;
    public $payment_id;
    public $product_id;
    public $tax_id;
    public $component_id;
    public $item_name;
    public $period_range_start; 
    public $period_range_end;
    public $parent_id;
    
    public $card_number;
    public $card_expiration;
    public $card_type;
    public $price_point_id;
    public $price_point_handle;
    public $taxations;

    public $original_amount_in_cents;
    public $discount_amount_in_cents;
    public $taxable_amount_in_cents;
    public $refunded_amount_in_cents;
    
    public $created;

    public $err_file;

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
//  desc: select_all for chargify_transactions table
    public function select_all()
    {

        $sql  = "SELECT * FROM ".CHARGIFY_TRANSACTIONS_TABLE;

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
//  date: 2019-01-07
//  auth: VVenning
//  desc: select_by_id for chargify_transactions table
	public function select_by_id($id=null)
	{
        $sql  = "SELECT * FROM ".CHARGIFY_TRANSACTIONS_TABLE." ";

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
//  date: 2019-01-07
//  auth: VVenning
//  desc: select_by_statement_id for chargify_transactions table
    public function select_by_statement_id($statement_id=null)
    {
        $sql  = "SELECT * FROM ".CHARGIFY_TRANSACTIONS_TABLE." ";

        if($statement_id == null) { $statement_id = $this->statement_id; }
        if($statement_id == null) { return false; } else { $sql .= "WHERE statement_id = ? "; }
        
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

//  name: select_by_event_id
//  date: 2019-01-07
//  auth: VVenning
//  desc: select_by_event_id for chargify_transactions table
    public function select_by_event_id($event_id=null)
    {
        $sql  = "SELECT * FROM ".CHARGIFY_TRANSACTIONS_TABLE." ";

        if($event_id == null) { $event_id = $this->event_id; }
        if($event_id == null) { return false; } else { $sql .= "WHERE event_id = ? "; }
        
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
            $this->log_database_error("Prepare failed for select_by_event_id()");
        }

    }



//  name: select_by_transactions_id
//  date: 2019-01-07
//  auth: VVenning
//  desc: select_by_transactions_id for chargify_transactions table
    public function select_by_transactions_id($transactions_id=null)
    {
        $sql  = "SELECT * FROM ".CHARGIFY_TRANSACTIONS_TABLE." ";

        if($transactions_id == null) { $transactions_id = $this->transactions_id; }
        if($transactions_id == null) { return false; } else { $sql .= "WHERE transactions_id = ? "; }
        
        $stmt = $this->conn->prepare($sql);

        if($stmt)
        {
            $stmt->bind_param("i", $transactions_id);

            if(!$stmt->execute())
            {
                $this->log_database_error("Execute failed for select_by_transactions_id()");
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
            $this->log_database_error("Prepare failed for select_by_transactions_id()");
        }
    }

//	name: insert
//	date: 2019-01-07
//	auth: VVenning
//	desc: insert for chargify_transactions table
	public function insert()
	{
        $this->database_error = null;

        if ($this->statement_id == null)
        {
            return false;
        }

        mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);
        try{
            $stmt = $this->conn->prepare("INSERT INTO ".CHARGIFY_TRANSACTIONS_TABLE." (customer_id, event_id, statement_id, transaction_id, 
            subscription_id, type, kind, transaction_type, success,
            amount_in_cents, memo, created_at, starting_balance_in_cents, ending_balance_in_cents,
            gateway_used, gateway_transaction_id, gateway_order_id, payment_id, product_id,
            tax_id, component_id, item_name, period_range_start, period_range_end, 
            original_amount_in_cents, discount_amount_in_cents, taxable_amount_in_cents, refunded_amount_in_cents,
            parent_id, card_number, card_expiration, card_type, 
            price_point_id, price_point_handle,
            created) VALUES (?,?,?,?,?, ?,?,?,?,?, ?,?,?,?,?, ?,?,?,?,?, ?,?,?,?,? ,?,?,?,?,?, ?,?,?,?, ?)");


            if($stmt)
            {
                $this->created = date("Y-m-d h:i:s", time());
                $stmt->bind_param("iiiiissssissiisisiiiisssiiiiissssss", $this->customer_id, $this->event_id, $this->statement_id, $this->transaction_id, 
                $this->subscription_id, $this->type, $this->kind, $this->transaction_type, $this->success,
                $this->amount_in_cents, $this->memo, $this->created_at, $this->starting_balance_in_cents, $this->ending_balance_in_cents,
                $this->gateway_used, $this->gateway_transaction_id, $this->gateway_order_id, $this->payment_id, $this->product_id, 
                $this->tax_id, $this->component_id, $this->item_name, $this->period_range_start, $this->period_range_end, 
                $this->original_amount_in_cents, $this->discount_amount_in_cents, $this->taxable_amount_in_cents, $this->refunded_amount_in_cents,
                $this->parent_id, $this->card_number, $this->card_expiration, $this->card_type,
                $this->price_point_id, $this->price_point_handle,
                $this->created);

                if(!$stmt->execute())
                {
                    $this->log_database_error("Execute failed for insert()");
                } 
                else
                {
                    $this->last_inserted_id = $this->conn->insert_id;
    
                //  echo $this->last_inserted_id;
                }
                $stmt->close();
            }
            else
            {
                $this->log_database_error("Prepare failed for insert()");
            }

        } catch (mysqli_sql_exception $e) {

            $this->log_database_error($e->getMessage());
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
        $err_txt  = $the_time." ".CHARGIFY_TRANSACTIONS_TABLE." - ";
        $err_txt .= $msg;
        $err_txt .= ": (" . $this->conn->errno . ") " . $this->conn->error;
        fwrite($err_log, $err_txt.PHP_EOL);
        fclose($this->err_log);
    }
}

?>
