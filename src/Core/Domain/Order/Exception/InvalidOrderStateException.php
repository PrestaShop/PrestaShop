<?php
/**
 * 2007-2020 Friends of PrestaShop
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Academic Free License 3.0 (AFL-3.0).
 * It is also available through the world-wide-web at this URL: https://opensource.org/licenses/AFL-3.0
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
