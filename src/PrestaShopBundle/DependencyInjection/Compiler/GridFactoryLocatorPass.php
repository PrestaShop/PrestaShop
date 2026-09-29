<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShopBundle\DependencyInjection\Compiler;

use PrestaShop\PrestaShop\Core\Grid\Definition\Factory\GridDefinitionFactoryInterface;
use PrestaShop\PrestaShop\Core\Grid\GridFactoryProvider;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\Compiler\ServiceLocatorTagPass;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;

final class GridFactoryLocatorPass implements CompilerPassInterface
{
    public const TAG_NAME = 'core.grid_factory';

    /**
     * @param ContainerBuilder $container
     *
     * @return void
     */
    public function process(ContainerBuilder $container): void
    {
        if (!$container->hasDefinition(GridFactoryProvider::class)) {
            return;
        }

        $factoriesByGridId = [];
        $resolvedFactoriesByGridId = [];

        foreach ($container->findTaggedServiceIds(self::TAG_NAME) as $serviceId => $tags) {
            $explicitGridId = $tags[0]['grid_id'] ?? null;

            if (null !== $explicitGridId) {
                $factoriesByGridId[$explicitGridId] = new Reference($serviceId);

                continue;
            }

            $gridId = $this->resolveGridIdFromDefinitionFactory($container, $serviceId);
            if (null !== $gridId && !isset($resolvedFactoriesByGridId[$gridId])) {
                $resolvedFactoriesByGridId[$gridId] = new Reference($serviceId);
            }
        }

        $factoriesByGridId += $resolvedFactoriesByGridId;

        $container->getDefinition(GridFactoryProvider::class)
            ->setArgument(0, ServiceLocatorTagPass::register($container, $factoriesByGridId))
        ;
    }

    /**
     * @param ContainerBuilder $container
     * @param string $gridFactoryServiceId
     *
     * @return string|null
     */
    private function resolveGridIdFromDefinitionFactory(ContainerBuilder $container, string $gridFactoryServiceId): ?string
    {
        $arguments = $container->getDefinition($gridFactoryServiceId)->getArguments();
        $definitionFactoryReference = $arguments[0] ?? $arguments['$definitionFactory'] ?? null;

        if (!$definitionFactoryReference instanceof Reference) {
            return null;
        }

        $definitionFactoryId = (string) $definitionFactoryReference;
        if (!$container->has($definitionFactoryId)) {
            return null;
        }

        $definitionFactoryClass = $container->findDefinition($definitionFactoryId)->getClass();
        if (null === $definitionFactoryClass) {
            return null;
        }

        $definitionFactoryClass = $container->getParameterBag()->resolveValue($definitionFactoryClass);

        if (!is_string($definitionFactoryClass)
            || !class_exists($definitionFactoryClass)
            || !is_subclass_of($definitionFactoryClass, GridDefinitionFactoryInterface::class)
            || !defined($definitionFactoryClass . '::GRID_ID')
        ) {
            return null;
        }

        $gridId = constant($definitionFactoryClass . '::GRID_ID');

        return is_string($gridId) ? $gridId : null;
    }
}
