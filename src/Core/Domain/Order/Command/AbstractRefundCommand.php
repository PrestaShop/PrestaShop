<?php
/**
 * 2007-2020 Friends of PrestaShop
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Academic Free License 3.0 (AFL-3.0).
 * It is also available through the world-wide-web at this URL: https://opensource.org/licenses/AFL-3.0
 */

namespace PrestaShop\PrestaShop\Core\Domain\Order\Command;

use InvalidArgumentException;
use PrestaShop\Decimal\Number;
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
     * @var Number|null
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
                $this->voucherRefundAmount = new Number($voucherRefundAmount);
            } catch (InvalidArgumentException $e) {
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
     * @return Number|null
     */
    public function getVoucherRefundAmount(): ?Number
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
