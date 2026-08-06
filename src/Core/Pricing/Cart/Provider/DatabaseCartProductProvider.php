<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Core\Pricing\Cart\Provider;

use Doctrine\DBAL\Connection;

/**
 * Reads cart product lines from the ps_cart_product table.
 *
 * @experimental
 */
class DatabaseCartProductProvider implements CartProductProviderInterface
{
    public function __construct(
        protected readonly Connection $connection,
        protected readonly string $dbPrefix,
    ) {
    }

    /**
     * @return CartProduct[]
     */
    public function getCartProducts(int $cartId): array
    {
        $sql = 'SELECT cp.id_product, cp.id_product_attribute, cp.id_customization, cp.quantity'
            . ' FROM ' . $this->dbPrefix . 'cart_product cp'
            . ' WHERE cp.id_cart = :cartId';

        $rows = $this->connection->fetchAllAssociative($sql, ['cartId' => $cartId]);

        $products = [];
        foreach ($rows as $row) {
            $products[] = new CartProduct(
                (int) $row['id_product'],
                (int) $row['id_product_attribute'],
                (int) $row['id_customization'],
                (int) $row['quantity'],
            );
        }

        return $products;
    }
}
