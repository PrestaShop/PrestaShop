<?php
/**
 * 2007-2019 PrestaShop and Contributors
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
 * @copyright 2007-2019 PrestaShop SA and Contributors
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 * International Registered Trademark & Property of PrestaShop SA
 */

namespace PrestaShop\PrestaShop\Adapter\Order\QueryHandler;

use Customer;
use DateTimeImmutable;
use Gender;
use Order;
use PrestaShop\PrestaShop\Core\Domain\Order\Exception\OrderNotFoundException;
use PrestaShop\PrestaShop\Core\Domain\Order\Query\GetOrderForViewing;
use PrestaShop\PrestaShop\Core\Domain\Order\QueryHandler\GetOrderForViewingHandlerInterface;
use PrestaShop\PrestaShop\Core\Domain\Order\QueryResult\OrderCustomerForViewing;
use PrestaShop\PrestaShop\Core\Domain\Order\QueryResult\OrderForViewing;
use PrestaShop\PrestaShop\Core\Domain\Order\ValueObject\OrderId;
use Tools;
use Validate;

/**
 * Get
 *
 * @internal
 */
final class GetOrderForViewingHandler implements GetOrderForViewingHandlerInterface
{
    /**
     * {@inheritdoc}
     */
    public function handle(GetOrderForViewing $query): OrderForViewing
    {
        $order = $this->getOrder($query->getOrderId());

        return new OrderForViewing(
            $this->getOrderCustomer($order)
        );
    }

    /**
     * @param OrderId $orderId
     *
     * @return Order
     *
     * @throws OrderNotFoundException
     */
    private function getOrder(OrderId $orderId): Order
    {
        $order = new Order($orderId->getValue());

        if ($order->id !== $orderId->getValue()) {
            throw new OrderNotFoundException(sprintf('Order with id "%s" was not found.',$orderId->getValue()));
        }

        return $order;
    }

    /**
     * @param Order $order
     *
     * @return OrderCustomerForViewing
     */
    private function getOrderCustomer(Order $order): OrderCustomerForViewing
    {
        $customer = new Customer($order->id_customer);
        $gender = new Gender($customer->id_gender);
        $genderName = '';

        if (Validate::isLoadedObject($gender)) {
            $genderName = $gender->name[$order->id_lang];
        }

        $customerStats = $customer->getStats();

        return new OrderCustomerForViewing(
            $customer->id,
            $customer->firstname,
            $customer->lastname,
            $genderName,
            $customer->email,
            new DateTimeImmutable($customer->date_add),
            Tools::displayPrice(Tools::ps_round(Tools::convertPrice($customerStats['total_orders'], $order->id_currency), PS_ROUND_HALF_UP), (int) $order->id_currency),
            $customerStats['nb_orders']
        );
    }
}
