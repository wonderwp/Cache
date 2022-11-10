<?php

namespace WonderWp\Component\Cache\Tests;

use PHPUnit\Framework\TestCase;
use WonderWp\Component\Cache\ArrayCache;
use WonderWp\Component\Cache\CacheInterface;

class ArrayCacheTest extends AbstractCacheTest
{
    protected function setUp(): void
    {
        $this->cache = new ArrayCache();
    }
}
