<!DOCTYPE HTML>
<html>
<head>
        <title>Login Page</title>
        <link rel="stylesheet" type="text/css" href="css/style.css">
</head>
<body>
    <div id="frm">
        <form action="login.php" method="post">
            <p>
                <label>Username:</label>
                <input type="text" id="username" name="username" />
            </p>
            <p>
                <label>Password:</label>
                <input type="password" id="password" name="password" />
            </p>
            <p>
              
                <input type="submit" id="btn" name="login" value="submit"/>
            </p>
             <p>
              
                <input type="submit" id="btn" name="register" value="Register"/>
            </p>
            <div class="g-recaptcha" data-sitekey="6Lfp65gUAAAAAAHm-fKzcDipAyY1ifIoFeC0JbfF"></div>
        </form>  
         <script src="https://www.google.com/recaptcha/api.js" async defer></script>
    </div>  
</body>
</html>  
<?php
session_start();
/*require_once "DB.class.php";*/
require "FN.class.php";
require_once "DB1.class.php";
/*$db = new DB();*/

 $db2 = new DB1();
    
    
if (($_SERVER["REQUEST_METHOD"] == "POST") && isset($_POST["login"])) {
    
    
    $user     = $_POST["username"];
    $password = $_POST["password"];
    $name     = "";
   
    //Query to fertch user credentials
    $query = "SELECT * FROM server_user where username= :username";
    $res = $db2->getResult($query,array('username'=>$user));
    
    if ($res != null) {
        foreach ($res as &$record) {
            $name   = $record["username"];
            $pass   = $record["password"];
            $role   = $record["role"];
            $team   = $record["team"];
            $league = $record["league"];
        }
    }
    
    //secert key, response key required to implement captcha
    $secretKey="6Lfp65gUAAAAALu5l9R8ssKdFvpt_NQJcz1bvIB3";
    $responseKey=$_POST['g-recaptcha-response'];
    $url="https://www.google.com/recaptcha/api/siteverify?secret=$secretKey&response=$responseKey";
    $response= file_get_contents($url);
    //converting the response to JSON to check if it's success
    $response=json_decode($response);

//condition to check if it's success    
if(($response->success)){   
if ($name == $user && password_verify($password, $pass)) { //condition to validate username and password
        $_SESSION['loggedIn'] = "true"; // set session loggedin as true
        $_SESSION['team']=$team;
        $_SESSION['userLogin']=$name;
        $_SESSION['roleID']=$role;
        $_SESSION['league']=$league;
      
         if ($role == 5) { //if the role is parent
     
            $_SESSION['loggedIn'] = "true";
            FN::errorLog("Logged parent successfully"); // message is stored in the log file
            header("Location: team.php?teamID=$team"); // if the user is parent redirect to team page
           
        }else{
             FN::errorLog("Logged in successfully"); 
            header("Location: admin.php?teamID=$team");// if the user not parent, redirect to admin page
             $_SESSION['role']=$role;
        
         }
        
    }
    
    else {
        echo "Invalid credentials";
    }
}else{
    echo "Please select the captcha";
}
     
}

//If the user is new and want to register
if(($_SERVER["REQUEST_METHOD"] == "POST") && isset($_POST["register"])){
    
    //redirect to register page
     header("Location: register.php?");

}
?>