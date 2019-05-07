<?php
/* 
    name: m_clients
    date: 2018-05-30
    auth: VVenning
    desc: basic CRUD for clients table
//  2018-09-18 1003 VVenning - USe constant for clients table name
//  2018-12-01 1500 VVenning - FInally wrote body of function
//  2018-02-25 1400 VVenning - New function updateUserCount

*/
class m_clients implements databaseModel
{
	public $id;
	public $partner_id; //
	public $AAD_identifier;  // Azure Active Directory identifier for client
	public $name; //
	public $email; //
	public $account_type;
	public $active;
	public $dont_send_email;
	public $demo;
	public $is_real;
	public $is_eva;
	public $is_dwba;
	public $user_count;
	public $phishing_count;
	public $display_ra_org;
	public $display_policy;
	public $link;
	public $logo;
	public $logo_dir;
	public $display_intro_video;
	public $admin_account;
	public $user_account;
	public $details;
	public $domain;
	public $no_of_employees;
	public $dwba_date;
	public $file_key;
	public $last_login;
	public $risk_assessment_status;
	public $created;
	public $modified;
	public $moodle_course_id;
	public $moodle_course_name;
	public $pax8_partner_id;
	public $pax8_customer_id;
	public $pax8_product_code;
	public $pax8_subscription_id;
	public $custom_policies;
	public $demo_eva;
	public $total_breaches;
    public $auto_phish;
    public $white_list;
    public $one_time_campaign;
    public $acknowledge_flag;

    public $conn;

