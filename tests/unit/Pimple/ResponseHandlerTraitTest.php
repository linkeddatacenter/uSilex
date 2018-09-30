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
use uSilex\Api\ResponseHandlerInterface;


class ResponseHandlerTraitTest extends TestCase
{
    
    public function testHandleResponse()
    {
        $app = new class extends Container implements ResponseHandlerInterface {
            use \uSilex\Pimple\ResponseHandlerTrait;
        };
       
        $response1 = $this->createMock('\\Psr\\Http\\Message\\ResponseInterface');
        $response2 = $this->createMock('\\Psr\\Http\\Message\\ResponseInterface');
        
        $pp1 = $this->createMock('\\uSilex\\Api\\ResponseProcessorInterface');
        $pp1->method('process')->willReturn($response2);
        
        $app['postProcessor'] = function ($app) use($pp1){ return $pp1;};
        $app->onResponse('postProcessor');
        
        $actualResponse = $app->handleResponse($response1);
        
        $this->assertEquals($response2, $actualResponse);
    }

    
    public function testHandleResponseNoResponseProcessor()
    {
        $app = new class extends Container implements ResponseHandlerInterface {
            use \uSilex\Pimple\ResponseHandlerTrait;
        };
        
        $response = $this->createMock('\\Psr\\Http\\Message\\ResponseInterface');
        
        $actualResponse = $app->handleResponse($response);
        
        $this->assertEquals($response, $actualResponse);
    }
    
    
    /**
     * @expectedException \TypeError
     * @expectedExceptionMessage fake is not a http response processor
     */
    public function testHandleWrongMiddlewareType()
    {
        $app = new class extends Container implements ResponseHandlerInterface {
            use \uSilex\Pimple\ResponseHandlerTrait;
        };
        
        $response = $this->createMock('\\Psr\\Http\\Message\\ResponseInterface');
        $app['fake'] = "a string instead of a response processor";
        $app->onResponse('fake');
        
        $actualResponse = $app->handleResponse($response);
    }
   
}
