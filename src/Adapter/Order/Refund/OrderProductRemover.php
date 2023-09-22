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

namespace PrestaShop\PrestaShop\Adapter\Order\Refund;

use Cart;
use CartRule;
use Db;
use Order;
use OrderCartRule;
use OrderDetail;
use PrestaShop\PrestaShop\Adapter\Cart\Comparator\CartProductsComparator;
use PrestaShop\PrestaShop\Adapter\Cart\Comparator\CartProductUpdate;
use PrestaShop\PrestaShop\Core\Domain\Order\Exception\DeleteCustomizedProductFromOrderException;
use PrestaShop\PrestaShop\Core\Domain\Order\Exception\DeleteProductFromOrderException;
use SpecificPrice;

class OrderProductRemover
{
    /**
     * @var CartProductsComparator
     */
    private $cartProductComparator;

    /**
     * @param Order $order
     * @param OrderDetail $orderDetail
     * @param bool $updateCart Used when you don't want to update the cart (CartRule removal for example)
     *
     * @return CartProductsComparator
     */
    public function deleteProductFromOrder(
        Order $order,
        OrderDetail $orderDetail,
        bool $updateCart = true
    ): CartProductsComparator {
        $cart = new Cart($order->id_cart);

        $this->removeSpecificProductCartRules($order, $orderDetail);
        // Important to remove order cart rule before the product is removed, so that cart rule can detect if it's applied on it
        $this->deleteOrderCartRule($order, $orderDetail, $cart);

        if ((int) $orderDetail->id_customization > 0) {
            $this->deleteCustomization($order, $orderDetail);
        }

        $this->cartProductComparator = new CartProductsComparator($cart);
        if ($updateCart) {
            $this->updateCart($cart, $orderDetail);
        }

        $this->deleteSpecificPrice($order, $orderDetail, $cart);

        if ((int) $orderDetail->id_customization > 0) {
            $this->deleteCustomization($order, $orderDetail);
        }

        $this->deleteOrderDetail(
            $order,
            $orderDetail
        );

        return $this->cartProductComparator;
    }

    /**
     * @param Cart $cart
     * @param OrderDetail $orderDetail
     */
    private function updateCart(
        Cart $cart,
        OrderDetail $orderDetail
    ): void {
        $knownUpdates = [
            new CartProductUpdate(
                (int) $orderDetail->product_id,
                (int) $orderDetail->product_attribute_id,
                -$orderDetail->product_quantity,
                false,
                (int) $orderDetail->id_customization
            ),
        ];
        $this->cartProductComparator->setKnownUpdates($knownUpdates);

        $cart->updateQty(
            $orderDetail->product_quantity,
            $orderDetail->product_id,
            $orderDetail->product_attribute_id,
            $orderDetail->id_customization,
            'down',
            0,
            null,
            true,
            false,
            false // Do not preserve gift removal
        );
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
            $discountedProducts = $cartRule->checkProductRestrictionsFromCart($cart, true, false, true);
            if (!is_array($discountedProducts)) {
                continue;
            }
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

    /**
     * This method removes cart rules which was applied to specific product when that product is being deleted.
     *
     * @param Order $order
     * @param OrderDetail $orderDetail
     *
     * @return void
     */
    private function removeSpecificProductCartRules(
        Order $order,
        OrderDetail $orderDetail
    ): void {
        foreach ($order->getCartRules() as $orderCartRule) {
            $cartRuleId = (int) $orderCartRule['id_cart_rule'];
            $cartRule = new CartRule($cartRuleId);

            if ($cartRuleId === (int) $cartRule->id && (int) $cartRule->reduction_product !== (int) $orderDetail->product_id) {
                continue;
            }

            $orderCartRuleId = (int) $orderCartRule['id_order_cart_rule'];
            $orderCartRuleObj = new OrderCartRule($orderCartRuleId);
            if ((int) $orderCartRuleObj->id !== $orderCartRuleId) {
                continue;
            }

            $orderCartRuleObj->deleted = true;
            $orderCartRuleObj->save();
        }
    }
}
