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

namespace PrestaShop\PrestaShop\Core\Domain\Product\Category\Command;

use PrestaShop\PrestaShop\Core\Domain\Category\Exception\CategoryException;
use PrestaShop\PrestaShop\Core\Domain\Product\Category\Exception\ProductCategoryConstraintException;
use PrestaShop\PrestaShop\Core\Domain\Product\Category\ValueObject\ProductCategory;
use PrestaShop\PrestaShop\Core\Domain\Product\ValueObject\ProductId;

/**
 * Updates product categories.
 */
class UpdateProductCategoriesAssociationCommand
{
    /**
     * @var ProductId
     */
    private $productId;

    /**
     * @var ProductCategory[]
     */
    private $categories;

    /**
     * @param int $productId
     * @param array $categories
     *
     * @throws CategoryException
     * @throws ProductCategoryConstraintException
     */
    public function __construct(int $productId, array $categories)
    {
        $this->productId = new ProductId($productId);
        $this->setCategories($categories);
    }

    /**
     * @return ProductId
     */
    public function getProductId(): ProductId
    {
        return $this->productId;
    }

    /**
     * @return ProductCategory[]
     */
    public function getCategories(): array
    {
        return $this->categories;
    }

    /**
     * @param array $categories
     *
     * @throws CategoryException
     * @throws ProductCategoryConstraintException
     */
    private function setCategories(array $categories): void
    {
        $mainCategoriesCount = 0;
        foreach ($categories as $category) {
            $isMainCategory = $category['is_main_category'];

            if ($isMainCategory) {
                ++$mainCategoriesCount;
            }

            if ($mainCategoriesCount > 1) {
                throw new ProductCategoryConstraintException(
                    'Cannot have two main categories',
                    ProductCategoryConstraintException::MULTIPLE_MAIN_CATEGORIES
                );
            }

            $this->categories[] = new ProductCategory(
                $category['id'],
                $isMainCategory
            );
        }
    }
}
