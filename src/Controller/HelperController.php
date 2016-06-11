<?php

namespace tippTopf\src\Controller;

class HelperController {

    /**
     * @return database connection
     */
    public static function getConnection() {
        $config = include(__DIR__ . '/../../configs/local.php');
        $db = new \PDO("mysql:host=localhost;dbname=mydb", $config['db']['user'], $config['db']['password']);

        return $db;
    }

    /**
     * get id of currently logged in User
     * 
     * @return int $user_id
     */
    public static function getLoggedInUserId() {
        $db = HelperController::getConnection();
        $sql = "SELECT user.id FROM mydb.user WHERE user.username = :username";

        $stmt = $db->prepare($sql);
        $stmt->bindParam(':username', $_SESSION["username"]);
        $stmt->execute();
        $result = $stmt->fetchAll();

        return $result[0]["id"];
    }

    /**
     * get email of currently logged in user
     * 
     * @return string email
     */
    public static function getLoggedInUserMail() {
        $db = HelperController::getConnection();
        $sql = "SELECT user.email FROM mydb.user WHERE user.username = :username";

        $stmt = $db->prepare($sql);
        $stmt->bindParam(':username', $_SESSION["username"]);
        $stmt->execute();
        $result = $stmt->fetchAll();

        return $result[0]["email"];
    }
    
    /**
     * get user
     * 
     * @return array
     */
    public static function getUser($user_id) {
        $db = HelperController::getConnection();
        $sql = "SELECT * FROM mydb.user WHERE user.id = :id";

        $stmt = $db->prepare($sql);
        $stmt->bindParam(':id', $user_id);
        $stmt->execute();
        $result = $stmt->fetchAll();

        return $result[0];
    }

    /**
     * get id of group by owner
     * 
     * @return int $user_id
     */
    public static function getGroupId($user_id, $name) {
        $db = HelperController::getConnection();
        $sql = "SELECT group.id FROM mydb.group WHERE group.owner = :user_id AND group.name = :name";

        $stmt = $db->prepare($sql);
        $stmt->bindParam(':user_id', $user_id);
        $stmt->bindParam(':name', $name);
        $stmt->execute();
        $result = $stmt->fetchAll();

        return $result[0]["id"];
    }

    /**
     * get group obj
     * 
     * @return int $user_id
     */
    public static function getGroup($group_id) {
        $db = HelperController::getConnection();
        $sql = "SELECT * FROM mydb.group WHERE group.id = :group_id";

        $stmt = $db->prepare($sql);
        $stmt->bindParam(':group_id', $group_id);
        $stmt->execute();
        $result = $stmt->fetchAll();

        return $result[0];
    }
    
    /**
     * get all users belonging to a group
     * 
     * @param Int $group_id
     * @return array
     */
    public function getGroupMembers($group_id){
        $members = array();
        $db = HelperController::getConnection();
        $sql = "SELECT user_has_group.user_id FROM mydb.user_has_group WHERE user_has_group.group_id = :group_id";

        $stmt = $db->prepare($sql);
        $stmt->bindParam(':group_id', $group_id);
        $stmt->execute();
        $result = $stmt->fetchAll();
        
        foreach ($result as $user){
            $group_member = HelperController::getUser($user["user_id"]);
            $members[]["username"] = $group_member["username"];
        }
        return $members;
    }
    
    /**
     * get group name
     * 
     * @param Int $id
     * @return string
     */
    public function getTeamName($id){
        $db = HelperController::getConnection();
        $sql = "SELECT team.name FROM mydb.team WHERE team.id=:id";

        $stmt = $db->prepare($sql);
        $stmt->bindParam(':id', $id);
        $stmt->execute();
        $result = $stmt->fetch();
        
        return $result["name"];
    }
    
     /**
     * get games with team flags
     * 
     * @return array $result
     */
    public static function getGames() {
        $db = HelperController::getConnection();
        $sql = "SELECT * FROM mydb.game";

        $stmt = $db->prepare($sql);
        $stmt->execute();
        $result = $stmt->fetchAll();
        
        foreach ($result as $key => $game){
            $id_team1 = $game["team_id1"];
            $id_team2 = $game["team_id2"];
            
            //get flag for team 1
            $sql = "SELECT team.flag FROM mydb.team WHERE team.id=:id";

            $stmt = $db->prepare($sql);
            $stmt->bindParam(':id', $id_team1);
            $stmt->execute();
            $result1 = $stmt->fetch();
            
            //get flag for team 2
            $sql = "SELECT team.flag FROM mydb.team WHERE team.id=:id";

            $stmt = $db->prepare($sql);
            $stmt->bindParam(':id', $id_team2);
            $stmt->execute();
            $result2 = $stmt->fetch();
            
            $result[$key]['flag_1'] = $result1["flag"];
            $result[$key]['flag_2'] = $result2["flag"];
            $result[$key]['name_1'] = HelperController::getTeamName($game["team_id1"]);
            $result[$key]['name_2'] = HelperController::getTeamName($game["team_id2"]);
        }
        return $result;
    }
    
     /**
     * get bet
     * 
     * @param Int $game_id
     * @return array
     */
    public function getBet($game_id, $group_id){
        $db = HelperController::getConnection();
        $user_id = HelperController::getLoggedInUserId();
        $sql = "SELECT * FROM mydb.bet WHERE bet.game_id=:game_id AND bet.user_has_group_group_id=:group_id AND bet.user_has_group_user_id = :user_id";
        
        $stmt = $db->prepare($sql);
        $stmt->bindParam(':game_id', $game_id);
        $stmt->bindParam(':group_id', $group_id);
        $stmt->bindParam(':user_id', $user_id);
        $stmt->execute();
        $result = $stmt->fetch();
        
        return $result;
    }

}
