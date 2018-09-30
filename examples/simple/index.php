<?php
require_once __DIR__.'/../vendor/autoload.php';
use uSilex\Application;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Psr\Http\Server\MiddlewareInterface;
use Zend\Diactoros\ServerRequestFactory;
use Zend\Diactoros\Response\TextResponse;

$app = new Application;

$app['request'] = function() {
    return ServerRequestFactory::fromGlobals();
};
$app['responseEmitter'] = $app->protect( function($response) {
    echo $response->getBody();
});

$app['message'] = 'hello world!';
$app['say_hello'] = function( Application $app) {
    return new class($app) implements MiddlewareInterface {
        use \uSilex\Psr11Trait;
        public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface {
            return new TextResponse( $this->get('message'));
        }
    };
};


$app->run('say_hello');
