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

use PrestaShop\PrestaShop\Adapter\Product\Combination\Repository\CombinationMultiShopRepository;
use PrestaShop\PrestaShop\Adapter\Product\Repository\ProductMultiShopRepository;
use PrestaShop\PrestaShop\Adapter\Product\Repository\ProductSupplierRepository;
use PrestaShop\PrestaShop\Adapter\Supplier\Repository\SupplierRepository;
use PrestaShop\PrestaShop\Core\Domain\Product\Combination\Exception\CannotUpdateCombinationException;
use PrestaShop\PrestaShop\Core\Domain\Product\Combination\ValueObject\CombinationId;
use PrestaShop\PrestaShop\Core\Domain\Product\Combination\ValueObject\CombinationIdInterface;
use PrestaShop\PrestaShop\Core\Domain\Product\Combination\ValueObject\NoCombinationId;
use PrestaShop\PrestaShop\Core\Domain\Product\Exception\CannotUpdateProductException;
use PrestaShop\PrestaShop\Core\Domain\Product\Exception\InvalidProductTypeException;
use PrestaShop\PrestaShop\Core\Domain\Product\Supplier\Exception\ProductSupplierNotFoundException;
use PrestaShop\PrestaShop\Core\Domain\Product\Supplier\ValueObject\ProductSupplierAssociation;
use PrestaShop\PrestaShop\Core\Domain\Product\ValueObject\ProductId;
use PrestaShop\PrestaShop\Core\Domain\Product\ValueObject\ProductType;
use PrestaShop\PrestaShop\Core\Domain\Shop\Exception\InvalidShopConstraintException;
use PrestaShop\PrestaShop\Core\Domain\Shop\ValueObject\ShopConstraint;
use PrestaShop\PrestaShop\Core\Domain\Shop\ValueObject\ShopId;
use PrestaShop\PrestaShop\Core\Domain\Supplier\ValueObject\SupplierId;
use PrestaShop\PrestaShop\Core\Exception\InvalidArgumentException;
use Product;
use ProductSupplier;

/**
 * Updates product supplier relation
 */
class ProductSupplierUpdater
{
    /**
     * @var ProductMultiShopRepository
     */
    private $productRepository;

