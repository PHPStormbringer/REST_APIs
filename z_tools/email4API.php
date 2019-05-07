<?php
// ***********************************************************
// name: email4API.php
// date: 2018-05-29
// auth: vvenning
// desc: trustsecurenow api
// *****************************************************

//	2018-08-28 1641 VVenning - Changed name of function from 'emailMicroTraining' to 'emailMandrillTemplate'
//	2018-09-19 1304 VVenning - Change Mandrill template.  This was already changed in production
//  2018-10-03 1632, add param 'MandrillKey' to function 'emailMandrillTemplate'
//  2018-10-04 0850 VVenning - Use mandrill key from json
//  2018-10-04 1007 VVenning - If this flag true, use test key

//	2018-11-16 1348 VVenning - loggin emails sent in a format that leands itself to creating a SQL WHERE IN clause
//	2018-11-19 1150 VVenning - Log Mandrill Payload
//	2018-11-19 1159 VVenning - Log Mandrill Key
//	2018-11-26 0915 VVenning - Log every return from Mandrill for ContactUsers
//	2019-02-08 1101 VVenning - Send Mandril payload logs to log directory and datetime prefix
//	2019-02-13 1156 VVenning - Add $async, $ip_pool, $send_at params to sendTemplate()
//	2019-04-16 1055 VVenning - Added $arrAttachments param
//  2019-04-16 1109 VVenning - If there is an array of attachments, process it




//  Import PHPMailer classes into the global namespace
//  These must be at the top of your script, not inside a function
    use PHPMailer\PHPMailer\PHPMailer;
    use PHPMailer\PHPMailer\Exception;

//  require '../api_tools/PHPMailer/src/Exception.php';
//  require '../api_tools/PHPMailer/src/PHPMailer.php';
//  require '../api_tools/PHPMailer/src/SMTP.php';

    require 'PHPMailer/src/Exception.php';
    require 'PHPMailer/src/PHPMailer.php';
    require 'PHPMailer/src/SMTP.php';

	require_once 'Mandrill/src/Mandrill.php'; //Not required with Composer

    require_once 'logging.php';

//	require_once($_SERVER["DOCUMENT_ROOT"].'/api_config/global_config.php');
	require_once '../z_config/global_config.php';


	function email4API($email_address, $subject, $body, $arrAttachments=null)
	{
	//	echo $email_address."<br />";
	//	echo $subject."<br />";
	//	echo $body."<br />";

		$mail = new PHPMailer(true);

        $addresses = explode(',', $email_address);
        foreach ($addresses as $address) 
        {
            $mail->AddAddress($address);
        }

    //  2019-04-16 1109 VVenning - If there is an array of attachments, process it
		if(isset($arrAttachments) && is_array($arrAttachments))
		{
			foreach($arrAttachments as $anAttach)
			{
				$mail->AddAttachment( $path_to_file , $name_of_file );
			}
		}

		$mail->isSMTP();
		$mail->Host = 'smtp.office365.com';
		$mail->Port       = 587;
		$mail->SMTPSecure = 'tls';
		$mail->SMTPAuth   = true;
		$mail->Username = 'no-reply@pii-protect.com';
		$mail->Password = 'Trustno1!@PII';
		$mail->SetFrom('no-reply@pii-protect.com', 'FromEmail');
	//	$mail->addAddress($email_address, 'ToEmail');
	//  $mail->SMTPDebug  = 3;
	//  $mail->Debugoutput = function($str, $level) {echo "debug level $level; message: $str";}; //$mail->Debugoutput = 'echo';
		$mail->IsHTML(true);

		$mail->Subject = $subject;
		$mail->Body    = $body;
		$mail->AltBody = $body;
			
	if(!$mail->send()) {
		    return 'Message could not be sent.  Mailer Error: ' . $mail->ErrorInfo;
		} else {
		    return '200';
		}	
	}


