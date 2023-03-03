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

use Cache;
use Category;
use PrestaShop\PrestaShop\Adapter\Category\Repository\CategoryRepository;
use PrestaShop\PrestaShop\Adapter\Product\Repository\ProductRepository;
use PrestaShop\PrestaShop\Core\Domain\Category\Exception\CategoryNotFoundException;
use PrestaShop\PrestaShop\Core\Domain\Category\ValueObject\CategoryId;
use PrestaShop\PrestaShop\Core\Domain\Product\Exception\CannotUpdateProductException;
use PrestaShop\PrestaShop\Core\Domain\Product\ValueObject\ProductId;
use PrestaShop\PrestaShop\Core\Domain\Shop\ValueObject\ShopConstraint;
use PrestaShop\PrestaShop\Core\Domain\Shop\ValueObject\ShopId;
use PrestaShop\PrestaShop\Core\Exception\CoreException;
use Product;
use SpecificPriceRule;

/**
 * Methods to update product & category relations
 */
class ProductCategoryUpdater
{
    /**
     * @var ProductRepository
     */
    private $productRepository;

    /**
     * @var CategoryRepository
     */
    private $categoryRepository;

    /**
     * @param ProductRepository $productRepository
     */
    public function __construct(
        ProductRepository $productRepository,
        CategoryRepository $categoryRepository
    ) {
        $this->productRepository = $productRepository;
        $this->categoryRepository = $categoryRepository;
    }

    /**
     * @param ProductId $productId
     * @param ShopConstraint $shopConstraint
     *
     * Warning: $categoryIds will replace current categories, erasing previous data
     *
     * @throws CannotUpdateProductException
     * @throws CoreException
     */
    public function removeAllCategories(ProductId $productId, ShopConstraint $shopConstraint): void
    {
        // Get current categories based on the provided shop constraint
        $this->deleteCategoriesAssociations($productId, [], $shopConstraint);
        $this->assignFallbackDefaultCategory($productId);

        SpecificPriceRule::applyAllRules([$productId->getValue()]);
        Cache::clean('Product::getProductCategories_' . $productId->getValue());
    }

    /**
     * @param ProductId $productId
     * @param CategoryId[] $newCategoryIds
     * @param CategoryId $defaultCategoryId
     * @param shopConstraint $shopConstraint
     *
     * Warning: $categoryIds will replace current categories, erasing previous data, it will only impact the categories
     * matching the shop constraint though
     *
     * @throws CannotUpdateProductException
     * @throws CoreException
     */
    public function updateCategories(ProductId $productId, array $newCategoryIds, CategoryId $defaultCategoryId, ShopConstraint $shopConstraint): void
    {
        $newCategoryIds = $this->formatCategoryIdsList($newCategoryIds, $defaultCategoryId);
        $this->assertCategoriesExists($newCategoryIds);

        // Get curren categories based on the provided shop constraint
        $this->deleteCategoriesAssociations($productId, $newCategoryIds, $shopConstraint);
        $this->categoryRepository->addProductAssociations($productId, $newCategoryIds);
        $this->updateDefaultCategory($productId, $defaultCategoryId, $shopConstraint);

        SpecificPriceRule::applyAllRules([$productId->getValue()]);
        Cache::clean('Product::getProductCategories_' . $productId->getValue());
    }

    /**
     * @param ProductId $productId
     * @param CategoryId[] $newCategories
     * @param ShopConstraint $shopConstraint
     */
    private function deleteCategoriesAssociations(ProductId $productId, array $newCategories, ShopConstraint $shopConstraint): void
    {
        // Get current categories based on provided context, only these ones should be deleted
        $currentCategoryIds = $this->categoryRepository->getProductCategoryIds($productId, $shopConstraint);
        $deletedCategories = array_filter($currentCategoryIds, static function (CategoryId $currentCategoryId) use ($newCategories): bool {
            foreach ($newCategories as $newCategory) {
                if ($newCategory->getValue() === $currentCategoryId->getValue()) {
                    return false;
                }
            }

            return true;
        });

        if (!empty($deletedCategories)) {
            $this->categoryRepository->removeProductAssociations($productId, $deletedCategories);
        }
    }

