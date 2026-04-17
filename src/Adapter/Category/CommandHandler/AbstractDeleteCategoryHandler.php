<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
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
            } catch (CategoryNotFoundException) {
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
