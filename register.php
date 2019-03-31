<?php
session_start();
require_once "DB1.class.php";
$db2 = new DB1();
require "FN.class.php";

echo "<link rel='stylesheet' type='text/css' href='css/style.css' />";
echo'
      <div id="frm1">
                <form action="register.php" method="post">
                    <p>
                        <label>Username:</label>
                        <input type="text" id="username" name="username" />
                    </p>
                     <p>
                <label>Password:</label>
                <input type="password" id="password" name="password" />
            </p>
            <p>
                <label>Confirm Password:</label>
                <input type="password" id="confirm" name="confirm" />
            </p>
                   
                         <p>
                        Team: <select name="team">
                <option value="RCB">RCB</option>
                <option value="KKR">KKR</option>
                <option value="Mumbai Indians">Mumbai Indians</option>
                <option value="CSK">CSK</option>
                <option value="Real Madrid">Real Madrid</option>
                <option value="FCB">FCB</option>
                <option value="Liverpool">Liverpool</option>
                <option value="Chelsea">Chelsea</option>
                
                </select>
                       
                    </p>
                    
                    League: <select name="league">
                <option value="IPL">IPL</option>
                <option value="champions league">champions league</option>
                </select>
                    <p>
                    </p>
                    <p>

                        <input type="submit" id="btn" name="submitRegister" value="submit"/>
                    </p>

                </form>    
                
    </div>  
    ';
    
                

if (($_SERVER["REQUEST_METHOD"] == "POST") && isset($_POST['submitRegister'])) {
                
            $username = $_POST["username"];
            
            $league   = $_POST["league"];
            $team   = $_POST["team"];    
            $password = $_POST["password"];
            $confirm  = $_POST["confirm"];
            
             $query2 = "SELECT * FROM server_team where name= :name";
            $getteam = $db2->getResult($query2,array('name'=>$team));
            $team_ID=$getteam[0]["id"];
                
             $query2 = "SELECT * FROM server_league where name= :name";
            $getleague = $db2->getResult($query2,array('name'=>$league));
            $league_ID=$getleague[0]["id"];    
         
                $role_ID=5;
           
          
            if ($password == $confirm) {
              
                 FN::errorLog("confirmed Password");
                
                $hashed_password = password_hash($password, PASSWORD_DEFAULT);
                
                 $query_adduser_tm = "INSERT INTO server_user(username, role, password,team,league)
        VAlUES(:username,:role,:password,:team,:league)";
               
                
                $result_add_user = $db2->insertResult($query_adduser_tm,array('username'=>$username,'role'=>$role_ID,'password'=>$hashed_password,'team'=>$team_ID,'league'=>$league_ID));
                 FN::errorLog("User Registered successfully");
                header("Location: login.php?");
                
            }else{
                echo "No Matching password";
                FN::errorLog("No Matching password");
            }
            
                
            }

?>