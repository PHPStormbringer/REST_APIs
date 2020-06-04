<?php
/* 
    name: m_clients
    date: 2018-05-30
    auth: VVenning
    desc: basic CRUD for clients table
//  2018-09-18 1003 VVenning - USe constant for clients table name
//  2018-12-01 1500 VVenning - FInally wrote body of function

*/
class m_clients
{
    public $id;
    public $partner_id; //
    public $name; //
    public $email; //
    public $phone;
    public $ext;
    public $cell;
    
    public $address;
    public $address2;
    public $city;
    public $state;
    public $zip;
    public $link;
    
    public $active;
    public $logo;
    public $white_label;
    public $account_type;
    public $dont_send_email;
    public $employee_count;

    public $created;
    public $modified;

    public $last_inserted_id;
    public $database_error;
    public $conn;
    

/*
 *
 */
    public function __construct()
    {


    }

/*
 *
 */
    public function __destruct()
    {


    }    

/*    
 *  name: prepareForSelect
 *  desc: select for clients table.  prepare part of the query column by reference.  By passing by reference, I'm allowing myself multiple returns without having them in the same variable

 *  @param $first_val   - Should I put in the WHERE, or use AND
 *  @param $select_type - 's' or 'i'
 *  @param $column      - column_name
 *  @param $value       - column value
 *  @param $arr_sql_where -  where clause
 *  @param $params        - values to be searched by
 *  @param $type          - 'sssiiiii'

 *  @return void
 */
    private function prepareForSelect(&$arr_sql_where, &$params, &$type, $select_type, $value, $column, $first_val)
    {
    //    This needs to support search where column value in db equals null
        if(trim($value) != null && strlen(trim($value)) > 0 )
        {
            if($first_val === 1)
            {
                array_push($arr_sql_where, " WHERE $column = ?");
            }
            else
            {
                array_push($arr_sql_where, " AND $column = ?");
            }
                
            array_push($params,$value);
            $type  .= $select_type;
        }
    }
    
    
    
/*
 *  name: select
 *  desc: select for clients table

 *  @param $arr_search_by - associative array of column names and values
 *  @param $count         - 1 or null.  1 means this is a select count

 *  @return int|array
 *  2019-06-05 1646 VVenning - Added $count param for select count
 */
    public function select($arr_search_by=null, $count=null)
    {
        
        $sql_statement = ($count===1) ? "SELECT COUNT(*) FROM ".CLIENTS_TABLE : "SELECT * FROM ".CLIENTS_TABLE;
        $type          = "";
        $params        = array();
        $sql_stmt_arr  = array();
        $count         = 0;
        
        $arr_sql_where = array();
        
        if(isset($arr_search_by) && count($arr_search_by) > 0)
        {
            foreach($arr_search_by as $key => $a_search_by)
            {
                $first_val = ($count == 0)?1:0;
                
             *  if($count == 0){$first_val = 1;    }else{$first_val = 0; }

                switch($key)
                {
                    case 'id':                $select_type = 'i'; break;
                    case 'partner_id':        $select_type = 'i'; break;    

                    case 'name':              $select_type = 's'; break;
                    case 'email':             $select_type = 's'; break;
                    case 'phone':             $select_type = 's'; break;
                    case 'ext':               $select_type = 's'; break;
                    case 'cell':              $select_type = 's'; break;

                    case 'address':           $select_type = 's'; break;
                    case 'address2':          $select_type = 's'; break;
                    case 'city':              $select_type = 's'; break;
                    case 'state':             $select_type = 's'; break;
                    case 'zip':               $select_type = 's'; break;
                    case 'link':              $select_type = 's'; break;

                    case 'active':            $select_type = 'i'; break;
                    case 'logo':              $select_type = 's'; break;
                    case 'whitelabel':        $select_type = 'i'; break;
                    case 'account_type':      $select_type = 's'; break;
                    case 'dont_send_email':   $select_type = 'i'; break;
                    case 'employee_count':    $select_type = 'i'; break;

                    case 'created':           $select_type = 's'; break;
                    case 'modified':          $select_type = 's'; break;
                }
                
                $this->prepareForSelect( $arr_sql_where, $params, $type, $select_type, $a_search_by, $key, $first_val );
                
                $count++;
            }            
        }
        $sql_statement .= implode(" ", $arr_sql_where);
        $str_params = implode(",", $params);

        return $this->executeQuery( $sql_statement, $type, $params, false );


    }

/*
 *  name: prepareForUpdate
 *  desc: prepare part of the query column by reference

 *  @param $update_type - 's' or 'i'
 *  @param $column      - column_name
 *  @param $value       - column value
 *  @param $sql_stmt_arr  - array of columns to be updated, formatted for PDO
 *  @param $params        - values to be inserted
 *  @param $type          - concatented string of 's' and 'i', 'sssiiiii'

 *  @return void

 *  2019-06-05 1646 VVenning - Added $count param for selectcount
 */    private function prepareForUpdate(&$sql_stmt_arr, &$params, &$type, $update_type, $value, $column)
    {
        if(trim($value) != null && strlen(trim($value)) > 0 )
        {
            array_push($sql_stmt_arr, " $column = ?");
            array_push($params,$value);
            $type  .= $update_type;
        }
    }
/*
 *  name: update
 *  desc: clients table selected fields

 *  Usage example :
 *  $client =  new m_clients();
 *  $client->name = 'name';
 *  $client->id = 1;
 *  $client->update();

 *  @return query
 *  2019-05-22 1315 VVenning - Only update values actually set
 */
    public function update()
    {
        $sql_statement = "UPDATE ".CLIENTS_TABLE." SET ";
        $type          = "";
        $params        = array();
        $sql_stmt_arr  = array();

        if(isset($this->partner_id))       { $this->prepareForUpdate($sql_stmt_arr, $params, $type, 'i',$this->partner_id, 'partner_id'); }
        if(isset($this->name))             { $this->prepareForUpdate($sql_stmt_arr, $params, $type, 's',$this->first_name, 'name'); }
        if(isset($this->email))            { $this->prepareForUpdate($sql_stmt_arr, $params, $type, 's',$this->email, 'email'); }
        if(isset($this->phone))            { $this->prepareForUpdate($sql_stmt_arr, $params, $type, 's',$this->phone, 'phone'); }
        if(isset($this->ext))            { $this->prepareForUpdate($sql_stmt_arr, $params, $type, 's',$this->ext, 'ext'); }
        if(isset($this->cell))             { $this->prepareForUpdate($sql_stmt_arr, $params, $type, 's',$this->cell, 'cell'); }
        
        if(isset($this->address))          { $this->prepareForUpdate($sql_stmt_arr, $params, $type, 's',$this->address, 'address'); }
        if(isset($this->address2))         { $this->prepareForUpdate($sql_stmt_arr, $params, $type, 's',$this->address, 'address2'); }
        if(isset($this->city))             { $this->prepareForUpdate($sql_stmt_arr, $params, $type, 's',$this->city, 'city'); }
        if(isset($this->state))            { $this->prepareForUpdate($sql_stmt_arr, $params, $type, 's',$this->state, 'state'); }
        if(isset($this->zip))              { $this->prepareForUpdate($sql_stmt_arr, $params, $type, 's',$this->zip, 'zip'); }
        if(isset($this->link))             { $this->prepareForUpdate($sql_stmt_arr, $params, $type, 's',$this->link, 'link'); }

        if(isset($this->active))           { $this->prepareForUpdate($sql_stmt_arr, $params, $type, 'i',$this->active, 'active'); }
        if(isset($this->logo))             { $this->prepareForUpdate($sql_stmt_arr, $params, $type, 'i',$this->logo, 'logo');  }
        if(isset($this->white_label))      { $this->prepareForUpdate($sql_stmt_arr, $params, $type, 'i',$this->white_label, 'white_label');  }
        if(isset($this->account_type))     { $this->prepareForUpdate($sql_stmt_arr, $params, $type, 'i',$this->account_type, 'account_type');  }
        if(isset($this->dont_send_email))  { $this->prepareForUpdate($sql_stmt_arr, $params, $type, 'i',$this->dont_send_email, 'dont_send_email');  }

        if(isset($this->employee_count))   { $this->prepareForUpdate($sql_stmt_arr, $params, $type, 'i',$this->employee_count, 'employee_count'); }

        // add modified date
        array_push($sql_stmt_arr, " modified = ? ");
        array_push($params,date("Y-m-d H:i:s"));
        $type  .= 's';

        if(count($sql_stmt_arr) >= 1)
        {
            $sql_statement   .=  implode(',',$sql_stmt_arr) ;
        }

        // add the where statement
        $sql_statement   .= " WHERE id = ?";
        $type            .= 'i';
        array_push($params,$this->id);

//echo $sql_statement;die;

        return $this->executeQuery( $sql_statement, $type, $params, true );

    }

/*
 *  name: prepareForCreate
 *  desc: prepare part of the query column by reference

 *  @param $insert_type - 's' or 'i'
 *  @param $column      - column_name
 *  @param $value       - column value
 *  @param $placeholder   - 
 *  @param $$arr_columns  - array of columns to be updated
 *  @param $arr_param     - values to be inserted
 *  @param $type          - concatented string of 's' and 'i', 'sssiiiii'

 *  @return void
 */
    private function prepareForCreate(&$placeholder, &$arr_columns, &$type, &$arr_param, $insert_type, $value, $column )
    {
        if(trim($value) != null && strlen(trim($value)) > 0 ) 
        {
            array_push($placeholder, '?');
            array_push($arr_columns, $column);
            array_push($type, $insert_type);
            array_push($arr_param, $value);
        }
    }

/*
 *  name: create
 *  desc: Create new client

 *  Usage example :
 *  $client =  new m_clients();
 *  $client->name = 'name';
 *  $client->id = 1;
 *  $client->create();

 *  @return query
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

        if(isset($this->partner_id))       { $this->prepareForCreate($placeholder, $arr_columns, $arr_type, $arr_param, 'i',$this->partner_id, 'partner_id'); }
        if(isset($this->name))             { $this->prepareForCreate($placeholder, $arr_columns, $arr_type, $arr_param, 's',$this->name, 'name'); }
        if(isset($this->email))            { $this->prepareForCreate($placeholder, $arr_columns, $arr_type, $arr_param, 's',$this->email, 'email'); }
        if(isset($this->phone))            { $this->prepareForCreate($placeholder, $arr_columns, $arr_type, $arr_param, 's',$this->phone, 'phone'); }
        if(isset($this->ext))              { $this->prepareForCreate($placeholder, $arr_columns, $arr_type, $arr_param, 's',$this->ext, 'ext'); }
        if(isset($this->cell))             { $this->prepareForCreate($placeholder, $arr_columns, $arr_type, $arr_param, 's',$this->cell, 'cell'); }
        
        if(isset($this->address))          { $this->prepareForCreate($placeholder, $arr_columns, $arr_type, $arr_param, 's',$this->address, 'address'); }
        if(isset($this->address_2))        { $this->prepareForCreate($placeholder, $arr_columns, $arr_type, $arr_param, 's',$this->address2, 'address2'); }
        if(isset($this->city))             { $this->prepareForCreate($placeholder, $arr_columns, $arr_type, $arr_param, 's',$this->city, 'city'); }
        if(isset($this->state))            { $this->prepareForCreate($placeholder, $arr_columns, $arr_type, $arr_param, 's',$this->state, 'state'); }
        if(isset($this->zip))              { $this->prepareForCreate($placeholder, $arr_columns, $arr_type, $arr_param, 's',$this->zip, 'zip'); }
        if(isset($this->link))             { $this->prepareForCreate($placeholder, $arr_columns, $arr_type, $arr_param, 's',$this->link, 'link'); }

        if(isset($this->active))           { $this->prepareForCreate($placeholder, $arr_columns, $arr_type, $arr_param, 'i',$this->active, 'active'); }
        if(isset($this->logo))             { $this->prepareForCreate($placeholder, $arr_columns, $arr_type, $arr_param, 's',$this->logo, 'logo'); }
        if(isset($this->white_label))      { $this->prepareForCreate($placeholder, $arr_columns, $arr_type, $arr_param, 's',$this->white_label, 'white_label'); }
        if(isset($this->account_type))     { $this->prepareForCreate($placeholder, $arr_columns, $arr_type, $arr_param, 's',$this->account_type, 'account_type'); }
        if(isset($this->dont_send_email))  { $this->prepareForCreate($placeholder, $arr_columns, $arr_type, $arr_param, 'i',$this->dont_send_email, 'dont_send_email'); }
        if(isset($this->employee_count))   { $this->prepareForCreate($placeholder, $arr_columns, $arr_type, $arr_param, 's',$this->employee_count, 'employee_count'); }

        if(isset($this->created))          { $this->prepareForCreate($placeholder, $arr_columns, $arr_type, $arr_param, 's',date( "Y-m-d H:i:s"),'created' ); }
        if(isset($this->modified))         { $this->prepareForCreate($placeholder, $arr_columns, $arr_type, $arr_param, 's',date( "Y-m-d H:i:s"),'modified' ); }

        $sql_statement = "INSERT INTO ".CLIENTS_TABLE." ( ".implode(',',$arr_columns).") VALUES ( ".implode(',',$placeholder).")";

        $this->executeQuery( $sql_statement,implode('',$arr_type), $arr_param, true );
        
        $arrId = array('id' => $this->last_inserted_id);
        
        return $this->select($arrId);
        
    }

/*
 *  name: deactivate
 *  desc: make a client inactive.  This changes a single value
 */
    public function deactivate()
    {
        //
    }

/*
 *  name: delete
 *  desc: delete for clients table.  WOn't be used except in emergencies.  We deactivate, which is an update process
 */
    public function delete()
    {
        //
    }

/*
 * name: executeQuery
 * desc: This function will run a query
 *
 * @param  $sql_statement   (sql query)
 * @param  $type            (sssii)
 * @params $params          (should be array)
 * @params $close           (select = false, updaten insert,delete = true)

 *  @return array|error message to file

 */
    private function executeQuery($sql_statement, $type, $params, $close)
    {
        mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);
        
        try
        {
            $stmt = $this->conn->prepare($sql_statement);

            if(!empty($type) && !empty($params))
            {
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

                }
                else 
                {
                    $res = $stmt->get_result();

                    if($res->num_rows > 1)
					{
                        $result = array();

                        while ($obj = $res->fetch_assoc()) 
                        {
                            array_push($result, $obj);
                        }
                    }
                    else
                    {
                    //  $result = $res->fetch_object('m_clients');
                        $result = $res->fetch_assoc();
                    }
                }
                $stmt->close();
                return  $result;
            }

        } 
        catch (mysqli_sql_exception $e) 
        {
            if(isset($this->conn->log_file_path) && !empty($this->conn->log_file_path))
            {
                $file = $_SERVER['DOCUMENT_ROOT'].'/'.$this->conn->log_file_path;
            }
            else
            {
                $file = $_SERVER['DOCUMENT_ROOT'].REST_API_DATABASE_ERROR_LOG_PATH;
            }

            universal_log_it($file, "m_clients - ".$e->getMessage());

            return null;
        }

    }

}
