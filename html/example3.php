<?php
namespace examples\routing;

require_once __DIR__.'/../vendor/autoload.php';
require_once __DIR__.'/example3_config/conf.php';

use uSilex\Application;
use uSilex\Provider\Psr15\RelayServiceProvider;
use uSilex\Provider\Psr7\DiactorosServiceProvider;

$app = new Application;
$app->register( new RelayServiceProvider());
$app->register( new DiactorosServiceProvider());
$app->register(new ServiceConfiguration);

// here an example of how to change default registered options and services.
$app['basepath'] = '/example3.php';
$app['routefile'] = 'routes.php'.
    

$app->run();