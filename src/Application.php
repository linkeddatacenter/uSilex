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

use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\ResponseInterface;
use InvalidArgumentException;


class Application extends HttpKernel
{
    use \uSilex\Pimple\BootManagerTrait;
    
    /**
     * Handles the request and delivers the response.
     *
     */
    public function run(string $middlewareServiceName = null) : bool
    {
        
        if( !isset($this['request']) ) {
            throw new InvalidArgumentException('request service must be defined');
        }
        $request = $this['request'];
        
        if( !($request instanceof ServerRequestInterface) ) {
            throw new InvalidArgumentException('request is not an http server request');
        }
          
        // auto service registration
        if( $middlewareServiceName ) {
            $this->registerAsMiddleware($middlewareServiceName);
        }
     
        $response = $this->boot()->handle($request);
        $response = $this->handleResponse($response);
        
        if( isset($this['responseEmitter']) && is_callable($this['responseEmitter'])) {
            call_user_func($this['responseEmitter'],$response);
        } else {
            var_dump($response);
        }
        
        return true;
    }
    
}