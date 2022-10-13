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

use Category;
use GroupReduction;
use Image;
use Language;
use Pack;
use PrestaShop\PrestaShop\Adapter\Product\Repository\ProductRepository;
use PrestaShop\PrestaShop\Core\Domain\Product\Exception\CannotDuplicateProductException;
use PrestaShop\PrestaShop\Core\Domain\Product\Exception\CannotUpdateProductException;
use PrestaShop\PrestaShop\Core\Domain\Product\ProductSettings;
use PrestaShop\PrestaShop\Core\Domain\Product\ValueObject\ProductId;
use PrestaShop\PrestaShop\Core\Domain\Product\ValueObject\ProductVisibility;
use PrestaShop\PrestaShop\Core\Domain\Shop\ValueObject\ShopId;
use PrestaShop\PrestaShop\Core\Exception\CoreException;
use PrestaShop\PrestaShop\Core\Hook\HookDispatcherInterface;
use PrestaShop\PrestaShop\Core\Multistore\MultistoreContextCheckerInterface;
use PrestaShop\PrestaShop\Core\Util\String\StringModifierInterface;
use PrestaShopException;
use Product;
use Search;
use Shop;
use ShopGroup;
use Symfony\Component\Translation\TranslatorInterface;

/**
 * @todo for now this service is mainly oriented for single shop (only prices are handled for multi shop)
 *       This service will likely have many things in common with ProductShopUpdater::copyToShop method, so it
 *       might be interesting to refacto and merge them into one at some point
 * Duplicates product
 */
