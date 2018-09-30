<?php
/**
 * Here your routes
 */       

use Zend\Diactoros\Response\TextResponse;


$routeMap->get('home', '/', function () {
    return new TextResponse("This is the home. Try '/hello/world'");
});
    
$routeMap->get('hello', '/hello/{name}', function ($request) {
    $name = $request->getAttribute('name');
    return new TextResponse("Hello $name");
});
