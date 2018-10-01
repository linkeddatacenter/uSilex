![µSilex logo](logo.png)

µSilex
======

[![Latest Version on Packagist](https://img.shields.io/packagist/v/linkeddatacenter/uSilex.svg?style=flat-square)](https://packagist.org/packages/linkeddatacenter/usilex)
[![Software License](https://img.shields.io/badge/license-MIT-brightgreen.svg?style=flat-square)](LICENSE)
[![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/linkeddatacenter/uSilex/badges/quality-score.png?b=master)](https://scrutinizer-ci.com/g/linkeddatacenter/uSilex/?branch=master)
[![Code Coverage](https://scrutinizer-ci.com/g/linkeddatacenter/uSilex/badges/coverage.png?b=master)](https://scrutinizer-ci.com/g/linkeddatacenter/uSilex/?branch=master)
[![Build Status](https://scrutinizer-ci.com/g/linkeddatacenter/uSilex/badges/build.png?b=master)](https://scrutinizer-ci.com/g/linkeddatacenter/uSilex/build-status/master)


µSilex (aka micro silex) is a micro framework inspired by Pimple and PSR standards. All with less than 100 lines of code!

This project is a try to build a standard middleware framework for developing micro-services and
APIs endpoints that require maximum performances with a minimum of memory footprint.

Why [Pimple](https://pimple.symfony.com/)? Because it is lazy, consistent, fast, elegant and small (about 80 lines of code). What else? 

Why [PSR standards](https://www.php-fig.org/psr)? Because it is a successful community project with a lot of good introperable implementations (psr15-middlewares, zend stratigility, Guzzle, etc. etc.).

Why µSilex? Silex was a great framework now abandoned in favour of Symfony + Flex. This is good when you need more power and flexibility. But you have to pay a price in terms of complexity and memory footprint. 
µSilex it is a new project that covers a small subset of the original Silex project: a µSilex Application is just a Pimple Container implementing all [PSR-15 specifications](https://www.php-fig.org/psr/psr-15/). That's it. 

As a matter of fact, in the JAMStack, Docker and XaaS era, you can let lot of conventional framework features to other components in the system application architecture (i.e. caching, authentication, security, monitoring, etc. etc).

Is µSilex a replacement of Silex? No, but it could be used to build your own "Silex like" framework .

Ask not why nobody is doing this. You are the "nobody"!

Have a nice day!


## Install

`compose require linkeddatacenter/usilex`


## Usage

Basically a µSilex provides the class **Application** that is a Pimple container that implements the PSR-15 middleware interface.

µSilex is not bound to any specific other specific implementations (apart from Pimple);
instead µSilex relay to PSR specifications. In particular µSilex uses PSR-7 specifications for http messages, PSR-15 for managing http handles and middleware and PSR-11 for containers.
 
For this reason, to bind µSilex with specific interface specifications, you need to configure some entries in the conainer:

- **uSilex.request**: a service that instantiate an implementation of PSR-7 server request object 
- **uSilex.responseEmitter**: an optional parameter containing a callable that echoes the http. If not provided, no output is generated. 
- **uSilex.exceptionHandler** a service that generates an http response from a PHP Exception. If not provided just an http 500 header with a text body is ouput
- **uSilex.httpHandler**: a service that instantiate an implementation of PSR-15 http handler

Beside the PSR-15 middleware *process* method, µSilex Application exposes the *run* method that realize typical server process workflow:
- creates a request using uSilex.request service
- calls the uSilex.httpHandler
- emits the http response calling uSilex.responseEmitter

If some php exceptions are thrown in the process, they are translated in Response and then  emitted.

Here is the signature for uSilex.responseEmitter:

```php
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;
use Pimple\Container;

function (
    Request $request, // the request
    Container $options  // the container (optional)
)  {
	echo ....
}

```



There are tons of libraries that implement great reusable middleware that are fully compatible with µSilex. For example see [MW library](https://github.com/middlewares/psr15-middlewares)) and lot of great PSR-7 implementations that match µSilex requirements. µSilex is also compatible with lot of Silex Service Providers and with some Silex Application traits.

You can create your custom framework just selecting the the components that fit your needs. 

out-of-the-box µSilex give to you a set of Service Providers (in the src/Provider directory ) 


µSilex also provides the *Psr11Trait* as an helper to declare a constructor to inject a  Pimple Container with PSR11 interface in any class.

This example uses the [Relay](http://relayphp.com/2.x) library for PSR-15 http handle provider and [Diactoros](https://docs.zendframework.com/zend-diactoros/) for PSR-7 http messages.

```php
<?php
require_once __DIR__.'/../vendor/autoload.php';
use uSilex\Application;
use Zend\Diactoros\Response\TextResponse;
use Zend\Diactoros\ServerRequestFactory;
use Relay\Relay;

$app = new Application;
$app['uSilex.request'] = function() { return ServerRequestFactory::fromGlobals();};
$app['uSilex.responseEmitter'] = $app->protect( function($response) {echo $response->getBody();});
$app['message'] = 'hello world!';
$app['uSilex.httpHandler'] = function($app) { 
    return new Relay([ 
        function() use($app){ return new TextResponse($app['message']);}
    ]); 
};
$app->run();
```

See more examples in the html directory.


## Testing 

Using docker:

	$ docker run --rm -ti -v $PWD/.:/app composer bash
	$ composer install
	$ vendor/bin/phpunit
	$ exit
	$ docker run -d -p 8000:80 --name apache -v $PWD/.:/var/www/ php:apache

Point your browser to:

- http://localhost:8000/example1.php
- http://localhost:8000/example2.php
- http://localhost:8000/example3.php/hello/world

Destroy the container:

	$ docker rm -f apache


## Credits

µSilex is inspired to the following projects:

- https://github.com/php-fig/fig-standards/
- https://github.com/pimple/pimple and https://github.com/silexphp/Silex projects by Fabien Potencier
- https://github.com/relayphp/Relay.Relay project
