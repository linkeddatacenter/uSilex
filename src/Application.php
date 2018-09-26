<?php 
namespace uSILEX;

use uSILEX\Exception\HttpExceptionInterface;
use uSILEX\Exception\NotFoundHttpException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\StreamedResponse;
use Symfony\Component\HttpFoundation\JsonResponse;
use Pimple\Container;
use Pimple\ServiceProviderInterface;


/*
 * Inspired from Silex application
 */
class Application extends Container
{    
    const VERSION = '1.0.0';
    
    protected $providers = [];
    protected $routes = [];
    protected $booted = false;

    /**
     * Instantiate a new Application.
     *
     * Objects and parameters can be passed as argument to the constructor.
     *
     */
    public function __construct()
    {
        parent::__construct();
        $this['debug'] = false;
        $this['version'] = static::VERSION;
        $this['error_msg.short_template'] = 'Error %s (%s)';
        $this['error_msg.full_template'] = "Error %s (%s) - %s \nTrace info:\n%s\n";
        $this['RouteMatcher'] = function($c) {
            return new RouteMatcher($c);
        };
        $this['ControllerResolver'] = function($c) {
            return new ControllerResolver($c);
        };
    }
    
    
    /**
     * Transform a PHP exception into an http error message.
     *
     * @param \Exception $e a trapped exception
     *
     */
    protected function exceptionToResponse(\Exception $e): Response
    {
        if ($e instanceof  HttpExceptionInterface) {
            $response = new Response($e->getMessage(), $e->getStatusCode());            
        } else {
            $response = new Response(
                $this['debug']
                    ?sprintf($this['error_msg.full_template'], $e->getCode(), $this['version'], $e->getMessage(), $e->getTraceAsString())
                    :sprintf($this['error_msg.short_template'], $e->getCode(), $this['version']),
                Response::HTTP_INTERNAL_SERVER_ERROR
            );
        }
        
        return $response;
    }
    

    public function addRoute(Route $route): Container
    {
        $this->routes[] = $route;
        
        return $this;
    }
    
    
    public function getRoutes(): array
    {
        return $this->routes;
    }
    
    
    public function handleRequest(): Response 
    {
        assert(isset($this['ControllerResolver']));
        assert(isset($this['request']));
        
        try {
            if (!$this->booted) {
                $this->boot();
            }
            
            
            // execute the controller action
            $controllerService = $this['ControllerResolver']->getController();
            
            assert(isset($this[$controllerService]));
            
            // call on_route_match hook
            if (isset($this['on_route_match'])) {
                $this['on_route_match.result'] = $this['on_route_match'];
            }
            
            $response = $this[$controllerService];
              
            
        } catch (Exception $e) {
            $response = $this->exceptionToResponse($e);
        }
        
        return $response;
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
     * Creates a streaming response.
     *
     * @param mixed $callback A valid PHP callback
     * @param int   $status   The response status code
     * @param array $headers  An array of response headers
     *
     * @return StreamedResponse
     */
    public function stream($callback = null, $status = 200, array $headers = [])
    {
        return new StreamedResponse($callback, $status, $headers);
    }
    
    
    /**
     * Convert some data into a JSON response.
     *
     * @param mixed $data    The response data
     * @param int   $status  The response status code
     * @param array $headers An array of response headers
     *
     * @return JsonResponse
     */
    public function json($data = [], $status = 200, array $headers = [])
    {
        return new JsonResponse($data, $status, $headers);
    }
    
    
    /**
     * Handles the request and delivers the response.
     *
     */
    public function run()
    {
        // define $this['request'] just for testing purposes
        if (!isset($this['request'])) {
            $this['request'] = Request::createFromGlobals();
        }
        
        // define $this['response'] just for testing purposes
        if (!isset($this['response'])) {
            $this['response'] = $this->handleRequest();
        }
        
        // call on_response hook
        if (isset($this['on_response'])) {
            $this['response'] = $this['on_response'];
        }
        
        // define $this['uSILEX_IGNORE_SEND'] just for unit testing purposes
        if (!(isset($this['uSILEX_IGNORE_SEND']) && $this['uSILEX_IGNORE_SEND'])) {
            $this['response']->send();
        } 
        
    }
    
}