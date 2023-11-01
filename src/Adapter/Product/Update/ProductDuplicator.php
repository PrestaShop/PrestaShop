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

use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Exception;
use Language;
use PrestaShop\PrestaShop\Adapter\Product\Combination\Repository\CombinationRepository;
use PrestaShop\PrestaShop\Adapter\Product\Combination\Update\CombinationStockProperties;
use PrestaShop\PrestaShop\Adapter\Product\Combination\Update\CombinationStockUpdater;
use PrestaShop\PrestaShop\Adapter\Product\Image\ProductImagePathFactory;
use PrestaShop\PrestaShop\Adapter\Product\Image\Repository\ProductImageRepository;
use PrestaShop\PrestaShop\Adapter\Product\Repository\ProductRepository;
use PrestaShop\PrestaShop\Adapter\Product\Repository\ProductSupplierRepository;
use PrestaShop\PrestaShop\Adapter\Product\SpecificPrice\Repository\SpecificPriceRepository;
use PrestaShop\PrestaShop\Adapter\Product\Stock\Repository\StockAvailableRepository;
use PrestaShop\PrestaShop\Adapter\Product\Stock\Update\ProductStockProperties;
use PrestaShop\PrestaShop\Adapter\Product\Stock\Update\ProductStockUpdater;
use PrestaShop\PrestaShop\Core\Domain\Product\Combination\ValueObject\CombinationId;
use PrestaShop\PrestaShop\Core\Domain\Product\Exception\CannotDuplicateProductException;
use PrestaShop\PrestaShop\Core\Domain\Product\Exception\CannotUpdateProductException;
use PrestaShop\PrestaShop\Core\Domain\Product\Image\ValueObject\ImageId;
use PrestaShop\PrestaShop\Core\Domain\Product\ProductSettings;
use PrestaShop\PrestaShop\Core\Domain\Product\Stock\Exception\StockAvailableNotFoundException;
use PrestaShop\PrestaShop\Core\Domain\Product\Stock\ValueObject\OutOfStockType;
use PrestaShop\PrestaShop\Core\Domain\Product\Stock\ValueObject\StockModification;
use PrestaShop\PrestaShop\Core\Domain\Product\Supplier\ValueObject\ProductSupplierId;
use PrestaShop\PrestaShop\Core\Domain\Product\ValueObject\ProductId;
use PrestaShop\PrestaShop\Core\Domain\Product\ValueObject\ProductType;
use PrestaShop\PrestaShop\Core\Domain\Shop\Exception\ShopAssociationNotFound;
use PrestaShop\PrestaShop\Core\Domain\Shop\ValueObject\ShopConstraint;
use PrestaShop\PrestaShop\Core\Domain\Shop\ValueObject\ShopId;
use PrestaShop\PrestaShop\Core\Exception\CoreException;
use PrestaShop\PrestaShop\Core\Exception\InvalidArgumentException;
use PrestaShop\PrestaShop\Core\Hook\HookDispatcherInterface;
use PrestaShop\PrestaShop\Core\Repository\AbstractMultiShopObjectModelRepository;
use PrestaShop\PrestaShop\Core\Util\DateTime\DateTime;
use PrestaShop\PrestaShop\Core\Util\String\StringModifierInterface;
use PrestaShopException;
use Product;
use ProductDownload as VirtualProductFile;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Contracts\Translation\TranslatorInterface;

/**
 * Duplicates product
 */
class ProductDuplicator extends AbstractMultiShopObjectModelRepository
{
    /**
     * @var ProductRepository
     */
    private $productRepository;

    /**
     * @var HookDispatcherInterface
     */
    private $hookDispatcher;

    /**
     * @var TranslatorInterface
     */
    private $translator;

    /**
     * @var StringModifierInterface
     */
    private $stringModifier;

    /**
     * @var Connection
     */
    private $connection;

    /**
     * @var string
     */
    private $dbPrefix;

    /**
     * @var CombinationRepository
     */
    private $combinationRepository;

    /**
     * @var ProductSupplierRepository
     */
    private $productSupplierRepository;

    /**
     * @var SpecificPriceRepository
     */
    private $specificPriceRepository;

    /**
     * @var StockAvailableRepository
     */
    private $stockAvailableRepository;

    /**
     * @var ProductStockUpdater
     */
    private $productStockUpdater;

    /**
     * @var CombinationStockUpdater
     */
    private $combinationStockUpdater;

    /**
     * @var ProductImageRepository
     */
    private $productImageRepository;

    /**
     * @var ProductImagePathFactory
     */
    private $productImageSystemPathFactory;

    public function __construct(
        ProductRepository $productRepository,
        HookDispatcherInterface $hookDispatcher,
        TranslatorInterface $translator,
        StringModifierInterface $stringModifier,
        Connection $connection,
        string $dbPrefix,
        CombinationRepository $combinationRepository,
        ProductSupplierRepository $productSupplierRepository,
        SpecificPriceRepository $specificPriceRepository,
        StockAvailableRepository $stockAvailableRepository,
        ProductStockUpdater $productStockUpdater,
        CombinationStockUpdater $combinationStockUpdater,
        ProductImageRepository $productImageRepository,
        ProductImagePathFactory $productImageSystemPathFactory
    ) {
        $this->productRepository = $productRepository;
        $this->hookDispatcher = $hookDispatcher;
        $this->translator = $translator;
        $this->stringModifier = $stringModifier;
        $this->connection = $connection;
        $this->dbPrefix = $dbPrefix;
        $this->combinationRepository = $combinationRepository;
        $this->productSupplierRepository = $productSupplierRepository;
        $this->specificPriceRepository = $specificPriceRepository;
        $this->stockAvailableRepository = $stockAvailableRepository;
        $this->productStockUpdater = $productStockUpdater;
        $this->combinationStockUpdater = $combinationStockUpdater;
        $this->productImageRepository = $productImageRepository;
        $this->productImageSystemPathFactory = $productImageSystemPathFactory;
    }

