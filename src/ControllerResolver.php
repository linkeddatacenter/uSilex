<?php
namespace uSILEX;

/*
 * inspired to https://api.symfony.com/4.1/Symfony/Component/HttpKernel/Controller/ControllerResolverInterface.html
 */

use uSILEX\Exception\NotFoundHttpException;


Class ControllerResolver implements ControllerResolverInterface
{
    protected $app;
    
    
    public function __construct( Application $app) 
    {
        assert( isset($app['RouteMatcher']));
        
        $this->app = $app;
    }

    public function getController() : string
    {
        assert( isset($this->app['request']));
        
        foreach( $this->app->getRoutes() as $route) {
            if ($matches = $this->app['RouteMatcher']->match($route)) {
                $this->app['request.matches'] = $matches;
                $this->app['request.route'] = $route;
                return $route->getAction();
            }
        }
        
        throw new NotFoundHttpException('Resource controller not found');
    }
}