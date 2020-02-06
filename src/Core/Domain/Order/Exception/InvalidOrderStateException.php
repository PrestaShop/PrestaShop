<?php
/**
 * 2007-2020 PrestaShop SA and Contributors
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
 * @copyright 2007-2020 PrestaShop SA and Contributors
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 * International Registered Trademark & Property of PrestaShop SA
 */

namespace PrestaShop\PrestaShop\Core\Domain\Order\Exception;

/**
 * Thrown when the order state is incompatible with an action (ex: standard
 * refund on an order not paid yet).
 */
class InvalidOrderStateException extends OrderException
{
    /**
     * Used when the order has no invoice (and it should have)
     */
    const INVOICE_NOT_FOUND = 1;

    /**
     * Used when the order has an invoice (and it should not)
     */
    const UNEXPECTED_INVOICE = 2;

    /**
     * Used when the order has not been delivered (and it should have)
     */
    const DELIVERY_NOT_FOUND = 3;

    /**
     * Used when the order has been delivered (and it shouldn't have)
     */
    const UNEXPECTED_DELIVERY = 4;

    /**
     * @param int $code
     * @param string $message
     * @param Throwable|null $previous
     */
    public function __construct($code = 0, $message = '', Throwable $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }
}
