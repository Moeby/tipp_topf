<?php
    use tippTopf\src\Controller\HomeController;
    use tippTopf\src\Controller\SignUpController;
    spl_autoload_extensions(".php"); // comma-separated list
    spl_autoload_register();
    
$container = $app->getContainer();
$app->get('/', 'HomeController:showHome');
$app->get('/signup', 'SignUpController:signUp');
$app->post('/signup', 'SignUpController:signUp');


$container['HomeController'] = function ($container)  use ($app) {
    $x = new \tippTopf\src\Controller\HomeController($app);
    return $x;
};
$container['SignUpController'] = function ($container)  use ($app) {
    $x = new \tippTopf\src\Controller\SignUpController($app);
    return $x;
};


$app->run();

// if only html needs to be rendered use the method below:

//$app->get('/[{name}]', function ($request, $response, $args) {
//    // Sample log message
//    $this->logger->info("Slim-Skeleton '/' route");
//
//    // Render index view
//    return $this->renderer->render($response, 'index.phtml', $args);
//});
