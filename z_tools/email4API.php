<?php
// ***********************************************************
// name: email4API.php
// date: 2018-05-29
// auth: vvenning
// desc: trustsecurenow api
// *****************************************************


//	2018-11-16 1348 VVenning - loggin emails sent in a format that leands itself to creating a SQL WHERE IN clause
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

    require_once 'logging.php'; 

//	require_once($_SERVER["DOCUMENT_ROOT"].'/api_config/global_config.php');
	require_once '../z_config/global_config.php';

/*
 *  name: email4API
 *  desc: send email messages from API
 *
 *  @param $email_address string
 *  @param $subject string
 *  @param $body string
 *  @param $arrAttachments array
 *
 *  @return 200|string (on error)
 * 
 */ function email4API($email_address, $subject, $body, $arrAttachments=null)
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
	//	$mail->Host = 'smtp.office365.com';
		$mail->Host = 'smtp.dotster.com';
		$mail->Port       = 587;
		$mail->SMTPSecure = 'tls';
		$mail->SMTPAuth   = true;
	//	$mail->Username = 'no-reply@pii-protect.com';
	//	$mail->Password = 'Trustno1!@PII';
		
		$mail->Username = 'no-reply@ringwoodcomputer.com';
		$mail->Password = 'V1ct@r0216';
		
		$mail->SetFrom('no-reply@ringwoodcomputer.com', 'FromEmail');
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


?>