    /**
     * @var CombinationMultiShopRepository
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

    public function __construct(
        ProductMultiShopRepository $productRepository,
        CombinationMultiShopRepository $combinationRepository,
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
     * @param ShopId $shopId
     *
     * @return ProductSupplierAssociation[]
     */
    public function associateSuppliers(ProductId $productId, array $supplierIds, ShopId $shopId): array
    {
        if (empty($supplierIds)) {
            throw new InvalidArgumentException('Provided empty list of suppliers to associate');
        }

        // First check that all suppliers exist
        foreach ($supplierIds as $supplierId) {
            $this->supplierRepository->assertSupplierExists($supplierId);
            $this->supplierRepository->assertShopAssociation($supplierId, $shopId);
        }

        // We get useless IDs and perform a bulk delete, we don't clean via a direct query even if it would be faster
        // because we need hook executed on each deleted ProductSupplier instance
        $uselessProductSupplierIds = $this->productSupplierRepository->getUselessProductSupplierIds($productId, $supplierIds, $shopId);
        $this->productSupplierRepository->bulkDelete($uselessProductSupplierIds);

        // Get list of combinations for product, or NoCombination for products without association
        $productType = $this->productRepository->getProductType($productId);

        // We should always create an association not related to a combination
        $combinationIds = [new NoCombinationId()];
        if ($productType->getValue() === ProductType::TYPE_COMBINATIONS) {
            $combinationIds = array_merge($combinationIds, $this->combinationRepository->getCombinationIds($productId, ShopConstraint::shop($shopId->getValue())));
        }

        // Now we search for each associated supplier if some associations are missing
        $allAssociations = [];
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

                    $allAssociations[] = new ProductSupplierAssociation(
                        $productId->getValue(),
                        $combinationId->getValue(),
                        $supplierId->getValue(),
                        (int) $productSupplier->id
                    );
                } else {
                    // We must use reset as the returned array is a filtered one so the first index is not necessarily 0
                    $allAssociations[] = reset($matchingAssociations);
                }
            }
        }

        // Check if product has a default supplier if not we must define it
        $defaultSupplierId = $this->productSupplierRepository->getDefaultSupplierId($productId, $shopId);
        if (null === $defaultSupplierId) {
            // We use the first created association by default
            $firstAssociation = $allAssociations[0];

            // Update the default supplier for products, and potentially all its combinations
            $this->updateProductDefaultSupplier($productId, $firstAssociation->getSupplierId(), $shopId);
        }

        return $allAssociations;
    }

    /**
     * When new combinations are created some product supplier are absent, so we get associated suppliers and associate
     * them again, it will only create the missing ones without modifying the existing ones.
     *
     * @param ProductId $productId
     * @param ShopConstraint $shopConstraint
     *
     * @return ProductSupplierAssociation[]
     *
     * @throw InvalidShopConstraintException
     */
    public function updateMissingProductSuppliers(ProductId $productId, ShopConstraint $shopConstraint): array
    {
        if ($shopConstraint->getShopGroupId()) {
            throw new InvalidShopConstraintException(sprintf('%s::%s does not handle constraint for shop group.', self::class, __FUNCTION__));
        } elseif ($shopConstraint->forAllShops()) {
            $shopIds = $this->productRepository->getAssociatedShopIds($productId);
        } else {
            $shopIds = [$shopConstraint->getShopId()];
        }

        $productAssociations = [];
        foreach ($shopIds as $shopId) {
            $supplierIds = $this->productSupplierRepository->getAssociatedSupplierIds($productId, $shopId);
            if (empty($supplierIds)) {
                continue;
            }

            $shopAssociations = $this->associateSuppliers($productId, $supplierIds, $shopConstraint->getShopId());

            // Add only associations not already present in the array
            $productAssociations = array_merge($productAssociations, array_filter($shopAssociations, function (ProductSupplierAssociation $shopAssociation) use ($productAssociations) {
                return !in_array($shopAssociation, $productAssociations);
            }));
        }

        return $productAssociations;
    }

    /**
     * @param ProductId $productId
     * @param array<int, ProductSupplier> $productSuppliers
     * @param ShopId $shopId
     *
     * @return array<int, ProductSupplierAssociation>
     */
    public function updateSuppliersForProduct(
        ProductId $productId,
        array $productSuppliers,
        ShopId $shopId
    ): array {
        $product = $this->productRepository->get($productId, $shopId);

        if ($product->getProductType() === ProductType::TYPE_COMBINATIONS) {
            $this->throwInvalidTypeException($productId, 'setCombinationSuppliers');
        }

        return $this->updateProductSuppliers($productId, $productSuppliers, new NoCombinationId(), $shopId);
    }

    /**
     * @param ProductId $productId
     * @param CombinationId $combinationId
     * @param array<int, ProductSupplier> $productSuppliers
     *
     * @return array<int, ProductSupplierAssociation>
     */
    public function updateSuppliersForCombination(
        ProductId $productId,
        CombinationId $combinationId,
        array $productSuppliers,
        ShopId $shopId
    ): array {
        return $this->updateProductSuppliers($productId, $productSuppliers, $combinationId, $shopId);
    }

    /**
     * Removes all product suppliers associated to specified product without combinations
     *
     * @param ProductId $productId
     */
    public function removeAllForProduct(ProductId $productId): void
    {
        $productSupplierIds = $this->productSupplierRepository->getProductSuppliersIds($productId);
        $this->productSupplierRepository->bulkDelete($productSupplierIds);
        foreach ($this->productRepository->getAssociatedShopIds($productId) as $shopId) {
            $this->resetDefaultSupplier($productId, $shopId);
        }
    }

    /**
     * When the default supplier is changed we must update all the synced field for the product and all its combinations.
     *
     * @param ProductId $productId
     * @param SupplierId $defaultSupplierId
     * @param ShopId $shopId
     */
    public function updateProductDefaultSupplier(ProductId $productId, SupplierId $defaultSupplierId, ShopId $shopId): void
    {
        $this->supplierRepository->assertSupplierExists($defaultSupplierId);
        $this->supplierRepository->assertShopAssociation($defaultSupplierId, $shopId);

        $productType = $this->productRepository->getProductType($productId);
        if ($productType->getValue() === ProductType::TYPE_COMBINATIONS) {
            // Product must always be updated even for product with combinations, we use the default combination as the reference
            $defaultProductSupplier = $this->getDefaultCombinationProductSupplier($productId, $defaultSupplierId);
            if (!$defaultProductSupplier) {
                // When no combinations exist yet we use the default ProductSupplier as a fallback
                $defaultProductSupplier = $this->getDefaultProductSupplier($productId, $defaultSupplierId);
            }
            $this->updateDefaultSupplierDataForProduct($defaultProductSupplier, false, $shopId);

            // Then each combination must be updated based on its data for default supplier (which may be different for each one)
            $associations = $this->productSupplierRepository->getAssociationsForSupplier($productId, $defaultSupplierId);
            foreach ($associations as $association) {
                if ($association->getCombinationId() instanceof CombinationId) {
                    $defaultCombinationSupplier = $this->productSupplierRepository->getByAssociation($association);
                    $this->updateDefaultSupplierDataForCombination($defaultCombinationSupplier, $shopId);
                }
            }
        } else {
            // For products without combinations only one association is possible
            $defaultProductSupplier = $this->getDefaultProductSupplier($productId, $defaultSupplierId);
            $this->updateDefaultSupplierDataForProduct($defaultProductSupplier, true, $shopId);
        }
    }

    /**
     * @param ProductId[] $productIds
     */
    public function resetSupplierAssociations(array $productIds, ShopId $shopId): void
    {
        foreach ($productIds as $productId) {
            $suppliers = $this->productSupplierRepository->getAssociatedSupplierIds($productId, $shopId);
            if (!empty($suppliers)) {
                $this->associateSuppliers($productId, $suppliers, $shopId);
            } else {
                $this->resetDefaultSupplier($productId, $shopId);
            }
        }
    }

    /**
     * @param ProductId $productId
     * @param array<int, ProductSupplier> $productSuppliers
     * @param CombinationIdInterface $combinationId
     * @param ShopId $shopId
     *
     * @return ProductSupplierAssociation[]
     */
    private function updateProductSuppliers(ProductId $productId, array $productSuppliers, CombinationIdInterface $combinationId, ShopId $shopId): array
    {
        $updatedAssociations = [];
        $defaultSupplierId = $this->productSupplierRepository->getDefaultSupplierId($productId, $shopId);

        /** @var ProductSupplier|null $updatedDefaultSupplier */
        $updatedDefaultSupplier = null;
        foreach ($productSuppliers as $productSupplier) {
            if (!$productSupplier->id) {
                throw new ProductSupplierNotFoundException(sprintf(
                    'Trying to update a nonexistent ProductSupplier for product #%d', $productId->getValue()
                ));
            }

            $this->productSupplierRepository->update($productSupplier);
            if ($defaultSupplierId->getValue() === (int) $productSupplier->id_supplier) {
                $updatedDefaultSupplier = $productSupplier;
            }

            $updatedAssociations[] = new ProductSupplierAssociation(
                $productId->getValue(),
                $combinationId->getValue(),
                (int) $productSupplier->id_supplier,
                (int) $productSupplier->id
            );
        }

        // If product supplier associated to default supplier was updated we need to update the product's default supplier related data
        if (null !== $updatedDefaultSupplier) {
            // CombinationInterface is either a CombinationId (identified combination) or a NoCombinationId (no combination for standard product)
            // So if $combinationId is a CombinationId we are updating a combination which also needs its default data to be updated
            if ($combinationId instanceof CombinationId) {
                $this->updateDefaultSupplierDataForCombination($updatedDefaultSupplier, $shopId);
                // Product default data is updated but not its wholesale price
                $this->updateDefaultSupplierDataForProduct($updatedDefaultSupplier, false, $shopId);
            } elseif ($combinationId instanceof NoCombinationId) {
                // Product default data is updated (including wholesale price) only when product itself is updated
                $this->updateDefaultSupplierDataForProduct($updatedDefaultSupplier, true, $shopId);
            }
        }

        return $updatedAssociations;
    }

    /**
     * Update the default data for combination since it has two fields that must be synced with the supplier
     *
     * @param ProductSupplier $defaultCombinationSupplier
     * @param ShopId $shopId
     */
    private function updateDefaultSupplierDataForCombination(ProductSupplier $defaultCombinationSupplier, ShopId $shopId): void
    {
        $combination = $this->combinationRepository->get(new CombinationId((int) $defaultCombinationSupplier->id_product_attribute), $shopId);
        $combination->supplier_reference = $defaultCombinationSupplier->product_supplier_reference;
        $combination->wholesale_price = (float) $defaultCombinationSupplier->product_supplier_price_te;

        $this->combinationRepository->partialUpdate(
            $combination,
            ['supplier_reference', 'wholesale_price', 'id_supplier'],
            ShopConstraint::shop($shopId->getValue()),
            CannotUpdateCombinationException::FAILED_UPDATE_DEFAULT_SUPPLIER_DATA
        );
    }

    /**
     * Update the default data for product since it has two fields that must be synced with the supplier, and most importantly
     * it is the one saving the default supplier association
     *
     * @param ProductSupplier $defaultProductSupplier
     * @param bool $updateWholeSalePrice
     * @param ShopId $shopId
     */
    private function updateDefaultSupplierDataForProduct(ProductSupplier $defaultProductSupplier, bool $updateWholeSalePrice, ShopId $shopId): void
    {
        $product = $this->productRepository->get(new ProductId((int) $defaultProductSupplier->id_product), $shopId);
        $product->supplier_reference = $defaultProductSupplier->product_supplier_reference;
        $product->id_supplier = (int) $defaultProductSupplier->id_supplier;
        if ($updateWholeSalePrice) {
            $product->wholesale_price = (float) (string) $defaultProductSupplier->product_supplier_price_te;
        }

        $this->productRepository->partialUpdate(
            $product,
            ['supplier_reference', 'wholesale_price', 'id_supplier'],
            ShopConstraint::shop($shopId->getValue()),
            CannotUpdateProductException::FAILED_UPDATE_DEFAULT_SUPPLIER
        );
    }

    /**
     * Find the ProductSupplier associated to the default combination of a product.
     *
     * @param ProductId $productId
     * @param SupplierId $supplierId
     *
     * @return ProductSupplier|null
     */
    private function getDefaultCombinationProductSupplier(ProductId $productId, SupplierId $supplierId): ?ProductSupplier
    {
        $defaultCombinationId = $this->combinationRepository->getDefaultCombinationId($productId);
        if (!$defaultCombinationId) {
            return null;
        }

        return $this->productSupplierRepository->getByAssociation(new ProductSupplierAssociation(
            $productId->getValue(),
            $defaultCombinationId->getValue(),
            $supplierId->getValue()
        ));
    }

    /**
     * Return the default ProductSupplier instance of a product, the one not associated to any combination.
     *
     * @param ProductId $productId
     * @param SupplierId $supplierId
     *
     * @return ProductSupplier
     *
     * @throws ProductSupplierNotFoundException
     */
    private function getDefaultProductSupplier(ProductId $productId, SupplierId $supplierId): ProductSupplier
    {
        return $this->productSupplierRepository->getByAssociation(new ProductSupplierAssociation(
            $productId->getValue(),
            NoCombinationId::NO_COMBINATION_ID,
            $supplierId->getValue()
        ));
    }

    /**
     * @param ProductId $productId
     * @param ShopId $shopId
     */
    private function resetDefaultSupplier(ProductId $productId, ShopId $shopId): void
    {
        $product = $this->productRepository->get($productId, $shopId);
        $product->supplier_reference = '';
        $product->id_supplier = 0;

        $this->productRepository->partialUpdate(
            $product,
            ['supplier_reference', 'id_supplier'],
            ShopConstraint::shop($shopId->getValue()),
            CannotUpdateProductException::FAILED_UPDATE_DEFAULT_SUPPLIER
        );
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
