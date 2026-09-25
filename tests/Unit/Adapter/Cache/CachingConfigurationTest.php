<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Tests\Unit\Adapter\Cache;

use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use PrestaShop\PrestaShop\Adapter\Cache\CachingConfiguration;
use PrestaShop\PrestaShop\Adapter\Cache\MemcacheServerManager;
use PrestaShop\PrestaShop\Adapter\Configuration\PhpParameters;
use PrestaShop\PrestaShop\Core\Cache\Clearer\CacheClearerInterface;

class CachingConfigurationTest extends TestCase
{
    /**
     * @var PhpParameters&MockObject
     */
    private $phpParameters;

    protected function setUp(): void
    {
        $this->phpParameters = $this->createMock(PhpParameters::class);
        $this->phpParameters->method('saveConfiguration')->willReturn(true);
    }

    /**
     * The Performance form does not post a disabled radio, so caching_system arrives null whenever
     * no caching system is selectable - which is exactly the state a shop is in when its extension
     * is too old for the adapter. That has to remain saveable, because turning caching off is the
     * only way out of it.
     */
    public function testCachingCanBeTurnedOffWhileNoSystemIsSelectable(): void
    {
        $this->phpParameters
            ->expects($this->once())
            ->method('setProperty')
            ->with('parameters.ps_cache_enable', false);

        $errors = $this->configuration(true, 'CacheMemcached')->updateConfiguration([
            'use_cache' => false,
            'caching_system' => null,
            'servers' => [],
        ]);

        $this->assertSame([], $errors);
    }

    public function testAMissingCachingSystemKeyIsStillRejected(): void
    {
        $this->assertFalse($this->configuration(true, 'CacheMemcached')->validateConfiguration([
            'use_cache' => false,
            'servers' => [],
        ]));
    }

    public function testASelectedSystemIsStillWritten(): void
    {
        $written = [];
        $this->phpParameters
            ->expects($this->exactly(2))
            ->method('setProperty')
            ->willReturnCallback(function ($key, $value) use (&$written): void {
                $written[$key] = $value;
            });

        $this->configuration(false, 'CacheMemcached')->updateConfiguration([
            'use_cache' => true,
            'caching_system' => 'CacheApc',
            'servers' => [],
        ]);

        $this->assertSame([
            'parameters.ps_cache_enable' => true,
            'parameters.ps_caching' => 'CacheApc',
        ], $written);
    }

    public function testNothingIsWrittenWhenNothingChanged(): void
    {
        $this->phpParameters->expects($this->never())->method('setProperty');

        $this->configuration(true, 'CacheApc')->updateConfiguration([
            'use_cache' => true,
            'caching_system' => 'CacheApc',
            'servers' => [],
        ]);
    }

    private function configuration(bool $isCachingEnabled, string $cachingSystem): CachingConfiguration
    {
        return new CachingConfiguration(
            $this->createMock(MemcacheServerManager::class),
            $this->phpParameters,
            $this->createMock(CacheClearerInterface::class),
            $isCachingEnabled,
            $cachingSystem
        );
    }
}