    /**
     * @param ProductId $productId
     * @param ShopConstraint $shopConstraint
     *
     * @return ProductId new product id
     *
     * @throws CannotDuplicateProductException
     * @throws CannotUpdateProductException
     * @throws CoreException
     */
    public function duplicate(ProductId $productId, ShopConstraint $shopConstraint): ProductId
    {
        //@todo: add database transaction. After/if PR #21740 gets merged
        $oldProductId = $productId->getValue();
        $this->hookDispatcher->dispatchWithParameters(
            'actionAdminDuplicateBefore',
            ['id_product' => $oldProductId]
        );
        $newProduct = $this->duplicateProduct($productId, $shopConstraint);
        $newProductId = (int) $newProduct->id;

        $this->duplicateRelations($oldProductId, $newProductId, $shopConstraint, $newProduct->getProductType());

        if ($newProduct->hasAttributes()) {
            $this->updateDefaultAttribute($newProductId, $oldProductId);
        }

        $this->hookDispatcher->dispatchWithParameters(
            'actionProductAdd',
            ['id_product_old' => $oldProductId, 'id_product' => $newProductId, 'product' => $newProduct]
        );

        $this->hookDispatcher->dispatchWithParameters(
            'actionAdminDuplicateAfter',
            ['id_product' => $oldProductId, 'id_product_new' => $newProductId]
        );
        //@todo: after ##21740 (transactions PR) is resolved.
        //  Based on if its accepted or not, we need to implement roll back if something went wrong.
        //  If transactions are accepted then we use it, else we manually rewind (delete the duplicate product)
        return new ProductId((int) $newProduct->id);
    }

    /**
     * @param ProductId $sourceProductId
     * @param ShopConstraint $shopConstraint
     *
     * @return Product
     */
    private function duplicateProduct(ProductId $sourceProductId, ShopConstraint $shopConstraint): Product
    {
        $sourceDefaultShopId = $this->productRepository->getProductDefaultShopId($sourceProductId);
        $shopIds = $this->productRepository->getShopIdsByConstraint($sourceProductId, $shopConstraint);

        if (empty($shopIds)) {
            throw new ShopAssociationNotFound(
                sprintf(
                    'No shops associated with product %d by shop constraint %s',
                    $sourceProductId->getValue(),
                    var_export($shopConstraint, true)
                )
            );
        }

        if ($shopConstraint->getShopId()) {
            $targetDefaultShopId = $shopConstraint->getShopId();
        } elseif ($shopConstraint->getShopGroupId()) {
            // If source default shop is in the group use it as new default, if not use the first shop from group
            $targetDefaultShopId = null;
            foreach ($shopIds as $groupShopId) {
                if ($groupShopId->getValue() === $sourceDefaultShopId->getValue()) {
                    $targetDefaultShopId = $sourceDefaultShopId;
                }
            }
            if ($targetDefaultShopId === null) {
                $targetDefaultShopId = reset($shopIds);
            }
        } else {
            $targetDefaultShopId = $sourceDefaultShopId;
        }

        // First add the product to its default shop
        $sourceProduct = $this->productRepository->get($sourceProductId, $targetDefaultShopId);
        $duplicatedProduct = $this->duplicateObjectModelToShop($sourceProduct, $targetDefaultShopId);

        // Then associate it to other shops and copy its values
        $newProductId = new ProductId((int) $duplicatedProduct->id);
        foreach ($shopIds as $shopId) {
            $shopProduct = $this->productRepository->get($sourceProductId, $shopId);
            // The duplicated product is disabled and not indexed by default
            $shopProduct->indexed = false;
            $shopProduct->active = false;
            // Force a copy name to tell the two products apart (for each shop since name can be different on each shop)
            $shopProduct->name = $this->getNewProductName($shopProduct->name);
            // Force ID to update the new product
            $shopProduct->id = $shopProduct->id_product = $newProductId->getValue();
            // Force the desired default shop so that it doesn't switch back to the source one
            $shopProduct->id_shop_default = $targetDefaultShopId->getValue();
            $this->productRepository->update(
                $shopProduct,
                ShopConstraint::shop($shopId->getValue()),
                CannotUpdateProductException::FAILED_DUPLICATION
            );
        }

        return $duplicatedProduct;
    }

    /**
     * @template T
     * @psalm-param T $sourceObjectModel
     *
     * @return T
     */
    private function duplicateObjectModelToShop($sourceObjectModel, ShopId $targetDefaultShopId)
    {
        $duplicatedObject = clone $sourceObjectModel;
        unset($duplicatedObject->id);

        $objectDefinition = $sourceObjectModel::$definition;
        $idTable = 'id_' . $objectDefinition['table'];
        if (property_exists($duplicatedObject, $idTable)) {
            unset($duplicatedObject->$idTable);
        }

        $this->addObjectModelToShops($duplicatedObject, [$targetDefaultShopId], CannotDuplicateProductException::class);

        return $duplicatedObject;
    }

