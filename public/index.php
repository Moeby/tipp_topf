<?php

if (PHP_SAPI == 'cli-server') {
    // To help the built-in PHP dev server, check if the request was actually for
    // something which should probably be served as a static file
    $file = __DIR__ . $_SERVER['REQUEST_URI'];
    if (is_file($file)) {
        return false;
    }
}

require __DIR__ . '/../vendor/autoload.php';

session_start();

// Instantiate the app
$config = [
    'settings' => [
        'displayErrorDetails' => true,
        'logger' => [
            'name' => 'slim-app',
            'level' => Monolog\Logger::DEBUG,
            'path' => __DIR__ . '/../logs/app.log',
        ],
    ],
];
$app = new \Slim\App($config);
$container = $app->getContainer();

// Register component on container
$container['view'] = function ($container) {
    $view = new \Slim\Views\Twig('templates/View/', [
        'cache' => false
    ]);
    $view->addExtension(new \Slim\Views\TwigExtension(
            $container['router'], $container['request']->getUri()
    ));
    return $view;
};

$logged_in = array('loggedin' => 'false', 'username' => '');

$app->getContainer()['view']->getEnvironment()->addGlobal('session', $logged_in);
$view = $app->getContainer()['view']; 

// Set up dependencies
require __DIR__ . '/../src/dependencies.php';

// Register middleware
require __DIR__ . '/../src/middleware.php';

// Register routes
require __DIR__ . '/../src/routes.php';

// Run app
$app->run();
