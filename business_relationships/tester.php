<?php
/*  Notes and Comments
    name: tester
    date: 
    crea: Victor Venning
    desc: 


*/
    set_time_limit(0);
	
    require '../z_config/api_config.php';
    require '../z_config/email_groups.php';
    
    require '../z_tools/validation.php';
    require '../z_tools/email4API.php';
    require '../z_tools/random_tools.php';

    require '../z_cxn/db_cxn.php';
    require '../Model/databaseModel.php';
    require '../Model/m_users.php';
    require '../Model/m_clients.php';
    require '../Model/m_partners.php';

    require '../Model/m_error_messages.php';

	require '../Model/m_partnerMessages.php'; 
    require '../Model/m_tags.php'; 


//  require '../ContactUsers/ContactUsersModel.php';

class tester
{
    public  $json_payload;
    private $arr_payload;
    private $apiKey;
    private $sender_id;
	private $arr_sender;


    private $action_datetime;


	private $id;
    private $password;
    private $password_old;
    private $first_name;
    private $last_name;
    private $email;
    private $email_new;  // Does a change email need to cascade?
    private $screen_name;  
	private $job_function;
    private $phone_number;
    private $phone_number_ext;
    private $cell_number;
    private $address;
    private $address2;
    private $city;
    private $state;
    private $zip;
    private $link;
    private $active;
    private $dont_send_email;
    private $last_login;
    private $role_id;
    private $client_id;
    private $partner_id;
    private $tag_id;
    private $created;
    private $modified;


    private $arr_users;  //  This will be a multi-dimensional array
    private $arr_client;
    private $arr_partner;

    private $operation;


    private $existingUser;

    private $client_name;
    private $partner_name;

    private $myfile;

    private $txt;

    private $mailer;

    private $outlier_report;

	
    private $database_error;
    private $last_inserted_id;

	private $arr_error_messages;
	
    private $who;
    private $what;
    private $desc;
    
    
    public  $status;
    private $file_prefix;

    private $data_json;

    public  $conn;
	
	
    public function __construct() 
    {
        $this->file_prefix = date("Y-m-d_his_",time());
        $this->txt  = date("Y-m-d h:i:s", time())." - ";

    //  Get database connection object
        $this->cxn_obj = new db_cxn(ENV);

        $this->myfile = fopen("logs/".$this->file_prefix."tester.txt", "a") or die("Unable to open file!");


        $this->o_error_messages       = new m_error_messages;
        $this->o_error_messages->conn = $this->cxn_obj->conn;
				
		$temp_err_messages     = $this->o_error_messages->select_all();
		
		foreach($temp_err_messages as $msg)
		{
			$this->arr_error_messages[$msg['err_name']] = array("err_subj"=>$msg['err_subj'], "err_text"=>$msg['err_text']);
		}
    }

    public function __destruct() 
    {
        fclose($this->myfile);
        
        $x = $_SERVER['DOCUMENT_ROOT']."/business_relationships/logs/";

    //  $x = $x."\\";
        
        $x = $x.$this->file_prefix."tester.txt";

        if( filesize($x) == false)
        {
           unlink($x);
        }
	}
	
