µSilex
======
[![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/linkeddatacenter/uSilex/badges/quality-score.png?b=master)](https://scrutinizer-ci.com/g/linkeddatacenter/uSilex/?branch=master)
[![Code Coverage](https://scrutinizer-ci.com/g/linkeddatacenter/uSilex/badges/coverage.png?b=master)](https://scrutinizer-ci.com/g/linkeddatacenter/uSilex/?branch=master)
[![Build Status](https://scrutinizer-ci.com/g/linkeddatacenter/uSilex/badges/build.png?b=master)](https://scrutinizer-ci.com/g/linkeddatacenter/uSilex/build-status/master)

µSilex (aka micro silex)  is a super micro framework based on Pimple and Symfony http_foundation  classes.

Silex was a great project now migrated to Symfony and Flex. This is good when if you need more power and flexibility.  
But you have to pay a price in terms of complexity and memory footprint.

µSilex covers a small subset of the original Silex projecy: no caching, no security, no authentication, limited middleware,  limited view support, no template, etc, etc. 

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

## Routing

The default routing policy of µSILEX does not support uri templates.

Default routing considers the first and the second argument of a new Route as a regular expression fragments. For instance 'GET' is computed as '#^GET$#' and '/' as '#^/$#' . This means that ^ and $ are always inserted and that the hash character need to be escaped.

In controllers, *$app['request.route']* contains the matched route and  *$app['request.matches']* contains the  regular expression  matches results on the route path (i.e. internally something similar to `preg_match($this->app['request.route']->getPath(),$this->app['request']->getPathInfo(),$this->app['request.matches'])` is used) and . For example:

	$app->addRoute(new Route('(GET|POST)', '/id/([0-9]+)', 'my_controller'));	
	$app['my_controller'] = function (Application $a) {
		$method = $a['request']->getMethod();
		$p1 = $a['request.matches][1];
		return new Response( "$method $p1 id");
	}


## Customize routing

The routing capability depends mainly from the RouteMatcher service. 
Write your own class that fulfill your needs and register it as a service:

```
...
$app = new \uSILEX\Application;
$app['RouteMatcher'] = function ($c) {
    return new MyVerySpecialRouteMatcherImplementation($c);
};
...
```

You can also redefine the $app['ControllerResolver'] service to change all the routing strategy.


## middleware

The middleware management capability is limited to a couple hooked services:

### on_route_match

	$app['on_route_match'] = function(Application $app){ };
 
this service is called when a request  matches a  route. If the default ControllerResolver implementation is used, then, inside on_route_match service you can access 
$app['request.matches'] and $app['request.route'].

'request.match.result' and 'request.match.route' are also available inside any controller.

### on_response

	$app['on_response'] = function(Application $app){ };

this service is called after the controller.  
$app['response'] contains the the response returned by the executed controller.This is an unique opportunity to change response before it is sent back to the client

## Response type

µSilex application supports out of the box a couple of shortcut to the Symphony http_foundation response classes:

- `$app->json` to output json from php data see Symfony\Component\HttpFoundation\JsonResponse;
- `$app->stream` to stream a resource, see use Symfony\Component\HttpFoundation\StreamedResponse;


## Testing

Using docker:

	$ docker run --rm -ti -v $PWD/.:/app composer install
	$ docker run --rm -ti -v $PWD/.:/app composer vendor/bin/phpunit
	$ docker run -d --name apache -v $PWD/.:/var/www/html php:apache
	$ docker exec -t apache curl http://localhost/examples/
	["hello","world"]
	$ docker rm -f apache


## Credits

µSilex is inspired form https://symfony.com/doc/current/components/http_foundation.html
and https://github.com/silexphp/Silex
by Fabien Potencier <fabien@symfony.com>
