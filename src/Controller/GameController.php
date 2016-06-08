<?php

namespace tippTopf\src\Controller; 

use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\ResponseInterface;

/**
 * 
 */
class GameController {
    
    private $app;
    /**
    /* set app variable
     * @param type $container
     */
    public function __construct($container) {
        $this->app = $container;
    }


    public function getResults(ServerRequestInterface $request, ResponseInterface $response) {
        var_dump($_POST); exit;
        $app = $this->app;      
        $this->app->getContainer()['view']->getEnvironment()->addGlobal("session", $_SESSION); 
        var_dump($request); exit;
      //  $app->getContainer()['view']->render($response, 'home.html.twig', array('title' => 'Home', 'page_title' => 'TippTopf'));
    }

}
