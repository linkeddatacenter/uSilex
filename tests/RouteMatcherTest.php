<?php
namespace uSILEX\Tests;

use PHPUnit\Framework\TestCase;
use uSILEX\Application;
use uSILEX\RouteMatcher;
use uSILEX\Route;
use Symfony\Component\HttpFoundation\Request;

class RouteTest extends TestCase
{
    
    public function testEqualPath()
    {
        $app = new Application();
        $app['request'] = Request::create('/pippo');
        $routerMatcher  = new RouteMatcher($app);
        $route = new Route('GET|POST', '/pippo', 'dummy');
        
        $matches = $routerMatcher->match($route );
        
        
        $this->assertEquals($matches, ['/pippo']);
    }
    
    public function testPathRegexp()
    {
        $app = new Application();
        $app['request'] = Request::create('/id1/pippo/id2/pluto');
        $routerMatcher  = new RouteMatcher($app);
        $route = new Route('GET', '/(id1)/(.+)/id2/(.+)', 'dummy');
        
        $matches = $routerMatcher->match($route );
        
        $this->assertEquals($matches, ['/id1/pippo/id2/pluto', 'id1', 'pippo', 'pluto']);
    }   
    
    public function testNotFound()
    {
        $app = new Application();
        $app['request'] = Request::create('/pippo');
        $routerMatcher  = new RouteMatcher($app);
        $route = new Route('GET', '/pluto', 'dummy');
        
        $matches = $routerMatcher->match($route );
        
        $this->assertTrue(empty($matches));
    }
    
    public function testVerbCaseInsensitive()
    {
        $app = new Application();
        $app['request'] = Request::create('/pippo','POST');
        $routerMatcher  = new RouteMatcher($app);
        $route = new Route('POST', '/pippo', 'dummy');
        
        $matches = $routerMatcher->match($route );
        
        $this->assertEquals($matches, ['/pippo']);
    }
    
    public function testMachVerb()
    {
        $app = new Application();
        $app['request'] = Request::create('/pippo');
        $routerMatcher  = new RouteMatcher($app);
        $route = new Route('POST|GET', '/pippo', 'dummy');
        
        $matches = $routerMatcher->match($route );
        
        $this->assertEquals($matches, ['/pippo']);
    }
    
    public function testWrongVerb()
    {
        $app = new Application();
        $app['request'] = Request::create('/pippo');
        $routerMatcher  = new RouteMatcher($app);
        $route = new Route('POST', '/pippo', 'dummy');
        
        $matches = $routerMatcher->match($route );
        
        $this->assertEmpty($matches);
    }
    
    
    
    public function testIgnoreRegexpErrorInvalidChar()
    {
        $app = new Application();
        $app['request'] = Request::create('/pippo','POST');
        $routerMatcher  = new RouteMatcher($app);
        $route = new Route('POST', 'pippo#', 'dummy');
        
        $matches = $routerMatcher->match($route );
        
        $this->assertEmpty($matches);
    }

}
