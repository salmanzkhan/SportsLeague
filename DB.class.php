<?php
class DB
{
    
    private $mysqli;
    
    function __construct()
    {
        
        require_once "db_conn.php";
        
        //open connection756/
        $this->mysqli = new mysqli($db_host, $db_user, $db_pass, $db_name);
        
        //2 check conncection;
        if ($this->mysqli->connect_error) {
            echo "connection failed " . $this->mysqli->connect_error;
            exit();
        }
        
    }
    
    function get_people($user)
    {
        $people=[];
        $pass  = "";
        $query = "SELECT *
                FROM server_user where username= ?";
        if ($stmt = $this->mysqli->prepare($query)) {
            $stmt->bind_param("s", $user);
            $stmt->execute();
            $stmt->store_result();
            $num_rows = $stmt->affected_rows;
            $stmt->bind_result($username, $role, $password, $team, $league);
            
            if ($num_rows > 0) {
                while ($stmt->fetch()) {
                    $people[] = array(
                        "username" => $username,
                        "password" => $password,
                        "role" => $role,
                        "team" => $team,
                        "league" => $league
                    );
                }
                
                
            }
            return $people;
        } else {
            return null;
        }
    }
    
    
    function get_team($teamID)
    {
        $people=[];
        $pass = "";
        
        
        $query1 = "SELECT server_team.id as id,server_team.name as name,mascot,server_sport.name as sports,server_league.name as league,server_season.description as season,picture,homecolor,awaycolor,maxplayers
        FROM server_team, server_sport, server_league,server_season
        WHERE server_team.sport=server_sport.id
        AND server_team.league=server_league.id
        AND server_team.season=server_season.id
        AND server_team.id=?";
        
        
        $query = "SELECT *
                FROM server_team where id= ?";
        if ($stmt = $this->mysqli->prepare($query1)) {
            $stmt->bind_param("i", $teamID);
            $stmt->execute();
            $stmt->store_result();
            $num_rows = $stmt->affected_rows;
            $stmt->bind_result($id, $name, $mascot, $sport, $league, $season, $picture, $homecolor, $awaycolor, $maxplayers);
            
            if ($num_rows > 0) {
                while ($stmt->fetch()) {
                    $people[] = array(
                        "id" => $id,
                        "name" => $name,
                        "mascot" => $mascot,
                        "sport" => $sport,
                        "league" => $league,
                        "season" => $season,
                        "picture" => $picture,
                        "homecolor" => $homecolor,
                        "awaycolor" => $awaycolor,
                        "maxplayers" => $maxplayers
                        
                    );
                }
                
            }
            return $people;
        } 
    }
    
    function get_schedule($teamID)
    {
        $people=[];
        $pass = "";
        
        $query2 = "SELECT server_sport.name as sport,server_league.name as league,server_season.description as season,server_team.name as hometeam,(select name from server_team where id= server_schedule.awayteam) as awayteam,homescore,awayscore,scheduled,completed
        FROM server_schedule,server_team, server_sport, server_league,server_season
        WHERE server_schedule.sport=server_sport.id
        AND server_schedule.league=server_league.id
        AND server_schedule.hometeam=server_team.id
        AND server_schedule.season=server_season.id
        AND server_schedule.hometeam= ?;";
        
        
        if ($stmt = $this->mysqli->prepare($query2)) {
            $stmt->bind_param("i", $teamID);
            $stmt->execute();
            $stmt->store_result();
            $num_rows = $stmt->affected_rows;
            
            $stmt->bind_result($sport, $league, $season, $hometeam, $awayteam, $homescore, $awayscore, $scheduled, $completed);
            
            if ($num_rows > 0) {
                while ($stmt->fetch()) {
                    
                    $people[] = array(
                        
                        "sport" => $sport,
                        "league" => $league,
                        "season" => $season,
                        "hometeam" => $hometeam,
                        "awayteam" => $awayteam,
                        "homescore" => $homescore,
                        "awayscore" => $awayscore,
                        "scheduled" => $scheduled,
                        "completed" => $completed
                        
                    );
                }
                
                
            }
            return $people;
        } 
    }
    