    public function process()
    {
        $this->status = 1;

    //  Bail out if there's no json
        $this->decode();
	
	//	2018-09-11 1441 VVenning - In the event of an error that makes this client's json invalid, stop processing, but allow the function to be called by the next file.
		if($this->status < MAJOR_ERROR_CODE) // MAJOR_ERROR_CODE check 1 
		{
			$this->assign_global_variables();

		//	2018-09-11 1441 VVenning - In the event of an error that makes this client's json invalid ...
			if($this->status < MAJOR_ERROR_CODE)
			{
                $this->check_api_key();

			//	2018-09-11 1441 VVenning - In the event of an error that makes this client's json invalid ...
				if($this->status < MAJOR_ERROR_CODE)
				{
                    if($this->analyze())
					{
						$this->validate_and_sanitize();
                    }

					$this->getUsersObject();
					$this->getSender();


				//	What am I trying to do?
				//	Create/Update member
				//	Create/Update client
				//	Create/Update partner


				//	2018-09-11 1441 VVenning - In the event of an error that makes this client's json invalid ...
					if($this->status < MAJOR_ERROR_CODE)
					{
						if(isset($this->client_id)) {$this->getClient(); }
						
					//	2018-09-11 1441 VVenning - In the event of an error that makes this client's json invalid ...
						if($this->status < MAJOR_ERROR_CODE)
						{
                            if(isset($this->partner_id)) {$this->getPartner(); }
							
							$this->checkPartnerConflict();

						//	2018-09-11 1441 VVenning - In the event of an error that makes this client's json invalid ...
							if($this->status < MAJOR_ERROR_CODE)
							{
							//  client and partner are listed first in logs.  Easier to read.
								$this->log_it("partner: " . $this->partner_name . ", client: ". $this->client_name);

							//  Things Ops might want to take a look at
								$this->outlier_report .= '{ "partner": "'.$this->partner_name.'", "client": "'. $this->client_name.'", "users": ['.PHP_EOL;

								$this->dont_send_email = false;

								$this->processUsers();
/*
							//  Start processing user by user
								foreach($this->arr_users as $aUser)
								{

								//  Assign values in users array to local variables sequentially, then update or create in users table
									$this->transaction    = $aUser['operation'];
									$this->o_users->email = $aUser['email'];
									$this->existingUser = "";
									$this->getExistingUser($aUser);

								//	We have established whether or not the target exists
									if (is_array($this->existingUser) || is_object($this->existingUser))
									{
									//  Update/Redact
										$this->update_user($aUser);
														
									//	if deleted user, dont send email
										if($aUser['operation'] == "delete") 
										{ 
										//	echo "deleted"; 
										}
										//	if we are re-activating an existing user
										elseif( $aUser['active'] == true && $this->existingUser['active'] == 0 && $this->dont_send_email == false )
										{
										//  send re-activated email
										}
										//	if we are de-activating an existing user
										elseif( ($aUser['active'] == false || $aUser['active'] == null) && $this->existingUser['active'] == 1  && $this->dont_send_email == false)
										{
										//  send re-activated email
										}
									}
									else
									{
									//  create token
										$hashed_password = password_hash($aUser['email'], PASSWORD_DEFAULT);
										$hashed_password = str_replace(' ', '-', $hashed_password); // Replaces all spaces with hyphens.
										$hashed_password = preg_replace('/[^A-Za-z0-9\-]/', '', $hashed_password); // Removes special chars.

										$aUser['tokenhash'] = $hashed_password; 

										//  Insert
										//	$this->create_user($aUser);
																
										//  Send welcome email
									}
								}
*/

								$this->outlier_report = trim($this->outlier_report);
								if(substr($this->outlier_report, -1) == ",")
								{
								//  Take comma off the end of the string
									$this->outlier_report = rtrim($this->outlier_report, ",");
								}

							//  Close per client report
								$this->outlier_report .= ']}'.PHP_EOL;

							//  fclose($this->myfile);

								$this->log_it($this->outlier_report);

								$this->who  = BUSINESS;
								$this->what = "Client Report";
								$this->desc = $this->outlier_report;

							//  $this->log_it($this->what.$this->desc);

								$objOutlier = json_decode($this->outlier_report);

								if(isset($objOutlier->users) && count($objOutlier->users) > 0)
								{
								//  format for email  
									$this->desc = str_replace(PHP_EOL,"<br />",$this->desc);
									$this->send_email($this->who, $this->what, $this->desc);
								}
										

//	$this->arr_partner['email'] = 'victorv@trustsecurenow.com'; // This line intentional.  Overrides emailing actual partners.

					
							}	// close: getPartner gives ERROR_CODE
						}	// close: getClient gives ERROR_CODE
						
						
					}	// close: analyze gives ERRORCODE
				}	//  close: check_api_key gives ERROR_CODE
			}	//  close: assign_global_variables gives ERROR_CODE
		}	//  close: decode gives ERROR_CODE
	}	// end process

	
/*  name: SimpleArraytoHTMLTable
    date:
    auth: VVenning
    desc:  
*/  function SimpleArraytoHTMLTable($arrEmailAddresses)
    {
        // start table
        $html = '<table>';

        // data rows
        foreach( $arrEmailAddresses as $Address)
        {
            
            $html .= '<tr><td>' . $Address . '</td></tr>';
        }

        $html .= '</table>';
        return $html;
    }	
	
/*  name: decode
    date: 
    auth: VVenning
    desc: Decode input 
*/  private function decode()
    {
        $this->arr_payload = json_decode($this->json_payload, true);
        
        date_default_timezone_set('US/Eastern');

    //  if it's an array, then the payload was json, if count > 0, it's json with content
        if(is_array($this->arr_payload) && count($this->arr_payload) > 0)
        {
            $retcode = true;
        }
        else
        {
            $this->who   = SOFTWARE.",".HARDWARE;
            $this->what  = "Payload could not be converted from JSON to PHP.";
            $this->desc  = "File: tester.php.<br />Function: decode.<br />Message:  Payload could not be converted from JSON to PHP.<br />";
            $this->desc .= print_r($this->json_payload, 1);
            $this->desc .= "<br /><br />If there is no json visible, this indicates that a character that could not be processed by the json_decode() is present in the json.";
            $this->urfile = fopen("logs/".$this->file_prefix."bad_json.txt", "a") or die("Unable to open file!");
            fwrite($this->urfile, $this->desc.PHP_EOL);

            $this->Terminate("400");
        }
    }


/*  name: assign_global_variables
    date: 
    auth: VVenning
    desc: Move vales from decoded json array to local variables */
    private function assign_global_variables()
    {
        $this->api_key         = $this->arr_payload['api_key'];
		$this->action_datetime = $this->arr_payload['datetime'];
        $this->stage_payload   = $this->arr_payload['stage_payload'];
        $this->sender_id       = $this->arr_payload['sender_id'];
        $this->password        = $this->arr_payload['password'];
        $this->override        = $this->arr_payload['override'];

        if(isset($this->arr_payload['client_id']))  { $this->client_id   = $this->arr_payload['client_id']; }
        if(isset($this->arr_payload['partner_id'])) { $this->partner_id  = $this->arr_payload['partner_id']; }

        if(isset($this->arr_payload['users']))      {$this->arr_users    = $this->arr_payload['users']; } //  This will be a multi-dimensional array
        if(isset($this->arr_payload['clients']))    {$this->arr_clients  = $this->arr_payload['clients']; }
        if(isset($this->arr_payload['partners']))   {$this->arr_partners = $this->arr_payload['partners']; }

    }


/*  name: check_api_key
    date: 
    auth: VVenning
    desc: Chek api_key in payload against config
*/  private function check_api_key()
    {
        if(!$this->api_key)
        {
            $this->who  = SOFTWARE.",".HARDWARE;
            $this->what = $this->arr_error_messages['No_API_Key']['err_subj'];
            $this->desc = $this->arr_error_messages['No_API_Key']['err_text'];

            $this->Terminate("401");
        }

		switch($this->arr_payload['api_key'])
		{
			case Users_API_KEY:

			//	code to be executed if n=label1;
			//	go against users table
				break;
			case Clients_API_KEY:
			//	code to be executed if n=label2;
			//  go against clients table
				break;
				
			case Partners_API_KEY:
			//	code to be executed if n=label3;
			//	go against partners table
				break;
			
			default:
				$this->who   = SOFTWARE.",".HARDWARE;
				$this->what  = $this->arr_error_messages['Bad_API_Key']['err_subj'];
				$this->desc  = $this->arr_error_messages['Bad_API_Key']['err_text'];
				$this->desc .= $this->api_key;

				$this->Terminate("401");
		}
    }

/*  name: analyze
    date: 
    auth: VVenning
    desc: Take apart the payload, determine what needs to be done, if anything
*/  private function analyze()
    {

        if(!isset($this->client_id) || $this->client_id  == "" )
        {
            $this->who  = SOFTWARE;
            $this->what = "No client id";
            $this->desc = "No client id";

            $this->Terminate("401");       
        }


    //  If one of the values in results is greater than zero, then I need to cycle through the users.  
        if(count($this->arr_users > 0))
        {
            return true;
        }
        else
        {
            $this->Terminate("200",": No user records");   
        }
    //  What do I do with 'operation'?
    }

/*  name: validate_and_sanitize
    date: 
    auth: VVenning
    desc: Take the values to be inserted into the database, and make sure the right datatype 
        and not dangerous

    validation::clean_email_address($email_address)
    validation::clean_string_input($str)
    validation::check_alphanum($txt)
    validation::check_alpha($txt)
    validation::check_numeric($x)

*/  private function validate_and_sanitize()
    {
        foreach($this->arr_users as &$aUser)
        {

        //  2018-09-28 1408 VVenning - validate email license address using same logic for 'email' as for 'email'
        //  2018-10-10 1724 VVenning -  add isset
            if(isset($aUser['email']))
            {
                $original_email = $aUser['email'];
                $clean_email = validation::clean_email_address($aUser['email']);

                if ($original_email == $clean_email && validation::validate_email_address($aUser['email']))
                {
                // now you know the original email was safe to insert.
                }
                else //  if ‘email’ key value not valid email, set value of active to 0, Write to log file
				{
				//	$aUser['active'] = 0;
					$this->outlier_report .= '{"Email: '.$aUser['email'].'": "is not valid email.},'.PHP_EOL;
                    $aUser['email'] = "";  //  By setting this value to null, I prevent it from being processed later
                }
            }
			
			
            if($aUser['first_name'])
            {
            //  Clean input.  If value is not alpha num, make null
                $aUser['first_name'] = validation::clean_string_input($aUser['first_name']);
            //    $aUser['first_name'] = validation::check_name($aUser['first_name']) ? $aUser['first_name'] : "";
            }

            if($aUser['last_name'])
            {
            //  Clean input.  If value is not alpha num, make null
                $aUser['last_name'] = validation::clean_string_input($aUser['last_name']);
            //    $aUser['last_name'] = validation::check_name($aUser['last_name']) ? $aUser['last_name'] : "";
            }

            if($aUser['screen_name'])
            {
            //  Clean input.  If value is not alpha num, make null
                $aUser['screen_name'] = validation::clean_string_input($aUser['screen_name']);
            }
            
		//	Make default true
			if(isset($aUser['active']))
			{
				$aUser['active'] = ( $aUser['active'] == 0 ) ? 0 : 1;
			}
			else
			{
				$aUser['active'] = 1;
			}
			
		//	INIT: Use groups to set role id
            if(isset($aUser['groups']) && is_array($aUser['groups']))
            {
            //  Determine what first tagName is, if it exists
                foreach($aUser['groups'] as $tag)
                {
                    if( substr($tag, 0, 4) == 'TAG-')
                    {
                        $aUser['tagName'] = substr($tag, 4);
                        break;
                    }
                }

                foreach($aUser['groups'] as &$aGroup)
                {
                //	Trim groups in groups array
                	$aGroup = trim($aGroup);

                    if($aGroup == MANAGERS)
                    {
                        $aUser['role_id'] = 2;
                        break;
                    }
                    elseif($aGroup == END_USERS)
                    {
                        $aUser['role_id'] = 3;
                    }
                //  As long as there's any group, there will be a group id
                    else
                    {
                    //  VVenning - Only assign 0 to role_id if role_id not 2 or 3
						if(isset($aUser['role_id']) && $aUser['role_id'] != 2 && $aUser['role_id'] != 3)
						{
                            $aUser['role_id'] = 4;
						}
                    }
                }
                
                if( isset($aUser['role_id']) && $aUser['role_id'] == 4)
			    {
				    $aUser['active'] = 0; 
			    }
            }
	
            if(isset($aUser['role_id']) && !$aUser['role_id'] == 2 && !$aUser['role_id'] == 3 && !$aUser['role_id'] == 4 )
            {
                $aUser['active'] = 0;
                $this->outlier_report .= '{"'.$aUser['email'].'": "no role defined for new user." },'.PHP_EOL; 
            }
            elseif(!isset($aUser['role_id']))
            {
                $aUser['active'] = 3;
                $this->outlier_report .= '{"'.$aUser['email'].'": "no role defined for new user." },'.PHP_EOL;
            }
		//	EXIT: Use groups to set role id


//  phone_number - is numeric or ()-
//  phone_number_ext - is numeric
//  client_id - Only an Administrator can change a users client_id
//  partner_id - Only an Administrator can change a users partner_id
//  sender_id - Is numeric
//	password;
//  dont_send_email 1 or 0

        }

        unset($aUser);
    }

/*  name: check_required_fields
    date: 
    auth: VVenning
    desc: Mostly for inserts.  Make sure any required fields have values.
          Check database schema
*/  private function check_required_fields()
    {


    }

/*  name: getUsersObject
    date: 
    auth: VVenning
    desc: Get users object and assign db cxn.  This is mostly here to wall off error handling si the processing is more readable 
*/  private function getUsersObject()
    {
        $this->o_users = new m_users;

    //  If m_users object cannot be created
        if(!$this->o_users)
        {
            $this->who  = SOFTWARE.", ".HARDWARE;
            $this->what = "404 - Not Found.  ADSync API is down";
            $this->desc = "Could not create m_user object.  Partner id: ". $this->partner_id . " - Client_id: " . $this->client_id;

            $this->Terminate("404", $this->desc);
        }

        $this->o_users->conn = $this->cxn_obj->conn;
    }


