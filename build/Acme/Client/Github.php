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
namespace Acme\Client;

use Spore\ClientAbstract;
use Spore\HttpFoundation\Response;
use RuntimeException;

/**
 * @see http://developer.github.com/v3/
 */
class Github extends ClientAbstract
{
    const VERSION = "0.1";

    /**
     * @var String $base
     */
    protected $base = "https://api.github.com";

    /**
     * Get a single user
     * 
     * @param Array $params
     */
    public function getUser(array $params)
    {
        $params = $this->verifyParameters(
            $params,
            array(
                "user",
            ),
            array()
        );

        $response = $this->get($this->base . "/users/:user", $params);

        if (!in_array($response->getStatusCode(), array(
            200,
        ))) {
            throw new RuntimeException(Response::$statusTexts[$response->getStatusCode()]);
        }
        
        return $response->getContent();
    }

    
}