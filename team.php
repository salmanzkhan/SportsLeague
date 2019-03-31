<?php
session_start();// start the session

require "FN.class.php";
require_once "DB1.class.php";

echo "<link rel='stylesheet' type='text/css' href='css/layout.css' />";
echo "<link rel='stylesheet' type='text/css' href='css/style.css' />";

if (isset($_SESSION["loggedIn"])) {//if the user is logged in
    
  $roles=$_SESSION['roleID']; 
    $five=5;

    $db2 = new DB1();
     FN::errorLog("Inside schedule block");
    $teamID = $_GET["teamID"];
    echo '<div class="header"><h1>Sports League</h1></div>';
    
        echo '<div class="topnav">
                             <a href="schedule.php?teamID='.$teamID.'">Match Schedule</a>
                              <div class="topnav-right">
                            <a href="logout.php">Logout</a>
                            </div>      
            </div>';

    $teamID = $_GET["teamID"];
    
    //query to fetch the team details 
     $query1 = "SELECT server_team.id as id,server_team.name as name,mascot,server_sport.name as sports,server_league.name as league,server_season.description as season,picture,homecolor,awaycolor,maxplayers
        FROM server_team, server_sport, server_league,server_season
        WHERE server_team.sport=server_sport.id
        AND server_team.league=server_league.id
        AND server_team.season=server_season.id
        AND server_team.id=:teamId"; 
    
    //execute the query and store result in $result
    $result=$db2->getResult($query1,array('teamId'=>$teamID));
    
   foreach ($result as &$record) {
            $picture     = $record["picture"];
            $record["picture"] = "<img src='$picture'>";
        }
        
    echo " " . "<br><br>";
    echo FN::build_table($result);// Build and display the table 
    FN::errorLog("Display Team page result");
    echo " " . "<br>";
    
    // if the user is admin show him the link to admin page
     if($_SESSION['roleID']!=5){
        echo "<a href='admin.php'>Admin</a>";
    }
    echo " " . "<br>";
   
} else {
    echo "You need to login" . "<br>";
    echo "<a href='login.php'>LogIn</a>";
}
    
?>