    /**
     * Provides duplicated product name
     *
     * @param array<int, string> $oldProductLocalizedNames
     *
     * @return array<int, string>
     */
    private function getNewProductName(array $oldProductLocalizedNames): array
    {
        $newProductLocalizedNames = [];
        foreach ($oldProductLocalizedNames as $langId => $oldName) {
            $langId = (int) $langId;
            $namePattern = $this->translator->trans('copy of %s', [], 'Admin.Catalog.Feature', Language::getLocaleById($langId));
            $newName = sprintf($namePattern, $oldName);
            $newProductLocalizedNames[$langId] = $this->stringModifier->cutEnd($newName, ProductSettings::MAX_NAME_LENGTH);
        }

        return $newProductLocalizedNames;
    }

    /**
     * Duplicates related product entities & associations
     *
     * @param int $oldProductId
     * @param int $newProductId
     * @param ShopConstraint $shopConstraint
     *
     * @throws CannotDuplicateProductException
     * @throws CoreException
     */
    private function duplicateRelations(int $oldProductId, int $newProductId, ShopConstraint $shopConstraint, string $productType): void
    {
        $shopIds = array_map(static function (ShopId $shopId) {
            return $shopId->getValue();
        }, $this->productRepository->getShopIdsByConstraint(new ProductId($oldProductId), $shopConstraint));

        $this->duplicateCategories($oldProductId, $newProductId);
        $combinationMatching = $this->duplicateCombinations($oldProductId, $newProductId, $shopIds);
        $this->duplicateSuppliers($oldProductId, $newProductId, $combinationMatching);
        $this->duplicateGroupReduction($oldProductId, $newProductId);
        $this->duplicateRelatedProducts($oldProductId, $newProductId);
        $this->duplicateFeatures($oldProductId, $newProductId);
        $this->duplicateSpecificPrices($oldProductId, $newProductId, $combinationMatching);
        $this->duplicatePackedProducts($oldProductId, $newProductId);
        $this->duplicateCustomizationFields($oldProductId, $newProductId);
        $this->duplicateTags($oldProductId, $newProductId);
        $this->duplicateVirtualProductFiles($oldProductId, $newProductId);
        $this->duplicateImages($oldProductId, $newProductId, $combinationMatching, $shopConstraint);
        $this->duplicateCarriers($oldProductId, $newProductId, $shopIds);
        $this->duplicateAttachmentAssociation($oldProductId, $newProductId);
        $this->duplicateStock($oldProductId, $newProductId, $shopIds, $productType, $combinationMatching);
    }

    /**
     * @param int $oldProductId
     * @param int $newProductId
     * @param int[] $shopIds
     * @param string $productType
     * @param array $combinationMatching
     */
    private function duplicateStock(int $oldProductId, int $newProductId, array $shopIds, string $productType, array $combinationMatching): void
    {
        $targetProductId = new ProductId($newProductId);
        foreach ($shopIds as $shopId) {
            $targetShopId = new ShopId($shopId);
            try {
                $this->stockAvailableRepository->getForProduct($targetProductId, $targetShopId);
            } catch (StockAvailableNotFoundException $e) {
                // We create the new StockAvailable for this product and shop, it will then be updated via stock modification
                $this->stockAvailableRepository->createStockAvailable($targetProductId, $targetShopId);
            }

            try {
                $sourceStock = $this->stockAvailableRepository->getForProduct(new ProductId($oldProductId), $targetShopId);
                $outOfStock = new OutOfStockType((int) $sourceStock->out_of_stock);
                $productQuantity = (int) $sourceStock->quantity;
                $location = $sourceStock->location;
            } catch (StockAvailableNotFoundException $e) {
                // The source product may not have any associated StockAvailable (this happens with product created with old versions)
                $outOfStock = new OutOfStockType(OutOfStockType::OUT_OF_STOCK_DEFAULT);
                $productQuantity = 0;
                $location = '';
            }

            $stockModification = StockModification::buildFixedQuantity($productQuantity);
            $stockProperties = new ProductStockProperties(
                $stockModification,
                $outOfStock,
                $location
            );
            $this->productStockUpdater->update($targetProductId, $stockProperties, ShopConstraint::shop($targetShopId->getValue()));

            if ($productType === ProductType::TYPE_COMBINATIONS) {
                $this->duplicateCombinationsStock($oldProductId, $newProductId, $targetShopId, $combinationMatching);
            }
        }
    }

    /**
     * @param int $oldProductId
     * @param int $newProductId
     * @param ShopId $targetShopId
     * @param array<int, int> $combinationMatching
     */
    private function duplicateCombinationsStock(int $oldProductId, int $newProductId, ShopId $targetShopId, array $combinationMatching): void
    {
        $targetProductId = new ProductId($newProductId);
        $sourceCombinations = $this->combinationRepository->getCombinationIds(
            new ProductId($oldProductId),
            ShopConstraint::shop($targetShopId->getValue())
        );
        $targetConstraint = ShopConstraint::shop($targetShopId->getValue());

        foreach ($sourceCombinations as $oldCombinationId) {
            $newCombinationId = new CombinationId($combinationMatching[$oldCombinationId->getValue()]);
            try {
                $this->stockAvailableRepository->getForCombination($newCombinationId, $targetShopId);
            } catch (StockAvailableNotFoundException $e) {
                $this->stockAvailableRepository->createStockAvailable($targetProductId, $targetShopId, $newCombinationId);
            }

            // Get the source stock
            try {
                $sourceStock = $this->stockAvailableRepository->getForCombination($oldCombinationId, $targetShopId);
                $combinationQuantity = (int) $sourceStock->quantity;
                $location = $sourceStock->location;
            } catch (StockAvailableNotFoundException $e) {
                // The source combination may not have any associated StockAvailable (this happens with combinations created with old versions)
                $combinationQuantity = 0;
                $location = '';
            }

            $stockModification = StockModification::buildFixedQuantity($combinationQuantity);
            $stockProperties = new CombinationStockProperties(
                $stockModification,
                $location
            );
            $this->combinationStockUpdater->update($newCombinationId, $stockProperties, $targetConstraint);
        }
    }

