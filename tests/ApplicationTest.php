<?php
namespace uSILEX\Tests;

use PHPUnit\Framework\TestCase;
use uSILEX\Application;
use uSILEX\BootableProviderInterface;
use Pimple\Container;
use Pimple\ServiceProviderInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;

class ApplicationTest extends TestCase
{
    
    public function testHandle()
    {
        $app = new Application();
        
        $request = $this->createMock('\\Psr\\Http\\Message\\ServerRequestInterface');
        
        $app['a_response'] = $this->createMock('\\Psr\\Http\\Message\\ResponseInterface');        
        $middleware1 = $this->createMock('\\Psr\\Http\\Server\\MiddlewareInterface');
        $middleware1->method('process')->willReturn($app['a_response']);
        
        $app['middlewareService'] = function ($app) use($middleware1){ return $middleware1;};
        $app->registerAsMiddleware('middlewareService');
        
        $actualResponse = $app->handle($request);
        
        $this->assertEquals($app['a_response'], $actualResponse);
    }

    public function testRegister()
    {
        $app = new Application();
        
        $provider1 = $this->createMock('\\Pimple\\ServiceProviderInterface');
        $provider2 = $this->createMock('\\Pimple\\ServiceProviderInterface');
        
        $app->register($provider1);
        $app->register($provider2);
        
        $this->assertEquals([$provider1, $provider2],$app->getProviders());
        
    }
    
    
    public function testBoot()
    {
        $app = new Application();
        
        $provider = new class implements ServiceProviderInterface, BootableProviderInterface {
            public function register(Container $app){}
            public function boot(Application $app){ $app['bootme']=true;}
        };
        
        $app->register($provider);
        $app->boot();
        
        $this->assertTrue($app['bootme']);
        
    }
     
    
    
    public function testOnResponse()
    {
        $app = new Application();
        
        $app['request'] = $this->createMock('\\Psr\\Http\\Message\\ServerRequestInterface');
        
        $app['response'] = $this->createMock('\\Psr\\Http\\Message\\ResponseInterface');        
        $app['responseChanged'] = $this->createMock('\\Psr\\Http\\Message\\ResponseInterface');
                
        $app->registerAsMiddleware('dummy');
          
        $app['on_response'] = function (Application $a) {
            return $a['responseChanged'];
        };
        
        $app->onResponse('on_response');

        $app->run();
        
        $this->assertEquals($app['responseChanged'],$app['response']);
    }
    
    
    public function testMultipleOnResponse()
    {
        $app = new Application();
        
        $app['request'] = $this->createMock('\\Psr\\Http\\Message\\ServerRequestInterface');
        
        $app['response'] = $this->createMock('\\Psr\\Http\\Message\\ResponseInterface');
        $app['responseBis'] = $this->createMock('\\Psr\\Http\\Message\\ResponseInterface');
        $app['responseTer'] = $this->createMock('\\Psr\\Http\\Message\\ResponseInterface');
        
        $app->registerAsMiddleware('dummy');
        
        $app['on_response_1'] = function (Application $a) {
            return $a['responseBis'];
        };
        
        $app['on_response_2'] = function (Application $a) {
            assert( $a['response'] = $a['responseBis']);
            return $a['responseTer'];
        };
        
        $app
            ->onResponse('on_response_1')
            ->onResponse('on_response_2')
            ->run();
        
        $this->assertEquals($app['responseTer'],$app['response']);
    }
 

}