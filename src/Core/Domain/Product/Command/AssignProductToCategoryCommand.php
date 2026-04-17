<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Core\Domain\Product\Command;

use PrestaShop\PrestaShop\Core\Domain\Category\Exception\CategoryConstraintException;
use PrestaShop\PrestaShop\Core\Domain\Category\ValueObject\CategoryId;
use PrestaShop\PrestaShop\Core\Domain\Product\Exception\ProductConstraintException;
use PrestaShop\PrestaShop\Core\Domain\Product\ValueObject\ProductId;

/**
 * Class AssignProductToCategoryCommand adds a product to a category.
 */
class AssignProductToCategoryCommand
{
    /**
     * @var CategoryId
     */
    private $categoryId;

    /**
     * @var ProductId
     */
    private $productId;

    /**
     * @param int $categoryId
     * @param int $productId
     *
     * @throws CategoryConstraintException
     * @throws ProductConstraintException */
    public function __construct($categoryId, $productId)
    {
        $this->setCategoryId($categoryId);
        $this->setProductId($productId);
    }

    /**
     * @param int $categoryId
     *
     * @return self
     */
    public function setCategoryId(int $categoryId): AssignProductToCategoryCommand
    {
        $this->categoryId = new CategoryId($categoryId);

        return $this;
    }

    /**
     * @return CategoryId
     */
    public function getCategoryId(): CategoryId
    {
        return $this->categoryId;
    }

    /**
     * @param int $productId
     *
     * @return self
     */
    public function setProductId(int $productId): AssignProductToCategoryCommand
    {
        $this->productId = new ProductId($productId);

        return $this;
    }

    /**
     * @return ProductId
     */
    public function getProductId(): ProductId
    {
        return $this->productId;
    }
}
