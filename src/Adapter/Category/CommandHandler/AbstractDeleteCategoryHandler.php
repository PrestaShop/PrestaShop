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

namespace PrestaShop\PrestaShop\Adapter\Category\CommandHandler;

use Db;
use PrestaShop\PrestaShop\Adapter\Category\Repository\CategoryRepository;
use PrestaShop\PrestaShop\Adapter\Product\Repository\ProductRepository;
use PrestaShop\PrestaShop\Core\Domain\Category\Exception\CategoryNotFoundException;
use PrestaShop\PrestaShop\Core\Domain\Category\ValueObject\CategoryDeleteMode;
use PrestaShop\PrestaShop\Core\Domain\Category\ValueObject\CategoryId;
use PrestaShop\PrestaShop\Core\Domain\Product\ValueObject\ProductId;
use Product;
use Shop;

/**
 * Class AbstractDeleteCategoryHandler.
 */
abstract class AbstractDeleteCategoryHandler
{
    /**
     * @var int
     */
    protected $homeCategoryId;

    /**
     * @var ProductRepository
     */
    private $productRepository;

    /**
     * @var CategoryRepository
     */
    private $categoryRepository;

    /**
     * @param int $homeCategoryId
     * @param ProductRepository $productRepository
     * @param CategoryRepository $categoryRepository
     */
    public function __construct(
        int $homeCategoryId,
        ProductRepository $productRepository,
        CategoryRepository $categoryRepository
    ) {
        $this->homeCategoryId = $homeCategoryId;
        $this->productRepository = $productRepository;
        $this->categoryRepository = $categoryRepository;
    }

