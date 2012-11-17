<?php
/**
 * @package     Spore
 * @author      Axel Etcheverry <axel@etcheverry.biz>
 * @copyright   Copyright (c) 2012 Axel Etcheverry (http://www.axel-etcheverry.com)
 * @license     MIT
 */

/**
 * @namespace
 */
namespace Spore\Middleware;

use Spore\HttpFoundation\Request;
use Spore\HttpFoundation\Response;
use ArrayObject;

interface MiddlewareInterface
{
    /**
     *
     * @return String
     */
    public function getName();
    
    /**
     *
     * @return Integer
     */
    public function getPriority();

    /**
     * 
     * @param \Spore\HttpFoundation\Request $request
     * @param \ArrayObject $env
     */
    public function processRequest(Request $request, ArrayObject $env);

    /**
     * 
     * @param \Spore\HttpFoundation\Response $response
     * @param \ArrayObject $env
     */
    public function processResponse(Response $response, ArrayObject $env);
}
