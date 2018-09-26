<?php
namespace uSILEX\Tests;

use PHPUnit\Framework\TestCase;
use uSILEX\Application;
use uSILEX\Route;
use uSILEX\Exception\HttpException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\StreamedResponse;

class ApplicationTest extends TestCase
{

    public function testGetRequest()
    {

        $app = new Application();
        $app['request'] = Request::create('/pippo');
        
        $app['my_controller'] = function (Application $a) {
            return new Response('ok');
        };
        $testRoute = new Route('GET', '/(pippo)', 'my_controller');
        $app->addRoute( $testRoute);
        
        $response = $app->handleRequest();
        
        $this->assertEquals($testRoute, $app['request.route']);
        $this->assertCount(2, $app['request.matches']);
        $this->assertEquals('pippo', $app['request.matches'][1]);
        $this->assertEquals('ok', $response->getContent());
    }
    
    
    public function testOn_route_match()
    {
        
        $app = new Application();
        $app['request'] = Request::create('/pluto');
        
        $app['on_route_match'] = function (Application $a) {
            $a['request.myparam'] = $a['request.matches'][1];
            return true;
        };
        
        $app['my_controller'] = function (Application $a) {
            return new Response($a['request.myparam']);
        };
        
        $app->addRoute( new Route('GET', '/(.*)', 'my_controller'));
        $response = $app->handleRequest();

        $this->assertEquals('pluto', $response->getContent());
    }
    
    
    
    public function testOn_response()
    {
        $app = new Application();
        $app['response'] = new Response('OK');
        $app['uSILEX_IGNORE_SEND'] = true;
          
        $app['on_response'] = function (Application $a) {
            return new Response( $a['response']->getContent(). ' CONFIRMED');
        };
        
        $app->run();
        
        $this->assertEquals('OK CONFIRMED', $app['response']->getContent());
    }
    

    public function testGetRoutesWithNoRoutes()
    {
        $app = new Application();

        $routes = $app->getRoutes();
        $this->assertCount(0, $routes);
    }

    public function testGetRoutesWithRoutes()
    {
        $app = new Application();

        $app->addRoute( new Route('GET', '/', 'pippo'));
        $app->addRoute( new Route('POST', '/','pippo'));
            
        $routes = $app->getRoutes();
        $this->assertCount(2, $routes);
    }

}