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

use Category;
use PrestaShop\PrestaShop\Adapter\Product\Repository\ProductRepository;
use PrestaShop\PrestaShop\Core\Domain\Category\ValueObject\CategoryId;
use PrestaShop\PrestaShop\Core\Domain\Product\Exception\CannotUpdateProductException;
use PrestaShop\PrestaShop\Core\Exception\CoreException;
use PrestaShopException;
use Product;

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
     * @param ProductRepository $productRepository
     */
    public function __construct(
        ProductRepository $productRepository
    ) {
        $this->productRepository = $productRepository;
    }

    /**
     * @param Product $product
     * @param CategoryId[] $categoryIds
     * @param CategoryId $defaultCategoryId
     *
     * Warning: $categoryIds will replace current categories, erasing previous data
     *
     * @throws CannotUpdateProductException
     * @throws CoreException
     */
    public function updateCategories(Product $product, array $categoryIds, CategoryId $defaultCategoryId): void
    {
        $categoryIds = $this->formatCategoryIdsList($categoryIds, $defaultCategoryId);

        try {
            $this->assertCategoriesExists($categoryIds);

            if (false === $product->updateCategories($categoryIds)) {
                throw new CannotUpdateProductException(
                    sprintf('Failed to update product #%d categories', $product->id),
                    CannotUpdateProductException::FAILED_UPDATE_CATEGORIES
                );
            }
            $this->updateDefaultCategory($product, $defaultCategoryId);
        } catch (PrestaShopException $e) {
            throw new CoreException(
                sprintf('Error occurred when trying to update product #%d categories', $product->id),
                0,
                $e
            );
        }
    }

    /**
     * @param Product $product
     * @param CategoryId $defaultCategoryId
     */
    private function updateDefaultCategory(Product $product, CategoryId $defaultCategoryId): void
    {
        $categoryId = $defaultCategoryId->getValue();

        $this->assertCategoriesExists([$categoryId]);
        $product->id_category_default = $categoryId;

        $this->productRepository->partialUpdate(
            $product,
            ['id_category_default'],
            CannotUpdateProductException::FAILED_UPDATE_DEFAULT_CATEGORY
        );
    }

    /**
     * Re-map array to contain scalar values instead of object,
     * append default category id to the list
     * and filter-out duplicate values
     *
     * @param CategoryId[] $categoryIds
     * @param CategoryId $defaultCategoryId
     *
     * @return int[]
     */
    private function formatCategoryIdsList(array $categoryIds, CategoryId $defaultCategoryId): array
    {
        $categoryIds = array_map(function (CategoryId $categoryId) {
            return $categoryId->getValue();
        }, $categoryIds);

        $categoryIds[] = $defaultCategoryId->getValue();
        $categoryIds = array_unique($categoryIds, SORT_REGULAR);

        return $categoryIds;
    }

    /**
     * @param int[] $categoryIds
     *
     * @throws CannotUpdateProductException|CoreException
     */
    private function assertCategoriesExists(array $categoryIds): void
    {
        try {
            if (!Category::categoriesExists($categoryIds)) {
                throw new CannotUpdateProductException(
                    sprintf('Failed to update product categories. Some of categories doesn\'t exist.'),
                    CannotUpdateProductException::FAILED_UPDATE_CATEGORIES
                );
            }
        } catch (PrestaShopException $e) {
            throw new CoreException('Error occurred when trying to assert categories existence');
        }
    }
}