    public $last_inserted_id;

	
//	name: select;
//	date: 2018-05-29
//	auth: VVenning
//	desc: slect for clients table
	public function select()
	{
		//
	}


//	name: select_all
//	date: 2018-08-04
//	auth: VVenning
//	desc: select_all for clients table
	public function select_all($active=null, $dont_send_email=null)
	{
    //  2018-09-18 1003 VVenning - USe constant for clients table name
        $sql  = "SELECT * FROM ".CLIENTS_TABLE." ";

        if($active == null && $dont_send_email == null)
        {
        //  No WHERE clause
        }
        else
        {
            $sql .= "WHERE ";
        }

        if($active != null)
        {
            $sql .= "active = ? ";
        }

        if($dont_send_email === null )
        {
            //do nothing
        }  
        elseif($dont_send_email !== null && $active !== null)
        {
            $sql .= "AND dont_send_email = ? ";
        }
        else
        {
            $sql .= "dont_send_email = ? ";
        }

        $stmt = $this->conn->prepare($sql);

        if($stmt)
        {

            if    ($active === null && dont_send_email === null)
            {
            //  bind no params
            }
            elseif($active !== null && $dont_send_email !== null)
            {
                $stmt->bind_param("ii", $active, $dont_send_email);
            }
            elseif($active !== null && $dont_send_email === null)
            {
                $stmt->bind_param("i", $active);
            }
            elseif($active === null && $dont_send_email !== null)
            {
                $stmt->bind_param("i", $dont_send_email);
            }

            $stmt->execute();

            $res = $stmt->get_result();

            $i = 0;
            while ($row = $res->fetch_assoc()){
          
                $data[] = $row;

                $i++;
            }
            
            return $data;
            
            $stmt->close();

            
        }
        else
        {
           $this->myfile = fopen("DatabaseErrLog.txt", "a") or die("Unable to open file!");

            $txt = "Failed prepare statement to select all user records. - ".date("Y-d-m h:i:s", time()).PHP_EOL;

            fwrite($this->myfile, $txt);
            
            fclose($this->myfile);
        }
	}

//  name: select_byID
//  date: 2018-05-29
//  auth: VVenning
//  desc: select byt unique id for clients table
//  param: unique id
//  2018-12-01 1500 VVenning - FInally wrote body of function
    public function select_byID($id)
    {
        $stmt = $this->conn->prepare("SELECT * FROM ".CLIENTS_TABLE." WHERE id = ?");

        if(!$stmt)
        {            
            $this->myfile = fopen("DatabaseErrLog.txt", "a") or die("Unable to open file!");

            $txt = "Failed prepare statement to select client record by id - ".$id." - ".date("Y-d-m h:i:s", time()).PHP_EOL;
            
            fwrite($this->myfile, $txt);
            fclose($this->myfile);
        }
        else
        {
            $data = "";

            $stmt->bind_param("s", $id);

            if (!$stmt->execute()) 
            {
                $this->myfile = fopen("DatabaseErrLog.txt", "a") or die("Unable to open file!");

                $txt .= "Execute failed: Statement to select client record by id - (".$id . ") - Errno: ";
//              $txt .= $stmt->errno . " " . $stmt->error . " " . date("Y-d-m h:i:s", time()).PHP_EOL;
                
                fwrite($this->myfile, $txt);
                fclose($this->myfile);
            }

            $res = $stmt->get_result();

            $i = 0;
            while ($row = $res->fetch_assoc()){
          
                $data[] = $row;

                $i++;
            }
            return isset($data[0]) ? $data[0] : null;
            
            $stmt->close();           
        }
    }


//	name: select_by_ActiveDirectoryID
//	date: 2018-06-11
//	auth: VVenning
//	desc: select by ActiveDirectoryID
//	param: unique id
	public function select_by_ActiveDirectoryID($AAD_identifier)
	{
        $stmt = $this->conn->prepare("SELECT * FROM ".CLIENTS_TABLE." WHERE AAD_identifier = ?");

        if(!$stmt)
        {            
            $this->myfile = fopen("DatabaseErrLog.txt", "a") or die("Unable to open file!");

            $txt = "Failed prepare statement to select client record by AAD_identifier - ".$AAD_identifier." - ".date("Y-d-m h:i:s", time()).PHP_EOL;
			
            fwrite($this->myfile, $txt);
            fclose($this->myfile);
        }
        else
        {
            $data = "";

            $stmt->bind_param("s", $AAD_identifier);

            if (!$stmt->execute()) 
            {
				$this->myfile = fopen("DatabaseErrLog.txt", "a") or die("Unable to open file!");

				$txt .= "Execute failed: Statement to select client record by AAD_identifier - (".$AAD_identifier . ") - Errno: ";
//				$txt .= $stmt->errno . " " . $stmt->error . " " . date("Y-d-m h:i:s", time()).PHP_EOL;
				
				fwrite($this->myfile, $txt);
				fclose($this->myfile);
            }

            $res = $stmt->get_result();

            $i = 0;
            while ($row = $res->fetch_assoc()){
          
                $data[] = $row;

                $i++;
            }
            return isset($data[0]) ? $data[0] : null;
            
            $stmt->close();           
        }
	}


//	name: insert
//	date: 2018-05-29
//	auth: VVenning
//	desc: insert for clients table
	public function insert()
	{
        $stmt = $this->conn->prepare("INSERT INTO ".CLIENTS_TABLE." (partner_id, name, email, account_type, moodle_course_id, moodle_course_name, created) VALUES (?, ?, ?, ?, ?, ?, ?)");

        if($stmt)
        {
        	$this->created = date("Y-m-d h:i:s", time());

            $stmt->bind_param("isssiss", $this->partner_id, $this->name, $this->email, $this->account_type, $this->moodle_course_id, $this->moodle_course_name, $this->created);

            $stmt->execute();
            $this->last_inserted_id = $this->conn->insert_id;

            $stmt->close();
        }
        else
        {
           $this->myfile = fopen("PartnerSignupDatabaseErrLog.txt", "a") or die("Unable to open file!");

            $txt = "Failed to insert client record: ". $this->name." - ".$this->email.time().PHP_EOL;
            fwrite($this->myfile, $txt);
            
            fclose($this->myfile);
        }
	}


//	name: deactivate
//	date: 2018-05-29
//	auth: VVenning
//	desc: make a client inactive.  This changes a single value
	public function deactivate()
	{
		//
	}


//	name: delete
//	date: 2018-05-29
//	auth: VVenning
//	desc: delete for clients table.  WOn;t be used except in emergencies.  We deactivate, which is an update process
	public function delete()
	{
		//
	}

//  name: updateUserCount
//  date: 2018-02-25
//  auth: VVenning
//  desc: updateUserCount
//  params: client_id, user_count
//  2018-02-25 1400 VVenning - New function updateUserCount
public function updateUserCount($client_id, $count)
{
    if( is_numeric($count) && is_numeric($client_id) )
    {
        $stmt = $this->conn->prepare("UPDATE ".CLIENTS_TABLE." SET user_count = ? WHERE id = ?");

        if($stmt)
        {
            $data = "";

            $stmt->bind_param("ii", $count, $client_id);

            if($stmt->execute())
            {
                return true;
            }
            else
            {
                return false;
            }
        }
        else
        {
            $this->myfile = fopen("DatabaseErrLog.txt", "a") or die("Unable to open file!");

            $txt = date("Y-d-m h:i:s", time()).": Failed prepare statement to update user_count by client_id - ".$client_id.PHP_EOL;
            fwrite($this->myfile, $txt);
            
            fclose($this->myfile);
            return false;
        }
    }
    else
    {
        $this->myfile = fopen("DatabaseErrLog.txt", "a") or die("Unable to open file!");

        $txt  = date("Y-d-m h:i:s", time());
        $txt .= ": Attempted to update user_count by non numeric id or count.  client id: ".$client_id." count: ".$count.PHP_EOL;
        fwrite($this->myfile, $txt);
        fclose($this->myfile);
        return false;
    }
}

