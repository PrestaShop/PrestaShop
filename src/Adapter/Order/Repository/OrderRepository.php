<?php
/**
 * 2007-2020 PrestaShop SA and Contributors
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
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
 * needs please refer to https://www.prestashop.com for more information.
 *
 * @author    PrestaShop SA <contact@prestashop.com>
 * @copyright 2007-2020 PrestaShop SA and Contributors
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 * International Registered Trademark & Property of PrestaShop SA
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Adapter\Order\Repository;

use Order;
use PrestaShop\PrestaShop\Adapter\AbstractObjectModelRepository;
use PrestaShop\PrestaShop\Core\Domain\Order\Exception\OrderNotFoundException;
use PrestaShop\PrestaShop\Core\Domain\Order\ValueObject\OrderId;
use PrestaShop\PrestaShop\Core\Exception\CoreException;
use PrestaShopException;

class OrderRepository extends AbstractObjectModelRepository
{
    /**
     * Gets legacy Order
     *
     * @param OrderId $orderId
     *
     * @return Order
     *
     * @throws CoreException
     */
    public function get(OrderId $orderId): Order
    {
        try {
            $order = new Order($orderId->getValue());

            if ($order->id !== $orderId->getValue()) {
                throw new OrderNotFoundException($orderId, sprintf('%s #%d was not found', Order::class, $orderId->getValue()));
            }
        } catch (PrestaShopException $e) {
            throw new CoreException(
                sprintf(
                    'Error occurred when trying to get %s #%d [%s]',
                    Order::class,
                    $orderId->getValue(),
                    $e->getMessage()
                ),
                0,
                $e
            );
        }

        return $order;
    }
}
