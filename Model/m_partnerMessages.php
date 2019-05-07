<?php
/* 
name: m_partnerMessages.php
date: 2018-08-26
auth: VVenning
desc: basic CRUD for m_partnerMessages table
*/
class m_partnerMessages
{
    private $id;
    private $client_id;
    public  $partner_id;
    private $subject;
    private $message_type;
    private $first_text;
    private $last_text;
    private $url;

	public  $arr_partner_messages;


    public function __construct()
    {
        $this->id = "";
        $this->client_id = "";
        $this->partner_id = "";
        $this->subject = "";
        $this->message_type = "";
        $this->first_text = "";
        $this->last_text = "";
        $this->url = "";

        $this->arr_partner_messages = array();

    }

//	name: selectByPartnerId
//	date: 2018-08-26
//	auth: VVenning
//	desc: select_all for partner_messages table
//	param: unique id
	public function selectByPartnerId($partner_id=null)
	{
	//	This allows you to pass in partner_id or use the partner_id property
        $partner_id = $partner_id ? $partner_id : $this->partner_id;

        if(is_numeric($partner_id))
        {
            $stmt = $this->conn->prepare("SELECT * FROM ".PARTNER_MESSAGES_TABLE." WHERE partner_id = ? order by message_type ASC");

            if($stmt)
            {
                $data = "";

                $stmt->bind_param("i", $partner_id);

                $stmt->execute();

                $res = $stmt->get_result();

                $i = 0;
                while ($row = $res->fetch_assoc()){
              
                    $this->arr_partner_messages[] = $row;

                    $i++;
                }

                return $this->arr_partner_messages;
                
                $stmt->close();

                
            }
            else
            {
                $this->myfile = fopen("DatabaseErrLog.txt", "a") or die("Unable to open file!");

                $txt = date("Y-d-m h:i:s", time()).": Failed prepare statement to select partner record by partner_id - ".$partner_id.PHP_EOL;
                fwrite($this->myfile, $txt);
                
                fclose($this->myfile);
            }
        }
        else
        {
            $this->myfile = fopen("DatabaseErrLog.txt", "a") or die("Unable to open file!");

            $txt = date("Y-d-m h:i:s", time()).": Attempted to select partner_message record using non numeric partner_id - ".$partner_id.PHP_EOL;
            fwrite($this->myfile, $txt);
            fclose($this->myfile);
        }
	}

//	name: selectByPartnerIdwithMessageType
//	date: 2018-11-06
//	auth: VVenning
//	desc: select_all for partner_messages table with text partner_message_type
//	param: unique id
    public function selectByPartnerIdwithMessageType($partner_id=null)
    {
    //	This allows you to pass in partner_id or use the partner_id property
        $partner_id = $partner_id ? $partner_id : $this->partner_id;

        if(is_numeric($partner_id))
        {
        //    $x  = "SELECT p.partner_id, p.client_id, t.id as message_type, t.message_type as message_type_label, p.subject, CONVERT(p.first_text USING utf8), p.url, CONVERT(p.second_text USING utf8) ";
            $x  = "SELECT p.partner_id, p.client_id, t.id as message_type, t.message_type as message_type_label, p.subject, p.first_text, p.url, p.second_text ";
            $x .= "FROM ".PARTNER_MESSAGES_TABLE." p ";
            $x .= "JOIN ".PARTNER_MESSAGE_TYPES_TABLE." t on p.message_type = t.id WHERE ";

            $stmt = $this->conn->prepare($x."partner_id = ? order by message_type ASC");

            if($stmt)
            {
                $data = "";

                $stmt->bind_param("i", $partner_id);

                $stmt->execute();

                $res = $stmt->get_result();

                $i = 0;
                while ($row = $res->fetch_assoc()){
            
                    $this->arr_partner_messages[] = $row;

                    $i++;
                }

                return $this->arr_partner_messages;
                
                $stmt->close();

                
            }
            else
            {
                $this->myfile = fopen("DatabaseErrLog.txt", "a") or die("Unable to open file!");

                $txt = date("Y-d-m h:i:s", time()).": Failed prepare statement to select partner record by partner_id - ".$partner_id.PHP_EOL;
                fwrite($this->myfile, $txt);
                
                fclose($this->myfile);
            }
        }
        else
        {
            $this->myfile = fopen("DatabaseErrLog.txt", "a") or die("Unable to open file!");

            $txt = date("Y-d-m h:i:s", time()).": Attempted to select partner_message record using non numeric partner_id - ".$partner_id.PHP_EOL;
            fwrite($this->myfile, $txt);
            fclose($this->myfile);
        }
    }
//	name: insertMessage
//	date: 2018-08-26
//	auth: VVenning
//	desc: insert for partner_messages table

	public function insertMessage($arr_message=null)
	{
		$arr_message = $arr_message ? $arr_message : $this->arr_partner_messages;
		
		$stmt = $this->conn->prepare("INSERT INTO ".PARTNER_MESSAGES_TABLE." (partner_id, client_id, message_type, subject, first_text, second_text, url, created) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");

        if($stmt)
        {
			
			$this->partner_id   = $arr_message['partner_id']; 
			$this->client_id    = $arr_message['client_id'];
			$this->message_type = $arr_message['message_type'];
			$this->subject      = $arr_message['subject'];
			$this->first_text   = $arr_message['first_text'];
			$this->second_text  = $arr_message['second_text'];
			$this->url          = $arr_message['url'];
			$this->created      = formattedNow();
			
            $this->date = date("Y-m-d", time());

            $stmt->bind_param("iiisssss", $this->partner_id, $this->client_id, $this->message_type, $this->subject, $this->first_text, $this->second_text, $this->created);

            if(!$stmt->execute())
            {
                $this->database_error  = "Execute failed: (" . $this->conn->errno . ") " . $this->conn->error;
                $this->database_error .= ".  INSERT: ". $this->partner_id." - ".time().PHP_EOL;
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
            $this->database_error .= ".  INSERT: ". $this->partner_id." - ".time().PHP_EOL;

        }
	}

//	name: update
//	date: 2018-08-26
//	auth: VVenning
//	desc: update for partner_messages table.  I'm still thinking about this philosophically
	public function update()
	{


	}


//	name: deactivate
//	date: 2018-08-26
//	auth: VVenning
//	desc: make a partner inactive.  This changes a single value
	public function deactivate()
	{
		//
	}


//	name: delete
//	date: 2018-08-26
//	auth: VVenning
//	desc: delete for partner_messages table.  WOn;t be used except in emergencies.  We deactivate, which is an update process
	public function delete()
	{
		//
	}	

}

?>