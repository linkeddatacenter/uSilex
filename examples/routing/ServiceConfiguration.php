<?php
namespace examples\aura_routing;

use Pimple\ServiceProviderInterface;
use Pimple\Container;

use Zend\Diactoros\Response\TextResponse;
use Zend\Diactoros\ServerRequestFactory;
use Zend\HttpHandlerRunner\Emitter\SapiEmitter;
use Aura\Router\RouterContainer;
use Middlewares\AuraRouter;
use Middlewares\RequestHandler;
use Middlewares\ErrorHandler;

/**
 * this configuration reuse middleware and other
 * components directry from the network
 */
class ServiceConfiguration implements ServiceProviderInterface
{

    public function register(Container $app)
    {
        
        // Error handler middleware configuration
        // from: https://github.com/middlewares/error-handler
        $app['errorHandlingMiddleware'] = function() {
            return (new ErrorHandler())->catchExceptions(true);
        };
        
        // aura routing middleware configuration
        // from: https://github.com/middlewares/aura-router
        $app['basepath'] = '/examples/routing/index.php';
        $app['auraRouterMiddleware'] = function($app) {
            $routeContainer =  new RouterContainer($app['basepath']);
            $routeMap = $routeContainer->getMap();
            
            include "routes.php";

            return new AuraRouter($routeContainer);
        };
        
        
        // register the RequestHandler
        // from https://github.com/middlewares/request-handler
        $app['requestHandlerMiddleware'] = function($app) {
            return new RequestHandler();
        };
             
        // uSilex core properties
        
        $app['request'] = ServerRequestFactory::fromGlobals();
        
        $app['response.emit'] = $app->protect(function($response) {
            (new SapiEmitter)->emit($response);
        });
            
        // kernel configuration:
        // see http://relayphp.com/2.x
        $app['kernel'] = new \Relay\Relay([
            $app['errorHandlingMiddleware'],
            $app['auraRouterMiddleware'],
            $app['requestHandlerMiddleware']
        ]);
        
    }
}
