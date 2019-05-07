<?php
/*
name: api_keys
date: 2018-08-29
auth: Claricel Ruiz
desc: basic CRUD for api_keys table
*/

require_once($_SERVER["DOCUMENT_ROOT"] . '/api_cxn/DatabaseConnection.php');

class APIKey
{
    /**
    * $id is the primary key
    * @var integer
    */
    public $id;

    /**
     * the secret key used in calculating signature, should be atleast 50 alpha numeric characters long and unique
     * @var string
     */
    public $private_api_key;

    /**
     * the public key used in calculating signature, should be atleast 25 alpha numeric characters long and unique
     * @var string
     */
    public $public_api_key;

    /**
     * $account value is a valid email
     * @var string
     */
    public $account;

    /**
     * $active is a flag to activate and deactivate an API key
     * @var boolean
     */
    public $active;

    /**
     * $admin is a flag that check is the API Key/user is allowed to generate private and public keys
     * @var boolean
     */
    public $admin;

    /**
     * this column checks if API Key/user is allowed to use a service
     * $allowed_services list of acceptable values are in $services
     *
     * NOTES:
     *      1. all will choose all services
     *      2. to select multiple services use a comma delimiter ex. contact_users, gone_phising
     * @var string
     */
    public $allowed_services;

    /**
     * $services are the list of allowable values for allowed_services column
     * add the new service to the list if its not included yet
     * @var array
     */
    public $services = [
                            'all',
                            'ad_sync',
                            'partner_signup',
                            'chargify',
                            'contact_users',
                            'gone_phising',
                            'breach_summary',
                            'password_vulnerability',
                            'api_key_service',
                            'email_senders'
                          ];

    /**
     *  the datetime on which the api_key table row is created
     * @var datetime
     */
    public $created;

    /**
     *  the datetime on which the api_key table row is updated or modified
     * @var datetime
     */
    public $modified;


    /**
     * $timestamp are in seconds, this parameter will be checked everytime a service get accessed and will expire after one hour
     * @var timestamp
     */
    public $timestamp;

    /**
     * mysql connection
     * @var connection
     */
    private $conn;

    /**
     * table name for this model
     * @var string
     */
    private $table = 'api_keys';


    /**
     * Constructor for APIKey model
     *
     * @param
     * @return void
     */
    public function __construct(){
        $db_conn       = new DatabaseConnection();
        $db_conn->setConnectionSettings('Api Key Service');
        $this->conn = $db_conn->createConnection();
    }

    /**
     * Retrieve a row by account
     *
     * @param $account
     * @return array
     */
    public function selectByAccount($account){

        $sql_statement = "SELECT * FROM ".$this->table." WHERE account = ? ";
        return $this->executeQuery( $sql_statement,'s', array($account), false );

    }

    /**
     * Retrieve a row by public_api_key
     *
     * @param $public_api_key
     * @return array
     */
    public function selectByPublicAPIKey($public_api_key){

        $sql_statement = "SELECT * FROM ".$this->table." WHERE public_api_key = ? ";
        return $this->executeQuery( $sql_statement,'s', array($public_api_key), false );

    }


    /**
     * will generate a new row in api_keys table
     *
     * @param
     * @return void
     */
    public function create(){

        $sql_statement = "INSERT INTO ".$this->table." (private_api_key, public_api_key, account, allowed_services,created,modified) VALUES (?,?,?,?,?,?)";
        $type = 'ssssss';
        $params = array(
                        $this->private_api_key,
                        $this->public_api_key,
                        $this->account,
                        $this->allowed_services,
                        date("Y-m-d H:i:s") ,
                        date("Y-m-d H:i:s"),
                    );

        return $this->executeQuery( $sql_statement,$type, $params, true );

    }

    /**
     * will update a row in api_keys table
     *
     * @param
     * @return void
     */
    public function update(){

        $sql_statement = "UPDATE ".$this->table." SET ";
        $type          = "";
        $params        = array();
        $sql_stmt_arr  = array();

        if(!empty($this->private_api_key)){
            array_push($sql_stmt_arr, " private_api_key = ?");
            array_push($params,$this->private_api_key);
            $type  .= 's';
        }

        if(!empty($this->public_api_key)){
            array_push($sql_stmt_arr," public_api_key = ?");
            array_push($params,$this->public_api_key);
            $type  .= 's';
        }

        if(!empty($this->account)){
            array_push($sql_stmt_arr," account = ?");
            array_push($params,$this->account);
            $type  .= 's';
        }

        if(!empty($this->allowed_services)){
            array_push($sql_stmt_arr, " allowed_services = ?");
            array_push($params,$this->allowed_services);
            $type  .= 's';
        }

        if($this->active !== null){
            array_push($sql_stmt_arr, " active = ?");
            array_push($params,$this->active);
            $type  .= 'i';
        }

        if($this->admin !== null){
            array_push($sql_stmt_arr," admin = ?");
            array_push($params,$this->admin);
            $type  .= 'i';
        }


        // add modified date
        array_push($sql_stmt_arr, " modified = ? ");
        array_push($params,date("Y-m-d H:i:s"));
        $type  .= 's';


        if(count($sql_stmt_arr) >= 1){
            $sql_statement   .=  implode(',',$sql_stmt_arr) ;
        }



        // add the where statement
        $sql_statement   .= " WHERE id = ?";
        $type            .= 'i';
        array_push($params,$this->id);

        return $this->executeQuery( $sql_statement, $type, $params, true );

    }

    /**
     * will delete a row in api_keys table
     *
     * @param
     * @return void
     */
    public function delete(){
        $sql_statement = "DELETE FROM ".$this->table." WHERE id = ? ";
        return $this->executeQuery( $sql_statement, 'i', array($this->id), true );
    }

