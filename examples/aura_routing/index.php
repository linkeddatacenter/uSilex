<?php
namespace EXAMPLE;
require_once __DIR__.'/../vendor/autoload.php';

include "AuraRoutingServiceProvider.php";
include "DiactorosServiceProvider.php";
include "ErrorHandlingServiceProvider.php";

use uSilex\Application;
use EXAMPLE\AuraRoutingExampleServiceProvider;
use Zend\Diactoros\Response\TextResponse;

$app = new Application;

$app->register(new DiactorosServiceProvider);
$app->register(new ErrorHandlingServiceProvider);
$app->register(new AuraRoutingServiceProvider);


$app['basepath'] = '/examples/aura_routing/index.php';
$app['routes'] = function($app){   
     
    $app['routeMap']->get('home', '/', function ($request) {
        return new TextResponse("This is the home. Try '/hello/world'");
    });
    
    $app['routeMap']->get('hello', '/hello/{name}', function ($request) {
        $name = $request->getAttribute('name');
        
        return new TextResponse("Hello $name");
    });
        
    return $route;
};

$app->run();