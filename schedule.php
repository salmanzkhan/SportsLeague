<?php
session_start();// start session

require "FN.class.php"; // import the files
require_once "DB1.class.php";
echo "<link rel='stylesheet' type='text/css' href='css/layout.css' />";
echo "<link rel='stylesheet' type='text/css' href='css/style.css' />";

if (isset($_SESSION["loggedIn"])) {

    $db2 = new DB1();
    $teamID = $_GET["teamID"];
    //header
    echo '<div class="header"><h1>Sports League</h1></div>';
           
                        echo '<div class="topnav">
                             <a href="team.php?teamID='.$teamID.'">Team</a>
                              <div class="topnav-right">
                                    <a href="logout.php">Logout</a>
                                </div>      
                            </div>';
   
    FN::errorLog("welcome to Schedule page");
    echo " " . "<br>";
    echo " " . "<br>";
    
    // query to fetch the schedule details for the team
     $query2 = "SELECT server_sport.name as sport,server_league.name as league,server_season.description as season,server_team.name as hometeam,(select name from server_team where id= server_schedule.awayteam) as awayteam,homescore,awayscore,scheduled,completed
        FROM server_schedule,server_team, server_sport, server_league,server_season
        WHERE server_schedule.sport=server_sport.id
        AND server_schedule.league=server_league.id
        AND server_schedule.hometeam=server_team.id
        AND server_schedule.season=server_season.id
        AND (server_schedule.hometeam= :homeId OR server_schedule.awayteam=:awayId)";
        
    
    // execute the query and store the result array in result
     $result = $db2->getResult($query2,array('homeId'=>$teamID,'awayId'=>$teamID));
 
    // builds the table for the result from the above query
    echo FN::build_table($result) . "<br>";
     FN::errorLog("Display Schedule rseult");
     
    // if the user is admin, show the link to redirect to admin page
    if($_SESSION['roleID']!=5){
        echo "<a href='admin.php'>Admin</a>";
    }
    echo " " . "<br>";
    
} else {
    
    echo "You need to login" . "<br>";
    echo "<a href='login.php'>LogIn</a>";
}
?>