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
use PrestaShop\PrestaShop\Core\Domain\Product\ValueObject\ProductId;

/**
 * Adds product to an existing order.
 */
class AddProductToOrderCommand
{
    /**
     * @var OrderId
     */
    private $orderId;

    /**
     * @var ProductId
     */
    private $productId;

    /**
     * @var int
     */
    private $combinationId;

    /**
     * @var float
     */
    private $productPriceTaxIncluded;

    /**
     * @var float
     */
    private $productPriceTaxExcluded;

    /**
     * @var int
     */
    private $productQuantity;

    /**
     * @var int|null invoice id or null if new invoice should be created
     */
    private $orderInvoiceId;

    /**
     * @var bool|null bool if product is being added using new invoice
     */
    private $isFreeShipping;

    /**
     * Add product to an order with new invoice. It applies to orders that were already paid and waiting for payment.
     *
     * @param int $orderId
     * @param int $productId
     * @param int $combinationId
     * @param float $productPriceTaxIncluded
     * @param float $productPriceTaxExcluded
     * @param int $productQuantity
     * @param bool $isFreeShipping
     *
     * @return self
     */
    public static function withNewInvoice(
        $orderId,
        $productId,
        $combinationId,
        $productPriceTaxIncluded,
        $productPriceTaxExcluded,
        $productQuantity,
        $isFreeShipping
    ) {
        $command = new self(
            $orderId,
            $productId,
            $combinationId,
            $productPriceTaxIncluded,
            $productPriceTaxExcluded,
            $productQuantity
        );

        $command->isFreeShipping = $isFreeShipping;

        return $command;
    }

    /**
     * Add product to an order using existing invoice. It applies only for orders that were not yet paid.
     *
     * @param int $orderId
     * @param int $orderInvoiceId
     * @param int $productId
     * @param int $combinationId
     * @param float $productPriceTaxIncluded
     * @param float $productPriceTaxExcluded
     * @param int $productQuantity
     *
     * @return self
     */
    public static function toExistingInvoice(
        $orderId,
        $orderInvoiceId,
        $productId,
        $combinationId,
        $productPriceTaxIncluded,
        $productPriceTaxExcluded,
        $productQuantity
    ) {
        $command = new self(
            $orderId,
            $productId,
            $combinationId,
            $productPriceTaxIncluded,
            $productPriceTaxExcluded,
            $productQuantity
        );

        $command->orderInvoiceId = $orderInvoiceId;

        return $command;
    }

    /**
     * @param int $orderId
     * @param int $productId
     * @param int $combinationId
     * @param float $productPriceTaxIncluded
     * @param float $productPriceTaxExcluded
     * @param int $productQuantity
     */
    private function __construct(
        $orderId,
        $productId,
        $combinationId,
        $productPriceTaxIncluded,
        $productPriceTaxExcluded,
        $productQuantity
    ) {
        $this->orderId = new OrderId($orderId);
        $this->productId = new ProductId($productId);
        $this->combinationId = $combinationId;
        $this->productPriceTaxIncluded = $productPriceTaxIncluded;
        $this->productPriceTaxExcluded = $productPriceTaxExcluded;
        $this->productQuantity = $productQuantity;
    }

    /**
     * @return OrderId
     */
    public function getOrderId()
    {
        return $this->orderId;
    }

    /**
     * @return ProductId
     */
    public function getProductId()
    {
        return $this->productId;
    }

    /**
     * @return int
     */
    public function getCombinationId()
    {
        return $this->combinationId;
    }

    /**
     * @return float
     */
    public function getProductPriceTaxIncluded()
    {
        return $this->productPriceTaxIncluded;
    }

    /**
     * @return float
     */
    public function getProductPriceTaxExcluded()
    {
        return $this->productPriceTaxExcluded;
    }

    /**
     * @return int
     */
    public function getProductQuantity()
    {
        return $this->productQuantity;
    }

    /**
     * @return int|null
     */
    public function getOrderInvoiceId()
    {
        return $this->orderInvoiceId;
    }

    /**
     * @return bool|null
     */
    public function isFreeShipping()
    {
        return $this->isFreeShipping;
    }
}
