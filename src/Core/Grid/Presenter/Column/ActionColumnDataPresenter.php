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

namespace PrestaShop\PrestaShop\Core\Grid\Presenter\Column;

use PrestaShop\PrestaShop\Core\Grid\Action\Row\RowActionInterface;
use PrestaShop\PrestaShop\Core\Grid\Column\ColumnInterface;
use PrestaShop\PrestaShop\Core\Grid\Column\Type\Common\ActionColumn;
use PrestaShop\PrestaShop\Core\Grid\Presenter\AccessbilityChecker\Row\RowAccessibilityCheckerChain;
use PrestaShop\PrestaShop\Core\Grid\Presenter\ColumnDataPresenterInterface;

/**
 * Prepares data for action column ready for rendering
 */
final class ActionColumnDataPresenter implements ColumnDataPresenterInterface
{
    /**
     * @var RowAccessibilityCheckerChain
     */
    private $accessibilityCheckerChain;

    public function __construct(RowAccessibilityCheckerChain $accessibilityCheckerChain)
    {
        $this->accessibilityCheckerChain = $accessibilityCheckerChain;
    }

    /**
     * {@inheritdoc}
     */
    public function present(ColumnInterface $column, array $record, $gridId)
    {
        $options = $column->getOptions();
        $presentedActions = [];

        if (null !== $options['actions']) {
            /** @var RowActionInterface $action */
            foreach ($options['actions'] as $action) {
                if (!$this->accessibilityCheckerChain->check($action, $record, $gridId)) {
                    continue;
                }

                // @todo: present action
                $presentedActions[] = [
                    // ...
                ];
            }
        }

        return [
            'actions' => $presentedActions,
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function supports(ColumnInterface $column)
    {
        return $column instanceof ActionColumn;
    }
}