    function get_position($teamID)
    {
        $people=[];
        $pass = "";
        
        $query = "SELECT *
                FROM server_position";
        if ($stmt = $this->mysqli->prepare($query)) {
            $stmt->execute();
            $stmt->store_result();
            $num_rows = $stmt->affected_rows;
            $stmt->bind_result($id, $name);
            
            if ($num_rows > 0) {
                while ($stmt->fetch()) {
                    
                    $people[] = array(
                        
                        "Name" => $id,
                        "id" => $name
                    );
                }
                
            }
            return $people;
        } else {
            return null;
        }
    }
    
    function add_position($playerId, $playerPosition, $teamID)
    {
        $query = "INSERT INTO server_position(id, name)
        VAlUES(?,?)";
        
        if ($stmt = $this->mysqli->prepare($query)) {
            
            $stmt->bind_param("is", $playerId, $playerPosition);
            
            $stmt->execute();
            $stmt->store_result();
            header("Location: admin.php?teamID=$teamID");
            
        }
        
        
    }
    
    function add_player($playerId, $firstname, $lastname, $dateofbirth, $jerseynumber, $teamID)
    {
        $query = "INSERT INTO server_player(`id`,`firstname`,`lastname`,`dateofbirth`,`jerseynumber`,`team`)
        VAlUES(?,?,?,?,?,?)";
        
        if ($stmt = $this->mysqli->prepare($query)) {
            
            $stmt->bind_param("isssss", $playerId, $firstname, $lastname, $dateofbirth, $jerseynumber, $teamID);
            
            $stmt->execute();
            $stmt->store_result();
            header("Location: admin.php?teamID=$teamID");
            
        }
    }
    function get_players($teamID)
    {
        $people=[];
        $pass = "";
        
        $query = "SELECT *
                FROM server_player where team= ?";
        if ($stmt = $this->mysqli->prepare($query)) {
            $stmt->bind_param("i", $teamID);
            $stmt->execute();
            $stmt->store_result();
            $num_rows = $stmt->affected_rows;
            $stmt->bind_result($id, $firstname, $lastname, $dateofbirth, $jerseynumber, $team);
            
            if ($num_rows > 0) {
                while ($stmt->fetch()) {
                    
                    $people[] = array(
                        "id" => $id,
                        "firstname" => $firstname,
                        "lastname" => $lastname,
                        "dateofbirth" => $dateofbirth,
                        "jerseynumber" => $jerseynumber,
                        "team" => $team
                    );
                }
            }
            return $people;
        } 
    }
    
    function delete_player($playerId)
    {
        $query = "DELETE FROM server_player WHERE id = ?";
        
        if ($stmt = $this->mysqli->prepare($query)) {
            $stmt->bind_param("i", $playerId);
            $stmt->execute();
            header("Location: admin.php");
        }
    }
    
    function edit_player($playerId, $firstname, $lastname, $dateofbirth, $jerseynumber, $team)
    {
        $query = "UPDATE server_player SET firstname = ?, lastname= ?, dateofbirth = ?,jerseynumber = ? Where id = ?";
        
        if ($stmt = $this->mysqli->prepare($query)) {
            $stmt->bind_param("ssssi", $firstname, $lastname, $dateofbirth, $jerseynumber, $playerId);
            $stmt->execute();
            header("Location: admin.php");
        }
    }
    function get_user($teamID)
    {
        $people=[];
        $pass = "";
        
        $query = "select username,server_roles.name as role,server_league.name as league,server_team.name as team
        from server_user,server_roles,server_league,server_team
        where server_user.role=server_roles.id
        AND server_user.league=server_league.id
        AND server_user.team=server_team.id
        AND server_roles.name IN('Parent','Coach','Team Manager')
        AND server_user.team= ?";
        
        if ($stmt = $this->mysqli->prepare($query)) {
            $stmt->bind_param("i", $teamID);
            $stmt->execute();
            $stmt->store_result();
            $num_rows = $stmt->affected_rows;
            $stmt->bind_result($username, $role, $league, $team);
            
            if ($num_rows > 0) {
                while ($stmt->fetch()) {
                    $people[] = array(
                        "username" => $username,
                        "role" => $role,
                        "league" => $league,
                        
                        "team" => $team
                    );
                }
                
            }
            return $people;
        } 
    }
    
