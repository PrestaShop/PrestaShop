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

namespace PrestaShop\PrestaShop\Adapter\Product;

use PrestaShop\PrestaShop\Adapter\Supplier\SupplierProvider;
use PrestaShop\PrestaShop\Core\Domain\Product\Exception\CannotUpdateProductException;
use PrestaShop\PrestaShop\Core\Domain\Product\Supplier\Exception\CannotUpdateProductSupplierException;
use PrestaShop\PrestaShop\Core\Domain\Product\Supplier\Exception\ProductSupplierNotFoundException;
use PrestaShop\PrestaShop\Core\Domain\Product\Supplier\ProductSupplierDeleterInterface;
use PrestaShop\PrestaShop\Core\Domain\Product\Supplier\ValueObject\ProductSupplierId;
use PrestaShop\PrestaShop\Core\Domain\Product\ValueObject\ProductId;
use PrestaShop\PrestaShop\Core\Domain\Supplier\ValueObject\SupplierId;
use PrestaShop\PrestaShop\Core\Exception\CoreException;
use Product;
use ProductSupplier;

class ProductSupplierUpdater
{
    /**
     * @var ProductProvider
     */
    private $productProvider;

    /**
     * @var ProductPersister
     */
    private $productPersister;

    /**
     * @var SupplierProvider
     */
    private $supplierProvider;

    /**
     * @var ProductSupplierPersister
     */
    private $productSupplierPersister;

    /**
     * @var ProductSupplierDeleterInterface
     */
    private $productSupplierDeleter;

    /**
     * @param ProductProvider $productProvider
     * @param ProductPersister $productPersister
     * @param SupplierProvider $supplierProvider
     * @param ProductSupplierPersister $productSupplierPersister
     * @param ProductSupplierDeleterInterface $productSupplierDeleter
     */
    public function __construct(
        ProductProvider $productProvider,
        ProductPersister $productPersister,
        SupplierProvider $supplierProvider,
        ProductSupplierPersister $productSupplierPersister,
        ProductSupplierDeleterInterface $productSupplierDeleter
    ) {
        $this->productProvider = $productProvider;
        $this->productPersister = $productPersister;
        $this->supplierProvider = $supplierProvider;
        $this->productSupplierPersister = $productSupplierPersister;
        $this->productSupplierDeleter = $productSupplierDeleter;
    }

    /**
     * @param ProductId $productId
     * @param SupplierId $defaultSupplierId
     * @param ProductSupplier[] $productSuppliers
     *
     * @throws CannotUpdateProductSupplierException
     * @throws CoreException
     * @throws ProductSupplierNotFoundException
     */
    public function setProductSuppliers(ProductId $productId, SupplierId $defaultSupplierId, array $productSuppliers): void
    {
        $deletableProductSupplierIds = $this->getDeletableProductSupplierIds($productId->getValue(), $productSuppliers);

        foreach ($productSuppliers as $productSupplier) {
            if ($productSupplier->id) {
                $this->productSupplierPersister->update($productSupplier);
            } else {
                $this->productSupplierPersister->add($productSupplier);
            }
        }

        $this->productSupplierDeleter->bulkDelete($deletableProductSupplierIds);
        $this->updateDefaultSupplier($productId, $defaultSupplierId);
    }

    /**
     * @param Product $product
     */
    public function resetDefaultSupplier(Product $product): void
    {
        $this->productPersister->update($product, [
            'supplier_reference' => '',
            'wholesale_price' => 0,
            'id_supplier' => 0,
        ], CannotUpdateProductException::FAILED_UPDATE_DEFAULT_SUPPLIER);
    }

    /**
     * @param ProductId $productId
     * @param SupplierId $supplierId
     */
    public function updateDefaultSupplier(ProductId $productId, SupplierId $supplierId): void
    {
        $product = $this->productProvider->get($productId);
        $supplierIdValue = $supplierId->getValue();
        $productIdValue = (int) $product->id;

        if ($product->hasCombinations()) {
            $this->resetDefaultSupplier($product);

            return;
        }

        if ((int) $product->id_supplier === $supplierIdValue) {
            return;
        }

        $this->supplierProvider->assertSupplierExists($supplierId);
        $productSupplierId = (int) ProductSupplier::getIdByProductAndSupplier($productIdValue, 0, $supplierIdValue);

        if (!$productSupplierId) {
            throw new ProductSupplierNotFoundException(sprintf(
                'Supplier #%d is not associated with product #%d', $supplierIdValue, $productIdValue
            ));
        }

        $this->productPersister->update($product, [
            'supplier_reference' => ProductSupplier::getProductSupplierReference($productIdValue, 0, $supplierIdValue),
            'wholesale_price' => ProductSupplier::getProductSupplierPrice($productIdValue, 0, $supplierIdValue),
            'id_supplier' => $supplierIdValue,
        ], CannotUpdateProductException::FAILED_UPDATE_DEFAULT_SUPPLIER);
    }

    /**
     * @param int $productId
     * @param ProductSupplier[] $providedProductSuppliers
     *
     * @return ProductSupplierId[]
     */
    private function getDeletableProductSupplierIds(int $productId, array $providedProductSuppliers): array
    {
        $existingProductSuppliers = ProductSupplier::getSupplierCollection($productId);
        $idsForDeletion = [];

        /** @var ProductSupplier $currentSupplier */
        foreach ($existingProductSuppliers as $currentSupplier) {
            $currentId = (int) $currentSupplier->id;
            $idsForDeletion[$currentId] = $currentId;
        }

        foreach ($providedProductSuppliers as $productSupplier) {
            $productSupplierId = (int) $productSupplier->id;

            if (isset($idsForDeletion[$productSupplierId])) {
                unset($idsForDeletion[$productSupplierId]);
            }
        }

        return $idsForDeletion;
    }
}
