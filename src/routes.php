<?php
    use tippTopf\src\Controller\HomeController;
    use tippTopf\src\Controller\SignUpController;
    spl_autoload_extensions(".php"); // comma-separated list
    spl_autoload_register();
    
$container = $app->getContainer();
$app->get('/', 'HomeController:showHome');
$app->get('/contact', 'HomeController:showContact');
$app->get('/signup', 'SignUpController:signUp');
$app->get('/login', 'LoginController:login');
$app->get('/logout', 'LoginController:logout');
$app->get('/groups', 'GroupController:groupAction');
$app->post('/showgroup', 'GroupController:showGroup');
$app->get('/overview', 'GroupController:showOverview');
$app->post('/newgroup', 'GroupController:addGroup');
$app->post('/login', 'LoginController:checkLogin');
$app->post('/signup', 'SignUpController:signUp');
$app->post('/results', 'GameController:getResults');

$container['HomeController'] = function ($container)  use ($app) {
    $x = new \tippTopf\src\Controller\HomeController($app);
    return $x;
};
$container['SignUpController'] = function ($container)  use ($app) {
    $x = new \tippTopf\src\Controller\SignUpController($app);
    return $x;
};
$container['LoginController'] = function ($container)  use ($app) {
    $x = new \tippTopf\src\Controller\LoginController($app);
    return $x;
};
$container['GroupController'] = function ($container)  use ($app) {
    $x = new \tippTopf\src\Controller\GroupController($app);
    return $x;
};
$container['GameController'] = function ($container)  use ($app) {
    $x = new \tippTopf\src\Controller\GameController($app);
    return $x;
};

$app->run();
