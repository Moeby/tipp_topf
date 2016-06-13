<?php

namespace tippTopf\src\Controller;

use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\ResponseInterface;

class SignUpController {

    private $app;

    /**
     * set app variable
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
        $this->app->getContainer()['view']->getEnvironment()->addGlobal("session", $_SESSION);

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

            if (empty($result_mail) && empty($result_username)) {
                $this->addNewUser($_POST, $db);
            } else {
                $app->getContainer()['view']->render($response, 'signup.html.twig', array('title' => 'Sign Up', 'page_title' => 'Sign Up'));
            }

            $app->getContainer()['view']->render($response, 'home.html.twig', array('title' => 'Home', 'page_title' => 'Home - Thanks for Signing Up!'));
        } else {
            $app->getContainer()['view']->render($response, 'signup.html.twig', array('title' => 'Sign Up', 'page_title' => 'Sign Up'));
        }
    }

    /**
     * hash password & add new user into db
     * @param array $user
     */
    private function addNewUser($user, $db) {

        $options = [
            'cost' => 11,
            'salt' => mcrypt_create_iv(22, MCRYPT_DEV_URANDOM),
        ];
        $hash = password_hash($user["password"], PASSWORD_BCRYPT, $options);

        $sql = "INSERT INTO user(username, password, email) 
            VALUES (:username, :password, :email)";

        $stmt = $db->prepare($sql);
        $stmt->bindParam(':username', $user["username"]);
        $stmt->bindParam(':password', $hash);
        $stmt->bindParam(':email', $user["email"]);
        $stmt->execute();
    }

}
