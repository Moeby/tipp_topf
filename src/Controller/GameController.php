<?php

namespace tippTopf\src\Controller;

use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\ResponseInterface;

/**
 * 
 */
class GameController {

    private $app;

    /**
      /* set app variable
     * @param type $container
     */
    public function __construct($container) {
        $this->app = $container;
    }

    /**
     * add team & results via data from js api call
     * 
     * @param ServerRequestInterface $request
     * @param ResponseInterface $response
     */
    public function getResults(ServerRequestInterface $request, ResponseInterface $response) {
        $app = $this->app;
        $this->app->getContainer()['view']->getEnvironment()->addGlobal("session", $_SESSION);
        
        $db = HelperController::getConnection();

        $games = $_POST['data'];
        foreach ($games as $game) {

            $match = array();
            $match['matchday'] = $game["matchday"];
            $match['date'] = $game["date"];
            $match['homeTeamName'] = $game["homeTeamName"];
            $match['awayTeamName'] = $game["awayTeamName"];
            $match['result_home'] = $game["result"]["goalsHomeTeam"];
            $match['result_away'] = $game["result"]["goalsAwayTeam"];

            $api_link_home = $game["_links"]["homeTeam"]["href"];
            $api_link_away = $game["_links"]["awayTeam"]["href"];

            //add team
            $home_team_id = $this->addTeam($match['homeTeamName'], $api_link_home, $app);
            $away_team_id = $this->addTeam($match['awayTeamName'], $api_link_away, $app);
            
            //check if game already exists
            $sql = "SELECT * FROM mydb.game WHERE game.team_id1 = :team_id1 AND game.team_id2 = :team_id2 AND game.date = :date";

            $stmt = $db->prepare($sql);
            $stmt->bindParam(':team_id1', $home_team_id);
            $stmt->bindParam(':team_id2', $away_team_id);
            $stmt->bindParam(':date', $match['date']);
            $stmt->execute();
            $result = $stmt->fetch();
                      
            //create datetime obj
            $start_date = new \DateTime($match['date']); 
            $date_utc_current = new \DateTime(null, new \DateTimeZone("UTC"));
            $date_finish = $date_utc_current;
            $date_finish->modify('+4 days');
            
            //add as new game
            if (empty($result)){
                $sql = "INSERT INTO `game`(`result_team1`, `result_team2`, `team_id1`, `team_id2`, `date`, `matchday`) VALUES (:result_team1, :result_team2, :team_id1, :team_id2, :date, :matchday)";
                
                $stmt = $db->prepare($sql);
                $stmt->bindParam(':result_team1', $match['result_home']);
                $stmt->bindParam(':result_team2', $match['result_away']);
                $stmt->bindParam(':team_id1', $home_team_id);
                $stmt->bindParam(':team_id2', $away_team_id);
                $stmt->bindParam(':date', $match['date']);
                $stmt->bindParam(':matchday', $match['matchday']);
                $stmt->execute();
            } elseif (($start_date < $date_utc_current) && ($start_date <  $date_finish)){
                
                $sql="UPDATE `game` SET result_team1=:result_team1, result_team2=:result_team2 WHERE id=:id";
                
                $stmt = $db->prepare($sql);
                $stmt->bindParam(':id', $result["id"]);
                $stmt->bindParam(':result_team1', $match['result_home']);
                $stmt->bindParam(':result_team2', $match['result_away']);
                $stmt->execute();
            }
            
        }
        return;
    }

    /**
     * check if team already exists, if not create
     * 
     * @param string $name
     * @param string $api_link
     * @param obj $app
     * @return array team information
     */
    public function addTeam($name, $api_link, $app) {
        $db = HelperController::getConnection();
        
        $sql = "SELECT * FROM mydb.team WHERE team.name = :name";
        
        $stmt = $db->prepare($sql);
        $stmt->bindParam(':name', $name);
        $stmt->execute();
        $result = $stmt->fetchAll();
        
        if (empty($result)){
            $flag = $this->getFlag($api_link);
            
            if (!empty($flag)){
                $sql = "INSERT INTO `team`(`name`, `flag`) VALUES (:name, :flag)";
                $stmt = $db->prepare($sql);
                $stmt->bindParam(':flag', $flag);
            } else {
                $sql = "INSERT INTO `team`(`name`) VALUES (:name)";
                $stmt = $db->prepare($sql);
            }
            $stmt->bindParam(':name', $name);
            $stmt->execute();
            
            //search for entry that has just been added
            $sql = "SELECT * FROM mydb.team WHERE team.name = :name";
        
            $stmt = $db->prepare($sql);
            $stmt->bindParam(':name', $name);
            $stmt->execute();
            $result = $stmt->fetchAll();   
        } 
        return $result[0]["id"];
    }

