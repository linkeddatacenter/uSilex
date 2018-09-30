<?php

/*
 * This file is part of the uSilex framework.
 *
 * (c) Enrico Fagnoni <enrico@linkeddata.center>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace uSilex\Api;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

/**
 * Handles a response and produces a new response
 *
 * An HTTP response handler process an HTTP response in order to produce a modified or new
 * HTTP response.
 */
interface ResponseHandlerInterface
{
    /**
     * Handles a request and produces a response
     *
     * May call other collaborating code to generate the response.
     */
    public function handleResponse(ResponseInterface $response): ResponseInterface;
}