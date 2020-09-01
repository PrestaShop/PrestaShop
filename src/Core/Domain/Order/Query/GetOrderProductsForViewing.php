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

use PrestaShop\PrestaShop\Core\Domain\Order\Exception\OrderException;
use PrestaShop\PrestaShop\Core\Domain\Order\ValueObject\OrderId;

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
     * @var @var null|int
     */
    private $limit;

    /**
     * Builds query for paginated results
     *
     * @param int $orderId
     * @param int $offset
     * @param int $limit
     *
     * @return GetOrderProductsForViewing
     *
     * @throws OrderException
     */
    public static function paginated(
        int $orderId,
        int $offset,
        int $limit
    ) {
        $query = new self();

        $query->orderId = new OrderId($orderId);
        $query->offset = $offset;
        $query->limit = $limit;

        return $query;
    }

    /**
     * Builds query for getting all results
     *
     * @param int $orderId
     *
     * @return GetOrderProductsForViewing
     *
     * @throws OrderException
     */
    public static function all(int $orderId)
    {
        $query = new self();
        $query->orderId = new OrderId($orderId);

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
}
