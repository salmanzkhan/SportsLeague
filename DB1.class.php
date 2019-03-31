<?php
class DB1 {
/* DB1 is the file where all the queries are executed here and the functions are reused at different places in the project */
  private $dbh;

  function __construct() {
    require_once ("db_conn2.php");
      
    try {
      //open a connection
      $this->dbh = new PDO("mysql:host=$host;dbname=$db", $user, $pass);
      //change error reporting
      $this->dbh->setAttribute(PDO::ATTR_ERRMODE,PDO::ERRMODE_EXCEPTION);		
    } 
    catch (PDOException $e) {
      echo $e->getMessage();
      die();			
    }
  }	

  function getResult($query,$vars=array()) {
    try {
      $data = array();
        $stmt = $this->dbh->prepare($query);
      //$stmt->execute(array('id'=>$id));
        $stmt->execute($vars);
      
      while ($row = $stmt->fetch(PDO::FETCH_ASSOC) ) {
        $data[] = $row;
      }
      
      return $data;
    } 
    catch (PDOException $e) {
      echo $e->getMessage();
      die();			
    }
  }     
    
function insertResult($query,$vars=array()) {
    try {
      $data = array();
        $stmt = $this->dbh->prepare($query);
        $stmt->execute($vars);
   
    } 
    catch (PDOException $e) {
      echo $e->getMessage();
      die();			
    }
  }   
function getInsertId($query,$vars=array()) {
    try {
      $data = array();
        $stmt = $this->dbh->prepare($query);
        $stmt->execute($vars);
   
      return $this->dbh->lastInsertId();
    } 
    catch (PDOException $e) {
      echo $e->getMessage();
      die();			
    }
  }       
 
} //class
?>