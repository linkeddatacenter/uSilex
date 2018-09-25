# µSILEX
µSILEX (aka micro silex)  is a super micro framework based on Pimple and Symfony http_foundation  classes.

Silex was a great project now migrated to Symfony and Flex. This is good when if you need more power and flexibility.  
But you have to pay a price in terms of complexity and memory footprint.

µSilex covers a small subset of the original Silex projecy: no caching, no security, no authentication, no middleware, no event, no views, no template, etc, etc. 

As a matter of fact, in the JAMStack, Docker and XaaS era, you can let all these features to other components in the system application architecture.

This project is a try to build a framework for developing APIs endpoints 
that require maximum performances
with a minimum of memory footprint (e.g. micro services, smart proxies, gateway, adaptors, etc, etc).

Have a nice day!

## Install

`compose require linkeddatacenter/usilex`

## Usage:

```
<?php
require_once __DIR__.'/../vendor/autoload.php';

// create a new application
$app = new \uSILEX\Application;

// create a service using pimple to be used as controller
$app['say_hello_controller']= function ($app) {
   return $app->json(['hello', 'world']);
};

// define a route
$app->addRoute(new \uSILEX\Route('GET', '/', 'say_hello_controller'));

//run the application
$app->run();
```

The routing capability depends mainly from the RouteMatcher service. 
Write your own Classe to fulfill your needs and register it as a service:

```
...
$app = new \uSILEX\Application;
$app['RouteMatcher'] = function ($c) {
    return new MyVerySpecialRouteMatcher($c);
};
...
```

See example dir.

## Testing

Using docker:

	$ docker run --rm -ti -v $PWD/.:/app composer install
	$ docker run --rm -ti -v $PWD/.:/app composer vendor/bin/phpunit
	$ docker run -d --name apache -v $PWD/.:/var/www/html php:apache
	$ docker exec -t apache curl http://localhost/examples/
	["hello","world"]
	$ docker rm -f apache

## Credits

uSILEX is inspired form https://symfony.com/doc/current/components/http_foundation.html
and https://github.com/silexphp/Silex
by Fabien Potencier <fabien@symfony.com>
