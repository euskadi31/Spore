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
namespace Spore\HttpFoundation;

use Symfony\Component\HttpFoundation\ParameterBag;

class Request
{
    /**
     * @var \Symfony\Component\HttpFoundation\ParameterBag
     */
    protected $query;

    /**
     * @var \Symfony\Component\HttpFoundation\ParameterBag
     */
    protected $headers;

    /**
     * @var Mixed
     */
    protected $data;

    /**
     * @var String
     */
    protected $host;

    /**
     * @var String
     */
    protected $path;

    /**
     * @var String
     */
    protected $scheme;

    /**
     * @var Integer
     */
    protected $port = 80;

    /**
     * @var String
     */
    protected $user;

    /**
     * @var String
     */
    protected $password;

    /**
     * @var String
     */
    protected $method;

    /**
     * @var Array
     */
    protected $methods = array(
        'GET',
        'POST',
        'HEAD',
        'PUT',
        'DELETE',
        'OPTIONS',
        'TRACE',
        'CONNECT',
        'PATCH'
    );

    /**
     *
     * @param String|Null $uri
     */
    public function __construct($uri = null)
    {
        if(!empty($uri)) {
            $this->setUri($uri);
        }
    }

    /**
     *
     * @param String $uri
     * @return \Spore\HttpFoundation\Request
     */
    public function setUri($uri)
    {
        $parts = parse_url($uri);

        $this->setScheme($parts['scheme']);
        $this->setHost($parts['host']);

        if(isset($parts['port'])) {
            $this->setPort($parts['port']);
        }

        if(isset($parts['user'])) {
            $this->setUser($parts['user']);
        }

        if(isset($parts['pass'])) {
            $this->setPassword($parts['pass']);
        }

        if(isset($parts['path'])) {
            $this->setPath($parts['path']);
        }

        if(isset($parts['query'])) {
            $this->setQuery($parts['query']);
        }

        return $this;
    }

    /**
     *
     * @return String
     */
    public function getUri()
    {
        $uri = $this->getScheme() . '://';

        if (!empty($this->user)) {
            $uri .= $this->user;

            if (!empty($this->password)) {
                $uri .= ':' . $this->password;
            }

            $uri .= '@';
        }

        $uri .= $this->host;

        if ($this->port != 80) {
            $uri .= ':' . $this->port;
        }

        if (!empty($this->path)) {
            $uri .= $this->path;
        }

        if ($this->getQuery()->count() > 0) {
            $uri .= '?' . http_build_query($this->getQuery()->all());
        }

        return $uri;
    }

    /**
     * Set method
     *
     * @param String $method
     * @return \Spore\HttpFoundation\Request
     */
    public function setMethod($method)
    {
        $method = strtoupper($method);

        if(in_array($method, $this->methods)) {
            $this->method = $method;
            return $this;
        }

        throw new \InvalidArgumentException(sprintf("Method %s unsupported.", $method));
    }

    /**
     *
     * @return String
     */
    public function getMethod()
    {
        return $this->method;
    }

    /**
     *
     * @return Boolean
     */
    public function isPost()
    {
        return $this->method == 'POST';
    }

    /**
     *
     * @return Boolean
     */
    public function isGet()
    {
        return $this->method == 'GET';
    }

    /**
     *
     * @return Boolean
     */
    public function isPut()
    {
        return $this->method == 'PUT';
    }

    /**
     *
     * @return Boolean
     */
    public function isDelete()
    {
        return $this->method == 'DELETE';
    }

    /**
     *
     * @return Boolean
     */
    public function isPatch()
    {
        return $this->method == 'PATCH';
    }

    /**
     *
     * @return Boolean
     */
    public function isOptions()
    {
        return $this->method == 'OPTIONS';
    }

    /**
     *
     * @return Boolean
     */
    public function isHead()
    {
        return $this->method == 'HEAD';
    }

    /**
     *
     * @return Boolean
     */
    public function isConnect()
    {
        return $this->method == 'CONNECT';
    }

    /**
     *
     * @return Boolean
     */
    public function isTrace()
    {
        return $this->method == 'TRACE';
    }

    /**
     *
     * @param String $host
     * @return \Spore\HttpFoundation\Request
     */
    public function setHost($host)
    {
        $this->host = $host;

        return $this;
    }

    /**
     *
     * @return String
     */
    public function getHost()
    {
        return $this->host;
    }

    /**
     *
     * @param Integer $port
     * @return \Spore\HttpFoundation\Request
     */
    public function setPort($port)
    {
        $this->port = $port;

        return $this;
    }

    /**
     *
     * @return Integer
     */
    public function getPort()
    {
        return $this->port;
    }

    /**
     *
     * @param String $path
     * @return \Spore\HttpFoundation\Request
     */
    public function setPath($path)
    {
        $this->path = $path;

        return $this;
    }

    /**
     *
     * @return String
     */
    public function getPath()
    {
        return $this->path;
    }

    /**
     *
     * @param String $scheme
     * @return \Spore\HttpFoundation\Request
     */
    public function setScheme($scheme)
    {
        $this->scheme = $scheme;

        return $this;
    }

    /**
     * Gets the request's scheme.
     *
     * @return String
     */
    public function getScheme()
    {
        return $this->scheme;
    }

    /**
     *
     * @param String $user
     * @return \Spore\HttpFoundation\Request
     */
    public function setUser($user)
    {
        $this->user = $user;

        return $this;
    }

    /**
     * Returns the user.
     *
     * @return String|Null
     */
    public function getUser()
    {
        return $this->user;
    }

    /**
     *
     * @param String $password
     * @return \Spore\HttpFoundation\Request
     */
    public function setPassword($password)
    {
        $this->password = $password;

        return $this;
    }

    /**
     * Returns the password.
     *
     * @return String|Null
     */
    public function getPassword()
    {
        return $this->password;
    }

    /**
     *
     * @return Boolean
     */
    public function isAuth()
    {
        return (!empty($this->user) && !empty($this->password));
    }

    /**
     *
     * @param String|Array $query
     * @return \Spore\HttpFoundation\Request
     * @throws \InvalidArgumentException
     */
    public function setQuery($query)
    {
        if(is_string($query)) {
            parse_str($query, $query);
        }

        if(!is_array($query)) {
            throw new \InvalidArgumentException("Invalid argument");
        }

        if(empty($this->query)) {
            $this->query = new ParameterBag($query);
        } else {
            $this->query->add($query);
        }

        return $this;
    }

    /**
     *
     * @return \Symfony\Component\HttpFoundation\ParameterBag
     */
    public function getQuery()
    {
        if(empty($this->query)) {
            $this->query = new ParameterBag();
        }

        return $this->query;
    }

    /**
     *
     * @param Array $headers
     * @return \Spore\HttpFoundation\Request
     */
    public function setHeader(array $headers)
    {
        if(empty($this->headers)) {
            $this->headers = new ParameterBag();
        }

        $this->headers->add($headers);

        return $this;
    }

    /**
     *
     * @return \Symfony\Component\HttpFoundation\ParameterBag
     */
    public function getHeaders()
    {
        if(empty($this->headers)) {
            $this->headers = new ParameterBag();
        }

        return $this->headers;
    }
    
    /**
     *
     * @param Mixed $data
     * @return \Spore\HttpFoundation\Request
     */
    public function setData($data)
    {
        $this->data = $data;

        return $this;
    }

    /**
     *
     * @return Mixed
     */
    public function getData()
    {
        return $this->data;
    }
}
