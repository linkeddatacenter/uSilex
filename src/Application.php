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
use Pimple\ServiceProviderInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Psr\Http\Server\MiddlewareInterface;
use Exception;

/**
 * The uSilex framework class.
*/
class Application extends Container implements MiddlewareInterface
{
    
    protected $providers = [];
    protected $booted = false;

    /**
     * Instantiate a new Application.
     *
     * Objects and parameters can be passed as argument to the constructor.
     *
     * @param array $values the parameters or objects
     */
    public function __construct(array $values = [])
    {
        parent::__construct($values);
        
        $this['debug'] = false;
    }
    
    /**
     * Redefine Registers a service provider.
     *
     * @param ServiceProviderInterface $provider A ServiceProviderInterface instance
     * @param array                    $values   An array of values that customizes the provider
     *
     */
    public function register(ServiceProviderInterface $provider, array $values = []) : self
    {
        $this->providers[] = $provider;
        
        parent::register($provider, $values);
        
        return $this;
    }
    
    
    /**
     * Boots all service providers.
     *
     * This method is automatically called by handle(), but you can use it
     * to boot all service providers when not handling a request.
     */
    public function boot() : self
    {
        if ($this->booted) {
            return $this;
        }
        
        $this->booted = true;
        
        foreach ($this->providers as $provider) {
            if ( ($provider instanceof ServiceProviderInterface) && method_exists($provider,'boot') ) {
                $provider->boot($this);
            }
        }
        
        return $this;
    }

    /**
     * Handles the request and delivers the response.
     *
     */
    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        // ensure container is booted
        if( !$this->booted ){
            $this->boot();
        }
            
        return $handler->handle($request);
    }

    
    /**
     * Process the request and delivers the response with error management.
     *
     */
    public function run() : bool
    {
        // ensure a default for 'uSilex.responseEmitter'
        if( !isset($this['uSilex.responseEmitter'])){ 
            $this['uSilex.responseEmitter'] = $this->protect(function(){});
        }
        
        try {
            $response = $this->process($this['uSilex.request'],$this['uSilex.httpHandler']);
           
            call_user_func($this['uSilex.responseEmitter'],$response, $this);
            
            $result = true;
        } catch (Exception $e) {
            $result = false;
            if( isset($this['uSilex.exceptionHandler'])) {
                $response = call_user_func($this['uSilex.exceptionHandler'], $e, $this);
                call_user_func($this['uSilex.responseEmitter'],$response, $this);
            } else {
                header('X-PHP-Response-Code: '. $e->getCode(), true, 500);
                echo $e->getMessage();
            }
        }
          
        return $result;
    }
    
}