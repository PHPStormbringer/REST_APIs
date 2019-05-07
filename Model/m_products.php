<?php
/**
 * Created by PhpStorm.
 * User: claricel
 * Date: 10/12/18
 * Time: 9:29 AM
 * desc: basic CRUD for products table
 */



class m_products
{

    /**
     * $id is the primary key
     * @var integer
     */
    public $id;

    /**
     * this variable is the product code like BSNPv2, BPP-4500 and etc.
     * @var string
     */
    public $code;

    /**
     * minimum users of each product code, NULL value means the code is for Unlimited Training
     * @var integer
     */
    public $min_users;

    /**
     *  maximum users of each product code, NULL value means the code is for Unlimited Training
     * @var integer
     */
    public $max_users;

    /**
     *  the datetime on which the products table row is created
     * @var datetime
     */
    public $created;

    /**
     *  the datetime on which the products table row is updated or modified
     * @var datetime
     */
    public $modified;

    /**
     * mysql connection
     * @var connection
     */
    public $conn;

    /**
     * table name for this model
     * @var string
     */
    private $table = 'products';

    /**
     * Retrieve a products by $quantity
     *
     * @param $account
     * @return array
     */
    public function getProductByQuantity($quantity){
        $sql_statement = "SELECT DISTINCT IF(min_users <= ? AND max_users >= ?, code, NULL) AS code FROM products ORDER BY code DESC LIMIT 1";
        return $this->executeQuery( $sql_statement,'ii', array($quantity,$quantity), false );
    }

    /**
     * Retrieve a quantity by product code
     *
     * @param $account
     * @return array
     */
    public function getProductByCode($code){
        $sql_statement = "SELECT * from products WHERE code = ? ";
        return $this->executeQuery( $sql_statement,'s', array($code), false );
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
            $stmt->bind_param($type, ...$params);

            $stmt->execute();

            if($stmt){
                if($close){
                    $result = $this->conn->affected_rows;
                    if(strpos($sql_statement, 'INSERT') !== false){
                        $this->last_inserted_id = $this->conn->insert_id;
                    }

                } else {
                    $res    = $stmt->get_result();

                    if($res->num_rows > 1){
                        $result = [];
                        while ($obj = $res->fetch_object('m_products')) {
                            array_push($result, $obj);
                        }
                    }else{
                        $result = $res->fetch_object('m_products');
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

            universal_log_it($file, "m_products - ".$e->getMessage());

            return null;
        }

    }

}