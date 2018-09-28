<?php
namespace EXAMPLE;

use Pimple\Container;
use Pimple\ServiceProviderInterface;

use Middlewares\ErrorHandler;

class ErrorHandlingServiceProvider implements ServiceProviderInterface
{

    public function register(Container $app)
    {  
        $app['errorHandling'] = function() {
            return (new ErrorHandler())->catchExceptions(true);
        };
        
        $app->registerAsMiddleware('errorHandling');
    }

}