<?php
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
