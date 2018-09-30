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
            public function register(Container $app){$app['bootme']=false;}
            public function boot(Container $app){ $app['bootme']=true;}
        };
        
        $app->register($provider);
        $app->boot();
        
        $this->assertTrue($app['bootme']);
    }
 
    
    public function testRun()
    {
        $app = new Application;
        
        $app['request'] = $this->createMock('\\Psr\\Http\\Message\\ServerRequestInterface');
        $response = $this->createMock('\\Psr\\Http\\Message\\ResponseInterface');
        $app['kernel'] = $this->createMock('\\Psr\\Http\\Server\\RequestHandlerInterface');
        $app['kernel']->method('handle')->willReturn($response);
        $app['uSilex.errorManagement'] = false;
        $app['response.emit'] = $app->protect( function(){ echo "OK"; });
        
        $actualResponse= $app->run();
        $this->assertTrue($actualResponse);
        $this->expectOutputString('OK');
    }
    
    
    
    public function testRunWithoutRequestCustomErrorManagement()
    {
        $app = new Application;
        $app['uSilex.errorManagement'] = $app->protect( function(){ echo 'Error detected';} );
        $actualResponse = $app->run();
        $this->assertFalse($actualResponse);
        $this->expectOutputString('Error detected');
    }
    
    
    /**
     * @expectedException \Exception
     */
    public function testRunWithoutRequestErrorManagementDisabled()
    {
        $app = new Application;
        $app['uSilex.errorManagement'] = false;
        $actualResponse = $app->run();
    }

    
    /**
     * @runInSeparateProcess
     */
    public function testRunErrorDefaultErrorManagement()
    {
        $app = new Application;
        $actualResponse = $app->run();
        $this->expectOutputString('Internal error. Identifier "kernel" is not defined.');
        $this->assertFalse($actualResponse);
    }
    
}