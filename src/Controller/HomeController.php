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
        
        $db = HelperController::getConnection();
        
        //check if user has been invited to a game
        $email = HelperController::getLoggedInUserMail();
        $user_id = HelperController::getLoggedInUserId();
        
        $sql = "SELECT * FROM mydb.invitation WHERE invitation.email = :email";

        $stmt = $db->prepare($sql);
        $stmt->bindParam(':email', $email);
        $stmt->execute();
        $invitations = $stmt->fetchAll();
        
        //add group name to array
        if (!empty($invitations)){
            foreach ($invitations as $key => $invitation){
                $group = HelperController::getGroup($invitation["group_id"]);
                $invitations[$key]['groupname'] = $group["name"];
            }
        }
        
        $app->getContainer()['view']->render($response, 'home.html.twig', array('title' => 'Home', 'page_title' => 'TippTopf', 'invitations' => $invitations[0], 'user_id' => $user_id));
    }
    
}
