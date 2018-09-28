<?php 
namespace uSILEX;

use Pimple\Container;
use Pimple\ServiceProviderInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use TypeError;

class Application extends Container implements RequestHandlerInterface
{    
    protected $providers = [];
    protected $middlewares = [];
    protected $onResponseListeners = [];
    protected $booted = false;

        
    public function getMiddlewares() : array
    {
        return $this->middlewares;
    }
    
    
    public function resetMiddlewaresQueue()  : Application
    {
        reset( $this->middlewares);
        
        return $this;
    }
    
    
    public function registerAsMiddleware(string $servicename) : Application
    {
        $this->middlewares[] = $servicename;
        
        return $this;
    }
    
    
    public function onResponse(string $serviceName)
    {
        $this->onResponseListeners[] = $serviceName;
        
        return $this;
    }
    
    
    public function handle(ServerRequestInterface $request) : ResponseInterface
    {
        if (!$this->booted) {
            $this->boot();
        }
        
        $middlewareServiceName = current($this->middlewares);
        $middleware = $this[$middlewareServiceName];
        next($this->middlewares);      
        
        if( $middleware instanceof MiddlewareInterface ) {
            $result = $middleware->process($request, $this);
        } elseif ( $middleware instanceof ResponseInterface) {
            $result = $middleware;
        } else {
            throw new TypeError;
        }
        
        return $result;
    }
    
    
    public function getProviders()
    {
        return $this->providers;
    }
    
    
    /**
     * Registers a service provider.
     *
     * @param ServiceProviderInterface $provider A ServiceProviderInterface instance
     * @param array                    $values   An array of values that customizes the provider
     *
     * @return Application
     */
    public function register(ServiceProviderInterface $provider, array $values = [])
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
    public function boot()
    {
        if ($this->booted) {
            return;
        }
        
        $this->booted = true;
        
        foreach ($this->providers as $provider) {
            if ($provider instanceof BootableProviderInterface) {
                $provider->boot($this);
            }
        }
    }
    
    
    /**
     * Handles the request and delivers the response.
     *
     */
    public function run()
    {
        
        assert( isset($this['request']) );
        assert( !empty($this->getMiddlewares()) );
        
        
        // define $this['response'] just for testing purposes
        if (!isset($this['response'])) {
            $this->resetMiddlewaresQueue();
            $this['response'] = $this->handle($this['request']);
        }
        
        // call onResponse hook
        foreach( $this->onResponseListeners as $serviceName) {
            $this['response'] = $this[$serviceName];
        }
        
        
        if( isset($this['responseEmitter']) && is_callable($this['responseEmitter'])) {
            call_user_func($this['responseEmitter'],$this['response']);
        }
        
    }
    
}