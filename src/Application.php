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
use Exception;

/**
 * The uSilex framework class.
*/
class Application extends Container
{
    
    protected $providers = [];
    protected $booted = false;
    
    
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
            return this;
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
    public function run() : bool
    {
        try {
            if( !isset($this['response.emit'])){
                $this['response.emit'] =  $this->protect( function(){} );
            }
            
            $this->boot();
            $response = $this['kernel']->handle($this['request']);
           
            call_user_func($this['response.emit'],$response);
            
            $result = true;
        } catch (Exception $e) {
            $result = false;
            if( isset($this['uSilex.errorManagement']) && !$this['uSilex.errorManagement']) {
                throw $e;
            } elseif (isset($this['uSilex.errorManagement'])) {
                call_user_func($this['uSilex.errorManagement'],$e);
            } else {
                header('X-PHP-Response-Code: '. $e->getCode(), true, 500);
                echo "Internal error. " . $e->getMessage();
                $this['uSilex.panic.error'] =  $e;
            }
        }
          
        return $result;
    }
    
}