	private function getSender()
	{
		$this->arr_sender = $this->o_users->select_byID($this->sender_id);

		$hashedword =  $this->arr_sender['password'];

		if(password_verify($this->password, $hashedword) )
		{
			echo "he's good";
			switch($this->arr_sender['role_id'])
			{
				
				case 1:
				//	Admin rights can do anything
					break;

				case 2:
				//	Manager rights.  Can alter users of his client
				//	getClient
					break;
				case 3:
				//	Member rights.  Can only select and view their own data
					break;
				case 5:
				//	Partner_admin.  Can alter data for their own client and users.
				//	getPartner
				case 4:
				default:
				//	No rights
					break;
				
			}
		} 
		else
		{
		//	bail out
			echo "bad guy";
			$this->who   = BUSINESS;
			$this->what  = "Not An Authorized User.";
			$this->desc  = "File: tester.php.<br />Function: process.<br />Message:  Not An Authorized User.<br />";
			$this->desc .= "sender_id: ".$this->sender_id;

			$this->Terminate("400");
		}
	}


	private function getExistingUser($aUser)
	{
		$this->o_users->email = $aUser['email'];
                                         
		$this->existingUser = "";
		
		if( isset($aUser['id'])) { $this->existingUser = $this->o_users->select_byID(); }

		if (is_array($this->existingUser) || is_object($this->existingUser))
		{
		// The user exists, no further search needed
		}
		else
		{
		//  Search by 'active' and client_id.  If they're on a different client, we don't want to update the record
            $this->existingUser = "";
                                                
			if(isset($aUser['email']) && $aUser['email'] != null)
            {
                $this->existingUser = $this->o_users->select_by_email();

				if (is_array($this->existingUser) || is_object($this->existingUser))
				{
				// The user exists, no further search needed
					if ( isset($this->client_id) && isset($this->existingUser['client_id']) && $this->client_id != $this->existingUser['client_id'] )
					{
					//	Email belongs to a different client.  Can't update
                        echo "Email belongs to a different client.  Can't update";
					}
				}
				else
				{
				//	The user does not exist
				}
			}
        }               
	}

