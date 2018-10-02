<?php
namespace uSilex\Tests;

use PHPUnit\Framework\TestCase;
use Pimple\Container;
use Pimple\ServiceProviderInterface;
use uSilex\Application;

class ApplicationTest extends TestCase
{
    public function testBoot()
    {
        $app = new Application;
        
        $provider = new class implements ServiceProviderInterface {
            public function register(Container $app)
            {
                $app['bootme']=0;
            }
            public function boot(Container $app)
            {
                $app['bootme']=$app['bootme']+1;
            }
        };
        
        $app->register($provider);
        $this->assertEquals(0, $app['bootme']);
        $app->boot();
        $this->assertEquals(1, $app['bootme']);
        $app->boot();
        $this->assertEquals(1, $app['bootme'], "ignored secon boot");
    }
 
    
    public function testProcess()
    {
        $app = new Application;
        
        $request = $this->createMock('\\Psr\\Http\\Message\\ServerRequestInterface');
        $expectedResponse = $this->createMock('\\Psr\\Http\\Message\\ResponseInterface');
        $handler = $this->createMock('\\Psr\\Http\\Server\\RequestHandlerInterface');
        $handler->method('handle')->willReturn($expectedResponse);
        
        $actualResponse= $app->process($request, $handler);
        $this->assertEquals($expectedResponse, $actualResponse);
    }

    
    public function testRun()
    {
        $app = new Application;
        
        $app['uSilex.request'] = $this->createMock('\\Psr\\Http\\Message\\ServerRequestInterface');
        $expectedResponse = $this->createMock('\\Psr\\Http\\Message\\ResponseInterface');
        $app['uSilex.httpHandler'] = $this->createMock('\\Psr\\Http\\Server\\RequestHandlerInterface');
        $app['uSilex.httpHandler']->method('handle')->willReturn($expectedResponse);
        $app['uSilex.responseEmitter'] = $app->protect(function ($response) use ($expectedResponse) {
            echo ($expectedResponse==$response)?"OK":"FAIL";
        });
    
        $actualResponse= $app->run();
        $this->assertTrue($actualResponse);
        $this->expectOutputString('OK');
    }

    public function testRunWithCustomErrorManagement()
    {
        $app = new Application;
        $app['uSilex.exceptionHandler'] = $app->protect(function ($e, $app) {
            return $e->getMessage();
        });
        $app['uSilex.responseEmitter'] = $app->protect(function ($r) {
            echo $r;
        });
        $actualResponse = $app->run();
        $this->assertFalse($actualResponse);
        $this->expectOutputString('Identifier "uSilex.request" is not defined.');
    }
    
    /**
     * @runInSeparateProcess
     */
    public function testRunWithoutRequestCustomErrorManagement()
    {
        $app = new Application;
        $actualResponse = $app->run();
        $this->assertFalse($actualResponse);
        $this->expectOutputString('Identifier "uSilex.request" is not defined.');
    }
}
