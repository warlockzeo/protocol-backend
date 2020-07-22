<?php

class ClassConection {
 
 private $db_host = "localhost";
 private $db_name = "protocolo central";
 private $db_user = "warlock";
 private $db_password = "smtqsgjh";
 public $conn;

 public function getConnection(){

     $this->conn = null;

     try{
        $this->conn = new PDO("mysql:host=" . $this->db_host . ";dbname=" . $this->db_name, $this->db_user, $this->db_password);
     }catch(PDOException $exception){
         echo "Connection failed: " . $exception -> getMessage();
     }

     return $this->conn;
 }
}
?>