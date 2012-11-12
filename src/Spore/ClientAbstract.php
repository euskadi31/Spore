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
namespace Spore;

use Spore\HttpFoundation\Request;
use Spore\HttpFoundation\Adapter\AdapterInterface;
use Spore\HttpFoundation\Adapter\Curl;
use Spore\Middleware\MiddlewareInterface;
use InvalidArgumentException;
use SplObjectStorage;
use ArrayObject;

abstract class ClientAbstract
{
    /**
     * @var \Spore\HttpFoundation\Adapter\AdapterInterface
     */
    protected $adapter;

    /**
     * @var \SplObjectStorage;
     */
    protected $middlewares;

    /**
     * @var String
     */
    protected $format = 'json';


    public function __construct()
    {
        $this->middlewares = new SplObjectStorage();

        $this->addMiddleware(new Middleware\Format());
    }

    /**
     * Add Middleware
     * 
     * @param \Spore\Middleware\MiddlewareInterface
     * @return \Spore\ClientAbstract
     */
    public function addMiddleware(MiddlewareInterface $middleware)
    {
        if(!$this->middlewares->contains($middleware)) {
            $this->middlewares->attach($middleware);
        }
        
        return $this;
    }

    /**
     * Set the http client adapter
     * 
     * @param \Spore\HttpFoundation\Adapter\AdapterInterface $adapter
     * @return \Spore\ClientAbstract
     */
    public function setAdapter(AdapterInterface $adapter)
    {
        $this->adapter = $adapter;

        return $this;
    }

    /**
     * Get the http client adapter
     *
     * @return \Spore\HttpFoundation\Adapter\AdapterInterface
     */
    public function getAdapter()
    {
        if(empty($this->adapter)) {
            $this->adapter = new Curl();
        }

        return $this->adapter;
    }

    /**
     * Verify parameters
     * 
     * @param Array $params
     * @param Array $required   required parameters
     * @param Array $optional   optional parameters
     * @return Array
     * @throws \InvalidArguementException
     */
    public function verifyParameters(array $params, array $required = array(), array $optional = array())
    {
        if (!empty($required)) {
            foreach ($required as $key) {
                if (!isset($params[$key])) {
                    throw new InvalidArgumentException(sprintf("Missing %s parameter.", $key));
                }
            }
        }

        if (!empty($optional)) {
            $optional = array_merge($optional, $required);
            foreach ($params as $key => $value) {
                if (!in_array($key, $optional)) {
                    unset($params[$key]);
                }
            }
        }

        return $params;
    }

    /**
     * Send get request
     * 
     * @param String $uri
     * @param Array $params
     * @param Array $headers
     * @return \Spore\HttpFoundation\Response
     */
    public function get($uri, array $params = array(), array $headers = array())
    {
        return $this->call('GET', $uri, $params, null, $headers);
    }

    /**
     * Send head request
     * 
     * @param String $uri
     * @param Array $params
     * @param Array $headers
     * @return \Spore\HttpFoundation\Response
     */
    public function head($uri, array $params = array(), array $headers = array())
    {
        return $this->call('HEAD', $uri, $params, null, $headers);
    }

    /**
     * Send post request
     * 
     * @param String $uri
     * @param Array $params
     * @param Mixed $data
     * @param Array $headers
     * @return \Spore\HttpFoundation\Response
     */
    public function post($uri, array $params = array(), $data = null, array $headers = array())
    {
        return $this->call('POST', $uri, $params, $data, $headers);
    }

    /**
     * Send put request
     * 
     * @param String $uri
     * @param Array $params
     * @param Mixed $data
     * @param Array $headers
     * @return \Spore\HttpFoundation\Response
     */
    public function put($uri, array $params = array(), $data = null, array $headers = array())
    {
        return $this->call('PUT', $uri, $params, $data, $headers);
    }

    /**
     * Send delete request
     * 
     * @param String $uri
     * @param Array $params
     * @param Array $headers
     * @return \Spore\HttpFoundation\Response
     */
    public function delete($uri, array $params = array(), array $headers = array())
    {
        return $this->call('DELETE', $uri, $params, null, $headers);
    }

    /**
     * Send request
     * 
     * @param String $method
     * @param String $uri
     * @param Array $params
     * @param Mixed $data
     * @param Array $headers
     * @return \Spore\HttpFoundation\Response
     */
    public function call($method, $uri, array $params, $data = null, array $headers = array())
    {
        $method = strtoupper($method);

        $parts = array();

        if (strpos($uri, '/') !== false) {
            $parts = array_filter(explode('/', $uri));
        }

        foreach ($params as $key => $value) {
            $uri = str_replace(':' . $key, $value, $uri);
        }

        if (!empty($parts)) {
            foreach ($parts as $value) {
                if ($value[0] == ':') {
                    $key = substr($value, 1);
                    if (isset($params[$key])) {
                        unset($params[$key]);
                    }
                }
            }
        }

        $request = new Request();
        $request->setUri($uri);
        $request->setMethod($method);

        if (!empty($params)) {
            $request->setQuery($params);
        }

        if (!empty($data)) {
            $request->setData($data);
        }

        $env = new ArrayObject(array(
            'format' => $this->format
        ));

        foreach ($this->middlewares as $middleware) {
            $middleware->processRequest($request, $env);
        }

        $response = $this->getAdapter()->execute($request);

        foreach ($this->middlewares as $middleware) {
            $middleware->processResponse($response, $env);
        }

        return $response;
    }
}