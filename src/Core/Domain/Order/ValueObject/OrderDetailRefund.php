<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Core\Domain\Order\ValueObject;

use InvalidArgumentException;
use PrestaShop\Decimal\DecimalNumber;
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
     * @var DecimalNumber|null
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
            $decimalRefundedAmount = new DecimalNumber($refundedAmount);
        } catch (InvalidArgumentException) {
            throw new InvalidAmountException();
        }

        if ($decimalRefundedAmount->isLowerOrEqualThanZero()) {
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
     * @param DecimalNumber|null $refundedAmount
     *
     * @throws OrderException
     */
    private function __construct(int $orderDetailId, int $productQuantity, ?DecimalNumber $refundedAmount)
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
     * @return DecimalNumber|null
     */
    public function getRefundedAmount(): ?DecimalNumber
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
