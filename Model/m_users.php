<?php
/* 
    name: users
    auth: VVenning
    desc: basic CRUD for users table

*/
class m_users
{
    public $id;
    public $password;
    public $password_old;
    public $first_name;
    public $last_name;
    public $email;
    public $screen_name;
    public $job_function;
    public $phone_number;
    public $phone_number_ext;
    public $cell_number;
    
    public $address;
    public $address2;
    public $city;
    public $state;
    public $zip;
    public $link;

    public $active;
    public $dont_send_email;
    public $last_login;

    public $role_id;
    public $client_id;
    public $partner_id;
    public $tag_id;

    public $tokenhash;

    public $created;
    public $modified;

    public $last_inserted_id;
    public $database_error;
    public $conn;

    public function __construct()
    {


    }

    public function __destruct()
    {


    }



/*  
 *  name: prepareForSelect
 *  auth: VVenning
 *  desc: select for users table.  prepare part of the query column by reference.  By passing by reference, I'm allowing myself multiple returns without having them in the same variable

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
 *  desc: select for users table

 *  @param $arr_search_by - associative array of column names and values
 *  @param $count         - 1 or null.  1 means this is a select count
 *  @return int|array

 *  2019-06-05 1646 VVenning - Added $count param for select count
 */
    public function select($arr_search_by=null, $count=null)
    {
        
        $sql_statement = ($count===1) ? "SELECT COUNT(*) FROM ".USERS_TABLE : "SELECT * FROM ".USERS_TABLE;
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
                
            //    if($count == 0){$first_val = 1;    }else{$first_val = 0; }
                
                switch($key)
                {
                    case 'id':                $select_type = 'i'; break;
                    case 'password':          $select_type = 's'; break;
                    case 'password_old':      $select_type = 's'; break;
                    case 'first_name':        $select_type = 's'; break;
                    case 'last_name':         $select_type = 's'; break;
                    case 'email':             $select_type = 's'; break;
                    case 'screen_name':       $select_type = 's'; break;
                    case 'job_function':      $select_type = 's'; break; 
                    case 'phone_number':      $select_type = 's'; break;
                    case 'phone_number_ext':  $select_type = 's'; break;
                    case 'cell_number':       $select_type = 's'; break;
    
                    case 'address':           $select_type = 's'; break;
                    case 'address2':          $select_type = 's'; break;
                    case 'city':              $select_type = 's'; break;
                    case 'state':             $select_type = 's'; break;
                    case 'zip':               $select_type = 's'; break;
                    case 'link':              $select_type = 's'; break;

                    case 'active':            $select_type = 'i'; break;
                    case 'dont_send_email':   $select_type = 'i'; break;
                    case 'last_login':        $select_type = 's'; break;

                    case 'role_id':           $select_type = 'i'; break;
                    case 'client_id':         $select_type = 'i'; break;
                    case 'partner_id':        $select_type = 'i'; break;
                    case 'tag_id':            $select_type = 'i'; break;

                    case 'tokenhash':         $select_type = 's'; break;

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
 */
    private function prepareForUpdate(&$sql_stmt_arr, &$params, &$type, $update_type, $value, $column)
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
 *  desc: users table selected fields

 *  Usage example :
 *  $user =  new m_users();
 *  $user->name = 'name';
 *  $user->id = 1;
 *  $user->update();

 *  @return query
 *  2019-05-22 1315 VVenning - Only update values actually set
 */
    public function update()
    {
        $sql_statement = "UPDATE ".USERS_TABLE." SET ";
        $type          = "";
        $params        = array();
        $sql_stmt_arr  = array();

        if(isset($this->password))         { $this->prepareForUpdate($sql_stmt_arr, $params, $type, 's',$this->password, 'password');  }
        if(isset($this->password_old))     { $this->prepareForUpdate($sql_stmt_arr, $params, $type, 's',$this->password_old, 'password_old');  }
        
        if(isset($this->first_name))       { $this->prepareForUpdate($sql_stmt_arr, $params, $type, 's',$this->first_name, 'first_name'); }
        if(isset($this->lasst_name))       { $this->prepareForUpdate($sql_stmt_arr, $params, $type, 's',$this->last_name, 'last_name'); }
        if(isset($this->email))            { $this->prepareForUpdate($sql_stmt_arr, $params, $type, 's',$this->email, 'email'); }

        if(isset($this->screen_name))      { $this->prepareForUpdate($sql_stmt_arr, $params, $type, 's',$this->screen_name, 'screen_name');  }
    //  if(isset($this->job_function))     { $this->prepareForUpdate($sql_stmt_arr, $params, $type, 's',$this->job_function, 'job_function');  }
                
        if(isset($this->phone_number))     { $this->prepareForUpdate($sql_stmt_arr, $params, $type, 's',$this->phone_number, 'phone_number'); }
        if(isset($this->phone_number_ext)) { $this->prepareForUpdate($sql_stmt_arr, $params, $type, 's',$this->phone_number_ext, 'phone_number_ext'); }
        if(isset($this->cell_number))      { $this->prepareForUpdate($sql_stmt_arr, $params, $type, 's',$this->cell_number, 'cell'); }
        
        if(isset($this->address))          { $this->prepareForUpdate($sql_stmt_arr, $params, $type, 's',$this->address, 'address'); }
        if(isset($this->address2))         { $this->prepareForUpdate($sql_stmt_arr, $params, $type, 's',$this->address, 'address2'); }
        if(isset($this->city))             { $this->prepareForUpdate($sql_stmt_arr, $params, $type, 's',$this->city, 'city'); }
        if(isset($this->state))            { $this->prepareForUpdate($sql_stmt_arr, $params, $type, 's',$this->state, 'state'); }
        if(isset($this->zip))              { $this->prepareForUpdate($sql_stmt_arr, $params, $type, 's',$this->zip, 'zip'); }
        if(isset($this->link))             { $this->prepareForUpdate($sql_stmt_arr, $params, $type, 's',$this->link, 'link'); }

        if(isset($this->active))           { $this->prepareForUpdate($sql_stmt_arr, $params, $type, 'i',$this->active, 'active'); }

        if(isset($this->dont_send_email))  { $this->prepareForUpdate($sql_stmt_arr, $params, $type, 'i',$this->dont_send_email, 'dont_send_email');  }
        if(isset($this->role_id))          { $this->prepareForUpdate($sql_stmt_arr, $params, $type, 'i',$this->role_id, 'role_id');  }
        if(isset($this->client_id))        { $this->prepareForUpdate($sql_stmt_arr, $params, $type, 'i',$this->client_id, 'client_id'); }
        if(isset($this->partner_id))       { $this->prepareForUpdate($sql_stmt_arr, $params, $type, 'i',$this->partner_id, 'partner_id'); }

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
 *  desc: Create new user
 
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

        if(isset($this->password))         { $this->prepareForCreate($placeholder, $arr_columns, $arr_type, $arr_param, 's',$this->password, 'password'); }
        if(isset($this->password_old))     { $this->prepareForCreate($placeholder, $arr_columns, $arr_type, $arr_param, 's',$this->password_old, 'password_old'); }
        if(isset($this->first_name))       { $this->prepareForCreate($placeholder, $arr_columns, $arr_type, $arr_param, 's',$this->first_name, 'first_name'); }
        if(isset($this->last_name))        { $this->prepareForCreate($placeholder, $arr_columns, $arr_type, $arr_param, 's',$this->last_name, 'last_name'); }
        if(isset($this->email))            { $this->prepareForCreate($placeholder, $arr_columns, $arr_type, $arr_param, 's',$this->email, 'email'); }
        if(isset($this->screen_name))      { $this->prepareForCreate($placeholder, $arr_columns, $arr_type, $arr_param, 's',$this->screen_name, 'screen_name'); }
        if(isset($this->job_function))     { $this->prepareForCreate($placeholder, $arr_columns, $arr_type, $arr_param, 's',$this->job_function, 'job_function'); }
        if(isset($this->phone_number))     { $this->prepareForCreate($placeholder, $arr_columns, $arr_type, $arr_param, 's',$this->phone_number, 'phone_number'); }
        if(isset($this->phone_number_ext)) { $this->prepareForCreate($placeholder, $arr_columns, $arr_type, $arr_param, 's',$this->phone_number_ext, 'phone_number_ext'); }
        if(isset($this->cell_number))      { $this->prepareForCreate($placeholder, $arr_columns, $arr_type, $arr_param, 's',$this->cell_number, 'cell_number'); }
        if(isset($this->address))          { $this->prepareForCreate($placeholder, $arr_columns, $arr_type, $arr_param, 's',$this->address, 'address'); }
        if(isset($this->address_2))        { $this->prepareForCreate($placeholder, $arr_columns, $arr_type, $arr_param, 's',$this->address2, 'address2'); }
        if(isset($this->city))             { $this->prepareForCreate($placeholder, $arr_columns, $arr_type, $arr_param, 's',$this->city, 'city'); }
        if(isset($this->state))            { $this->prepareForCreate($placeholder, $arr_columns, $arr_type, $arr_param, 's',$this->state, 'state'); }
        if(isset($this->zip))              { $this->prepareForCreate($placeholder, $arr_columns, $arr_type, $arr_param, 's',$this->zip, 'zip'); }
        if(isset($this->link))             { $this->prepareForCreate($placeholder, $arr_columns, $arr_type, $arr_param, 's',$this->link, 'link'); }
        if(isset($this->active))           { $this->prepareForCreate($placeholder, $arr_columns, $arr_type, $arr_param, 'i',$this->active, 'active'); }
        if(isset($this->dont_send_email))  { $this->prepareForCreate($placeholder, $arr_columns, $arr_type, $arr_param, 'i',$this->dont_send_email, 'dont_send_email'); }
        if(isset($this->last_login))       { $this->prepareForCreate($placeholder, $arr_columns, $arr_type, $arr_param, 's',$this->last_login, 'last_login'); }
        if(isset($this->role_id))          { $this->prepareForCreate($placeholder, $arr_columns, $arr_type, $arr_param, 'i',$this->role_id, 'role_id'); }
        if(isset($this->client_id))        { $this->prepareForCreate($placeholder, $arr_columns, $arr_type, $arr_param, 'i',$this->client_id, 'client_id'); }
        if(isset($this->partner_id))       { $this->prepareForCreate($placeholder, $arr_columns, $arr_type, $arr_param, 'i',$this->partner_id, 'partner_id'); }
        if(isset($this->tag_id))           { $this->prepareForCreate($placeholder, $arr_columns, $arr_type, $arr_param, 'i',$this->tag_id, 'tag_id'); }

        if(isset($this->created))          { $this->prepareForCreate($placeholder, $arr_columns, $arr_type, $arr_param, 's',date( "Y-m-d H:i:s"),'created' ); }
        if(isset($this->modified))         { $this->prepareForCreate($placeholder, $arr_columns, $arr_type, $arr_param, 's',date( "Y-m-d H:i:s"),'modified' ); }

        $sql_statement = "INSERT INTO ".USERS_TABLE." ( ".implode(',',$arr_columns).") VALUES ( ".implode(',',$placeholder).")";

        $this->executeQuery( $sql_statement,implode('',$arr_type), $arr_param, true );
        
        $arrId = array('id' => $this->last_inserted_id);
        
        return $this->select($arrId);
        
    }
/*
 *  name: deactivate
 *  desc: make a user inactive.  This changes a single value
 */
    public function deactivate()
    {
        $this->active = 0;
        $this->update();
    }

/*
 *  name: delete
 *  desc: delete for users table.  We deactivate, which is an update process
*/
    public function delete()
    {
        //
    }    
/*
 *  name: executeQuery
 *  desc: This function will run a query

 *  @param  $sql_statement   (sql query)
 *  @param  $type            (sssii)
 *  @params $params          (should be array)
 *  @params $close           (select = false, update insert,delete = true)

 *  @return arrays
 */
    private function executeQuery( $sql_statement, $type, $params, $close)
    {
        mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);
        
        try
        {
            $stmt = $this->conn->prepare($sql_statement);
            
            if($params)
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
                    //  $result = $res->fetch_object();
                        $result = $res->fetch_assoc($res);
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

            universal_log_it($file, "m_users - ".$e->getMessage());

            return null;
        }

    }
/*
 *  name: select_clientID_by_emailist()
 *  desc: select client_id by search for the client id shared by all emails from ADSYnc

 *  Usage example :
 *  $this->list_of_emails = "'name1@example.com','name1@example.com'" ;

 *  @return array|error message to file

 */
    public function select_clientID_by_emailist()
    {
        $sqlGetClient = "SELECT client_id FROM ".USERS_TABLE." where email IN ('.$this->list_of_emails. ')";
 
    //    $stmt = $this->conn->prepare("SELECT * FROM users WHERE email = ?");
        $stmt = $this->conn->prepare("SELECT client_id FROM ".USERS_TABLE." where email IN (?) ");
    //  $stmt = $this->conn->prepare($sqlGetClient);

        if($stmt)
        {
            $data = "";

            $stmt->bind_param("s", $this->list_of_emails);
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
           $this->myfile = fopen("PartnerSignupDatabaseErrLog.txt", "a") or die("Unable to open file!");

            $txt = "Failed to create statemnt to select client id:" . time().PHP_EOL;
            fwrite($this->myfile, $txt);
            
            fclose($this->myfile);
        }
    }   



}
