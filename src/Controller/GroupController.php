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
        $app = $this->app;      
        $this->app->getContainer()['view']->getEnvironment()->addGlobal("session", $_SESSION); 
        
        //check if user is logged in
        if (isset($_SESSION['loggedin']) && $_SESSION['loggedin'] == true) {
            $db = HelperController::getConnection();
            

            $user_id = HelperController::getLoggedInUserId();
            
            // search if group already exists
            $sql = "SELECT * FROM mydb.user_has_group WHERE user_has_group.user_id = :user_id";
            
            $stmt = $db->prepare($sql);
            $stmt->bindParam(':user_id', $user_id);
            $stmt->execute();
            $result = $stmt->fetchAll();
            
            if (!empty($result)){
               $app->getContainer()['view']->render($response, 'groups.html.twig', array('title' => 'Group', 'page_title' => 'Group')); 
            } else {
                $app->getContainer()['view']->render($response, 'newGroup.html.twig', array('title' => 'New Group', 'page_title' => 'Create New Group')); 
            }
        } else {
            $app->getContainer()['view']->render($response, 'error.html.twig', array('title' => 'Restricted Access', 'page_title' => "Access Restriced \r\n Please Log in to view this page"));
        }
    }
    
    /**
     * add group & invitations if entered
     * 
     * @param ServerRequestInterface $request
     * @param ResponseInterface $response
     */
    public function addGroup(ServerRequestInterface $request, ResponseInterface $response){
        $app = $this->app;      
        $this->app->getContainer()['view']->getEnvironment()->addGlobal("session", $_SESSION); 
        
        $db = HelperController::getConnection();
        $user_id = HelperController::getLoggedInUserId();
        
        // search if group name already exists
        $sql = "SELECT * FROM mydb.group WHERE group.name = :name";

        $stmt = $db->prepare($sql);
        $stmt->bindParam(':name', $_POST['name']);
        $stmt->execute();
        $result = $stmt->fetchAll();
            
        if (empty($result)){
            // add group 
            $sql = "INSERT INTO `group`(`name`, `owner`) VALUES (:name, :user_id)";           

            $stmt = $db->prepare($sql);
            $stmt->bindParam(':name', $_POST['name']);
            $stmt->bindParam(':user_id', $user_id);
            $stmt->execute();

            $group_id = HelperController::getGroupId($user_id);
            unset($_POST['name']);
            
            //add invitations
            foreach ($_POST as $invitation){
                $this->addInvitation($db, $invitation, $group_id);
            }
            $app->getContainer()['view']->render($response, 'groups.html.twig', array('title' => 'Group', 'page_title' => 'Group')); 
            
        } else {
            $app->getContainer()['view']->render($response, 'error.html.twig', array('title' => 'ERROR', 'page_title' => ">Group couldn't be created \r\n Please use a different name, the one you tried already exists"));
        }
    }
    
    /**
     * 
     * @param type $db
     * @param type $invitation
     * @param type $group_id
     */
    public function addInvitation($db, $invitation, $group_id){
        
        // search if group name already exists
        $sql = "SELECT * FROM mydb.invitation WHERE invitation.group_id = :group_id AND invitation.email = :invitation";

        $stmt = $db->prepare($sql);
        $stmt->bindParam(':group_id', $group_id);
        $stmt->bindParam(':invitation', $invitation);
        $stmt->execute();
        $result = $stmt->fetchAll();
        
        if (empty($result)){
            $sql = "INSERT INTO `invitation`(`group_id`, `email`) VALUES (:group_id, :email)";           

            $stmt = $db->prepare($sql);
            $stmt->bindParam(':group_id', $group_id);
            $stmt->bindParam(':email', $invitation);
            $stmt->execute();
        }
    }

}
