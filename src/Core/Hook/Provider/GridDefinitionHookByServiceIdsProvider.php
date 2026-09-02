<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
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
    public const HOOK_PREFIX = 'action';
    public const GRID_DEFINITION_HOOK_SUFFIX = 'GridDefinitionModifier';
    public const GRID_QUERY_BUILDER_HOOK_SUFFIX = 'GridQueryBuilderModifier';
    public const GRID_DATA_HOOK_SUFFIX = 'GridDataModifier';
    public const GRID_FILTER_FORM_SUFFIX = 'GridFilterFormModifier';
    public const GRID_PRESENTER_SUFFIX = 'GridPresenterModifier';

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
