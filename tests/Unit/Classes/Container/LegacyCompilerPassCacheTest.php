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

class LegacyCompilerPassCacheTest extends TestCase
{
    public function testTheLifetimeFromTheParametersFileReachesTheFactoryCall(): void
    {
        $container = new ContainerBuilder();
        include _PS_ROOT_DIR_ . '/app/config/set_parameters.php';

        $driver = $container->getParameter('cache.driver');
        $container = $this->compile($container);

        $lifetime = $container->getParameter('cache.default_lifetime');
        $this->assertIsInt($lifetime);
        $this->assertSame([$driver, $lifetime], $container->getDefinition($driver)->getArguments());
    }

    public function testAConfiguredLifetimeReachesTheFactoryCall(): void
    {
        $container = new ContainerBuilder();
        $container->setParameter('cache.driver', 'memcached');
        $container->setParameter('cache.default_lifetime', 3600);

        $container = $this->compile($container);

        $this->assertSame(['memcached', 3600], $container->getDefinition('memcached')->getArguments());
    }

    private function compile(ContainerBuilder $container): ContainerBuilder
    {
        $container->addCompilerPass(new LegacyCompilerPass());
        $container->compile();

        return $container;
    }
}
