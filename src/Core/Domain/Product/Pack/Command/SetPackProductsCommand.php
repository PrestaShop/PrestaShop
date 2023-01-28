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

namespace PrestaShop\PrestaShop\Core\Domain\Product\Pack\Command;

use PrestaShop\PrestaShop\Core\Domain\Product\Pack\Exception\ProductPackConstraintException;
use PrestaShop\PrestaShop\Core\Domain\Product\Pack\ValueObject\PackId;
use PrestaShop\PrestaShop\Core\Domain\Product\QuantifiedProduct;
use RuntimeException;

/**
 * Sets products of product pack
 */
class SetPackProductsCommand
{
    /**
     * @var PackId
     */
    private $packId;

    /**
     * @var QuantifiedProduct[]
     */
    private $products;

    /**
     * @param int $packId
     * @param array $products array of elements where each element contains product information
     *                        which allows building @var QuantifiedProduct
     */
    public function __construct(int $packId, array $products)
    {
        $this->packId = new PackId($packId);
        $this->setProducts($products);
    }

    /**
     * @return PackId
     */
    public function getPackId(): PackId
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
            throw new RuntimeException(sprintf(
                'Empty products array provided in %s. Use %s to remove all pack products',
                static::class,
                RemoveAllProductsFromPackCommand::class
            ));
        }

        foreach ($products as $product) {
            $this->assertQuantity((int) $product['quantity']);
            $this->products[] = new QuantifiedProduct(
                (int) $product['product_id'],
                (int) $product['quantity'],
                isset($product['combination_id']) ? (int) $product['combination_id'] : null
            );
        }
    }

    /**
     * @param int $quantity
     *
     * @throws ProductPackConstraintException
     */
    private function assertQuantity(int $quantity): void
    {
        if ($quantity < 0) {
            throw new ProductPackConstraintException(
                sprintf('Pack product quantity cannot be negative. Got "%s"', $quantity),
                ProductPackConstraintException::INVALID_QUANTITY
            );
        }
    }
}
