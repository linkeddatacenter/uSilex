<?php
require_once __DIR__.'/../vendor/autoload.php';
use uSilex\Application;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Psr\Http\Server\MiddlewareInterface;

class MyRequestProcessor implements MiddlewareInterface {
    use \uSilex\Psr11Trait;
    
    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        return new \Zend\Diactoros\Response\TextResponse( $this->get('message'));
    }
}  

$app = new Application;
$app['request'] = \Zend\Diactoros\ServerRequestFactory::fromGlobals();
$app['response.emit'] = $app->protect( function($response) {echo $response->getBody();});
$app['message'] = 'hello world!';
$app['kernel'] = new \Relay\Relay([ new MyRequestProcessor($app)]);
$app->run();
