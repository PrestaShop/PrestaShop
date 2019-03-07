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

namespace PrestaShop\PrestaShop\Core\Grid\Presenter;

use PrestaShop\PrestaShop\Core\Grid\Column\ColumnInterface;
use PrestaShop\PrestaShop\Core\Grid\GridInterface;

/**
 * Wraps grid into array which is ready for displaying in Twig templates
 */
final class TwigGridPresenter implements GridPresenterInterface
{
    /**
     * @var ColumnDataPresenterChainInterface
     */
    private $columnDataPresenterChain;

    public function __construct(ColumnDataPresenterChainInterface $columnPresenterChain)
    {
        $this->columnDataPresenterChain = $columnPresenterChain;
    }

    /**
     * {@inheritdoc}
     */
    public function present(GridInterface $grid)
    {
        return [
            'id' => $grid->getDefinition()->getId(),
            'name' => $grid->getDefinition()->getName(),
            'data' => [
                'records' => $this->presentRecords($grid),
                'records_total' => $grid->getData()->getRecordsTotal(),
                'query' => $grid->getData()->getQuery(),
            ],
        ];
    }

    private function presentRecords(GridInterface $grid)
    {
        $presentedRecords = [];

        foreach ($grid->getData()->getRecords() as $i => $record) {
            /** @var ColumnInterface $column */
            foreach ($grid->getDefinition()->getColumns() as $column) {
                $presentedRecords[$i][$column->getId()] = $this->columnDataPresenterChain->present(
                    $column,
                    $record,
                    $grid->getDefinition()->getId()
                );
            }
        }

        return $presentedRecords;
    }
}
