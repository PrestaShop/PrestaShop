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

use LogicException;
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
     * Builds command to replace all product existing suppliers to provided ones
     *
     * @param int $productId
     * @param array $productSuppliers
     * @param int|null $defaultSupplierId if not provided, the first supplier in list becomes default
     *
     * @return UpdateProductSuppliersCommand
     */
    public static function replace(int $productId, array $productSuppliers, ?int $defaultSupplierId = null): self
    {
        if (empty($productSuppliers)) {
            throw new LogicException(sprintf(
                'Providing empty array will remove all productSuppliers. Use %s::deleteAll()',
                self::class
            ));
        }

        return new self($productId, $defaultSupplierId, $productSuppliers);
    }

    /**
     * Builds command to delete all product suppliers except the default one
     *
     * @param int $productId
     *
     * @return UpdateProductSuppliersCommand
     */
    public static function deleteAll(int $productId): self
    {
        return new self($productId, null, []);
    }

    /**
     * Builds command to update only default supplier
     *
     * @param int $productId
     * @param int $defaultSupplierId
     *
     * @return UpdateProductSuppliersCommand
     */
    public static function updateOnlyDefault(int $productId, int $defaultSupplierId): self
    {
        return new self($productId, $defaultSupplierId);
    }

    /**
     * @return ProductId
     */
    public function getProductId(): ProductId
    {
        return $this->productId;
    }

    /**
     * @return ProductSupplier[]|null
     */
    public function getProductSuppliers(): ?array
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
     * Use static factories to initiate this class
     *
     * @param int $productId
     * @param int|null $defaultSupplierId
     * @param array $productSuppliers
     *
     * @throws SupplierException
     */
    private function __construct(int $productId, ?int $defaultSupplierId = null, ?array $productSuppliers = null)
    {
        $this->productId = new ProductId($productId);
        $this->defaultSupplierId = $defaultSupplierId !== null ? new SupplierId($defaultSupplierId) : null;

        if (is_array($productSuppliers)) {
            $this->setProductSuppliers($productSuppliers);
        }
    }

    /**
     * @param array[] $productSuppliers
     */
    private function setProductSuppliers(array $productSuppliers): void
    {
        // empty array is handled differently than null.
        if (empty($productSuppliers)) {
            $this->productSuppliers = [];

            return;
        }

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
