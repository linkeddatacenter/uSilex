<?php
$time_start = microtime(true);
/* same as in README.md */
require_once __DIR__.'/../vendor/autoload.php';
use uSilex\Application;
use Zend\Diactoros\Response\TextResponse;
use Zend\Diactoros\ServerRequestFactory;
use Relay\Relay;

$app = new Application;
$app['uSilex.request'] = function() { return ServerRequestFactory::fromGlobals();};
$app['uSilex.responseEmitter'] = $app->protect( function($response) {echo $response->getBody();});
$app['message'] = 'hello world!';
$app['uSilex.httpHandler'] = function($app) { 
    return new Relay([ 
        function() use($app){ return new TextResponse($app['message']);}
    ]); 
};
$app->run();

echo "\n<pre>";
echo "\nmemory_get_usage: ".memory_get_usage ();
echo "\nscript execution time:". (microtime(true) - $_SERVER["REQUEST_TIME_FLOAT"]);
echo "<pre>";