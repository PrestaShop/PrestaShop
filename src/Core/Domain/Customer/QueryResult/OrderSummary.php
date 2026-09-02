<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Core\Domain\Customer\QueryResult;

/**
 * Holds data of summarized order
 */
class OrderSummary
{
    /**
     * @var int
     */
    private $orderId;

    /**
     * @var string
     */
    private $orderPlacedDate;

    /**
     * @var string
     */
    private $paymentMethodName;

    /**
     * @var string
     */
    private $orderStatus;

    /**
     * @var int
     */
    private $orderProductsCount;

    /**
     * @var string
     */
    private $totalPaid;

    /**
     * OrderForOrderCreation constructor.
     *
     * @param int $orderId
     * @param string $orderPlacedDate
     * @param string $paymentMethodName
     * @param string $orderStatus
     * @param int $orderProductsCount
     * @param string $totalPaid
     */
    public function __construct(
        int $orderId, string $orderPlacedDate,
        string $paymentMethodName,
        string $orderStatus,
        int $orderProductsCount,
        string $totalPaid
    ) {
        $this->orderId = $orderId;
        $this->orderPlacedDate = $orderPlacedDate;
        $this->paymentMethodName = $paymentMethodName;
        $this->orderStatus = $orderStatus;
        $this->orderProductsCount = $orderProductsCount;
        $this->totalPaid = $totalPaid;
    }

    /**
     * @return int
     */
    public function getOrderId(): int
    {
        return $this->orderId;
    }

    /**
     * @return string
     */
    public function getOrderPlacedDate(): string
    {
        return $this->orderPlacedDate;
    }

    /**
     * @return string
     */
    public function getPaymentMethodName(): string
    {
        return $this->paymentMethodName;
    }

    /**
     * @return string
     */
    public function getOrderStatus(): string
    {
        return $this->orderStatus;
    }

    /**
     * @return int
     */
    public function getOrderProductsCount(): int
    {
        return $this->orderProductsCount;
    }

    /**
     * @return string
     */
    public function getTotalPaid(): string
    {
        return $this->totalPaid;
    }
}
