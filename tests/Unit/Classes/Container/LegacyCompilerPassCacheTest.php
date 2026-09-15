<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Tests\Unit\Classes\Container;

use LegacyCompilerPass;
use PHPUnit\Framework\TestCase;
use Symfony\Component\DependencyInjection\ContainerBuilder;

/**
 * The cache adapter is built by a factory call assembled here, so the lifetime configured for the
 * pool has to be handed to that call - otherwise an adapter that outlives the request keeps its
 * entries forever.
 */
class LegacyCompilerPassCacheTest extends TestCase
{
    public function testTheConfiguredLifetimeIsPassedToTheFactory(): void
    {
        $container = $this->buildContainer(['cache.driver' => 'memcached', 'cache.default_lifetime' => 3600]);

        $this->assertSame(['memcached', 3600], $container->getDefinition('memcached')->getArguments());
    }

    public function testAMissingParameterFallsBackToNoExpiration(): void
    {
        $container = $this->buildContainer(['cache.driver' => 'memcached']);

        $this->assertSame(['memcached', 0], $container->getDefinition('memcached')->getArguments());
    }

    /**
     * @param array<string, mixed> $parameters
     */
    private function buildContainer(array $parameters): ContainerBuilder
    {
        $container = new ContainerBuilder();
        foreach ($parameters as $name => $value) {
            $container->setParameter($name, $value);
        }

        (new LegacyCompilerPass())->process($container);

        return $container;
    }
}
