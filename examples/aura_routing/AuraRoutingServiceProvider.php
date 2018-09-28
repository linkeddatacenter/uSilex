<?php
namespace EXAMPLE;

use Pimple\Container;
use Pimple\ServiceProviderInterface;

use Aura\Router\RouterContainer;
use Middlewares\AuraRouter;
use Middlewares\RequestHandler;

class AuraRoutingServiceProvider implements ServiceProviderInterface
{

    public function register(Container $app)
    {  
        // change ththi if the endpoint is not on the root
        $app['basepath'] = '/';
         
        $app['routeMap'] = function($app){
            return $app['auraRouterContainer']->getMap();
        };
        
        $app['routes'] = function($app){
            #$app['routeMap']->[verb]( name, path, action);
        };
        
        $app['auraRouterContainer'] = function($app) {
            return new RouterContainer($app['basepath']);
        };
            
        // register a router (lazy)
        $app['auraRouterMiddleware'] = function($app) {
            $app['routes'];
            return new AuraRouter($app['auraRouterContainer']);
        };
        
        // register the RequestHandler
        $app['requestHandlerMiddleware'] = function($app) {
            return new RequestHandler();
        };
                
        $app->registerAsMiddleware('auraRouterMiddleware');
        $app->registerAsMiddleware('requestHandlerMiddleware');   

    }
}