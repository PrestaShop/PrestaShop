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
