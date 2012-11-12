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

use Spore\HttpFoundation\Response;
use Spore\HttpFoundation\Request;
use Spore\Cache\CacheInterface;
use Spore\Cache\PhpArray;
use InvalidArgumentException;
use RuntimeException;
use SplFileInfo;
use ArrayObject;

class Client extends ClientAbstract
{
    const VERSION = '0.1';

    /**
     * @var Array
     */
    protected $spec;

    /**
     * @var \Spore\Cache\CacheInterface
     */
    protected $cache;

    /**
     * @var String
     */
    protected $format = 'json';

    /**
     * @param \Spore\Cache\CacheInterface $cache
     * @return \Spore\Client
     */
    public function setCache(CacheInterface $cache)
    {
        $this->cache = $cache;

        return $this;
    }

    /**
     * @return \Spore\Cache\CacheInterface
     */
    public function getCache()
    {
        if (empty($this->cache)) {
            $this->cache = new PhpArray();
        }

        return $this->cache;
    }

    /**
     *
     * @param String|Null $filename A spec filename
     * @return \Spore\Client
     * @throws \InvalidArgumentException
     */
    public function loadSpec($filename)
    {
        $file = new SplFileInfo($filename);

        $key = 'spore_spec_' . strtolower($file->getBasename('.json'));

        if (!$spec = $this->getCache()->get($key)) {

            if (!$file->isFile()) {
                throw new InvalidArgumentException(sprintf(
                    "%s it is not a file.", 
                    $file->getRealPath()
                ));
            }

            if (!$file->isReadable()) {
                throw new InvalidArgumentException(sprintf(
                    "Unable to read file %s", 
                    $file->getRealPath()
                ));
            }

            $spec = json_decode(file_get_contents($file->getRealPath()), true);

            $this->getCache()->set($key, $spec);
        }

        $this->spec = $spec;

        unset($spec);

        if (isset($this->spec['format'])) {
            if (!in_array('json', $this->spec['format'])) {
                $this->format = $this->spec['format'][0];
            }
        }
        
        return $this;
    }

    /**
     * Send request
     * 
     * @param String $method
     * @param String $endpoint
     * @param Array $params
     * @param Mixed $data
     * @param Array $headers
     * @return \Spore\HttpFoundation\Response
     * @throws \InvalidArgumentException, \RuntimeException
     */
    public function call($method, $endpoint, array $params, $data = null, array $headers = array())
    {
        $method = strtoupper($method);

        if (isset($this->spec['methods'][$endpoint])) {

            $spec = $this->spec['methods'][$endpoint];

            if ($spec['method'] != $method) {
                throw new InvalidArgumentException(sprintf(
                    "Method %s is not allowed.",
                    $method
                ));
            }

            $params = $this->verifyParameters(
                $params,
                isset($spec['required_params']) ? $spec['required_params'] : array(),
                isset($spec['optional_params']) ? $spec['optional_params'] : array()
            );

            $uri = rtrim($this->spec['base_url'], '/') . $spec['path'];

            foreach ($params as $key => $value) {
                $uri = str_replace(':' . $key, $value, $uri);
            }
            
            $request = new Request();
            $request->setUri($uri);
            $request->setMethod($method);

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

            if (isset($spec['expected'])) {
                if (!in_array($response->getStatusCode(), $spec['expected'])) {
                    throw new RuntimeException(Response::$statusTexts[$response->getStatusCode()]);
                }
            }

            foreach ($this->middlewares as $middleware) {
                $middleware->processResponse($response, $env);
            }

            return $response;
        }

        throw new InvalidArgumentException(sprintf(
            "Endpoint %s is not defined.", 
            $endpoint
        ));
    }
}