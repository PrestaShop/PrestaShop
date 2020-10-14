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
use PrestaShop\PrestaShop\Adapter\StockManager;
use PrestaShop\PrestaShop\Core\ConfigurationInterface;
use PrestaShop\PrestaShop\Core\Domain\Order\Command\DeleteCartRuleFromOrderCommand;
use PrestaShop\PrestaShop\Core\Domain\Order\CommandHandler\DeleteCartRuleFromOrderHandlerInterface;
use PrestaShop\PrestaShop\Core\Domain\Order\Exception\OrderException;
use StockAvailable;
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
     * @var ConfigurationInterface
     */
    private $configuration;

    /**
     * @var StockManager
     */
    private $stockManager;

    /**
     * @param OrderAmountUpdater $orderAmountUpdater
     * @param ContextStateManager $contextStateManager
     * @param OrderProductQuantityUpdater $orderProductQuantityUpdater
     * @param ConfigurationInterface $configuration
     * @param StockManager $stockManager
     */
    public function __construct(
        OrderAmountUpdater $orderAmountUpdater,
        OrderProductQuantityUpdater $orderProductQuantityUpdater,
        ContextStateManager $contextStateManager,
        ConfigurationInterface $configuration,
        StockManager $stockManager
    ) {
        $this->orderAmountUpdater = $orderAmountUpdater;
        $this->orderProductQuantityUpdater = $orderProductQuantityUpdater;
        $this->contextStateManager = $contextStateManager;
        $this->configuration = $configuration;
        $this->stockManager = $stockManager;
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
                // The cart doesn't handle re-injecting the gift because as long as the order doesn't exists it's not
                // supposed to Now that the order exists we must update it
                $this->reInjectGiftOrderDetail($giftOrderDetail);

                // To avoid the cart from removing the product twice (one has already been removed
                // via removeCartRule) we pre-update the OrderDetail quantity
                $giftOrderDetail->product_quantity = ((int) $giftOrderDetail->product_quantity) - 1;

                // This calls the OrderAmountUpdater internally so no need to perform both calls
                $this->orderProductQuantityUpdater->update(
                    $order,
                    $giftOrderDetail,
                    $giftOrderDetail->product_quantity,
                    $giftOrderDetail->id_order_invoice != 0 ? new OrderInvoice($giftOrderDetail->id_order_invoice) : null
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

    /**
     * @param OrderDetail $orderDetail
     */
    private function reInjectGiftOrderDetail(OrderDetail $orderDetail): void
    {
        //@todo: this doesn't handle the advance stock management like it does in OrderProductQuantityUpdater:reinjectQuantity
        StockAvailable::updateQuantity(
            $orderDetail->product_id,
            $orderDetail->product_attribute_id,
            1,
            $orderDetail->id_shop,
            true,
            [
                'id_order' => $orderDetail->id_order,
                'id_stock_mvt_reason' => $this->configuration->get('PS_STOCK_CUSTOMER_ORDER_REASON'),
            ]
        );

        // sync all stock
        $this->stockManager->updatePhysicalProductQuantity(
            (int) $orderDetail->id_shop,
            (int) $this->configuration->get('PS_OS_ERROR'),
            (int) $this->configuration->get('PS_OS_CANCELED'),
            null,
            (int) $orderDetail->id_order
        );
    }
}
