<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Core\Domain\Customer\QueryResult;

/**
 * Class CustomerOrderInformation.
 */
class OrderInformation
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
     * @param int $orderId
     * @param string $orderPlacedDate
     * @param string $paymentMethodName
     * @param string $orderStatus
     * @param int $orderProductsCount
     * @param string $totalPaid
     */
    public function __construct($orderId, $orderPlacedDate, $paymentMethodName, $orderStatus, $orderProductsCount, $totalPaid)
    {
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
    public function getOrderId()
    {
        return $this->orderId;
    }

    /**
     * @return string
     */
    public function getOrderPlacedDate()
    {
        return $this->orderPlacedDate;
    }

    /**
     * @return string
     */
    public function getPaymentMethodName()
    {
        return $this->paymentMethodName;
    }

    /**
     * @return string
     */
    public function getOrderStatus()
    {
        return $this->orderStatus;
    }

    /**
     * @return int
     */
    public function getOrderProductsCount()
    {
        return $this->orderProductsCount;
    }

    /**
     * @return string
     */
    public function getTotalPaid()
    {
        return $this->totalPaid;
    }
}
