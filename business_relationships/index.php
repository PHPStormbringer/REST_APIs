<?php
/*  Notes and Comments
    name: index.php
    date: 
    crea: Victor Venning
    desc: Landing point for API call.  Writes json to file.
*/
    require '../z_config/global_config.php';
	require_once("tester.php");

	
	date_default_timezone_set('US/Eastern');

    if($_SERVER['REQUEST_METHOD'] === 'POST')
    {
	//	get json
		$json_payload = file_get_contents('php://input');
		
		if(!$json_payload)
		{
		//	
		}
		else
		{
		//	Write file contents to origin file
			$file_prefix = date("Y-m-d_his_",time());
			$myfile = fopen("logs/".$file_prefix."Original_Payload.txt", "a") or die("Unable to open file!");
			fwrite($myfile, $json_payload);
			fclose($myfile);
		
		//	convert json to array
			$arrPayload = json_decode($json_payload, true);

			if($arrPayload['stage_payload'] !== true)
			{
				
				$obj_tester = new tester;
				if(!is_object($obj_tester))
				{
					include '../z_config/email_groups.php';
					include '../z_tools/email4API.php';
					
				//  log
					$file_prefix = date("Y-m-d_hi_",time());

					$txt  = date("Y-m-d h:i:s",time()).": 404 - Not Found.  Could not initialize tester model. The API is down.";
						
					$myfile = fopen("logs/".$file_prefix."InitialFatalError.txt", "a") or die("Unable to open file!");
					fwrite($myfile, $txt);
					fclose($myfile);

				//	email
					$email_address =  DEV_TEAM;
					$subject = "TEST: tester is down";
					$body = $txt;
					email4API($email_address, $subject, $body);
					die;
				}

			//	I got a string, send them a string
				$obj_tester->json_payload = utf8_encode($json_payload); // 2019-02-08 1534 VVenning - utf8_encode entire file contents
//				$content_prefix = date("Y-m-d_his",time());
//				$txt  = $content_prefix.": file process init.".PHP_EOL;
//				$txt .= print_r($obj_tester->json_payload, 1);
//				$txt .= PHP_EOL.PHP_EOL;
//				fwrite($myfile, $txt);

				$obj_tester->process();
			}
			else
			{
			//	use time and clientID from array to make unique file name
				$AADSync_filename = time()."_".$arrPayload['client_id'].".txt";

			//	write json to file
				$fp = fopen(JSON_staging.$AADSync_filename, 'w');
				
				print_r(error_get_last ());
				
				fwrite($fp, $json_payload);
				fclose($fp);
			}
		}
	}
	else
	{
	//  405 Method Not Allowed
		$txt  = date("Y-m-d h:i:s",time()).": 405 - Method Not Allowed.  The API was called, but not via POST. ";
		$txt .= "REQUEST_METHOD was ".print_r($_SERVER['REQUEST_METHOD'],1).".  ";
		$txt .= "Calling URL listed as " . print_r($_SERVER['HTTP_REFERER'],1) . PHP_EOL;

		$myfile = fopen("ADSync.txt", "a") or die("Unable to open file!");
		fwrite($myfile, $txt);
		fclose($myfile);
	}
	
?>
