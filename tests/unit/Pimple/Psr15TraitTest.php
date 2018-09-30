<?php

/*
 * This file is part of the uSilex framework.
 *
 * (c) Enrico Fagnoni <enrico@linkeddata.center>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace uSilex\Tests;

use PHPUnit\Framework\TestCase;
use Pimple\Container;
use Psr\Http\Server\RequestHandlerInterface;


class Psr15TraitTest extends TestCase
{
    
    public function testHandle()
    {
        $app = new class extends Container implements RequestHandlerInterface {
            use \uSilex\Pimple\Psr15Trait;
        };
        
        $request = $this->createMock('\\Psr\\Http\\Message\\ServerRequestInterface');
        
        $response = $this->createMock('\\Psr\\Http\\Message\\ResponseInterface');
        
        $middleware1 = $this->createMock('\\Psr\\Http\\Server\\MiddlewareInterface');
        $middleware1->method('process')->willReturn($response);
        
        $app['middlewareService'] = function ($app) use($middleware1){ return $middleware1;};
        $app->registerAsMiddleware('middlewareService');
        
        $actualResponse = $app->handle($request);
        
        $this->assertEquals($response, $actualResponse);
    }
    
    
    /**
     * @expectedException \TypeError
     * @expectedExceptionMessage fake is not a middleware
     */
    public function testHandleWrongMiddlewareType()
    {
        $app = new class extends Container implements RequestHandlerInterface {
            use \uSilex\Pimple\Psr15Trait;
        };
        
        $request = $this->createMock('\\Psr\\Http\\Message\\ServerRequestInterface');
        $app['fake'] = "a string instead of a middleware";
        $app->registerAsMiddleware('fake');
        
        $actualResponse = $app->handle($request);
    }
    
    
    /**
     * @expectedException \OutOfBoundsException
     * @expectedExceptionMessage No middleware to produce an http response
     */
    public function testHandleNoMiddleware()
    {
        $app = new class extends Container implements RequestHandlerInterface {
            use \uSilex\Pimple\Psr15Trait;
        };
        
        $request = $this->createMock('\\Psr\\Http\\Message\\ServerRequestInterface');
        
        $actualResponse = $app->handle($request);
    }
    
    
}
