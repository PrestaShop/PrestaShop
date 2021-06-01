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

namespace PrestaShop\PrestaShop\Core\Domain\Order\Exception;

use Throwable;

/**
 * Throw new when a partial refund's is asked without a specified quantity
 */
class InvalidCancelProductException extends OrderException
{
    /**
     * @var int
     */
    private $refundableQuantity;

    /**
     * Used when the quantity refunded is not strictly positive
     */
    public const INVALID_QUANTITY = 1;

    /**
     * Used when the quantity refunded is higher than the remaining quantity
     */
    public const QUANTITY_TOO_HIGH = 2;

    /**
     * Used when the amount refunded is not strictly positive
     */
    public const INVALID_AMOUNT = 3;

    /**
     * Used when no refund details have been supplied (nor products nor shipping refund)
     */
    public const NO_REFUNDS = 4;

    /**
     * Used when no generation is set (no credit slip and no voucher generation)
     */
    public const NO_GENERATION = 5;

    /**
     * @param int $code
     * @param int $refundableQuantity
     * @param string $message
     * @param Throwable|null $previous
     */
    public function __construct($code = 0, int $refundableQuantity = 0, $message = '', Throwable $previous = null)
    {
        parent::__construct($message, $code, $previous);
        $this->refundableQuantity = $refundableQuantity;
    }

    /**
     * @return int
     */
    public function getRefundableQuantity(): int
    {
        return $this->refundableQuantity;
    }
}
