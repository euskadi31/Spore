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
namespace Spore\Cache;

class PhpArray implements CacheInterface
{
    protected static $data;

    public function __construct()
    {
        if(empty(self::$data)) {
            self::$data = array();
        }
    }

    /**
     * 
     * @param String $name
     * @return Boolean
     */
    public function exists($name)
    {
        return isset(self::$data[$name]);
    }

    /**
     * 
     * @param String $name
     * @return Mixed
     */
    public function get($name)
    {
        if(isset(self::$data[$name])) {
            return self::$data[$name];
        }

        return false;
    }

    /**
     * 
     * @param String $name
     * @param Mixed $data
     * @return Boolean
     */
    public function set($name, $data)
    {
        self::$data[$name] = $data;
        return true;
    }

    /**
     * 
     * @param String $name
     * @return Boolean
     */
    public function delete($name)
    {
        if(isset(self::$data[$name])) {
            unset(self::$data[$name]);
        }

        return true;
    }
}