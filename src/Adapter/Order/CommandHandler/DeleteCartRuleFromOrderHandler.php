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
use Currency;
use Customer;
use Exception;
use Order;
use OrderCartRule;
use PrestaShop\PrestaShop\Adapter\ContextStateManager;
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
     * @var ContextStateManager
     */
    private $contextStateManager;

    /**
     * @param OrderAmountUpdater $orderAmountUpdater
     */
    public function __construct(OrderAmountUpdater $orderAmountUpdater, ContextStateManager $contextStateManager)
    {
        $this->orderAmountUpdater = $orderAmountUpdater;
        $this->contextStateManager = $contextStateManager;
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

        $this->contextStateManager
            ->setCurrency(new Currency($order->id_currency))
            ->setCustomer(new Customer($order->id_customer));

        try {
            $this->deleteCartRuleAndUpdateOrder($orderCartRule, $cart, $order);
        } catch (Exception $exception) {
            $this->contextStateManager->restoreContext();

            throw $exception;
        }
    }

    /**
     * @param OrderCartRule $orderCartRule
     * @param Cart $cart
     * @param Order $order
     *
     * @throws OrderException
     * @throws \PrestaShopDatabaseException
     * @throws \PrestaShopException
     */
    private function deleteCartRuleAndUpdateOrder(OrderCartRule $orderCartRule, Cart $cart, Order $order): void
    {
        // Delete Order Cart Rule and update Order
        $orderCartRule->softDelete();
        $cart->removeCartRule($orderCartRule->id_cart_rule);

        $this->orderAmountUpdater->update($order, $cart, $orderCartRule->id_order_invoice);
    }
}
