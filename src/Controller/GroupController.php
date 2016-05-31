<?php

namespace tippTopf\src\Controller; 

use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\ResponseInterface;

/**
 * 
 */
class GroupController {
    
    private $app;
    /**
    /* set app variable
     * @param type $container
     */
    public function __construct($container) {
        $this->app = $container;
    }

    /**
     * login check & redirect to correcponding view
     * return view
     */
    public function groupAction(ServerRequestInterface $request, ResponseInterface $response) {
        session_start();
        //check if user is logged in
        if (isset($_SESSION['loggedin']) && $_SESSION['loggedin'] == true) {
            $app = $this->app;      
            HelperController::getView($app);

            $app->getContainer()['view']->render($response, 'groups.html.twig', array('title' => 'Group', 'page_title' => 'Group'));
        } else {
            $app->getContainer()['view']->render($response, 'error.html.twig', array('title' => 'Restricted Access', 'page_title' => "Access Restriced <br> Please Log in to view this page"));
        }
    }

}