    /**
     *  Udate for clients table.
     *  Usage example :
     *  $clients =  new m_clients();
     *  $clients->name = 'name';
     *  $clients->id = 1;
     *  $clients->update();
     *
     * date: 2018-05-29, 2018-09-25
     * auth: VVenning, Ruiz
     * @return query
     */
    public function update()
    {
        $sql_statement = "UPDATE ".CLIENTS_TABLE." SET ";
        $type          = "";
        $params        = array();
        $sql_stmt_arr  = array();

        $this->prepareForUpdate($sql_stmt_arr, $params, $type, 'i',$this->partner_id, 'partner_id');
        $this->prepareForUpdate($sql_stmt_arr, $params, $type, 's',$this->AAD_identifier, 'AAD_identifier');
        $this->prepareForUpdate($sql_stmt_arr, $params, $type, 's',$this->name, 'name');
        $this->prepareForUpdate($sql_stmt_arr, $params, $type, 's',$this->email, 'email');
        $this->prepareForUpdate($sql_stmt_arr, $params, $type, 's',$this->account_type, 'account_type');
        $this->prepareForUpdate($sql_stmt_arr, $params, $type, 'i',$this->active, 'active');
        $this->prepareForUpdate($sql_stmt_arr, $params, $type, 'i',$this->dont_send_email, 'dont_send_email');
        $this->prepareForUpdate($sql_stmt_arr, $params, $type, 'i',$this->demo, 'demo');
        $this->prepareForUpdate($sql_stmt_arr, $params, $type, 'i',$this->is_real, 'is_real');
        $this->prepareForUpdate($sql_stmt_arr, $params, $type, 'i',$this->user_count, 'user_count');
        $this->prepareForUpdate($sql_stmt_arr, $params, $type, 'i',$this->phishing_count, 'phishing_count');
        $this->prepareForUpdate($sql_stmt_arr, $params, $type, 's',$this->display_ra_org, 'display_ra_org');
        $this->prepareForUpdate($sql_stmt_arr, $params, $type, 'i',$this->display_policy, 'display_policy');
        $this->prepareForUpdate($sql_stmt_arr, $params, $type, 's',$this->link, 'link');
        $this->prepareForUpdate($sql_stmt_arr, $params, $type, 's',$this->logo, 'logo');
        $this->prepareForUpdate($sql_stmt_arr, $params, $type, 's',$this->logo_dir, 'logo_dir');
        $this->prepareForUpdate($sql_stmt_arr, $params, $type, 'i',$this->display_intro_video, 'display_intro_video');
       // $this->prepareForUpdate($sql_stmt_arr, $params, $type, 'i',$this->custom_policies, 'custom_policies');
        $this->prepareForUpdate($sql_stmt_arr, $params, $type, 's',$this->admin_account, 'admin_account');
        $this->prepareForUpdate($sql_stmt_arr, $params, $type, 's',$this->user_account, 'user_account');
        $this->prepareForUpdate($sql_stmt_arr, $params, $type, 's',$this->details, 'details');
        $this->prepareForUpdate($sql_stmt_arr, $params, $type, 's',$this->domain, 'domain');
        $this->prepareForUpdate($sql_stmt_arr, $params, $type, 'i',$this->no_of_employees, 'no_of_employees');
        $this->prepareForUpdate($sql_stmt_arr, $params, $type, 's',$this->dwba_date, 'dwba_date');
        $this->prepareForUpdate($sql_stmt_arr, $params, $type, 's',$this->file_key, 'file_key');
        $this->prepareForUpdate($sql_stmt_arr, $params, $type, 's',$this->last_login, 'last_login');
        $this->prepareForUpdate($sql_stmt_arr, $params, $type, 's',$this->risk_assessment_status, 'risk_assessment_status');
        $this->prepareForUpdate($sql_stmt_arr, $params, $type, 's',$this->moodle_course_id, 'moodle_course_id');
        $this->prepareForUpdate($sql_stmt_arr, $params, $type, 's',$this->moodle_course_name, 'moodle_course_name');
        $this->prepareForUpdate($sql_stmt_arr, $params, $type, 's',$this->pax8_partner_id, 'pax8_partner_id');
        $this->prepareForUpdate($sql_stmt_arr, $params, $type, 's',$this->pax8_customer_id, 'pax8_customer_id');
        $this->prepareForUpdate($sql_stmt_arr, $params, $type, 's',$this->pax8_product_code, 'pax8_product_code');
        $this->prepareForUpdate($sql_stmt_arr, $params, $type, 's',$this->pax8_subscription_id, 'pax8_subscription_id');
       // $this->prepareForUpdate($sql_stmt_arr, $params, $type, 'i',$this->demo_eva, 'demo_eva');
       // $this->prepareForUpdate($sql_stmt_arr, $params, $type, 'i',$this->total_breaches, 'total_breaches');
        $this->prepareForUpdate($sql_stmt_arr, $params, $type, 'i',$this->is_eva, 'is_eva');
        $this->prepareForUpdate($sql_stmt_arr, $params, $type, 'i',$this->is_dwba, 'is_dwba');
       // $this->prepareForUpdate($sql_stmt_arr, $params, $type, 'i',$this->auto_phish, 'auto_phish');
       // $this->prepareForUpdate($sql_stmt_arr, $params, $type, 'i',$this->white_list, 'white_list');
       // $this->prepareForUpdate($sql_stmt_arr, $params, $type, 'i',$this->one_time_campaign, 'one_time_campaign');
       // $this->prepareForUpdate($sql_stmt_arr, $params, $type, 'i',$this->acknowledge_flag, 'acknowledge_flag');

        // add modified date
        array_push($sql_stmt_arr, " modified = ? ");
        array_push($params,date("Y-m-d H:i:s"));
        $type  .= 's';

        if(count($sql_stmt_arr) >= 1){
            $sql_statement   .=  implode(',',$sql_stmt_arr) ;
        }

        // add the where statement
        $sql_statement   .= " WHERE id = ?";
        $type            .= 'i';
        array_push($params,$this->id);

        return $this->executeQuery( $sql_statement, $type, $params, true );

    }