class ProductDuplicator
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
     * @var bool
     */
    private $isSearchIndexationOn;

    /**
     * @var MultistoreContextCheckerInterface
     */
    private $multistoreContextChecker;

    /**
     * @var TranslatorInterface
     */
    private $translator;

    /**
     * @var StringModifierInterface
     */
    private $stringModifier;

    /**
     * @param ProductRepository $productRepository
     * @param HookDispatcherInterface $hookDispatcher
     * @param bool $isSearchIndexationOn
     * @param MultistoreContextCheckerInterface $multistoreContextChecker
     * @param TranslatorInterface $translator
     * @param StringModifierInterface $stringModifier
     */
    public function __construct(
        ProductRepository $productRepository,
        HookDispatcherInterface $hookDispatcher,
        bool $isSearchIndexationOn,
        MultistoreContextCheckerInterface $multistoreContextChecker,
        TranslatorInterface $translator,
        StringModifierInterface $stringModifier
    ) {
        $this->productRepository = $productRepository;
        $this->hookDispatcher = $hookDispatcher;
        $this->isSearchIndexationOn = $isSearchIndexationOn;
        $this->multistoreContextChecker = $multistoreContextChecker;
        $this->translator = $translator;
        $this->stringModifier = $stringModifier;
    }

    /**
     * @param ProductId $productId
     *
     * @return ProductId new product id
     *
     * @throws CannotDuplicateProductException
     * @throws CannotUpdateProductException
     * @throws CoreException
     */
    public function duplicate(ProductId $productId): ProductId
    {
        //@todo: add database transaction. After/if PR #21740 gets merged
        $product = $this->productRepository->get($productId);
        $oldProductId = $productId->getValue();
        $this->hookDispatcher->dispatchWithParameters(
            'actionAdminDuplicateBefore',
            ['id_product' => $oldProductId]
        );
        $newProduct = $this->duplicateProduct($product);
        $newProductId = (int) $newProduct->id;

        $this->duplicateRelations($oldProductId, $newProductId);

        if ($product->hasAttributes()) {
            $this->updateDefaultAttribute($newProductId, $oldProductId);
        }

        $this->hookDispatcher->dispatchWithParameters(
            'actionProductAdd',
            ['id_product_old' => $oldProductId, 'id_product' => $newProductId, 'product' => $newProduct]
        );

        $this->updateSearchIndexation($newProduct, $oldProductId);

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
     * @todo this function should actualy use the ProductIndexationUpdater service
     *
     * @param Product $newProduct
     * @param int $oldProductId
     *
     * @throws CannotUpdateProductException
     */
    private function updateSearchIndexation(Product $newProduct, int $oldProductId): void
    {
        $productIsVisibleInSearch = in_array(
            $newProduct->visibility,
            [ProductVisibility::VISIBLE_EVERYWHERE, ProductVisibility::VISIBLE_IN_SEARCH]
        );

        if (!$this->isSearchIndexationOn || !$productIsVisibleInSearch) {
            return;
        }

        try {
            if (!Search::indexation(false, $newProduct->id)) {
                throw new CannotUpdateProductException(
                    sprintf('Cannot update search indexation when duplicating product %d', $oldProductId),
                    CannotUpdateProductException::FAILED_UPDATE_SEARCH_INDEXATION
                );
            }
        } catch (PrestaShopException $e) {
            throw new CoreException(
                sprintf('Error occurred when duplicating product %d. Failed to update search indexation', $oldProductId),
                0,
                $e
            );
        }
    }

    /**
     * @param Product $product
     *
     * @return Product the new product
     */
    private function duplicateProduct(Product $product): Product
    {
        $product->name = $this->getNewProductName($product->name);
        $this->setPriceByShops($product);

        $product->indexed = false;
        $product->active = false;

        return $this->productRepository->duplicate($product);
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
     * @param Product $product
     */
    private function setPriceByShops(Product $product): void
    {
        if (!empty($product->price) || !$this->multistoreContextChecker->isGroupShopContext()) {
            return;
        }

        foreach ($this->getContextShops() as $shop) {
            $priceByShop = $this->productRepository->getPriceByShop(
                new ProductId((int) $product->id),
                new ShopId((int) $shop['id_shop'])
            );

            if (!$priceByShop) {
                continue;
            }

            $product->price = (float) (string) $priceByShop;
        }
    }

    /**
     * @return array<int, array<string, string>>
     *
     * @throws CoreException
     */
    private function getContextShops(): array
    {
        try {
            $shops = ShopGroup::getShopsFromGroup(Shop::getContextShopGroupID());
        } catch (PrestaShopException $e) {
            throw new CoreException(
                'Error occurred when trying to get context shop groups',
                0,
                $e
            );
        }

        return $shops;
    }

    /**
     * Duplicates related product entities & associations
     *
     * @param int $oldProductId
     * @param int $newProductId
     *
     * @throws CannotDuplicateProductException
     * @throws CoreException
     */
    private function duplicateRelations(int $oldProductId, int $newProductId): void
    {
        $this->duplicateCategories($oldProductId, $newProductId);
        $this->duplicateSuppliers($oldProductId, $newProductId);
        $combinationImages = $this->duplicateAttributes($oldProductId, $newProductId);
        $this->duplicateGroupReduction($oldProductId, $newProductId);
        $this->duplicateRelatedProducts($oldProductId, $newProductId);
        $this->duplicateFeatures($oldProductId, $newProductId);
        $this->duplicateSpecificPrices($oldProductId, $newProductId);
        $this->duplicatePackedProducts($oldProductId, $newProductId);
        $this->duplicateCustomizationFields($oldProductId, $newProductId);
        $this->duplicatePrices($oldProductId, $newProductId);
        $this->duplicateTags($oldProductId, $newProductId);
        $this->duplicateTaxes($oldProductId, $newProductId);
        $this->duplicateDownloads($oldProductId, $newProductId);
        $this->duplicateImages($oldProductId, $newProductId, $combinationImages);
        $this->duplicateCarriers($oldProductId, $newProductId);
        $this->duplicateAttachmentAssociation($oldProductId, $newProductId);
    }

    /**
     * @param int $oldProductId
     * @param int $newProductId
     *
     * @throws CannotDuplicateProductException
     * @throws CoreException
     */
    private function duplicateCategories(int $oldProductId, int $newProductId): void
    {
        /* @see Category::duplicateProductCategories() */
        $this->duplicateRelation(
            [Category::class, 'duplicateProductCategories'],
            [$oldProductId, $newProductId],
            CannotDuplicateProductException::FAILED_DUPLICATE_CATEGORIES
        );
    }

    /**
     * @param int $oldProductId
     * @param int $newProductId
     *
     * @throws CannotDuplicateProductException
     * @throws CoreException
     */
    private function duplicateSuppliers(int $oldProductId, int $newProductId): void
    {
        /* @see Product::duplicateSuppliers() */
        $this->duplicateRelation(
            [Product::class, 'duplicateSuppliers'],
            [$oldProductId, $newProductId],
            CannotDuplicateProductException::FAILED_DUPLICATE_SUPPLIERS
        );
    }

    /**
     * @param int $oldProductId
     * @param int $newProductId
     *
     * @return array<string, array<int, array<int, int>>> combination images
     *                                                    [
     *                                                    'old' => [1 {id product attribute} => [0 {index} => 1 {id image}]]
     *                                                    'new' => [2 {id product attribute} => [0 {index} => 3 {id image}]]
     *                                                    ]
     *
     * @throws CannotDuplicateProductException
     * @throws CoreException
     */
    private function duplicateAttributes(int $oldProductId, int $newProductId): array
    {
        /* @see Product::duplicateAttributes() */
        $result = $this->duplicateRelation(
            [Product::class, 'duplicateAttributes'],
            [$oldProductId, $newProductId],
            CannotDuplicateProductException::FAILED_DUPLICATE_ATTRIBUTES
        );

        if (!$result) {
            return [];
        }

        return $result;
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
        /* @see Product::duplicateAccessories() */
        $this->duplicateRelation(
            [Product::class, 'duplicateAccessories'],
            [$oldProductId, $newProductId],
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
     *
     * @throws CannotDuplicateProductException
     * @throws CoreException
     */
    private function duplicateSpecificPrices(int $oldProductId, int $newProductId): void
    {
        /* @see Product::duplicateSpecificPrices() */
        $this->duplicateRelation(
            [Product::class, 'duplicateSpecificPrices'],
            [$oldProductId, $newProductId],
            CannotDuplicateProductException::FAILED_DUPLICATE_SPECIFIC_PRICES
        );
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
     * @throws CoreException
     */
    private function duplicateCustomizationFields(int $oldProductId, int $newProductId): void
    {
        /* @see Product::duplicateCustomizationFields() */
        $this->duplicateRelation(
            [Product::class, 'duplicateCustomizationFields'],
            [$oldProductId, $newProductId],
            CannotDuplicateProductException::FAILED_DUPLICATE_CUSTOMIZATION_FIELDS
        );
    }

    /**
     * @param int $oldProductId
     * @param int $newProductId
     *
     * @throws CannotDuplicateProductException
     * @throws CoreException
     */
    private function duplicatePrices(int $oldProductId, int $newProductId): void
    {
        /* @see Product::duplicatePrices() */
        $this->duplicateRelation(
            [Product::class, 'duplicatePrices'],
            [$oldProductId, $newProductId],
            CannotDuplicateProductException::FAILED_DUPLICATE_PRICES
        );
    }

    /**
     * @param int $oldProductId
     * @param int $newProductId
     *
     * @throws CannotDuplicateProductException
     * @throws CoreException
     */
    private function duplicateTags(int $oldProductId, int $newProductId): void
    {
        /* @see Product::duplicateTags() */
        $this->duplicateRelation(
            [Product::class, 'duplicateTags'],
            [$oldProductId, $newProductId],
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
     *
     * @throws CannotDuplicateProductException
     * @throws CoreException
     */
    private function duplicateCarriers(int $oldProductId, int $newProductId): void
    {
        /* @see Product::duplicateCarriers() */
        $this->duplicateRelation(
            [Product::class, 'duplicateCarriers'],
            [$oldProductId, $newProductId],
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
}