    /**
     * @param int $oldProductId
     * @param int $newProductId
     */
    private function duplicateCategories(int $oldProductId, int $newProductId): void
    {
        $oldRows = $this->getRows('category_product', ['id_product' => $oldProductId], CannotDuplicateProductException::FAILED_DUPLICATE_CATEGORIES);
        $newRows = [];
        $lastCategoriesPosition = [];
        foreach ($oldRows as $oldRow) {
            $categoryId = (int) $oldRow['id_category'];
            if (isset($lastCategoriesPosition[$categoryId])) {
                $lastCategoryPosition = $lastCategoriesPosition[$categoryId];
            } else {
                $lastCategoryPosition = (int) $this->connection->createQueryBuilder()
                    ->select('cp.position')
                    ->from($this->dbPrefix . 'category_product', 'cp')
                    ->where('cp.id_category = :categoryId')
                    ->setParameter('categoryId', $categoryId)
                    ->addOrderBy('position', 'DESC')
                    ->execute()
                    ->fetchOne()
                ;
            }

            $newRows[] = [
                'id_product' => $newProductId,
                'id_category' => $categoryId,
                'position' => ++$lastCategoryPosition,
            ];
            $lastCategoriesPosition[$categoryId] = $lastCategoryPosition;
        }
        $this->bulkInsert('category_product', $newRows, CannotDuplicateProductException::FAILED_DUPLICATE_CATEGORIES);
    }

    /**
     * @param int $oldProductId
     * @param int $newProductId
     *
     * @throws CannotDuplicateProductException
     * @throws CoreException
     */
    private function duplicateSuppliers(int $oldProductId, int $newProductId, array $combinationMatching): void
    {
        $oldSuppliers = $this->getRows('product_supplier', ['id_product' => $oldProductId], CannotDuplicateProductException::FAILED_DUPLICATE_SUPPLIERS);
        if (empty($oldSuppliers)) {
            return;
        }

        foreach ($oldSuppliers as $oldSupplier) {
            $newProductSupplier = $this->productSupplierRepository->get(new ProductSupplierId((int) $oldSupplier['id_product_supplier']));
            $newProductSupplier->id_product = $newProductId;
            $newProductSupplier->id_product_attribute = $combinationMatching[(int) $oldSupplier['id_product_attribute']] ?? 0;
            $this->productSupplierRepository->add($newProductSupplier);
        }
    }

    /**
     * @param int $oldProductId
     * @param int $newProductId
     * @param int[] $shopIds
     *
     * @return array<int, int> Combination matching (key is the old ID, value is the new one)
     *
     * @throws CannotDuplicateProductException
     * @throws CoreException
     */
    private function duplicateCombinations(int $oldProductId, int $newProductId, array $shopIds): array
    {
        $oldCombinationsShop = $this->getRows(
            'product_attribute_shop',
            [
                'id_product' => $oldProductId,
                'id_shop' => $shopIds,
            ],
            CannotDuplicateProductException::FAILED_DUPLICATE_COMBINATIONS,
            [
                'id_product_attribute' => 'ASC',
                'id_shop' => 'ASC',
            ]
        );

        // First create new combinations which are copies of the old ones
        $combinationMatching = [];
        $newShopAssociations = [];
        foreach ($oldCombinationsShop as $oldCombinationShop) {
            $oldCombinationId = (int) $oldCombinationShop['id_product_attribute'];

            if (!isset($combinationMatching[$oldCombinationId])) {
                // New combination to create, copy the old combination and associate to appropriate attributes, store the new ID for matching
                $oldCombinations = $this->getRows(
                    'product_attribute',
                    [
                        'id_product' => $oldProductId,
                        'id_product_attribute' => $oldCombinationId,
                    ],
                    CannotDuplicateProductException::FAILED_DUPLICATE_COMBINATIONS
                );
                $newCombination = array_merge(reset($oldCombinations), [
                    'id_product' => $newProductId,
                    'id_product_attribute' => null,
                ]);
                $newCombinationId = $this->insertRow('product_attribute', $newCombination, CannotDuplicateProductException::FAILED_DUPLICATE_COMBINATIONS);
                if (empty($newCombinationId)) {
                    throw new CannotDuplicateProductException('Could not duplicate combination', CannotDuplicateProductException::FAILED_DUPLICATE_COMBINATIONS);
                }
                $combinationMatching[$oldCombinationId] = $newCombinationId;

                // Associate attributes to combination
                $oldAttributes = $this->getRows(
                    'product_attribute_combination',
                    ['id_product_attribute' => $oldCombinationId],
                    CannotDuplicateProductException::FAILED_DUPLICATE_COMBINATIONS
                );
                $newAttributes = $this->replaceInRows($oldAttributes, ['id_product_attribute' => $newCombinationId]);
                $this->bulkInsert('product_attribute_combination', $newAttributes, CannotDuplicateProductException::FAILED_DUPLICATE_COMBINATIONS);
            }

            // Add new shop association
            $newCombinationId = $combinationMatching[$oldCombinationId];
            $newCombinationShop = array_merge($oldCombinationShop, [
                'id_product_attribute' => $newCombinationId,
                'id_product' => $newProductId,
            ]);
            $newShopAssociations[] = $newCombinationShop;
        }

        // Insert all shop associations
        $this->bulkInsert('product_attribute_shop', $newShopAssociations, CannotDuplicateProductException::FAILED_DUPLICATE_COMBINATIONS);

        // Finally copy all combination multi lang fields
        $oldCombinationsLang = $this->getRows(
            'product_attribute_lang',
            [
                'id_product_attribute' => array_keys($combinationMatching),
            ],
            CannotDuplicateProductException::FAILED_DUPLICATE_COMBINATIONS
        );
        $newCombinationsLang = [];
        foreach ($oldCombinationsLang as $oldLang) {
            $newCombinationsLang[] = array_merge($oldLang, [
                'id_product_attribute' => $combinationMatching[(int) $oldLang['id_product_attribute']],
            ]);
        }
        $this->bulkInsert('product_attribute_lang', $newCombinationsLang, CannotDuplicateProductException::FAILED_DUPLICATE_COMBINATIONS);

        return $combinationMatching;
    }

