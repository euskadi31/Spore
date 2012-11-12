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

class Format extends MiddlewareAbstract
{
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
