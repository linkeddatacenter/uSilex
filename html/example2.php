<?php
require_once __DIR__.'/../vendor/autoload.php';
$time_start = microtime(true);

use uSilex\Application;
use uSilex\Provider\Psr15\RelayServiceProvider;
use uSilex\Provider\Psr7\DiactorosServiceProvider;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Psr\Http\Server\MiddlewareInterface;
use Zend\Diactoros\Response\TextResponse;

class MyRequestProcessor implements MiddlewareInterface {
    use \uSilex\Psr11Trait;
    
    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        return new TextResponse($this->get('message'));
    }
}  

$app = new Application;
$app->register(new RelayServiceProvider());
$app->register(new DiactorosServiceProvider());
$app['message'] = 'hello world!';
$app['myMiddleware'] = function($app) {
    return new MyRequestProcessor($app);
};
$app['handler.queue'] = ['myMiddleware'];

$app->run();

echo "\nmemory_get_usage: ".memory_get_usage();
echo "\nscript execution time:".(microtime(true) - $_SERVER["REQUEST_TIME_FLOAT"]);