    /**
     * @param int $oldProductId
     * @param int $newProductId
     *
     * @throws CannotDuplicateProductException
     * @throws CoreException
     */
    private function duplicateGroupReduction(int $oldProductId, int $newProductId): void
    {
        $this->duplicateProductTable('product_group_reduction_cache', $oldProductId, $newProductId, CannotDuplicateProductException::FAILED_DUPLICATE_GROUP_REDUCTION);
    }

    /**
     * @param int $oldProductId
     * @param int $newProductId
     *
     * @throws CannotDuplicateProductException
     * @throws CoreException
     */
    private function duplicateRelatedProducts(int $oldProductId, int $newProductId): void
    {
        $oldRows = $this->getRows(
            'accessory',
            ['id_product_1' => $oldProductId],
            CannotDuplicateProductException::FAILED_DUPLICATE_RELATED_PRODUCTS
        );

        if (empty($oldRows)) {
            return;
        }
        $newRows = $this->replaceInRows($oldRows, ['id_product_1' => $newProductId]);
        $this->bulkInsert(
            'accessory',
            $newRows,
            CannotDuplicateProductException::FAILED_DUPLICATE_RELATED_PRODUCTS
        );
    }

    /**
     * @param int $oldProductId
     * @param int $newProductId
     *
     * @throws CannotDuplicateProductException
     * @throws CoreException
     */
    private function duplicateFeatures(int $oldProductId, int $newProductId): void
    {
        $oldProductFeatures = $this->getRows('feature_product', ['id_product' => $oldProductId], CannotDuplicateProductException::FAILED_DUPLICATE_FEATURES);

        // Custom values need to be copied and assigned to new products
        $featureValuesIds = array_map(static function (array $oldProductFeature) {
            return (int) $oldProductFeature['id_feature_value'];
        }, $oldProductFeatures);
        $customFeatureValues = $this->getRows('feature_value', ['id_feature_value' => $featureValuesIds, 'custom' => 1], CannotDuplicateProductException::FAILED_DUPLICATE_FEATURES);
        $customValuesMapping = [];
        if (!empty($customFeatureValues)) {
            $lastFeatureValueId = (int) $this->connection->createQueryBuilder()
                ->from($this->dbPrefix . 'feature_value')
                ->select('id_feature_value')
                ->addOrderBy('id_feature_value', 'DESC')
                ->execute()
                ->fetchOne()
            ;

            $newCustomFeatureValues = [];
            $newCustomFeatureValuesLang = [];
            foreach ($customFeatureValues as $customFeatureValue) {
                $newCustomFeatureValueId = ++$lastFeatureValueId;
                $oldCustomFeatureValueId = (int) $customFeatureValue['id_feature_value'];
                $customValuesMapping[$oldCustomFeatureValueId] = $newCustomFeatureValueId;
                $newCustomFeatureValues[] = array_merge($customFeatureValue, [
                    'id_feature_value' => $newCustomFeatureValueId,
                ]);

                $langData = $this->getRows('feature_value_lang', ['id_feature_value' => $oldCustomFeatureValueId], CannotDuplicateProductException::FAILED_DUPLICATE_FEATURES);
                $langData = $this->replaceInRows($langData, ['id_feature_value' => $newCustomFeatureValueId]);
                $newCustomFeatureValuesLang = array_merge($newCustomFeatureValuesLang, $langData);
            }
            $this->bulkInsert('feature_value', $newCustomFeatureValues, CannotDuplicateProductException::FAILED_DUPLICATE_FEATURES);
            $this->bulkInsert('feature_value_lang', $newCustomFeatureValuesLang, CannotDuplicateProductException::FAILED_DUPLICATE_FEATURES);
        }

        // Now we can duplicate relations (and replace custom ones with newly copied feature values)
        $newProductFeatures = [];
        foreach ($oldProductFeatures as $oldProductFeature) {
            $oldCustomFeatureValueId = (int) $oldProductFeature['id_feature_value'];
            if (!isset($customValuesMapping[$oldCustomFeatureValueId])) {
                $newProductFeatures[] = [
                    'id_product' => $newProductId,
                    'id_feature' => $oldProductFeature['id_feature'],
                    'id_feature_value' => $oldProductFeature['id_feature_value'],
                ];
            } else {
                $newProductFeatures[] = [
                    'id_product' => $newProductId,
                    'id_feature' => $oldProductFeature['id_feature'],
                    'id_feature_value' => $customValuesMapping[$oldCustomFeatureValueId],
                ];
            }
        }
        $this->bulkInsert('feature_product', $newProductFeatures, CannotDuplicateProductException::FAILED_DUPLICATE_FEATURES);
    }

