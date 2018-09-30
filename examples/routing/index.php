<?php
namespace examples\aura_routing;

require_once __DIR__.'/../vendor/autoload.php';
require_once __DIR__.'/ServiceConfiguration.php';

use uSilex\Application;

$app = new Application;
$app->register(new ServiceConfiguration);
$app->run();