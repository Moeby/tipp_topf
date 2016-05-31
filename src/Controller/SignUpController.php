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
     * redirect to sign page & do user registration
     * return view
     */
    public function signUp(ServerRequestInterface $request, ResponseInterface $response) {
        $app = $this->app;      
        HelperController::getView($app);
        
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $db = HelperController::getConnection();
            
            // check if email already exists in db
            $sql = "SELECT * FROM mydb.user WHERE user.email = :email";
            $stmt = $db->prepare($sql);
            $stmt->bindParam(':email', $_POST["email"]);
            $stmt->execute();
            $result_mail = $stmt->fetchAll();
            
            // check if username already exists in db
            $sql = "SELECT * FROM mydb.user WHERE user.username = :username";
            $stmt = $db->prepare($sql);
            $stmt->bindParam(':username', $_POST["username"]);
            $stmt->execute();
            $result_username = $stmt->fetchAll();
            
            if (empty($result_mail) && empty($result_username)){
                $this->addNewUser($_POST, $db);
            }

        } else  {
            $app->getContainer()['view']->render($response, 'signup.html.twig', array('title' => 'Sign Up', 'page_title' => 'Sign Up'));
        }        
    }
    
    /**
     * add new user into db
     * @param array $user
     */
    private function addNewUser($user, $db){
        $sql = "INSERT INTO mydb.user(user.username, user.password,user.email) 
            VALUES (:username, :password, :email)";            

        $stmt = $db->prepare($sql);
        $stmt->bindParam(':username', $user["username"]);
        $stmt->bindParam(':password', $user["password"]);
        $stmt->bindParam(':password', $user["email"]);
        $stmt->execute();
    }

}
