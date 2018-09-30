<?php
namespace EXAMPLE;

use Pimple\Container;
use Pimple\ServiceProviderInterface;

use Zend\Diactoros\ServerRequestFactory;
use Zend\HttpHandlerRunner\Emitter\SapiEmitter;

use Aura\Router\RouterContainer;

class DiactorosServiceProvider implements ServiceProviderInterface
{

    public function register(Container $app)
    {
        $app['request'] = function() {
            return ServerRequestFactory::fromGlobals();
        };
        
        $app['sapiEmitter'] = function() {
            return new SapiEmitter;
        };
   
        $app['responseEmitter'] = $app->protect(function($response) {
            echo $app['sapiEmitter']->emit($response);
        });
   
    }

}