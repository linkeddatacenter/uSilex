µSilex
======

[![Latest Version on Packagist](https://img.shields.io/packagist/v/linkeddatacenter/uSilex.svg?style=flat-square)](https://packagist.org/packages/linkeddatacenter/usilex)
[![Software License](https://img.shields.io/badge/license-MIT-brightgreen.svg?style=flat-square)](LICENSE)
[![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/linkeddatacenter/uSilex/badges/quality-score.png?b=master)](https://scrutinizer-ci.com/g/linkeddatacenter/uSilex/?branch=master)
[![Code Coverage](https://scrutinizer-ci.com/g/linkeddatacenter/uSilex/badges/coverage.png?b=master)](https://scrutinizer-ci.com/g/linkeddatacenter/uSilex/?branch=master)
[![Build Status](https://scrutinizer-ci.com/g/linkeddatacenter/uSilex/badges/build.png?b=master)](https://scrutinizer-ci.com/g/linkeddatacenter/uSilex/build-status/master)

µSilex (aka micro silex) is a micro framework inspired by Pimple and PSR standards. All can you with less tha 100 lines of code!

This project is a try to build a standard conceptual framework for developing micro-services and
APIs endpoints that require maximum performances with a minimum of memory footprint.

Why [Pimple](https://pimple.symfony.com/)? Because it is lazy, consistent, elegant, small (about 80 lines of code). What else? 

Why [PSR standards](https://www.php-fig.org/psr)? Because it is a successful community project with a lot of good implementations.

Why µSilex? Silex was a great framework now abandoned in favour of Symfony + Flex. This is good when you need more power and flexibility. But you have to pay a price in terms of complexity and memory footprint. 

µSilex it is a new project that covers a small subset of the original Silex project: a µSilex is just a Pimple container enabling the reuse of implementations that follow [PSR specifications](https://www.php-fig.org/psr). That's it. µSilex inflates Pimple with just about 30 lines of code!

As a matter of fact, in the JAMStack, Docker and XaaS era, you can let lot of conventional framework features to other components in the system application architecture (i.e. caching, authentication, security, monitoring, etc. etc).

Is µSilex a replacement of Silex? No, but it could be used to build one.

Ask not why nobody is doing this. You are the "nobody"!

Have a nice day!


## Install

`compose require linkeddatacenter/usilex`


## Usage

Basically a µSilex provides the class **Application** that is a Pimple container with few extra features.

µSilex is not bound to any specific other specific implementation (apart from Pimple);
instead µSilex relay to PSR specifications. In particular µSilex uses PSR-7 specifications for http messages, PSR-15 for managing http handles and middleware and PSR-11 for containers. 
 
For this reason, you need to configure some entries:

- **$app['request']**: should contain the server http request
- **$app['response.emit']**: an optional function that echoes the http response provided as parameter. If not provided, no output is sent back.
- **$app['kernel']**: a service that instantiate an implementation of PSR-15 http handler

µSilex Application provides the *run* method that calls the kernel request handler.

The run method is protected by a basic error handler from PHP errors. If something goes wrong, it emits a http 500 state; the option *uSilex.panic.error* contains the trapped php exception. You can disable the embedded error management defining the option ['uSilex.errorManagement'] = false. In this case you have to catch exceptions. You can also define your custom errorManagement defining a function that accept a PHP Exception and assigning it to 'uSilex.errorManagement' option.

There are tons of libraries that implement great reusable middleware that are fully compatible with µSilex. For example see [MW library](https://github.com/middlewares/psr15-middlewares)) and lot of great PSR-7 implementations that match µSilex requirements. µSilex is also compatible with lot of Silex Service Providers and with some Silex Application traits.

You can create your custom framework just selecting the the components that fit your needs. 

This example uses the [Relay](http://relayphp.com/2.x) library for PSR-15 http handle provider and [Diactoros](https://docs.zendframework.com/zend-diactoros/) for PSR-7 http messages.

	<?php
	require_once __DIR__.'/../vendor/autoload.php';
	use uSilex\Application;
	use Psr\Http\Message\ResponseInterface;
	use Psr\Http\Message\ServerRequestInterface;
	use Psr\Http\Server\RequestHandlerInterface;
	use Psr\Http\Server\MiddlewareInterface;
	
	class MyRequestProcessor implements MiddlewareInterface {
	    use \uSilex\Psr11Trait;
	    
	    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
	    {
	        return new \Zend\Diactoros\Response\TextResponse( $this->get('message'));
	    }
	}  
	
	$app = new Application;
	$app['request'] = \Zend\Diactoros\ServerRequestFactory::fromGlobals();
	$app['response.emit'] = $app->protect( function($response) {echo $response->getBody();});
	$app['message'] = 'hello world!';
	$app['kernel'] = new \Relay\Relay([ new MyRequestProcessor($app)]);
	$app->run();


See more in the [examples](examples/README.md) dir.


## Testing and running examples

Using docker:

	$ docker run --rm -ti -v $PWD/.:/app composer bash
	$ composer install
	$ vendor/bin/phpunit
	$ cd examples
	$ composer install
	$ exit
	$ docker run -d -p 8000:80 --name apache -v $PWD/.:/var/www/html php:apachee

Point your browser to:

- http://localhost:8000/examples/simple/
- http://localhost:8000/examples/routing/index.php/hello/world

Destroy the container:

	$ docker rm -f apache

## Credits

µSilex is inspired to the following projects:

- https://github.com/php-fig/fig-standards/
- https://github.com/pimple/pimple and https://github.com/silexphp/Silex projects by Fabien Potencier
- https://github.com/relayphp/Relay.Relay project
