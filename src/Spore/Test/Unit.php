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
namespace Spore\Test;

use \mageekguy\atoum;
use \mageekguy\atoum\factory;

abstract class Unit extends atoum\test 
{
    public function __construct(factory $factory = null)
    {
        $this->setTestNamespace('Tests\\Units');
        parent::__construct($factory);
    }
}