<?php

namespace tippTopf\src\Controller;

use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\ResponseInterface;

/**
 * 
 */
class LoginController {

    private $app;

    /**
     * set app variable
     * @param type $container
     */
    public function __construct($container) {
        $this->app = $container;
    }

    /**
     * redirect to login twig file
     * return view
     */
    public function login(ServerRequestInterface $request, ResponseInterface $response) {
        $app = $this->app;
        $this->app->getContainer()['view']->getEnvironment()->addGlobal("session", $_SESSION); 

        $app->getContainer()['view']->render($response, 'login.html.twig', array('title' => 'Login', 'page_title' => 'Login'));
    }

    /**
     * check login data against db data
     * 
     * @param ServerRequestInterface $request
     * @param ResponseInterface $response
     */
    public function checkLogin(ServerRequestInterface $request, ResponseInterface $response) {
        $app = $this->app;
        $db = HelperController::getConnection();

        // search for user
        $sql = "SELECT * FROM mydb.user WHERE user.username = :username";
        $stmt = $db->prepare($sql);
        $stmt->bindParam(':username', $_POST["username"]);
        $stmt->execute();
        $result = $stmt->fetchAll();

        if (!empty($result)) {
            $username = $result[0]["username"];
            $hash_pw = $result[0]["password"];
                       
            $pw_match = password_verify($_POST["password"], $hash_pw);
            
            if ($pw_match){
                    $_SESSION['loggedin'] = true;
                    $_SESSION['username'] = $username;
                 
                $this->app->getContainer()['view']->getEnvironment()->addGlobal("session", $_SESSION); 
                    
            }
            $app->getContainer()['view']->render($response, 'home.html.twig', array('title' => 'TippTopf', 'page_title' => 'Home'));

        } else {
            $app->getContainer()['view']->render($response, 'login.html.twig', array('title' => 'Login', 'page_title' => 'Login - Wrong Username(Try Again)'));
        }
    }

    /**
     * log out 
     * 
     * @param ServerRequestInterface $request
     * @param ResponseInterface $response
     */
    public function logout(ServerRequestInterface $request, ResponseInterface $response){
        session_start();
        
        $app = $this->app;
        
        $_SESSION['loggedin'] = false;
        $_SESSION['username'] = '';
        
        $app->getContainer()['view']->render($response, 'logout.html.twig', array('title' => 'Home', 'page_title' => "You've been logged out. We hope to welcome you back soon!"));
    }
}