    /**
     * @param ProductId $productId
     * @param CategoryId $defaultCategoryId
     * @param ShopConstraint $shopConstraint
     */
    private function updateDefaultCategory(ProductId $productId, CategoryId $defaultCategoryId, ShopConstraint $shopConstraint): void
    {
        $product = $this->productRepository->getByShopConstraint($productId, $shopConstraint);
        $product->id_category_default = $defaultCategoryId->getValue();

        $this->productRepository->partialUpdate(
            $product,
            ['id_category_default'],
            $shopConstraint,
            CannotUpdateProductException::FAILED_UPDATE_DEFAULT_CATEGORY
        );

        $this->assignFallbackDefaultCategory($productId);
    }

    private function assignFallbackDefaultCategory(ProductId $productId): void
    {
        // First define every shop that needs a fallback category defined, we perform the new association after each fallback is defined
        // to avoid the first iteration to influence the fallback choice in following iterations
        $fallbackCategories = [];
        foreach ($this->productRepository->getAssociatedShopIds($productId) as $shopId) {
            $defaultCategoryId = $this->categoryRepository->getProductDefaultCategory($productId, $shopId);
            if (null === $defaultCategoryId) {
                $shopConstraint = ShopConstraint::shop($shopId->getValue());
                $productCategories = $this->categoryRepository->getProductCategoryIds($productId, $shopConstraint);
                if (empty($productCategories)) {
                    // If product has no more categories it needs at least the default category from shop
                    $fallbackDefaultCategory = $this->categoryRepository->getShopDefaultCategory($shopId)->getValue();
                } else {
                    // If product has still some categories we use the one with smallest ID as fallback
                    $fallbackDefaultCategory = min(array_values(array_map(static function (CategoryId $categoryId): int {
                        return $categoryId->getValue();
                    }, $productCategories)));
                }
                $fallbackCategories[$shopId->getValue()] = $fallbackDefaultCategory;
            }
        }

        // We can update each default category for each shop, we also add the missing association
        foreach ($fallbackCategories as $shopId => $fallbackDefaultCategory) {
            $this->categoryRepository->addProductAssociations($productId, [new CategoryId($fallbackDefaultCategory)]);
            $product = $this->productRepository->get($productId, new ShopId($shopId));
            $product->id_category_default = $fallbackDefaultCategory;

            $this->productRepository->partialUpdate(
                $product,
                ['id_category_default'],
                ShopConstraint::shop($shopId),
                CannotUpdateProductException::FAILED_UPDATE_DEFAULT_CATEGORY
            );
        }
    }

    /**
     * Make sure default category ID is in the list and each ID is unique.
     *
     * @param CategoryId[] $categoryIds
     * @param CategoryId $defaultCategoryId
     *
     * @return CategoryId[]
     */
    private function formatCategoryIdsList(array $categoryIds, CategoryId $defaultCategoryId): array
    {
        $categoryIds = array_map(function (CategoryId $categoryId) {
            return $categoryId->getValue();
        }, $categoryIds);

        $categoryIds[] = $defaultCategoryId->getValue();
        $categoryIds = array_unique($categoryIds, SORT_REGULAR);

        return array_map(static function (int $categoryId) {
            return new CategoryId($categoryId);
        }, $categoryIds);
    }

    /**
     * @param CategoryId[] $categoryIds
     *
     * @throws CannotUpdateProductException|CoreException
     */
    private function assertCategoriesExists(array $categoryIds): void
    {
        try {
            foreach ($categoryIds as $categoryId) {
                $this->categoryRepository->assertCategoryExists($categoryId);
            }
        } catch (CategoryNotFoundException $e) {
            throw new CannotUpdateProductException(
                sprintf('Failed to update product categories. Some of categories doesn\'t exist.'),
                CannotUpdateProductException::FAILED_UPDATE_CATEGORIES
            );
        }
    }
}
