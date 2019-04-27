<?php
/**
 * 2007-2019 PrestaShop SA and Contributors
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
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
 * needs please refer to http://www.prestashop.com for more information.
 *
 * @author    PrestaShop SA <contact@prestashop.com>
 * @copyright 2007-2019 PrestaShop SA and Contributors
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 * International Registered Trademark & Property of PrestaShop SA
 */

namespace PrestaShop\PrestaShop\Core\Hook\Provider;

use PrestaShop\PrestaShop\Core\Grid\Definition\Factory\GridDefinitionFactoryInterface;
use Symfony\Component\DependencyInjection\Container;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Provides hooks list by calling service ids from the container.
 */
final class GridDefinitionHookByServiceIdsProvider implements HookByServiceIdsProviderInterface
{
    const HOOK_STARTS_WITH = 'action';
    const GRID_DEFINITION_HOOK_ENDS_WITH = 'GridDefinitionModifier';
    const GRID_QUERY_BUILDER_HOOK_ENDS_WITH = 'GridQueryBuilderModifier';
    const GRID_DATA_HOOK_ENDS_WITH = 'GridDataModifier';
    const GRID_FILTER_FORM_ENDS_WITH = 'GridFilterFormModifier';
    const GRID_PRESENTER_ENDS_WITH = 'GridPresenterModifier';

    /**
     * {@inheritdoc}
     */
    public function getHookNames(ContainerInterface $container, array $gridDefinitionServiceIds)
    {
        $gridDefinitionIds = $this->getGridDefinitionIds($container, $gridDefinitionServiceIds);

        $gridDefinitionHookNames = $this->collectHookNames(
            $gridDefinitionIds,
            self::GRID_DEFINITION_HOOK_ENDS_WITH
        );

        $gridQueryBuilderHookNames = $this->collectHookNames(
            $gridDefinitionIds,
            self::GRID_QUERY_BUILDER_HOOK_ENDS_WITH
        );

        $gridDataHookNames = $this->collectHookNames(
            $gridDefinitionIds,
            self::GRID_DATA_HOOK_ENDS_WITH
        );

        $gridFilterFormHookNames = $this->collectHookNames(
            $gridDefinitionIds,
            self::GRID_FILTER_FORM_ENDS_WITH
        );

        $gridPresenterHookNames = $this->collectHookNames(
            $gridDefinitionIds,
            self::GRID_PRESENTER_ENDS_WITH
        );

        return array_merge(
            $gridDefinitionHookNames,
            $gridQueryBuilderHookNames,
            $gridDataHookNames,
            $gridFilterFormHookNames,
            $gridPresenterHookNames
        );
    }

    /**
     * Gets grid definition ids which are used in a grid hook formation.
     *
     * @param ContainerInterface $container
     * @param array $gridDefinitionServiceIds
     *
     * @return array
     */
    private function getGridDefinitionIds(ContainerInterface $container, array $gridDefinitionServiceIds)
    {
        $definitionIds = [];
        foreach ($gridDefinitionServiceIds as $serviceId) {
            $service = $container->get($serviceId);

            if (!$service instanceof GridDefinitionFactoryInterface) {
                continue;
            }

            $definition = $service->getDefinition();

            $definitionId = $definition->getId();

            $camelizedDefinitionId = Container::camelize($definitionId);

            $definitionIds[] = $camelizedDefinitionId;
        }

        return $definitionIds;
    }

    /**
     * Collects hook names by using common pattern for all grids.
     *
     * @param array $gridDefinitionIds
     * @param string $hookNameEndsWith
     * @return array
     */
    private function collectHookNames($gridDefinitionIds, $hookNameEndsWith)
    {
        $hookNames = [];
        foreach ($gridDefinitionIds as $gridDefinitionId) {
            $hookNames[] = $this->formatHookName(
                self::HOOK_STARTS_WITH,
                $gridDefinitionId,
                $hookNameEndsWith
            );
        }

        return $hookNames;
    }

    /**
     * Formats hook names.
     *
     * @param string $hookStartsWith
     * @param string $hookId
     * @param string $hookEndsWidth
     *
     * @return string
     */
    private function formatHookName($hookStartsWith, $hookId, $hookEndsWidth)
    {
        return $hookStartsWith . $hookId . $hookEndsWidth;
    }
}
