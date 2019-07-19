<?php
/**
 * 2007-2019 PrestaShop and Contributors
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
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
 * needs please refer to https://www.prestashop.com for more information.
 *
 * @author    PrestaShop SA <contact@prestashop.com>
 * @copyright 2007-2019 PrestaShop SA and Contributors
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 * International Registered Trademark & Property of PrestaShop SA
 */

namespace PrestaShop\PrestaShop\Core\Domain\Product\Command;

use PrestaShop\PrestaShop\Core\Domain\Category\Exception\CategoryConstraintException;
use PrestaShop\PrestaShop\Core\Domain\Product\Exception\ProductConstraintException;

/**
 * Class AddCategoryToProductCommand adds a product to a category.
 */
class AddCategoryToProductCommand
{
    /**
     * @var int
     */
    private $categoryId;

    /**
     * @var int
     */
    private $productId;

    /**
     * @param int $categoryId
     * @param int $productId
     *
     * @throws CategoryConstraintException
     * @throws ProductConstraintException
     */
    public function __construct($categoryId, $productId)
    {
        $this->setCategoryId($categoryId);
        $this->setProductId($productId);
    }

    /**
     * @param int $categoryId
     *
     * @return self
     *
     * @throws CategoryConstraintException
     */
    public function setCategoryId(int $categoryId)
    {
        if (!is_int($categoryId) || 0 >= $categoryId) {
            throw new CategoryConstraintException(
                sprintf('Invalid Category id %s supplied', var_export($categoryId, true)),
                CategoryConstraintException::INVALID_ID
            );
        }

        $this->categoryId = $categoryId;

        return $this;
    }

    /**
     * @return int
     */
    public function getCategoryId()
    {
        return $this->categoryId;
    }

    /**
     * @param int $productId
     *
     * @return self
     *
     * @throws ProductConstraintException
     */
    public function setProductId(int $productId)
    {
        if (!is_int($productId) || 0 >= $productId) {
            throw new ProductConstraintException(
                sprintf('Invalid Product id %s supplied', var_export($productId, true)),
                ProductConstraintException::INVALID_PRODUCT_ID
            );
        }

        $this->productId = $productId;

        return $this;
    }

    /**
     * @return int
     */
    public function getProductId()
    {
        return $this->productId;
    }
}
