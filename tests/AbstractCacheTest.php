<?php

namespace WonderWp\Component\Cache\Tests;

use PHPUnit\Framework\TestCase;
use WonderWp\Component\Cache\ArrayCache;

class AbstractCacheTest extends TestCase
{
    /** @var CacheInterface */
    protected $cache;

    public function testSetElementShouldReturnTrue()
    {
        $key   = 'key';
        $value = 'val';
        $set   = $this->cache->set($key, $value);
        $this->assertTrue($set);
    }

    public function testHasElementOnSetElementWithoutExpiryShouldReturnTrue()
    {
        $key   = 'key';
        $value = 'val';
        $set   = $this->cache->set($key, $value);
        $has   = $this->cache->has($key);
        $this->assertTrue($has);
    }

    public function testHasElementOnSetElementWithExpiryShouldReturnFalse()
    {
        $key   = 'key';
        $value = 'val';
        $set   = $this->cache->set($key, $value, -10);
        $has   = $this->cache->has($key);
        $this->assertFalse($has);
    }

    public function testHasElementOnUnSetElementShouldReturnFalse()
    {
        $key = 'key';
        $has = $this->cache->has($key);
        $this->assertFalse($has);
    }

    public function testGetElementOnSetElementWithoutExpiryShouldReturnValue()
    {
        $key   = 'key';
        $value = 'val';
        $set   = $this->cache->set($key, $value);
        $got   = $this->cache->get($key);
        $this->assertSame($value, $got);
    }

    public function testGetElementOnSetElementWithExpiryShouldReturnDefault()
    {
        $key     = 'key';
        $value   = 'val';
        $default = 'default';
        $set     = $this->cache->set($key, $value, -10);
        $got     = $this->cache->get($key, $default);
        $this->assertSame($default, $got);
    }

    public function testGetElementOnNonSetElementShouldReturnDefault()
    {
        $key     = 'key';
        $default = 'default';
        $got     = $this->cache->get($key, $default);
        $this->assertSame($default, $got);
    }

    public function testDeleteElementOnSetElementReturnTrue()
    {
        $key     = 'key';
        $value   = 'val';
        $set     = $this->cache->set($key, $value);
        $deleted = $this->cache->delete($key);
        $this->assertTrue($deleted);
    }

    public function testClearShouldClear()
    {
        $key     = 'key';
        $value   = 'val';
        $set     = $this->cache->set($key, $value);
        $cleared = $this->cache->clear($key);
        $this->assertTrue($cleared);
        $this->assertFalse($this->cache->has($key));
    }

    public function testSetMultipleShouldReturnTrue()
    {
        $elements = [
            'key1' => 'val1',
            'key2' => 'val2',
            'key3' => 'val3'
        ];
        $set      = $this->cache->setMultiple($elements);
        $this->assertTrue($set);
        $keys     = array_keys($elements);
        foreach ($keys as $key) {
            $this->assertTrue($this->cache->has($key));
        }
    }

    public function testGetMultipleShouldReturnMultiple()
    {
        $elements = [
            'key1' => 'val1',
            'key2' => 'val2',
            'key3' => 'val3'
        ];
        $set      = $this->cache->setMultiple($elements);
        $got      = $this->cache->getMultiple(array_keys($elements));
        $this->assertSame($elements, $got);
    }

    public function testDeleteMultipleShouldReturnTrue()
    {
        $elements = [
            'key1' => 'val1',
            'key2' => 'val2',
            'key3' => 'val3'
        ];
        $set      = $this->cache->setMultiple($elements);
        $keys     = array_keys($elements);
        $deleted  = $this->cache->deleteMultiple($keys);
        foreach ($keys as $key) {
            $this->assertFalse($this->cache->has($key));
        }
    }
}
