<?php
namespace examples\routing;
$time_start = microtime(true);

require_once __DIR__.'/../vendor/autoload.php';
require_once __DIR__.'/example3/MiddlewaresServiceProvider.php';

use uSilex\Application;
use uSilex\Provider\Psr15\RelayServiceProvider;
use uSilex\Provider\Psr7\DiactorosServiceProvider;

$app = new Application;
$app->register(new RelayServiceProvider());
$app->register(new DiactorosServiceProvider());
$app->register(new MiddlewaresServiceProvider);

// here an example of how to change default registered options and services.
$app['basepath'] = '/example3.php';
$app['routefile'] = 'routes.php'.
    

$app->run();

echo "\n<pre>";
echo "\nmemory_get_usage: ".memory_get_usage();
echo "\nscript execution time:".(microtime(true) - $_SERVER["REQUEST_TIME_FLOAT"]);
echo "<pre>";