    /**
     * prepare part of the query column by reference
     * @return void
     */
    private function prepareForUpdate(&$sql_stmt_arr, &$params, &$type,$update_type, $value, $column){
        //if($value != null && strlen(trim($value)) > 0 ){
         if(!empty($value) ||  $value  === 0 ){
            array_push($sql_stmt_arr, " $column = ?");
            array_push($params,$value);
            $type  .= $update_type;
        }
    }

    /**
     * Create new client specifically for Pax8
     * @return query
     */
    public function createPax8Client()
    {

        $columns_arr   = [];
        $type          = [];
        $param_arr     = [];
        $placeholder   = [];



        //columns that don't depend on API parameters, values are calculated or have a set of default
        $this->user_count           = 5000;
        $this->is_real              = 1;
        $this->is_eva               = 1;
        $this->active               = 1;
        $this->account_type         = 'BPP'; // temporary default value
        $this->user_count           = 1;
        $this->admin_account        = randomString(10);
        $this->user_account         = randomString(10);
        $this->file_key             = randomString(10);
        $this->moodle_course_id     = 5;
        $this->moodle_course_name   = 'SEC-102';
        $this->pax8_partner_id      = '';
        $this->pax8_customer_id     = '';
        $this->pax8_product_code    = 'BSNPv2'; // temporary default value
        $this->pax8_subscription_id = '';

        // columns that depends on API parameters
        $this->prepareForInsert($placeholder, $columns_arr, $type, $param_arr, 'i',$this->partner_id,'partner_id' );
        $this->prepareForInsert($placeholder, $columns_arr, $type, $param_arr, 's',$this->name,'name' );
        $this->prepareForInsert($placeholder, $columns_arr, $type, $param_arr, 's',$this->email,'email' );

        //columns that don't depend on API parameters, values are calculated or have a set of default
        $this->prepareForInsert($placeholder, $columns_arr, $type, $param_arr, 'i',$this->is_eva,'is_eva' );
        $this->prepareForInsert($placeholder, $columns_arr, $type, $param_arr, 'i',$this->is_real,'is_real' );
        $this->prepareForInsert($placeholder, $columns_arr, $type, $param_arr, 'i',$this->active,'active' );
        $this->prepareForInsert($placeholder, $columns_arr, $type, $param_arr, 's',$this->account_type,'account_type' );
        $this->prepareForInsert($placeholder, $columns_arr, $type, $param_arr, 'i',$this->user_count,'user_count' );
        $this->prepareForInsert($placeholder, $columns_arr, $type, $param_arr, 's',$this->admin_account,'admin_account' );
        $this->prepareForInsert($placeholder, $columns_arr, $type, $param_arr, 's',$this->user_account,'user_account' );
        $this->prepareForInsert($placeholder, $columns_arr, $type, $param_arr, 's',$this->file_key,'file_key' );
        $this->prepareForInsert($placeholder, $columns_arr, $type, $param_arr, 'i',$this->moodle_course_id,'moodle_course_id' );
        $this->prepareForInsert($placeholder, $columns_arr, $type, $param_arr, 's',$this->moodle_course_name,'moodle_course_name' );
        $this->prepareForInsert($placeholder, $columns_arr, $type, $param_arr, 's',$this->pax8_partner_id,'pax8_partner_id' );
        $this->prepareForInsert($placeholder, $columns_arr, $type, $param_arr, 's',$this->pax8_customer_id,'pax8_customer_id' );
        $this->prepareForInsert($placeholder, $columns_arr, $type, $param_arr, 's',$this->pax8_product_code,'pax8_product_code' );
        $this->prepareForInsert($placeholder, $columns_arr, $type, $param_arr, 's',$this->pax8_subscription_id,'pax8_subscription_id' );

        // created and modified dates
        $this->prepareForInsert($placeholder, $columns_arr, $type, $param_arr, 's',date( "Y-m-d H:i:s"),'created' );
        $this->prepareForInsert($placeholder, $columns_arr, $type, $param_arr, 's',date( "Y-m-d H:i:s"),'modified' );


        $sql_statement = "INSERT INTO ".CLIENTS_TABLE." ( ".implode(',',$columns_arr).") VALUES ( ".implode(',',$placeholder).")";

        $this->executeQuery( $sql_statement,implode('',$type), $param_arr, true );

        return $this->selectPax8ClientById($this->last_inserted_id);

    }
    /**
     * prepare part of the insert query column by reference
     * @return void
     */
    private function prepareForInsert(&$placeholder, &$columns_arr, &$type, &$param_arr, $insert_type, $value, $column ){
        if(trim($value) != null && strlen(trim($value)) > 0 ) {
            array_push($placeholder, '?');
            array_push($columns_arr, $column);
            array_push($type, $insert_type);
            array_push($param_arr, $value);
        }
    }

