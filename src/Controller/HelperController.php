<?php

namespace tippTopf\src\Controller;

class HelperController {

    /**
     * return database connection
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

}
