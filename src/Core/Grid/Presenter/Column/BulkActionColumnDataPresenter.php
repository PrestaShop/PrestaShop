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

use PrestaShop\PrestaShop\Core\Grid\Column\ColumnInterface;
use PrestaShop\PrestaShop\Core\Grid\Column\Type\Common\BulkActionColumn;

/**
 * Prepares "BulkActionColumn" for rendering
 */
final class BulkActionColumnDataPresenter implements ColumnDataPresenterInterface
{
    /**
     * @var int
     */
    private $contextEmployeeId;

    /**
     * @param int $contextEmployeeId
     */
    public function __construct($contextEmployeeId)
    {
        $this->contextEmployeeId = $contextEmployeeId;
    }

    /**
     * {@inheritdoc}
     */
    public function present(ColumnInterface $column, array $row, $gridId)
    {
        $options = $column->getOptions();

        if ('employee' === $gridId && !$this->canShowEmployeeBulkAction($row)) {
            return [
                'bulk_value' => null,
            ];
        }

        return [
            'bulk_value' => $row[$options['bulk_field']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function supports(ColumnInterface $column)
    {
        return $column instanceof BulkActionColumn;
    }

    /**
     * @param array $row
     *
     * @return bool
     */
    private function canShowEmployeeBulkAction(array $row)
    {
        if ($this->contextEmployeeId === (int) $row['id_employee']) {
            return false;
        }

        return true;
    }
}
