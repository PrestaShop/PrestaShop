<?php
/**
 * 2007-2020 PrestaShop SA and Contributors
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
 * @copyright 2007-2020 PrestaShop SA and Contributors
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 * International Registered Trademark & Property of PrestaShop SA
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Adapter\Product;

use PrestaShop\Decimal\Number;
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
                    'Product #%s was not found',
                    $productIdValue
                ));
            }

            $this->setUnitPrice($product);
        } catch (PrestaShopException $e) {
            throw new ProductException(
                sprintf('Error occurred when trying to get product #%s', $productId),
                0,
                $e
            );
        }

        return $product;
    }

    /**
     * Provides product field as Number instead of float.
     *
     * @param Product $product
     * @param string $field
     *
     * @return Number
     */
    protected function getPropertyAsNumber(Product $product, string $property): Number
    {
        $numericProperties = [
            'price',
            'ecotax',
            'wholesale_price',
            'unit_price',
            'unit_price_ratio',
        ];

        if (!in_array($property, $numericProperties, true)) {
            throw new ProductException(sprintf('Product property "%s" does\'t exist or is not numeric', $property));
        }

        // To make sure all values are safely converted to Number.
        // Because casting null to string results in empty string which isn't valid to create Number and throws error.
        if (null === $product->{$property}) {
            $product->{$property} = 0;
        }

        return new Number((string) $product->{$property});
    }

    /**
     * @todo: product name is not required. Fix that? issue: #19441 for discussion
     *
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
                        'Invalid localized product %s for language with id "%s"',
                        $field,
                        $langId
                    ),
                    $errorCode
                );
            }
        }
    }

    /**
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
     * @param Product $product
     */
    private function setUnitPrice(Product $product)
    {
        $price = $this->getPropertyAsNumber($product, 'price');
        $unitPriceRatio = $this->getPropertyAsNumber($product, 'unit_price_ratio');

        if (!$unitPriceRatio->equals(new Number('0'))) {
            $product->unit_price = (float) (string) $price->dividedBy($unitPriceRatio);
        }
    }
}
