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
    public $trial_started_at;
    public $trial_ended_at;
    public $activated_at;
    public $created_at;
    public $updated_at;
    public $expires_at;
    public $current_period_ends_at;
    public $next_assessment_at;
    public $canceled_at;
    public $cancellation_message;
    public $next_product_id;
    public $cancel_at_end_of_period;
    public $payment_collection_method;
    public $snap_day;
    public $cancellation_method;
    public $receives_invoice_emails;
    public $current_period_started_at;
    public $previous_state;
    public $signup_payment_id;
    public $signup_revenue;
    public $delayed_cancel_at;
    public $coupon_code;
    public $total_revenue_in_cents;
    public $product_price_in_cents;
    public $product_version_number;
    public $payment_type;
    public $referral_code;
    public $coupon_use_count;
    public $coupon_uses_allowed;
    public $reason_code;
    public $automatically_resume_at;
    public $offer_id;
    public $payer_id;
    public $referred_by;
    public $product_price_point_id;
    public $next_product_price_point_id;



    public $arr_subscriptions;
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
                $this->log_database_error("File: m_chargify_subscriptions.php.  Message: Execute failed for function select_all().  MySQL_Error: ".$stmt->error);
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
            $this->log_database_error("File: m_chargify_subscriptions.php.  Message: Prepare failed for function select_all().");
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
                $this->log_database_error("File: m_chargify_subscriptions.php.  Message: Execute failed for function select_by_id().  MySQL_Error: ".$stmt->error);
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
            $this->log_database_error("File: m_chargify_subscriptions.php.  Message: Prepare failed for function select_by_id().");
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
                $this->log_database_error("File: m_chargify_subscriptions.php.  Message: Execute failed for function select_by_subscription_id().  MySQL_Error: ".$stmt->error);
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
            $this->log_database_error("File: m_chargify_subscriptions.php.  Message: Prepare failed for function select_by_subscription_id().");
        }

    }

//	name: insert
//	date: 2019-01-11
//	auth: VVenning
//	desc: insert for chargify_subscriptions table
	public function insert()
	{
        $this->database_error = null;

        $stmt = $this->conn->prepare("INSERT INTO ".CHARGIFY_SUBSCRIPTIONS_TABLE." (`customer_id`, `event_id`, `subscription_id`, `state`, `balance_in_cents`, `created`, `trial_started_at`, `trial_ended_at`, `activated_at`, `created_at`, `updated_at`, `expires_at`, `current_period_ends_at`, `next_assessment_at`, `canceled_at`, `cancellation_message`, `next_product_id`, `cancel_at_end_of_period`, `payment_collection_method`, `snap_day`, `cancellation_method`, `receives_invoice_emails`, `current_period_started_at`, `previous_state`, `signup_payment_id`, `signup_revenue`, `delayed_cancel_at`, `coupon_code`, `total_revenue_in_cents`, `product_price_in_cents`, `product_version_number`, `payment_type`, `referral_code`, `coupon_use_count`, `coupon_uses_allowed`, `reason_code`, `automatically_resume_at`, `offer_id`, `payer_id`, `referred_by`, `product_price_point_id`, `next_product_price_point_id`) 
            VALUES (?,?,?,?, ?,?,?,?, ?,?,?,?, ?,?,?,?, ?,?,?,?, ?,?,?,?, ?,?,?,?, ?,?,?,?, ?,?,?,?, ?,?,?,?, ?, ?)" );

// public $product_price_point_id;
// public $next_product_price_point_id;
         


        if($stmt)
        {
            $this->created = date("Y-m-d h:i:s", time());

            $stmt->bind_param("iiisissssssssssssssssissiisssiisssssssssis", $this->customer_id, $this->event_id, $this->subscription_id, $this->state, $this->balance_in_cents, $this->created, $this->trial_started_at, $this->trial_ended_at, $this->activated_at, $this->created_at, $this->updated_at, $this->expires_at, $this->current_period_ends_at, $this->next_assessment_at, $this->canceled_at, $this->cancellation_message, $this->next_product_id, $this->cancel_at_end_of_period, $this->payment_collection_method, $this->snap_day, $this->cancellation_method, $this->receives_invoice_emails, $this->current_period_started_at, $this->previous_state, $this->signup_payment_id, $this->signup_revenue, $this->delayed_cancel_at, $this->coupon_code, $this->total_revenue_in_cents, $this->product_price_in_cents, $this->product_version_number, $this->payment_type, $this->referral_code, $this->coupon_use_count, $this->coupon_uses_allowed, $this->reason_code, $this->automatically_resume_at, $this->offer_id, $this->payer_id, $this->referred_by, $this->product_price_point_id, $this->next_product_price_point_id );

            if(!$stmt->execute())
            {
                $this->log_database_error("File: m_chargify_subscriptions.php.  Message: Execute failed for function insert().  MySQL_Error: ".$stmt->error);
            } 
            else
            {
                $this->last_inserted_id = $this->conn->insert_id;
            }
            $stmt->close();
        }
        else
        {
            $this->log_database_error("File: m_chargify_subscriptions.php.  Message: Prepare failed for function insert().");
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
        $err_txt    = $the_time." Table: ".CHARGIFY_SUBSCRIPTIONS_TABLE." - ";
        $err_txt   .= $msg;
        $err_txt   .= "  MySQL_error: (" . $this->conn->errno . ") " . $this->conn->error;
        fwrite($err_log, $err_txt.PHP_EOL);
        fclose($this->err_log);
    }
}

?>
