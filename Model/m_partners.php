<?php
/* 
name: m_parters
date: 2018-05-29
auth: VVenning
desc: basic CRUD for partners table

//  
*/
class m_partners implements databaseModel
{
	public $id;
	public $name;
	public $email;
	public $phone;
	public $ext;
	public $cell;
	public $address;
	public $address2;
	public $city;
	public $state;
	public $zip;
    public $country_code;
	public $link;
	public $active;
	public $logo;
	public $whitelabel;
	public $created;
	public $modified;

    public $last_inserted_id;
    public $conn;
	
//	name: select
//	date: 2018-05-29
//	auth: VVenning
//	desc: slect for partners table
	public function select()
	{
		//
	}


//	name: select_all
//	date: 2018-08-04
//	auth: VVenning
//	desc: select_all for partners table
	public function select_all($active=null)
	{

        $sql  = "SELECT * FROM ".PARTNERS_TABLE;

        if($active == null)
        {
        //  No WHERE clause
        }
        else
        {
            $sql .= " WHERE active = ? ";
        }

        $stmt = $this->conn->prepare($sql);

        if($stmt)
        {
            if    ($active === null)
            {
            //  bind no params
            }
            else
            {
                $stmt->bind_param("i", $active);
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

            $txt = "Failed prepare statement to select all partner records. - ".date("Y-d-m h:i:s", time()).PHP_EOL;

            fwrite($this->myfile, $txt);
            
            fclose($this->myfile);
        }
	}

//	name: select_byID
//	date: 2018-05-29
//	auth: VVenning
//	desc: select by id for partners table
//	param: unique id
	public function select_byID($id=null)
	{
        $id = $id ? $id : $this->id;

		if(is_numeric($id))
		{
	        $stmt = $this->conn->prepare("SELECT * FROM ".PARTNERS_TABLE." WHERE id = ?");

	        if($stmt)
	        {
	            $data = "";

	            $stmt->bind_param("i", $id);

	            $stmt->execute();

	            $res = $stmt->get_result();

	            $i = 0;
	            $data = $res->fetch_assoc();

	            return $data;
	            
	            $stmt->close();

	            
	        }
	        else
	        {
	            $this->myfile = fopen("DatabaseErrLog.txt", "a") or die("Unable to open file!");

	            $txt = date("Y-d-m h:i:s", time()).": Failed prepare statement to select partner record by id - ".$id.PHP_EOL;
	            fwrite($this->myfile, $txt);
	            
	            fclose($this->myfile);
	        }
	    }
	    else
	    {
            $this->myfile = fopen("DatabaseErrLog.txt", "a") or die("Unable to open file!");

            $txt = date("Y-d-m h:i:s", time()).": Attempted to select partner record by non numeric id - ".$id.PHP_EOL;
            fwrite($this->myfile, $txt);
            fclose($this->myfile);
	    }
	}

//	name: insert
//	date: 2018-05-29
//	auth: VVenning
//	desc: insert for partners table
	public function insert()
	{

        $this->created = date("Y-m-d h:i:s", time());

    //    echo "'".$this->name."', '".$this->email."', '".$this->link."', '".$this->logo."', '".$this->created."'";
        
        $stmt = $this->conn->prepare("INSERT INTO ".PARTNERS_TABLE." (name, email, link, logo, created) VALUES (?, ?, ?, ?, ?)");

        if($stmt)
        {
            $this->created = date("Y-m-d h:i:s", time());

            $stmt->bind_param("sssss", $this->name, $this->email, $this->link, $this->logo, $this->created);

			$stmt->execute();	
			$this->last_inserted_id = $this->conn->insert_id;
            
            $stmt->close();
        }
        else
        {
           $this->myfile = fopen("PartnerSignupDatabaseErrLog.txt", "a") or die("Unable to open file!");

            $txt = "Failed to insert partner: ". $this->name." - ".$this->email." - ".time().PHP_EOL;
            fwrite($this->myfile, $txt);
            
            fclose($this->myfile);
        }
	}



//	name: deactivate
//	date: 2018-05-29
//	auth: VVenning
//	desc: make a partner inactive.  This changes a single value
	public function deactivate()
	{
		//
	}


//	name: delete
//	date: 2018-05-29
//	auth: VVenning
//	desc: delete for partners table.  WOn;t be used except in emergencies.  We deactivate, which is an update process
	public function delete()
	{
		//
	}


    /**
     * Update partners table
     * Usage example :
     *  $partner =  new m_partner();
     *  $partner->name = 'name';
     *  $partner->id = 1;
     *  $partner->update();
     *
     * date: 2018-05-29, 2018-09-18
     * auth: VVenning, Ruiz
     * @return query
     */
    public function update()
    {
        $sql_statement = "UPDATE ".PARTNERS_TABLE." SET ";
        $type          = "";
        $params        = array();
        $sql_stmt_arr  = array();

		if(isset($this->name))      { $this->prepareForUpdate($sql_stmt_arr, $params, $type, 's',$this->name, 'name'); }
		if(isset($this->email))     { $this->prepareForUpdate($sql_stmt_arr, $params, $type, 's',$this->email, 'email'); }
		if(isset($this->phone))     { $this->prepareForUpdate($sql_stmt_arr, $params, $type, 's',$this->phone, 'phone'); }
		if(isset($this->ext))       { $this->prepareForUpdate($sql_stmt_arr, $params, $type, 's',$this->cell, 'ext'); }
		if(isset($this->cell))      { $this->prepareForUpdate($sql_stmt_arr, $params, $type, 's',$this->cell, 'cell'); }
		if(isset($this->address))   { $this->prepareForUpdate($sql_stmt_arr, $params, $type, 's',$this->address, 'address'); }
		if(isset($this->address2))  { $this->prepareForUpdate($sql_stmt_arr, $params, $type, 's',$this->address, 'address2'); }
        if(isset($this->city))      { $this->prepareForUpdate($sql_stmt_arr, $params, $type, 's',$this->city, 'city'); }
        if(isset($this->state))     { $this->prepareForUpdate($sql_stmt_arr, $params, $type, 's',$this->state, 'state'); }
        if(isset($this->zip))       { $this->prepareForUpdate($sql_stmt_arr, $params, $type, 's',$this->zip, 'zip'); }
        if(isset($this->link))      { $this->prepareForUpdate($sql_stmt_arr, $params, $type, 's',$this->link, 'link'); }
        if(isset($this->active))    { $this->prepareForUpdate($sql_stmt_arr, $params, $type, 'i',$this->active, 'active'); }
		if(isset($this->logo))      { $this->prepareForUpdate($sql_stmt_arr, $params, $type, 's',$this->logo, 'logo'); }
        if(isset($this->whitelabel)){ $this->prepareForUpdate($sql_stmt_arr, $params, $type, 'i',$this->whitelabel, 'whitelabel'); }
 
//      if(isset($this->country_code)) { $this->prepareForUpdate($sql_stmt_arr, $params, $type, 's',$this->country_code, 'country_code'); }

    //  add modified date
        array_push($sql_stmt_arr, " modified = ? ");
        array_push($params,date("Y-m-d H:i:s"));
        $type  .= 's';

        if(count($sql_stmt_arr) >= 1){
            $sql_statement   .=  implode(',',$sql_stmt_arr) ;
        }

    //  add the where statement
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
        if(trim($value) != null && strlen(trim($value)) > 0 ){
            array_push($sql_stmt_arr, " $column = ?");
            array_push($params,$value);
            $type  .= $update_type;
        }
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

            if($stmt)
			{
                if($close)
				{
                    $result = $this->conn->affected_rows;
                    if(strpos($sql_statement, 'INSERT') !== false)
					{
                        $this->last_inserted_id = $this->conn->insert_id;
                    }

                } else {
                    $res    = $stmt->get_result();

                    if($res->num_rows > 1){
                        $result = [];
                        while ($obj = $res->fetch_object('m_partners')) {
                            array_push($result, $obj);
                        }
                    }else{
                        $result = $res->fetch_object('m_partners');
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
            universal_log_it($file, "m_partners - ".$e->getMessage());
            return null;
        }

    }


}

?>