<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Tests\Unit\Adapter\Cache;

use PHPUnit\Framework\TestCase;
use PrestaShop\PrestaShop\Adapter\Cache\CachingConfiguration;
use PrestaShop\PrestaShop\Adapter\Cache\MemcacheServerManager;
use PrestaShop\PrestaShop\Adapter\Configuration\PhpParameters;
use PrestaShop\PrestaShop\Core\Cache\Clearer\CacheClearerInterface;
use PrestaShop\PrestaShop\Core\Domain\Configuration\ShopConfigurationInterface;
use PrestaShop\PrestaShop\Core\Domain\Shop\ValueObject\ShopConstraint;

class CachingConfigurationTest extends TestCase
{
    public function testGetParallelWarmUp(): void
    {
        $configuration = $this->createMock(ShopConfigurationInterface::class);
        $configuration
            ->method('get')
            ->with('PS_CACHE_PARALLEL_WARMUP', false, ShopConstraint::allShops())
            ->willReturn('1');

        $this->assertTrue($this->createCachingConfiguration($configuration)->getConfiguration()['parallel_warmup']);
    }

    public function testParallelWarmUpDisabledByDefault(): void
    {
        $configuration = $this->createMock(ShopConfigurationInterface::class);
        $configuration->method('get')->willReturnArgument(1);

        $this->assertFalse($this->createCachingConfiguration($configuration)->getConfiguration()['parallel_warmup']);
    }

    public function testUpdateParallelWarmUp(): void
    {
        $configuration = $this->createMock(ShopConfigurationInterface::class);
        $configuration
            ->expects($this->once())
            ->method('set')
            ->with('PS_CACHE_PARALLEL_WARMUP', true, ShopConstraint::allShops());

        $this->assertSame([], $this->createCachingConfiguration($configuration)->updateConfiguration([
            'use_cache' => false,
            'caching_system' => 'CacheMemcache',
            'servers' => [],
            'parallel_warmup' => true,
        ]));
    }

    public function testUpdateParallelWarmUpWithoutCachingSystem(): void
    {
        $configuration = $this->createMock(ShopConfigurationInterface::class);
        $configuration
            ->expects($this->once())
            ->method('set')
            ->with('PS_CACHE_PARALLEL_WARMUP', false, ShopConstraint::allShops());

        $this->assertSame([], $this->createCachingConfiguration($configuration)->updateConfiguration([
            'use_cache' => false,
            'caching_system' => null,
            'servers' => [],
            'parallel_warmup' => false,
        ]));
    }

    public function testUpdateWithoutParallelWarmUp(): void
    {
        $configuration = $this->createMock(ShopConfigurationInterface::class);
        $configuration->expects($this->never())->method('set');

        $this->assertSame([], $this->createCachingConfiguration($configuration)->updateConfiguration([]));
    }

    private function createCachingConfiguration(ShopConfigurationInterface $configuration): CachingConfiguration
    {
        return new CachingConfiguration(
            $this->createMock(MemcacheServerManager::class),
            $this->createMock(PhpParameters::class),
            $this->createMock(CacheClearerInterface::class),
            false,
            'CacheMemcache',
            $configuration,
        );
    }
}
