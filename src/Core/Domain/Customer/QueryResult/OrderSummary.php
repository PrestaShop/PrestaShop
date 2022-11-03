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
 * needs please refer to https://devdocs.prestashop-project.org/ for more information.
 *
 * @author    PrestaShop SA and Contributors <contact@prestashop.com>
 * @copyright Since 2007 PrestaShop SA and Contributors
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
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