/*  name: emailBSNwelcome
    date: 2018-06-26
    auth: VVenning
    desc: Sendcome email to new users
    	  This is the cakePHP version. Need to build something platform independent

I have created a new template, template name: aad-sync-template 
If you send it the merge tag of WELCOMEMESSAGE it will send a welcome message. If you send a merge tag of WELCOMEBACKMESSAGE it will send a welcome back message. If you send a merge tag of DISABLEMESSAGE it will send a the disabled message. Be sure to only send the merge tag you want to use.


	note: 2018-08-10 VVenning - pass entire partner array, not just pcode
*/  function emailBSNwelcome($fname, $email, $arrPartner, $passhash=null, $subject=null, $from_email=null, $email_type=null) 
	{
		$async   = null;
		$ip_pool = null;
		$send_at = null;

        switch($email_type)
		{
			case "3":
				$message_type_1 = 'WELCOMEMESSAGE';
				$message_type_2 = 'POSTURLMESSAGE';
				$message_text_1 =  $arrPartner['welcome_text_1'];
				$message_text_2 =  $arrPartner['welcome_text_2'];

			//  Requirement - Checking the deferred sending flag in the messages table for the partner.  
				if($arrPartner['deferred_sending'] === 1)
				{
				//  Requirement - If enabled, read the deferred hours and deferred days.  
				//	86400 seconds in a day
				//	3600 seconds in an hour
				//  Requirement -   Calculate the send_at time by adding the hours and days to the current time.
					$date_in_seconds = time() + ($arrPartner['deferred_days'] * 86400) + ($arrPartner['deferred_hours'] * 3600);
					$send_at = gmdate("Y-m-d H:m:s", $date_in_seconds);
				}

				if($subject == null)   { $subject = Mandrill_EMPLOYEE_WELCOME_SUBJECT;   }
				break;;
			
			case "1";

				$message_type_1 = 'WELCOMEBACKMESSAGE';
                $message_type_2 = 'POSTURLMESSAGE';
				
				if($subject == null)
				{ 
					$subject = Mandrill_EMPLOYEE_WELCOMEBACK_SUBJECT;
				}
				$message_text_1 = $arrPartner['welcomeback_text_1'];
				$message_text_2 = $arrPartner['welcomeback_text_2'];

				break;
			
			case "2":
			
				$message_type_1 = 'DISABLEMESSAGE';
				$message_type_2 = 'POSTURLMESSAGE';
				$message_text_1 = $arrPartner['deactivated_text_1'];
				$message_text_2 = $arrPartner['deactivated_text_2'];
				
				if($subject == null)   
				{ 
			        $subject = "Notification from PII-Protect!";   
				}
				break;
			
			default:
				
				$message_type_1 = 'WELCOMEMESSAGE';
				$message_type_2 = 'POSTURLMESSAGE';
				$message_text_1 = $arrPartner['welcome_text_1'];
				$message_text_2 = $arrPartner['welcome_text_2'];

				if($subject == null)   { $subject = Mandrill_EMPLOYEE_WELCOME_SUBJECT;   }
				break;
		}
		
		if($from_email == null){ $from_email = Mandrill_EMPLOYEE_WELCOME_FROM; }

		try
		{
			$mandrill = new Mandrill(xMandrill_API_KEY);
		} 
		catch(Mandrill_Error $e) 
		{
		// 	Mandrill errors are thrown as exceptions
			$err = 'A mandrill error occurred: ' . get_class($e) . ' - ' . $e->getMessage();
		    universal_log_it("logs/mandrill_errors.txt", $err);

		    throw $e;
		}
		catch (Exception $e) 
		{
		    $err = "Caught exception: ". $e->getMessage();

		    universal_log_it("logs/mandrill_errors.txt", $err);
		}


	//	2018-11-19 1159 VVenning - Log Mandrill Key
	    universal_log_it("APIkeyLog.txt","API key used: ".Mandrill_API_KEY);

		curl_setopt($mandrill->ch, CURLOPT_SSL_VERIFYHOST, 2);
		curl_setopt($mandrill->ch, CURLOPT_SSL_VERIFYPEER, true);
		curl_setopt($mandrill->ch, CURLOPT_CAINFO, 'C:/bin/CA/cacert.pem'); // C:\bin\CA\cacert.pem"

    //  2018-09-19 1304 VVenning - Change Mandrill template.  This was already changed in production
    //	$template_name = Mandrill_EMPLOYEE_WELCOME;
    	$template_name = AAD_SYNC_TEMPLATE;

    	$bsn_password_url = BSN_PASSWORD_RESET;

    	if($passhash)
    	{
    		$bsn_password_url = $bsn_password_url.$passhash;  // We do this because the hash contains periods and forward slashes
    	}

    	if(isset($pcode))
    	{
    		$bsn_password_url ."?brand_key=".$arrPartner['brand_key'];
    	}
		else
		{
		    $pcode = "";
		}
		$template_content = array(
		    array(
		        'name' => 'FNAME',
		        'content' => $fname),
		    array(
		        'name' => 'URL',
		        'content' => $bsn_password_url),
            array(
		        'name' => 'PCODE',
		        'content' => $arrPartner['brand_key'])
		);

// 'send_at' => '2019-02-08 11:30:00',
	    
		$message = array(
	    'html' => '<p>this is a test message with Mandrill\'s PHP wrapper!.</p>',
	    'subject' => $subject,
	    'from_email' => $from_email,
	    'to' => array(array('email' => $email, 'name' => $fname)),
	    'merge_vars' => array(array(
	        'rcpt' => $email,
	        'vars' => array(
	            array(
	                'name' => 'FNAME',
	                'content' => $fname),
	            array(
	                'name' => 'URL',
	                'content' => $bsn_password_url),
	            array(
	                'name' => 'PCODE',
	                'content' => $arrPartner['brand_key'])
	    

	    ))),
	    'global_merge_vars' => array(
                array(
                    'name' => $message_type_1,
                    'content' => $message_text_1),
                array(
                    'name' =>  $message_type_2,
                    'content' => $message_text_2),
                array(
                    'name' => 'PARTNERNAME',
                    'content' => '<div style="text-align: center;">'.$arrPartner['name'].'</div>')
            )
		);

	//	2019-02-08 1101 VVenning - Send Mandril payload logs to log directory and datetime prefix
		$file_prefix = date("Y-m-d_hi_",time());

	//	2018-11-19 1150 VVenning - Log Mandrill Payload
		universal_log_it("logs/".$file_prefix."mandrill_send_payload.txt", "template name: ".$template_name);
		$x = print_r($template_content,1);
		universal_log_it("logs/".$file_prefix."mandrill_send_payload.txt", "template content: ".$x);
		$x = print_r($message,1);
		universal_log_it("logs/".$file_prefix."mandrill_send_payload.txt", "message: ".$x);


		try
		{
		//	2019-02-13 1156 VVenning - Add $async, $ip_pool, $send_at params to sendTemplate()
		    $result = $mandrill->messages->sendTemplate( $template_name, $template_content, $message, $async, $ip_pool, $send_at);
		//	$result = $mandrill->messages->sendTemplate( $template_name, $template_content, $message);

			return $result;
		} 
		catch(Mandrill_Error $e) {
		// 	Mandrill errors are thrown as exceptions
			$err = 'A mandrill error occurred: ' . get_class($e) . ' - ' . $e->getMessage();
		//	echo $err;
			universal_log_it("logs/mandrill_errors.txt", $err);
	
			throw $e;
		}
		catch (Exception $e) 
		{
			$err = "Caught exception: ". $e->getMessage();
			universal_log_it("logs/mandrill_errors.txt", $err);

			return false;
		}
	
	}

