<?php

namespace tippTopf\src\Controller; //Acme\src\Controller

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
        HelperController::getView($app);

        $app->getContainer()['view']->render($response, 'home.html.twig', array('title' => 'Home', 'page_title' => 'TippTopf'));
    }

}
