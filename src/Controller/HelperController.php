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
}


