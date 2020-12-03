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
