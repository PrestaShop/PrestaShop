<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Core\Domain\Order\Command;

use InvalidArgumentException;
use PrestaShop\Decimal\DecimalNumber;
use PrestaShop\PrestaShop\Core\Domain\Order\Exception\InvalidAmountException;
use PrestaShop\PrestaShop\Core\Domain\Order\Exception\InvalidCancelProductException;
use PrestaShop\PrestaShop\Core\Domain\Order\Exception\OrderException;
use PrestaShop\PrestaShop\Core\Domain\Order\ValueObject\OrderId;

/**
 * Command abstract class for refund commands
 */
abstract class AbstractRefundCommand
{
    /**
     * @var OrderId
     */
    protected $orderId;

    /**
     * @var array
     */
    protected $orderDetailRefunds;

    /**
     * @var bool
     */
    protected $restockRefundedProducts;

    /**
     * @var bool
     */
    protected $generateCreditSlip;

    /**
     * @var bool
     */
    protected $generateVoucher;

    /**
     * @var int
     */
    protected $voucherRefundType;

    /**
     * @var DecimalNumber|null
     */
    protected $voucherRefundAmount;

    /**
     * @param int $orderId
     * @param array $orderDetailRefunds
     * @param bool $restockRefundedProducts
     * @param bool $generateVoucher
     * @param bool $generateCreditSlip
     * @param int $voucherRefundType
     * @param string|null $voucherRefundAmount
     *
     * @throws InvalidCancelProductException
     * @throws OrderException
     */
    public function __construct(
        int $orderId,
        array $orderDetailRefunds,
        bool $restockRefundedProducts,
        bool $generateCreditSlip,
        bool $generateVoucher,
        int $voucherRefundType,
        ?string $voucherRefundAmount = null
    ) {
        $this->orderId = new OrderId($orderId);
        $this->restockRefundedProducts = $restockRefundedProducts;
        $this->generateCreditSlip = $generateCreditSlip;
        $this->generateVoucher = $generateVoucher;
        $this->voucherRefundType = $voucherRefundType;
        if (null !== $voucherRefundAmount) {
            try {
                $this->voucherRefundAmount = new DecimalNumber($voucherRefundAmount);
            } catch (InvalidArgumentException) {
                throw new InvalidAmountException();
            }
        }
        $this->setOrderDetailRefunds($orderDetailRefunds);
        if (!$this->generateCreditSlip && !$this->generateVoucher) {
            throw new InvalidCancelProductException(InvalidCancelProductException::NO_GENERATION);
        }
    }

    /**
     * @return OrderId
     */
    public function getOrderId(): OrderId
    {
        return $this->orderId;
    }

    /**
     * @return array
     */
    public function getOrderDetailRefunds(): array
    {
        return $this->orderDetailRefunds;
    }

    /**
     * @return bool
     */
    public function restockRefundedProducts(): bool
    {
        return $this->restockRefundedProducts;
    }

    /**
     * @return bool
     */
    public function generateCreditSlip(): bool
    {
        return $this->generateCreditSlip;
    }

    /**
     * @return bool
     */
    public function generateVoucher(): bool
    {
        return $this->generateVoucher;
    }

    /**
     * @return int
     */
    public function getVoucherRefundType(): int
    {
        return $this->voucherRefundType;
    }

    /**
     * @return DecimalNumber|null
     */
    public function getVoucherRefundAmount(): ?DecimalNumber
    {
        return $this->voucherRefundAmount;
    }

    /**
     * @param array $orderDetailRefunds
     *
     * @throws InvalidCancelProductException
     * @throws OrderException
     */
    abstract protected function setOrderDetailRefunds(array $orderDetailRefunds);
}
