<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Core\Pricing\Product\Provider;

use Doctrine\DBAL\Connection;
use PrestaShop\Decimal\DecimalNumber;
use PrestaShop\PrestaShop\Core\Pricing\Exception\ProductPriceNotFoundException;

/**
 * Reads raw product pricing data from the catalog tables (ps_product, ps_product_attribute)
 * in a single query. Returns the data as-is with no computation. Used in FO / cart context.
 */
class CatalogProductProvider implements ProductProviderInterface
{
    public function __construct(
        protected readonly Connection $connection,
        protected readonly string $dbPrefix,
    ) {
    }

    public function getProductPriceData(int $productId, int $combinationId): ProductPriceData
    {
        if ($combinationId > 0) {
            return $this->fetchProductWithCombination($productId, $combinationId);
        }

        return $this->fetchProduct($productId);
    }

    /**
     * @throws ProductPriceNotFoundException when the product does not exist
     */
    protected function fetchProduct(int $productId): ProductPriceData
    {
        $sql = 'SELECT p.price, p.unit_price'
            . ' FROM ' . $this->dbPrefix . 'product p'
            . ' WHERE p.id_product = :productId';

        $row = $this->connection->fetchAssociative($sql, ['productId' => $productId]);

        if ($row === false) {
            throw new ProductPriceNotFoundException(sprintf('Product %d not found', $productId));
        }

        return new ProductPriceData(
            new DecimalNumber((string) $row['price']),
            new DecimalNumber((string) $row['unit_price']),
            new DecimalNumber('0'),
            new DecimalNumber('0'),
        );
    }

    /**
     * @throws ProductPriceNotFoundException when the product does not exist
     */
    protected function fetchProductWithCombination(int $productId, int $combinationId): ProductPriceData
    {
        $sql = 'SELECT p.price, p.unit_price, pa.price AS combination_impact, pa.unit_price_impact'
            . ' FROM ' . $this->dbPrefix . 'product p'
            . ' LEFT JOIN ' . $this->dbPrefix . 'product_attribute pa'
            . ' ON pa.id_product = p.id_product AND pa.id_product_attribute = :combinationId'
            . ' WHERE p.id_product = :productId';

        $row = $this->connection->fetchAssociative($sql, [
            'productId' => $productId,
            'combinationId' => $combinationId,
        ]);

        if ($row === false) {
            throw new ProductPriceNotFoundException(sprintf(
                'Product %d with combination %d not found',
                $productId,
                $combinationId
            ));
        }

        return new ProductPriceData(
            new DecimalNumber((string) $row['price']),
            new DecimalNumber((string) $row['unit_price']),
            new DecimalNumber((string) ($row['combination_impact'] ?? '0')),
            new DecimalNumber((string) ($row['unit_price_impact'] ?? '0')),
        );
    }
}