    /**
     * Get clients of PAX8 by Id
     * @return query
     */
    public function selectPax8ClientById($id)
    {
        $sql_statement = "SELECT c.* FROM ".CLIENTS_TABLE." AS c, ".PARTNERS_TABLE." AS p WHERE c.partner_id = p.id and p.distributor = 'PAX8' AND c.id = ?  ";
        return $this->executeQuery( $sql_statement,'i', array($id), false );
    }

    /**
     * Get clients of PAX8 by subscription id
     * @return query
     */
    public function selectPax8ClientBySubscriptionId($id)
    {
        $sql_statement = "SELECT c.* FROM ".CLIENTS_TABLE." AS c, ".PARTNERS_TABLE." AS p WHERE c.partner_id = p.id and p.distributor = 'PAX8' AND c.pax8_subscription_id = ?  ";
        return $this->executeQuery( $sql_statement,'i', array($id), false );
    }

    /**
     * Get clients of PAX8 by name
     * @param  $name
     * @param  $partner_id
     * @return query
     */
    public function selectPax8ClientByName($name, $partner_id)
    {
        $sql_statement = "SELECT c.* FROM ".CLIENTS_TABLE." AS c, ".PARTNERS_TABLE." AS p WHERE c.partner_id = p.id and p.distributor = 'PAX8' AND c.name = ? AND c.partner_id = ?  ";
        return $this->executeQuery( $sql_statement,'si', array($name,$partner_id), false );
    }


