<?php
/**
 * 2007-2020 Friends of PrestaShop
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Academic Free License 3.0 (AFL-3.0).
 * It is also available through the world-wide-web at this URL: https://opensource.org/licenses/AFL-3.0
 */

namespace PrestaShop\PrestaShop\Core\Domain\Order\ValueObject;

use InvalidArgumentException;
use PrestaShop\Decimal\Number;
use PrestaShop\PrestaShop\Core\Domain\Order\Exception\InvalidAmountException;
use PrestaShop\PrestaShop\Core\Domain\Order\Exception\InvalidCancelProductException;
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
     * @param string $refundedAmount
     *
     * @return self
     *
     * @throws InvalidCancelProductException
     * @throws OrderException
     */
    public static function createPartialRefund(int $orderDetailId, int $productQuantity, string $refundedAmount): self
    {
        try {
            $decimalRefundedAmount = new Number($refundedAmount);
        } catch (InvalidArgumentException $e) {
            throw new InvalidAmountException();
        }

        if ($decimalRefundedAmount->isLowerOrEqualThan(new Number('0'))) {
            throw new InvalidCancelProductException(InvalidCancelProductException::INVALID_AMOUNT);
        }

        return new self($orderDetailId, $productQuantity, $decimalRefundedAmount);
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
     * @param Number|null $refundedAmount
     *
     * @throws OrderException
     */
    private function __construct(int $orderDetailId, int $productQuantity, ?Number $refundedAmount)
    {
        $this->assertOrderDetailIdIsGreaterThanZero($orderDetailId);
        if (0 >= $productQuantity) {
            throw new InvalidCancelProductException(InvalidCancelProductException::INVALID_QUANTITY);
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
     * @return Number|null
     */
    public function getRefundedAmount(): ?Number
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
