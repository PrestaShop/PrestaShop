<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Core\Domain\Product\Command;

use PrestaShop\PrestaShop\Core\Domain\Category\ValueObject\CategoryId;
use PrestaShop\PrestaShop\Core\Domain\Product\ValueObject\ProductId;
use PrestaShop\PrestaShop\Core\Domain\Shop\ValueObject\ShopConstraint;
use RuntimeException;

/**
 * Sets new product-category associations
 */
class SetAssociatedProductCategoriesCommand
{
    /**
     * @var ProductId
     */
    private $productId;

    /**
     * @var CategoryId
     */
    private $defaultCategoryId;

    /**
     * @var CategoryId[]
     */
    private $categoryIds;

    /**
     * @var ShopConstraint
     */
    private $shopConstraint;

    /**
     * @param int $productId
     * @param int $defaultCategoryId
     * @param int[] $categoryIds
     */
    public function __construct(
        int $productId,
        int $defaultCategoryId,
        array $categoryIds,
        ShopConstraint $shopConstraint
    ) {
        $this->setCategoryIds($categoryIds);
        $this->defaultCategoryId = new CategoryId($defaultCategoryId);
        $this->productId = new ProductId($productId);
        $this->shopConstraint = $shopConstraint;
    }

    /**
     * @return CategoryId
     */
    public function getDefaultCategoryId(): CategoryId
    {
        return $this->defaultCategoryId;
    }

    /**
     * @return ProductId
     */
    public function getProductId(): ProductId
    {
        return $this->productId;
    }

    /**
     * @return CategoryId[]
     */
    public function getCategoryIds(): array
    {
        return $this->categoryIds;
    }

    /**
     * @return ShopConstraint
     */
    public function getShopConstraint(): ShopConstraint
    {
        return $this->shopConstraint;
    }

    /**
     * @param int[] $categoryIds
     */
    private function setCategoryIds(array $categoryIds): void
    {
        $this->assertCategoryIdsAreNotEmpty($categoryIds);

        $this->categoryIds = array_map(
            function ($id) {
                return new CategoryId($id);
            }, $categoryIds
        );
    }

    /**
     * @param int[] $categoryIds
     */
    private function assertCategoryIdsAreNotEmpty(array $categoryIds): void
    {
        if (empty($categoryIds)) {
            throw new RuntimeException(sprintf(
                'Empty categoryIds provided in %s. To remove categories use %s.',
                self::class,
                RemoveAllAssociatedProductCategoriesCommand::class
            ));
        }
    }
}
