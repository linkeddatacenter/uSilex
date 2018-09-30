<?php
namespace uSilex\Tests;

use PHPUnit\Framework\TestCase;
use uSilex\Application;


class ApplicationTest extends TestCase
{
    
    public function testApplicationInterfaces()
    {
        $app = new Application;
       
        $this->assertInstanceOf('\\uSilex\\HttpKernel', $app );
    }
    
    
    public function testHandle()
    {
        $app = new Application;
        
        $app['request'] = $this->createMock('\\Psr\\Http\\Message\\ServerRequestInterface');
        
        $response1 = $this->createMock('\\Psr\\Http\\Message\\ResponseInterface');
        $response2 = $this->createMock('\\Psr\\Http\\Message\\ResponseInterface');
        
        $middleware1 = $this->createMock('\\Psr\\Http\\Server\\MiddlewareInterface');
        $middleware1->method('process')->willReturn($response1);
        
        $postprocessor1 = $this->createMock('\\uSilex\\Api\\ResponseProcessorInterface');
        $postprocessor1->method('process')->willReturn($response2);
        
        $app['middlewareService'] = function ($app) use($middleware1){ return $middleware1;};       
        
        $app['postProcessor'] = function ($app) use($postprocessor1){ return $postprocessor1;};
        $app->onResponse('postProcessor');
        
        $app['responseEmitter'] = $app->protect( function($response) use($response2){
            if ($response!=$response2) {throw new Exception('Test failed');}
        });
        
        $actualResponse = $app->run('middlewareService');
        
        $this->assertTrue($actualResponse);
    }
    
    
    public function testHandleWithNoPostProcessing()
    {
        $app = new Application;
        
        $app['request'] = $this->createMock('\\Psr\\Http\\Message\\ServerRequestInterface');
        
        $response1 = $this->createMock('\\Psr\\Http\\Message\\ResponseInterface');
        
        $middleware1 = $this->createMock('\\Psr\\Http\\Server\\MiddlewareInterface');
        $middleware1->method('process')->willReturn($response1);
        
        $app['middlewareService'] = function ($app) use($middleware1){ return $middleware1;};

        
        $app['responseEmitter'] = $app->protect( function($response) use($response1){
            if ($response!=$response1) {throw new Exception('Test failed');}
        });
        
        $actualResponse = $app->run('middlewareService');
        
        $this->assertTrue($actualResponse);
    }
    
    
    
    /**
     * @expectedException \InvalidArgumentException
     * @expectedExceptionMessage request service must be defined
     */
    public function testHandleWithoutRequest()
    {
        $app = new Application;
        $actualResponse = $app->run();
    }
    
    
    /**
     * @expectedException \InvalidArgumentException
     * @expectedExceptionMessage request is not an http server request
     */
    public function testHandleInvalidRequestType()
    {
        $app = new Application;
        $app['request'] = "a string instead of a request";
        $actualResponse = $app->run();
    }
    
}