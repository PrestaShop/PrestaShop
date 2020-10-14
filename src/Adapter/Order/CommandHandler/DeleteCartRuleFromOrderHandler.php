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
use Currency;
use Customer;
use Order;
use OrderCartRule;
use OrderDetail;
use OrderInvoice;
use PrestaShop\PrestaShop\Adapter\ContextStateManager;
use PrestaShop\PrestaShop\Adapter\Order\AbstractOrderHandler;
use PrestaShop\PrestaShop\Adapter\Order\OrderAmountUpdater;
use PrestaShop\PrestaShop\Adapter\Order\OrderProductQuantityUpdater;
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
     * @var OrderProductQuantityUpdater
     */
    private $orderProductQuantityUpdater;

    /**
     * @param OrderAmountUpdater $orderAmountUpdater
     * @param ContextStateManager $contextStateManager
     * @param OrderProductQuantityUpdater $orderProductQuantityUpdater
     */
    public function __construct(
        OrderAmountUpdater $orderAmountUpdater,
        OrderProductQuantityUpdater $orderProductQuantityUpdater,
        ContextStateManager $contextStateManager
    ) {
        $this->orderAmountUpdater = $orderAmountUpdater;
        $this->orderProductQuantityUpdater = $orderProductQuantityUpdater;
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
            // Delete Order Cart Rule and update Order
            $cartRule = new CartRule($orderCartRule->id_cart_rule);
            $orderCartRule->softDelete();
            $cart->removeCartRule($orderCartRule->id_cart_rule);

            // If cart rule was a gift product we must update an OrderDetail manually
            $giftOrderDetail = $this->getGiftOrderDetail($order, $cartRule);
            if (null !== $giftOrderDetail) {
                $newQuantity = ((int) $giftOrderDetail->product_quantity) - 1;

                /*
                 * Note: we are lucky the stock updates happens smoothly, it was not re-injected by removing the CartRule
                 * because Cart doesn't update the stock but it will while we update the OrderDetail Lucky
                 * for us it's the same difference of 1 so it's all accurate in the end
                 */
                // This calls the OrderAmountUpdater internally so no need to perform both calls
                $this->orderProductQuantityUpdater->update(
                    $order,
                    $giftOrderDetail,
                    $newQuantity,
                    $giftOrderDetail->id_order_invoice != 0 ? new OrderInvoice($giftOrderDetail->id_order_invoice) : null,
                    false
                );
            } else {
                $this->orderAmountUpdater->update($order, $cart, $orderCartRule->id_order_invoice);
            }
        } finally {
            $this->contextStateManager->restoreContext();
        }
    }

    /**
     * @param Order $order
     * @param CartRule $cartRule
     *
     * @return OrderDetail|null
     */
    private function getGiftOrderDetail(Order $order, CartRule $cartRule): ?OrderDetail
    {
        $productId = (int) $cartRule->gift_product;
        $productAttributeId = (int) $cartRule->gift_product_attribute;

        // First filter OrderDetails matching the gift
        $giftOrderDetails = [];
        foreach ($order->getOrderDetailList() as $orderDetail) {
            if ((int) $orderDetail['product_id'] !== $productId || (int) $orderDetail['product_attribute_id'] !== $productAttributeId) {
                continue;
            }
            $giftOrderDetails[] = $orderDetail;
        }

        if (empty($giftOrderDetails)) {
            return null;
        }

        $giftOrderDetailId = null;
        // We try to find a row with at least 2 quantities
        foreach ($giftOrderDetails as $giftOrderDetail) {
            if ($giftOrderDetail['product_quantity'] > 1) {
                $giftOrderDetailId = $giftOrderDetail['id_order_detail'];
                break;
            }
        }

        // By default use the first one as fallback
        if (null === $giftOrderDetailId) {
            $giftOrderDetailId = $giftOrderDetails[0]['id_order_detail'];
        }

        return null !== $giftOrderDetailId ? new OrderDetail($giftOrderDetailId) : null;
    }
}
