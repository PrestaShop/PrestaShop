<?php
/**
 * 2007-2019 PrestaShop and Contributors
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
 * needs please refer to https://www.prestashop.com for more information.
 *
 * @author    PrestaShop SA <contact@prestashop.com>
 * @copyright 2007-2019 PrestaShop SA and Contributors
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 * International Registered Trademark & Property of PrestaShop SA
 */

namespace PrestaShopBundle\DependencyInjection\Compiler;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;

class GridPass implements CompilerPassInterface
{
    const ROW_ACTION_ACCESSIBILITY_CHECKER_TAG_NAME = 'grid.row_accessibility_checker';
    const COLUMN_DATA_PRESENTER_TAG_NAME = 'grid.column_data_presenter';

    /**
     * {@inheritdoc}
     */
    public function process(ContainerBuilder $container)
    {
        $this->processGridRowAccessibilityCheckers($container);
        $this->processGridColumnDataPresenter($container);
    }

    /**
     * Finds row accessibility checkers and adds them to row accessibility checker chain
     *
     * @param ContainerBuilder $container
     */
    private function processGridRowAccessibilityCheckers(ContainerBuilder $container)
    {
        if (!$container->has('prestashop.core.grid.presenter.accessibility_checker.row.row_accessibility_checker_chain')) {
            return;
        }

        $chain = $container->findDefinition('prestashop.core.grid.presenter.accessibility_checker.row.row_accessibility_checker_chain');
        $checkers = $container->findTaggedServiceIds(self::ROW_ACTION_ACCESSIBILITY_CHECKER_TAG_NAME);

        foreach ($checkers as $id => $tags) {
            $chain->addMethodCall('addChecker', [new Reference($id)]);
        }
    }

    /**
     * Collects column data presenters into chain
     *
     * @param ContainerBuilder $container
     */
    private function processGridColumnDataPresenter(ContainerBuilder $container)
    {
        if (!$container->has('prestashop.core.grid.presenter.column.column_data_presenter_chain')) {
            return;
        }

        $chain = $container->findDefinition('prestashop.core.grid.presenter.column.column_data_presenter_chain');
        $columnPresenters = $container->findTaggedServiceIds(self::COLUMN_DATA_PRESENTER_TAG_NAME);

        foreach ($columnPresenters as $id => $tags) {
            $chain->addMethodCall('addColumnDataPresenter', [new Reference($id)]);
        }
    }
}
