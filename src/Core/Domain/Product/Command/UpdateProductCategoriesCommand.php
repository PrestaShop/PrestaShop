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

namespace PrestaShop\PrestaShop\Core\Domain\Product\Command;

use LogicException;
use PrestaShop\PrestaShop\Core\Domain\Category\ValueObject\CategoryId;
use PrestaShop\PrestaShop\Core\Domain\Product\ValueObject\ProductId;

class UpdateProductCategoriesCommand
{
    /**
     * @var ProductId
     */
    private $productId;

    /**
     * @var CategoryId|null
     */
    private $defaultCategoryId;

    /**
     * @var CategoryId[]
     */
    private $categoryIds;

    /**
     * Builds command to replace all product existing categories to provided ones
     *
     * @param int $productId
     * @param int $defaultCategoryId
     * @param array $categoryIds
     *
     * @return UpdateProductCategoriesCommand
     */
    public static function replace(int $productId, int $defaultCategoryId, array $categoryIds): self
    {
        if (empty($categoryIds)) {
            throw new LogicException(sprintf(
                'Providing empty array will remove all categories except default. Use %s::deleteAllExceptDefault()',
                self::class
            ));
        }

        return new self($productId, $defaultCategoryId, $categoryIds);
    }

    /**
     * Builds command to delete all product categories except the default one
     *
     * @param int $productId
     *
     * @return UpdateProductCategoriesCommand
     */
    public static function deleteAllExceptDefault(int $productId): self
    {
        return new self($productId);
    }

    /**
     * Builds command to update only default category
     *
     * @param int $productId
     * @param int $defaultCategoryId
     *
     * @return UpdateProductCategoriesCommand
     */
    public static function updateOnlyDefault(int $productId, int $defaultCategoryId): self
    {
        return new self($productId, $defaultCategoryId);
    }

    /**
     * @return CategoryId|null
     */
    public function getDefaultCategoryId(): ?CategoryId
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
     * Use static factories to initiate this class
     *
     * @param int $productId
     * @param int $defaultCategoryId
     * @param int[] $categoryIds
     */
    private function __construct(int $productId, ?int $defaultCategoryId = null, array $categoryIds = [])
    {
        $this->productId = new ProductId($productId);
        $this->defaultCategoryId = null !== $defaultCategoryId ? new CategoryId($defaultCategoryId) : null;

        $this->categoryIds = array_map(
            function ($id) {
                return new CategoryId($id);
            }, $categoryIds
        );
    }
}
