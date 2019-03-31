<?php
session_start();
require_once "DB.class.php";
require_once "DB1.class.php";
require "FN.class.php";
/*<link rel="stylesheet" href="layout.css" type="text/css">;*/
echo "<link rel='stylesheet' type='text/css' href='css/layout.css' />";
echo "<link rel='stylesheet' type='text/css' href='css/style.css' />";
echo "<link rel='stylesheet' type='text/css' href='css/twocolumn.css' />";

if (isset($_SESSION["loggedIn"])) {
    
    $roles = $_SESSION['roleID'];
    if ($roles != 5) { //condition to check if the user is not parent
        
        $db2 = new DB1();
        ob_start();
        $teamID      = $_SESSION['team'];
        $logged_user = $_SESSION['userLogin'];
        FN::errorLog("Session Team ID : " . $teamID);
        FN::errorLog("Session Username : " . $logged_user);
        
        // header
        echo '<div class="header"><h1>Sports League</h1></div>';
        
        
        echo '<div class="topnav">  
                        <a href="schedule.php?teamID=' . $teamID . '">Match Schedule</a> 
                        <a href="team.php?teamID=' . $teamID . '">Team</a>
                        <a href="admin.php">Admin</a>
                            <div class="topnav-right">
                                <a href="logout.php">Logout</a>  
                            </div>      
                 
            </div>';
        
        echo "<h3>Logged in as : $logged_user</h3>"; // logged in user 
        echo " " . "<br><br>";
        
        // query to fetch all the player postions
        $query_position = "SELECT * FROM server_position";
        
        /* echo "<h4>Player Position</h4>";*/
        $result_position = $db2->getResult($query_position);
        
        //table for the player position
        echo "<div class='tabnew'><h5>Player Position</h5>" . FN::build_table($result_position) . "</div>" . "<br><br>";
        FN::errorLog("result for player server_position");
        echo " " . "<br><br>";
        
        // form to enterthe  new player postion from the UI interface
        echo '
        <div id="frm4">
        <h5>Add Position</h5>
                <form action="admin.php" method="post">
                    <p>
                        <label class="col-25">Position:</label>
                        <input type="text" id="position" name="position" />
                    </p>
                    <p>
                        <input type="submit" id="btn" name="submit" value="submit"/>
                    </p>
                </form>    
        </div>  
        ';
        
        if (isset($_POST['submit'])) {
            FN::errorLog("Inside submit position");
            $errorPosition = $playerPosition = "";
            
            if (!empty($_POST["position"])) { // input validation and sanitization
                
                $playerPosition = FN::is_valid($_POST["position"]);
            } else {
                $errorPosition = "Please add Position";
                FN::errorLog($errorPosition);
            }
            
            //condition to check if there is no error above and the execute the queries
            if (!empty($errorPosition)) {
                echo '<div id="errorHandle">
                              <p id="line">' . $errorPosition . '</p>
                        </div>';
                
            } else {
                //Query to insert the player position
                $query1 = "INSERT INTO server_position(name)
                    VAlUES(:name)";
                
                $ress = $db2->insertResult($query1, array(
                    'name' => $playerPosition
                ));
                FN::errorLog("Insereted server_position in DB");
                header("Location: admin.php");
            }
        } // end 
        echo " " . "<br><br><br><br><br><br>";
        echo "<h4>List of Players</h4>";
        
        $query_players = "  SELECT server_player.id as id, firstname,lastname,dateofbirth,jerseynumber,server_team.name as team FROM server_player,server_team
            where server_player.team=server_team.id
            AND team =:team";
        
        if (($_SERVER["REQUEST_METHOD"] == "POST") && isset($_POST['submitplayer'])) {
            
            
            FN::errorLog("Inside submitplayer");
            $playerId     = $firstname = $lastname = $dateofbirth = $jerseynumber = $teamID = "";
            $errfirstname = $errlastname = $errdateofbirth = $errjerseynumber = "";
            
            //input validation and sanitization
            if (!empty($_POST["firstname"])) {
                $firstname = FN::is_valid($_POST["firstname"]);
            } else {
                $errfirstname = "Error in First Name";
                FN::errorLog($errfirstname);
            }
            if (!empty($_POST["lastname"])) {
                $lastname = FN::is_valid($_POST["lastname"]);
            } else {
                $errlastname = "Error in last Name";
                FN::errorLog($errlastname);
            }
            if (!empty($_POST["dateofbirth"])) {
                $dateofbirth = FN::is_valid($_POST["dateofbirth"]);
            } else {
                $errdateofbirth = "Error in dob";
                FN::errorLog($errdateofbirth);
            }
            
            if (!empty($_POST["jerseynumber"]) && is_numeric($_POST["jerseynumber"])) {
                $jerseynumber = FN::is_valid($_POST["jerseynumber"]);
            } else {
                $errjerseynumber = "Error in Jersey";
                FN::errorLog($errjerseynumber);
            }
            $teamID = $_SESSION['team'];
            
            if (!empty($errfirstname) || !empty($errlastname) || !empty($errdateofbirth) || !empty($errjerseynumber)) {
                echo '<div id="errorHandle">
                              <p id="line">' . $errfirstname . '</p>
                              <p id="line">' . $errlastname . '</p>
                              <p id="line">' . $errdateofbirth . '</p>
                               <p id="line">' . $errjerseynumber . '</p>

                        </div>';
                
            } else {
                // query to insert the new player details in the server_player table   
                $query = "INSERT INTO server_player(id,firstname,lastname,dateofbirth,jerseynumber,team)
        VAlUES(:id,:firstname,:lastname,:dateofbirth,:jerseynumber,:team)";
                
                $ressplayer = $db2->insertResult($query, array(
                    'id' => $playerId,
                    'firstname' => $firstname,
                    'lastname' => $lastname,
                    'dateofbirth' => $dateofbirth,
                    'jerseynumber' => $jerseynumber,
                    'team' => $teamID
                ));
                FN::errorLog("Inserted server_player table in DB");
                header("Location: admin.php");
            }
        }
        
        $teamID         = $_SESSION['team'];
        //execute the above query and store the result in the form of array
        $result_players = $db2->getResult($query_players, array(
            'team' => $teamID
        ));
        
        //Delete a player block 
        if (($_SERVER["REQUEST_METHOD"] == "POST") && isset($_POST["del"])) {
            
            FN::errorLog("Inside delete of player");
            $playerId = $_POST["id"];
            
            //query to delete a player by considering his id
            $query_delete_player = "DELETE FROM server_player WHERE id = :id";
            $ress_del            = $db2->insertResult($query_delete_player, array(
                'id' => $playerId
            ));
            FN::errorLog("deleted player");
            header("Location: admin.php");
            
        }
        
        if (($_SERVER["REQUEST_METHOD"] == "POST") && isset($_POST["Edit"])) { //Edit player block
            
            FN::errorLog("Inside Edit player");
            $playerId     = $_POST["id"];
            $firstname    = $_POST["firstname" . $playerId];
            $lastname     = $_POST["lastname" . $playerId];
            $dateofbirth  = $_POST["dateofbirth" . $playerId];
            $jerseynumber = $_POST["jerseynumber" . $playerId];
            
            //query to update the player information
            $query = "UPDATE server_player SET firstname = :firstname, lastname= :lastname, dateofbirth = :dateofbirth,jerseynumber = :jerseynumber Where id = :id";
            
            $db2->insertResult($query, array(
                'firstname' => $firstname,
                'lastname' => $lastname,
                'dateofbirth' => $dateofbirth,
                'jerseynumber' => $jerseynumber,
                'id' => $playerId
            ));
            FN::errorLog("Updated player in table");
            header("Location: admin.php");
        }
        foreach ($result_players as &$record) {
            $playerId     = $record["id"];
            $firstname    = $record["firstname"];
            $lastname     = $record["lastname"];
            $dateofbirth  = $record["dateofbirth"];
            $jerseynumber = $record["jerseynumber"];
            $team         = $record["team"];
            
            $record["id"]           = "<label><input type='radio' name='id' value=$playerId>$playerId</label>";
            $record["firstname"]    = "<input type='text' name = 'firstname$playerId' value=$firstname>";
            $record["lastname"]     = "<input type='text' name = 'lastname$playerId' value=$lastname>";
            $record["dateofbirth"]  = "<input type='text' name = 'dateofbirth$playerId' value=$dateofbirth>";
            $record["jerseynumber"] = "<input type='text' name = 'jerseynumber$playerId' value=$jerseynumber>";
            
        }
        
        echo "<form action=" . $_SERVER['PHP_SELF'] . " method='post'>";
        echo "<input type='hidden' name='playerID' value=$playerId>";
        
        // build the table for the above result array
        echo "<div class='tabPlayer'>" . FN::build_table($result_players) . "<br>";
        echo "<button class='button button1' type='submit' name='del' value='del'>Delete</button> ";
        echo "<button class='button button1' type='submit' name='Edit' value='Edit'>Edit</button> ";
        echo "</div>";
        echo "<br><br><br>";
        echo "</form>";
        
        // Form to insert a new player from user interface 
        echo '
    <div id="frm6">
            <h5>Add Player</h5>
        <form action="admin.php" method="post">
            <p>
                <label class="col-25">First Name:</label>
                <input type="text" id="firstname" name="firstname" />
            </p>
            <p>
                <label class="col-25">Last Name:</label>
                <input type="text" id="lastname" name="lastname" />
            </p>
            <p>
                <label class="col-25">Dateofbirth:</label>
                <input type="text" id="dateofbirth" name="dateofbirth" />
            </p>
            <p>
                <label class="col-25">Jersey Number:</label>
                <input type="text" id="jerseynumber" name="jerseynumber" />
            </p>
            <p>

            <input type="submit" id="btn" name="submitplayer" value="submit"/>
            </p>

        </form>    
    </div>  
    ';
        // query to fetch users for the roles parent,coach and Team manager
        $query_users = "select username,server_roles.name as role,server_league.name as league,server_team.name as team
        from server_user,server_roles,server_league,server_team
        where server_user.role=server_roles.id
        AND server_user.league=server_league.id
        AND server_user.team=server_team.id
        AND server_roles.name IN('Parent','Coach','Team Manager')
        AND server_user.team= :id";
        
        //Execute the above query and store the result
        $result_user = $db2->getResult($query_users, array(
            'id' => $teamID
        ));
        
        foreach ($result_user as &$record) {
            $tm       = $champ = $parent = $coach = $ipl = "";
            $username = $record["username"];
            $role     = $record["role"];
            $league   = $record["league"];
            $team     = $record["team"];
            
            if ($role == "Team Manager") {
                $tm = 'selected';
            } elseif ($role == "Coach") {
                $coach = 'selected';
            } else {
                $parent = 'selected';
            }
            
            if ($league == "IPL") {
                $ipl = 'selected';
            } else {
                $champ = 'selected';
            }
            
            $record["username"] = "<label><input type='radio' name='username' value=$username>$username</label>";
            
            $record["role"] = "<select name='role" . $username . "'>
            <option value='Team Manager' " . $tm . ">Team Manager</option>
             <option value='Coach' " . $coach . ">Coach</option>
              <option value='Parent' " . $parent . ">Parent</option>
            </selected>";
            
            $record["league"] = "<select name='league" . $username . "'>
            <option value='IPL' " . $ipl . ">IPL</option>
            
            </selected>";
        } // end for each loop
        
        echo "<form action=" . $_SERVER['PHP_SELF'] . " method='post'>";
        echo "<input type='hidden' name='playerID' value=$playerId>";
        
        //Build the table for the above usere query
        // echo "List of User that are Parent, Team Manager and coach";
        echo "<div class='tabuser'><h5>List of Users</h5>" . FN::build_table($result_user) . "<br>";
        echo "<button class='button button1' type='submit' name='userdel' value='userdel'>Delete</button> ";
        echo "<button class='button button1' type='submit' name='UserEdit' value='UserEdit'>Edit</button> ";
        echo "</div>";
        echo "</form>";
        
        if (isset($_POST["UserEdit"])) { // EDit user block
            FN::errorLog("Inside Edit user");
            $league   = $role = "";
            $username = $_POST["username"];
            $role     = $_POST["role" . $username];
            $league   = $_POST["league" . $username];
            
            $query2  = "SELECT * FROM server_roles where name= :name";
            $getRole = $db2->getResult($query2, array(
                'name' => $role
            ));
            $roleID  = $getRole[0]["id"];
            
            $query3    = "SELECT * FROM server_league where name= :name";
            $getleague = $db2->getResult($query3, array(
                'name' => $league
            ));
            $leagueID  = $getleague[0]["id"];
            
            // query to update role, league for the selected username
            $query = "UPDATE server_user SET role = :role, league= :league Where username = :username";
            
            $db2->insertResult($query, array(
                'role' => $roleID,
                'league' => $leagueID,
                'username' => $username
            ));
            FN::errorLog("Updated User table");
            header("Location: admin.php");
            
            
        }
        
        //Form to add user from the UI
        echo '
        <div id="frmUser">
        <h5>Add user</h5>
                <form action="admin.php" method="post">
                    <p>
                        <label class="col-25">Username:</label>
                        <input type="text" id="username" name="username" />
                    </p>
                     <p>
                <label class="col-25">Password:</label>
                <input type="password" id="password" name="password" />
                </p>
                <p>
                <label class="col-25">Confirm Password:</label>
                <input type="password" id="confirm" name="confirm" />
                </p>
                 <p>
                        Role: <select name="role">
                <option value="Team Manager">Team Manager</option>
                <option value="Coach">Coach</option>
                <option value="Parent">Parent</option>
                </select>
                    </p>
                    League: <select name="league">
                <option value="IPL">IPL</option>
                </select>
                    <p>
                        <input type="submit" id="btn" name="submitAddUser" value="submit"/>
                    </p>

                </form>    
    </div>  
    ';
        
        if (($_SERVER["REQUEST_METHOD"] == "POST") && isset($_POST["userdel"])) { //uder delete block
            
            FN::errorLog("Inside delete User");
            $username = $_POST["username"];
            
            //query to delete the user
            $query = "DELETE FROM server_user WHERE username =:username";
            $db2->insertResult($query, array(
                'username' => $username
            ));
            
            FN::errorLog("deleted User" . $username);
            header("Location: admin.php");
            
        }
        if (($_SERVER["REQUEST_METHOD"] == "POST") && isset($_POST['submitAddUser'])) { //insert user block
            $username    = $password = $confirm = "";
            $errusername = $errpassword = $errconfirm = "";
            
            // Input validation and sanitization
            FN::errorLog("Inside Insesrt User block");
            if (!empty($_POST["username"])) {
                $username = FN::is_valid($_POST["username"]);
            } else {
                $errusername = "Error in username";
                FN::errorLog($errusername);
            }
            $role   = $_POST["role"];
            $league = $_POST["league"];
            if (!empty($_POST["username"])) {
                $password = $_POST["password"];
            } else {
                $errpassword = "Error in password";
                FN::errorLog($password);
            }
            if (!empty($_POST["username"])) {
                $confirm = $_POST["confirm"];
            } else {
                $errconfirm = "Error in confirm password";
            }
            
            //condition to check if there's any error the input  
            if (!empty($errusername) || !empty($errpassword) || !empty($errconfirm)) {
                echo '<div id="errorHandle">
                              <p id="line">' . $errusername . '</p>
                              <p id="line">' . $errpassword . '</p>
                              <p id="line">' . $errconfirm . '</p>
                        </div>';
                
            } else {
                $query2   = "SELECT * FROM server_roles where name= :name";
                $getRole  = $db2->getResult($query2, array(
                    'name' => $role
                ));
                $roleID   = $getRole[0]["id"];
                $leagueId = $_SESSION['league'];
                
                // check if the password and confirm password matches
                if ($password == $confirm) {
                    
                    FN::errorLog("confirmed Password");
                    $hashed_password = password_hash($password, PASSWORD_DEFAULT);
                    
                    // query to insert the new user
                    $query_adduser_tm = "INSERT INTO server_user(username, role, password,team,league)
        VAlUES(:username,:role,:password,:team,:league)";
                    
                    $result_add_user = $db2->insertResult($query_adduser_tm, array(
                        'username' => $username,
                        'role' => $roleID,
                        'password' => $hashed_password,
                        'team' => $teamID,
                        'league' => $leagueId
                    ));
                    FN::errorLog("Created user");
                    header("Location: admin.php?");
                    
                }
            }
        }
        
        if ($roles == 2 || $roles == 1) { // if the user is league manager of Admin
            
            
            FN::errorLog("You are a league manager or Admin");
            
            //query to fetch the season details
            $query         = "select id,year, description as season FROM server_season";
            $result_season = $db2->getResult($query);
            
            if (($_SERVER["REQUEST_METHOD"] == "POST") && isset($_POST['submitSeason'])) {
                $id      = $year = $season = "";
                $erryear = $errseason = "";
                
                //Input validation and sanitization
                if (!empty($_POST["year"]) && is_numeric($_POST["year"])) {
                    $year = FN::is_valid($_POST["year"]);
                } else {
                    $erryear = "Error in year";
                    FN::errorLog($erryear);
                }
                if (!empty($_POST["season"])) {
                    $season = FN::is_valid($_POST["season"]);
                } else {
                    $errseason = "Error in season";
                    FN::errorLog($errseason);
                }
                
                // condition to check if there's no error in the input     
                if (!empty($erryear) || !empty($errseason)) {
                    echo '<div id="errorHandle">
                              <p id="line">' . $erryear . '</p>
                              <p id="line">' . $errseason . '</p>
                        </div>';
                    
                } else { // if no errors then execute the below query
                    
                    // query to insert the new season
                    $query = "INSERT INTO server_season(year,description)
        VAlUES(:year,:description)";
                    
                    $db2->insertResult($query, array(
                        'year' => $year,
                        'description' => $season
                    ));
                    
                    FN::errorLog("Inserted a record in server_season table");
                    header("Location: admin.php?");
                }
            }
            
            foreach ($result_season as &$record) {
                $seasonId    = $record["id"];
                $year        = $record["year"];
                $description = $record["season"];
                
                $record["id"]     = "<label><input type='radio' name='id' value=$seasonId>$seasonId</label>";
                /* $record["year"]   = "<input type='text' name = 'year$seasonId' value=$year>";*/
                $record["season"] = "<input type='text' name = 'description$seasonId' value=$description>";
                
            } //end of for each loop
            
            echo "<form action=" . $_SERVER['PHP_SELF'] . " method='post'>";
            echo "<input type='hidden' name='seasonId' value=$seasonId>";
            /* echo "List of Seasons";*/
            // Build and displays the table for the result
            echo "<div class='tabuser'><h4>List of Seasons</h4>" . FN::build_table($result_season) . "<br>";
            echo "<button class='button button1' type='submit' name='delseason' value='delseason'>Delete</button> ";
            echo "<button class='button button1' type='submit' name='Editseason' value='Editseason'>Edit</button> ";
            echo "</div>";
            echo "</form>";
            
            //form to add the new season 
            echo '
                <div id="frmSeason">
                <h5>Add Season</h5>
                    <form action="admin.php" method="post">
                        <p>
                            <label class="col-25">Year:</label>
                            <input type="text" id="year" name="year" />
                        </p>
                        <p>
                            <label class="col-25">Season:</label>
                            <input type="text" id="season" name="season" />
                        </p>
                        

                        <input type="submit" id="btn" name="submitSeason" value="submit"/>
                        </p>

                    </form>    
                </div>  
            ';
            
            if (($_SERVER["REQUEST_METHOD"] == "POST") && isset($_POST["delseason"])) {
                //block to delete the season
                
                FN::errorLog("Inside delete Season block");
                $seasonId = $_POST["id"];
                
                //query to delete the season by id
                $query = "DELETE FROM server_season WHERE id = :id";
                
                $db2->insertResult($query, array(
                    'id' => $seasonId
                ));
                FN::errorLog("deleted a Season");
                header("Location: admin.php");
                
            }
            if (($_SERVER["REQUEST_METHOD"] == "POST") && isset($_POST["Editseason"])) {
                // block of edit season detials
                FN::errorLog("Inside edit season block");
                $seasonId = $_POST["id"];
                $year     = $_POST["year" . $seasonId];
                $season   = $_POST["description" . $seasonId];
                //query to update the season
                $query    = "UPDATE server_season SET description= :season Where id = :id";
                
                $db2->insertResult($query, array(
                    
                    'season' => $season,
                    'id' => $seasonId
                ));
                FN::errorLog("updated Season table");
                header("Location: admin.php");
                
            }
            $leagueId = $_SESSION['league'];
            
            //query to fetch sport,league and season details from the server_slseason table
            $query_sls = "SELECT CONCAT(sport,'.',season,'.',league) as id,server_sport.name as sport,server_league.name as league,server_season.description as season FROM server_slseason,server_sport,server_league,server_season
            where server_slseason.sport=server_sport.id
            AND server_slseason.league=server_league.id
            AND server_slseason.season=server_season.id";
            
            $result_sportLeagueSession = $db2->getResult($query_sls);
            
            if (($_SERVER["REQUEST_METHOD"] == "POST") && isset($_POST['submitSLS'])) {
                $year     = $sport = $league = $season = "";
                $err_year = $err_sport = $err_league = $err_season = "";
                FN::errorLog("Inside server_slseason block");
                
                // Input validation and sanitization
                if (!empty($_POST["year"]) && is_numeric($_POST["year"])) {
                    $year = FN::is_valid($_POST["year"]);
                } else {
                    $err_year = "Error in year";
                    FN::errorLog($err_year);
                }
                if (!empty($_POST["sport"])) {
                    $sport = FN::is_valid($_POST["sport"]);
                } else {
                    $err_sport = "Error in sport";
                    FN::errorLog($err_sport);
                }
                if (!empty($_POST["league"])) {
                    $league = FN::is_valid($_POST["league"]);
                } else {
                    $err_league = "Error in league";
                    FN::errorLog($err_league);
                }
                if (!empty($_POST["season"])) {
                    $season = FN::is_valid($_POST["season"]);
                } else {
                    $err_season = "Error in season";
                    FN::errorLog($err_season);
                }
                // show error to user if there's anything wrong in the input data    
                if (!empty($err_year) || !empty($err_sport) || !empty($err_league) || !empty($err_season)) {
                    echo '<div id="errorHandle">
                              <p id="line">' . $err_year . '</p>
                              <p id="line">' . $err_sport . '</p>
                              <p id="line">' . $err_league . '</p>
                              <p id="line">' . $err_season . '</p>
                        </div>';
                    
                } else {
                    
                    $query_sport  = "INSERT INTO server_sport(name)
        VAlUES(:name)";
                    $res_sport    = $db2->getInsertId($query_sport, array(
                        'name' => $sport
                    ));
                    $query_league = "INSERT INTO server_league(name)
        VAlUES(:name)";
                    $res_league   = $db2->getInsertId($query_league, array(
                        'name' => $league
                    ));
                    $query_season = "INSERT INTO server_season(year,description)
        VAlUES(:year,:description)";
                    $res_season   = $db2->getInsertId($query_season, array(
                        'year' => $year,
                        'description' => $season
                    ));
                    
                    //query to insert the sport, league and season
                    $query   = "INSERT INTO server_slseason(sport, league, season)
        VAlUES(:sport,:league,:season)";
                    // execute the query
                    $res_sls = $db2->insertResult($query, array(
                        'sport' => $res_sport,
                        'league' => $res_league,
                        'season' => $res_season
                    ));
                    
                    FN::errorLog("Inserted a record in server_slseason table");
                    header("Location: admin.php");
                }
            }
            
            foreach ($result_sportLeagueSession as &$record) {
                $sportId  = $record["sport"];
                $leagueId = $record["league"];
                $seasonId = $record["season"];
                $id       = $record["id"];
                $id2      = $record["id"];
                
                list($a, $b, $c) = explode('.', $id2);
                $id1 = $a . $b . $c;
                
                $record["id"]     = "<label><input type='radio' name='id' value=$id>$id1</label>";
                $record["sport"]  = "<input type='text' name = 'sport$id1' value='$sportId'>";
                $record["league"] = "<input type='text' name = 'league$id1' value='$leagueId'>";
                $record["season"] = "<input type='text' name = 'season$id1' value='$seasonId'>";
                
                
            } // end of for each loop
            
            echo "<form action=" . $_SERVER['PHP_SELF'] . " method='post'>";
            // Build and displays the table
            echo "<div class='tabsls'><h5>Add Sport,League & Season</h5>" . FN::build_table($result_sportLeagueSession) . "<br>";
            echo "<button class='button button1' type='submit' name='delsls' value='delsls'>Delete</button> ";
            echo "<button class='button button1' type='submit' name='Editsls' value='Editsls'>Edit</button> ";
            echo "</div>";
            echo "</form>";
            echo "<br>";
            // form to add sport, league and season from the UI
            echo '
                <div id="frm12">
                <h5>Add Sport,League & Season</h5>
                    <form action="admin.php" method="post">
                        <p>
                            <label class="col-25">Sport:</label>
                            <input type="text" id="sport" name="sport" />
                        </p>
                        <p>
                            <label class="col-25">League:</label>
                            <input type="text" id="league" name="league" />
                        </p>
                           <p>
                            <label class="col-25">Season:</label>
                            <input type="text" id="season" name="season" />
                        </p>
                        <p>
                            <label class="col-25">Year:</label>
                            <input type="text" id="year" name="year" />
                        </p>

                        <input type="submit" id="btn" name="submitSLS" value="submit"/>
                        </p>

                    </form>    
                </div>  
            ';
            echo "<br><br>";
            
            //Edit sportleagueSeason
            if (($_SERVER["REQUEST_METHOD"] == "POST") && isset($_POST["Editsls"])) {
                
                FN::errorLog("Inside edit season block");
                
                $id = $_POST["id"];
                
                list($a, $b, $c) = explode('.', $id);
                $id1      = $a . $b . $c;
                $sport    = $_POST["sport" . $id1];
                $league   = $_POST["league" . $id1];
                $season   = $_POST["season" . $id1];
                $sportId  = $a;
                $seasonId = $b;
                $leagueId = $c;
                
                $query_sp = "UPDATE server_sport SET name = :name Where id = :id";
                $db2->insertResult($query_sp, array(
                    'name' => $sport,
                    'id' => $sportId
                ));
                
                $query_season = "UPDATE server_season SET description = :description Where id = :id";
                $db2->insertResult($query_season, array(
                    'description' => $season,
                    'id' => $seasonId
                ));
                
                // query to update the details in server_league table
                $query_league = "UPDATE server_league SET name = :name Where id = :id";
                $db2->insertResult($query_league, array(
                    'name' => $league,
                    'id' => $leagueId
                ));
                FN::errorLog("Updated table");
                header("Location: admin.php");
                
            }
            
            if (($_SERVER["REQUEST_METHOD"] == "POST") && isset($_POST["delsls"])) {
                // delete sport,league and season block
                FN::errorLog("Inside edit sls block");
                $id = $_POST["id"];
                list($a, $b, $c) = explode('.', $id);
                $id1      = $a . $b . $c;
                $sportId  = $a;
                $seasonId = $b;
                $leagueId = $c;
                //query to delete the record in server_slseason table
                $query    = "DELETE FROM server_slseason Where sport = :sport AND league=:league AND season=:season";
                
                $db2->insertResult($query, array(
                    'sport' => $sportId,
                    'league' => $leagueId,
                    'season' => $seasonId
                    
                ));
                FN::errorLog("deleted record from server_slseason");
                header("Location: admin.php");
                
            }
            
            
            echo "<br>";
            $leagId = $_SESSION['league'];
            
            //Query for fetching team in a league   
            $query_tl = "select server_team.id as id,server_team.name as team,server_team.mascot as mascot,server_team.homecolor as homecolor,server_team.awaycolor as awaycolor,server_team.maxplayers as maxplayers,server_league.name as league,server_team.picture as picture from server_team,server_league where server_team.league=server_league.id
             AND league= :league";
            
            $result_team_league = $db2->getResult($query_tl, array(
                'league' => $leagId
            ));
            
            foreach ($result_team_league as &$record) {
                $teamId     = $record["id"];
                $team       = $record["team"];
                $mascot     = $record["mascot"];
                $homecolor  = $record["homecolor"];
                $awaycolor  = $record["awaycolor"];
                $maxplayers = $record["maxplayers"];
                $league     = $record["league"];
                $picture    = $record["picture"];
                
                $record["id"]         = "<label><input type='radio' name='leagueTeam' value=$teamId>$teamId</label>";
                $record["team"]       = "<input type='text' name = 'team$teamId' value='$team'>";
                $record["mascot"]     = "<input type='text' name = 'mascot$teamId' value='$mascot'>";
                $record["homecolor"]  = "<input type='text' name = 'homecolor$teamId' value='$homecolor'>";
                $record["awaycolor"]  = "<input type='text' name = 'awaycolor$teamId' value=$awaycolor>";
                $record["maxplayers"] = "<input type='text' name = 'maxplayers$teamId' value=$maxplayers>";
                $record["picture"]    = "<img src='$picture'>";
                
                
            } //end of for each loop
            
            echo "<form action=" . $_SERVER['PHP_SELF'] . " method='post'>";
            echo "<div class='tabPlayer'><h5>List of Team</h5>" . FN::build_table($result_team_league) . "<br>";
            echo "<button class='button button1' type='submit' name='delTeam' value='delTeam'>Delete</button> ";
            echo "<button class='button button1' type='submit' name='EditTeam' value='EditTeam'>Edit</button> ";
            echo "</div>";
            echo "</form>";
            
            
            if (($_SERVER["REQUEST_METHOD"] == "POST") && isset($_POST["delTeam"])) {
                // Block to delete a team
                FN::errorLog("Inside deleted Team block");
                $teamIdToDel = $_POST["leagueTeam"];
                
                //query to delete team using id
                $query = "DELETE FROM server_team WHERE id =:id";
                $db2->insertResult($query, array(
                    'id' => $teamIdToDel
                ));
                FN::errorLog("Entry deleted from server_team");
                header("Location: admin.php");
                
            }
            
            if (($_SERVER["REQUEST_METHOD"] == "POST") && isset($_POST["EditTeam"])) {
                //Block to edit team details
                FN::errorLog("Inside team edit block");
                $teamID = $_POST["leagueTeam"];
                
                $team       = $_POST["team" . $teamID];
                $mascot     = $_POST["mascot" . $teamID];
                $homecolor  = $_POST["homecolor" . $teamID];
                $awaycolor  = $_POST["awaycolor" . $teamID];
                $maxplayers = $_POST["maxplayers" . $teamID];
                
                $query = "UPDATE server_team SET name = :name, mascot=:mascot,homecolor=:homecolor,awaycolor=:awaycolor,maxplayers=:maxplayers Where id =:id";
                
                $db2->insertResult($query, array(
                    'name' => $team,
                    'mascot' => $mascot,
                    'homecolor' => $homecolor,
                    'awaycolor' => $awaycolor,
                    'maxplayers' => $maxplayers,
                    'id' => $teamID
                ));
                
                FN::errorLog("Updated server_team table");
                header("Location: admin.php");
                
            }
            
            // form to add a new team from the UI
            echo '
                <div id="frmTeam">
                <h5>Add Team</h5>
                    <form action="admin.php" method="post">
                        
                        <p>
                            <label class="col-25">Mascot:</label>
                            <input type="text" id="mascotName" name="mascotName" />
                        </p>
                         <p>
                            <label class="col-25">Homecolor:</label>
                            <input type="text" id="homecolor" name="homecolor" />
                        </p>
                         <p>
                            <label class="col-25">Awaycolor:</label>
                            <input type="text" id="awaycolor" name="awaycolor" />
                        </p>
                           <p>
                            <label class="col-25">Maxplayer:</label>
                            <input type="text" id="maxplayer" name="maxplayer" />
                        </p>
                        <p>
                            <label class="col-25">Team Name:</label>
                            <input type="text" id="teamName" name="teamName" />
                        </p>

                        <input type="submit" id="btn" name="submitLeagueTeam" value="submit"/>
                        </p>

                    </form>    
                </div>  
            ';
            echo "<br><br><br><br>";
            if (($_SERVER["REQUEST_METHOD"] == "POST") && isset($_POST['submitLeagueTeam'])) {
                FN::errorLog("Inside submitLeagueTeam block");
                $id          = "";
                $lId         = $_SESSION['league'];
                $tID         = $_SESSION['team'];
                $teamName    = $mascotName = $homecolor = $awaycolor = $maxplayer = "";
                $errteamName = $errmascotName = $errhomecolor = $errawaycolor = $errmaxplayer = "";
                
                // Input validation and sanitization
                if (!empty($_POST["teamName"])) {
                    $teamName = FN::is_valid($_POST["teamName"]);
                } else {
                    $errteamName = "Error in team Name";
                    FN::errorLog($errteamName);
                }
                if (!empty($_POST["mascotName"])) {
                    $mascotName = FN::is_valid($_POST["mascotName"]);
                } else {
                    $errmascotName = "Error in mascot Name";
                    FN::errorLog($errmascotName);
                }
                if (!empty($_POST["homecolor"])) {
                    $homecolor = FN::is_valid($_POST["homecolor"]);
                } else {
                    $errhomecolor = "Error in homecolor";
                    FN::errorLog($errhomecolor);
                }
                if (!empty($_POST["awaycolor"])) {
                    $awaycolor = FN::is_valid($_POST["awaycolor"]);
                } else {
                    $errawaycolor = "Error in awaycolor";
                    FN::errorLog($errawaycolor);
                }
                if (!empty($_POST["maxplayer"]) && is_numeric($_POST["maxplayer"])) {
                    $maxplayer = FN::is_valid($_POST["maxplayer"]);
                } else {
                    $errmaxplayer = "Error in maxplayer";
                    FN::errorLog($errmaxplayer);
                }
                //check for the error  while adding a new team    
                if (!empty($errteamName) || !empty($errmascotName) || !empty($errhomecolor) || !empty($errawaycolor) || !empty($errmaxplayer)) {
                    echo '<div id="errorHandle">
                              <p id="line">' . $errteamName . '</p>
                              <p id="line">' . $errmascotName . '</p>
                              <p id="line">' . $errhomecolor . '</p>
                              <p id="line">' . $errawaycolor . '</p>
                               <p id="line">' . $errmaxplayer . '</p>
                        </div>';
                    
                } else { // if no error, then execute the below query
                    
                    $query    = "select sport,league,season from server_team where league=:league AND id=:id";
                    $res_team = $db2->getResult($query, array(
                        'league' => $lId,
                        'id' => $tID
                    ));
                    
                    $spotID       = $res_team[0]["sport"];
                    $leagueID     = $res_team[0]["league"];
                    $seasonID     = $res_team[0]["season"];
                    $picture      = "img/hyd1.png";
                    //query to add a new team in the DB  
                    $query_insert = "INSERT INTO server_team(name,mascot,sport,league, season,picture,homecolor,awaycolor, maxplayers)
        VAlUES(:name,:mascot,:sport,:league,:season,:picture,:homecolor,:awaycolor,:maxplayers)";
                    
                    $res_team = $db2->insertResult($query_insert, array(
                        'name' => $teamName,
                        'mascot' => $mascotName,
                        'sport' => $spotID,
                        'league' => $leagueID,
                        'season' => $seasonID,
                        'picture' => $picture,
                        'homecolor' => $homecolor,
                        'awaycolor' => $awaycolor,
                        'maxplayers' => $maxplayer
                    ));
                    FN::errorLog("Entry inserted in server_team");
                    header("Location: admin.php");
                }
                
            }
            
            $league_ID = $_SESSION['league'];
            
            // query to fetch schedule details 
            $query_schedule = "SELECT CONCAT(server_schedule.sport,'.',server_schedule.season,'.',server_schedule.league,'.',server_schedule.hometeam,'.',server_schedule.awayteam)as id, server_sport.name as sport,server_league.name as league,server_season.description as season,server_team.name as hometeam,(select name from server_team where id= server_schedule.awayteam) as awayteam,homescore,awayscore,scheduled,completed
            FROM server_schedule,server_team, server_sport, server_league,server_season
            WHERE server_schedule.sport=server_sport.id
            AND server_schedule.league=server_league.id
            AND server_schedule.hometeam=server_team.id
            AND server_schedule.season=server_season.id
            AND server_schedule.league=:league";
            
            $result_get_league_schedule = $db2->getResult($query_schedule, array(
                'league' => $league_ID
            ));
            
            foreach ($result_get_league_schedule as &$record) {
                $teamMgr   = $champL = $coach = $ipl = "";
                $csk       = $mi = $rcb = $kkr = $cskA = $rcbA = $miA = $kkrA = $rm = $fcb = $lp = $ch = "";
                $username  = $record["sport"];
                $league    = $record["league"];
                $season    = $record["season"];
                $hometeam  = $record["hometeam"];
                $awayteam  = $record["awayteam"];
                $homescore = $record["homescore"];
                $awayscore = $record["awayscore"];
                $scheduled = $record["scheduled"];
                $completed = $record["completed"];
                $id        = $record["id"];
                $id2       = $record["id"];
                
                list($a, $b, $c, $d, $e) = explode('.', $id2);
                $id1 = $a . $b . $c . $d . $e;
                
                if ($league == "IPL") {
                    $ipl = 'selected';
                } else {
                    $champL = 'selected';
                }
                
                if ($hometeam == "RCB") {
                    $rcb = 'selected';
                } elseif ($hometeam == "KKR") {
                    $kkr = 'selected';
                } elseif ($hometeam == "Mumbai Indians") {
                    $mi = 'selected';
                } elseif ($hometeam == "CSK") {
                    $csk = 'selected';
                }
                
                if ($awayteam == "RCB") {
                    $rcbA = 'selected';
                } elseif ($awayteam == "KKR") {
                    $kkrA = 'selected';
                } elseif ($awayteam == "Mumbai Indians") {
                    $miA = 'selected';
                } elseif ($awayteam == "CSK") {
                    $cskA = 'selected';
                }
                
                $record["id"] = "<label><input type='radio' name='id' value=$id>$id1</label>";
                
                $record["league"] = "<select name='league" . $id1 . "'>
                <option value='IPL' " . $ipl . ">IPL</option>
             
             </selected>";
                
                $record["hometeam"] = "<select name='hometeam" . $id1 . "'>
              <option value='RCB' " . $rcb . ">RCB</option>
               <option value='KKR' " . $kkr . ">KKR</option>
               <option value='Mumbai Indians' " . $mi . ">Mumbai Indians</option>
               <option value='CSK' " . $csk . ">CSK</option>
             
                </selected>";
                $record["awayteam"] = "<select name='awayteam" . $id1 . "'>
                <option value='RCB' " . $rcbA . ">RCB</option>
                <option value='KKR' " . $kkrA . ">KKR</option>
                <option value='Mumbai Indians' " . $miA . ">Mumbai Indians</option>
                <option value='CSK' " . $cskA . ">CSK</option>
                </selected>";
                
                $record["homescore"] = "<input type='text' name = 'homescore$id1' value='$homescore'>";
                $record["awayscore"] = "<input type='text' name = 'awayscore$id1' value='$awayscore'>";
                $record["scheduled"] = "<input type='text' name = 'scheduled$id1' value='$scheduled'>";
                $record["completed"] = "<input type='text' name = 'completed$id1' value='$completed'>";
                
            } //end of for loop
            
            echo "<form action=" . $_SERVER['PHP_SELF'] . " method='post'>";
            // Build and displays the result
            echo "<div class='tabSchedule'><h4>Schedule</h4>" . FN::build_table($result_get_league_schedule) . "<br>";
            echo "<button class='button button1' type='submit' name='leagueDelSchedule' value='leagueDelSchedule'>Delete</button> ";
            echo "<button class='button button1' type='submit' name='leagueEditSchedule' value='leagueEditSchedule'>Edit</button> ";
            echo "</div>";
            echo "</form>";
            echo "<br>";
            //Edit sportleagueSeason
            if (($_SERVER["REQUEST_METHOD"] == "POST") && isset($_POST["leagueEditSchedule"])) {
                
                FN::errorLog("Inside edit Schedule block");
                
                $id = $_POST["id"];
                
                list($a, $b, $c, $d, $e) = explode('.', $id);
                $id1 = $a . $b . $c . $d . $e;
                
                $scheduled        = $_POST["scheduled" . $id1];
                $completed        = $_POST["completed" . $id1];
                $homeTeamSelected = $_POST["hometeam" . $id1];
                $awayTeamSelected = $_POST["awayteam" . $id1];
                $homescore        = $_POST["homescore" . $id1];
                $awayscore        = $_POST["awayscore" . $id1];
                $sportId          = $a;
                $seasonId         = $b;
                $leagueId         = $c;
                $homeTeamId       = $d;
                $awayTeamId       = $e;
                
                $query4             = "SELECT * FROM server_team where name= :name";
                $getTeam            = $db2->getResult($query4, array(
                    'name' => $homeTeamSelected
                ));
                $homeTeamSelectedID = $getTeam[0]["id"];
                
                $query5             = "SELECT * FROM server_team where name= :name";
                $getTeamAway        = $db2->getResult($query5, array(
                    'name' => $awayTeamSelected
                ));
                $awayTeamSelectedID = $getTeamAway[0]["id"];
                
                //query to update team details        
                $query = "UPDATE server_schedule SET hometeam = :hometeam, awayteam= :awayteam,homescore=:homescore,awayscore=:awayscore,scheduled=:scheduled,completed=:completed Where sport = :sport AND league=:league AND season=:season AND hometeam=:hometeamID AND awayteam=:awayteamID";
                
                $db2->insertResult($query, array(
                    'hometeam' => $homeTeamSelectedID,
                    'awayteam' => $awayTeamSelectedID,
                    'homescore' => $homescore,
                    'awayscore' => $awayscore,
                    'scheduled' => $scheduled,
                    'completed' => $completed,
                    'sport' => $sportId,
                    'league' => $leagueId,
                    'season' => $seasonId,
                    'hometeamID' => $homeTeamId,
                    'awayteamID' => $awayTeamId
                ));
                FN::errorLog("Updated server_schedule table");
                header("Location: admin.php");
                
                
            }
            // BLock to delete a schedule from the table
            if (($_SERVER["REQUEST_METHOD"] == "POST") && isset($_POST["leagueDelSchedule"])) {
                
                FN::errorLog("Inside delete Schedule");
                
                $id = $_POST["id"];
                list($a, $b, $c, $d, $e) = explode('.', $id);
                $id1        = $a . $b . $c . $d . $e;
                $sportId    = $a;
                $seasonId   = $b;
                $leagueId   = $c;
                $homeTeamId = $d;
                $awayTeamId = $e;
                //query to delete a schedule
                $query      = "DELETE FROM server_schedule Where sport = :sport AND league=:league AND season=:season AND hometeam=:hometeam AND awayteam=:awayteam";
                
                $db2->insertResult($query, array(
                    'sport' => $sportId,
                    'league' => $leagueId,
                    'season' => $seasonId,
                    'hometeam' => $homeTeamId,
                    'awayteam' => $awayTeamId
                    
                ));
                FN::errorLog("deleted entry from server_schedule");
                header("Location: admin.php");
                
            }
            // form to add a new schedule from the UI 
            echo '
          <div id="frmSchedule">
          <h5>Add new Schedule</h5>
                <form action="admin.php" method="post">
                    <p>
                        <label>Sport:</label>
                <select name="sport">
                <option value="Cricket">Cricket</option>
                </select>
                    </p>
                    <p>
                        <label>League:</label>
                <select name="league">
                 <option value="IPL">IPL</option>
                </select>
                    </p>
                    <p>
                        <label>Season:</label>
                <select name="season">
                 <option value="Season11">Season11</option>
                </select>
                    </p>
                         <p>
                        Home Team: <select name="hometeam">
                <option value="RCB">RCB</option>
                <option value="KKR">KKR</option>
                <option value="Mumbai Indians">Mumbai Indians</option>
                <option value="CSK">CSK</option>
                </select>
                    </p>
                             <p>
                        Away Team: <select name="awayteam">
                <option value="RCB">RCB</option>
                <option value="KKR">KKR</option>
                <option value="Mumbai Indians">Mumbai Indians</option>
                <option value="CSK">CSK</option>
                </select>
                       
                    </p>
                     <p>
                            <label >Home Score:</label>
                            <input type="text" id="homescore" name="homescore" />
                        </p>
                     <p>
                            <label >Away Score:</label>
                            <input type="text" id="awayscore" name="awayscore" />
                        </p>
                    <p>
                            <label >Schedule:</label>
                            <input type="text" id="schedule" name="schedule" />
                        </p>  
                         <p>
                        <label>Completed:</label>
                <select name="completed">
                 <option value="0">0</option>
                 <option value="1">1</option>
                </select>
                    </p>
                    <p>
                        <input type="submit" id="btn" name="submitAddSchedule" value="submit"/>
                    </p>

                </form>    
    </div>  
    ';
            
            //BLock to insert the details provided in the above form
            if (($_SERVER["REQUEST_METHOD"] == "POST") && isset($_POST["submitAddSchedule"])) {
                
                FN::errorLog("Inside Add Schedule block");
                $homescore    = $awayscore = $schedule = "";
                $errhomescore = $errawayscore = $errschedule = "";
                
                $sport    = FN::is_valid($_POST["sport"]);
                $league   = FN::is_valid($_POST["league"]);
                $season   = FN::is_valid($_POST["season"]);
                $hometeam = FN::is_valid($_POST["hometeam"]);
                $awayteam = FN::is_valid($_POST["awayteam"]);
                
                //perform the input validation and sanitization
                if (!empty($_POST["homescore"]) && is_numeric($_POST["homescore"])) {
                    $homescore = FN::is_valid($_POST["homescore"]);
                } else {
                    $errhomescore = "Error in homescore";
                    FN::errorLog($errhomescore);
                }
                if (!empty($_POST["awayscore"]) && is_numeric($_POST["awayscore"])) {
                    $awayscore = FN::is_valid($_POST["awayscore"]);
                } else {
                    $errawayscore = "Error in awayscore";
                    FN::errorLog($errawayscore);
                }
                if (!empty($_POST["schedule"])) {
                    $schedule = FN::is_valid($_POST["schedule"]);
                } else {
                    $errschedule = "Error in schedule";
                    FN::errorLog($errschedule);
                }
                
                $completed_schedule = FN::is_valid($_POST["completed"]);
                
                // Display the error for the incorrect or no input
                if (!empty($errhomescore) || !empty($errawayscore) || !empty($errschedule)) {
                    echo '<div id="errorHandle">
                              <p id="line">' . $errhomescore . '</p>
                              <p id="line">' . $errawayscore . '</p>
                              <p id="line">' . $errschedule . '</p>
                              
                        </div>';
                    
                } else {
                    $query1   = "SELECT * FROM server_sport where name= :name";
                    $getsport = $db2->getResult($query1, array(
                        'name' => $sport
                    ));
                    $sportID  = $getsport[0]["id"];
                    
                    $query2    = "SELECT * FROM server_season where description= :description";
                    $getseason = $db2->getResult($query2, array(
                        'description' => $season
                    ));
                    $seasonID  = $getseason[0]["id"];
                    
                    $query3    = "SELECT * FROM server_league where name= :name";
                    $getleague = $db2->getResult($query3, array(
                        'name' => $league
                    ));
                    $leagueID  = $getleague[0]["id"];
                    
                    $query4             = "SELECT * FROM server_team where name= :name";
                    $getTeam            = $db2->getResult($query4, array(
                        'name' => $hometeam
                    ));
                    $homeTeamSelectedID = $getTeam[0]["id"];
                    
                    $query5             = "SELECT * FROM server_team where name= :name";
                    $getTeamAway        = $db2->getResult($query5, array(
                        'name' => $awayteam
                    ));
                    $awayTeamSelectedID = $getTeamAway[0]["id"];
                    
                    // query to insert a new schedule in server_schedule table     
                    $query = "INSERT INTO server_schedule(sport, league, season,hometeam,awayteam,homescore,awayscore,scheduled,completed)
        VAlUES(:sport,:league,:season,:hometeam,:awayteam,:homescore,:awayscore,:scheduled,:completed)";
                    
                    $db2->insertResult($query, array(
                        'sport' => $sportID,
                        'league' => $leagueID,
                        'season' => $seasonID,
                        'hometeam' => $homeTeamSelectedID,
                        'awayteam' => $awayTeamSelectedID,
                        'homescore' => $homescore,
                        'awayscore' => $awayscore,
                        'scheduled' => $schedule,
                        'completed' => (int) $completed_schedule
                        
                    ));
                    FN::errorLog("Inserted a record into server_schedule table");
                    header("Location: admin.php?");
                }
            }
            
            
            //query to Add user by League Manager
            $query_user = "select username,server_roles.name as role,server_team.name as team,server_league.name as league from server_user,server_team,server_roles,server_league where 
            server_user.team=server_team.id
            AND server_user.league=server_league.id
            AND server_user.role=server_roles.id
            AND role IN (3,4)";
            
            $result_getUser = $db2->getResult($query_user);
            
            //block to insert a new user
            if (($_SERVER["REQUEST_METHOD"] == "POST") && isset($_POST['submitAddUserLeague'])) {
                
                
                FN::errorLog("Inside add user by league Manager block");
                $user    = $password = $confirm = "";
                $erruser = $errpassword = $errconfirm = "";
                
                //perform input validation and sanitization
                if (!empty($_POST["user"])) {
                    $user = FN::is_valid($_POST["user"]);
                } else {
                    $erruser = "Error in username";
                    FN::errorLog($erruser);
                }
                $roleAcess    = FN::is_valid($_POST["roleAcess"]);
                $selectTeam   = FN::is_valid($_POST["selectTeam"]);
                $leagueAssign = FN::is_valid($_POST["leagueAssign"]);
                
                if (!empty($_POST["pass"])) {
                    $password = FN::is_valid($_POST["pass"]);
                } else {
                    $errpassword = "Error in password";
                    FN::errorLog($errpassword);
                }
                if (!empty($_POST["confm"])) {
                    $confirm = FN::is_valid($_POST["confm"]);
                } else {
                    $errconfirm = "Error in confirm password";
                    FN::errorLog($errconfirm);
                }
                
                //display the error if there's any incorrect input provided    
                if (!empty($erruser) || !empty($errpassword) || !empty($errconfirm)) {
                    echo '<div id="errorHandle">
                              <p id="line">' . $erruser . '</p>
                              <p id="line">' . $errpassword . '</p>
                              <p id="line">' . $errconfirm . '</p>
                              
                        </div>';
                    
                } else {
                    $query2  = "SELECT * FROM server_roles where name= :name";
                    $getRole = $db2->getResult($query2, array(
                        'name' => $roleAcess
                    ));
                    $roleID  = $getRole[0]["id"];
                    
                    $query3    = "SELECT * FROM server_league where name= :name";
                    $getleague = $db2->getResult($query3, array(
                        'name' => $leagueAssign
                    ));
                    $leagueID  = $getleague[0]["id"];
                    
                    $query4       = "SELECT * FROM server_team where name= :name";
                    $getTeam      = $db2->getResult($query4, array(
                        'name' => $selectTeam
                    ));
                    $selectTeamID = $getTeam[0]["id"];
                    
                    //re check the password and confirm password is same
                    if ($password == $confirm) {
                        
                        //hash the password before storing in DB
                        $hashed_password = password_hash($password, PASSWORD_DEFAULT);
                        FN::errorLog("confirm password");
                        
                        //query to insert a user in server_user table
                        $query_adduser_tm = "INSERT INTO server_user(username, role, password,team,league)
        VAlUES(:username,:role,:password,:team,:league)";
                        
                        $result_add_user = $db2->insertResult($query_adduser_tm, array(
                            'username' => $user,
                            'role' => $roleID,
                            'password' => $hashed_password,
                            'team' => $selectTeamID,
                            'league' => $leagueID
                        ));
                        FN::errorLog("Inserted a new user");
                        header("Location: admin.php?");
                        
                    }
                }
            } // end of inserting a new user
            
            foreach ($result_getUser as &$record) {
                $teamMgr  = $champL = $coach = $ipl = "";
                $csk      = $mi = $rcb = $kkr = $csk = $rm = $fcb = $lp = $ch = "";
                $username = $record["username"];
                $role     = $record["role"];
                $league   = $record["league"];
                $team     = $record["team"];
                
                if ($role == "Team Manager") {
                    $teamMgr = 'selected';
                } elseif ($role == "Coach") {
                    $coach = 'selected';
                }
                
                if ($league == "IPL") {
                    $ipl = 'selected';
                } else {
                    $champL = 'selected';
                }
                
                if ($team == "RCB") {
                    $rcb = 'selected';
                } elseif ($team == "KKR") {
                    $kkr = 'selected';
                } elseif ($team == "Mumbai Indians") {
                    $mi = 'selected';
                } elseif ($team == "CSK") {
                    $csk = 'selected';
                } elseif ($team == "Real Madrid") {
                    $rm = 'selected';
                } elseif ($team == "FCB") {
                    $fcb = 'selected';
                } elseif ($team == "Liverpool") {
                    $lp = 'selected';
                } elseif ($team == "Chelsea") {
                    $ch = 'selected';
                }
                
                $record["username"] = "<label><input type='radio' name='username' value=$username>$username</label>";
                
                $record["role"] = "<select name='role" . $username . "'>
            <option value='Team Manager' " . $teamMgr . ">Team Manager</option>
             <option value='Coach' " . $coach . ">Coach</option>
            </selected>";
                
                $record["team"] = "<select name='team" . $username . "'>
            <option value='RCB' " . $rcb . ">RCB</option>
             <option value='KKR' " . $kkr . ">KKR</option>
             <option value='Mumbai Indians' " . $mi . ">Mumbai Indians</option>
             <option value='CSK' " . $csk . ">CSK</option>
             
             <option value='Real Madrid' " . $rm . ">Real Madrid</option>
             <option value='FCB' " . $fcb . ">FCB</option>
             <option value='Liverpool' " . $lp . ">Liverpool</option>
             <option value='Chelsea' " . $ch . ">Chelsea</option>
             
            </selected>";
                
                $record["league"] = "<select name='league" . $username . "'>
        <option value='IPL' " . $ipl . ">IPL</option>
             <option value='champions league' " . $champL . ">champions league</option>
        </selected>";
            } // end of for loop
            
            echo "<form action=" . $_SERVER['PHP_SELF'] . " method='post'>";
            echo "<div class='tabuserLeague'><h5>List of User</h5>" . FN::build_table($result_getUser) . "<br>";
            echo "<button class='button button1' type='submit' name='leagueDelUser' value='leagueDelUser'>Delete</button> ";
            echo "<button class='button button1' type='submit' name='leagueEditUser' value='leagueEditUser'>Edit</button> ";
            echo "</div>";
            echo "</form>";
            echo "<br>";
            
            // Form to add a new user by league manager  
            echo '
                <div id="frmLeague">
                <h5>Add User</h5>
                        <form action="admin.php" method="post">
                            <p>
                                <label class="col-25">Username:</label>
                                <input type="text" id="user" name="user" />
                            </p>
                             <p>
                        <label class="col-25">Password:</label>
                        <input type="password" id="pass" name="pass" />
                    </p>
                    <p>
                        <label class="col-25">Confirm Password:</label>
                        <input type="password" id="confm" name="confm" />
                    </p>
                            <p>
                                Role: <select name="roleAcess">
                        <option value="Team Manager">Team Manager</option>
                        <option value="Coach">Coach</option>
                        </select>

                            </p>
                                 <p>
                                Team: <select name="selectTeam">
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
                            League: <select name="leagueAssign">
                        <option value="IPL">IPL</option>
                        <option value="champions league">champions league</option>
                        </select>
                            <p>
                            </p>
                            <p>
                                <input type="submit" id="btn" name="submitAddUserLeague" value="submit"/>
                            </p>

                        </form>    
                    </div>  
                    ';
            echo "<br><br><br>";
            //block to edit a user
            if (($_SERVER["REQUEST_METHOD"] == "POST") && isset($_POST["leagueEditUser"])) {
                
                $league = $role = "";
                FN::errorLog("inside edit of league add team");
                
                $user_name = $_POST["username"];
                $role_lm   = $_POST["role" . $user_name];
                $team_lm   = $_POST["team" . $user_name];
                $league_lm = $_POST["league" . $user_name];
                
                $query2  = "SELECT * FROM server_roles where name= :name";
                $getRole = $db2->getResult($query2, array(
                    'name' => $role_lm
                ));
                $roleID  = $getRole[0]["id"];
                
                $query3    = "SELECT * FROM server_league where name= :name";
                $getleague = $db2->getResult($query3, array(
                    'name' => $league_lm
                ));
                $leagueID  = $getleague[0]["id"];
                
                $query4       = "SELECT * FROM server_team where name= :name";
                $getTeam      = $db2->getResult($query4, array(
                    'name' => $team_lm
                ));
                $selectTeamID = $getTeam[0]["id"];
                
                // query to update the user details    
                $query = "UPDATE server_user SET role = :role, league= :league,team=:team Where username = :username";
                
                $db2->insertResult($query, array(
                    'role' => $roleID,
                    'league' => $leagueID,
                    'team' => $selectTeamID,
                    'username' => $user_name
                ));
                FN::errorLog("Updated server_user table");
                header("Location: admin.php");
                
                
                
            }
            if (($_SERVER["REQUEST_METHOD"] == "POST") && isset($_POST["leagueDelUser"])) {
                FN::errorLog("Inside delete User");
                $user_name_lm = $_POST["username"];
                
                $query = "DELETE FROM server_user WHERE username =:username";
                $db2->insertResult($query, array(
                    'username' => $user_name_lm
                ));
                FN::errorLog("delete User");
                header("Location: admin.php");
                
            }
            
            //Admin role
            if ($roles == 1) {
                
                FN::errorLog("Logged in as Admin");
                //query to fetch all the users
                $query_admin_user = "select username,server_roles.name as role,server_team.name as team,server_league.name as league from server_user,server_team,server_roles,server_league where 
                server_user.team=server_team.id
                AND server_user.league=server_league.id
                AND server_user.role=server_roles.id";
                
                //execute the result
                $result_getAdminUser = $db2->getResult($query_admin_user);
                
                foreach ($result_getAdminUser as &$record) {
                    $admin          = $lm = $teamMgr = $champL = $coach = $ipl = $parent = "";
                    $csk            = $mi = $rcb = $kkr = $csk = $rm = $fcb = $lp = $ch = "";
                    $username_admin = $record["username"];
                    $role_admin     = $record["role"];
                    $league_admin   = $record["league"];
                    $team_admin     = $record["team"];
                    
                    
                    if ($role_admin == "Admin") {
                        $admin = 'selected';
                    } elseif ($role_admin == "League Manager") {
                        $lm = 'selected';
                    } elseif ($role_admin == "Team Manager") {
                        $teamMgr = 'selected';
                    } elseif ($role_admin == "Coach") {
                        $coach = 'selected';
                    } elseif ($role_admin == "Parent") {
                        $parent = 'selected';
                    }
                    
                    if ($league_admin == "IPL") {
                        $ipl = 'selected';
                    } elseif ($league_admin == "champions league") {
                        $champL = 'selected';
                    }
                    
                    if ($team_admin == "RCB") {
                        $rcb = 'selected';
                    } elseif ($team_admin == "KKR") {
                        $kkr = 'selected';
                    } elseif ($team_admin == "Mumbai Indians") {
                        $mi = 'selected';
                    } elseif ($team_admin == "CSK") {
                        $csk = 'selected';
                    } elseif ($team_admin == "Real Madrid") {
                        $rm = 'selected';
                    } elseif ($team_admin == "FCB") {
                        $fcb = 'selected';
                    } elseif ($team_admin == "Liverpool") {
                        $lp = 'selected';
                    } elseif ($team_admin == "Chelsea") {
                        $ch = 'selected';
                    }
                    
                    $record["username"] = "<label><input type='radio' name='username' value=$username_admin>$username_admin</label>";
                    
                    $record["role"] = "<select name='role" . $username_admin . "'>
                <option value='Admin' " . $admin . ">Admin</option>
                <option value='League Manager' " . $lm . ">League Manager</option>
                <option value='Team Manager' " . $teamMgr . ">Team Manager</option>
                 <option value='Coach' " . $coach . ">Coach</option>
                 <option value='Parent' " . $parent . ">Parent</option>
                </selected>";
                    
                    $record["team"] = "<select name='team" . $username_admin . "'>
                <option value='RCB' " . $rcb . ">RCB</option>
                 <option value='KKR' " . $kkr . ">KKR</option>
                 <option value='Mumbai Indians' " . $mi . ">Mumbai Indians</option>
                 <option value='CSK' " . $csk . ">CSK</option>

                 <option value='Real Madrid' " . $rm . ">Real Madrid</option>
                 <option value='FCB' " . $fcb . ">FCB</option>
                 <option value='Liverpool' " . $lp . ">Liverpool</option>
                 <option value='Chelsea' " . $ch . ">Chelsea</option>
             
                </selected>";
                    
                    $record["league"] = "<select name='league" . $username_admin . "'>
                <option value='IPL' " . $ipl . ">IPL</option>
                     <option value='champions league' " . $champL . ">champions league</option>
                </selected>";
                } //end of for each loop
                
                echo "<form action=" . $_SERVER['PHP_SELF'] . " method='post'>";
                
                echo "<div class='tabPlayer'>" . FN::build_table($result_getAdminUser) . "<br>";
                
                echo "<button class='button button1' type='submit' name='adminDelUser' value='adminDelUser'>Delete</button> ";
                echo "<button class='button button1' type='submit' name='adminEditUser' value='adminEditUser'>Edit</button> ";
                echo "</div>";
                echo "</form>";
                
                //edit user by admin
                if (($_SERVER["REQUEST_METHOD"] == "POST") && isset($_POST["adminEditUser"])) {
                    FN::errorLog("Inside Edit Admin block");
                    $league = $role = "";
                    
                    
                    $user_name_admin = $_POST["username"];
                    $role_ad         = $_POST["role" . $user_name_admin];
                    $team_ad         = $_POST["team" . $user_name_admin];
                    $league_ad       = $_POST["league" . $user_name_admin];
                    
                    
                    $query2  = "SELECT * FROM server_roles where name= :name";
                    $getRole = $db2->getResult($query2, array(
                        'name' => $role_ad
                    ));
                    $roleID  = $getRole[0]["id"];
                    
                    $query3    = "SELECT * FROM server_league where name= :name";
                    $getleague = $db2->getResult($query3, array(
                        'name' => $league_ad
                    ));
                    $leagueID  = $getleague[0]["id"];
                    
                    $query4       = "SELECT * FROM server_team where name= :name";
                    $getTeam      = $db2->getResult($query4, array(
                        'name' => $team_ad
                    ));
                    $selectTeamID = $getTeam[0]["id"];
                    
                    $query = "UPDATE server_user SET role = :role, league= :league,team=:team Where username = :username";
                    
                    $db2->insertResult($query, array(
                        'role' => $roleID,
                        'league' => $leagueID,
                        'team' => $selectTeamID,
                        'username' => $user_name_admin
                    ));
                    FN::errorLog("updated server_user by Admin");
                    header("Location: admin.php");
                    
                } // end of edit user block by admin
                
                //Delete a user by Admin
                if (($_SERVER["REQUEST_METHOD"] == "POST") && isset($_POST["adminDelUser"])) {
                    FN::errorLog("Inside Delete user block");
                    $user_name_ad = $_POST["username"];
                    //  $db->delete_league_user($user_name_ad);
                    
                    // query to delete a user   
                    $query = "DELETE FROM server_user WHERE username =:username";
                    $db2->insertResult($query, array(
                        'username' => $user_name_ad
                    ));
                    FN::errorLog("Deleted a user");
                    header("Location: admin.php");
                    
                }
                // Form to add a new user by the admin       
                echo '
        <div id="frmAdmin">
        <h5>Add User</h5>
                <form action="admin.php" method="post">
                    <p>
                        <label>Username:</label>
                        <input type="text" id="userAdmin" name="userAdmin" />
                    </p>
                     <p>
                <label>Password:</label>
                <input type="password" id="passad" name="passad" />
            </p>
            <p>
                <label>Confirm Password:</label>
                <input type="password" id="confmad" name="confmad" />
            </p>
                    <p>
                        Role: <select name="roleAdmin">
                <option value="Admin">Admin</option>
                <option value="League Manager">League Manager</option>
                <option value="Team Manager">Team Manager</option>
                <option value="Coach">Coach</option>
                <option value="Parent">Coach</option>
                </select>
                       
                    </p>
                         <p>
                        Team: <select name="selectTeamAdmin">
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
                    League: <select name="AdminAssign">
                <option value="IPL">IPL</option>
                <option value="champions league">champions league</option>
                </select>
                    <p>
                    </p>
                    <p>

                        <input type="submit" id="btn" name="submitAddUserAdmin" value="submit"/>
                    </p>

                </form>    
    </div>  
    ';
                if (($_SERVER["REQUEST_METHOD"] == "POST") && isset($_POST['submitAddUserAdmin'])) {
                    
                    $userAdmin    = $password = $confirm = "";
                    $erruserAdmin = $errpassword = $errconfirm = "";
                    FN::errorLog("Inside add user by Admin Manager block");
                    
                    // Perform input validation and sanitization
                    if (!empty($_POST["userAdmin"])) {
                        $userAdmin = FN::is_valid($_POST["userAdmin"]);
                    } else {
                        $erruserAdmin = "Error in  username";
                        FN::errorLog($erruserAdmin);
                    }
                    $roleAdmin       = FN::is_valid($_POST["roleAdmin"]);
                    $selectTeamAdmin = FN::is_valid($_POST["selectTeamAdmin"]);
                    $AdminAssign     = FN::is_valid($_POST["AdminAssign"]);
                    if (!empty($_POST["passad"])) {
                        $password = FN::is_valid($_POST["passad"]);
                    } else {
                        $errpassword = "Error in  password";
                        FN::errorLog($errpassword);
                    }
                    if (!empty($_POST["confmad"])) {
                        $confirm = FN::is_valid($_POST["confmad"]);
                    } else {
                        $errconfirm = "Error in confirm password";
                        FN::errorLog($errconfirm);
                    }
                    // display the error for the incorrect provided the user
                    if (!empty($erruserAdmin) || !empty($errpassword) || !empty($errconfirm)) {
                        echo '<div id="errorHandle">
                              <p id="line">' . $erruserAdmin . '</p>
                              <p id="line">' . $errpassword . '</p>
                              <p id="line">' . $errconfirm . '</p>
                              
                        </div>';
                        
                    } else {
                        $query2  = "SELECT * FROM server_roles where name= :name";
                        $getRole = $db2->getResult($query2, array(
                            'name' => $roleAdmin
                        ));
                        $roleID  = $getRole[0]["id"];
                        
                        $query3    = "SELECT * FROM server_league where name= :name";
                        $getleague = $db2->getResult($query3, array(
                            'name' => $AdminAssign
                        ));
                        $leagueID  = $getleague[0]["id"];
                        
                        $query4       = "SELECT * FROM server_team where name= :name";
                        $getTeam      = $db2->getResult($query4, array(
                            'name' => $selectTeamAdmin
                        ));
                        $selectTeamID = $getTeam[0]["id"];
                        //match the password and confirm password are same
                        if ($password == $confirm) {
                            FN::errorLog("confirmed Password");
                            //Hash the password and insert in DB
                            $hashed_password = password_hash($password, PASSWORD_DEFAULT);
                            
                            $query_adduser_tm = "INSERT INTO server_user(username, role, password,team,league)
        VAlUES(:username,:role,:password,:team,:league)";
                            
                            $result_add_user = $db2->insertResult($query_adduser_tm, array(
                                'username' => $userAdmin,
                                'role' => $roleID,
                                'password' => $hashed_password,
                                'team' => $selectTeamID,
                                'league' => $leagueID
                            ));
                            FN::errorLog("Inserted user in DB");
                            header("Location: admin.php?");
                            
                        }
                    }
                } //end of add a user by admin 
                
                //query to fetch all the sport
                $query                = "select * from server_sport";
                $result_getAdminsport = $db2->getResult($query);
                
                //block to add a new sport
                if (($_SERVER["REQUEST_METHOD"] == "POST") && isset($_POST["submitAddSport"])) {
                    FN::errorLog("Inside Add sport");
                    $sport_name = $errsport_name = "";
                    
                    //Perform input validation and sanitization
                    if (!empty($_POST["sportName"])) {
                        $sport_name = FN::is_valid($_POST["sportName"]);
                    } else {
                        $errsport_name = "Error in sport Name";
                        FN::errorLog($errsport_name);
                    }
                    //check for input errors  
                    if (!empty($errsport_name)) {
                        echo '<div id="errorHandle">
                              <p id="line">' . $errsport_name . '</p>
                              
                        </div>';
                        
                    } else {
                        $query = "INSERT INTO server_sport(name)
                    VAlUES(:name)";
                        $db2->insertResult($query, array(
                            'name' => $sport_name
                        ));
                        FN::errorLog("Inserted into server_sport table");
                        header("Location: admin.php?");
                    }
                }
                foreach ($result_getAdminsport as &$record) {
                    $sportId   = $record["id"];
                    $sportName = $record["name"];
                    
                    
                    $record["id"]   = "<label><input type='radio' name='id' value=$sportId>$sportId</label>";
                    $record["name"] = "<input type='text' name = 'name$sportId' value='$sportName'>";
                    
                    
                } //end of for each loop
                
                echo "<form action=" . $_SERVER['PHP_SELF'] . " method='post'>";
                echo "<input type='hidden' name='seasonId' value=$seasonId>";
                
                echo "<div class='tabsport'><h5>Sport List</h5>" . FN::build_table($result_getAdminsport) . "<br>";
                echo "<button class='button button1' type='submit' name='delsport' value='delsport'>Delete</button> ";
                echo "<button class='button button1' type='submit' name='Editsport' value='Editsport'>Edit</button> ";
                echo "<br>";
                echo "</div>";
                echo "<br>";
                echo "</form>";
                
                //block to delete a sport
                if (($_SERVER["REQUEST_METHOD"] == "POST") && isset($_POST["delsport"])) {
                    
                    FN::errorLog("Inside delete sport block");
                    $sport_Id_admin = $_POST["id"];
                    
                    $query = "DELETE FROM server_sport WHERE id = :id";
                    
                    $db2->insertResult($query, array(
                        'id' => $sport_Id_admin
                    ));
                    FN::errorLog("deleted a sport from server_sport");
                    header("Location: admin.php");
                    
                    
                }
                //Block to edit a sport
                if (($_SERVER["REQUEST_METHOD"] == "POST") && isset($_POST["Editsport"])) {
                    
                    FN::errorLog("Inside edit season");
                    $sportId    = $_POST["id"];
                    $sport_name = $_POST["name" . $sportId];
                    
                    $query = "UPDATE server_sport SET name = :name Where id = :id";
                    
                    $db2->insertResult($query, array(
                        'name' => $sport_name,
                        'id' => $sportId
                    ));
                    FN::errorLog("Updated server_sport table");
                    header("Location: admin.php");
                    
                    
                    
                }
                //Form to add a new sport from the UI
                
                echo '
        <div id="frmSport">
            <h5>Add Sport</h5>
                <form action="admin.php" method="post">
                    
                    <p>
                        <label>Sport:</label>
                        <input  type="text" id="sportName" name="sportName" />
                    </p>
                    <p>

                        <input type="submit" id="btn" name="submitAddSport" value="submit"/>
                    </p>

                </form>    
    </div>  
    ';
                
            } //end of Admin Role scope
            
        } //end of LM and Admin
        
    } else {
        // IF the parent is trying to access the admin page, below is the message and link provided
        $teamID = $_SESSION['team'];
        echo "You don't have access to admin page";
        echo "<br>";
        echo '<a href="schedule.php?teamID=' . $teamID . '">Match Schedule</a>';
    }
    
    
} else {
    echo "You need to login" . "<br>";
    echo "<a href='login.php'>LogIn</a>";
}
?>
<!DOCTYPE HTML>
<html>
<head>
        <title>Admin Page</title>
        <link rel="stylesheet" type="text/css" href="style.css">
</head>
<body>
    
</body>
</html>  