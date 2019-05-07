<?php
/* 
	name: m_tags
	date: 2019-03-15
	auth: VVenning
	desc: limited CRUD for tags table.  Create, Retrieve
*/

class m_tags
{
    public $id;
    public $client_id;
    public $name;
    public $created;

    public $tag_name;
    public $arr_tags;
    public $data;
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
//  date: 2019-03-15
//  auth: VVenning
//  desc: select_all for tags table
    public function select_all()
    {
        $sql  = "SELECT * FROM ".TAGS_TABLE;

        $stmt = $this->conn->prepare($sql);

        if($stmt)
        {
            if(!$stmt->execute())
            {
                $this->log_database_error("File: m_tags.php.  Message: Execute failed for function select_all().  MySQL_Error: ".$stmt->error);
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
            $this->log_database_error("File: m_tags.php.  Message: Prepare failed for function select_all().");
        }
    }


//	name: select_by_id
//  date: 2019-03-15
//  auth: VVenning
//  desc: select_by_id for tags table
	public function select_by_id($id=null)
	{
        $sql  = "SELECT * FROM ".TAGS_TABLE." ";

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

            if(!$stmt->execute())
            {
                $this->log_database_error("File: m_tags.php.  Message: Execute failed for function select_by_id().  MySQL_Error: ".$stmt->error);
            } 
            else
            {
                $res = $stmt->get_result();
                return $res->fetch_assoc();

                $stmt->close();
            }
        }
        else
        {
            $this->log_database_error("File: m_tags.php.  Message: Prepare failed for function select_by_id().");
        }
	}


//  name: select_by_client_id
//  date: 2019-03-15
//  auth: VVenning
//  desc: select_by_event_id for tags table
    public function select_by_client_id($client_id=null)
    {
        $sql  = "SELECT * FROM ".TAGS_TABLE." ";

        if($client_id == null)
        {
            $client_id = $this->client_id;
        }

        if($client_id == null)
        {
            return false;
        }
        else
        {
            $sql .= "WHERE client_id = ? ";
        }
        
        $stmt = $this->conn->prepare($sql);

        if($stmt)
        {
            $stmt->bind_param("i", $client_id);

            if(!$stmt->execute())
            {
                $this->log_database_error("File: m_tags.php.  Message: Execute failed for function select_by_client_id().  MySQL_Error: ".$stmt->error);
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
            $this->log_database_error("File: m_tags.php.  Message: Prepare failed for function select_by_client_id().");
        }

    }

//  name: select_by_name
//  date: 2019-03-18
//  auth: VVenning
//  desc: select_by_id for tags table
    public function select_by_name($tag_name=null, $client_id=null)
    {
        $data = false;

        $sql  = "SELECT * FROM ".TAGS_TABLE." ";

        if($tag_name == null) { $tag_name = $this->tag_name; }
        if($client_id == null) { $tag_name = $this->client_id; }

        if($tag_name == null || $client_id == null) { return false; } else { $sql .= "WHERE name = ? AND client_id = ?"; }
        
        $stmt = $this->conn->prepare($sql);

        if($stmt)
        {
            $stmt->bind_param("si", $tag_name, $client_id);

            if(!$stmt->execute())
            {
                $this->log_database_error("File: m_tags.php.  Message: Execute failed for function select_by_name().  MySQL_Error: ".$stmt->error);
            } 
            else
            {
                $res = $stmt->get_result();
                return $res->fetch_assoc();

                $stmt->close();
            }
        }
        else
        {
            $this->log_database_error("File: m_tags.php.  Message: Prepare failed for function select_by_name().");
        }
    }


//	name: insert
//	date: 2019-03-15
//	auth: VVenning
//	desc: insert for tags table
	public function insert()
	{
        $this->database_error = null;

        $stmt = $this->conn->prepare("INSERT INTO ".TAGS_TABLE." (client_id, name, created) VALUES (?,?,?)" );

        if($stmt)
        {
            $this->created = date("Y-m-d h:i:s", time());

            $stmt->bind_param("iss", $this->client_id, $this->name, $this->created);

            if(!$stmt->execute())
            {
                $this->log_database_error("File: m_tags.php.  Message: Execute failed for function insert().  MySQL_Error: ".$stmt->error);
            } 
            else
            {  
                $this->last_inserted_id = $this->conn->insert_id;

            //  echo $this->last_inserted_id;
            }
            $stmt->close();
        }
        else
        {
            $this->log_database_error("File: m_tags.php.  Message: Prepare failed for function insert().");
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
        $the_time = date("Y-m-d h:i:s", time());
        $err_log = fopen("logs/DatabaseErrLog.txt", "a") or die("Unable to open file!");
        $err_txt  = $the_time." Table: ".TAGS_TABLE." - ";
        $err_txt .= $msg;
        $err_txt .= "  MySQL_error: (" . $this->conn->errno . ") " . $this->conn->error;
        fwrite($err_log, $err_txt.PHP_EOL);
        fclose($this->err_log);
    }
}

?>