//	2018-08-28 1641 VVenning - Changed name of function from 'emailMicroTraining' to 'emailMandrillTemplate' 
/*  name: emailMandrillTemplate
    date: 2018-07-31
    auth: ALowestein
    desc: Send microtraining email users
	Note: 2018-07-31 1711 VVenning - Probably messed it up
//  2018-10-03 1632, add param 'MandrillKey' to function 'emailMandrillTemplate' 
	
*/  function emailMandrillTemplate($emails, $mergeVars, $globalMergeVars, $template_name, $subject, $from_email, $MandrillKey, $file_prefix=null) 
	{
		foreach($globalMergeVars as $gMV)
		{
			if($gMV['name'] == 'TRACKINGTAGS')
			{
				$tag = $gMV['content'];
				break;
			}
			else
			{
				
				$tag = "pii-protect";
			}
		}
	
	    $args = array(
	        'subject' => $subject,
	        'from_email' => $from_email,
	        'from_name' => "*|NEWFROM|*",
	        'to' => $emails,
	        'headers' => array(),
	        'important' => false,
	        'track_opens' => true,
	        'track_clicks' => true,
	        'auto_text' => true,
	        'auto_html' => false,
	        'inline_css' => true,
	        'tracking_domain' => null,
	        'signing_domain' => null,
	        'return_path_domain' => null,
	        'merge' => true,
	        'merge_vars' => $mergeVars,
	        'global_merge_vars' => $globalMergeVars,
	        'merge_language' => 'mailchimp',
	        'tags' => $tag ? array( $tag ) : null
	    );

		$x = print_r($args,1);
		universal_log_it("logs/".$file_prefix."mandrill_send_payload.txt", $x);

	// 	2018-10-04 1007 VVenning - If this flag true, use test key
	    if(Mandrill_USE_TEST_KEY == true)
        {
		    $MandrillKey = Mandrill_API_KEY; // test key
	    }

	    universal_log_it("logs/".$file_prefix."APIkeyLog.txt","API key used: ".$MandrillKey);


	//  2018-10-04 0850 VVenning - Use mandrill key from json
	//	$mandrill = new Mandrill(Mandrill_API_KEY);
		try
		{
			$mandrill = new Mandrill($MandrillKey);
		} 
		catch(Mandrill_Error $e) 
		{
		// 	Mandrill errors are thrown as exceptions
			$err = 'A mandrill error occurred: ' . get_class($e) . ' - ' . $e->getMessage();
		    universal_log_it("logs/mandrill_errors.txt", $err);

		    throw $e;
		}
		catch (Exception $e) 
		{
		    $err = "Caught exception: ". $e->getMessage();

		    universal_log_it("logs/mandrill_errors.txt", $err);
		}

    	$template_content = array(
        array(
            'name' => 'main', // mc:edit="main"
            'content' => ''
	        )
	    );
    
//    	$template_content = array(
//			array(
//				'name' => 'main', // mc:edit="main"
//				'content' => $message
//				)
//			);

    //	2018-11-16 1348 VVenning - loggin emails sent in a format that leands itself to creating a SQL WHERE IN clause
		$strEmail="";
		foreach($emails as $em)
		{
			$strEmail .= '"'.$em['email'].'",';
		}

		$z = strrpos($strEmail, ',');
		$strEmail = substr($strEmail, 0, $z);

	    universal_log_it("logs/".$file_prefix."EmailsLog.txt",$strEmail);
	
		try
		{
		// doc: https://mandrillapp.com/api/docs/messages.php.html#method=send-template			
	    	$result = $mandrill->messages->sendTemplate( $template_name, $template_content, $args );

		//	2018-11-26 0915 VVenning - Log every return from Mandrill for ContactUsers
			universal_log_it("logs/".$file_prefix."mandrill_return.txt", print_r($result, 1));

			return $result;
		} 
		catch(Mandrill_Error $e) 
		{
		// 	Mandrill errors are thrown as exceptions
			$err = 'A mandrill error occurred: ' . get_class($e) . ' - ' . $e->getMessage();
		//	echo $err;

		    universal_log_it("logs/".$file_prefix."mandrill_errors.txt", $err);

		    return false;
		}
	}


	/**
	 * Sends welcome email to client
	 *
	 * @param  $partner_id
	 * @param  $user_email
	 * @param  $user_name
	 * @param  $tokenhash (user's token hash to reset password)
	 * @param  $type (Pax8 or BSN)
	 * @return mixed
	 */
	function sendPartnerWelcomeEmail($partner_id,$partner_name, $user_email, $user_name, $tokenhash, $type){
        $email              = array($user_email);
        $email_subject      = "Welcome to the Breach Secure Now! Partner Program";
        $reset_password_url = BSN_PASSWORD_RESET.$tokenhash;

        if($type == "Pax8"){
            $contact = "support@pax8.com";
            $id 				= base64_encode('this user id is: '.$partner_id);
            $upload_logo_url     = "https://".$_SERVER['HTTP_HOST'].'/Pax8PartnerSignUp/partner/upload_logo.php?signature='.$id;
        }else{
            $contact = "operations@breachsecurenow.com";
            $id 			       = base64_encode('this user id is: '.$partner_id);
            //@TODO change this for bsn url
            $upload_logo_url     = "https://".$_SERVER['HTTP_HOST'].'/Pax8PartnerSignUp/partner/upload_logo.php?signature='.$id;
		}

        $merge_vars = array();
        $global_merge_vars = array(
            array('name' => 'FNAME', 'content' => $user_name),
            array('name' => 'SETPASSWORDURL','content' => $reset_password_url),
            array('name' => 'UPLOADLOGOURL','content' => $upload_logo_url),
            array('name' => 'CONTACTUSEMAIL','content' => $contact),
            array('name' => 'TRACKINGTAGS','content' => "Pax8PartnerSignup"),
            array('name' => 'NEWFROM','content' => 'Breach Secure Now Operations'),
            array('name' => 'EMAIL_FROM','content' => 'operations@breachsecurenow.com')
        );

        $arr_email = array( array(
            'email' => $user_email,
            'name' => '',
            'type' => 'to'
        ));


        $response = emailMandrillTemplate( $arr_email,
            $merge_vars,
            $global_merge_vars,
            'new-partner-welcome-v1',
            $email_subject,
            'operations@breachsecurenow.com',
            MANDRILL_API_KEY_FOR_BSN_WELCOME_MESSAGE
        );

        return $response;
	}

	/**
	 * Sends welcome email to client
	 *
	 * @param  $user_email
	 * @param  $user_name
	 * @param  $client_name
	 * @param  $partner_name
	 * @param  $type (Pax8 or BSN)
	 * @return mixed
	 */
	function sendClientWelcomeEmail($user_email,$user_name, $client_name,$partner_name, $type ){
        $email              = array($user_email);
        $email_subject      = $partner_name." / Breach Prevention Platform – Next Steps!";

        if($type == "Pax8"){
            $contact = "support@pax8.com";
        }else{
            $contact = "operations@breachsecurenow.com";
        }

        $merge_vars = array();
        $global_merge_vars = array(
            array('name' => 'FNAME', 'content' => $user_name),
            array('name' => 'CLIENTNAME','content' => $client_name),
            array('name' => 'CONTACTUSEMAIL','content' => $contact),
            array('name' => 'TRACKINGTAGS','content' => "Pax8PartnerSignup"),
            array('name' => 'NEWFROM','content' => $partner_name)
        );

        $arr_email = array( array(
            'email' => $user_email,
            'name' => '',
            'type' => 'to'
        ));


        $response = emailMandrillTemplate( $arr_email,
            $merge_vars,
            $global_merge_vars,
            'new-bpp-welcome-v1',
            $email_subject,
            Mandrill_EMAIL_FROM_NO_REPLY,
            MANDRILL_API_KEY_FOR_BSN_WELCOME_MESSAGE
        );

        return $response;
	}


?>
