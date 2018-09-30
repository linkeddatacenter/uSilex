<?php

/*
 * This file is part of the uSilex framework.
 *
 * (c) Enrico Fagnoni <enrico@linkeddata.center>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace uSilex\Tests;

use PHPUnit\Framework\TestCase;
use Pimple\Container;
use Pimple\ServiceProviderInterface;

class BootManagerTraitTest extends TestCase
{

    public function testBoot()
    {
        $app = new class extends Container {
            use \uSilex\Pimple\BootManagerTrait;
        };
        
        $provider = new class implements ServiceProviderInterface {
            public function register(Container $app){$app['bootme']=false;}
            public function boot(Container $app){ $app['bootme']=true;}
        };
        
        $app->register($provider);
        $app->boot();
        
        $this->assertTrue($app['bootme']);       
    }
    
}
