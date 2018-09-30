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
 * Participant in processing a response message
 *
 * It is similar to an HTTP middleware FIG-15 component but it participates in the postprocessing of an HTTP response:
 * by acting on the response, generating a new response, or forwarding the
 * response to a subsequent response postprocessor.
 * It is similar to FIG PSR-15 specification
 */
interface ResponseProcessorInterface
{
    /**
     * Process an response message
     *
     * Processes a response messge in order to produce a modified version of the response.
     * If unable to produce the response itself, it may delegate to the provided
     * request handler to do so.
     */
    public function process(ResponseInterface $response, ResponseHandlerInterface $handler): ResponseInterface;
}