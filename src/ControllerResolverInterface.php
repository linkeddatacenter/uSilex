<?php
namespace uSILEX;

/*
 * same as https://api.symfony.com/4.1/Symfony/Component/HttpKernel/Controller/ControllerResolverInterface.html
 */

Interface ControllerResolverInterface
{
    public function getController() : Route;
}