<?php
namespace uSILEX;


interface RouteMatcherInterface
{
    public function match(Route $route);
}