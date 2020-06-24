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

namespace PrestaShop\PrestaShop\Core\Domain\Product\Command;

use PrestaShop\PrestaShop\Core\Domain\Product\Exception\ProductPackException;
use PrestaShop\PrestaShop\Core\Domain\Product\QuantifiedProduct;
use PrestaShop\PrestaShop\Core\Domain\Product\ValueObject\ProductId;

/**
 * Updates pack of products
 */
class UpdateProductPackCommand
{
    /**
     * @var ProductId
     */
    private $packId;

    /**
     * @var QuantifiedProduct[]
     */
    private $products;

    /**
     * @param int $packId the id of product which becomes the pack after it contains packed items
     * @param array $products array of elements where each element contains product information
     *                        which allows building @var QuantifiedProduct
     */
    public function __construct(int $packId, array $products)
    {
        $this->packId = new ProductId($packId);
        $this->setProducts($products);
    }

    /**
     * @return ProductId
     */
    public function getPackId(): ProductId
    {
        return $this->packId;
    }

    /**
     * @return QuantifiedProduct[]
     */
    public function getProducts(): array
    {
        return $this->products;
    }

    /**
     * @param array $products
     */
    private function setProducts(array $products): void
    {
        if (empty($products)) {
            throw new ProductPackException(
                'Empty products list provided for packing',
                ProductPackException::EMPTY_PRODUCT_LIST_FOR_PACKING
            );
        }

        foreach ($products as $product) {
            $this->assertQuantity($product['quantity']);

            $this->products[] = new QuantifiedProduct(
                $product['product_id'],
                $product['quantity'],
                isset($product['combination_id']) ? $product['combination_id'] : null
            );
        }
    }

    /**
     * @param int $quantity
     *
     * @throws ProductPackException
     */
    private function assertQuantity(int $quantity): void
    {
        if ($quantity < 0) {
            throw new ProductPackException(
                sprintf('Pack product quantity cannot be negative. Got "%s"', $quantity),
            ProductPackException::INVALID_QUANTITY
            );
        }
    }
}
