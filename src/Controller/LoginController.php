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
      /* set app variable
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
        HelperController::getView($app);

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
        HelperController::getView($app);

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
            }
            
            $app->getContainer()['view']->render($response, 'home.html.twig', array('title' => 'Home', 'page_title' => 'Home'));

        } else {
            $app->getContainer()['view']->render($response, 'login.html.twig', array('title' => 'Login', 'page_title' => 'Login - Wrong Username(Try Again)'));
        }
    }

}
