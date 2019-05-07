<?php
/* 
	name: users
	date: 
	auth: VVenning
	desc: basic CRUD for users table

*/
class m_users implements databaseModel
{
	public $id;
    public $password;
    public $password_old;
    public $first_name;
    public $last_name;
    public $email;
    public $screen_name;
    public $job_function;
    public $phone_number;
    public $phone_number_ext;
    public $cell_number;
	
    public $address;
    public $address2;
    public $city;
    public $state;
    public $zip;
    public $link;

    public $active;
    public $dont_send_email;
    public $last_login;
    public $role_id;
    public $client_id;
    public $partner_id;
    public $tag_id;
    public $tokenhash;


    public $created;
    public $modified;

    public $last_inserted_id;
    public $database_error;
    public $conn;

    public function __construct()
    {


    }


//	name: select
//	date: 
//	auth: VVenning
//	desc: select for users table
	public function select()
	{
		//
	}


//	name: select_all
//	date: 
//	auth: VVenning
//	desc: select_all for users table
	public function select_all($active=null, $dont_send_email=null)
	{

        $sql  = "SELECT * FROM ".USERS_TABLE." ";

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

	
//  name: select_custom
//  date: 
//  auth: VVenning
//  desc: custom select
    public function select_custom($x)
    {
        //
        if(stripos($x, "DROP") > 0 || stripos($x, "DELETE") > 0 || stripos($x, "UPDATE") > 0 || stripos($x, "TRUNCATE") > 0 || stripos($x, "ALTER") > 0 || stripos($x, "INSERT") > 0)
        {
            $this->myfile = fopen("DatabaseErrLog.txt", "a") or die("Unable to open file!");

            $txt = date("Y-d-m h:i:s", time()).": File: m_users.php.  Function: select_custom().  Message:  Certain words cannot be used with this this function. 'DROP', DELETE','UPDATE','TRUNCATE','ALTER','INSERT' not permitted.  sql: ".$x.PHP_EOL;

            fwrite($this->myfile, $txt);    
            fclose($this->myfile);
        }
        else
        {
            $stmt = $this->conn->prepare($x);

            if(!$stmt)
            {            
	            $this->myfile = fopen("DatabaseErrLog.txt", "a") or die("Unable to open file!");

	            $txt = "Prepare failed: (" . $this->conn->errno . ") " . $this->conn->error.PHP_EOL;

	            fwrite($this->myfile, $txt);    
	            fclose($this->myfile);
            }

            if($stmt)
            {
                $stmt->execute();

                $res = $stmt->get_result();

                $i = 0;
                while ($row = $res->fetch_assoc())
                {
                    $arrEmailAll[] = ($row);
                }
            }
        }

        return $arrEmailAll;
    }


/*	name: select_byID
//	date: 
//	auth: VVenning
//	desc: select one user by id
//	parm: unique id
*/  public function select_byID($id=null)
    {
        $id = $id ? $id : $this->id;

        if(is_numeric($id))
        {
            $stmt = $this->conn->prepare("SELECT * FROM ".USERS_TABLE." WHERE id = ?");

            if($stmt)
            {
                $data = "";

                $stmt->bind_param("i", $id);

                $stmt->execute();

                $res = $stmt->get_result();

                $data = $res->fetch_assoc();
                
                $stmt->close();
				
				return $data;
            }
            else
            {
                $this->myfile = fopen("DatabaseErrLog.txt", "a") or die("Unable to open file!");

                $txt = date("Y-d-m h:i:s", time()).": Failed prepare statement to select user record by id - ".$id.PHP_EOL;
                fwrite($this->myfile, $txt);
                
                fclose($this->myfile);
            }
        }
        else
        {
            $this->myfile = fopen("DatabaseErrLog.txt", "a") or die("Unable to open file!");

            $txt = date("Y-d-m h:i:s", time()).": Attempted to select user record by non numeric id - ".$id.PHP_EOL;
            fwrite($this->myfile, $txt);
            fclose($this->myfile);
        }
    }


//	name: select_by_email
//	date: 
//	auth: VVenning
//	desc: select_user by email.
//  data:  
	public function select_by_email($email=null, $active=null)
	{
		$email = $email ?: $this->email;

        if(!$active)
        {
        //  $stmt = $this->conn->prepare("SELECT * FROM users WHERE email = ? AND client_id = ?");
            $stmt = $this->conn->prepare("SELECT * FROM ".USERS_TABLE." WHERE email = ? ");
        }
        else
        {
        //  $stmt = $this->conn->prepare("SELECT * FROM users WHERE email = ? AND client_id = ? AND active = ?");
            $stmt = $this->conn->prepare("SELECT * FROM ".USERS_TABLE." WHERE email = ? AND active = ?");    
        }

        if($stmt)
        {
            $data = "";

            if(!$active)
            {
                $stmt->bind_param("s", $this->email);
            }
            else
            {
                $stmt->bind_param("si", $this->email, $active);
            }
            $stmt->execute();

			$res = $stmt->get_result();

		    $data = $res->fetch_assoc();

            $stmt->close();
		  
            return $data;
        }
        else
        {
           $this->myfile = fopen("DatabaseErrLog.txt", "a") or die("Unable to open file!");

            $txt = "Failed prepare statement to select user record by email - ".$this->email." - ".date("Y-d-m h:i:s", time()).PHP_EOL;

            fwrite($this->myfile, $txt);
            
            fclose($this->myfile);
        }
	}


//	name: select_by_email_client_id
//	date: 
//	auth: VVenning
//	desc: select_user by email.
//  data:  
	public function select_by_email_client_id($email=null, $client_id=null)
	{
		$email     = $email     ?: $this->email;
		$client_id = $client_id ?: $this->client_id;



        if(!client_id)
        {
            $stmt = $this->conn->prepare("SELECT * FROM ".USERS_TABLE." WHERE email = ? ");
        }
        else
        {
            $stmt = $this->conn->prepare("SELECT * FROM ".USERS_TABLE." WHERE email = ? AND client_id = ?");    
        }

        if($stmt)
        {
            $data = "";

            if(!$client_id)
            {
                $stmt->bind_param("s", $this->email);
            }
            else
            {
                $stmt->bind_param("si", $this->email, $client_id);
            }
            $stmt->execute();

			$res = $stmt->get_result();

            $data = $res->fetch_assoc();
			
            $stmt->close();
			return $data;
            
        }
        else
        {
           $this->myfile = fopen("DatabaseErrLog.txt", "a") or die("Unable to open file!");

            $txt = "Failed prepare statement to select user record by email - ".$this->email." - ".date("Y-d-m h:i:s", time()).PHP_EOL;

            fwrite($this->myfile, $txt);
            
            fclose($this->myfile);
        }
	}

/*	name: insert
//	date: 
//	auth: VVenning
//	desc: insert for users table
*/
	public function insert()
	{
        $this->database_error = null;

        $stmt = $this->conn->prepare("INSERT INTO ".USERS_TABLE." (password, first_name, last_name, email, screen_name, phone_number, phone_number_ext, cell_number, address, address2, city, state, zip, link, active, dont_send_email, role_id, client_id, partner_id, tag_id,  created) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");

		$the_time = date("Y-d-m h:i:s", time());

        if($stmt)
        {
            $this->created = date("Y-m-d h:i:s", time());

            $stmt->bind_param("ssssssssssssssiiiiiis", $this->password, $this->first_name, $this->last_name, $this->email, $this->screen_name, $this->phone_number, $this->phone_number_ext, $this->cell_number, $this->address, $this->address2, $this->city, $this->state, $this->zip, $this->link,$this->active, $this->dont_send_email, $this->role_id, $this->client_id, $this->partner_id, $this->tag_id, $this->created);

            if(!$stmt->execute())
            {
				

                $this->myfile = fopen("DatabaseErrLog.txt", "a") or die("Unable to open file!");

                $this->database_error  = "Execute failed: (" . $this->conn->errno . ") " . $this->conn->error;
                $this->database_error .= ".  INSERT: ". $this->email." - ".$this->email." ".$the_time.PHP_EOL;
            } 
            else
            {  
                $this->last_inserted_id = $this->conn->insert_id;
            }
            $stmt->close();
        }
        else
        {
            $this->myfile = fopen("DatabaseErrLog.txt", "a") or die("Unable to open file!");

            $this->database_error  = "Prepare failed: (" . $this->conn->errno . ") " . $this->conn->error;
            $this->database_error .= ".  INSERT: ". $this->email." - ".$this->email." ".$the_time.PHP_EOL;

        }


	}

//	name: update
//	date: 2019-04-29
//	auth: VVenning
//	desc: update for users table.  I'm still thinking about this philosophically
	public function update()
	{
        $this->database_error = null;
        
		$the_time = date("Y-d-m h:i:s", time());
		
		
    //  email, password, role_id,partner_id,first_name, last_name, phone_number, phone_number_ext,role_id, partner_id
    //  2019-09-27 1010 VVenning - Add dont_send_email to update
    //  2019-10-03 1202 VVenning - Add client_groups to update
    //  2019-03-19 1111 VVenning - Modified update() to handle cell_number and tag_id
        $stmt = $this->conn->prepare("UPDATE ".USERS_TABLE." SET first_name = ?, last_name = ?, email = ?, screen_name = ? ,phone_number = ?, phone_number_ext = ?, cell_number = ?, active = ?, dont_send_email = ?, role_id = ?, tag_id = ?, modified = ? WHERE id = ?");

        if($stmt)
        {
            $this->modified = date("Y-m-d h:i:s", time());

            $stmt->bind_param("sssssssiiiisi", $this->first_name, $this->last_name, $this->email, $this->screen_name, $this->phone_number, $this->phone_number_ext, $this->cell_number, $this->active, $this->dont_send_email, $this->role_id, $this->tag_id, $this->modified, $this->id);

            if(!$stmt->execute())
            {
                $this->myfile2 = fopen("DatabaseErrLog.txt", "a") or die("Unable to open file!");

                $this->database_error  = "Execute failed: (" . $this->conn->errno . ") " . $this->conn->error;
                $this->database_error .= ".  UPDATE: ". $this->email." - ".$this->email." ".$the_time.PHP_EOL;

                fwrite($this->myfile2, $the_time.$this->database_error.PHP_EOL);
                fclose($this->myfile2);
            } 
        
            $stmt->close();
        }
        else
        {

            $this->myfile2 = fopen("DatabaseErrLog.txt", "a") or die("Unable to open file!");

            $this->database_error  = "Prepare failed: (" . $this->conn->errno . ") " . $this->conn->error;
            $this->database_error .= ".  UPDATE: ". $this->email." - ".$this->email.time().PHP_EOL;

            fwrite($this->myfile2, $the_time.$this->database_error.PHP_EOL);
            fclose($this->myfile2);
        }

	}


//	name: deactivate
//	date: 2019-04-29
//	auth: VVenning
//	desc: make a user inactive.  This changes a single value
	public function deactivate()
	{
		//
	}


//	name: delete
//	date: 2019-04-29
//	auth: VVenning
//	desc: delete for users table.  WOn;t be used except in emergencies.  We deactivate, which is an update process
	public function delete()
	{
		//
	}	


//  name: select_clientID_by_emailist()
//  date: 2019-03-06
//  auth: VVenning
//  desc: select client_id by search for the client id shared by all emails from ADSYnc
    public function select_clientID_by_emailist()
    {
        $sqlGetClient = "SELECT client_id FROM ".USERS_TABLE." where email IN ('.$this->list_of_emails. ')";
 
	//	$stmt = $this->conn->prepare("SELECT * FROM users WHERE email = ?");
		$stmt = $this->conn->prepare("SELECT client_id FROM ".USERS_TABLE." where email IN (?) ");
    //  $stmt = $this->conn->prepare($sqlGetClient);

        if($stmt)
        {
            $data = "";

            $stmt->bind_param("s", $this->list_of_emails);
            $stmt->execute();

            $res = $stmt->get_result();

            while ($row = $res->fetch_assoc()){
                $data[] = $row;
            }
            
            return $data;
            
            $stmt->close();
        }
        else
        {
           $this->myfile = fopen("PartnerSignupDatabaseErrLog.txt", "a") or die("Unable to open file!");

            $txt = "Failed to create statemnt to select client id:" . time().PHP_EOL;
            fwrite($this->myfile, $txt);
            
            fclose($this->myfile);
        }
    }   




//  name: selectCountByClientId
//  date: 2019-02-25
//  auth: VVenning
//  desc: select for users table by client id
//  param: client id
//  2019-02-25 1200 Created function selectCountByClientId() 
    public function selectCountByClientId($client_id=null)
    {
        $client_id = $client_id ? $client_id : $this->client_id;

        if(is_numeric($client_id))
        {
            $stmt = $this->conn->prepare("SELECT COUNT(*) FROM ".USERS_TABLE." WHERE active = 1 AND client_id = ?");

            if($stmt)
            {
                $data = "";

                $stmt->bind_param("i", $client_id);

                $stmt->execute();
                $stmt->bind_result($totalUsers);
                $stmt->fetch();

            //    $res = $stmt->get_result();
            //    $data = $res->fetch();
                $stmt->close();
                return $totalUsers;
            }
            else
            {
                $this->myfile = fopen("DatabaseErrLog.txt", "a") or die("Unable to open file!");

                $txt = date("Y-d-m h:i:s", time()).": Failed prepare statement to select count of users by client_id - ".$client_id.PHP_EOL;
                fwrite($this->myfile, $txt);
                
                fclose($this->myfile);
            }
        }
        else
        {
            $this->myfile = fopen("DatabaseErrLog.txt", "a") or die("Unable to open file!");

            $txt = date("Y-d-m h:i:s", time()).": Attempted to select count of users by non numeric id - ".$client_id.PHP_EOL;
            fwrite($this->myfile, $txt);
            fclose($this->myfile);
        }
    }


//  name: selectCountByPartnerId
//  date: 2019-02-25
//  auth: VVenning
//  desc: select for users table by partner id
//  param: partner id
//  2019-02-25 1330 Created function selectCountBypartnerId() 
    public function selectCountByPartnerId($partner_id=null)
    {
        $partner_id = $partner_id ? $partner_id : $this->partner_id;

        if(is_numeric($partner_id))
        {
            $stmt = $this->conn->prepare("SELECT COUNT(*) FROM ".USERS_TABLE." WHERE active = 1 AND partner_id = ?");

            if($stmt)
            {
                $data = "";

                $stmt->bind_param("i", $partner_id);

                $stmt->execute();

                $stmt->bind_result($totalUsers);
                $stmt->fetch();

            //    $res = $stmt->get_result();
            //    $data = $res->fetch();
                $stmt->close();
                return $totalUsers;
            }
            else
            {
                $this->myfile = fopen("DatabaseErrLog.txt", "a") or die("Unable to open file!");

                $txt = date("Y-d-m h:i:s", time()).": Failed prepare statement to select count of users by partner_id - ".$partner_id.PHP_EOL;
                fwrite($this->myfile, $txt);
                
                fclose($this->myfile);
            }
        }
        else
        {
            $this->myfile = fopen("DatabaseErrLog.txt", "a") or die("Unable to open file!");

            $txt = date("Y-d-m h:i:s", time()).": Attempted to select count of users by non numeric id - ".$partner_id.PHP_EOL;
            fwrite($this->myfile, $txt);
            fclose($this->myfile);
        }
    }
	

    /**
     *  Update  users table selected fields
     *  Usage example :
     *  $user =  new m_users();
     *  $user->name = 'name';
     *  $user->id = 1;
     *  $user->updateSelectedFields();
     *
     * date: 2019-04-29, 2019-09-27
     * auth: VVenning, Ruiz
     * @return query
     */
    public function updateSelectedFields()
    {
        $sql_statement = "UPDATE ".USERS_TABLE." SET ";
        $type          = "";
        $params        = array();
        $sql_stmt_arr  = array();
/*
        $this->prepareForUpdate($sql_stmt_arr, $params, $type, 's',$this->password, 'password');
        $this->prepareForUpdate($sql_stmt_arr, $params, $type, 's',$this->password_old, 'password_old');
        $this->prepareForUpdate($sql_stmt_arr, $params, $type, 's',$this->first_name, 'first_name');
        $this->prepareForUpdate($sql_stmt_arr, $params, $type, 's',$this->last_name, 'last_name');
        $this->prepareForUpdate($sql_stmt_arr, $params, $type, 's',$this->email, 'email');
        $this->prepareForUpdate($sql_stmt_arr, $params, $type, 's',$this->screen_name, 'screen_name');
        $this->prepareForUpdate($sql_stmt_arr, $params, $type, 's',$this->job_function, 'job_function');
        $this->prepareForUpdate($sql_stmt_arr, $params, $type, 's',$this->phone_number, 'phone_number');
        $this->prepareForUpdate($sql_stmt_arr, $params, $type, 's',$this->phone_number_ext, 'phone_number_ext');
        $this->prepareForUpdate($sql_stmt_arr, $params, $type, 's',$this->cell_number, 'cell_number');
        $this->prepareForUpdate($sql_stmt_arr, $params, $type, 'i',$this->active, 'active');
        $this->prepareForUpdate($sql_stmt_arr, $params, $type, 'i',$this->dont_send_email, 'dont_send_email');

        $this->prepareForUpdate($sql_stmt_arr, $params, $type, 's',$this->last_login, 'last_login');
        $this->prepareForUpdate($sql_stmt_arr, $params, $type, 'i',$this->role_id, 'role_id');
        $this->prepareForUpdate($sql_stmt_arr, $params, $type, 'i',$this->client_id, 'client_id');
        $this->prepareForUpdate($sql_stmt_arr, $params, $type, 'i',$this->partner_id, 'partner_id');
        $this->prepareForUpdate($sql_stmt_arr, $params, $type, 's',$this->AAD_client_id, 'AAD_client_id');
        $this->prepareForUpdate($sql_stmt_arr, $params, $type, 's',$this->AAD_user_id, 'AAD_user_id');
        $this->prepareForUpdate($sql_stmt_arr, $params, $type, 'i',$this->tag_id, 'tag_id');
        $this->prepareForUpdate($sql_stmt_arr, $params, $type, 'i',$this->email_validated, 'email_validated');
        $this->prepareForUpdate($sql_stmt_arr, $params, $type, 's',$this->validation_code, 'validation_code');
        $this->prepareForUpdate($sql_stmt_arr, $params, $type, 'i',$this->failed_attempt, 'failed_attempt');
        $this->prepareForUpdate($sql_stmt_arr, $params, $type, 'i',$this->welcome_screen, 'welcome_screen');
        $this->prepareForUpdate($sql_stmt_arr, $params, $type, 'i',$this->external_breach_visit, 'external_breach_visit');
        $this->prepareForUpdate($sql_stmt_arr, $params, $type, 'i',$this->allow_ess, 'allow_ess');
        $this->prepareForUpdate($sql_stmt_arr, $params, $type, 'i',$this->latest_ess, 'latest_ess');
        $this->prepareForUpdate($sql_stmt_arr, $params, $type, 's',$this->moodle_token, 'moodle_token');
        $this->prepareForUpdate($sql_stmt_arr, $params, $type, 's',$this->acknowledged_policies_at, 'acknowledged_policies_at');
        $this->prepareForUpdate($sql_stmt_arr, $params, $type, 's',$this->acknowledged_other_policies_at, 'acknowledged_other_policies_at');
*/


		if(isset($this->password           { $this->prepareForUpdate($sql_stmt_arr, $params, $type, 's',$this->password, 'password');  }
		if(isset($this->password_old       { $this->prepareForUpdate($sql_stmt_arr, $params, $type, 's',$this->password_old, 'password_old');  }
		
		if(isset($this->first_name))       { $this->prepareForUpdate($sql_stmt_arr, $params, $type, 's',$this->first_name, 'first_name'); }
		if(isset($this->lasst_name))       { $this->prepareForUpdate($sql_stmt_arr, $params, $type, 's',$this->last_name, 'last_name'); }
		if(isset($this->email))            { $this->prepareForUpdate($sql_stmt_arr, $params, $type, 's',$this->email, 'email'); }

		if(isset($this->screen_name))      { $this->prepareForUpdate($sql_stmt_arr, $params, $type, 's',$this->screen_name, 'screen_name');  }
        if(isset($this->job function))     { $this->prepareForUpdate($sql_stmt_arr, $params, $type, 's',$this->job_function, 'job_function');  }
		
		
		if(isset($this->phone_number))     { $this->prepareForUpdate($sql_stmt_arr, $params, $type, 's',$this->phone_number, 'phone_number'); }
		if(isset($this->phone_number_ext)) { $this->prepareForUpdate($sql_stmt_arr, $params, $type, 's',$this->phone_number_ext, 'phone_number_ext'); }
		if(isset($this->cell_number))      { $this->prepareForUpdate($sql_stmt_arr, $params, $type, 's',$this->cell_number, 'cell'); }
		
		if(isset($this->address))          { $this->prepareForUpdate($sql_stmt_arr, $params, $type, 's',$this->address, 'address'); }
		if(isset($this->address2))         { $this->prepareForUpdate($sql_stmt_arr, $params, $type, 's',$this->address, 'address2'); }
        if(isset($this->city))             { $this->prepareForUpdate($sql_stmt_arr, $params, $type, 's',$this->city, 'city'); }
        if(isset($this->state))            { $this->prepareForUpdate($sql_stmt_arr, $params, $type, 's',$this->state, 'state'); }
        if(isset($this->zip))              { $this->prepareForUpdate($sql_stmt_arr, $params, $type, 's',$this->zip, 'zip'); }
        if(isset($this->link))             { $this->prepareForUpdate($sql_stmt_arr, $params, $type, 's',$this->link, 'link'); }

        if(isset($this->active))           { $this->prepareForUpdate($sql_stmt_arr, $params, $type, 'i',$this->active, 'active'); }

		if(isset($this->dont_send_email))  { $this->prepareForUpdate($sql_stmt_arr, $params, $type, 'i',$this->dont_send_email, 'dont_send_email');  }
		if(isset($this->role_id))          { $this->prepareForUpdate($sql_stmt_arr, $params, $type, 'i',$this->role_id, 'role_id');  }
		if(isset($this->client_id))        { $this->prepareForUpdate($sql_stmt_arr, $params, $type, 'i',$this->client_id, 'client_id'); }
		if(isset($this->partner_id))       { $this->prepareForUpdate($sql_stmt_arr, $params, $type, 'i',$this->partner_id, 'partner_id'); }

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
    private function prepareForUpdate(&$sql_stmt_arr, &$params, &$type, $update_type, $value, $column){
        if(trim($value) != null && strlen(trim($value)) > 0 )
		{
            array_push($sql_stmt_arr, " $column = ?");
            array_push($params,$value);
            $type  .= $update_type;
        }
    }

    /**
     * prepare part of the insert query column by reference
     * @return void
     */
    private function prepareForInsert(&$placeholder, &$arr_columns, &$type, &$arr_param, $insert_type, $value, $column ){
        if(trim($value) != null && strlen(trim($value)) > 0 ) 
		{
            array_push($placeholder, '?');
            array_push($arr_columns, $column);
            array_push($type, $insert_type);
            array_push($arr_param, $value);
        }
    }


    /**
     * Create new user
     * @return query
     */
    public function create()
    {

        $arr_columns   = [];
        $arr_type      = [];
        $arr_param     = [];
        $placeholder   = [];

        //create password
        $readable_password = randomString(10);
        $this->password   = password_hash($readable_password, PASSWORD_DEFAULT);

        // create token
        $hashed_password = str_replace(' ', '-', $this->password); // Replaces all spaces with hyphens.
        $hashed_password = preg_replace('/[^A-Za-z0-9\-]/', '', $hashed_password); // Removes special chars.
        $this->tokenhash = $hashed_password;

        //create a screen name
        $this->screen_name = 'user-'.randomString(7);
        $this->active   = 1;

        $this->prepareForInsert($placeholder, $arr_columns, $arr_type, $arr_param, 's',$this->password, 'password');
        $this->prepareForInsert($placeholder, $arr_columns, $arr_type, $arr_param, 's',$this->password_old, 'password_old');

        $this->prepareForInsert($placeholder, $arr_columns, $arr_type, $arr_param, 's',$this->first_name, 'first_name');
        $this->prepareForInsert($placeholder, $arr_columns, $arr_type, $arr_param, 's',$this->last_name, 'last_name');
        $this->prepareForInsert($placeholder, $arr_columns, $arr_type, $arr_param, 's',$this->email, 'email');
        $this->prepareForInsert($placeholder, $arr_columns, $arr_type, $arr_param, 's',$this->screen_name, 'screen_name');
        $this->prepareForInsert($placeholder, $arr_columns, $arr_type, $arr_param, 's',$this->job_function, 'job_function');
        $this->prepareForInsert($placeholder, $arr_columns, $arr_type, $arr_param, 's',$this->phone_number, 'phone_number');
        $this->prepareForInsert($placeholder, $arr_columns, $arr_type, $arr_param, 's',$this->phone_number_ext, 'phone_number_ext');
        $this->prepareForInsert($placeholder, $arr_columns, $arr_type, $arr_param, 's',$this->cell_number, 'cell_number');


        $this->prepareForInsert($placeholder, $arr_columns, $arr_type, $arr_param, 's',$this->address, 'address');
        $this->prepareForInsert($placeholder, $arr_columns, $arr_type, $arr_param, 's',$this->address2, 'address2');
        $this->prepareForInsert($placeholder, $arr_columns, $arr_type, $arr_param, 's',$this->city, 'city');
        $this->prepareForInsert($placeholder, $arr_columns, $arr_type, $arr_param, 's',$this->state, 'state');
        $this->prepareForInsert($placeholder, $arr_columns, $arr_type, $arr_param, 's',$this->zip, 'zip');
        $this->prepareForInsert($placeholder, $arr_columns, $arr_type, $arr_param, 's',$this->zip, 'link');

        $this->prepareForInsert($placeholder, $arr_columns, $arr_type, $arr_param, 'i',$this->active, 'active');
        $this->prepareForInsert($placeholder, $arr_columns, $arr_type, $arr_param, 'i',$this->dont_send_email, 'dont_send_email');
        $this->prepareForInsert($placeholder, $arr_columns, $arr_type, $arr_param, 's',$this->last_login, 'last_login');
		
        $this->prepareForInsert($placeholder, $arr_columns, $arr_type, $arr_param, 'i',$this->role_id, 'role_id');
        $this->prepareForInsert($placeholder, $arr_columns, $arr_type, $arr_param, 'i',$this->client_id, 'client_id');
        $this->prepareForInsert($placeholder, $arr_columns, $arr_type, $arr_param, 'i',$this->partner_id, 'partner_id');
        $this->prepareForInsert($placeholder, $arr_columns, $arr_type, $arr_param, 'i',$this->tag_id, 'tag_id');

        $this->prepareForInsert($placeholder, $arr_columns, $arr_type, $arr_param, 's',date( "Y-m-d H:i:s"),'created' );
        $this->prepareForInsert($placeholder, $arr_columns, $arr_type, $arr_param, 's',date( "Y-m-d H:i:s"),'modified' );


        $sql_statement = "INSERT INTO ".USERS_TABLE." ( ".implode(',',$arr_columns).") VALUES ( ".implode(',',$placeholder).")";

        $this->executeQuery( $sql_statement,implode('',$arr_type), $arr_param, true );
		
       return $this->selectPax8UserById($this->last_inserted_id);

    }

    /**
     * This function will run a query
     *
     * @param  $sql_statement   (sql query)
     * @param  $type            (sssii)
     * @params $params          (should be array)
     * @params $close           (select = false, update insert,delete = true)
     *
     * @return arrays
     */
    private function executeQuery( $sql_statement, $type, $params, $close){
        mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);
        
		try{
            $stmt = $this->conn->prepare($sql_statement);
            $stmt->bind_param($type, ...$params);

            $stmt->execute();

            if($stmt)
			{
                if($close){
                    $result = $this->conn->affected_rows;
                    if(strpos($sql_statement, 'INSERT') !== false){
                        $this->last_inserted_id = $this->conn->insert_id;
                    }
                } else {
                    $res    = $stmt->get_result();
                    if($res->num_rows > 1){
                        $result = array();
                        while ($obj = $res->fetch_object()) {
                            array_push($result, $obj);
                        }
                    }else{
                        $result = $res->fetch_object();
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

            universal_log_it($file, "m_users - ".$e->getMessage());

            return null;
        }

    }
}

?>
