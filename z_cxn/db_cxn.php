<?php

require_once('../z_config/db_config.php');	

class db_cxn
{
   
    public $conn;

/*
 *  name: __construct
 *  desc: establish connection to database
 *
 *  @param $host string
 *
 *  @assign $this->conn
 *
 */	public function __construct($host)
    {   

        if($host=="dev")
        {
            $this->conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
        }
        elseif($host=="qa")
        {
        // Create connection
            $this->conn = new mysqli(DB_HOST_QA, DB_USER_QA, DB_PASS_QA, DB_NAME_QA);
        }
        elseif($host=="prod")
        {
        // Create connection
            $this->conn = new mysqli(DB_HOST_PROD, DB_USER_PROD, DB_PASS_PROD, DB_NAME_PROD);
        }
        elseif($host=="local")
        {
        // Create connection
            $this->conn = new mysqli(DB_HOST_LOCAL, DB_USER_LOCAL, DB_PASS_LOCAL, DB_NAME_LOCAL);
        }


        $the_time = date("Y-m-d h:i:s", time())." - ";
        
        // Check connection
        if ($this->conn->connect_error) 
        {
            $who  = DEV_TEAM.",".HARDWARE;
            $what = "Database connection failed";
            $desc = "Database connection failed";

        	email4API($who, $what, $desc);

            $this->myfile = fopen("logs/db_cxn_failure.txt", "a") or die("Unable to open file!");

        	fwrite($this->myfile, $the_time."Connection failed".PHP_EOL);

            die("Connection failed: " . $this->conn->connect_error.PHP_EOL);
        } 
        
        $this->myfile = fopen("logs/Connection.txt", "a") or die("Unable to open file!");
        fwrite($this->myfile, $the_time."Connected successfully".PHP_EOL);
    }
	// ******************************************************************************************************
	// Note:  Theoretically, you could split up connecting to MySQL and connecting to the db
	// ******************************************************************************************************
    
/*
 *  name make
 *  desc return connection object
 *
 *  @return object
 *
 */	public function make()
    {
	// Create connection
       return $this->conn;
    }

/*
 *  name nuke
 *  desc return connection object
 *
 *  @param $cxn
 *
 *  @return void
 *
 */	public static function nuke($cxn)
    {
        mysqli_close($cxn);
    }	
}
