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
 * needs please refer to https://devdocs.prestashop-project.org/ for more information.
 *
 * @author    PrestaShop SA and Contributors <contact@prestashop.com>
 * @copyright Since 2007 PrestaShop SA and Contributors
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Core\Domain\OrderReturn\Exception;

/**
 * Is thrown when order return constraint is violated
 */
class OrderReturnConstraintException extends OrderReturnException
{
    /**
     * When order return id is not valid
     */
    public const INVALID_ID = 10;

    /**
     * When customer id is not valid
     */
    public const INVALID_ID_CUSTOMER = 20;

    /**
     * When order id is not valid
     */
    public const INVALID_ID_ORDER = 30;

    /**
     * When state is not valid
     */
    public const INVALID_STATE = 40;

    /**
     * When question is not valid
     */
    public const INVALID_QUESTION = 50;

    /**
     * When date added is not valid
     */
    public const INVALID_DATE_ADD = 60;

    /**
     * When date updated is not valid
     */
    public const INVALID_DATE_UPD = 70;
}
