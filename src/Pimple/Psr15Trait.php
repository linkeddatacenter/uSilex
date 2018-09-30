<?php

/*
 * This file is part of the uSilex framework.
 *
 * (c) Enrico Fagnoni <enrico@linkeddata.center>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace uSilex\Pimple;

use Psr\Http\Server\RequestHandlerInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\ResponseInterface;
use OutOfBoundsException;
use TypeError;

/**
 * Psr15 trait. HTTP Server Request Handlers implementation
 *
 * @author Enrico Fagnoni <enrico@linkeddata.center>
 */
trait Psr15Trait
{
    
    protected $middlewares = [];
    
    
    public function registerAsMiddleware(string $servicename)
    {
        $this->middlewares[] = $servicename;
        
        return $this;
    }
    
    
    protected function handleRunner(ServerRequestInterface $request) : ResponseInterface
    {
        $middlewareServiceName = current($this->middlewares);
        if (!$middlewareServiceName) {
            throw new OutOfBoundsException("No middleware to produce an http response");
        }
        $middleware = $this[$middlewareServiceName];
        if( !($middleware instanceof MiddlewareInterface)) {
            throw new TypeError("$middlewareServiceName is not a middleware");
        }
        next($this->middlewares);
        
        // execute middleware
        $result = $middleware->process($request, $this);
        
        return $result;
    }
    
    
    /**
     * Handles a request and produces a response
     *
     * May call other collaborating code to generate the response.
     */
    public function handle(ServerRequestInterface $request) : ResponseInterface
    {
        reset( $this->middlewares);
        return $this->handleRunner($request);
    }
    
}
