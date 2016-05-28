<?php
    use tippTopf\src\Controller\HomeController;
    spl_autoload_extensions(".php"); // comma-separated list
    spl_autoload_register();

$container = $app->getContainer();
$app->get('/', 'HomeController:showHome');

$container['HomeController'] = function ($container)  use ($app) {
    $x = new \tippTopf\src\Controller\HomeController($app);
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
