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
 * needs please refer to https://devdocs.prestashop.com/ for more information.
 *
 * @author    PrestaShop SA and Contributors <contact@prestashop.com>
 * @copyright Since 2007 PrestaShop SA and Contributors
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 */

namespace PrestaShop\PrestaShop\Core\Domain\Shipment\QueryResult;

class OrderShipmentProduct
{
    public function __construct(
        private int $orderDetailId,
        private int $quantity,
        private string $productName,
        private string $productReference,
        private string $productImagePath,
    ) {
    }

    /**
     * @return int
     */
    public function getOrderDetailId(): int
    {
        return $this->orderDetailId;
    }

    /**
     * @return string
     */
    public function getProductName(): string
    {
        return $this->productName;
    }

    /**
     * @return int
     */
    public function getQuantity(): int
    {
        return $this->quantity;
    }

    /**
     * @return string
     */
    public function getProductReference(): string
    {
        return $this->productReference;
    }

    /**
     * @return string
     */
    public function getProductImagePath(): string
    {
        return $this->productImagePath;
    }

    public function toArray(): array
    {
        return [
            'order_detail_id' => $this->getOrderDetailId(),
            'quantity' => $this->getQuantity(),
            'product_name' => $this->getProductName(),
            'product_reference' => $this->getProductReference(),
            'product_image_path' => $this->getProductImagePath(),
        ];
    }
}
