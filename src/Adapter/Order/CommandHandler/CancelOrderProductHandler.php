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

use Configuration;
use StockAvailable;
use Customization;
use Hook;
use Order;
use OrderCarrier;
use OrderDetail;
use PrestaShop\PrestaShop\Core\Domain\Order\CommandHandler\CancelOrderProductHandlerInterface;
// use PrestaShop\PrestaShop\Adapter\Order\CommandHandler\AbstractOrderCommandHandler;
use PrestaShop\PrestaShop\Core\Domain\Order\Command\CancelOrderProductCommand;
use PrestaShop\PrestaShop\Core\Domain\Order\Exception\OrderException;
use Context;
use Cart;
use Validate;

/**
 * @internal
 */
final class CancelOrderProductHandler extends AbstractOrderCommandHandler implements CancelOrderProductHandlerInterface
{
    private $translator;

    public function handle(CancelOrderProductCommand $command)
    {
        $this->translator = Context::getContext()->getTranslator();
        $order = new Order($command->getOrder()->getId());
        // $this->checkInput($command);

        $cartId = Cart::getCartIdByOrderId($command->getOrder()->getId());
        $customizationQuantities = Customization::countQuantityByCart($cartId);
        $details = [];
        $customizedProductsOrderDetail = [];
        $orderDetails = $this->getOrderDetails($command);

        if (!empty($orderDetails['productsOrderDetails'])) {
            foreach ($orderDetails['productsOrderDetails'] as $orderDetail) {
                $details[] = $orderDetail;
                $customizationQuantity = 0;
                $cancelQuantity = $orderDetails['productCancelQuantity'][$orderDetail->id_order_detail];
                if (array_key_exists($orderDetail->product_id, $customizationQuantities) && array_key_exists($orderDetail->product_attribute_id, $customizationQuantities[$orderDetail->product_id])) {
                    $customizationQuantity = (int) $customizationQuantities[$orderDetail->product_id][$orderDetail->product_attribute_id];
                }
                if (($orderDetail->product_quantity - $customizationQuantity - $orderDetail->product_quantity_refunded - $orderDetail->product_quantity_return) < $cancelQuantity) {
                    throw new OrderException($this->translator->trans('An invalid quantity was selected for this product.', array(), 'Admin.Orderscustomers.Notification'));
                }
            }
        }

        if (!empty($orderDetails['customizedProductsOrderDetail'])) {
            $customizationList = [];
            foreach ($orderDetails['customizedProductsOrderDetail'] as $orderDetail) {
                $customizationList[$orderDetail->id_customization] = $orderDetail->id_order_detail;
            }
            $customization_quantities = Customization::retrieveQuantitiesFromIds(array_keys($customizationList));
            foreach ($customizationList as $id_customization => $id_order_detail) {
                $qtyCancelProduct = abs($orderDetails['customizedCancelQuantity'][$id_customization]);
                $customization_quantity = $customization_quantities[$id_customization];
                if (!$qtyCancelProduct) {
                    throw new OrderException($this->trans('No quantity has been selected for this product.', array(), 'Admin.Orderscustomers.Notification'));
                }
                if ($qtyCancelProduct > ($customization_quantity['quantity'] - ($customization_quantity['quantity_refunded'] + $customization_quantity['quantity_returned']))) {
                    throw new OrderException($this->trans('An invalid quantity was selected for this product.', array(), 'Admin.Orderscustomers.Notification'));
                }
            }
        }

        if (!empty($orderDetails['productsOrderDetails'])) {
            foreach ($orderDetails['productsOrderDetails'] as $orderDetail) {
                $qty_cancel_product = $orderDetails['productCancelQuantity'][$orderDetail->id_order_detail];
                if (!$order->hasBeenDelivered() || ($order->hasBeenDelivered() /*&& Tools::isSubmit('reinjectQuantities'))*/ && $qty_cancel_product > 0)) {
                    $this->reinjectQuantity($orderDetail, $qty_cancel_product);
                }

                // Delete product
                if (!$order->deleteProduct($order, $orderDetail, $qty_cancel_product)) {
                    throw new OrderException($this->trans('An error occurred while attempting to delete the product.', array(), 'Admin.Orderscustomers.Notification'));
                }

                // Update weight SUM
                $order_carrier = new OrderCarrier((int) $order->getIdOrderCarrier());
                if (Validate::isLoadedObject($order_carrier)) {
                    $order_carrier->weight = (float) $order->getTotalWeight();
                    if ($order_carrier->update()) {
                        $order->weight = sprintf('%.3f ' . Configuration::get('PS_WEIGHT_UNIT'), $order_carrier->weight);
                    }
                }

                if (Configuration::get('PS_ADVANCED_STOCK_MANAGEMENT') && StockAvailable::dependsOnStock($orderDetail->product_id)) {
                    StockAvailable::synchronize($orderDetail->product_id);
                }
                Hook::exec('actionProductCancel', array('order' => $order, 'id_order_detail' => (int) $orderDetail->id_order_detail), null, false, true, false, $order->id_shop);
            }
        }
        if (!empty($orderDetails['customizedProductsOrderDetail'])) {
            foreach ($orderDetails['customizedProductsOrderDetail'] as $orderDetail)
            {
                $qtyCancelProduct = abs($orderDetails['customizedCancelQuantity'][$orderDetail->id_customization]);
                if (!$order->deleteCustomization($orderDetail->id_customization, $qtyCancelProduct, $orderDetail)) {
                    $this->errors[] = $this->trans('An error occurred while attempting to delete product customization.', array(), 'Admin.Orderscustomers.Notification') . ' ' . $id_customization;
                }
            }
        }
        var_dump($command->getToBeCanceledProducts()); exit;
    }

    private function getOrderDetails(CancelOrderProductCommand $command)
    {
        $productList = [];
        $customizedProductsList = [];
        $customizedCancelQuantity = [];
        $productCancelQuantity = [];
        foreach ($command->getToBeCanceledProducts() as $orderDetailId => $cancelQuantity) {
            $orderDetail = new OrderDetail($orderDetailId);
            if ((int) $orderDetail->id_customization > 0) {
                $customizedProductsList[] = $orderDetail;
                $customizedCancelQuantity[$orderDetail->id_customization] = $cancelQuantity;
            } else {
                $productList[] = $orderDetail;
                $productCancelQuantity[$orderDetail->id_order_detail] = $cancelQuantity;
            }
        }

        return [
            'productsOrderDetails' => $productList,
            'customizedProductsOrderDetail' => $customizedProductsList,
            'customizedCancelQuantity' => $customizedCancelQuantity,
            'productCancelQuantity' => $productCancelQuantity,
        ];
    }

    private function checkInput(CancelOrderProductCommand $command)
    {
        if (empty($command->getToBeCanceledProducts())) {
            throw new OrderException($this->translator->trans('You must select a product.', array(), 'Admin.Orderscustomers.Notification'));
        }

        foreach ($command->getToBeCanceledProducts() as $orderDetailId => $quantity) {
            if ((int) $quantity <= 0) {
                throw new OrderException($this->translator->trans('You must enter a quantity.', array(), 'Admin.Orderscustomers.Notification'));
            }
        }
    }
}