<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Core\Domain\Order\Query;

use PrestaShop\PrestaShop\Core\Domain\Exception\InvalidSortingException;
use PrestaShop\PrestaShop\Core\Domain\Order\Exception\OrderException;
use PrestaShop\PrestaShop\Core\Domain\Order\ValueObject\OrderId;
use PrestaShop\PrestaShop\Core\Domain\ValueObject\QuerySorting;

/**
 * Get order for view in Back Office
 */
class GetOrderForViewing
{
    /**
     * @var OrderId
     */
    private $orderId;

    /**
     * @var QuerySorting
     */
    private $productsSorting;

    /**
     * @param int $orderId
     * @param string $productsSorting
     *
     * @throws OrderException
     * @throws InvalidSortingException
     */
    public function __construct(int $orderId, string $productsSorting = QuerySorting::ASC)
    {
        $this->orderId = new OrderId($orderId);
        $this->productsSorting = new QuerySorting($productsSorting);
    }

    /**
     * @return OrderId
     */
    public function getOrderId(): OrderId
    {
        return $this->orderId;
    }

    /**
     * @return QuerySorting
     */
    public function getProductsSorting(): QuerySorting
    {
        return $this->productsSorting;
    }
}