    /**
     * @param int $oldProductId
     * @param int $newProductId
     * @param array $combinationMatching
     *
     * @throws CannotDuplicateProductException
     * @throws CoreException
     */
    private function duplicateSpecificPrices(int $oldProductId, int $newProductId, array $combinationMatching): void
    {
        $specificPriceIds = $this->specificPriceRepository->getProductSpecificPricesIds(new ProductId($oldProductId));
        foreach ($specificPriceIds as $specificPriceId) {
            $specificPrice = $this->specificPriceRepository->get($specificPriceId);
            $specificPrice->id_product = $newProductId;
            $specificPrice->id_product_attribute = $combinationMatching[(int) $specificPrice->id_product_attribute] ?? 0;
            $this->specificPriceRepository->add($specificPrice);
        }

        // Duplicate priorities
        $oldPriorities = $this->getRows('specific_price_priority', ['id_product' => $oldProductId], CannotDuplicateProductException::FAILED_DUPLICATE_SPECIFIC_PRICES);
        $newPriorities = $this->replaceInRows($oldPriorities, ['id_product' => $newProductId, 'id_specific_price_priority' => null]);
        $this->bulkInsert('specific_price_priority', $newPriorities, CannotDuplicateProductException::FAILED_DUPLICATE_SPECIFIC_PRICES);
    }

    /**
     * @param int $oldProductId
     * @param int $newProductId
     *
     * @throws CannotDuplicateProductException
     * @throws CoreException
     */
    private function duplicatePackedProducts(int $oldProductId, int $newProductId): void
    {
        $oldPackContent = $this->getRows('pack', ['id_product_pack' => $oldProductId], CannotDuplicateProductException::FAILED_DUPLICATE_PACKED_PRODUCTS);
        $newPackContent = $this->replaceInRows($oldPackContent, ['id_product_pack' => $newProductId]);
        $this->bulkInsert('pack', $newPackContent, CannotDuplicateProductException::FAILED_DUPLICATE_PACKED_PRODUCTS);
    }

    /**
     * @param int $oldProductId
     * @param int $newProductId
     *
     * @throws CannotDuplicateProductException
     */
    private function duplicateCustomizationFields(int $oldProductId, int $newProductId): void
    {
        $oldCustomizationFields = $this->getRows('customization_field', ['id_product' => $oldProductId], CannotDuplicateProductException::FAILED_DUPLICATE_CUSTOMIZATION_FIELDS);
        $lastCustomizationFieldId = (int) $this->connection->createQueryBuilder()
            ->from($this->dbPrefix . 'customization_field')
            ->select('id_customization_field')
            ->addOrderBy('id_customization_field', 'DESC')
            ->execute()
            ->fetchOne()
        ;

        $newCustomizationFields = [];
        $newCustomizationFieldsLang = [];
        foreach ($oldCustomizationFields as $oldCustomizationField) {
            $oldCustomizationFieldId = (int) $oldCustomizationField['id_customization_field'];
            $newCustomizationFieldId = ++$lastCustomizationFieldId;

            $newCustomizationFields[] = array_merge($oldCustomizationField, [
                'id_product' => $newProductId,
                'id_customization_field' => $newCustomizationFieldId,
            ]);

            $oldCustomizationFieldsLang = $this->getRows('customization_field_lang', ['id_customization_field' => $oldCustomizationFieldId], CannotDuplicateProductException::FAILED_DUPLICATE_CUSTOMIZATION_FIELDS);
            foreach ($oldCustomizationFieldsLang as $oldCustomizationFieldLang) {
                $newCustomizationFieldsLang[] = array_merge($oldCustomizationFieldLang, [
                    'id_customization_field' => $newCustomizationFieldId,
                ]);
            }
        }

        $this->bulkInsert('customization_field', $newCustomizationFields, CannotDuplicateProductException::FAILED_DUPLICATE_CUSTOMIZATION_FIELDS);
        $this->bulkInsert('customization_field_lang', $newCustomizationFieldsLang, CannotDuplicateProductException::FAILED_DUPLICATE_CUSTOMIZATION_FIELDS);
    }

    /**
     * @param int $oldProductId
     * @param int $newProductId
     */
    private function duplicateTags(int $oldProductId, int $newProductId): void
    {
        $this->duplicateProductTable(
            'product_tag',
            $oldProductId,
            $newProductId,
            CannotDuplicateProductException::FAILED_DUPLICATE_TAGS
        );
    }

