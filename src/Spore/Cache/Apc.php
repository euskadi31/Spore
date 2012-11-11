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

use RuntimeException;

class Apc implements CacheInterface
{
    /**
     * @throws \RuntimeException
     */
    public function __construct()
    {
        if(version_compare('3.1.6', phpversion('apc')) > 0) {
            throw new RuntimeException("Missing ext/apc >= 3.1.6");
        }

        $enabled = ini_get('apc.enabled');
        if(PHP_SAPI == 'cli') {
            $enabled = $enabled && (bool) ini_get('apc.enable_cli');
        }

        if (!$enabled) {
            throw new RuntimeException(
                "ext/apc is disabled - see 'apc.enabled' and 'apc.enable_cli'"
            );
        }
    }

    /**
     * Checks if APC key exists
     * 
     * @param String $name
     * @return Boolean
     */
    public function exists($name)
    {
        return apc_exists($name);
    }

    /**
     * Fetch a stored variable from the cache
     * 
     * @param String $name
     * @return Mixed
     */
    public function get($name)
    {
        return apc_fetch($name);
    }

    /**
     * Cache a variable in the data store
     * 
     * @param String $name
     * @param Mixed $data
     * @return Boolean
     */
    public function set($name, $data)
    {
        return apc_store($name, $data);
    }

    /**
     * Removes a stored variable from the cache
     * 
     * @param String $name
     * @return Boolean
     */
    public function delete($name)
    {
        return apc_delete($name);
    }
}