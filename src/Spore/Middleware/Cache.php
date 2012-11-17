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

use Spore\HttpFoundation\Response;
use RuntimeException;
use InvalidArgumentException;
use ArrayObject;

class Cache extends MiddlewareAbstract
{
    /**
     * @var Integer
     */
    protected $priority = -100;

    /**
     * @var String
     */
    protected $name = 'cache';

    /**
     * 
     * @param \Spore\HttpFoundation\Request $request
     * @param \ArrayObject $env
     */
    public function processRequest(Request $request, ArrayObject $env)
    {
        // serialize and hash $request
        // chack cache
        // if cached set $env['request.cached'] = true;
    }

    /**
     * 
     * @param \Spore\HttpFoundation\Response $response
     * @param \ArrayObject $env
     * @throws \RuntimeException
     */
    public function processResponse(Response $response, ArrayObject $env)
    {
        if (!isset($env['format'])) {
            throw new InvalidArgumentException("Missing format parameter.");
        }

        if ($response->isSuccessful()) {

            if (!$response->isEmpty()) {

                switch ($env['format']) {
                    case 'json':
                        $response->setContent(json_decode($response->getContent()));
                        break;
                    
                    default:
                        throw new RuntimeException(sprintf(
                            "don't know how to handle this format %s", 
                            $env['format']
                        ));
                        break;
                }
            }

        }
    }
}