    /**
     * @param int $oldProductId
     * @param int $newProductId
     *
     * @throws CannotDuplicateProductException
     * @throws CoreException
     */
    private function duplicateVirtualProductFiles(int $oldProductId, int $newProductId): void
    {
        $oldVirtualProductFiles = $this->getRows('product_download', ['id_product' => $oldProductId], CannotDuplicateProductException::FAILED_DUPLICATE_DOWNLOADS);

        $newVirtualProductFiles = [];
        foreach ($oldVirtualProductFiles as $oldVirtualProductFile) {
            $newFilename = VirtualProductFile::getNewFilename();
            copy(_PS_DOWNLOAD_DIR_ . $oldVirtualProductFile['filename'], _PS_DOWNLOAD_DIR_ . $newFilename);
            $newVirtualProductFiles[] = array_merge($oldVirtualProductFile, [
                'id_product_download' => null,
                'id_product' => $newProductId,
                'filename' => $newFilename,
                'date_add' => date('Y-m-d H:i:s'),
            ]);
        }
        $this->bulkInsert('product_download', $newVirtualProductFiles, CannotDuplicateProductException::FAILED_DUPLICATE_DOWNLOADS);
    }

    /**
     * @param int $oldProductId
     * @param int $newProductId
     * @param array $combinationMatching
     * @param ShopConstraint $shopConstraint
     *
     * @throws CannotDuplicateProductException
     * @throws CoreException
     */
    private function duplicateImages(int $oldProductId, int $newProductId, array $combinationMatching, ShopConstraint $shopConstraint): void
    {
        $oldImages = $this->getRows('image', ['id_product' => $oldProductId], CannotDuplicateProductException::FAILED_DUPLICATE_IMAGES);

        $imagesMapping = [];
        $fs = new Filesystem();
        foreach ($oldImages as $oldImage) {
            $oldImageId = new ImageId((int) $oldImage['id_image']);
            $newImage = $this->productImageRepository->duplicate($oldImageId, new ProductId($newProductId), $shopConstraint);
            if (null === $newImage) {
                continue;
            }

            $newImageId = new ImageId((int) $newImage->id);
            $imageTypes = $this->productImageRepository->getProductImageTypes();

            // Copy the generated images instead of generating them is more performant
            foreach ($imageTypes as $imageType) {
                $fs->copy(
                    $this->productImageSystemPathFactory->getPathByType($oldImageId, $imageType->name),
                    $this->productImageSystemPathFactory->getPathByType($newImageId, $imageType->name)
                );
            }

            // Also copy original
            $oldOriginalPath = $this->productImageSystemPathFactory->getPath($oldImageId);
            $newOriginalPath = $this->productImageSystemPathFactory->getPath($newImageId);
            $fs->copy(
                $oldOriginalPath,
                $newOriginalPath
            );

            // And fileType
            $originalFileTypePath = dirname($oldOriginalPath) . '/fileType';
            if (file_exists($originalFileTypePath)) {
                $fs->copy(
                    $originalFileTypePath,
                    dirname($newOriginalPath) . '/fileType'
                );
            }

            $imagesMapping[$oldImageId->getValue()] = $newImageId->getValue();
        }

        $oldCombinationImages = $this->getRows('product_attribute_image', ['id_image' => array_keys($imagesMapping)], CannotDuplicateProductException::FAILED_DUPLICATE_IMAGES);
        $newCombinationImages = [];
        foreach ($oldCombinationImages as $oldCombinationImage) {
            $newCombinationImages[] = [
                'id_image' => $imagesMapping[(int) $oldCombinationImage['id_image']],
                'id_product_attribute' => $combinationMatching[(int) $oldCombinationImage['id_product_attribute']],
            ];
        }
        $this->bulkInsert('product_attribute_image', $newCombinationImages, CannotDuplicateProductException::FAILED_DUPLICATE_IMAGES);
    }

    /**
     * @param int $oldProductId
     * @param int $newProductId
     * @param int[] $shopIds
     *
     * @throws CannotDuplicateProductException
     * @throws CoreException
     */
    private function duplicateCarriers(int $oldProductId, int $newProductId, array $shopIds): void
    {
        $this->duplicateProductTableForShops(
            'product_carrier',
            $oldProductId,
            $newProductId,
            $shopIds,
            CannotDuplicateProductException::FAILED_DUPLICATE_CARRIERS
        );
    }

    /**
     * @param int $oldProductId
     * @param int $newProductId
     *
     * @throws CannotDuplicateProductException
     * @throws CoreException
     */
    private function duplicateAttachmentAssociation(int $oldProductId, int $newProductId): void
    {
        $this->duplicateProductTable('product_attachment', $oldProductId, $newProductId, CannotDuplicateProductException::FAILED_DUPLICATE_ATTACHMENT_ASSOCIATION);
    }

    /**
     * @param int $newProductId
     * @param int $oldProductId
     *
     * @throws CannotUpdateProductException
     * @throws CoreException
     */
    private function updateDefaultAttribute(int $newProductId, int $oldProductId): void
    {
        try {
            if (!Product::updateDefaultAttribute($newProductId)) {
                throw new CannotUpdateProductException(
                    sprintf('Failed to update default attribute when duplicating product %d', $oldProductId),
                    CannotUpdateProductException::FAILED_UPDATE_DEFAULT_ATTRIBUTE
                );
            }
        } catch (PrestaShopException $e) {
            throw new CoreException(
                sprintf('Error occurred when trying to duplicate product #%d. Failed to update default attribute', $oldProductId),
                0,
                $e
            );
        }
    }

