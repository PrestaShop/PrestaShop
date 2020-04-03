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

namespace PrestaShop\PrestaShop\Adapter\Product;

use PrestaShop\PrestaShop\Core\Domain\Product\Exception\ProductException;
use PrestaShop\PrestaShop\Core\Domain\Product\Exception\ProductNotFoundException;
use PrestaShop\PrestaShop\Core\Domain\Product\ValueObject\ProductId;
use PrestaShopException;
use Product;

/**
 * Contains reusable methods for product query/command handlers
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
        } catch (PrestaShopException $e) {
            throw new ProductException(
                sprintf('An error occurred trying to load product #%s', $productIdValue),
                0,
                $e
            );
        }

        if ((int) $product->id !== $productIdValue) {
            throw new ProductNotFoundException(sprintf(
                'Could not find product #%s',
                $productIdValue
            ));
        }

        return $product;
    }
}
