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

namespace PrestaShop\PrestaShop\Adapter\Product\Update;

use PrestaShop\PrestaShop\Adapter\Product\Repository\ProductRepository;
use PrestaShop\PrestaShop\Adapter\Product\Repository\ProductSupplierRepository;
use PrestaShop\PrestaShop\Adapter\Supplier\Repository\SupplierRepository;
use PrestaShop\PrestaShop\Core\Domain\Product\Exception\CannotUpdateProductException;
use PrestaShop\PrestaShop\Core\Domain\Product\Supplier\Exception\CannotUpdateProductSupplierException;
use PrestaShop\PrestaShop\Core\Domain\Product\Supplier\Exception\ProductSupplierNotFoundException;
use PrestaShop\PrestaShop\Core\Domain\Product\ValueObject\ProductId;
use PrestaShop\PrestaShop\Core\Domain\Supplier\ValueObject\SupplierId;
use PrestaShop\PrestaShop\Core\Exception\CoreException;
use Product;
use ProductSupplier;

/**
 * Updates product supplier relation
 */
class ProductSupplierUpdater
{
    /**
     * @var ProductRepository
     */
    private $productRepository;

    /**
     * @var SupplierRepository
     */
    private $supplierRepository;

    /**
     * @var ProductSupplierRepository
     */
    private $productSupplierRepository;

    /**
     * @param ProductRepository $productRepository
     * @param SupplierRepository $supplierRepository
     * @param ProductSupplierRepository $productSupplierRepository
     */
    public function __construct(
        ProductRepository $productRepository,
        SupplierRepository $supplierRepository,
        ProductSupplierRepository $productSupplierRepository
    ) {
        $this->productRepository = $productRepository;
        $this->supplierRepository = $supplierRepository;
        $this->productSupplierRepository = $productSupplierRepository;
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
                $this->productSupplierRepository->update($productSupplier);
            } else {
                $this->productSupplierRepository->add($productSupplier);
            }
        }

        $this->productSupplierRepository->bulkDelete($deletableProductSupplierIds);
        $this->updateDefaultSupplier($productId, $defaultSupplierId);
    }

    /**
     * @param Product $product
     */
    public function resetDefaultSupplier(Product $product): void
    {
        $product->supplier_reference = '';
        $product->wholesale_price = '0';
        $product->id_supplier = 0;

        $this->productRepository->partialUpdate(
            $product,
            ['supplier_reference', 'wholesale_price', 'id_supplier'],
            CannotUpdateProductException::FAILED_UPDATE_DEFAULT_SUPPLIER
        );
    }

    /**
     * @param ProductId $productId
     * @param SupplierId $supplierId
     */
    public function updateDefaultSupplier(ProductId $productId, SupplierId $supplierId): void
    {
        $product = $this->productRepository->get($productId);
        $supplierIdValue = $supplierId->getValue();
        $productIdValue = (int) $product->id;

        if ($product->hasCombinations()) {
            $this->resetDefaultSupplier($product);

            return;
        }

        if ((int) $product->id_supplier === $supplierIdValue) {
            return;
        }

        $this->supplierRepository->assertSupplierExists($supplierId);
        $productSupplierId = (int) ProductSupplier::getIdByProductAndSupplier($productIdValue, 0, $supplierIdValue);

        if (!$productSupplierId) {
            throw new ProductSupplierNotFoundException(sprintf(
                'Supplier #%d is not associated with product #%d', $supplierIdValue, $productIdValue
            ));
        }

        $product->supplier_reference = ProductSupplier::getProductSupplierReference($productIdValue, 0, $supplierIdValue);
        $product->wholesale_price = (string) ProductSupplier::getProductSupplierPrice($productIdValue, 0, $supplierIdValue);
        $product->id_supplier = $supplierIdValue;

        $this->productRepository->partialUpdate(
            $product,
            ['supplier_reference', 'wholesale_price', 'id_supplier'],
            CannotUpdateProductException::FAILED_UPDATE_DEFAULT_SUPPLIER
        );
    }

    /**
     * @param int $productId
     * @param ProductSupplier[] $providedProductSuppliers
     *
     * @return array<int, int>
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
