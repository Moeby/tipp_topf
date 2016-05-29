<?php
namespace tippTopf\src\Controller;

class HelperController {
     /**
     * create view object
     * @param array $container
     */
    public static function getView($app){
        $container = $app->getContainer();  
        
        $container['view'] = function ($container) {
            $view = new \Slim\Views\Twig('templates/View/', [
                'cache' => false
            ]);
            $view->addExtension(new \Slim\Views\TwigExtension(
                    $container['router'], $container['request']->getUri()
            ));

            return $view;
        };     
    }
    
    /**
     * return database connection
     */
    public static function getConnection(){
        $config = include(__DIR__ . '/../../configs/local.php');
        $db = new \PDO("mysql:host=localhost;dbname=mydb", $config['db']['user'], $config['db']['password'] );
        
        return $db;
    }
}


