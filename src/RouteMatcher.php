<?php
namespace uSILEX;


Class RouteMatcher implements RouteMatcherInterface
{
    protected $app;
    
    public function __construct( Application $app) 
    {    
        
        $this->app = $app;
    }
  
    public function match(Route $route) : bool {
        assert( isset($this->app['request']));
        return (
            ($route->getHttpVerb() == $this->app['request']->getMethod()) &&
            ($route->getPath() == $this->app['request']->getPathInfo())
        );
    }
}