    function edit_user($username,$role,$league,$team){

        $query2 = "SELECT * FROM server_roles where name= ?";
        
         if ($stmt = $this->mysqli->prepare($query2)) {
            $stmt->bind_param("s", $role);
            $stmt->execute();
            $stmt->store_result();
            $num_rows = $stmt->affected_rows;
            $stmt->bind_result($id, $name);
            $stmt->fetch();
            $stmt->close();
        
            
        }
     
    $query3 = "SELECT * FROM server_league where name= ?";    
    if ($stmt = $this->mysqli->prepare($query3)) {
            $stmt->bind_param("s", $league);
            $stmt->execute();
            $stmt->store_result();
            $num_rows = $stmt->affected_rows;
            $stmt->bind_result($lid, $lname);
            $stmt->fetch();
            $stmt->close();
        
        $leagueId=  $lid;  
        }
        
        $roleId=$id;
    
        $query = "UPDATE server_user SET role = ?, league= ? Where username = ?";
        if ($stmt = $this->mysqli->prepare($query)) {
            echo "Inside Update";
            $stmt->bind_param("iis", $roleId, $leagueId, $username);
            $stmt->execute();
            header("Location: admin.php");
        }
    }
    
    
    function add_user($username,$role,$hashed_password,$teamID,$league)
    {
        
         $query2 = "SELECT * FROM server_roles where name= ?";
        
         if ($stmt = $this->mysqli->prepare($query2)) {
            $stmt->bind_param("s", $role);
            $stmt->execute();
            $stmt->store_result();
            $num_rows = $stmt->affected_rows;
            $stmt->bind_result($id, $name);
            $stmt->fetch();
            $stmt->close();
        $roleID=$id;
            
        }
         $query3 = "SELECT * FROM server_league where name= ?";    
    if ($stmt = $this->mysqli->prepare($query3)) {
            $stmt->bind_param("s", $league);
            $stmt->execute();
            $stmt->store_result();
            $num_rows = $stmt->affected_rows;
            $stmt->bind_result($lid, $lname);
            $stmt->fetch();
            $stmt->close();
        
        $leagueId=  $lid;  
        }
        $query = "INSERT INTO server_user(username, role, password,team,league)
        VAlUES(?,?,?,?,?)";
        
        if ($stmt = $this->mysqli->prepare($query)) {
            
            $stmt->bind_param("sisii", $username,$roleID,$hashed_password,$teamID,$leagueId);
            
           $stmt->execute();
            $stmt->store_result();
            header("Location: admin.php?teamID=$teamID");
            
        }
        
        
    }
   
    function delete_user($username)
    {
        echo $username;
        $query = "DELETE FROM server_user WHERE username = ?";
        
        if ($stmt = $this->mysqli->prepare($query)) {
            $stmt->bind_param("s", $username);
            $stmt->execute();
             $stmt->close();
            header("Location: admin.php");
        }
        
    }
   
        function get_seasons()
    {
        $season=[];
        $query = "select id,year, description as season FROM server_season";
        
        if ($stmt = $this->mysqli->prepare($query)) {
        //    $stmt->bind_param("i", $teamID);
            $stmt->execute();
            $stmt->store_result();
            $num_rows = $stmt->affected_rows;
            $stmt->bind_result($id, $year, $description);
            
            if ($num_rows > 0) {
                while ($stmt->fetch()) {
                    $season[] = array(
                        "id"=>$id,
                        "year"=>$year,
                        "season" => $description
                       
                    );
                }
                
            }
            return $season;
        } 
    }
    
   
      function add_season($id,$year,$season)
    {
        
        $query = "INSERT INTO server_season(id,year,description)
        VAlUES(?,?,?)";
        
        if ($stmt = $this->mysqli->prepare($query)) {
            
            $stmt->bind_param("iis", $id,$year,$season);
            
           $stmt->execute();
            $stmt->store_result();
             $stmt->close();
            header("Location: admin.php?teamID=$teamID");
            
        }
        
    }
    
   
     function delete_season($seasonId)
    {
       
        $query = "DELETE FROM server_season WHERE id = ?";
        
        if ($stmt = $this->mysqli->prepare($query)) {
            $stmt->bind_param("i", $seasonId);
            $stmt->execute();
            header("Location: admin.php");
        }
        
        
    }
    
