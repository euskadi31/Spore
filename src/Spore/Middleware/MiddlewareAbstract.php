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

abstract class MiddlewareAbstract implements MiddlewareInterface
{
    /**
     * @var Integer
     */
    protected $priority = 0;

    /**
     * @var String
     */
    protected $name;

    /**
     *
     * @return String
     */
    public function getName()
    {
        return $this->name;
    }
    
    /**
     *
     * @return Integer
     */
    public function getPriority()
    {
        return $this->priority;
    }

    /**
     * 
     * @param \Spore\HttpFoundation\Request $request
     * @param \ArrayObject $env
     */
    public function processRequest(Request $request, ArrayObject $env)
    {
    }

    /**
     * 
     * @param \Spore\HttpFoundation\Response $response
     * @param \ArrayObject $env
     */
    public function processResponse(Response $response, ArrayObject $env)
    {
    }
}
