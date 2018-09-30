<?php
namespace examples\routing;

require_once __DIR__.'/../vendor/autoload.php';
require_once __DIR__.'/conf.php';

use uSilex\Application;

$app = new Application;
$app->register(new ServiceConfiguration);

// here an example of how to change default registered options and services.
$app['basepath'] = '/examples/routing/index.php';
$app['routefile'] = 'routes.php'.
    
$app->run();