<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Core\Domain\Product\Stock\Query;

use PrestaShop\PrestaShop\Core\Domain\Product\ValueObject\ProductId;
use PrestaShop\PrestaShop\Core\Domain\Shop\ValueObject\ShopId;
use PrestaShop\PrestaShop\Core\Exception\InvalidArgumentException;

/**
 * This query returns a list of stock movements for a product, each row is either
 * an edition from the BO by an employee or a range of customer orders resume (all the
 * products that were sold between each edition).
 */
class GetProductStockMovements
{
    public const DEFAULT_LIMIT = 5;

    /**
     * @var ShopId
     */
    private $shopId;

    /**
     * @var int
     */
    private $offset;

    /**
     * @var int
     */
    private $limit;

    /**
     * @var ProductId
     */
    private $productId;

    public function __construct(
        int $productId,
        int $shopId,
        int $offset = 0,
        int $limit = self::DEFAULT_LIMIT
    ) {
        $this->shopId = new ShopId($shopId);

        if ($offset < 0) {
            throw new InvalidArgumentException('Offset should be a positive integer');
        }
        $this->offset = $offset;

        if ($limit < 0) {
            throw new InvalidArgumentException('Limit should be a positive integer');
        }
        $this->limit = $limit;
        $this->productId = new ProductId($productId);
    }

    public function getShopId(): ShopId
    {
        return $this->shopId;
    }

    public function getOffset(): int
    {
        return $this->offset;
    }

    public function getLimit(): int
    {
        return $this->limit;
    }

    public function getProductId(): ProductId
    {
        return $this->productId;
    }
}
