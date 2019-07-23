<?php

namespace PrestaShop\PrestaShop\Core\Domain\Product\ValueObject;

use PrestaShop\PrestaShop\Core\Domain\Category\Exception\CategoryException;
use PrestaShop\PrestaShop\Core\Domain\Category\ValueObject\CategoryId;

class Category
{
    /**
     * @var bool
     */
    private $isMainCategory;

    /**
     * @var CategoryId
     */
    private $categoryId;

    /**
     * @param int $categoryId
     * @param bool $isMainCategory
     *
     * @throws CategoryException
     */
    public function __construct(int $categoryId, bool $isMainCategory)
    {
        $this->categoryId = new CategoryId($categoryId);
        $this->isMainCategory = $isMainCategory;
    }

    /**
     * @return bool
     */
    public function isMainCategory(): bool
    {
        return $this->isMainCategory;
    }

    /**
     * @return CategoryId
     */
    public function getCategoryId(): CategoryId
    {
        return $this->categoryId;
    }
}