     function  edit_season($seasonId,$year,$season)
    {
 
        $query = "UPDATE server_season SET year = ?, description= ? Where id = ?";
        
        if ($stmt = $this->mysqli->prepare($query)) {
            $stmt->bind_param("isi", $year, $season, $seasonId);
            $stmt->execute();
            header("Location: admin.php");
        }
    }    
    
    
    function get_league_team($leagueId)
    {
        $team_league=[];
        
        $query = "select server_team.id as id,server_team.name as team,server_team.mascot as mascot,server_team.homecolor as homecolor,server_team.awaycolor as awaycolor,server_team.maxplayers as maxplayers,server_league.name as league from server_team,server_league where server_team.league=server_league.id
        AND league= ?";
        
        if ($stmt = $this->mysqli->prepare($query)) {
            $stmt->bind_param("i", $leagueId);
            $stmt->execute();
            $stmt->store_result();
            $num_rows = $stmt->affected_rows;
            $stmt->bind_result($id, $team,$mascot,$homecolor,$awaycolor,$maxplayers,$league);
            
            if ($num_rows > 0) {
                while ($stmt->fetch()) {
                    $team_league[] = array(
                        "id"=>$id,
                        "team"=>$team,
                        "mascot"=>$mascot,
                        "homecolor"=>$homecolor,
                        "awaycolor"=>$awaycolor,
                        "maxplayers"=>$maxplayers,
                        "league" => $league
                       
                    );
                }
                
            }
            return $team_league;
        } 
    }
    
        function get_sportLeagueSession()
    {
        $sport_league=[];
        
        $query = "SELECT sport as sp,season as se,league as le,server_sport.name as sport,server_league.name as league,server_season.description as season FROM server_slseason,server_sport,server_league,server_season
        where server_slseason.sport=server_sport.id
        AND server_slseason.league=server_league.id
        AND server_slseason.season=server_season.id";
        
        if ($stmt = $this->mysqli->prepare($query)) {
     
            $stmt->execute();
            $stmt->store_result();
            $num_rows = $stmt->affected_rows;
            $stmt->bind_result($sp,$se,$le,$sport, $league, $season);
            
            if ($num_rows > 0) {
                while ($stmt->fetch()) {
                    $sport_league[] = array(
                        "id"=>$sp.".".$se.".".$le,
                        "sport"=>$sport,
                        "league"=>$league,
                        "season" => $season
                       
                    );
                }
                
            }
            return $sport_league;
        } 
    }
    

     function add_sls($sport,$league,$year,$season)
    {
        
        $query = "INSERT INTO server_sport(name)
        VAlUES(?)";
        
        if ($stmt = $this->mysqli->prepare($query)) {
            
            $stmt->bind_param("s", $sport);
            
            $stmt->execute();
            $stmt->store_result();
            $num_rows=$stmt->affected_rows;
        
            $sport_id=$stmt->insert_id;
             $stmt->close();
        
            
        }
         
          $query1 = "INSERT INTO server_league(name)
        VAlUES(?)";
        
        if ($stmt = $this->mysqli->prepare($query1)) {
            
            $stmt->bind_param("s", $league);
            
            $stmt->execute();
            $stmt->store_result();
            $num_rows=$stmt->affected_rows;
        
            $league_id=$stmt->insert_id;
             $stmt->close();
        
            
        }
         
        $query2 = "INSERT INTO server_season(year,description)
        VAlUES(?,?)";
        
        if ($stmt = $this->mysqli->prepare($query2)) {
            
            $stmt->bind_param("is", $year,$season);
            
            $stmt->execute();
            $stmt->store_result();
            $num_rows=$stmt->affected_rows;
        
            $season_id=$stmt->insert_id;
             $stmt->close();
        
            
        }
        $query3 = "INSERT INTO server_slseason(sport, league, season)
        VAlUES(?,?,?)";
        
        if ($stmt = $this->mysqli->prepare($query3)) {
            
            $stmt->bind_param("iii", $sport_id,$league_id,$season_id);
            
            $stmt->execute();
            $stmt->store_result();
           
             $stmt->close();
            header("Location: admin.php");
        
            
        } 
        
    }
    
