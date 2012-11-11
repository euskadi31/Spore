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
namespace Spore\HttpFoundation\Adapter;

use Spore\HttpFoundation\Request;

interface AdapterInterface
{
    /**
     * 
     * @param \Spore\HttpFoundation\Request $request
     * @return \Spore\HttpFoundation\Response
     */
    public function execute(Request $request);
}