    /**
     * This function will run a query
     *
     * @param  $sql_statement   (sql query)
     * @param  $type            (sssii)
     * @params $params          (should be array)
     * @params $close           (select = false, updaten insert,delete = close)
     *
     * @return arrays
     */
    public function executeQuery($sql_statement, $type, $params, $close){

        $stmt = $this->conn->prepare($sql_statement);

        $stmt->bind_param($type, ...$params);

        $stmt->execute();

        if($close){

            $result = $this->conn->affected_rows;

        } else {

            $res    = $stmt->get_result();
            $result = $res->fetch_assoc();
         }

        $stmt->close();
        return  $result;
    }

    /**
     * This function will generate random characters for public_api_keys and private_api_keys
     * @param $type (public or private)
     * @return string
     */
    public function generateKeys( $type = 'public'){

        if (function_exists('randomString') === false) {
            die('Please include api_tools/random_tools.php in your page ');
        }

        $len    = 0;
        $sql_statement = "";
        if($type == 'public'){
            $len = 25;
            $sql_statement = "SELECT * FROM ".$this->table." WHERE public_api_key = ? ";
        }

        if($type == 'private'){
            $len = 50;
            $sql_statement = "SELECT * FROM ".$this->table." WHERE private_api_key = ? ";;
        }

        $api_key = randomString($len);
        $params_type = 's';
        $result =  $this->executeQuery( $sql_statement, $params_type, array($api_key), false );

        if(!empty($result)){
            $this->gererateKeys($type);
        }

        return $api_key;
    }


    /**
     * This function will check the API and the generated signature
     * @param $signature (passed by clients)
     * @param $api_service  (for a list of allowable services, check $this->services)
     * @return mixed
     */
    public function validateSignature($client_signature, $api_service){

        $api_keys = $this->selectByPublicAPIKey($this->public_api_key);

        if(empty($this->public_api_key) && empty($this->timestamp)){
            return array(400, 'Error: public_api_key or timestamp is empty.' );
        }

        if(empty($api_keys)){
            return array(400, "Error: public_api_key don't have any records. " );
        }

        if(empty($api_service) ||  !in_array($api_service, $this->services)){
            return array(400, 'Error: please provide what api service you are accessing,
             check the list from $this->services variable of this model. Add the service is its not yet included to the list.' );
        }

        // check if the API Key is active
        if($api_keys['active'] !== 1){
            return array(400, 'Error: This key is not active anymore.' );
        }

        // check if the timestamp is older than 1 hour, if it is older return an error
        $now = time();
        $old_time = $this->timestamp ;

        if( (($now - $old_time) < 3600) === false ){
            return array(400, 'Error: timestamp is greater than 1 hr old.' );
        }

        // check if this api key is allowed to use this service
        if($this->checkIfServiceIsAllowable($api_keys, $api_service) === false){
            return array(400, 'Error: this api_key is not allowed to access '.$api_service.'.' );
        }
        $this->private_api_key = $api_keys['private_api_key'];
        $server_signature = $this->createSignature();

        if($client_signature !== $server_signature){
            return array(400, 'Error: incorrect signature.' );
        }

        return array(200, 'Correct signature.' );

    }

    /**
     * This function will check if the api service is in the list of allowable API service
     * @param $server_api_keys (row of api_keys table)
     * @param $api_service (api service to check)
     *
     * @return boolean
     */
    public function checkIfServiceIsAllowable($server_api_keys, $client_api_service)
    {
        // only administrators can access the API Key Service to create,update,delete or view API Key information
        if($client_api_service === 'api_key_service'){
            if($server_api_keys['admin'] !== 1 ){
                return false;
            }
        }

        if($server_api_keys['allowed_services'] == "all" ){
            return true;
        }

        if($server_api_keys['allowed_services'] !== 'all'){
            $services = explode(',',$server_api_keys['allowed_services']);
            foreach ($services as $service){
                if($client_api_service == $service){
                    return true;
                }
            }
        }

        return false;
    }

    /**
     * This function will generate random characters for public_api_keys and private_api_keys
     *
     * @return array of public_api_keys and private_api_keys
     */
    public function createSignature(){

        // the salt for calculating hashmac is timestamp +  public_api_key
        $salt = $this->timestamp . $this->public_api_key;

        if(empty($this->private_api_key)){
            return false;
        }

        if (function_exists('hash_hmac')) {
            return hash_hmac('sha1', $salt, $this->private_api_key);
        }

        return $this->custom_hmac('sha1', $salt, $this->private_api_key, false);

    }

    /**
     * Sometimes a hosting provider doesn't provide access to the Hash extension. Here is a clone of the hash_hmac
     * function you can use in the event you need an HMAC generator and Hash is not available. It's only usable with
     * MD5 and SHA1 encryption algorithms, but its output is identical to the official hash_hmac function
     * from: http://php.net/manual/en/function.hash-hmac.php#93440
     *
     * @return array of public_api_keys and private_api_keys
     */
    public function custom_hmac($algo, $data, $key, $raw_output = false)
    {
        $algo = strtolower($algo);
        $pack = 'H'.strlen($algo('test'));
        $size = 64;
        $opad = str_repeat(chr(0x5C), $size);
        $ipad = str_repeat(chr(0x36), $size);

        if (strlen($key) > $size) {
            $key = str_pad(pack($pack, $algo($key)), $size, chr(0x00));
        } else {
            $key = str_pad($key, $size, chr(0x00));
        }

        for ($i = 0; $i < strlen($key) - 1; $i++) {
            $opad[$i] = $opad[$i] ^ $key[$i];
            $ipad[$i] = $ipad[$i] ^ $key[$i];
        }

        $output = $algo($opad.pack($pack, $algo($ipad.$data)));

        return ($raw_output) ? pack($pack, $output) : $output;
    }


}