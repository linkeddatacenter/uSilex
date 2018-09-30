<?php 

/*
 * This file is part of the uSilex framework.
 *
 * (c) Enrico Fagnoni <enrico@linkeddata.center>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace uSilex;

use Pimple\Container;
use Psr\Http\Server\RequestHandlerInterface;
use uSilex\Api\ResponseHandlerInterface;

class HttpKernel extends Container implements
    RequestHandlerInterface,
    ResponseHandlerInterface
{     
    use \uSilex\Pimple\Psr15Trait;
    use \uSilex\Pimple\ResponseHandlerTrait;
}