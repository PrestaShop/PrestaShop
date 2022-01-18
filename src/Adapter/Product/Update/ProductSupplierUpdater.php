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

use PrestaShop\PrestaShop\Adapter\Product\Combination\Repository\CombinationRepository;
use PrestaShop\PrestaShop\Adapter\Product\Repository\ProductRepository;
use PrestaShop\PrestaShop\Adapter\Product\Repository\ProductSupplierRepository;
use PrestaShop\PrestaShop\Adapter\Supplier\Repository\SupplierRepository;
use PrestaShop\PrestaShop\Core\Domain\Product\Combination\ValueObject\CombinationId;
use PrestaShop\PrestaShop\Core\Domain\Product\Combination\ValueObject\CombinationIdInterface;
use PrestaShop\PrestaShop\Core\Domain\Product\Combination\ValueObject\NoCombinationId;
use PrestaShop\PrestaShop\Core\Domain\Product\Exception\CannotUpdateProductException;
use PrestaShop\PrestaShop\Core\Domain\Product\Exception\InvalidProductTypeException;
use PrestaShop\PrestaShop\Core\Domain\Product\Supplier\Exception\ProductSupplierNotAssociatedException;
use PrestaShop\PrestaShop\Core\Domain\Product\Supplier\Exception\ProductSupplierNotFoundException;
use PrestaShop\PrestaShop\Core\Domain\Product\Supplier\ValueObject\ProductSupplierAssociation;
use PrestaShop\PrestaShop\Core\Domain\Product\Supplier\ValueObject\ProductSupplierId;
use PrestaShop\PrestaShop\Core\Domain\Product\ValueObject\ProductId;
use PrestaShop\PrestaShop\Core\Domain\Product\ValueObject\ProductType;
use PrestaShop\PrestaShop\Core\Domain\Supplier\ValueObject\SupplierId;
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
     * @var CombinationRepository
     */
    private $combinationRepository;

    /**
     * @var SupplierRepository
     */
    private $supplierRepository;

    /**
     * @var ProductSupplierRepository
     */
    private $productSupplierRepository;

    /**
     * @var int
     */
    private $defaultCurrencyId;

    /**
     * @param ProductRepository $productRepository
     * @param CombinationRepository $combinationRepository
     * @param SupplierRepository $supplierRepository
     * @param ProductSupplierRepository $productSupplierRepository
     * @param int $defaultCurrencyId
     */
    public function __construct(
        ProductRepository $productRepository,
        CombinationRepository $combinationRepository,
        SupplierRepository $supplierRepository,
        ProductSupplierRepository $productSupplierRepository,
        int $defaultCurrencyId
    ) {
        $this->productRepository = $productRepository;
        $this->supplierRepository = $supplierRepository;
        $this->productSupplierRepository = $productSupplierRepository;
        $this->combinationRepository = $combinationRepository;
        $this->defaultCurrencyId = $defaultCurrencyId;
    }

    /**
     * Apply associations between a product and provided suppliers. If association that don't match the provided suppliers
     * exist they are removed. If some associations are missing they are created with empty values. Existing and valid
     * association are not modified. If the product has combination the association is created for all of them.
     *
     * @param ProductId $productId
     * @param SupplierId[] $supplierIds
     *
     * @return ProductSupplierAssociation[]
     */
    public function associateSuppliers(ProductId $productId, array $supplierIds): array
    {
        // We get useless IDs and perform a bulk delete, we don't clean via a direct query even if we could because we
        // need hook executed on each deleted ProductSupplier instance
        $uselessProductSupplierIds = $this->productSupplierRepository->getUselessProductSupplierIds($productId, $supplierIds);
        $this->productSupplierRepository->bulkDelete($uselessProductSupplierIds);

        // Get list of combinations for product, or NoCombination for products without association
        $combinationIds = $this->combinationRepository->getCombinationIdsByProductId($productId);
        if (empty($combinationIds)) {
            $combinationIds = [new NoCombinationId()];
        }

        // Now we search for each associated supplier if some associations are missing
        $productAssociations = [];
        foreach ($supplierIds as $supplierId) {
            $supplierAssociations = $this->productSupplierRepository->getAssociationsForSupplier($productId, $supplierId);

            // Loop through all combinations to check if they have a matching association if not it will need to be created
            foreach ($combinationIds as $combinationId) {
                // Search matching association by combination, if none is found the association is missing
                $matchingAssociations = array_filter($supplierAssociations, function (ProductSupplierAssociation $association) use ($combinationId) {
                    return $association->getCombinationId()->getValue() === $combinationId->getValue();
                });

                if (empty($matchingAssociations)) {
                    $productSupplier = new ProductSupplier();
                    $productSupplier->id_product = $productId->getValue();
                    $productSupplier->id_product_attribute = $combinationId->getValue();
                    $productSupplier->id_supplier = $supplierId->getValue();
                    $productSupplier->id_currency = $this->defaultCurrencyId;
                    $this->productSupplierRepository->add($productSupplier);

                    $productAssociations[] = new ProductSupplierAssociation(
                        $productId->getValue(),
                        $combinationId->getValue(),
                        $supplierId->getValue(),
                        (int) $productSupplier->id
                    );
                } else {
                    $productAssociations[] = reset($matchingAssociations);
                }
            }
        }

        // Check if product has a default supplier if not use the first one
        $defaultSupplierId = $this->productSupplierRepository->getDefaultSupplierId($productId);
        if (null === $defaultSupplierId) {
            $defaultSupplierId = reset($supplierIds);
            $this->updateDefaultSupplier($productId, $defaultSupplierId);
        }

        return $productAssociations;
    }

    /**
     * @param ProductId $productId
     * @param array<int, ProductSupplier> $productSuppliers
     *
     * @return array<int, ProductSupplierAssociation>
     */
    public function updateSuppliersForProduct(
        ProductId $productId,
        array $productSuppliers
    ): array {
        $product = $this->productRepository->get($productId);

        if ($product->getProductType() === ProductType::TYPE_COMBINATIONS) {
            $this->throwInvalidTypeException($productId, 'setCombinationSuppliers');
        }

        return $this->updateProductSuppliers($productId, $productSuppliers, new NoCombinationId());
    }

    /**
     * @param ProductId $productId
     * @param CombinationId $combinationId
     * @param array<int, ProductSupplier> $productSuppliers
     *
     * @return array<int, ProductSupplierAssociation>
     */
    public function updateCombinationSuppliers(
        ProductId $productId,
        CombinationId $combinationId,
        array $productSuppliers
    ): array {
        // Only pick association that match no combination to clean them (in case some are present because of inconsistent DB)
        $existingNonCombinationSuppliers = $this->getProductSupplierIds($productId, new NoCombinationId());
        $this->productSupplierRepository->bulkDelete($existingNonCombinationSuppliers);

        return $this->updateProductSuppliers($productId, $productSuppliers, $combinationId);
    }

    /**
     * Removes all product suppliers associated to specified product without combinations
     *
     * @param ProductId $productId
     */
    public function removeAllForProduct(ProductId $productId): void
    {
        $product = $this->productRepository->get($productId);

        if ($product->getProductType() === ProductType::TYPE_COMBINATIONS) {
            $this->throwInvalidTypeException($productId, 'removeAllForCombination');
        }

        $productSupplierIds = $this->getProductSupplierIds($productId);
        $this->productSupplierRepository->bulkDelete($productSupplierIds);
        $this->resetDefaultSupplier($product);
    }

    /**
     * Removes all product suppliers associated to specified combination
     *
     * @param CombinationId $combinationId
     */
    public function removeAllForCombination(CombinationId $combinationId): void
    {
        $combination = $this->combinationRepository->get($combinationId);
        $productId = new ProductId((int) $combination->id_product);
        $product = $this->productRepository->get($productId);

        $productSupplierIds = $this->getProductSupplierIds($productId, $combinationId);
        $this->productSupplierRepository->bulkDelete($productSupplierIds);
        $this->resetDefaultSupplier($product);
    }

    /**
     * @param Product $product
     */
    public function resetDefaultSupplier(Product $product): void
    {
        $product->supplier_reference = '';
        $product->id_supplier = 0;

        $this->productRepository->partialUpdate(
            $product,
            ['supplier_reference', 'id_supplier'],
            CannotUpdateProductException::FAILED_UPDATE_DEFAULT_SUPPLIER
        );
    }

    /**
     * @param ProductId $productId
     * @param SupplierId $supplierId
     */
    public function updateProductDefaultSupplier(ProductId $productId, SupplierId $supplierId): void
    {
        $this->updateDefaultSupplier($productId, $supplierId);
    }

    /**
     * @param ProductId $productId
     * @param SupplierId $supplierId
     * @param CombinationId $combinationId
     */
    public function updateCombinationDefaultSupplier(ProductId $productId, SupplierId $supplierId, CombinationId $combinationId): void
    {
        $this->updateDefaultSupplier($productId, $supplierId);
    }

    /**
     * @param ProductId $productId
     * @param SupplierId $supplierId
     */
    private function updateDefaultSupplier(ProductId $productId, SupplierId $supplierId): void
    {
        $product = $this->productRepository->get($productId);
        $supplierIdValue = $supplierId->getValue();

        $this->supplierRepository->assertSupplierExists($supplierId);
        $productSuppliers = $this->productSupplierRepository->getAssociationsForSupplier($productId, $supplierId);
        if (empty($productSuppliers)) {
            throw new ProductSupplierNotAssociatedException(sprintf(
                'Supplier #%d is not associated with product #%d', $supplierIdValue, $productId->getValue()
            ));
        }

        $firstAssociation = reset($productSuppliers);
        $defaultProductSupplier = $this->productSupplierRepository->get($firstAssociation->getProductSupplierId());
        $product->supplier_reference = $defaultProductSupplier->product_supplier_reference;
        $product->wholesale_price = (float) (string) $defaultProductSupplier->product_supplier_price_te;
        $product->id_supplier = $supplierIdValue;

        $this->productRepository->partialUpdate(
            $product,
            ['supplier_reference', 'wholesale_price', 'id_supplier'],
            CannotUpdateProductException::FAILED_UPDATE_DEFAULT_SUPPLIER
        );
    }

    /**
     * @param ProductId $productId
     * @param array<int, ProductSupplier> $productSuppliers
     * @param CombinationIdInterface $combinationId
     *
     * @return ProductSupplierAssociation[]
     */
    private function updateProductSuppliers(ProductId $productId, array $productSuppliers, CombinationIdInterface $combinationId): array
    {
        $updatedAssociations = [];
        $defaultSupplierId = $this->productSupplierRepository->getDefaultSupplierId($productId);
        $updateDefaultValues = false;
        foreach ($productSuppliers as $productSupplier) {
            if (!$productSupplier->id) {
                throw new ProductSupplierNotFoundException(sprintf(
                    'Trying to update a nonexistent ProductSupplier for product #%d', $productId->getValue()
                ));
            }

            $this->productSupplierRepository->update($productSupplier);
            if ($defaultSupplierId->getValue() === (int) $productSupplier->id_supplier) {
                $updateDefaultValues = true;
            }
            $updatedAssociations[] = new ProductSupplierAssociation(
                $productId->getValue(),
                $combinationId->getValue(),
                (int) $productSupplier->id_supplier,
                (int) $productSupplier->id
            );
        }

        // If product supplier associated to default supplier was updated we need to update the product's default supplier related data
        if ($updateDefaultValues) {
            $this->updateDefaultSupplier($productId, $defaultSupplierId);
        }

        return $updatedAssociations;
    }

    /**
     * @param ProductId $productId
     * @param CombinationIdInterface|null $combinationId
     *
     * @return array<int, ProductSupplierId>
     */
    private function getProductSupplierIds(ProductId $productId, ?CombinationIdInterface $combinationId = null): array
    {
        return array_map(function (array $currentSupplier): ProductSupplierId {
            return new ProductSupplierId((int) $currentSupplier['id_product_supplier']);
        }, $this->productSupplierRepository->getProductSuppliersInfo($productId, $combinationId));
    }

    /**
     * @param ProductId $productId
     *
     * @throws InvalidProductTypeException
     */
    private function throwInvalidTypeException(ProductId $productId, string $appropriateMethod): void
    {
        throw new InvalidProductTypeException(
            InvalidProductTypeException::EXPECTED_NO_COMBINATIONS_TYPE,
            sprintf(
                'Product #%d has combinations. Use %s::%s to set product suppliers for specified combination',
                $productId->getValue(),
                self::class,
                $appropriateMethod
            ));
    }
}
