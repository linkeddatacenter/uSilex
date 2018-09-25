<?php
require_once __DIR__.'/../vendor/autoload.php';

// create a new application
$app = new \uSILEX\Application;

// create a service using pimple to be used as controller
$app['say_hello_controller']= function ($app) {
    return $app->json(['hello', 'world']);
};

// define a route
$app->addRoute(new \uSILEX\Route('GET', '/', 'say_hello_controller'));

//run application
$app->run();