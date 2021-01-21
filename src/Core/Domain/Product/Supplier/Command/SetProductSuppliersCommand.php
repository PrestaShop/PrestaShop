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

use PrestaShop\PrestaShop\Core\Domain\Product\Supplier\Exception\ProductSupplierException;
use PrestaShop\PrestaShop\Core\Domain\Product\Supplier\ProductSupplier;
use PrestaShop\PrestaShop\Core\Domain\Product\ValueObject\ProductId;
use PrestaShop\PrestaShop\Core\Domain\Supplier\ValueObject\SupplierId;
use RuntimeException;

/**
 * Updates product suppliers
 */
class SetProductSuppliersCommand
{
    /**
     * @var ProductId
     */
    private $productId;

    /**
     * @var ProductSupplier[]
     */
    private $productSuppliers;

    /**
     * @var SupplierId
     */
    private $defaultSupplierId;

    /**
     * @param int $productId
     * @param array<string, mixed> $productSuppliers
     * @param int $defaultSupplierId
     *
     * @see SetProductSuppliersCommand::setProductSuppliers() for $productSuppliers structure
     */
    public function __construct(int $productId, array $productSuppliers, int $defaultSupplierId)
    {
        $this->setProductSuppliers($productSuppliers);
        $this->productId = new ProductId($productId);
        $this->defaultSupplierId = new SupplierId($defaultSupplierId);
        $this->assertDefaultSupplierIsOneOfProvidedSuppliers();
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
     * @return SupplierId
     */
    public function getDefaultSupplierId(): SupplierId
    {
        return $this->defaultSupplierId;
    }

    /**
     * @param array[] $productSuppliers
     */
    private function setProductSuppliers(array $productSuppliers): void
    {
        if (empty($productSuppliers)) {
            throw new RuntimeException(sprintf(
                'Empty array of product suppliers provided in %s. To remove all product suppliers use %s.',
                self::class,
                RemoveAllAssociatedProductSuppliersCommand::class
            ));
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

    /**
     * @throws ProductSupplierException
     */
    private function assertDefaultSupplierIsOneOfProvidedSuppliers(): void
    {
        $defaultSupplierId = $this->getDefaultSupplierId()->getValue();

        foreach ($this->productSuppliers as $productSupplier) {
            if ($productSupplier->getSupplierId() === $defaultSupplierId) {
                return;
            }
        }

        throw new ProductSupplierException('Default supplier must be one of provided suppliers');
    }
}
