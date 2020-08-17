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

namespace PrestaShop\PrestaShop\Adapter\Order\Refund;

use Cart;
use CartRule;
use Configuration;
use Db;
use Order;
use OrderCartRule;
use OrderDetail;
use OrderHistory;
use PrestaShop\PrestaShop\Core\Domain\Order\Exception\DeleteCustomizedProductFromOrderException;
use PrestaShop\PrestaShop\Core\Domain\Order\Exception\DeleteProductFromOrderException;
use Psr\Log\LoggerInterface;
use SpecificPrice;
use Symfony\Component\Translation\TranslatorInterface;

class OrderProductRemover
{
    /**
     * @var LoggerInterface
     */
    private $logger;

    /**
     * @var TranslatorInterface
     */
    private $translator;

    /**
     * OrderProductRemover constructor.
     *
     * @param LoggerInterface $logger
     */
    public function __construct(LoggerInterface $logger, TranslatorInterface $translator)
    {
        $this->logger = $logger;
        $this->translator = $translator;
    }

    /**
     * @param Order $order
     * @param OrderDetail $orderDetail
     */
    public function deleteProductFromOrder(Order $order, OrderDetail $orderDetail)
    {
        $cart = new Cart($order->id_cart);

        // Important to remove order cart rule before the product is removed, so that cart rule can detect if it's applied on it
        $this->deleteOrderCartRule($order, $orderDetail, $cart);

        if ((int) $orderDetail->id_customization > 0) {
            $this->deleteCustomization($order, $orderDetail);
        }

        $this->updateCart($cart, $orderDetail);

        $this->deleteSpecificPrice($order, $orderDetail, $cart);

        $this->deleteOrderDetail(
            $order,
            $orderDetail
        );
    }

    /**
     * @param Cart $cart
     * @param OrderDetail $orderDetail
     */
    private function updateCart(Cart $cart, OrderDetail $orderDetail)
    {
        $cart->updateQty(
            $orderDetail->product_quantity,
            $orderDetail->product_id,
            $orderDetail->product_attribute_id,
            false,
            'down'
        );
        $cart->update();
    }

    /**
     * @param Order $order
     * @param OrderDetail $orderDetail
     *
     * @throws DeleteProductFromOrderException
     * @throws \PrestaShopDatabaseException
     * @throws \PrestaShopException
     */
    private function deleteOrderDetail(
        Order $order,
        OrderDetail $orderDetail
    ) {
        if (!$orderDetail->delete()) {
            throw new DeleteProductFromOrderException('Could not delete order detail');
        }
        if (count($order->getProductsDetail()) == 0) {
            $history = new OrderHistory();
            $history->id_order = (int) $order->id;
            $history->changeIdOrderState(Configuration::get('PS_OS_CANCELED'), $order);
            if (!$history->addWithemail()) {
                // email failure must not block order update process
                $this->logger->warning(
                    $this->translator->trans(
                        'Order history email could not be sent, test your email configuration in the Advanced Parameters > E-mail section of your back office.',
                        [],
                        'Admin.Orderscustomers.Notification'
                    )
                );
            }
        }

        $order->update();
    }

    /**
     * @param Order $order
     * @param OrderDetail $orderDetail
     */
    private function deleteCustomization(Order $order, OrderDetail $orderDetail)
    {
        if (!(int) $order->getCurrentState()) {
            throw new DeleteCustomizedProductFromOrderException('Could not get a valid Order state before deletion');
        }
        if (!Db::getInstance()->execute('DELETE FROM `' . _DB_PREFIX_ . 'customization` WHERE `id_customization` = ' . (int) $orderDetail->id_customization . ' AND `id_cart` = ' . (int) $order->id_cart . ' AND `id_product` = ' . (int) $orderDetail->product_id)) {
            throw new DeleteCustomizedProductFromOrderException('Could not delete customization from database.');
        }
    }

    /**
     * The deleted OrderCartRule are ignored by CartRule:autoAdd and CartRule:autoRemove so it is not able to clean
     * them when the product is removed, hence the discount could never be re applied. So we manually check and remove
     * them.
     *
     * @param Order $order
     * @param OrderDetail $orderDetail
     * @param Cart $cart
     */
    private function deleteOrderCartRule(
        Order $order,
        OrderDetail $orderDetail,
        Cart $cart
    ): void {
        $orderCartRules = $order->getDeletedCartRules();
        if (empty($orderCartRules)) {
            return;
        }

        $removedOrderCartRules = [];
        foreach ($orderCartRules as $orderCartRule) {
            $cartRule = new CartRule($orderCartRule['id_cart_rule']);
            $discountedProducts = $cartRule->checkProductRestrictionsFromCart($cart, true, true, true);
            foreach ($discountedProducts as $discountedProduct) {
                // The return value is the concatenation of productId and attributeId, but the attributeId is always replaced by 0
                if ($discountedProduct === $orderDetail->product_id . '-0') {
                    if (!in_array($orderCartRule['id_order_cart_rule'], $removedOrderCartRules)) {
                        $removedOrderCartRules[] = $orderCartRule['id_order_cart_rule'];
                    }
                }
            }
        }

        foreach ($removedOrderCartRules as $removedOrderCartRuleId) {
            $orderCartRule = new OrderCartRule($removedOrderCartRuleId);
            $orderCartRule->delete();
        }
    }

    /**
     * @param Order $order
     * @param OrderDetail $orderDetail
     * @param Cart $cart
     */
    private function deleteSpecificPrice(
        Order $order,
        OrderDetail $orderDetail,
        Cart $cart
    ): void {
        $productQuantity = $cart->getProductQuantity($orderDetail->product_id, $orderDetail->product_attribute_id);
        if (!isset($productQuantity['quantity']) || (int) $productQuantity['quantity'] > 0) {
            return;
        }

        // WARNING: DO NOT use SpecificPrice::getSpecificPrice as it filters out fields that are not in database
        // hence it ignores the customer or cart restriction and results are biased
        $existingSpecificPriceId = SpecificPrice::exists(
            (int) $orderDetail->product_id,
            (int) $orderDetail->product_attribute_id,
            0,
            0,
            0,
            $order->id_currency,
            $order->id_customer,
            SpecificPrice::ORDER_DEFAULT_FROM_QUANTITY,
            SpecificPrice::ORDER_DEFAULT_DATE,
            SpecificPrice::ORDER_DEFAULT_DATE,
            false,
            $order->id_cart
        );
        if (!empty($existingSpecificPriceId)) {
            $specificPrice = new SpecificPrice($existingSpecificPriceId);
            $specificPrice->delete();
        }
    }
}
