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

use Generator;
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
     * @var ContainerInterface
     */
    private $container;

    /**
     * @param ContainerInterface $container
     */
    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
    }

    /**
     * {@inheritdoc}
     */
    public function getHookNames(array $gridDefinitionServiceIds)
    {
        $gridDefinitionIds = $this->getGridDefinitionIds($gridDefinitionServiceIds);

        $gridDefinitionHookNames = [];
        $gridQueryBuilderHookNames = [];
        $gridDataHookNames = [];
        $gridFilterFormHookNames = [];
        $gridPresenterHookNames = [];

        foreach ($gridDefinitionIds as $gridDefinitionId) {
            $gridDefinitionHookNames[] = $this->formatHookName(
                self::HOOK_STARTS_WITH,
                $gridDefinitionId,
                self::GRID_DEFINITION_HOOK_ENDS_WITH
            );

            $gridQueryBuilderHookNames[] = $this->formatHookName(
                self::HOOK_STARTS_WITH,
                $gridDefinitionId,
                self::GRID_QUERY_BUILDER_HOOK_ENDS_WITH
            );

            $gridDataHookNames[] = $this->formatHookName(
                self::HOOK_STARTS_WITH,
                $gridDefinitionId,
                self::GRID_DATA_HOOK_ENDS_WITH
            );

            $gridFilterFormHookNames[] = $this->formatHookName(
                self::HOOK_STARTS_WITH,
                $gridDefinitionId,
                self::GRID_FILTER_FORM_ENDS_WITH
            );

            $gridPresenterHookNames[] = $this->formatHookName(
                self::HOOK_STARTS_WITH,
                $gridDefinitionId,
                self::GRID_PRESENTER_ENDS_WITH
            );
        }

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
     * @param array $gridDefinitionServiceIds
     *
     * @return Generator
     */
    private function getGridDefinitionIds(array $gridDefinitionServiceIds)
    {
        foreach ($gridDefinitionServiceIds as $serviceId) {
            $service = $this->container->get($serviceId);

            if (!$service instanceof GridDefinitionFactoryInterface) {
                continue;
            }

            $definition = $service->getDefinition();

            $definitionId = $definition->getId();

            $camelizedDefinitionId = Container::camelize($definitionId);

            yield $camelizedDefinitionId;
        }
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
