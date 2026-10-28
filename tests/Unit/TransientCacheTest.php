<?php

declare(strict_types=1);

namespace WonderWp\Component\Cache\Tests\Unit;

use PHPUnit\Framework\TestCase;
use WonderWp\Component\Cache\TransientCache;

final class TransientCacheTest extends TestCase
{
    private TransientCache $cache;

    protected function setUp(): void
    {
        $GLOBALS['wwp_test_transients'] = [];
        $this->cache = new TransientCache();
    }

    public function testGetReturnsDefaultForMissingTransient(): void
    {
        $this->assertSame('fallback', $this->cache->get('missing', 'fallback'));
    }

    public function testGetTreatsZeroAsEmptyAndReturnsDefault(): void
    {
        set_transient('zero-key', 0);
        $this->assertSame('default', $this->cache->get('zero-key', 'default'));
    }

    public function testGetTreatsEmptyStringAsEmptyAndReturnsDefault(): void
    {
        set_transient('empty-key', '');
        $this->assertSame('default', $this->cache->get('empty-key', 'default'));
    }

    public function testGetTreatsFalseAsEmptyAndReturnsDefault(): void
    {
        set_transient('false-key', false);
        $this->assertSame('default', $this->cache->get('false-key', 'default'));
    }

    public function testGetReturnsStoredTruthyValues(): void
    {
        set_transient('ok-key', 'cached');
        $this->assertSame('cached', $this->cache->get('ok-key', 'default'));
    }
}
