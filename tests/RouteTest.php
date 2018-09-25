<?php
use PHPUnit\Framework\TestCase;
use uSILEX\Route;

class RouteTest extends TestCase
{
    public function testRoute()
    {
        $route = new Route('get','/b','c');
        $this->assertEquals('GET', $route->getHttpVerb());
        $this->assertEquals('/b', $route->getPath());
        $this->assertEquals('c', $route->getAction());
    }

}
