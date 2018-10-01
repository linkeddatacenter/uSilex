<?php
require_once __DIR__.'/../vendor/autoload.php';
$time_start = microtime(true);

use uSilex\Application;
use uSilex\Provider\Psr15\ZendPipeServiceProvider;
use uSilex\Provider\Psr7\DiactorosServiceProvider;

use Zend\Diactoros\Response;
use Zend\Diactoros\Server;
use Zend\Stratigility\Middleware\NotFoundHandler;

use function Zend\Stratigility\middleware;
use function Zend\Stratigility\path;

$app = new Application;
$app->register(new ZendPipeServiceProvider());
$app->register(new DiactorosServiceProvider());
$app['basepath'] = '/example4.php';

// Landing page
$app['piper']->pipe(middleware(function($req, $handler) use ($app) {
    if (!in_array($req->getUri()->getPath(), [$app['basepath'].'/', $app['basepath']], true)) {
        return $handler->handle($req);
    }
    
    $response = new Response();
    $response->getBody()->write("This is the home. Try '/hello'");
    
    return $response;
}));  
$app['message'] = "Hello World";
  
// Another page
$app['piper']->pipe(path($app['basepath'].'/hello', middleware(function($req, $handler) {
    $response = new Response();
    $response->getBody()->write("Hello. Try '/hello/world'");
    
    return $response;
})));
    
// Another page
$app['piper']->pipe(path($app['basepath'].'/foo', middleware(function($req, $handler) use ($app) {
    $response = new Response();
    $response->getBody()->write($app['message']);
    
    return $response;
})));
    
// 404 handler
$app['piper']->pipe(new NotFoundHandler(function() {
    return new Response();
}));


$app->run();

echo "\n<pre>";
echo "\nmemory_get_usage: ".memory_get_usage();
echo "\nscript execution time:".(microtime(true) - $_SERVER["REQUEST_TIME_FLOAT"]);
echo "\n</pre>";