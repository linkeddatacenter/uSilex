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

use Pimple\ServiceProviderInterface;

/**
 * BootManager trait. Manages Bootable providers
 *
 * @author Enrico Fagnoni <enrico@linkeddata.center>
 */
trait BootManagerTrait
{

    protected $providers = [];
    protected $booted = false;
    
    
    /**
     * Redefine Registers a service provider.
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
            if ( ($provider instanceof ServiceProviderInterface) && method_exists($provider,'boot') ) {
                $provider->boot($this);
            }
        }
        
        return $this;
    }
}