      function edit_sls($sportId,$leagueId,$seasonId,$sport,$league,$season)
    {
            $query = "UPDATE server_sport SET name = ? Where id = ?";
        
        if ($stmt = $this->mysqli->prepare($query)) {
            $stmt->bind_param("si", $sport, $sportId);
            $stmt->execute();
             $stmt->store_result();
             $stmt->close();
           
        }
          
           $query1 = "UPDATE server_season SET description = ? Where id = ?";
        
        if ($stmt = $this->mysqli->prepare($query1)) {
            $stmt->bind_param("si", $season, $seasonId);
            $stmt->execute();
             $stmt->store_result();
             $stmt->close();
           
        }
          
           $query2 = "UPDATE server_league SET name = ? Where id = ?";
        
        if ($stmt = $this->mysqli->prepare($query2)) {
            $stmt->bind_param("si", $league, $leagueId);
            $stmt->execute();
             $stmt->store_result();
             $stmt->close();
             header("Location: admin.php");
        }
    }    
   
      function  edit_team_league($teamID, $team, $mascot, $homecolor, $awaycolor, $maxplayers)
    {
       
        $query = "UPDATE server_team SET name = ?, mascot= ?,homecolor=?,awaycolor=?,maxplayers=? Where id = ?";
        
        if ($stmt = $this->mysqli->prepare($query)) {
            $stmt->bind_param("ssssii",$team, $mascot,$homecolor,$awaycolor,$maxplayers,$teamID);
            $stmt->execute();
             $stmt->close();
            header("Location: admin.php");
        }
    }    
    
 function delete_teamInLeague($teamIdToDel)
    {
    
        $query = "DELETE FROM server_team WHERE id = ?";
        
        if ($stmt = $this->mysqli->prepare($query)) {
            $stmt->bind_param("i", $teamIdToDel);
            $stmt->execute();
            header("Location: admin.php");
        }
        
    }
    
    function add_team_league($teamName,$mascotName,$homecolor,$awaycolor,$maxplayer,$lId,$tID)
    {
         $query2 = "select sport,league,season from server_team where league=? AND id=?";
        
         if ($stmt = $this->mysqli->prepare($query2)) {
            $stmt->bind_param("ii", $lId,$tID);
            $stmt->execute();
            $stmt->store_result();
            $num_rows = $stmt->affected_rows;
            $stmt->bind_result($sport, $league,$season);
            $stmt->fetch();
            $stmt->close();
        $sportID=$sport;
        $leagueID=$league;
        $seasonID=$season;    
            
        }
        
       $query = "INSERT INTO server_team(id,name,mascot,sport,league, season,picture,homecolor,awaycolor, maxplayers)
        VAlUES(?,?,?,?,?,?,?,?,?,?)";
        header("Location: admin.php?teamID=$teamID");
        if ($stmt = $this->mysqli->prepare($query)) {
            $picture=pic;
            $ttid="";
            $stmt->bind_param("issiiisssi", $ttid,$teamName,$mascotName,$sportID,$leagueID,$seasonID,$picture,$homecolor,$awaycolor,$maxplayer);
            
           $stmt->execute();
            $stmt->store_result();
             $stmt->close();
            header("Location: admin.php?teamID=$teamID");
            
        }
        
    }
    
     function getLeagueUser()
    {
        $sport_league=[];
        $query = "select username,server_roles.name as role,server_team.name as team,server_league.name as league from server_user,server_team,server_roles,server_league where 
        server_user.team=server_team.id
        AND server_user.league=server_league.id
        AND server_user.role=server_roles.id
        AND role IN (3,4)";
        
        if ($stmt = $this->mysqli->prepare($query)) {
            $stmt->execute();
            $stmt->store_result();
            $num_rows = $stmt->affected_rows;
            $stmt->bind_result($username,$role,$team, $league);
            
            if ($num_rows > 0) {
                while ($stmt->fetch()) {
                    $sport_league[] = array(
                        "username"=>$username,
                        "role"=>$role,
                        "team" => $team,
                        "league"=>$league
                        
                       
                    );
                }
                
            }
            return $sport_league;
        } 
    }
    
