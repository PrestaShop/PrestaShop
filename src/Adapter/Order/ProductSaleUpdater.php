<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Adapter\Order;

use Doctrine\DBAL\Connection;

/**
 * @internal
 */
final class ProductSaleUpdater
{
    public function __construct(
        private readonly Connection $connection,
        private readonly string $dbPrefix,
    ) {
    }

    public function updateQuantity(int $productId, int $quantityDifference): void
    {
        if (0 === $quantityDifference) {
            return;
        }

        // Editing a line changes the units sold, not the number of sales.
        $this->connection->createQueryBuilder()
            ->update($this->dbPrefix . 'product_sale')
            ->set('quantity', 'GREATEST(0, CAST(quantity AS SIGNED) + :quantityDifference)')
            ->set('date_upd', 'CURRENT_DATE()')
            ->where('id_product = :productId')
            ->setParameter('quantityDifference', $quantityDifference)
            ->setParameter('productId', $productId)
            ->executeStatement();
    }
}
