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

interface CacheInterface
{
    /**
     * 
     * @param String $name
     * @return Boolean
     */
    public function exists($name);
    
    /**
     * 
     * @param String $name
     * @return Mixed
     */
    public function get($name);

    /**
     * 
     * @param String $name
     * @param Mixed $data
     * @return Boolean
     */
    public function set($name, $data);

    /**
     * 
     * @param String $name
     * @return Boolean
     */
    public function delete($name);
}