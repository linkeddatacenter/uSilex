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

use uSilex\Api\ResponseProcessorInterface;
use uSilex\Api\ResponseHandlerInterface;
use Psr\Http\Message\ResponseInterface;
use TypeError;

/**
 * Psr15plus trait. HTTP response post processing implementation
 *
 * @author Enrico Fagnoni <enrico@linkeddata.center>
 */
trait ResponseHandlerTrait
{
    
    protected $responseProcessors = [];
    
    
    public function onResponse(string $serviceName)
    {
        $this->responseProcessors[] = $serviceName;
        
        return $this;
    }
    
    
    /**
     * Handles a response and produces a response
     *
     * May call other collaborating code to generate the response.
     */
    protected function handleResponseRunner (ResponseInterface $response) : ResponseInterface
    {
        $responseProcessorServiceName = current($this->responseProcessors);
        if (!$responseProcessorServiceName) {
            return $response;
        }
        $responseProcessor = $this[$responseProcessorServiceName];
        if( !($responseProcessor instanceof ResponseProcessorInterface)) {
            throw new TypeError("$responseProcessorServiceName is not a http response processor");
        }
        next($this->responseProcessors);
        
        // execute middleware
        $result = $responseProcessor->process($response, $this);
        
        return $result;
    }
    
    
    /**
     * Handles a response and produces a response
     *
     * May call other collaborating code to generate the response.
     */
    public function handleResponse(ResponseInterface $response) : ResponseInterface
    {
        reset( $this->responseProcessors);
        return $this->handleResponseRunner($response);
    }
    
}
