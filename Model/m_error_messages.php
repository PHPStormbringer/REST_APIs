<?php
/* 
	name: m_error_messages
	date: 2019-01-07
	auth: VVenning
	desc: limited CRUD for error_messages table.  Create, Update, Retrieve, Deactivate

*/

class m_error_messages
{
    public $id;
    public $email_to;
    public $subject;
    public $text;

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
//  desc: select_all for error_messages table
    public function select_all()
    {

        $sql  = "SELECT * FROM ".ERROR_MESSAGSES_TABLE;

        $stmt = $this->conn->prepare($sql);

        if($stmt)
        {
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
           $this->myfile = fopen("DatabaseErrLog.txt", "a") or die("Unable to open file!");

            $txt = "Failed prepare statement to select all records from ".ERROR_MESSAGSES_TABLE." - ".date("Y-d-m h:i:s", time()).PHP_EOL;

            fwrite($this->myfile, $txt);
            
            fclose($this->myfile);
        }
    }


//	name: select_by_id
//  date: 2019-01-07
//  auth: VVenning
//  desc: select_by_id for error_messages table
	public function select_by_id($id=null)
	{
        $this->email_to = "";
        $this->subject = "";
        $this->text = "";

        $sql  = "SELECT * FROM ".ERROR_MESSAGSES_TABLE." ";

        if($id == null)
        {
            $id = $this->id;
        }

        if($id == null)
        {
            return false;
        }
        else
        {
            $sql .= "WHERE id = ? ";
        }
        
        $stmt = $this->conn->prepare($sql);

        if($stmt)
        {

            $stmt->bind_param("i", $id);

            $stmt->execute();

            $res = $stmt->get_result();

            $row = $res->fetch_assoc();

            $this->email_to = $row['email_to'];
            $this->subject  = $row['subject'];
            $this->text     = $row['text'];
            
            $stmt->close();
        }
        else
        {
           $this->myfile = fopen("DatabaseErrLog.txt", "a") or die("Unable to open file!");

            $txt = "Failed prepare statement to select record by id from ".ERROR_MESSAGSES_TABLE." - ".date("Y-d-m h:i:s", time()).PHP_EOL;

            fwrite($this->myfile, $txt);
            
            fclose($this->myfile);
        }
	}
}

?>
