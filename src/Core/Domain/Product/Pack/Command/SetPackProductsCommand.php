<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
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
