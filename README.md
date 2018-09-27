µSilex
======

[![Latest Version on Packagist](https://img.shields.io/packagist/v/linkeddatacenter/uSilex.svg?style=flat-square)](https://packagist.org/packages/linkeddatacenter/usilex)
[![Software License](https://img.shields.io/badge/license-MIT-brightgreen.svg?style=flat-square)](LICENSE)
[![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/linkeddatacenter/uSilex/badges/quality-score.png?b=master)](https://scrutinizer-ci.com/g/linkeddatacenter/uSilex/?branch=master)
[![Code Coverage](https://scrutinizer-ci.com/g/linkeddatacenter/uSilex/badges/coverage.png?b=master)](https://scrutinizer-ci.com/g/linkeddatacenter/uSilex/?branch=master)
[![Build Status](https://scrutinizer-ci.com/g/linkeddatacenter/uSilex/badges/build.png?b=master)](https://scrutinizer-ci.com/g/linkeddatacenter/uSilex/build-status/master)

µSilex (aka micro silex)  is a super micro framework inspired on Pimple and Symfony http_foundation  classes.

Silex was a great project that now migrated to Symfony + Flex. This is good when if you need more power and flexibility. But you have to pay a price in terms of complexity and memory footprint.

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
namespace uSILEX;
require_once __DIR__.'/../vendor/autoload.php';
$app = new Application;
$app->addRoute(new Route('GET', '/', 'say_hello'));
$app['say_hello']= function (Application $app) {
   return $app->json(['hello', 'world']);
};
$app->run();
```

A uSILEX\Application is just a Pimple\Container with few extra features (like in the good old Silex package).

## Routing

The default routing policy of µSILEX does not support uri templates, it relies on regexp.

The first argument of a route matches the http verb (case insensitive). 
The second argument matches the request path
The last argument is a service name that must be defined runtime.

You do not have to use delimeter and qualifier in Route: we use hash (#) for you;  '^' is inserted at the beginning of regexp and 
'$' is always inserted at the end. 
This means that the hash character (#) need to be escaped.

For example in `New Route('GET|POST', '/id/([0-9]+)', 'my_controller' )` the first argument is evaluated as the regexp `#^GET|POST$#i` and the second argument as `#^/id/([0-9]+)$#` . In the controller service you can access regexp capturing group in $app['request.matches'] variable and the matched route in $app['request.route'].

Note that $app['request.matches'][0] always matches the whole route path,  
$app['request.matches'][1] is the first group (if any, etc etc.).


In controllers, *$app['request.route']* contains the matched route and  *$app['request.matches']* contains the  regular expression  matches results on the route path (i.e. internally something similar to `preg_match($this->app['request.route']->getPath(),$this->app['request']->getPathInfo(),$this->app['request.matches'])` is used) and . For example:

	$app->addRoute(new Route('GET', '/id/([0-9]+)', 'my_controller'));	
	$app['my_controller'] = function (Application $a) {
		$pfirst = $a['request.matches][1];
		return new Response( "$method $p1 id");
	}


## Customize routing

The routing capability depends mainly from the *$app['RouteMatcher']* service. 
You can also redefine the *$app['ControllerResolver']* service to change all the routing strategy.

Write your own classess that fulfill your needs and register them as a service before running the application.


## middleware

The middleware management capability is limited to a couple listener:

	$app->onRouteMatch( 'service-name' );

The 'service-name'service will be called when a request  matches a  route. If the default ControllerResolver implementation is used, then, inside hooked service you can access 
$app['request.matches'] and $app['request.route'].

	$app->onResponse( 'service-name' );

The 'service-name' service is called after a successful  controller execution an must return a valid
Response object.
  
$app['response'] contains the the response returned by the executed controller and will be overidden
by onResponse Hook.
This is an unique opportunity to change the response before it is sent back to the http client.

You can hook multiple services to `onRouteMatch` and to `onResponse`. They will be executed in the registration order.

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