    /**
     * @deprecated since 8.1.0 and will be removed in next major version.
     * @see updateProductCategories instead
     *
     * Handle products category after its deletion.
     *
     * @param int $parentCategoryId
     * @param CategoryDeleteMode $mode
     */
    protected function handleProductsUpdate($parentCategoryId, CategoryDeleteMode $mode)
    {
        @trigger_error(
            __FUNCTION__ . 'is deprecated. Use AbstractDeleteCategoryHandler::updateProductCategories instead.',
            E_USER_DEPRECATED
        );

        $productsWithoutCategory = \Db::getInstance()->executeS('
			SELECT p.`id_product`
			FROM `' . _DB_PREFIX_ . 'product` p
			' . Shop::addSqlAssociation('product', 'p') . '
			WHERE NOT EXISTS (
			    SELECT 1 FROM `' . _DB_PREFIX_ . 'category_product` cp WHERE cp.`id_product` = p.`id_product`
			)
		');

        foreach ($productsWithoutCategory as $productWithoutCategory) {
            $product = new Product((int) $productWithoutCategory['id_product']);

            if ($product->id) {
                if (0 === $parentCategoryId || $mode->shouldRemoveProducts()) {
                    $product->delete();

                    continue;
                }

                if ($mode->shouldDisableProducts()) {
                    $product->active = false;
                }

                $product->id_category_default = $parentCategoryId;
                $product->addToCategories($parentCategoryId);
                $product->save();
            }
        }
    }

    /**
     * @param array<int, int[]> $deletedCategoryIdsByParent
     * @param CategoryDeleteMode $mode
     */
    protected function updateProductCategories(array $deletedCategoryIdsByParent, CategoryDeleteMode $mode): void
    {
        $this->updateProductsWithoutCategories($deletedCategoryIdsByParent, $mode);
        $this->updateProductsByDefaultCategories($deletedCategoryIdsByParent);
    }

    /**
     * @param int $categoryId
     * @param array<int, int[]> $deletedCategoryIdsByParent
     *
     * @return int|null
     */
    private function findCategoryParentId(int $categoryId, array $deletedCategoryIdsByParent): ?int
    {
        foreach ($deletedCategoryIdsByParent as $parentId => $deletedIds) {
            if (!in_array($categoryId, $deletedIds, true)) {
                continue;
            }

            try {
                // check if the found parent category exists
                $this->categoryRepository->assertCategoryExists(new CategoryId($parentId));

                return $parentId;
            } catch (CategoryNotFoundException $e) {
                // if category doesn't exist, we could continue trying to find another parent
                // but most of the time this command will be run from BO, which is constructed in a way that
                // all the deleted category ids will have the same parent,
                // so there is no point looping and checking the same category id existence over again
                return null;
            }
        }

        return null;
    }

    /**
     * @param Product $product
     * @param int $categoryId
     * @param array<int, int[]> $deletedCategoryIdsByParent
     */
    private function addProductDefaultCategory(Product $product, int $categoryId, array $deletedCategoryIdsByParent): void
    {
        $parentCategoryId = $this->findCategoryParentId($categoryId, $deletedCategoryIdsByParent) ?: $this->homeCategoryId;
        $product->id_category_default = $parentCategoryId;
        $product->save();

        $product->addToCategories($parentCategoryId);
    }

    /**
     * @param array<int, int[]> $deletedCategoryIdsByParent
     * @param CategoryDeleteMode $mode
     */
    private function updateProductsWithoutCategories(array $deletedCategoryIdsByParent, CategoryDeleteMode $mode): void
    {
        $productIdsWithoutCategories = $this->findProductIdsWithoutCategories();

        foreach ($productIdsWithoutCategories as $productId) {
            $product = $this->productRepository->getProductByDefaultShop($productId);

            if ($mode->shouldRemoveProducts()) {
                $product->delete();

                continue;
            }

            if ($mode->shouldDisableProducts()) {
                $product->active = false;
            }

            $this->addProductDefaultCategory($product, (int) $product->id_category_default, $deletedCategoryIdsByParent);
        }
    }

    /**
     * @param array<int, int[]> $deletedCategoryIdsByParent
     */
    private function updateProductsByDefaultCategories(array $deletedCategoryIdsByParent): void
    {
        $productIds = $this->findProductsByDefaultCategories($deletedCategoryIdsByParent);

        foreach ($productIds as $productId) {
            $product = $this->productRepository->getProductByDefaultShop($productId);
            $this->addProductDefaultCategory($product, (int) $product->id_category_default, $deletedCategoryIdsByParent);
        }
    }

    /**
     * @param array<int, int[]> $deletedCategoryIdsByParent
     *
     * @return ProductId[]
     */
    private function findProductsByDefaultCategories(array $deletedCategoryIdsByParent): array
    {
        $deletedCategoryIds = [];
        foreach ($deletedCategoryIdsByParent as $deletedIds) {
            $deletedCategoryIds = array_merge($deletedCategoryIds, $deletedIds);
        }

        return $this->findProductIdsByDefaultCategories($deletedCategoryIds);
    }

    /**
     * @return ProductId[]
     */
    private function findProductIdsWithoutCategories(): array
    {
        $results = Db::getInstance()->executeS('
			SELECT p.`id_product`
			FROM `' . _DB_PREFIX_ . 'product` p
			' . Shop::addSqlAssociation('product', 'p') . '
			WHERE NOT EXISTS (
			    SELECT 1 FROM `' . _DB_PREFIX_ . 'category_product` cp WHERE cp.`id_product` = p.`id_product`
			)
		');

        return $this->buildProductIdsFromResults($results);
    }

    /**
     * @param int[] $defaultCategoryIds
     *
     * @return ProductId[]
     */
    private function findProductIdsByDefaultCategories(array $defaultCategoryIds): array
    {
        $results = Db::getInstance()->executeS('
			SELECT p.`id_product`
			FROM `' . _DB_PREFIX_ . 'product` p
			' . Shop::addSqlAssociation('product', 'p') . '
			WHERE p.id_category_default IN (' . implode(',', array_map('intval', $defaultCategoryIds)) . ')
		');

        return $this->buildProductIdsFromResults($results);
    }

    /**
     * @param array<int, array<string, mixed>> $results
     *
     * @return ProductId[]
     */
    private function buildProductIdsFromResults(array $results): array
    {
        if (empty($results)) {
            return [];
        }

        return array_map(static function (array $result): ProductId {
            return new ProductId((int) $result['id_product']);
        }, $results);
    }
}
