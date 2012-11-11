<?php

namespace Spore\Cache\Tests\Units;

require_once __DIR__ . '/../../../vendor/autoload.php';

use Spore;

class PhpArray extends Spore\Test\Unit
{
    public function testClass()
    {
        $cache = new Spore\Cache\PhpArray();

        $this->assert->object($cache)
            ->isInstanceOf('\Spore\Cache\PhpArray');

        $cache->set('foot', 'bar');

        $this->assert->string($cache->get('foot'))
            ->isEqualTo('bar');

        $this->assert->boolean($cache->get('foot4'))
            ->isFalse();

        $this->assert->boolean($cache->exists('foot'))
            ->isTrue();

        $this->assert->boolean($cache->exists('foot4'))
            ->isFalse();

        $cache->delete('foot');

        $this->assert->boolean($cache->get('foot'))
            ->isFalse();
    }
}