     function add_user_league($user,$roleAcess,$hashed_password,$selectTeam,$leagueAssign)
    {

         $query2 = "SELECT * FROM server_roles where name= ?";
        
         if ($stmt = $this->mysqli->prepare($query2)) {
            $stmt->bind_param("s", $roleAcess);
            $stmt->execute();
            $stmt->store_result();
            $num_rows = $stmt->affected_rows;
            $stmt->bind_result($id, $name);
            $stmt->fetch();
            $stmt->close();
        $roleID=$id;
            
        }
         $query3 = "SELECT * FROM server_league where name= ?";    
    if ($stmt = $this->mysqli->prepare($query3)) {
            $stmt->bind_param("s", $leagueAssign);
            $stmt->execute();
            $stmt->store_result();
            $num_rows = $stmt->affected_rows;
            $stmt->bind_result($lid, $lname);
            $stmt->fetch();
            $stmt->close();
        
        $leagueId=  $lid;  
        }
        
            $query4 = "SELECT id FROM server_team where name= ?";    
    if ($stmt = $this->mysqli->prepare($query4)) {
            $stmt->bind_param("s", $selectTeam);
            $stmt->execute();
            $stmt->store_result();
            $num_rows = $stmt->affected_rows;
            $stmt->bind_result($stId);
            $stmt->fetch();
            $stmt->close();
        
        $selectTeamId=  $stId; 
        }
       
        $query = "INSERT INTO server_user(username, role, password,team,league)
        VAlUES(?,?,?,?,?)";
        
        if ($stmt = $this->mysqli->prepare($query)) {
            
            $stmt->bind_param("sisii", $user,$roleID,$hashed_password,$selectTeamId,$leagueId);
            
           $stmt->execute();
            $stmt->store_result();
            header("Location: admin.php");
            
        }
        
        
    }
    
    function edit_league_user($user_name,$role,$league,$team){
        
        $query2 = "SELECT * FROM server_roles where name= ?";
        
         if ($stmt = $this->mysqli->prepare($query2)) {
            $stmt->bind_param("s", $role);
            $stmt->execute();
            $stmt->store_result();
            $num_rows = $stmt->affected_rows;
            $stmt->bind_result($id, $name);
            $stmt->fetch();
            $stmt->close();
        
            
        }
     
    $query3 = "SELECT * FROM server_league where name= ?";    
    if ($stmt = $this->mysqli->prepare($query3)) {
            $stmt->bind_param("s", $league);
            $stmt->execute();
            $stmt->store_result();
            $num_rows = $stmt->affected_rows;
            $stmt->bind_result($lid, $lname);
            $stmt->fetch();
            $stmt->close();
        
        $leagueId=  $lid;  
        }
            
      $query4 = "SELECT id FROM server_team where name= ?";    
    if ($stmt = $this->mysqli->prepare($query4)) {
            $stmt->bind_param("s", $team);
            $stmt->execute();
            $stmt->store_result();
            $num_rows = $stmt->affected_rows;
            $stmt->bind_result($stId);
            $stmt->fetch();
            $stmt->close();
        
        $selectTeamId=  $stId; 
        $roleId=$id;
     
    }
        $query = "UPDATE server_user SET role = ?,team=?, league= ? Where username = ?";
        if ($stmt = $this->mysqli->prepare($query)) {
            echo "Inside Update";
            $stmt->bind_param("iiis", $roleId,$selectTeamId, $leagueId, $user_name);
            $stmt->execute();
             $stmt->close();
            header("Location: admin.php");
        }
    }

    
    function delete_league_user($user_name_lm)
    {
    
        $query = "DELETE FROM server_user WHERE username = ?";
        
        if ($stmt = $this->mysqli->prepare($query)) {
            $stmt->bind_param("s", $user_name_lm);
            $stmt->execute();
             $stmt->close();
            header("Location: admin.php");
        }
        
        
    }
    
