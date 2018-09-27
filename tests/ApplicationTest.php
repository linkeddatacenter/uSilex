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
    
    
    public function testHttpError()
    {
        
        $app = new Application();
        $app['request'] = Request::create('/pippo');
        
        $app['my_controller'] = function (Application $a) {
            throw New HttpException(500, 'controller error');
        };
        $testRoute = new Route('GET', '/(pippo)', 'my_controller');
        $app->addRoute( $testRoute);
        
        $response = $app->handleRequest();
        $this->assertFalse($response->isOk());
        $this->assertEquals(500, $response->getStatusCode());
        $this->assertEquals('controller error', $response->getContent());
    }
    
     
    public function testPhpError()
    {
        
        $app = new Application();
        $app['request'] = Request::create('/pippo');
        
        $app['my_controller'] = function (Application $a) {
            throw new Exception();
        };
        $testRoute = new Route('GET', '/(pippo)', 'my_controller');
        $app->addRoute( $testRoute);
        
        $response = $app->handleRequest();
        
        $this->assertEquals(Response::HTTP_INTERNAL_SERVER_ERROR, $response->getStatusCode());
        $this->assertContains('Error 0', $response->getContent());
    }
    
    
    public function testOnRouteMatch()
    {
        
        $app = new Application();
        $app['request'] = Request::create('/pluto');
        
        $app['on_route_match'] = function (Application $a) {
            $a['myparam'] = $a['request.matches'][1];
        };
        
        $app->onRouteMatch('on_route_match');
        
        $app['my_controller'] = function (Application $a) {
            return new Response($a['myparam']);
        };
        
        $app->addRoute( new Route('GET', '/plu(.*)', 'my_controller'));
        $response = $app->handleRequest();

        $this->assertEquals('to', $response->getContent());
    }
    
    
    
    public function testOnResponse()
    {
        $app = new Application();
        $app['response'] = new Response('OK');
          
        $app['on_response'] = function (Application $a) {
            return new Response( $a['response']->getContent(). ' CONFIRMED');
        };
        
        $app->onResponse('on_response');
        
        $app->run();
        
        $this->expectOutputString('OK CONFIRMED');
    }
    
    
    public function testMultipleOnResponse()
    {
        $app = new Application();
        $app['response'] = new Response('OK');
        
        $app['on_response_1'] = function (Application $a) {
            return new Response( $a['response']->getContent(). ' CONFIRMED');
        };
        $app['on_response_2'] = function (Application $a) {
            return new Response( $a['response']->getContent(). ' AGAIN');
        };
        
        $app
            ->onResponse('on_response_1')
            ->onResponse('on_response_2')
            ->run();
        
        $this->expectOutputString('OK CONFIRMED AGAIN');
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