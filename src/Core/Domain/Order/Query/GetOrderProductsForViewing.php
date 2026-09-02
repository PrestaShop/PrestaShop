<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */
declare(strict_types=1);

namespace PrestaShop\PrestaShop\Core\Domain\Order\Query;

use PrestaShop\PrestaShop\Core\Domain\Exception\InvalidSortingException;
use PrestaShop\PrestaShop\Core\Domain\Order\Exception\OrderException;
use PrestaShop\PrestaShop\Core\Domain\Order\ValueObject\OrderId;
use PrestaShop\PrestaShop\Core\Domain\ValueObject\QuerySorting;

/**
 * Query for paginated order products
 */
class GetOrderProductsForViewing
{
    /**
     * @var OrderId
     */
    private $orderId;

    /**
     * @var int|null
     */
    private $offset;

    /**
     * @var int|null
     */
    private $limit;

    /**
     * @var QuerySorting
     */
    private $productsSorting;

    /**
     * Builds query for paginated results
     *
     * @param int $orderId
     * @param int $offset
     * @param int $limit
     * @param string $productsSorting
     *
     * @return GetOrderProductsForViewing
     *
     * @throws OrderException
     * @throws InvalidSortingException
     */
    public static function paginated(
        int $orderId,
        int $offset,
        int $limit,
        string $productsSorting = QuerySorting::ASC
    ) {
        $query = new self();

        $query->orderId = new OrderId($orderId);
        $query->productsSorting = new QuerySorting($productsSorting);
        $query->offset = $offset;
        $query->limit = $limit;

        return $query;
    }

    /**
     * Builds query for getting all results
     *
     * @param int $orderId
     * @param string $productsSorting
     *
     * @return GetOrderProductsForViewing
     *
     * @throws OrderException
     * @throws InvalidSortingException
     */
    public static function all(int $orderId, string $productsSorting = QuerySorting::ASC)
    {
        $query = new self();
        $query->orderId = new OrderId($orderId);
        $query->productsSorting = new QuerySorting($productsSorting);

        return $query;
    }

    /**
     * @return OrderId
     */
    public function getOrderId(): OrderId
    {
        return $this->orderId;
    }

    /**
     * @return int|null
     */
    public function getOffset(): ?int
    {
        return $this->offset;
    }

    /**
     * @return int|null
     */
    public function getLimit(): ?int
    {
        return $this->limit;
    }

    /**
     * @return QuerySorting
     */
    public function getProductsSorting(): QuerySorting
    {
        return $this->productsSorting;
    }
}