     function getAdminUser()
    {
        $sport_league=[];
      
        $query = "select username,server_roles.name as role,server_team.name as team,server_league.name as league from server_user,server_team,server_roles,server_league where 
        server_user.team=server_team.id
        AND server_user.league=server_league.id
        AND server_user.role=server_roles.id";
        
        if ($stmt = $this->mysqli->prepare($query)) {
            $stmt->execute();
            $stmt->store_result();
            $num_rows = $stmt->affected_rows;
            $stmt->bind_result($username,$role,$team, $league);
            
            if ($num_rows > 0) {
                while ($stmt->fetch()) {
                    $sport_league[] = array(
                        "username"=>$username,
                        "role"=>$role,
                        "team" => $team,
                        "league"=>$league
                       
                    );
                }
                
            }
            return $sport_league;
        } 
    }
    
    function getAdminSport()
    {
        $sport=[];
        $query = "select * from server_sport";
        if ($stmt = $this->mysqli->prepare($query)) {
            $stmt->execute();
            $stmt->store_result();
            $num_rows = $stmt->affected_rows;
            $stmt->bind_result($id, $name);
            
            if ($num_rows > 0) {
                while ($stmt->fetch()) {
                    
                    $sport[] = array(
                        "id" => $id,
                        "name" => $name
                    );
                }
            }
            return $sport;
        } 
    }
    
    function edit_sport($sportId, $sport_name)
    {
            $query = "UPDATE server_sport SET name = ? Where id = ?";
        
        if ($stmt = $this->mysqli->prepare($query)) {
            $stmt->bind_param("si", $sport_name, $sportId);
            $stmt->execute();
             $stmt->store_result();
             $stmt->close();
             header("Location: admin.php");
        }
    }
    
    function delete_sport($sport_Id_admin)
    {
  
        $query = "DELETE FROM server_sport WHERE id = ?";
        
        if ($stmt = $this->mysqli->prepare($query)) {
            $stmt->bind_param("i", $sport_Id_admin);
            $stmt->execute();
             $stmt->close();
            header("Location: admin.php");
        }
        
        
    }
    function add_sport($sport_name)
    {
         $query = "INSERT INTO server_sport(name)
        VAlUES(?)";
        
        if ($stmt = $this->mysqli->prepare($query)) {
            
            $stmt->bind_param("s", $sport_name);
            $stmt->execute();
            $stmt->store_result();
            $stmt->close();
            header("Location: admin.php");
        }
    }
    
    function get_league_schedule($leagueID)
    {
        $people=[];
        $pass = "";
        
        $query2 = "SELECT server_schedule.sport as sp,server_schedule.season as se,server_schedule.league as le, server_schedule.hometeam as ht,server_schedule.awayteam as at,server_sport.name as sport,server_league.name as league,server_season.description as season,server_team.name as hometeam,(select name from server_team where id= server_schedule.awayteam) as awayteam,homescore,awayscore,scheduled,completed
        FROM server_schedule,server_team, server_sport, server_league,server_season
        WHERE server_schedule.sport=server_sport.id
        AND server_schedule.league=server_league.id
        AND server_schedule.hometeam=server_team.id
        AND server_schedule.season=server_season.id
        AND server_schedule.league=?";
        
        
        if ($stmt = $this->mysqli->prepare($query2)) {
            $stmt->bind_param("i", $leagueID);
            $stmt->execute();
            $stmt->store_result();
            $num_rows = $stmt->affected_rows;
            
            $stmt->bind_result($sp,$se,$le,$ht,$at,$sport, $league, $season, $hometeam, $awayteam, $homescore, $awayscore, $scheduled, $completed);
            
            if ($num_rows > 0) {
                while ($stmt->fetch()) {
                    
                    $people[] = array(
                        "id"=>$sp.".".$se.".".$le.".".$ht.".".$at,
                        "sport" => $sport,
                        "league" => $league,
                        "season" => $season,
                        "hometeam" => $hometeam,
                        "awayteam" => $awayteam,
                        "homescore" => $homescore,
                        "awayscore" => $awayscore,
                        "scheduled" => $scheduled,
                        "completed" => $completed
                        
                    );
                }
                
                
            }
            return $people;
        } 
    }
    
