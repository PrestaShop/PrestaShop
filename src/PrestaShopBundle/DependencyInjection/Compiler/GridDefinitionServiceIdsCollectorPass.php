<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShopBundle\DependencyInjection\Compiler;

use PrestaShop\PrestaShop\Core\Grid\Definition\Factory\GridDefinitionFactoryInterface;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;

/**
 * Collects grid definition service ids.
 */
final class GridDefinitionServiceIdsCollectorPass implements CompilerPassInterface
{
    public const GRID_DEFINITION_SERVICE_PREFIX = 'prestashop.core.grid.definition';

    /**
     * {@inheritdoc}
     */
    public function process(ContainerBuilder $container)
    {
        if (!in_array($container->getParameter('kernel.environment'), ['dev', 'test'])) {
            return;
        }

        $serviceDefinitions = $container->getDefinitions();
        $gridServiceIds = [];

        foreach ($serviceDefinitions as $serviceId => $serviceDefinition) {
            if ($serviceDefinition->isAbstract() || $serviceDefinition->isPrivate()) {
                continue;
            }

            if ($this->isGridDefinitionService($serviceId, $serviceDefinition->getClass())) {
                $gridServiceIds[] = $serviceId;
            }
        }

        $container->setParameter(
            'prestashop.core.grid.definition.service_ids',
            $gridServiceIds
        );
    }

    /**
     * Checks if grid definition service.
     *
     * @param string $serviceId
     * @param string $serviceClass
     *
     * @return bool
     */
    private function isGridDefinitionService($serviceId, $serviceClass)
    {
        $doesServiceStartsWithGridDefinition = str_starts_with($serviceId, self::GRID_DEFINITION_SERVICE_PREFIX);

        return $doesServiceStartsWithGridDefinition && is_subclass_of($serviceClass, GridDefinitionFactoryInterface::class);
    }
}
