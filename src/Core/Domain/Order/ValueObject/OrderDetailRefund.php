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

namespace PrestaShop\PrestaShop\Core\Domain\Order\ValueObject;

use PrestaShop\PrestaShop\Core\Domain\Order\Exception\InvalidRefundAmountException;
use PrestaShop\PrestaShop\Core\Domain\Order\Exception\InvalidRefundQuantityException;
use PrestaShop\PrestaShop\Core\Domain\Order\Exception\OrderException;

/**
 * Class ProductRefund
 */
class OrderDetailRefund
{
    /**
     * @var int
     */
    private $orderDetailId;

    /**
     * @var int
     */
    private $productQuantity;

    /**
     * @var float|null
     */
    private $refundedAmount;

    /**
     * @param int $orderDetailId
     * @param int $productQuantity
     * @param float $refundedAmount
     *
     * @return self
     *
     * @throws InvalidRefundAmountException
     * @throws OrderException
     */
    public static function createPartialRefund(int $orderDetailId, int $productQuantity, float $refundedAmount): self
    {
        if (0 >= $refundedAmount) {
            throw new InvalidRefundAmountException();
        }

        return new self($orderDetailId, $productQuantity, $refundedAmount);
    }

    /**
     * @param int $orderDetailId
     * @param int $productQuantity
     *
     * @return self
     *
     * @throws OrderException
     */
    public static function createStandardRefund(int $orderDetailId, int $productQuantity): self
    {
        return new self($orderDetailId, $productQuantity, null);
    }

    /**
     * @param int $orderDetailId
     * @param int $productQuantity
     * @param float|null $refundedAmount
     *
     * @throws OrderException
     */
    private function __construct(int $orderDetailId, int $productQuantity, ?float $refundedAmount)
    {
        $this->assertOrderDetailIdIsGreaterThanZero($orderDetailId);
        if (0 >= $productQuantity) {
            throw new InvalidRefundQuantityException( InvalidRefundQuantityException::EMPTY_QUANTITY);
        }
        $this->orderDetailId = $orderDetailId;
        $this->productQuantity = $productQuantity;
        $this->refundedAmount = $refundedAmount;
    }

    /**
     * @return int
     */
    public function getOrderDetailId(): int
    {
        return $this->orderDetailId;
    }

    /**
     * @return int
     */
    public function getProductQuantity(): int
    {
        return $this->productQuantity;
    }

    /**
     * @return float|null
     */
    public function getRefundedAmount(): ?float
    {
        return $this->refundedAmount;
    }

    /**
     * @param int $orderDetailId
     *
     * @throws OrderException
     */
    private function assertOrderDetailIdIsGreaterThanZero(int $orderDetailId)
    {
        if (0 > $orderDetailId) {
            throw new OrderException(sprintf('Order detail id %s is invalid. Order detail id must be number that is greater than zero.', var_export($orderDetailId, true)));
        }
    }
}
