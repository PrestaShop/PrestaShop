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

namespace PrestaShop\PrestaShop\Core\Grid\Presenter\AccessbilityChecker\Row;

use PrestaShop\PrestaShop\Core\Grid\Action\Row\RowActionInterface;

/**
 * Checks if "Delete" action can be performed on employee
 */
final class EmployeeDeleteAccessibilityChecker
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
     * Check if "Delete" row action is granted for employee
     *
     * @param array $record
     *
     * @return bool
     */
    public function isGranted(array $record)
    {
        if ($this->contextEmployeeId === (int) $record['id_employee']) {
            // employee cannot delete it's own account
            return false;
        }

        return true;
    }

    /**
     * Checking is only supported for "Delete" action in "Employees" grid
     *
     * @param RowActionInterface $action
     * @param string $gridId
     *
     * @return bool
     */
    public function supports(RowActionInterface $action, $gridId)
    {
        return $action->getId() === 'delete' && 'employee' === $gridId;
    }
}