    /**
     * Get client of PAX8 by Multiple Ids
     * @return query
     */
    public function selectPax8ClientByMultipleIds($ids, $sort_column = 'id')
    {
        if(is_array($ids) && count($ids) <=0){
            return false;
        }
        $placeholders = implode(',',array_fill(0, count($ids), '?'));
        $type = implode(array_fill(0, count($ids), 'i'));

        $sql_statement = "SELECT c.* FROM ".CLIENTS_TABLE." AS c, ".PARTNERS_TABLE." AS p WHERE c.partner_id = p.id and p.distributor = 'PAX8' AND c.id IN ($placeholders) ORDER BY c.$sort_column";
        return $this->executeQuery( $sql_statement,$type, $ids, false );
    }

    /**
     * Get client of PAX8 by Multiple Ids
     * @return query
     */
    public function selectPax8ClientByMultipleSubscriptionIds($ids, $sort_column = 'pax_subscription_id')
    {
        if(is_array($ids) && count($ids) <=0){
            return false;
        }
        $placeholders = implode(',',array_fill(0, count($ids), '?'));
        $type = implode(array_fill(0, count($ids), 'i'));

        $sql_statement = "SELECT c.* FROM ".CLIENTS_TABLE." AS c, ".PARTNERS_TABLE." AS p WHERE c.partner_id = p.id and p.distributor = 'PAX8'AND c.pax8_subscription_id IN ($placeholders)  ORDER BY c.$sort_column";
        return $this->executeQuery( $sql_statement,$type, $ids, false );
    }

    /**
     * Get all clientss, default limit is 10 and default offset is 0
     *
     * @return query
     */
    public function selectAllPax8Client($offset,$max, $sort_column = 'id')
    {
        $sql_statement = "SELECT c.* FROM ".CLIENTS_TABLE." AS c, ".PARTNERS_TABLE." AS p WHERE c.partner_id = p.id and p.distributor = 'PAX8'  ORDER BY c.$sort_column LIMIT ?  OFFSET ?";
        return $this->executeQuery( $sql_statement,'ii', array($max, $offset), false );
    }

    /**
     * Get the last logo_dir and service_logo_dir
     *
     * @return query
     */
    public function getColumnMaxValue($column)
    {
        $sql_statement = "SELECT * FROM ".CLIENTS_TABLE." WHERE $column IS NOT NULL ORDER BY $column DESC LIMIT 1 ";
        return $this->executeQuery( $sql_statement,'', array(), false );
    }

    /**
     * This function will run a query
     *
     * @param  $sql_statement   (sql query)
     * @param  $type            (sssii)
     * @params $params          (should be array)
     * @params $close           (select = false, updaten insert,delete = true)
     *
     * @return arrays
     */
    private function executeQuery($sql_statement, $type, $params, $close){
        mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);
        try{
            $stmt = $this->conn->prepare($sql_statement);

            if(!empty($type) && !empty($params)){
                $stmt->bind_param($type, ...$params);
            }

            $stmt->execute();

            if($stmt){
                if($close){
                    $result = $this->conn->affected_rows;
                    if(strpos($sql_statement, 'INSERT') !== false){
                        $this->last_inserted_id = $this->conn->insert_id;
                    }

                } else {
                    $res    = $stmt->get_result();

                    if($res->num_rows > 1){
                        $result = [];
                        while ($obj = $res->fetch_object('m_clients')) {
                            array_push($result, $obj);
                        }
                    }else{
                        $result = $res->fetch_object('m_clients');
                    }
                }
                $stmt->close();
                return  $result;
            }

        } catch (mysqli_sql_exception $e) {
            if(isset($this->conn->log_file_path) && !empty($this->conn->log_file_path)){
                $file = $_SERVER['DOCUMENT_ROOT'].'/'.$this->conn->log_file_path;
            }else{
                $file = $_SERVER['DOCUMENT_ROOT'].'/logs/database_error.txt';
            }

            universal_log_it($file, "m_clients - ".$e->getMessage());

            return null;
        }

    }

}

?>