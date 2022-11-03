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
 * needs please refer to https://devdocs.prestashop-project.org/ for more information.
 *
 * @author    PrestaShop SA and Contributors <contact@prestashop.com>
 * @copyright Since 2007 PrestaShop SA and Contributors
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 */

namespace PrestaShop\PrestaShop\Core\Domain\Order\Command;

use PrestaShop\PrestaShop\Core\Domain\Order\Exception\OrderException;
use PrestaShop\PrestaShop\Core\Domain\Order\ValueObject\OrderId;

/**
 * Changes status for given orders.
 */
class BulkChangeOrderStatusCommand
{
    /**
     * @var OrderId[]
     */
    private $orderIds;

    /**
     * @var int
     */
    private $newOrderStatusId;

    /**
     * @param int[] $orderIds
     * @param int $newOrderStatusId
     */
    public function __construct(array $orderIds, $newOrderStatusId)
    {
        if (!is_int($newOrderStatusId) || 0 >= $newOrderStatusId) {
            throw new OrderException(sprintf('Order status Id must be integer greater than 0, but %s given.', var_export($newOrderStatusId, true)));
        }

        $this->newOrderStatusId = $newOrderStatusId;
        $this->setOrderIds($orderIds);
    }

    /**
     * @return OrderId[]
     */
    public function getOrderIds()
    {
        return $this->orderIds;
    }

    /**
     * @return int
     */
    public function getNewOrderStatusId()
    {
        return $this->newOrderStatusId;
    }

    /**
     * @param int[] $orderIds
     */
    private function setOrderIds(array $orderIds)
    {
        foreach ($orderIds as $orderId) {
            $this->orderIds[] = new OrderId($orderId);
        }
    }
}
