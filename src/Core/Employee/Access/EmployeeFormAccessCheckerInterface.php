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

namespace PrestaShop\PrestaShop\Core\Employee\Access;

/**
 * Interface EmployeeFormAccessCheckerInterface defines employee form access checker.
 */
interface EmployeeFormAccessCheckerInterface
{
    /**
     * Checks if employee has restricted access to the employee form.
     * Restricted access usually is used when an employee edits their own account.
     * Restricted access means that the employee is restricted from some of
     * the fields in the edit form, which would modify his account's accessibility.
     * E.g. active status, profile, shop association.
     *
     * @param int $employeeId
     *
     * @return bool
     */
    public function isRestrictedAccess($employeeId);

    /**
     * Check if context employee can access edit form for given employee.
     *
     * @param int $employeeId
     *
     * @return bool
     */
    public function canAccessEditFormFor($employeeId);

    /**
     * Check if context employee can access addons connect form control.
     *
     * @return bool
     */
    public function canAccessAddonsConnect();
}
