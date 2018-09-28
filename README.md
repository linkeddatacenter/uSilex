µSilex
======

[![Latest Version on Packagist](https://img.shields.io/packagist/v/linkeddatacenter/uSilex.svg?style=flat-square)](https://packagist.org/packages/linkeddatacenter/usilex)
[![Software License](https://img.shields.io/badge/license-MIT-brightgreen.svg?style=flat-square)](LICENSE)
[![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/linkeddatacenter/uSilex/badges/quality-score.png?b=master)](https://scrutinizer-ci.com/g/linkeddatacenter/uSilex/?branch=master)
[![Code Coverage](https://scrutinizer-ci.com/g/linkeddatacenter/uSilex/badges/coverage.png?b=master)](https://scrutinizer-ci.com/g/linkeddatacenter/uSilex/?branch=master)
[![Build Status](https://scrutinizer-ci.com/g/linkeddatacenter/uSilex/badges/build.png?b=master)](https://scrutinizer-ci.com/g/linkeddatacenter/uSilex/build-status/master)

µSilex (aka micro silex) is a super micro framework inspired on Pimple and PSR standard.

This project is a try to build a standard conceptual framework for developing micro-services and
APIs endpoints that require maximum performances with a minimum of memory footprint.


Silex was a great project that now migrated to Symfony + Flex. This is good when if you need more power and flexibility. But you have to pay a price in terms of complexity and memory footprint.

µSilex covers a small subset of the original Silex projecy: a µSilex is just a Pimple container that implements [PSR-15 specifications](https://www.php-fig.org/psr/psr-15/). That's it. 


As a matter of fact, in the JAMStack, Docker and XaaS era, you can let lot of conventional framework features to other components in the system application architecture (i.e. caching, authentication, security, monitoring, etc. etc). 

Beside this, there are tons of libraris that implement great reusable middleware that are fully compatible with µSilex (e.g. [MW library](https://github.com/middlewares/psr15-middlewares)) and lot of great PSR implementations that fit µSilex requirements.


Have a nice day!

## Install

`compose require linkeddatacenter/usilex`

## Usage

To run µSilex *Application* you require to register at least a Pimple service that instantiates  a *Middleware* object. The object must implement Psr\Http\Server\MiddlewareInterface. 

All registered middleware services must be explicitly register with the Application method *registerAsMiddleware*. 

Runtime, all Middleware services will be executed in the registration order.

This example use the zend Diactoros PSR-7 concrete implementation:

```
require_once __DIR__.'/../vendor/autoload.php';

use uSILEX\Application;
use Zend\Diactoros\ServerRequestFactory;
use Zend\Diactoros\Response\TextResponse;

$app = new Application;
$app['request'] = ServerRequestFactory::fromGlobals();
$app['responseEmitter'] = 'print_r';
$app['hello-world'] = new TextResponse('Hello world!');
$app->registerAsMiddleware('hello-world');

$app->run();
```


µSilex Application provides an helper function *run* that realize a typical http server application workflow:

1. create a request from server variable
2. call the handler to execute all registered middleware ( usually you will define at least an error handler and a router)
3. do some post processing to the handle response
3. send the response to client (emit)

Before to call the *run* method you must register following Pimple services:

- *request*  that instantiates an object implementing PSR-7 ServerRequestInterface 
- *responseEmitter*  that instantiates an object that implements the emit($response) method.  

After calling the request  handler (i.e. executing middlewares), the resulting response will be stored into  $app['response']. The *run* method will finally execute in sequence all Pimple service you registered with the method *onResponse*.
This is an unique opportunity to change the response before it is sent back to the http client using your response emitter.


See more in the [examples](examples/README.md) dir.


## Testing and examples

Using docker:

	$ docker run --rm -ti -v $PWD/.:/app composer bash
	$ composer install
	$ vendor/bin/phpunit
	$ cd examples
	$ composer install
	$ exit
	$ docker run -d -p 8000:80 --name apache -v $PWD/.:/var/www/html php:apache

Point your browser to

- http://localhost:8000/examples/simple/
- http://localhost:8000/examples/aura_routing/index.php/hello/world

Destroy the container:

	$ docker rm -f apache


## Credits

µSilex is inspired form:

- All PSR standards
- https://github.com/pimple/pimple and https://github.com/silexphp/Silex projects by Fabien Potencier
- https://github.com/relayphp/Relay.Relay project
