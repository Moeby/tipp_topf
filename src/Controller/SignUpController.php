<?php

namespace tippTopf\src\Controller; 

use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\ResponseInterface;

/**
 * 
 */
class SignUpController {
    
    private $app;
    /**
    /* set app variable
     * @param type $container
     */
    public function __construct($container) {
        $this->app = $container;
    }

    /**
     * redirect to sign page & user registration
     * return view
     */
    public function signUp(ServerRequestInterface $request, ResponseInterface $response) {
        $app = $this->app;      
        HelperController::getView($app);
        
        $db = HelperController::getConnection();
        
        $sql = "SELECT * FROM mydb.user WHERE user.id = 1";
        
        $smtp = $db->prepare($sql);
        $smtp->execute();
        $result = $smtp->fetchAll();

        $app->getContainer()['view']->render($response, 'signup.html.twig', array('title' => 'Sign Up', 'page_title' => 'Sign Up'));
    }

}
