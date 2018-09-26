<?php
namespace uSILEX;


Class RouteMatcher implements RouteMatcherInterface
{
    protected $app;
    
    public function __construct(Application $app) 
    {    
        $this->app = $app;
    }

    
    public function match(Route $route) : array 
    {
        assert( isset($this->app['request']));
        
        $verbRegexp= '#^' . $route->getHttpVerb() . '$#i'; // case insensitive
        $pathRegexp= '#^' . $route->getPath() . '$#'; // case sensitive
        
        $matches = null;
        try {
            preg_match($verbRegexp, $this->app['request']->getMethod()) &&
            preg_match($pathRegexp, $this->app['request']->getPathInfo(),$matches);
        } catch (Exception $e) {// just ignore regexp errors...}
        
        return $matches;
    }
}