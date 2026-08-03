<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Core\Import\Engine\Repository;

use Doctrine\DBAL\Connection;

/**
 * @internal only meant for internal use by the Import engine components,
 *           not to be overridden or decorated
 */
final class ProductLookup
{
    public function __construct(
        private readonly Connection $connection,
        private readonly string $dbPrefix,
    ) {
    }

    /**
     * Shop-scoped reference lookup — the unified match_ref lookup (legacy used
     * two divergent helpers, see the behavior inventory in the Import plan).
     */
    public function getProductIdByReference(string $reference, int $shopId): ?int
    {
        $productId = $this->connection->fetchOne(
            'SELECT p.id_product
            FROM ' . $this->dbPrefix . 'product p
            INNER JOIN ' . $this->dbPrefix . 'product_shop ps
                ON ps.id_product = p.id_product AND ps.id_shop = :shopId
            WHERE p.reference = :reference
            ORDER BY p.id_product ASC',
            ['reference' => $reference, 'shopId' => $shopId]
        );

        return false === $productId ? null : (int) $productId;
    }

    public function productExists(int $productId): bool
    {
        return false !== $this->connection->fetchOne(
            'SELECT 1 FROM ' . $this->dbPrefix . 'product WHERE id_product = :productId',
            ['productId' => $productId]
        );
    }

    /**
     * Current physical quantity of the product itself (no combination), or
     * null when no stock row exists. Checks the shop-scoped row first, then
     * the group-shared row (id_shop = 0).
     */
    public function getStockQuantity(int $productId, int $shopId): ?int
    {
        foreach ([$shopId, 0] as $stockShopId) {
            $quantity = $this->connection->fetchOne(
                'SELECT quantity FROM ' . $this->dbPrefix . 'stock_available
                WHERE id_product = :productId AND id_product_attribute = 0 AND id_shop = :shopId',
                ['productId' => $productId, 'shopId' => $stockShopId]
            );
            if (false !== $quantity) {
                return (int) $quantity;
            }
        }

        return null;
    }
}
