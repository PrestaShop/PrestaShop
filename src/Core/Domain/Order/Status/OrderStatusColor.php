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

namespace PrestaShop\PrestaShop\Core\Domain\Order\Status;

/**
 * Defines colors for order statuses
 */
class OrderStatusColor
{
    /**
     * Used for statuses that are waiting for customer actions.
     * Example statuses: Awaiting bank wire payment, Awaiting check payment, On backorder (not paid).
     */
    public const AWAITING_PAYMENT = '#34209E';

    /**
     * Used for statuses when further merchant action is required.
     * Example statuses: Processing in progress, On backorder (paid), Payment accepted.
     */
    public const ACCEPTED_PAYMENT = '#3498D8';

    /**
     * Used for statuses when no actions are required anymore.
     * Example statuses: Shipped, Refunded, Delivered.
     */
    public const COMPLETED = '#01b887';

    /**
     * Used for error statuses.
     * Example statuses: Payment error.
     */
    public const ERROR = '#E74C3C';

    /**
     * Used for statuses with special cases.
     * Example statuses: Canceled.
     */
    public const SPECIAL = '#2C3E50';

    /**
     * Class is not meant to be initialized.
     */
    private function __construct()
    {
    }
}
