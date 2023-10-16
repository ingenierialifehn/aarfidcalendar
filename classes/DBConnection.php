<?php
if(!defined('DB_SERVER')){
    require_once("../initialize.php");
}
class DBConnection{

    private $host = DB_SERVER;
    private $username = DB_USERNAME;
    private $password = DB_PASSWORD;
    private $database = DB_NAME;
    
    public $conn;
    public $conn2;
    
    public function __construct(){

        if (!isset($this->conn)) {
            
            $this->conn = new mysqli($this->host, $this->username, $this->password, $this->database);
            
            if (!$this->conn) {
                echo 'Cannot connect to database server';
                exit;
            }            
        }    
        if (!isset($this->conn2)) {
            
            $this->conn2 = new mysqli($this->host, $this->username, $this->password, $this->database);
            
            if (!$this->conn2) {
                echo 'Cannot connect to database server';
                exit;
            }            
        }    
        
    }



    public function __destruct(){
        $this->conn->close();
        $this->conn2->close();
    }

}
?>