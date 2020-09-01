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

namespace PrestaShop\PrestaShop\Core\Hook\Provider;

use Exception;
use Generator;
use Logger;
use PrestaShop\PrestaShop\Core\Grid\Definition\Factory\GridDefinitionFactoryInterface;
use Symfony\Component\DependencyInjection\Container;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Provides hooks list by calling service ids from the container.
 */
final class GridDefinitionHookByServiceIdsProvider implements HookByServiceIdsProviderInterface
{
    const HOOK_PREFIX = 'action';
    const GRID_DEFINITION_HOOK_SUFFIX = 'GridDefinitionModifier';
    const GRID_QUERY_BUILDER_HOOK_SUFFIX = 'GridQueryBuilderModifier';
    const GRID_DATA_HOOK_SUFFIX = 'GridDataModifier';
    const GRID_FILTER_FORM_SUFFIX = 'GridFilterFormModifier';
    const GRID_PRESENTER_SUFFIX = 'GridPresenterModifier';

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
        /** @var Generator $gridDefinitionIds */
        $gridDefinitionIds = $this->getGridDefinitionIds($gridDefinitionServiceIds);

        $gridDefinitionHookNames = [];
        $gridQueryBuilderHookNames = [];
        $gridDataHookNames = [];
        $gridFilterFormHookNames = [];
        $gridPresenterHookNames = [];

        foreach ($gridDefinitionIds as $gridDefinitionId) {
            $gridDefinitionHookNames[] = $this->formatHookName(
                self::HOOK_PREFIX,
                $gridDefinitionId,
                self::GRID_DEFINITION_HOOK_SUFFIX
            );

            $gridQueryBuilderHookNames[] = $this->formatHookName(
                self::HOOK_PREFIX,
                $gridDefinitionId,
                self::GRID_QUERY_BUILDER_HOOK_SUFFIX
            );

            $gridDataHookNames[] = $this->formatHookName(
                self::HOOK_PREFIX,
                $gridDefinitionId,
                self::GRID_DATA_HOOK_SUFFIX
            );

            $gridFilterFormHookNames[] = $this->formatHookName(
                self::HOOK_PREFIX,
                $gridDefinitionId,
                self::GRID_FILTER_FORM_SUFFIX
            );

            $gridPresenterHookNames[] = $this->formatHookName(
                self::HOOK_PREFIX,
                $gridDefinitionId,
                self::GRID_PRESENTER_SUFFIX
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
            try {
                $service = $this->container->get($serviceId);
                if (!$service instanceof GridDefinitionFactoryInterface) {
                    continue;
                }
                $definition = $service->getDefinition();
                $definitionId = $definition->getId();
                $camelizedDefinitionId = Container::camelize($definitionId);
                yield $camelizedDefinitionId;
            } catch (Exception $e) {
                Logger::addLog(sprintf('Error while loading service: %s . Error: %s', $serviceId, $e));
            }
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