	private function processUsers()
	{
	//  Start processing user by user
		foreach($this->arr_users as $aUser)
		{
		//  Assign values in users array to local variables sequentially, then update or create in users table
			$this->transaction    = $aUser['operation'];
			$this->o_users->email = $aUser['email'];
			$this->existingUser = "";
			$this->getExistingUser($aUser);

		//	We have established whether or not the target exists
			if (is_array($this->existingUser) || is_object($this->existingUser))
			{
			//  Update/Redact
				$this->update_user($aUser);
				
			//	if deleted user, dont send email
				if($aUser['operation'] == "delete") 
				{ 
				//	echo "deleted"; 
				}
				//	if we are re-activating an existing user
				elseif( $aUser['active'] == true && $this->existingUser['active'] == 0 && $this->dont_send_email == false )
				{
				//  send re-activated email
				}
				//	if we are de-activating an existing user
				elseif( ($aUser['active'] == false || $aUser['active'] == null) && $this->existingUser['active'] == 1  && $this->dont_send_email == false)
				{
				//  send re-activated email
				}
			}
			else
			{
			//  create token
				$hashed_password = password_hash($aUser['email'], PASSWORD_DEFAULT);
				$hashed_password = str_replace(' ', '-', $hashed_password); // Replaces all spaces with hyphens.
				$hashed_password = preg_replace('/[^A-Za-z0-9\-]/', '', $hashed_password); // Removes special chars.

				$aUser['tokenhash'] = $hashed_password; 
				//  Insert
					$this->create_user($aUser);
						
				//  Send welcome email
			}
		}
	}


/*  name: getClient
    date: 
    auth: VVenning
    desc: Use AAD_client_id (tenant_id) to get client information
*/  private function getClient()
    {
        $this->o_clients = new m_clients;
    
    //  If m_clients object cannot be created
        if(!$this->o_clients)
        {
            $this->who  = SOFTWARE.", ".HARDWARE;
            $this->what = "404 - Not Found.  ADSync API is down";
            $this->desc = "Could not create m_client object.  Active Directory Client id: ".$this->AAD_client_id;

            $this->Terminate("404", $this->desc);
        }

        $this->o_clients->conn = $this->cxn_obj->conn;

	//	get client info by client id 
		$this->arr_client = $this->o_clients->select_byID($this->client_id);
            
	    if(!$this->arr_client || count($this->arr_client) < 1)
	    {
        //  BUSINESS doesn't get this email.  It's a tech problem
            $this->who  = BUSINESS;
            $this->what = "Client not found using client id";
            $this->desc = "client id: ".$this->client_id;

            $this->Terminate("405", $this->desc);
        }

        If(!isset($this->partner_id)) { $this->partner_id = $this->arr_client['partner_id']; }
        
        $this->client_name    = $this->arr_client['name'];        
    }
    
/*  name: getPartner
    date: 
    auth: VVenning
    desc: Use partner_id to get partner information
*/  private function getPartner()
    {
        $this->o_partners = new m_partners;

    //  If m_partners object cannot be created
        if(!$this->o_partners)
        {
            $this->who  = SOFTWARE.", ".HARDWARE;
            $this->what = "404 - Not Found.";
            $this->desc = "Could not create m_partner object.";

            $this->Terminate("404", $this->desc);
        }

        $this->o_partners->conn = $this->cxn_obj->conn;

        $this->arr_partner      = $this->o_partners->select_byID($this->partner_id);

        if(!$this->arr_partner || count($this->arr_partner) < 1)
        {
            $this->who  = BUSINESS;
            $this->what = "Partner not found based on partner id";
            $this->desc = "Partner id: ". $this->partner_id;

            $this->Terminate("405", $this->desc);
        }
		
        $this->partner_name    = isset($this->arr_partner['name']) ? $this->arr_partner['name'] : "";
    }

