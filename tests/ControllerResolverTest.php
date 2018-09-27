<?php
namespace uSILEX\Tests;

use PHPUnit\Framework\TestCase;
use uSILEX\Application;
use uSILEX\ControllerResolver;
use uSILEX\Route;
use uSILEX\Exception\NotFoundHttpException;
use Symfony\Component\HttpFoundation\Request;

class ControllerResolverTest extends TestCase
{
    /**
     * @expectedException uSILEX\Exception\NotFoundHttpException
     * @expectedExceptionMessage Resource controller not found
     */
    public function testEmpyRoute()
    {
        $app = new Application();
        $app['request'] = Request::create('/pippo');
        $controllerResolver  = new ControllerResolver($app);
        
        $route = $controllerResolver->getController();
    }
    
    
    /**
     * @expectedException uSILEX\Exception\NotFoundHttpException
     * @expectedExceptionMessage Resource controller not found
     */
    public function testNotFountInList()
    {
        $app = new Application();
        $app['request'] = Request::create('/pippo');
        $app['RouteMatcher'] =  $this->createMock('\\uSILEX\\RouteMatcher');
        $app->addRoute( 
            new Route('.*','1','a'),
            new Route('.*','2','b'),
            new Route('.*','3','c')
        );
           
        $controllerResolver  = new ControllerResolver($app);
        $route = $controllerResolver->getController();
    }
    
    public function testFoundRoute()
    {
        $app = new Application();
        $app['request'] = Request::create('/pippo');
        $route= new Route('.*', '.*', 'x');
        $app->addRoute($route);
        $app['RouteMatcher'] =  $this->createMock('\\uSILEX\\RouteMatcher');
        $app['RouteMatcher']->method('match')->willReturn(['/pippo']);
        
        $controllerResolver  = new ControllerResolver($app);
        
        $this->assertEquals($route, $controllerResolver->getController());
    }
}
