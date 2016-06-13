<?php

namespace tippTopf\src\Controller;

use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\ResponseInterface;

class GroupController {

    private $app;

    /**
     * set app variable
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
            $app->getContainer()['view']->render($response, 'newGroup.html.twig', array('title' => 'New Group', 'page_title' => 'Create New Group'));
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
    public function addGroup(ServerRequestInterface $request, ResponseInterface $response) {
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

        if (empty($result)) {
            // add group 
            $sql = "INSERT INTO `group`(`name`, `owner`) VALUES (:name, :user_id)";

            $stmt = $db->prepare($sql);
            $stmt->bindParam(':name', $_POST['name']);
            $stmt->bindParam(':user_id', $user_id);
            $stmt->execute();

            $group_id = HelperController::getGroupId($user_id, $_POST['name']);
            unset($_POST['name']);

            //add invitations
            foreach ($_POST as $invitation) {
                $this->addInvitation($db, $invitation, $group_id);
            }

            //add into user_has_group table
            $sql = "INSERT INTO `user_has_group`(`user_id`, `group_id`) VALUES (:user_id, :group_id)";

            $stmt = $db->prepare($sql);
            $stmt->bindParam(':user_id', $user_id);
            $stmt->bindParam(':group_id', $group_id);
            $stmt->execute();

            $app->getContainer()['view']->render($response, 'error.html.twig', array('title' => 'Group Added', 'page_title' => 'Group has been added'));
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
    public function addInvitation($db, $invitation, $group_id) {
        // search if group name already exists
        $sql = "SELECT * FROM mydb.invitation WHERE invitation.group_id = :group_id AND invitation.email = :invitation";

        $stmt = $db->prepare($sql);
        $stmt->bindParam(':group_id', $group_id);
        $stmt->bindParam(':invitation', $invitation);
        $stmt->execute();
        $result = $stmt->fetchAll();

        if (empty($result)) {
            $sql = "INSERT INTO `invitation`(`group_id`, `email`) VALUES (:group_id, :email)";

            $stmt = $db->prepare($sql);
            $stmt->bindParam(':group_id', $group_id);
            $stmt->bindParam(':email', $invitation);
            $stmt->execute();

            $this->sendInvitation($invitation);
        }
    }

    /**
     * sent invitation mail
     * @param string $email
     */
    private function sendInvitation($email) {
        $config = include(__DIR__ . '/../../configs/local.php');
        $user_mail = HelperController::getLoggedInUserMail();
        
        $body = "Hi there, \r\n\r\n"
              . "$user_mail sents you an invitation to join his/her football betting group on tipptopf.ch. \r\n"
              . "Signing up is free and it's all about having a fun competition without having to bet any money on the outcome.\r\n"
              . "If you want to join the group please follow the link below and use the email address we sent this email to during the sign up process, "
              . "you'll be promted to join the group after your first login."
              . "\r\n\r\n"
              . "www.tipptopf.ch/signup \r\n\r\n"
              . "Kind regards, \r\n"
              . "TippTopf Support";
        
        $transport = \Swift_SmtpTransport::newInstance('smtp.gmail.com', 465, "ssl")
                ->setUsername($config['mail']['user'])
                ->setPassword($config['mail']['password']);

        $mailer = \Swift_Mailer::newInstance($transport);

        $message = \Swift_Message::newInstance("TippTopf: $user_mail has invited you to join his football betting group!")
                ->setFrom(array('tipptopf@massiveimpact.com' => 'TippTopf'))
                ->setTo(array($email))
                ->setBody($body);

        $result = $mailer->send($message);
    }


    /**
     * look up groups that user belongs to and render corresponding twig file
     * 
     * @param ServerRequestInterface $request
     * @param ResponseInterface $response
     */
    public function showOverview(ServerRequestInterface $request, ResponseInterface $response) {
        $app = $this->app;
        $this->app->getContainer()['view']->getEnvironment()->addGlobal("session", $_SESSION);

        $db = HelperController::getConnection();
        $user_id = HelperController::getLoggedInUserId();

        //check if user is logged in
        if (isset($_SESSION['loggedin']) && $_SESSION['loggedin'] == true) {

            // search if groups exists for user
            $sql = "SELECT * FROM mydb.user_has_group WHERE user_has_group.user_id = :user_id";

            $stmt = $db->prepare($sql);
            $stmt->bindParam(':user_id', $user_id);
            $stmt->execute();
            $result = $stmt->fetchAll();

            $groups = array();
            
            //check if redirect to specific group needed
            $redirect = null;
            if (!empty($_GET)){
                $redirect = $_GET["submitted"];
            }
            
            // fill group array with necessary inf
            foreach ($result as $rel) {
                $group_obj = HelperController::getGroup($rel["group_id"]);
                $groups[]["name"] = $group_obj["name"];

                $user = HelperController::getUser($group_obj["owner"]);
                $last_key = end(array_keys($groups));
                $groups[$last_key]["id"] = $group_obj["id"];
                $groups[$last_key]["owner"] = $user["username"];
            }
            $app->getContainer()['view']->render($response, 'groupOverview.html.twig', array('title' => 'Groups', 'page_title' => 'Groups Overview', 'groups' => $groups, 'redirect' => $redirect));
        } else {
            $app->getContainer()['view']->render($response, 'error.html.twig', array('title' => 'Restricted Access', 'page_title' => "Access Restriced \r\n Please Log in to view this page"));
        }
    }
    
