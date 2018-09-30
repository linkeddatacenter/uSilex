<?php
namespace uSilex\Tests;

use PHPUnit\Framework\TestCase;
use uSilex\HttpKernel;


class HttpKernelTest extends TestCase
{
    public function testHandleConfig()
    {
        $app = new HttpKernel;
       
        $this->assertInstanceOf('\\Psr\\Http\\Server\\RequestHandlerInterface', $app );
        $this->assertInstanceOf('\\uSilex\\Api\\ResponseHandlerInterface', $app );
        $this->assertInstanceOf('\\Pimple\\Container', $app );
    }

}