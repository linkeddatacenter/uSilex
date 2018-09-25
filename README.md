# µSILEX
µSSILEX ( aka micro silex)  is a super micro kernel based on Pimple and http_foundation Symfony core classes.

Silex was a great project abbandoned because it can be substituted with Symfony with Flex. This is true only if you need more or the 
just the same power of Silex.  But you have to pay a price in terms of memory footprint and performances. 
µSilex covers a subset of the original Silex projecy: no caching, no security and authentication, no middleware, no event,
no views, no template etc, etc. 
In the JAMStack, Docker and XaaS era, you can let these features to other components in the system application architecture.

This micro framework is a try to realize a framework for developing APIs endpoints that requires maximum performances
with a minimum of memory footprint (e.g. smart proxies, gateway, adaptors, etc, etc).


## Install

`compose require linkeddatacenter/usilex`

## Usage:

```
<?php
require_once __DIR__.'/vendor/autoload.php';
$app = new \uSILEX\Core\Application;

$app['say_hello_controller']= function ($app) {
   return $app->json(['hello', 'world']);
}
$app->route('GET', '/', 'say_hello_controller');
$app->run();
```

See example dir.

## Credits

uSILEX is inspired form https://symfony.com/doc/current/components/http_foundation.html
and https://github.com/silexphp/Silex