    /**
     * show single group
     * 
     * @param ServerRequestInterface $request
     * @param ResponseInterface $response
     */
    public function showGroup(ServerRequestInterface $request, ResponseInterface $response){
        $app = $this->app;
        $this->app->getContainer()['view']->getEnvironment()->addGlobal("session", $_SESSION);

        $db = HelperController::getConnection();
        $group_id = $_POST["id"]; 
        $user_id = HelperController::getLoggedInUserId();
        
        if (isset($_SESSION['loggedin']) && $_SESSION['loggedin'] == true) {
            //security check if logged in user belongs to group
            $sql = "SELECT * FROM mydb.user_has_group WHERE user_has_group.user_id = :user_id AND user_has_group.group_id = :group_id";

            $stmt = $db->prepare($sql);
            $stmt->bindParam(':user_id', $user_id);
            $stmt->bindParam(':group_id', $group_id);
            $stmt->execute();
            $result = $stmt->fetchAll();
             
            if (!empty($result)){
                $games = HelperController::getGames();
                $members = HelperController::getGroupMembers($group_id);
                $member_count = count($members);
                $group = HelperController::getGroup($group_id); 
                $owner = HelperController::getUser($group["owner"])["username"];
                
                // calculate points for members
                foreach ($members as $key => $member){
                    $points = HelperController::calculatePoints($group_id, $member["id"]);
                    $members[$key]['points'] = $points;
                    usort($members, 'sortByPoints');
                }
                
                //add current bets and information if betting is still possible to games array 
                foreach ($games as $key => $game){
                    $bet = HelperController::getBet($game["id"], $group_id);
                    if (!empty($bet)){
                        $current_bet = '('.$bet["result_team1"].') : ('.$bet["result_team2"].')';
                        $games[$key]['current_bet'] = $current_bet;
                    } else {
                        $games[$key]['current_bet'] = "None";
                    }
                    
                    $start_date = new \DateTime($game['date']); 
                    $date_utc_current = new \DateTime(null, new \DateTimeZone("UTC"));
                    
                    if ($start_date < $date_utc_current){
                        $games[$key]['closed'] = true;
                    } else {
                        $games[$key]['closed'] = false;
                    }
                }
                
                $app->getContainer()['view']->render($response, 'singleGroup.html.twig', array('title' => 'Group', 'page_title' => "Group Overview", 'group' => $group, 'owner' => $owner, 'nbr' => $member_count, 'games' => $games, 'members' => $members));
            } else {
                $app->getContainer()['view']->render($response, 'error.html.twig', array('title' => 'Restricted Access', 'page_title' => "Access Restricted"));
            }
            
        } else {
            $app->getContainer()['view']->render($response, 'error.html.twig', array('title' => 'Restricted Access', 'page_title' => "Access Restriced \r\n Please Log in to view this page"));
        }
    }
    
    /**
     * sort array
     */
    public function sortByPoints($a, $b) {
        return $a['points'] - $b['points'];
    }
    
    /**
     * join group & delete invitation
     * 
     * @param ServerRequestInterface $request
     * @param ResponseInterface $response
     */
    public function joinGroup(ServerRequestInterface $request, ResponseInterface $response) {
        $db = HelperController::getConnection();
        
        $user_id = $_POST["user_id"];
        $inv_id = $_POST["inv_id"];
        $accepted = $_POST["accepted"];
        $email = $_POST["user_email"];
        $group_id = $_POST["group_id"];
        
        
        if ($accepted === "accept=yes"){
            //insert user into group
            $sql = "INSERT INTO `user_has_group`(`user_id`, `group_id`) VALUES (:user_id, :group_id)";

            $stmt = $db->prepare($sql);
            $stmt->bindParam(':user_id', $user_id);
            $stmt->bindParam(':group_id', $group_id);
            $stmt->execute();
        }
        
        $sql = "DELETE FROM mydb.invitation WHERE invitation.id = :id";

        $stmt = $db->prepare($sql);
        $stmt->bindParam(':id', $inv_id);
        $stmt->execute();
        
    }

}
