µSilex
======

[![Latest Version on Packagist](https://img.shields.io/packagist/v/linkeddatacenter/uSilex.svg?style=flat-square)](https://packagist.org/packages/linkeddatacenter/usilex)
[![Software License](https://img.shields.io/badge/license-MIT-brightgreen.svg?style=flat-square)](LICENSE)
[![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/linkeddatacenter/uSilex/badges/quality-score.png?b=master)](https://scrutinizer-ci.com/g/linkeddatacenter/uSilex/?branch=master)
[![Code Coverage](https://scrutinizer-ci.com/g/linkeddatacenter/uSilex/badges/coverage.png?b=master)](https://scrutinizer-ci.com/g/linkeddatacenter/uSilex/?branch=master)
[![Build Status](https://scrutinizer-ci.com/g/linkeddatacenter/uSilex/badges/build.png?b=master)](https://scrutinizer-ci.com/g/linkeddatacenter/uSilex/build-status/master)

µSilex (aka micro silex) is a super micro framework inspired on Pimple and PSR standards.

This project is a try to build a standard conceptual framework for developing micro-services and
APIs endpoints that require maximum performances with a minimum of memory footprint.

Why [Pimple](https://pimple.symfony.com/)? Because it is lazy, consistent, elegant, small (about 80 lines of code). What else? 

Why [PSR standards](https://www.php-fig.org/psr)? Because it is a successful community effort rhat produced many good implementations.

Why µSilex? Silex was a great framework  that now abandoned because it moved to Symfony + Flex. This is good when you need more power and flexibility. But you have to pay a price in terms of complexity and memory footprint.

µSilex covers a small subset of the original Silex project: a µSilex is just a Pimple container that implements modular [PSR specifications](https://www.php-fig.org/psr). That's it. µSilex inflates Pimple with just from 20 to 100 extra lines of code (dependings from your needs)!

As a matter of fact, in the JAMStack, Docker and XaaS era, you can let lot of conventional framework features to other components in the system application architecture (i.e. caching, authentication, security, monitoring, etc. etc). 

Beside this, there are tons of libraries that implement great reusable middleware that are fully compatible with µSilex. For example see [MW library](https://github.com/middlewares/psr15-middlewares)) and lot of great PSR-7 implementations that match µSilex requirements. µSilex is also compatible with lot of Silex Service Providers and with some Silex Application traits.

Basically µSilex it is composed by a set of traits that implements specific interfaces and two wrapper classes:

- **BootableContainer**: a Pimple container .
- **HttpKernel**: a Pimple container that implements PDR-15 specifications (i.e. HTTP Handlers with middleware capability) with response post-processing capability.
- **Application**: that is an httpKernel with support to bootable service providers + the method *run*. More or less this class  is a Silex\Application subset that do not support routing and silex middleware (we use PSR-15 middleware).

You can create your framework just composing the available Kernel traits and middlewares. Probably you could create a full Silex compatible framework just implementing the missing components (routing, Symphony Events, etc. etc.) 

Ask not why nobody is doing the full compatibility with Silex. You are the "nobody"!

Have a nice day!

## Install

`compose require linkeddatacenter/usilex`

## Usage

To run µSilex *Application* you require to register at least a Pimple service that instantiates  a *Middleware* object. The object must implement Psr\Http\Server\MiddlewareInterface.
All registered middleware services must be explicitly register with the method *registerAsMiddleware*.
Middlewre management capability is realized by \uSilex\Kernel\Psr1 trait. 

This example uses the zend Diactoros PSR-7 concrete implementation:

	<?php
	require_once __DIR__.'/../vendor/autoload.php';
	use uSilex\Application;
	use Psr\Http\Message\ResponseInterface;
	use Psr\Http\Message\ServerRequestInterface;
	use Psr\Http\Server\RequestHandlerInterface;
	use Psr\Http\Server\MiddlewareInterface;
	use Zend\Diactoros\ServerRequestFactory;
	use Zend\Diactoros\Response\TextResponse;
	
	$app = new Application;
	
	$app['request'] = function() {
	    return ServerRequestFactory::fromGlobals();
	};
	$app['responseEmitter'] = $app->protect( function($response) {
	    echo $response->getBody();
	});
	
	$app['message'] = 'hello world!';
	$app['say_hello'] = function( Application $app) {
	    return new class($app) implements MiddlewareInterface {
	        use \uSilex\Psr11Trait;
	        public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface {
	            return new TextResponse( $this->get('message'));
	        }
	    };
	};


$app->run('say_hello');


The µSilex Application helper function *run* that realizes a typical http server application workflow:

1. creates a request from server variables
2. calls the handler to execute all registered middleware ( usually you will define at least an error handler and a router
3. post processes to the handler response (if needed)
4. sends the response to client (emit))

Before to call the *run* method you must register following Pimple services:
- *request*  that instantiates an object implementing PSR-7 ServerRequestInterface
- *responseEmitter*  that instantiates an object that write on STDOUT a response. 
If not provided, the run implementation just render the response with var_dump. 

An Application instance must register at least a middleware component that produces a response.

After calling the request  handler (i.e. executing middlewares). The *run* method will execute in all response processor services you registered with the method *onResponse*.
This is an unique opportunity to change the response before it is sent back to the http client through the configured emitter service.

See more in the [examples](examples/README.md) dir.

Before to call handler, you need to call

## Testing and examples

Using docker

	$ docker run --rm -ti -v $PWD/.:/app composer bash
	$ composer install
	$ vendor/bin/phpunit
	$ cd examples
	$ composer install
	$ exit
	$ docker run -d -p 8000:80 --name apache -v $PWD/.:/var/www/html php:apachee

Point your browser to:

- http://localhost:8000/examples/simple
- http://localhost:8000/examples/aura_routing/index.php/hello/world

Destroy the container:

	$ docker rm -f apache

## Credits

µSilex is inspired to the following projects:

- https://github.com/php-fig/fig-standards/
- https://github.com/pimple/pimple and https://github.com/silexphp/Silex projects by Fabien Potencier
- https://github.com/relayphp/Relay.Relay projectt
- https://github.com/relayphp/Relay.Relay
