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

namespace PrestaShop\PrestaShop\Adapter\Product;

use ObjectModel;
use PrestaShop\PrestaShop\Core\Domain\Product\Exception\CannotUpdateProductException;
use PrestaShop\PrestaShop\Core\Domain\Product\Exception\ProductConstraintException;
use PrestaShop\PrestaShop\Core\Domain\Product\Exception\ProductException;
use PrestaShop\PrestaShop\Core\Domain\Product\Exception\ProductNotFoundException;
use PrestaShop\PrestaShop\Core\Domain\Product\ValueObject\ProductId;
use PrestaShopException;
use Product;

/**
 * Provides reusable methods for product handlers
 */
abstract class AbstractProductHandler
{
    /**
     * @var array specific product fields which needs to be updated.
     *
     * This is necessary because product is not fully loaded from database by default
     * So during partial update we don't want to accidentally reset some fields
     */
    protected $fieldsToUpdate = [];

    /**
     * @todo: to be removed when all usecases are replaced by ObjectModelProvider
     *
     * @param ProductId $productId
     *
     * @return Product
     *
     * @throws ProductException
     * @throws ProductNotFoundException
     */
    protected function getProduct(ProductId $productId): Product
    {
        $productIdValue = $productId->getValue();

        try {
            $product = new Product($productIdValue);

            if ((int) $product->id !== $productIdValue) {
                throw new ProductNotFoundException(sprintf(
                    'Product #%d was not found',
                    $productIdValue
                ));
            }
        } catch (PrestaShopException $e) {
            throw new ProductException(
                sprintf('Error occurred when trying to get product #%d', $productId),
                0,
                $e
            );
        }

        return $product;
    }

    /**
     * @todo: product name is not required. Fix that? issue: #19441 for discussion
     * @todo: to be removed when all usecases are replaced by ObjectModelPersister & validator
     * Validates Product object model multilingual property using legacy validation
     *
     * @param Product $product
     * @param string $field
     * @param int $errorCode
     *
     * @throws ProductConstraintException
     */
    protected function validateLocalizedField(Product $product, string $field, int $errorCode): void
    {
        foreach ($product->{$field} as $langId => $value) {
            if (true !== $product->validateField($field, $value, $langId)) {
                throw new ProductConstraintException(
                    sprintf(
                        'Invalid localized product %d for language with id "%d"',
                        $field,
                        $langId
                    ),
                    $errorCode
                );
            }
        }
    }

    /**
     * @todo: to be removed when all usecases are replaced by ObjectModelPersister & validator
     * Validates Product object model property using legacy validation
     *
     * @param Product $product
     * @param string $field
     * @param int $errorCode
     *
     * @throws ProductConstraintException
     */
    protected function validateField(Product $product, string $field, int $errorCode): void
    {
        if (true !== $product->validateField($field, $product->{$field})) {
            throw new ProductConstraintException(
                sprintf(
                    'Invalid product %s. Got "%s"',
                    $field,
                    $product->{$field}
                ),
                $errorCode
            );
        }
    }

    /**
     * @todo: to be removed when all usecases are replaced by ObjectModelPersister & validator
     *
     * @param Product $product
     * @param int $errorCode
     *
     * @throws CannotUpdateProductException
     * @throws ProductException
     */
    protected function performUpdate(Product $product, int $errorCode): void
    {
        try {
            if (false === $product->update()) {
                throw new CannotUpdateProductException(
                    sprintf('Failed to update product #%d', $product->id),
                    $errorCode
                );
            }
        } catch (PrestaShopException $e) {
            throw new ProductException(
                sprintf('Error occurred when trying to update product #%d', $product->id),
                0,
                $e
            );
        }
    }

    /**
     * @todo: to be removes when all usecases are replaced by ObjectModelProvider->assertExists()
     *
     * @param string $entityName
     * @param int $entityId
     *
     * @return bool
     *
     * @throws ProductException
     */
    protected function entityExists(string $entityName, int $entityId): bool
    {
        try {
            return ObjectModel::existsInDatabase($entityId, $entityName);
        } catch (PrestaShopException $e) {
            throw new ProductException(
                sprintf('Error occurred when trying to find %s with id %d', $entityName, $entityId),
                0,
                $e
            );
        }
    }
}
