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

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Core\Domain\Product\Supplier\Command;

use PrestaShop\PrestaShop\Core\Domain\Product\Supplier\ProductSupplier;
use PrestaShop\PrestaShop\Core\Domain\Product\ValueObject\ProductId;
use PrestaShop\PrestaShop\Core\Domain\Supplier\Exception\SupplierException;
use PrestaShop\PrestaShop\Core\Domain\Supplier\ValueObject\SupplierId;

/**
 * Updates product suppliers
 */
class UpdateProductSuppliersCommand
{
    /**
     * @var ProductId
     */
    private $productId;

    /**
     * @var ProductSupplier[]|null
     */
    private $productSuppliers;

    /**
     * @var SupplierId|null
     */
    private $defaultSupplierId;

    /**
     * @param int $productId
     */
    public function __construct(int $productId)
    {
        $this->productId = new ProductId($productId);
    }

    /**
     * @return ProductId
     */
    public function getProductId(): ProductId
    {
        return $this->productId;
    }

    /**
     * @return ProductSupplier[]
     */
    public function getProductSuppliers(): array
    {
        return $this->productSuppliers;
    }

    /**
     * @return SupplierId|null
     */
    public function getDefaultSupplierId(): ?SupplierId
    {
        return $this->defaultSupplierId;
    }

    /**
     * @param int $supplierId
     *
     * @throws SupplierException
     */
    public function setDefaultSupplierId(int $supplierId): self
    {
        $this->defaultSupplierId = new SupplierId($supplierId);

        return $this;
    }

    /**
     * @param array[] $productSuppliers
     */
    public function setProductSuppliers(array $productSuppliers): void
    {
        foreach ($productSuppliers as $productSupplier) {
            $this->productSuppliers[] = new ProductSupplier(
                $productSupplier['supplier_id'],
                $productSupplier['currency_id'],
                $productSupplier['reference'],
                $productSupplier['price_tax_excluded'],
                $productSupplier['combination_id'],
                $productSupplier['product_supplier_id'] ?? null
            );
        }
    }
}
