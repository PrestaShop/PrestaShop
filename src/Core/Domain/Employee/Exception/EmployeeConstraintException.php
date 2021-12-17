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

namespace PrestaShop\PrestaShop\Core\Domain\Employee\Exception;

/**
 * Is thrown when employee constraint is violated
 */
class EmployeeConstraintException extends EmployeeException
{
    /**
     * @var int Code is used when invalid email is provided for employee
     */
    public const INVALID_EMAIL = 1;

    /**
     * @var int Code is used when invalid first name is provided for employee
     */
    public const INVALID_FIRST_NAME = 2;

    /**
     * @var int Code is used when invalid last name is provided for employee
     */
    public const INVALID_LAST_NAME = 3;

    /**
     * @var int code is used when password of invalid length is provided for employee
     */
    public const INVALID_PASSWORD = 4;

    /**
     * @var int Code is used when incorrect password is provided for employee
     */
    public const INCORRECT_PASSWORD = 5;
}
