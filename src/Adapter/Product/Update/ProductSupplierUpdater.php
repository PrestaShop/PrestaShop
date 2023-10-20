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
 * needs please refer to https://devdocs.prestashop-project.org/ for more information.
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
use PrestaShop\PrestaShop\Core\Domain\Product\Combination\Exception\CannotUpdateCombinationException;
use PrestaShop\PrestaShop\Core\Domain\Product\Combination\ValueObject\CombinationId;
use PrestaShop\PrestaShop\Core\Domain\Product\Combination\ValueObject\CombinationIdInterface;
use PrestaShop\PrestaShop\Core\Domain\Product\Combination\ValueObject\NoCombinationId;
use PrestaShop\PrestaShop\Core\Domain\Product\Exception\CannotUpdateProductException;
use PrestaShop\PrestaShop\Core\Domain\Product\Exception\InvalidProductTypeException;
use PrestaShop\PrestaShop\Core\Domain\Product\Supplier\Exception\ProductSupplierNotFoundException;
use PrestaShop\PrestaShop\Core\Domain\Product\Supplier\ValueObject\ProductSupplierAssociation;
use PrestaShop\PrestaShop\Core\Domain\Product\Supplier\ValueObject\ProductSupplierId;
use PrestaShop\PrestaShop\Core\Domain\Product\ValueObject\ProductId;
use PrestaShop\PrestaShop\Core\Domain\Product\ValueObject\ProductType;
use PrestaShop\PrestaShop\Core\Domain\Shop\ValueObject\ShopConstraint;
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
        $this->supplierRepository = $supplierRepository;
        $this->productSupplierRepository = $productSupplierRepository;
        $this->combinationRepository = $combinationRepository;
        $this->defaultCurrencyId = $defaultCurrencyId;
        $this->productRepository = $productRepository;
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
        if (empty($supplierIds)) {
            throw new InvalidArgumentException('Provided empty list of suppliers to associate');
        }

        // First check that all suppliers exist
        foreach ($supplierIds as $supplierId) {
            $this->supplierRepository->assertSupplierExists($supplierId);
        }

        // We get useless IDs and perform a bulk delete, we don't clean via a direct query even if it would be faster
        //because we need hook executed on each deleted ProductSupplier instance
        $uselessProductSupplierIds = $this->productSupplierRepository->getUselessProductSupplierIds($productId, $supplierIds);
        $this->productSupplierRepository->bulkDelete($uselessProductSupplierIds);

        // Get list of combinations for product, or NoCombination for products without association
        $productType = $this->productRepository->getProductType($productId);

        // We should always create an association not related to a combination
        $combinationIds = [new NoCombinationId()];
        if ($productType->getValue() === ProductType::TYPE_COMBINATIONS) {
            $combinationIds = array_merge($combinationIds, $this->combinationRepository->getCombinationIds(
                $productId,
                ShopConstraint::allShops()
            ));
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
        $defaultSupplierId = $this->productSupplierRepository->getDefaultSupplierId($productId);
        if (null === $defaultSupplierId) {
            // We use the first created association by default
            $firstAssociation = $allAssociations[0];

            // Update the default supplier for products, and potentially all its combinations
            $this->updateProductDefaultSupplier($productId, $firstAssociation->getSupplierId());
        }

        return $allAssociations;
    }

    /**
     * When new combinations are created some product supplier are absent, so we get associated suppliers and associate
     * them again, it will only created the missing ones without modifying the existing ones.
     *
     * @param ProductId $productId
     *
     * @return ProductSupplierAssociation[]
     */
    public function updateMissingProductSuppliers(ProductId $productId): array
    {
        $supplierIds = $this->productSupplierRepository->getAssociatedSupplierIds($productId);
        if (empty($supplierIds)) {
            return [];
        }

        return $this->associateSuppliers($productId, $supplierIds);
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
        $product = $this->productRepository->getProductByDefaultShop($productId);

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
    public function updateSuppliersForCombination(
        ProductId $productId,
        CombinationId $combinationId,
        array $productSuppliers
    ): array {
        return $this->updateProductSuppliers($productId, $productSuppliers, $combinationId);
    }

    /**
     * Removes all product suppliers associated to specified product without combinations
     *
     * @param ProductId $productId
     */
    public function removeAllForProduct(ProductId $productId): void
    {
        $product = $this->productRepository->getProductByDefaultShop($productId);

        $productSupplierIds = $this->getProductSupplierIds($productId);
        $this->productSupplierRepository->bulkDelete($productSupplierIds);
        $this->resetDefaultSupplier($product);
    }

    /**
     * When the default supplier is changed we must update all the synced field for the product and all its combinations.
     *
     * @param ProductId $productId
     * @param SupplierId $defaultSupplierId
     */
    public function updateProductDefaultSupplier(ProductId $productId, SupplierId $defaultSupplierId): void
    {
        $productType = $this->productRepository->getProductType($productId);
        if ($productType->getValue() === ProductType::TYPE_COMBINATIONS) {
            // Product must always be updated even for product with combinations, we use the default combination as the reference
            $defaultProductSupplier = $this->getDefaultCombinationProductSupplier($productId, $defaultSupplierId);
            if (!$defaultProductSupplier) {
                // When no combinations exist yet we use the default ProductSupplier as a fallback
                $defaultProductSupplier = $this->getDefaultProductSupplier($productId, $defaultSupplierId);
            }
            $this->updateDefaultSupplierDataForProduct($defaultProductSupplier);

            // Then each combination must be updated based on its data for default supplier (which may be different for each one)
            $associations = $this->productSupplierRepository->getAssociationsForSupplier($productId, $defaultSupplierId);
            foreach ($associations as $association) {
                if ($association->getCombinationId() instanceof CombinationId) {
                    $defaultCombinationSupplier = $this->productSupplierRepository->getByAssociation($association);
                    $this->updateDefaultSupplierDataForCombination($defaultCombinationSupplier);
                }
            }
        } else {
            // For products without combinations only one association is possible
            $defaultProductSupplier = $this->getDefaultProductSupplier($productId, $defaultSupplierId);
            $this->updateDefaultSupplierDataForProduct($defaultProductSupplier);
        }
    }

    /**
     * @param ProductId[] $productIds
     */
    public function resetSupplierAssociations(array $productIds): void
    {
        foreach ($productIds as $productId) {
            $suppliers = $this->productSupplierRepository->getAssociatedSupplierIds($productId);
            if (!empty($suppliers)) {
                $this->associateSuppliers($productId, $suppliers);
            } else {
                $this->resetDefaultSupplier($this->productRepository->getProductByDefaultShop($productId));
            }
        }
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
                $this->updateDefaultSupplierDataForCombination($updatedDefaultSupplier);
                $this->updateDefaultSupplierDataForProduct($updatedDefaultSupplier);
            } elseif ($combinationId instanceof NoCombinationId) {
                $this->updateDefaultSupplierDataForProduct($updatedDefaultSupplier);
            }
        }

        return $updatedAssociations;
    }

    /**
     * Update the default data for combination since it has two fields that must be synced with the supplier
     *
     * @param ProductSupplier $defaultCombinationSupplier
     */
    private function updateDefaultSupplierDataForCombination(ProductSupplier $defaultCombinationSupplier): void
    {
        $shopConstraint = ShopConstraint::allShops();
        $combination = $this->combinationRepository->getByShopConstraint(
            new CombinationId((int) $defaultCombinationSupplier->id_product_attribute),
            $shopConstraint
        );
        $combination->supplier_reference = $defaultCombinationSupplier->product_supplier_reference;

        $this->combinationRepository->partialUpdate(
            $combination,
            ['supplier_reference'],
            $shopConstraint,
            CannotUpdateCombinationException::FAILED_UPDATE_DEFAULT_SUPPLIER_DATA
        );
    }

    /**
     * Update the default data for product since it has two fields that must be synced with the supplier, and most importantly
     * it is the one saving the default supplier association
     *
     * @param ProductSupplier $defaultProductSupplier
     */
    private function updateDefaultSupplierDataForProduct(ProductSupplier $defaultProductSupplier): void
    {
        $product = $this->productRepository->getProductByDefaultShop(new ProductId((int) $defaultProductSupplier->id_product));
        $product->supplier_reference = $defaultProductSupplier->product_supplier_reference;
        $product->id_supplier = (int) $defaultProductSupplier->id_supplier;

        $this->productRepository->partialUpdate(
            $product,
            ['supplier_reference', 'id_supplier'],
            ShopConstraint::allShops(),
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
        $defaultCombinationId = $this->combinationRepository->findDefaultCombinationIdForShop(
            $productId,
            $this->productRepository->getProductDefaultShopId($productId)
        );
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
     * @param Product $product
     */
    private function resetDefaultSupplier(Product $product): void
    {
        $product->supplier_reference = '';
        $product->id_supplier = 0;

        $this->productRepository->partialUpdate(
            $product,
            ['supplier_reference', 'id_supplier'],
            ShopConstraint::allShops(),
            CannotUpdateProductException::FAILED_UPDATE_DEFAULT_SUPPLIER
        );
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
