<?php
/**
 * 2007-2019 PrestaShop SA and Contributors
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

namespace PrestaShop\PrestaShop\Adapter\Order\CommandHandler;

use Address;
use Cart;
use PrestaShop\PrestaShop\Adapter\Order\AbstractOrderHandler;
use PrestaShop\PrestaShop\Core\Domain\Order\Command\ChangeOrderDeliveryAddressCommand;
use PrestaShop\PrestaShop\Core\Domain\Order\CommandHandler\ChangeOrderDeliveryAddressHandlerInterface;
use PrestaShop\PrestaShop\Core\Domain\Order\Exception\OrderException;
use Validate;

/**
 * @internal
 */
final class ChangeOrderDeliveryAddressHandler extends AbstractOrderHandler implements ChangeOrderDeliveryAddressHandlerInterface
{
    /**
     * {@inheritdoc}
     */
    public function handle(ChangeOrderDeliveryAddressCommand $command)
    {
        $order = $this->getOrderObject($command->getOrderId());
        $address = new Address($command->getNewDeliveryAddressId()->getValue());

        $cart = Cart::getCartByOrderId($order->id);

        if (!Validate::isLoadedObject($address)) {
            throw new OrderException('New delivery address is not valid');
        }

        $order->id_address_delivery = $address->id;
        $cart->id_address_delivery = $address->id;

        $order->update();
        $order->refreshShippingCost();
        $cart->update();
    }
}
