<?php
session_start();
require "FN.class.php";
if (isset($_SESSION["loggedIn"])){
    
    session_unset(); // unset the session
    session_destroy();// destroy the session
    FN::errorLog("User logout successfully");// Message is logged ini log file
    header("Location: login.php");
 
    
}else{
     echo"You need to login"."<br>";
      echo "<a href='login.php'>LogIn</a>";
}


?>