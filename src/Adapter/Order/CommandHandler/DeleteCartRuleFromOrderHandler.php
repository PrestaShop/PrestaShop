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

namespace PrestaShop\PrestaShop\Adapter\Order\CommandHandler;

use Cart;
use CartRule;
use OrderCartRule;
use PrestaShop\PrestaShop\Adapter\Order\AbstractOrderHandler;
use PrestaShop\PrestaShop\Adapter\Order\OrderAmountUpdater;
use PrestaShop\PrestaShop\Core\Domain\Order\Command\DeleteCartRuleFromOrderCommand;
use PrestaShop\PrestaShop\Core\Domain\Order\CommandHandler\DeleteCartRuleFromOrderHandlerInterface;
use PrestaShop\PrestaShop\Core\Domain\Order\Exception\OrderException;
use Validate;

/**
 * @internal
 */
final class DeleteCartRuleFromOrderHandler extends AbstractOrderHandler implements DeleteCartRuleFromOrderHandlerInterface
{
    /**
     * @var OrderAmountUpdater
     */
    private $orderAmountUpdater;

    /**
     * @param OrderAmountUpdater $orderAmountUpdater
     */
    public function __construct(OrderAmountUpdater $orderAmountUpdater)
    {
        $this->orderAmountUpdater = $orderAmountUpdater;
    }

    /**
     * {@inheritdoc}
     */
    public function handle(DeleteCartRuleFromOrderCommand $command)
    {
        $order = $this->getOrder($command->getOrderId());
        $orderCartRule = new OrderCartRule($command->getOrderCartRuleId());
        if (!Validate::isLoadedObject($orderCartRule) || $orderCartRule->id_order != $order->id) {
            throw new OrderException('Invalid order cart rule provided.');
        }

        $cart = Cart::getCartByOrderId($order->id);
        if (!Validate::isLoadedObject($cart) || $order->id_cart != $cart->id) {
            throw new OrderException('Invalid cart provided.');
        }

        $cartRule = new CartRule($orderCartRule->id_cart_rule);
        if (!Validate::isLoadedObject($cartRule)) {
            throw new OrderException('Invalid cart rule provided.');
        }

        // Delete Order Cart Rule and update Order
        $orderCartRule->delete();
        $cart->removeCartRule($orderCartRule->id_cart_rule);

        // We udpate the order nad its cart rules, but we disble automatic add cart rule
        // to avoid adding the on that was just added
        $this->orderAmountUpdater->update($order, $cart, false, false);
    }
}
