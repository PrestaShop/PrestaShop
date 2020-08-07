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

namespace PrestaShop\PrestaShop\Adapter\Product\CommandHandler;

use Category;
use PrestaShop\PrestaShop\Adapter\Product\AbstractProductHandler;
use PrestaShop\PrestaShop\Core\Domain\Product\Command\UpdateProductCategoriesCommand;
use PrestaShop\PrestaShop\Core\Domain\Product\CommandHandler\UpdateProductCategoriesHandlerInterface;
use PrestaShop\PrestaShop\Core\Domain\Product\Exception\CannotUpdateProductException;
use PrestaShop\PrestaShop\Core\Domain\Product\Exception\ProductException;
use PrestaShopException;
use Product;

/**
 * Handles @var UpdateProductCategoriesCommand using legacy object model
 */
final class UpdateProductCategoriesHandler extends AbstractProductHandler implements UpdateProductCategoriesHandlerInterface
{
    /**
     * {@inheritdoc}
     */
    public function handle(UpdateProductCategoriesCommand $command): void
    {
        $product = $this->getProduct($command->getProductId());
        $categoryIds = $command->getCategoryIds();
        $defaultCategoryId = $command->getDefaultCategoryId();

        if (empty($categoryIds) && !empty($defaultCategoryId)) {
            $this->updateOnlyDefaultCategory($product, $defaultCategoryId->getValue());

            return;
        }

        if (empty($categoryIds) && empty($defaultCategoryId)) {
            $currentDefaultCategory = $product->id_category_default;

            $this->updateCategories($product, [$currentDefaultCategory]);

            return;
        }

        $categoryIds[] = $defaultCategoryId;
        $categoryIds = array_map(function ($categoryId) {
            return $categoryId->getValue();
        }, $categoryIds);

        $this->updateCategories($product, $categoryIds);
        $this->updateDefaultCategory($product, $defaultCategoryId->getValue());
    }

    /**
     * @param Product $product
     * @param int $defaultCategoryId
     *
     * @throws CannotUpdateProductException
     * @throws ProductException
     */
    private function updateOnlyDefaultCategory(Product $product, int $defaultCategoryId): void
    {
        $currentProductCategories = array_map('intval', $product->getCategories());
        $currentProductCategories[] = $defaultCategoryId;

        $this->updateCategories($product, $currentProductCategories);
        $this->updateDefaultCategory($product, $defaultCategoryId);
    }

    /**
     * @param Product $product
     * @param int $defaultCategoryId
     *
     * @throws CannotUpdateProductException
     * @throws ProductException
     */
    private function updateDefaultCategory(Product $product, int $defaultCategoryId): void
    {
        $product->id_category_default = $defaultCategoryId;
        $product->setFieldsToUpdate(['id_category_default']);

        $this->performUpdate($product, CannotUpdateProductException::FAILED_UPDATE_CATEGORIES);
    }

    /**
     * @param Product $product
     * @param array $categoryIds
     *
     * @throws CannotUpdateProductException
     * @throws ProductException
     */
    private function updateCategories(Product $product, array $categoryIds): void
    {
        $categoryIds = array_unique($categoryIds);

        try {
            $this->assertCategoriesExists($product, $categoryIds);

            if (false === $product->updateCategories($categoryIds)) {
                throw new CannotUpdateProductException(
                    sprintf('Failed to update product #%d categories', $product->id),
                    CannotUpdateProductException::FAILED_UPDATE_CATEGORIES
                );
            }
        } catch (PrestaShopException $e) {
            throw new ProductException(
                sprintf('Error occurred when trying to update product #%d categories', $product->id),
                0,
                $e
            );
        }
    }

    /**
     * @param Product $product
     * @param array $categoryIds
     *
     * @throws CannotUpdateProductException
     */
    private function assertCategoriesExists(Product $product, array $categoryIds): void
    {
        if (false === Category::categoriesExists($categoryIds)) {
            throw new CannotUpdateProductException(
                sprintf('Failed to update product #%d categories. Some of categories doesn\'t exist.', $product->id),
                CannotUpdateProductException::FAILED_UPDATE_CATEGORIES
            );
        }
    }
}
