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

use Combination;
use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Exception;
use GroupReduction;
use Image;
use Language;
use Pack;
use PrestaShop\PrestaShop\Adapter\Product\Combination\Repository\CombinationRepository;
use PrestaShop\PrestaShop\Adapter\Product\Combination\Update\CombinationStockProperties;
use PrestaShop\PrestaShop\Adapter\Product\Combination\Update\CombinationStockUpdater;
use PrestaShop\PrestaShop\Adapter\Product\Repository\ProductRepository;
use PrestaShop\PrestaShop\Adapter\Product\Repository\ProductSupplierRepository;
use PrestaShop\PrestaShop\Adapter\Product\SpecificPrice\Repository\SpecificPriceRepository;
use PrestaShop\PrestaShop\Adapter\Product\Stock\Repository\StockAvailableRepository;
use PrestaShop\PrestaShop\Adapter\Product\Stock\Update\ProductStockProperties;
use PrestaShop\PrestaShop\Adapter\Product\Stock\Update\ProductStockUpdater;
use PrestaShop\PrestaShop\Core\Domain\Product\Combination\ValueObject\CombinationId;
use PrestaShop\PrestaShop\Core\Domain\Product\Exception\CannotDuplicateProductException;
use PrestaShop\PrestaShop\Core\Domain\Product\Exception\CannotUpdateProductException;
use PrestaShop\PrestaShop\Core\Domain\Product\ProductSettings;
use PrestaShop\PrestaShop\Core\Domain\Product\Stock\Exception\StockAvailableNotFoundException;
use PrestaShop\PrestaShop\Core\Domain\Product\Stock\ValueObject\OutOfStockType;
use PrestaShop\PrestaShop\Core\Domain\Product\Stock\ValueObject\StockModification;
use PrestaShop\PrestaShop\Core\Domain\Product\Supplier\ValueObject\ProductSupplierId;
use PrestaShop\PrestaShop\Core\Domain\Product\ValueObject\ProductId;
use PrestaShop\PrestaShop\Core\Domain\Product\ValueObject\ProductType;
use PrestaShop\PrestaShop\Core\Domain\Shop\ValueObject\ShopConstraint;
use PrestaShop\PrestaShop\Core\Domain\Shop\ValueObject\ShopId;
use PrestaShop\PrestaShop\Core\Exception\CoreException;
use PrestaShop\PrestaShop\Core\Exception\InvalidArgumentException;
use PrestaShop\PrestaShop\Core\Hook\HookDispatcherInterface;
use PrestaShop\PrestaShop\Core\Repository\AbstractMultiShopObjectModelRepository;
use PrestaShop\PrestaShop\Core\Util\String\StringModifierInterface;
use PrestaShopException;
use Product;
use Shop;
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
        CombinationStockUpdater $combinationStockUpdater
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
        $shopIds = $this->getShopIdsByConstraint($sourceProductId, $shopConstraint);
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
     * @param ProductId $sourceProductId
     * @param ShopConstraint $shopConstraint
     *
     * @return ShopId[]
     */
    private function getShopIdsByConstraint(ProductId $sourceProductId, ShopConstraint $shopConstraint): array
    {
        if ($shopConstraint->getShopId()) {
            return [$shopConstraint->getShopId()];
        } elseif ($shopConstraint->getShopGroupId()) {
            return $this->productRepository->getAssociatedShopIdsFromGroup($sourceProductId, $shopConstraint->getShopGroupId());
        }

        return $this->productRepository->getAssociatedShopIds($sourceProductId);
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
        }, $this->getShopIdsByConstraint(new ProductId($oldProductId), $shopConstraint));

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
        $this->duplicateTaxes($oldProductId, $newProductId);
        $this->duplicateDownloads($oldProductId, $newProductId);
        $this->duplicateImages($oldProductId, $newProductId, $combinationMatching);
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

            $sourceStock = $this->stockAvailableRepository->getForProduct(new ProductId($oldProductId), $targetShopId);
            $outOfStock = new OutOfStockType((int) $sourceStock->out_of_stock);

            $stockModification = StockModification::buildFixedQuantity((int) $sourceStock->quantity);
            $stockProperties = new ProductStockProperties(
                null,
                $stockModification,
                $outOfStock,
                null,
                $sourceStock->location
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
            $sourceStock = $this->stockAvailableRepository->getForCombination($oldCombinationId, $targetShopId);

            $stockModification = StockModification::buildFixedQuantity((int) $sourceStock->quantity);
            $stockProperties = new CombinationStockProperties(
                $stockModification,
                null,
                $sourceStock->location
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
        foreach ($oldRows as $oldRow) {
            $newRows[] = [
                'id_product' => $newProductId,
                'id_category' => $oldRow['id_category'],
                'position' => '(SELECT tmp.max + 1 FROM (
					SELECT MAX(cp.`position`) AS max
					FROM `' . $this->dbPrefix . 'category_product` cp
					WHERE cp.`id_category`=' . (int) $oldRow['id_category'] . ') AS tmp)',
            ];
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
        foreach ($oldCombinationsShop as $oldCombination) {
            $oldCombinationId = (int) $oldCombination['id_product_attribute'];

            if (!isset($combinationMatching[$oldCombinationId])) {
                // New combination to create, associate it to the related shop
                $shopId = new ShopId((int) $oldCombination['id_shop']);
                $oldCombination = $this->combinationRepository->get(new CombinationId($oldCombinationId), $shopId);
                $oldCombination->id_product = $newProductId;
                $newCombination = $this->duplicateObjectModelToShop($oldCombination, $shopId);
                $newCombinationId = (int) $newCombination->id;
                $combinationMatching[$oldCombinationId] = $newCombinationId;

                // Associate attributes to combination
                $oldAttributes = $this->getRows(
                    'product_attribute_combination',
                    ['id_product_attribute' => $oldCombinationId],
                    CannotDuplicateProductException::FAILED_DUPLICATE_COMBINATIONS
                );
                $newAttributes = $this->replaceInRows($oldAttributes, ['id_product_attribute' => $newCombinationId]);
                $this->bulkInsert('product_attribute_combination', $newAttributes, CannotDuplicateProductException::FAILED_DUPLICATE_COMBINATIONS);
            } else {
                // Combination already created to another shop, now we focus on duplicating the data for every other shop
                $newCombinationId = $combinationMatching[$oldCombinationId];
                $newCombinationShop = $this->replaceInRows([$oldCombination], ['id_product_attribute' => $newCombinationId, 'id_product' => $newProductId]);
                $this->bulkInsert('product_attribute_shop', $newCombinationShop, CannotDuplicateProductException::FAILED_DUPLICATE_COMBINATIONS);
            }
        }

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
        /* @see GroupReduction::duplicateReduction() */
        $this->duplicateRelation(
            [GroupReduction::class, 'duplicateReduction'],
            [$oldProductId, $newProductId],
            CannotDuplicateProductException::FAILED_DUPLICATE_GROUP_REDUCTION
        );
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
        /* @see Product::duplicateFeatures() */
        $this->duplicateRelation(
            [Product::class, 'duplicateFeatures'],
            [$oldProductId, $newProductId],
            CannotDuplicateProductException::FAILED_DUPLICATE_FEATURES
        );
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
        /* @see Pack::duplicate() */
        $this->duplicateRelation(
            [Pack::class, 'duplicate'],
            [$oldProductId, $newProductId],
            CannotDuplicateProductException::FAILED_DUPLICATE_PACKED_PRODUCTS
        );
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
    private function duplicateTaxes(int $oldProductId, int $newProductId): void
    {
        /* @see Product::duplicateTaxes() */
        $this->duplicateRelation(
            [Product::class, 'duplicateTaxes'],
            [$oldProductId, $newProductId],
            CannotDuplicateProductException::FAILED_DUPLICATE_TAXES
        );
    }

    /**
     * @param int $oldProductId
     * @param int $newProductId
     *
     * @throws CannotDuplicateProductException
     * @throws CoreException
     */
    private function duplicateDownloads(int $oldProductId, int $newProductId): void
    {
        /* @see Product::duplicateDownload() */
        $this->duplicateRelation(
            [Product::class, 'duplicateDownload'],
            [$oldProductId, $newProductId],
            CannotDuplicateProductException::FAILED_DUPLICATE_DOWNLOADS
        );
    }

    /**
     * @param int $oldProductId
     * @param int $newProductId
     * @param array $combinationImages
     *
     * @throws CannotDuplicateProductException
     * @throws CoreException
     */
    private function duplicateImages(int $oldProductId, int $newProductId, array $combinationImages): void
    {
        /* @see Image::duplicateProductImages() */
        $this->duplicateRelation(
            [Image::class, 'duplicateProductImages'],
            [$oldProductId, $newProductId, $combinationImages],
            CannotDuplicateProductException::FAILED_DUPLICATE_IMAGES
        );
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
        /* @see Product::duplicateAttachmentAssociation() */
        $this->duplicateRelation(
            [Product::class, 'duplicateAttachmentAssociation'],
            [$oldProductId, $newProductId],
            CannotDuplicateProductException::FAILED_DUPLICATE_ATTACHMENT_ASSOCIATION
        );
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
     * Wraps product relations duplication in try-catch
     *
     * @param array $staticCallback
     * @param array $arguments
     * @param int $errorCode
     *
     * @return array|null result of callback. If result is array then its returned, else null is returned
     *
     * @throws CannotDuplicateProductException
     * @throws CoreException
     */
    private function duplicateRelation(array $staticCallback, array $arguments, int $errorCode): ?array
    {
        try {
            $result = call_user_func($staticCallback, ...$arguments);

            if (is_array($result)) {
                return $result;
            }

            if (!$result) {
                throw new CannotDuplicateProductException(
                    sprintf('Cannot duplicate product. [%s] failed', implode('::', $staticCallback)),
                    $errorCode
                );
            }

            return null;
        } catch (PrestaShopException $e) {
            throw new CoreException(sprintf(
                'Error occured when trying to duplicate product. Method [%s]',
                implode('::', $staticCallback)
            ));
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
     * Bulk insert some row values, all row must be formatted with the exact same keys and in the same order
     * so that the defined column match the values for each row.
     *
     * @param string $table
     * @param array $rowValues
     * @param int $errorCode
     */
    private function bulkInsert(string $table, array $rowValues, int $errorCode): void
    {
        if (empty($rowValues)) {
            return;
        }

        $insertKeys = array_keys(reset($rowValues));
        $bulkInsertSql = 'INSERT IGNORE INTO ' . $this->dbPrefix . $table . ' (' . implode(',', $insertKeys) . ') VALUES ';
        foreach ($rowValues as $i => $rowValue) {
            if (array_keys($rowValue) !== $insertKeys) {
                throw new InvalidArgumentException('The provided data has different keys in some rows');
            }

            $bulkInsertSql .= '(' . implode(',', array_map(static function ($columnValue): string {
                // We stringify values to avoid SQL syntax error, the float and integers will correctly casted in the DB anyway
                // however string values and date time need to be quoted
                return "'$columnValue'";
            }, $rowValue)) . ')';
            if ($i < count($rowValues) - 1) {
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
                sprintf('Cannot select rows from table %s', $table),
                $errorCode
            );
        }

        return $rows;
    }
}