    /**
     * get  team crest
     * 
     * @param type $api_link
     * @return type array
     */
    public function getFlag($api_link) {
        $ch = curl_init();

        // define options
        $optArray = array(
            CURLOPT_URL => $api_link,
            CURLOPT_RETURNTRANSFER => true
        );

        curl_setopt_array($ch, $optArray);
        $result = curl_exec($ch);

        $array = json_decode($result, true);

        if (is_array($array)) {
            return $array["crestUrl"];
        } else {
            return;
        }
    }
    
    /**
     * add Bet
     * 
     * @param ServerRequestInterface $request
     * @param ResponseInterface $response
     */
    public function showBet(ServerRequestInterface $request, ResponseInterface $response) {
        $app = $this->app;
        $this->app->getContainer()['view']->getEnvironment()->addGlobal("session", $_SESSION);
        // search corresponding game
        $db = HelperController::getConnection();
        
        $sql = "SELECT game.id, game.team_id1, game.team_id2, game.date FROM mydb.game WHERE game.id = :game_id";
        $stmt = $db->prepare($sql);
        $stmt->bindParam(':game_id', $_POST["data"]);
        $stmt->execute();
        $result = $stmt->fetch();

        //check date 
        $start_date = new \DateTime($result['date']); 
        $date_utc_current = new \DateTime(null, new \DateTimeZone("UTC"));
        
        if ($start_date > $date_utc_current){
            $result["team1_name"] = HelperController::getTeamName($result["team_id1"]);
            $result["team2_name"] = HelperController::getTeamName($result["team_id2"]);
            
            //echo returns the string to the js, return doesn't
            echo(json_encode( $result )); exit;
        }
    }
    
    /**
     * handle data from js & add bet into db
     * @param ServerRequestInterface $request
     * @param ResponseInterface $response
     */
    public function addBet(ServerRequestInterface $request, ResponseInterface $response) {
        $app = $this->app;
        $this->app->getContainer()['view']->getEnvironment()->addGlobal("session", $_SESSION);
        
        $user_id = HelperController::getLoggedInUserId();
        $group_id = $_POST["group_id"];
        $bets = $_POST["data"][0];
        $game_data = json_decode($_POST["data"][1], true);
        
        // get betting information into right format
        $arr = explode('team=', $bets);
        $bets = array_filter($arr);
        $bets[1] = str_replace("&","", $bets[1]);
        
        $start_date = new \DateTime($game_data['date']); 
        $date_utc_current = new \DateTime(null, new \DateTimeZone("UTC"));
        
        //check date again, only allow bet if current date smaller than game start
        if ($date_utc_current < $start_date){
            $db = HelperController::getConnection();
            
            $bet_exists = HelperController::getBet($game_data["id"], $group_id);

            if (empty($bet_exists)){
                $sql = "INSERT INTO `bet`(`result_team1`, `result_team2`, `game_id`, `user_has_group_user_id`, `user_has_group_group_id`) VALUES (:result_team1, :result_team2, :game_id, :user_id, :group_id)";
                
                $stmt = $db->prepare($sql);
                $stmt->bindParam(':result_team1', $bets[1]);
                $stmt->bindParam(':result_team2', $bets[2]);
                $stmt->bindParam(':game_id', $game_data["id"]);
                $stmt->bindParam(':user_id', $user_id);
                $stmt->bindParam(':group_id', $group_id);
                $stmt->execute();
            } else { 
                $sql="UPDATE `bet` SET result_team1=:result_team1, result_team2=:result_team2 WHERE id=:id";
                
                $stmt = $db->prepare($sql);
                $stmt->bindParam(':id', $bet_exists["id"]);
                $stmt->bindParam(':result_team1', $bets[1]);
                $stmt->bindParam(':result_team2', $bets[2]);
                $stmt->execute();
            }
        
        }
        
        
    }
    
}
