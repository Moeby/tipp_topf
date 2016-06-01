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
}


