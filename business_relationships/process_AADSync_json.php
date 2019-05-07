<?php
/*  Notes and Comments
    name: process_AADSync_json.php (for ActiveDirectorySync)
    date: 2018-09-06 1435
    crea: Victor Venning
    desc: Landing point for API call.  Writes to file.  Calls model to process json.  Returns 401 or 200.  200 likely to be removed
    note: This is being modified from the original index.php File, created2018-06-05 1900

//	2019-02-08 1534 VVenning - utf8_encode entire file contents
*/
    require '../api_config/global_config.php';

	require_once("ActiveDirectorySyncModel.php");

	date_default_timezone_set('US/Eastern');

	$objADSync = new ActiveDirectorySyncModel;
//	If this fails, log and email
	if(!is_object($objADSync))
	{
	//	include '../api_config/api_config.php';
		include '../api_config/email_groups.php';
		include '../api_tools/email4API.php';
		
	//  log
		$file_prefix = date("Y-m-d_hi_",time());

		$txt  = date("Y-m-d h:i:s",time()).": 404 - Not Found.  Could not initialize ActiveDirectorySync model. The API is down.";
			
		$myfile = fopen("logs/".$file_prefix."InitialFatalError.txt", "a") or die("Unable to open file!");
		fwrite($myfile, $txt);
		fclose($myfile);

	//	email
		$email_address =  DEV_TEAM;
		$subject = "TEST: ADSync is down";
		$body = $txt;
		email4API($email_address, $subject, $body);
		die;
	}
	else
	{
		$arrFiles = scandir(AAD_Sync_Client_JSON_staging);

		$file_prefix = date("Y-m-d_hi_",time());
		$myfile = fopen("logs/".$file_prefix."Original_Payload.txt", "a") or die("Unable to open file!");

		foreach($arrFiles as $aFile)
		{

			if($aFile != "." && $aFile != "..")
			{
				if(file_exists ( AAD_Sync_Client_JSON_staging.$aFile ))
				{
				//	move file from staging to processing
					rename(AAD_Sync_Client_JSON_staging.$aFile, AAD_Sync_Client_JSON_process.$aFile);
					
				//	Extract file contents	
					$objADSync->jsonPayload = file_get_contents(AAD_Sync_Client_JSON_process.$aFile);

				//	move file from processing to archive
					rename(AAD_Sync_Client_JSON_process.$aFile, AAD_Sync_Client_JSON_archive.$aFile);
					
					$objADSync->jsonPayload = utf8_encode($objADSync->jsonPayload); // 2019-02-08 1534 VVenning - utf8_encode entire file contents

					$content_prefix = date("Y-m-d_his",time());
					$txt  = $content_prefix.": file process init.".PHP_EOL;
					$txt .= print_r($objADSync->jsonPayload, 1);
					$txt .= PHP_EOL.PHP_EOL;
					fwrite($myfile, $txt);

					$objADSync->process();
				}

			}
		}
		fclose($myfile);
	}

?>
