<?php
/**
 * Copyright since 2007 PrestaShop SA and Contributors
 * PrestaShop is an International Registered Trademark & Property of PrestaShop SA
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.md.
 * It is also available through the world-wide-web at this URL:
 * https://opensource.org/licenses/OSL-3.0
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@prestashop.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade PrestaShop to newer
 * versions in the future. If you wish to customize PrestaShop for your
 * needs please refer to https://devdocs.prestashop.com/ for more information.
 *
 * @author    PrestaShop SA and Contributors <contact@prestashop.com>
 * @copyright Since 2007 PrestaShop SA and Contributors
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
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
        $doesServiceStartsWithGridDefinition = strpos($serviceId, self::GRID_DEFINITION_SERVICE_PREFIX) === 0;

        return $doesServiceStartsWithGridDefinition && is_subclass_of($serviceClass, GridDefinitionFactoryInterface::class);
    }
}