    /**
     * Fetch all rows related to a product on a specific table and duplicate it by replacing only the column id_product.
     *
     * @param string $table
     * @param int $oldProductId
     * @param int $newProductId
     * @param int $errorCode
     *
     * @throws InvalidArgumentException
     * @throws CannotDuplicateProductException
     */
    private function duplicateProductTable(string $table, int $oldProductId, int $newProductId, int $errorCode): void
    {
        $oldRows = $this->getRows($table, ['id_product' => $oldProductId], $errorCode);
        if (empty($oldRows)) {
            return;
        }
        $newRows = $this->replaceInRows($oldRows, ['id_product' => $newProductId]);
        $this->bulkInsert($table, $newRows, $errorCode);
    }

    /**
     * Fetch all rows related to a product on a specific table for a set of shop IDs and duplicate it by replacing only the column id_product.
     *
     * @param string $table
     * @param int $oldProductId
     * @param int $newProductId
     * @param int[] $shopIds
     * @param int $errorCode
     *
     * @throws InvalidArgumentException
     * @throws CannotDuplicateProductException
     */
    private function duplicateProductTableForShops(string $table, int $oldProductId, int $newProductId, array $shopIds, int $errorCode): void
    {
        $oldRows = $this->getRows($table, [
            'id_product' => $oldProductId,
            'id_shop' => $shopIds,
        ], $errorCode);
        if (empty($oldRows)) {
            return;
        }
        $newRows = $this->replaceInRows($oldRows, ['id_product' => $newProductId]);
        $this->bulkInsert($table, $newRows, $errorCode);
    }

    /**
     * Bulk insert one row, the values is an associative array defining each column in the row.
     *
     * @param string $table
     * @param array $rowValues
     * @param int $errorCode
     *
     * @return int
     */
    private function insertRow(string $table, array $rowValues, int $errorCode): int
    {
        $this->bulkInsert($table, [$rowValues], $errorCode);

        return (int) $this->connection->lastInsertId();
    }

    /**
     * Bulk insert some row values, all row must be formatted with the exact same keys and in the same order
     * so that the defined column match the values for each row.
     *
     * @param string $table
     * @param array $multipleRowValues
     * @param int $errorCode
     */
    private function bulkInsert(string $table, array $multipleRowValues, int $errorCode): void
    {
        if (empty($multipleRowValues)) {
            return;
        }

        $insertKeys = array_keys(reset($multipleRowValues));
        $bulkInsertSql = 'INSERT INTO ' . $this->dbPrefix . $table . ' (' . implode(',', $insertKeys) . ') VALUES ';
        foreach ($multipleRowValues as $i => $rowValue) {
            if (array_keys($rowValue) !== $insertKeys) {
                throw new InvalidArgumentException('The provided data has different keys in some rows');
            }

            $bulkInsertSql .= '(' . implode(',', array_map(static function ($columnValue): string {
                if ($columnValue === null) {
                    return 'null';
                } elseif (!empty($columnValue) && DateTime::isNull($columnValue)) {
                    // We can't use 0000-00-00 as a value it's not valid in Mysql, so we use null instead
                    return 'null';
                }

                if (gettype($columnValue) == 'string') {
                    $columnValue = str_replace("'", "''", $columnValue);
                }

                // We stringify values to avoid SQL syntax error, the float and integers will correctly casted in the DB anyway
                // however string values and date time need to be quoted
                return "'$columnValue'";
            }, $rowValue)) . ')';
            if ($i < count($multipleRowValues) - 1) {
                $bulkInsertSql .= ',';
            } else {
                $bulkInsertSql .= ';';
            }
        }

        try {
            $this->connection->executeStatement($bulkInsertSql);
        } catch (Exception $e) {
            throw new CannotDuplicateProductException(
                sprintf('Cannot bulk insert into table %s failed', $table),
                $errorCode
            );
        }
    }

    /**
     * Replace columns values in every row.
     *
     * @param array $rows
     * @param array $replacements
     *
     * @return array
     */
    private function replaceInRows(array $rows, array $replacements): array
    {
        $replacedRows = [];
        foreach ($rows as $key => $row) {
            $replacedRows[$key] = array_merge($row, $replacements);
        }

        return $replacedRows;
    }

    /**
     * Returns all the columns of a specific table, you can add criteria to filter, prefix is automatically added.
     *
     * @param string $table
     * @param array $criteria
     * @param int $errorCode
     * @param array<string, string|array<string|int>> $orderBy
     *
     * @return array
     */
    private function getRows(string $table, array $criteria, int $errorCode, array $orderBy = []): array
    {
        $qb = $this->connection
            ->createQueryBuilder()
            ->from($this->dbPrefix . $table)
            ->select('*')
        ;

        foreach ($criteria as $column => $value) {
            if (is_array($value)) {
                $arrayType = is_int(reset($value)) ? Connection::PARAM_INT_ARRAY : Connection::PARAM_STR_ARRAY;
                $qb
                    ->andWhere("$column IN (:$column)")
                    ->setParameter(":$column", $value, $arrayType)
                ;
            } else {
                $qb
                    ->andWhere("$column = :$column")
                    ->setParameter(":$column", $value)
                ;
            }
        }

        foreach ($orderBy as $orderKey => $orderWay) {
            $qb->addOrderBy($orderKey, $orderWay);
        }

        try {
            $rows = $qb->execute()->fetchAllAssociative();
        } catch (Exception $e) {
            throw new CannotDuplicateProductException(
                sprintf('Cannot select rows from table %s', $this->dbPrefix . $table),
                $errorCode
            );
        }

        return $rows;
    }
}
