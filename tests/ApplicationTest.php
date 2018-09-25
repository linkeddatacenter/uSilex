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
        $request = Request::create('/');

        $app = new Application();
        $app['my_controller'] = function (Application $a) use($request) {
            return $request === $a['request'] ? new Response('ok') : new Response('ko');
        };
        $app->addRoute( new Route('GET', '/', 'my_controller'));
        
        $app['request'] = $request;
        
        $this->assertEquals('ok', $app->handle()->getContent());
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