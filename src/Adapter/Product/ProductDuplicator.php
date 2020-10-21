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
use Pack;
use PrestaShop\PrestaShop\Adapter\Product\Repository\ProductRepository;
use PrestaShop\PrestaShop\Core\Domain\Product\Exception\CannotDuplicateProductException;
use PrestaShop\PrestaShop\Core\Domain\Product\Exception\CannotUpdateProductException;
use PrestaShop\PrestaShop\Core\Exception\CoreException;
use PrestaShop\PrestaShop\Core\Hook\HookDispatcherInterface;
use PrestaShopException;
use Product;
use Search;

/**
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
     * @param ProductRepository $productRepository
     * @param HookDispatcherInterface $hookDispatcher
     */
    public function __construct(
        ProductRepository $productRepository,
        HookDispatcherInterface $hookDispatcher
    ) {
        $this->productRepository = $productRepository;
        $this->hookDispatcher = $hookDispatcher;
    }

    /**
     * @param Product $product
     *
     * @return Product
     */
    public function duplicate(Product $product): Product
    {
        //@todo: don't forget controller hooks PrestaShopBundle\Controller\Admin\ProductController L1063
        $oldProductId = (int) $product->id;
        $newProduct = $this->duplicateProduct($product);
        $newProductId = (int) $newProduct->id;

        $this->duplicateRelations($newProductId, $oldProductId);

        if ($product->hasAttributes()) {
            $this->updateDefaultAttribute($newProductId, $oldProductId);
        }

        $this->hookDispatcher->dispatchWithParameters(
            'actionProductAdd',
            ['id_product_old' => $oldProductId, 'id_product' => $newProductId, 'product' => $newProduct]
        );

        $this->updateSearchIndexation($newProduct, $oldProductId);

        return $newProduct;
    }

    /**
     * @param Product $newProduct
     * @param int $oldProductId
     *
     * @throws CannotUpdateProductException
     */
    private function updateSearchIndexation(Product $newProduct, int $oldProductId): void
    {
        if (!in_array($newProduct->visibility, ['both', 'search']) || !$this->isSearchIndexationOn) {
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
        unset($product->id, $product->id_product);
        $product->indexed = 0;
        $product->active = 0;

        $this->productRepository->add($product)->getValue();

        return $product;
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
        $this->duplicateReduction($oldProductId, $newProductId);
        $this->duplicateRelatedProducts($oldProductId, $newProductId);
        $this->duplicateFeatures($oldProductId, $newProductId);
        $this->duplicateSpecificPrices($oldProductId, $newProductId);
        $this->duplicatePackedProducts($oldProductId, $newProductId);
        $this->duplicateCustomizationFields($oldProductId, $newProductId);
        $this->duplicateTags($oldProductId, $newProductId);
        $this->duplicateDownload($oldProductId, $newProductId);
        $this->duplicateImages($oldProductId, $newProductId, $combinationImages);
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
            0
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
            0
        );
    }

    /**
     * @param int $oldProductId
     * @param int $newProductId
     *
     * @return array<string, array<int, array<int, int>>> combination images
     *                       [
     *                       'old' => [1 {id product attribute} => [0 {index} => 1 {id image}]]
     *                       'new' => [2 {id product attribute} => [0 {index} => 3 {id image}]]
     *                       ]
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
            0
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
    private function duplicateReduction(int $oldProductId, int $newProductId): void
    {
        /* @see GroupReduction::duplicateReduction() */
        $this->duplicateRelation(
            [GroupReduction::class, 'duplicateReduction'],
            [$oldProductId, $newProductId],
            0
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
            0
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
            0
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
            0
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
            0
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
            0
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
            0
        );
    }

    /**
     * @param int $oldProductId
     * @param int $newProductId
     *
     * @throws CannotDuplicateProductException
     * @throws CoreException
     */
    private function duplicateDownload(int $oldProductId, int $newProductId): void
    {
        /* @see Product::duplicateDownload() */
        $this->duplicateRelation(
            [Product::class, 'duplicateDownload'],
            [$oldProductId, $newProductId],
            0
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
     * @param int $oldProductId
     * @param int $newProductId
     * @param array $combinationImages
     *
     * @throws CannotDuplicateProductException
     * @throws CoreException
     */
    public function duplicateImages(int $oldProductId, int $newProductId, array $combinationImages): void
    {
        //@todo: add option of noImage in command
        /* @see Image::duplicateProductImages() */
        $this->duplicateRelation(
            [Image::class, 'duplicateProductImages'],
            [$oldProductId, $newProductId, $combinationImages],
            0
        );
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
            $result = call_user_func($staticCallback, $arguments);

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
