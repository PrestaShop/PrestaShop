<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */
use PrestaShop\PrestaShop\Adapter\Configuration;
use PrestaShopBundle\DependencyInjection\CacheAdapterFactory;
use Symfony\Component\Cache\Adapter\AdapterInterface;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\Reference;

class LegacyCompilerPass implements CompilerPassInterface
{
    /**
     * Add legacy services that need to be built using Context::getContext().
     *
     * @param ContainerBuilder $container
     */
    public function process(ContainerBuilder $container)
    {
        $this->buildDefinitions([
            'configuration' => Configuration::class,
            'context' => [Context::class, 'getContext'],
            'db' => [Db::class, 'getInstance'],
        ], $container);

        $cacheDriver = $container->getParameter('cache.driver');
        $this->buildCacheDefinition($cacheDriver, $container);

        $this->buildSyntheticDefinitions(['shop' => Shop::class, 'employee' => Employee::class], $container);
    }

    private function buildDefinitions(array $keys, ContainerBuilder $container): void
    {
        foreach ($keys as $key => $class) {
            if (is_array($class)) {
                $definition = new Definition($class[0]);
                $definition->setFactory($class);
            } else {
                $definition = new Definition($class);
            }
            $definition->setPublic(true);
            $container->setDefinition($key, $definition);
        }
    }

    private function buildCacheDefinition(string $cacheDriver, ContainerBuilder $container): void
    {
        $container->setDefinition(CacheAdapterFactory::class, new Definition(CacheAdapterFactory::class));
        $definition = new Definition(AdapterInterface::class);
        $definition
            ->setPublic(true)
            ->setFactory([new Reference(CacheAdapterFactory::class), 'getCacheAdapter'])
            ->setArguments([$cacheDriver])
        ;

        $container->setDefinition($cacheDriver, $definition);
    }

    private function buildSyntheticDefinitions(array $keys, ContainerBuilder $container): void
    {
        foreach ($keys as $key => $class) {
            $definition = new Definition($class);
            $definition
                ->setSynthetic(true)
                ->setPublic(true)
            ;
            $container->setDefinition($key, $definition);
        }
    }
}
