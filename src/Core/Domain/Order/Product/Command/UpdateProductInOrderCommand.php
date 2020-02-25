<?php
/**
 * 2007-2019 PrestaShop SA and Contributors
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
 * @copyright 2007-2019 PrestaShop SA and Contributors
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 * International Registered Trademark & Property of PrestaShop SA
 */

namespace PrestaShop\PrestaShop\Core\Domain\Order\Product\Command;

use PrestaShop\PrestaShop\Core\Domain\Order\ValueObject\OrderId;

/**
 * Updates product in given order.
 */
class UpdateProductInOrderCommand
{
    /**
     * @var OrderId
     */
    private $orderId;

    /**
     * @var int
     */
    private $orderDetailId;

    /**
     * @var float
     */
    private $priceTaxIncluded;

    /**
     * @var float
     */
    private $priceTaxExcluded;

    /**
     * @var int
     */
    private $quantity;

    /**
     * @var int|null
     */
    private $orderInvoiceId;

    /**
     * @param int $orderId
     * @param int $orderDetailId
     * @param float $priceTaxIncluded
     * @param float $priceTaxExcluded
     * @param int $quantity
     * @param int|null $orderInvoiceId
     */
    public function __construct(
        $orderId,
        $orderDetailId,
        $priceTaxIncluded,
        $priceTaxExcluded,
        $quantity,
        $orderInvoiceId = null
    ) {
        $this->orderId = new OrderId($orderId);
        $this->orderDetailId = $orderDetailId;
        $this->priceTaxIncluded = $priceTaxIncluded;
        $this->priceTaxExcluded = $priceTaxExcluded;
        $this->quantity = $quantity;
        $this->orderInvoiceId = $orderInvoiceId;
    }

    /**
     * @return OrderId
     */
    public function getOrderId()
    {
        return $this->orderId;
    }

    /**
     * @return int
     */
    public function getOrderDetailId()
    {
        return $this->orderDetailId;
    }

    /**
     * @return float
     */
    public function getPriceTaxIncluded()
    {
        return $this->priceTaxIncluded;
    }

    /**
     * @return float
     */
    public function getPriceTaxExcluded()
    {
        return $this->priceTaxExcluded;
    }

    /**
     * @return int
     */
    public function getQuantity()
    {
        return $this->quantity;
    }

    /**
     * @return int|null
     */
    public function getOrderInvoiceId()
    {
        return $this->orderInvoiceId;
    }
}