	private function checkPartnerConflict()
	{
		if( isset($this->partner_id) && isset($this->client_id) && ($this->arr_client['partner_id'] != $this->partner_id))
		{
		//  Partner id conflict.  Suspend processing.  The designated partner is not that client's partner 
            $this->who  = BUSINESS;
            $this->what = "Partner id conflict";
            $this->desc = "Partner id provided: ". $this->partner_id . "does not match partner id of client: " . $this->arr_client['partner_id'];

            $this->Terminate("401", $this->desc);
			
		}			
	}


/*  name: getPartnerMessages
    date: 
    auth: VVenning
    desc: Get partner specific messages, if any
*/  private function getPartnerMessages($partner_id = null)
    {
        $this->o_partner_messages = new m_partnerMessages;
        
    //  If m_partners object cannot be created
        if(!$this->o_partner_messages)
        {
            $this->who  = SOFTWARE.", ".HARDWARE;
            $this->what = "404 - Not Found.  ADSync API is down";
            $this->desc = "Could not create m_partner_messages object.   Client id: ".$this->client_id;
		}
		$this->o_partner_messages->conn = $this->cxn_obj->conn;

        if($partner_id == null) { $partner_id = $this->partner_id; }
		
	//  2018-10-02 1207 VVenning - Allow partner_id to be passed as param
		$this->o_partner_messages->partner_id = $partner_id; 
        $this->arr_partner_messages = $this->o_partner_messages->selectByPartnerId();

    //  2019-04-22 1306 VVenning - Remove non-printing characters from partner message
        foreach($this->arr_partner_messages as &$one_partners_messages)
        {
        //  $one_partners_messages['message_type'] === 1 would limit this fix to welcome messages, but the problem could occur in any messages
        //  if($one_partners_messages['partner_id'] !== 1 && $one_partners_messages['message_type'] === 1)
            if($one_partners_messages['partner_id'] !== 1)
            {
                $one_partners_messages['first_text'] =  json_encode($one_partners_messages['first_text']);
                $one_partners_messages['first_text'] = str_replace('"', '', $one_partners_messages['first_text']);

                if($one_partners_messages['first_text'] == null)
                {
                    $this->log_it("partner message - first_text: ". json_last_error_msg());
                }

                $one_partners_messages['second_text'] =  json_encode($one_partners_messages['second_text']);
                $one_partners_messages['second_text'] = str_replace('"', '', $one_partners_messages['second_text']);

                if($one_partners_messages['second_text'] == null)
                {
                    $this->log_it("partner message - second_text: ". json_last_error_msg());
                }
            }
        }

    }

	
/*  name: attachMessagesToPartnerArray
    date: 
    auth: VVenning
    desc: Get partner specific messages, if any
*/  private function attachMessagesToPartnerArray()
    {
    //  Assign default partner messages
		$this->arr_partner['welcome_text_1']     = DEFAULT_PARTNER_WELCOME_TEXT_1;
		$this->arr_partner['welcome_text_2']     = DEFAULT_PARTNER_WELCOME_TEXT_2;
		$this->arr_partner['welcomeback_text_1'] = DEFAULT_PARTNER_WELCOMEBACK_TEXT_1;
		$this->arr_partner['welcomeback_text_2'] = DEFAULT_PARTNER_WELCOMEBACK_TEXT_2;
		$this->arr_partner['deactivated_text_1'] = DEFAULT_PARTNER_DEACTIVATED_TEXT_1;
		$this->arr_partner['deactivated_text_2'] = DEFAULT_PARTNER_DEACTIVATED_TEXT_2;

		foreach($this->arr_default_messages as $a_default_message)
		{
		    switch($a_default_message['message_type'])
			{
			//  welcome message
				case 1:
                    
					$this->arr_partner['welcome_text_1']  = $a_default_message['first_text'];
					$this->arr_partner['welcome_text_2']  = $a_default_message['second_text'];
					$this->arr_partner['email_subject']   = $a_default_message['subject'];
				    break;
 
					
                case 2:

                    $this->arr_partner['welcomeback_text_1'] = $a_default_message['first_text'];
                    $this->arr_partner['welcomeback_text_2'] = $a_default_message['second_text'];
					$this->arr_partner['email_subject']      = $a_default_message['subject'];
                    break;
	
                case 3:
					
                    $this->arr_partner['deactivated_text_1'] = $a_default_message['first_text'];
                    $this->arr_partner['deactivated_text_2'] = $a_default_message['second_text'];
					$this->arr_partner['email_subject']      = $a_default_message['subject'];
                    break;

	
                case 8:
					
                    $this->arr_partner['invalid_email_1'] = $a_default_message['first_text'];
                    $this->arr_partner['invalid_email_2'] = $a_default_message['second_text'];
					$this->arr_partner['email_subject']   = $a_default_message['subject'];
                    break;	
            }
		}
		
		//  Cycle through the returned arrays
		foreach($this->arr_partner_messages as $partner_message)
		{
			switch($partner_message['message_type'])
			{
                case 1:
					
                    $this->arr_partner['welcome_text_1'] = $partner_message['first_text'];
                    $this->arr_partner['welcome_text_2'] = $partner_message['second_text'];

                //  2019-02-13 1223 VVenning - Attach deferred_sending, days, hours to partner array
                    $this->arr_partner['deferred_sending'] = $partner_message['deferred_sending'];
                    $this->arr_partner['deferred_days']    = $partner_message['days'];
                    $this->arr_partner['deferred_hours']   = $partner_message['hours'];
                    break;
					
                case 2:

                    $this->arr_partner['welcomeback_text_1'] = $partner_message['first_text'];
                    $this->arr_partner['welcomeback_text_2'] = $partner_message['second_text'];
                    break;
	
                case 3:
					
                    $this->arr_partner['deactivated_text_1'] = $partner_message['first_text'];
                    $this->arr_partner['deactivated_text_2'] = $partner_message['second_text'];
                    break;
					
                case 8:
					
                    $this->arr_partner['invalid_email_1'] = $partner_message['first_text'];
                    $this->arr_partner['invalid_email_2'] = $partner_message['second_text'];
                    break;					
			}
		}
    }


/*  name: checkTagName
    date: 2018-03-18
    auth: VVenning
    desc: check tag name against tags table, return tag info if found

//  2019-03-19 1134 VVenning - New function: checkTagName() 
*/  private function checkTagName($tag_name=null, $client_id=null)
    {
        if($tag_name == null) { $tag_name = $this->tag_name; }
        if($client_id == null) { $client_id = $this->arr_client['id']; }

        $this->o_tags = new m_tags;

    //  If m_tags object cannot be created
        if(!$this->o_tags)
        {
            $this->who  = SOFTWARE.", ".HARDWARE;
            $this->what = "AADSync API problem.   Attempt to create tags object for database lookup failed.";
            $this->desc = "AADSync: File - ActiveDirectorySyncModel.php.  Function -  checkTagName().  Could not create m_tag object.";

            $this->Terminate("404", $this->desc);
        }

        $this->o_tags->conn = $this->cxn_obj->conn;

        $this->arr_tag      = $this->o_tags->select_by_name($tag_name, $client_id);

        if(!$this->arr_tag || count($this->arr_tag) < 1)
        {
            return false;
        }
        else
        {        
            return $this->arr_tag;
        }
    }


/*  name: insertTagRecord
    date: 2018-03-19 1133
    auth: VVenning
    desc: call m_tags to insert new tag
//  2019-03-19 1134 VVenning - New function: insertTagRecord().

*/  private function insertTagRecord($tag_name=null, $client_id=null)
    {   
        if($tag_name == null ) { $tag_name  = $this->tag_name; }
        if($client_id == null) { $client_id = $this->client_id; }
  
        $this->o_tags = new m_tags;

    //  If m_tags object cannot be created
        if(!$this->o_tags)
        {
            $this->who  = SOFTWARE.", ".HARDWARE;
            $this->what = "AADSync API problem.   Attempt to create tags object for database lookup failed.";
            $this->desc = "AADSync: File - ActiveDirectorySyncModel.php.  Function - insertTagRecord().  Could not create m_tag object.";

            $this->Terminate("404", $this->desc);
        }

        $this->o_tags->conn = $this->cxn_obj->conn;

        $this->o_tags->client_id = $client_id;
        $this->o_tags->name      = $tag_name;

        $this->o_tags->insert();

        $this->tag_id = $this->o_tags->last_inserted_id; 
    }

	
/*  name: create_user
    date: 
    auth: VVenning
    desc: call m_user insert method
//  2019-03-19 1141 VVenning - Modify create_user to process phone numbers and tags
*/  private function create_user($oneUser)
    {
        $this->o_users->password          = md5("breach".time("Y-m-d h:i:s"));
		$this->o_users->first_name        = isset( $oneUser['first_name']) ? $oneUser['first_name'] : "";
        $this->o_users->last_name         = isset( $oneUser['last_name']) ? $oneUser['last_name'] : "";
        $this->o_users->email             = isset($oneUser['email']) ? $oneUser['email'] : ""; 
        $this->o_users->screen_name       = isset($oneUser['screen_name']) ? $oneUser['screen_name'] : "user-".randomString(7);
	//  job_function
        $this->o_users->phone_number      = isset($oneUser['phone_number'])    ? $oneUser['phone_number'] : "";
        $this->o_users->phone_number_ext  = isset($oneUser['phone_number_ext']) ? $oneUser['phone_number_ext'] : "";
        $this->o_users->cell_number       = isset($oneUser['cell_number'])     ? $oneUser['cell_number'] : "";

        $this->o_users->address           = isset($oneUser['address'])     ? $oneUser['address'] : "";
        $this->o_users->address2          = isset($oneUser['address2'])     ? $oneUser['address2'] : "";
        $this->o_users->city              = isset($oneUser['city'])     ? $oneUser['city'] : "";
        $this->o_users->state             = isset($oneUser['state'])     ? $oneUser['state'] : "";
        $this->o_users->zip               = isset($oneUser['zip'])     ? $oneUser['zip'] : "";
        $this->o_users->link              = isset($oneUser['link'])     ? $oneUser['link'] : "";
        $this->o_users->active            = ($oneUser['active'] == true) ? 1 : 0;
        $this->o_users->dont_send_email   = false;
        $this->o_users->role_id           = isset($oneUser['role_id']) ? $oneUser['role_id'] : 4; 
        $this->o_users->client_id         = $this->client_id;
        $this->o_users->partner_id        = $this->partner_id;

        $this->o_users->tokenhash         = $oneUser['tokenhash'];

    //  2018-09-24 1303 VVenning - Set dont_send_email to true on new user record
        $this->o_users->dont_send_email = false;
        
        $this->o_users->insert();

        $this->database_error    = isset($this->o_users->database_error) ? $this->o_users->database_error : null;
        $this->last_inserted_id  = isset($this->o_users->last_inserted_id) ? $this->o_users->last_inserted_id : null;

        if(isset($this->database_error))
        {
            $this->outlier_report .= '{"' . $oneUser['email'] . '": "' . $this->database_error . '"},'.PHP_EOL;
        }
        else
        {
        //  Not an outlier situation
        //  $this->outlier_report .= '{"' . $oneUser['email'] . '": "INSERT_SUCCESSFUL"},'.PHP_EOL;
        }
    }
    
/*  name: update_user
    date: 
    auth: VVenning
    desc: call m_user update method.
    note: As of now, calls a method in m_users that is only used in ADSync

//  2019-03-19 1141 VVenning - Modify update_user to process phone numbers and tags

*/  private function update_user($oneUser)
    {
		echo "update_user";
		
		if( isset( $oneUser['password'])  )
		{
			$this->o_users->password_old = $this->existingUser['password'];
			$this->o_users->password     = $oneUser['password'];
		}

		if(isset( $oneUser['first_name']))       { $this->o_users->first_name = $oneUser['first_name']; }
		if(isset( $oneUser['last_name']))        { $this->o_users->last_name = $oneUser['last_name'];  }
		if(isset( $oneUser['email']))            { $this->o_users->email = $oneUser['email'];  }
		if(isset( $oneUser['screen_name']))      { $this->o_users->screen_name = $oneUser['screen_name'];  }
	//  job_function
		if(isset( $oneUser['phone_number']))     { $this->o_users->phone_number = $oneUser['phone_number'];  }
		if(isset( $oneUser['phone_number_ext'])) { $this->o_users->phone_number_ext = $oneUser['phone_number_ext'];  }
		if(isset( $oneUser['cell_number']))      { $this->o_users->cell_number = $oneUser['cell_number'];  }
 
		if(isset( $oneUser['address']))          { $this->o_users->address = $oneUser['address'];  }
		if(isset( $oneUser['address2']))         { $this->o_users->address2 = $oneUser['address2'];  }
		if(isset( $oneUser['city']))             { $this->o_users->city = $oneUser['city'];  }
		if(isset( $oneUser['state']))            { $this->o_users->state = $oneUser['state'];  }
		if(isset( $oneUser['zip']))              { $this->o_users->zip = $oneUser['zip'];  }
		if(isset( $oneUser['link']))             { $this->o_users->link = $oneUser['link'];  }

        if(isset( $oneUser['active']))           { $this->o_users->active = $oneUser['active']; }


		if(isset( $oneUser['dont_send_email']))  { $this->o_users->dont_send_email = $oneUser['dont_send_email'];  }
		if(isset( $oneUser['role_id']))          { $this->o_users->role_id = $oneUser['role_id'];  }
		if(isset( $oneUser['client_id']))        { $this->o_users->client_id = $oneUser['client_id'];  }
		if(isset( $oneUser['partner_id']))       { $this->o_users->partner_id = $oneUser['partner_id'];  }
		if(isset( $oneUser['tokenhash']))        { $this->o_users->tokenhash = $oneUser['tokenhash'];  }


        $this->o_users->id = $this->existingUser['id'];

        $this->o_users->update();

        $this->database_error = isset($this->o_users->database_error) ? $this->o_users->database_error : null;

        if(isset($this->database_error))
        {
            $this->outlier_report .= '{"'.$oneUser['email'].'": ' . $this->database_error.'},'.PHP_EOL;
        }
        elseif($this->o_users->active == 0)
        {
        //  Not an outlier situation
        //  $this->outlier_report .= '{"' . $oneUser['email'] . '": "DELETE_SUCCESSFUL"},'.PHP_EOL;
        }
        else
        {
        //  Not an outlier situation
        //  $this->outlier_report .= '{"' . $oneUser['email'] . '": "UPDATE_SUCCESSFUL"},'.PHP_EOL;   
        }
    }
    
/*  name: redact_user
    date: 
    auth: VVenning
    desc: call m_user update method, set active flag to inactve
*/  private function redact_user()
    {
        $this->o_users->update();
    }

/*  name: send_email
    date: 
    auth: VVenning
    desc: see api_tools/email4API.php for actual code that sends email
*/  private function send_email($recipients=SOFTWARE, $subject, $body)
    {
        date_default_timezone_set('US/Eastern');
        $the_time = date("Y-m-d h:i:s", time());
		
	//	$x = false;
        $x = email4API($recipients, $subject, $body);

        if($x == false)
        {
            $this->log_it("Email not accepted for delivery. - ".$subject." - ".$body);
        }
    }

		
/*  name: log_it
    date: 
    auth: VVenning
    desc: 
    param: string
*/  private function log_it($msg)
    {
		$msg = str_replace("<br />", PHP_EOL, $msg);


        date_default_timezone_set('US/Eastern');
        $the_time = date("Y-m-d h:i:s", time())." - ";
        $x = fwrite($this->myfile, $the_time.$msg.PHP_EOL);
    }

/*  name: Terminate
    date: 
    auth: VVenning
    desc: echo http err codes
          "She's leaving home after living alone for so many years" - The Beatles
          description param for if I want to send more detial in email
*/  private function Terminate($err, $description=null)
    {
       switch ($err) 
       {
            case "200":
            //  header('HTTP/1.1 200 Success', true, 200);
                $msg = "200";

                break;

            case "400":
            //  header('HTTP/1.1 400 Bad Request', true, 400);
                $msg = "400 Bad Request";
				
				$this->status = MAJOR_ERROR_CODE;
				
                break;

            case "401":
            //  header('HTTP/1.1 401 Unauthorized', true, 401);
                $msg = "401 Unauthorized";
				
				$this->status = MAJOR_ERROR_CODE;
				
                break;

            case "404":
            //  header("HTTP/1.1 404 Not Found", true, 404);
                $msg = "404 Not Found";
				
				$this->status = FATAL_ERROR_CODE;

                break;

            case "405":
            //  header('HTTP/1.1 405 - method not allowed', true, 405);
                $msg = "405 - method not allowed";
				
				$this->status = MAJOR_ERROR_CODE;
                break;

            default:
            //  header("HTTP/1.1 418 I'm a teapot", true, 418);
                $msg = "418 I'm a teapot";
            //  $err = 418;
        }

    //  Process 200 Successs
        if($err == "200")
        {
        //  Log sucess
            $this->txt .= "Success: tenant id - ".$this->AAD_client_id;

            if($description){$this->txt .= PHP_EOL.$description; }

            $this->txt .= PHP_EOL;
            $this->log_it($this->txt.PHP_EOL);
        }
        else
        {
        //  On failure send email
        //  Log failure
            $this->txt .= $msg;

        //  Send email, use description param
            $this->send_email($this->who, $this->what, $this->desc);


            $this->log_it($this->what . ": " . $this->desc);
        
		//	2018-09-11 1241 VVenning - Restrict circumstaces where we kill the process 
			if($this->status == FATAL_ERROR_CODE){ die; }
		}
    }
}