     function get_teamid($selectTeam)
    {

    $query4 = "SELECT id FROM server_team where name= ?";    
    if ($stmt = $this->mysqli->prepare($query4)) {
            $stmt->bind_param("s", $selectTeam);
            $stmt->execute();
            $stmt->store_result();
            $num_rows = $stmt->affected_rows;
            $stmt->bind_result($stId);
            $stmt->fetch();
            $stmt->close();
        
        $selectTeamId=  $stId; 
        }
       
       
        return $selectTeamId;
        
    }
    function get_leagueId($league)
    {

    $query3 = "SELECT * FROM server_league where name= ?";    
    if ($stmt = $this->mysqli->prepare($query3)) {
            $stmt->bind_param("s", $league);
            $stmt->execute();
            $stmt->store_result();
            $num_rows = $stmt->affected_rows;
            $stmt->bind_result($lid,$name);
            $stmt->fetch();
            $stmt->close();
        
        $leagueId=  $lid;  
        }
         
        return $leagueId;
        
    }
    
     function get_sportId($sport)
    {

    $query3 = "SELECT * FROM server_sport where name= ?";    
    if ($stmt = $this->mysqli->prepare($query3)) {
            $stmt->bind_param("s", $sport);
            $stmt->execute();
            $stmt->store_result();
            $num_rows = $stmt->affected_rows;
            $stmt->bind_result($sid,$name);
            $stmt->fetch();
            $stmt->close();
        
        $sportId=  $sid;  
        }
         
        return $sportId;
        
    }
    
     function get_seasonId($season)
    {

    $query3 = "SELECT * FROM server_season where description= ?";    
    if ($stmt = $this->mysqli->prepare($query3)) {
            $stmt->bind_param("s", $season);
            $stmt->execute();
            $stmt->store_result();
            $num_rows = $stmt->affected_rows;
            $stmt->bind_result($ssid,$year,$desc);
            $stmt->fetch();
            $stmt->close();
        
        $seasonID=  $ssid;  
        }
         
        return $seasonID;
        
    }
    
    
     function  edit_schedule($homeTeamSelectedID, $awayTeamSelectedID, $homescore, $awayscore, $completed, $scheduled,$sportId,$seasonId,$leagueId,$homeTeamId,$awayTeamId)
    {
         //`sport`, `league`, `season`,`hometeam`,`awayteam`,`homescore`,`awayscore`,`scheduled`,`completed`
       echo "edit schedule DB::";
        // echo "Home:".$homeTeamSelected;
         
        $query = "UPDATE server_schedule SET hometeam = ?, awayteam= ?,homescore=?,awayscore=?,scheduled=? ,completed=? Where sport = ? AND league=? AND season=? AND hometeam=? AND awayteam=?";
        
        if ($stmt = $this->mysqli->prepare($query)) {
            $stmt->bind_param("iiiisiiiiii",$homeTeamSelectedID,$awayTeamSelectedID,$homescore,$awayscore,$scheduled,$completed,$sportId,$leagueId,$seasonId,$homeTeamId,$awayTeamId);
            $stmt->execute();
             $stmt->close();
            header("Location: admin.php");
        }
    }    
    
    function  delete_schedule($sportId,$seasonId,$leagueId,$homeTeamId,$awayTeamId)
    {
         $query = "DELETE FROM server_schedule Where sport = ? AND league=? AND season=? AND hometeam=? AND awayteam=?";
        
        if ($stmt = $this->mysqli->prepare($query)) {
            $stmt->bind_param("iiiii", $sportId,$leagueId,$seasonId,$homeTeamId,$awayTeamId);
            $stmt->execute();
             $stmt->close();
            header("Location: admin.php");
        }
    }    
    
    
     function add_schedule($sportID,$leagueID,$seasonID,$homeTeamSelectedID,$awayTeamSelectedID,$homescore,$awayscore,$schedule,$completed)
    {
         echo "Inside add Db::";
         echo $sportID;
         echo $schedule;
         
          $query = "INSERT INTO server_schedule(sport, league, season,hometeam,awayteam,homescore,awayscore,scheduled,completed)
        VAlUES(?,?,?,?,?,?,?,?,?)";
         
       
        
        if ($stmt = $this->mysqli->prepare($query)) {
            
            $stmt->bind_param("iiiiiiisi", $sportID,$leagueID,$seasonID,$homeTeamSelectedID,$awayTeamSelectedID,$homescore,$awayscore,$schedule,$completed);
            
           $stmt->execute();
            $stmt->store_result();
            header("Location: admin.php");
            
        }
    }
    
}//end of class
?>