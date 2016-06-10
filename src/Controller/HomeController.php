<?php

namespace tippTopf\src\Controller; 

use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\ResponseInterface;

/**
 * 
 */
class HomeController {
    
    private $app;
    /**
    /* set app variable
     * @param type $container
     */
    public function __construct($container) {
        $this->app = $container;
    }

    /**
     * redirect to home twig file
     * return view
     */
    public function showHome(ServerRequestInterface $request, ResponseInterface $response) {
        $app = $this->app;      
        $this->app->getContainer()['view']->getEnvironment()->addGlobal("session", $_SESSION); 
        
        $app->getContainer()['view']->render($response, 'home.html.twig', array('title' => 'Home', 'page_title' => 'TippTopf'));
    }
    
    public function showContact(ServerRequestInterface $request, ResponseInterface $response) {
        $app = $this->app;      
        $this->app->getContainer()['view']->getEnvironment()->addGlobal("session", $_SESSION); 
        
        $app->getContainer()['view']->render($response, 'contact.html.twig', array('title' => 'Contact', 'page_title' => 'Contact'));
    }

}
