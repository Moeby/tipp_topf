<?php
namespace tippTopf\src\Controller;

class HelperController {
    
    /**
     * return database connection
     */
    public static function getConnection(){
        $config = include(__DIR__ . '/../../configs/local.php');
        $db = new \PDO("mysql:host=localhost;dbname=mydb", $config['db']['user'], $config['db']['password'] );
        
        return $db;
    }
    
    /**
     * get id of currently logged in User
     * 
     * @return int $user_id
     */
    public static function getLoggedInUserId(){
        
            $db = HelperController::getConnection();
            $sql = "SELECT user.id FROM mydb.user WHERE user.username = :username";
            
            $stmt = $db->prepare($sql);
            $stmt->bindParam(':username', $_SESSION["username"]);
            $stmt->execute();
            $result = $stmt->fetchAll();

            return $result[0]["id"];
    }
}


