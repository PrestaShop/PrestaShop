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

namespace PrestaShop\PrestaShop\Adapter\Order\Refund;

use Cart;
use Configuration;
use Context;
use Db;
use Order;
use OrderDetail;
use OrderHistory;
use PrestaShop\PrestaShop\Core\Domain\Order\Exception\DeleteCustomizedProductFromOrderException;
use PrestaShop\PrestaShop\Core\Domain\Order\Exception\DeleteProductFromOrderException;
use Psr\Log\LoggerInterface;
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
     * @param int $quantity
     */
    public function deleteProductFromOrder(Order $order, OrderDetail $orderDetail, int $quantity)
    {
        if ((int) $orderDetail->id_customization > 0) {
            $this->deleteCustomization($order, $orderDetail, $quantity);
        }
        $productPriceTaxExcl = $orderDetail->unit_price_tax_excl * $quantity;
        $productPriceTaxIncl = $orderDetail->unit_price_tax_incl * $quantity;

        $cart = new Cart($order->id_cart);
        $this->updateCart($cart, $orderDetail, $quantity);

        $packageShippingCostTaxIncl = $cart->getPackageShippingCost(
            $order->id_carrier,
            true,
            null,
            $order->getCartProducts()
        );
        $packageShippingCostTaxExcl = $cart->getPackageShippingCost(
            $order->id_carrier,
            false,
            null,
            $order->getCartProducts()
        );

        $shippingDiffTaxIncl = $order->total_shipping_tax_incl - $packageShippingCostTaxIncl;
        $shippingDiffTaxExcl = $order->total_shipping_tax_excl - $packageShippingCostTaxExcl;

        $this->updateOrder(
            $order,
            $productPriceTaxIncl,
            $productPriceTaxExcl,
            $shippingDiffTaxIncl,
            $shippingDiffTaxExcl
        );

        $this->updateOrderDetail(
            $order,
            $orderDetail,
            $quantity,
            $productPriceTaxIncl,
            $productPriceTaxExcl,
            $shippingDiffTaxIncl,
            $shippingDiffTaxExcl
        );

        $orderDetail->update();
        $order->update();
    }

    /**
     * @param Cart $cart
     * @param OrderDetail $orderDetail
     * @param int $quantity
     */
    private function updateCart(Cart $cart, OrderDetail $orderDetail, int $quantity)
    {
        $cart->updateQty(
            $quantity,
            $orderDetail->product_id,
            $orderDetail->product_attribute_id,
            false,
            'down'
        );
        $cart->update();
    }

    /**
     * @param Order $order
     * @param float $productPriceTaxIncl
     * @param float $productPriceTaxExcl
     * @param float $shippingDiffTaxIncl
     * @param float $shippingDiffTaxExcl
     */
    private function updateOrder(
        Order $order,
        float $productPriceTaxIncl,
        float $productPriceTaxExcl,
        float $shippingDiffTaxIncl,
        float $shippingDiffTaxExcl
    ) {
        $order->total_shipping -= $shippingDiffTaxIncl;
        $order->total_shipping_tax_excl -= $shippingDiffTaxExcl;
        $order->total_shipping_tax_incl -= $shippingDiffTaxIncl;
        $order->total_products -= $shippingDiffTaxExcl;
        $order->total_products_wt -= $productPriceTaxIncl;
        $order->total_paid -= $productPriceTaxIncl + $shippingDiffTaxIncl;
        $order->total_paid_tax_incl -= $productPriceTaxIncl + $shippingDiffTaxIncl;
        $order->total_paid_tax_excl -= $productPriceTaxExcl + $shippingDiffTaxExcl;
        $order->total_paid_real -= $productPriceTaxIncl + $shippingDiffTaxIncl;

        $fields = [
            'total_shipping',
            'total_shipping_tax_excl',
            'total_shipping_tax_incl',
            'total_products',
            'total_products_wt',
            'total_paid',
            'total_paid_tax_incl',
            'total_paid_tax_excl',
            'total_paid_real',
        ];

        /* Prevent from floating precision issues */
        foreach ($fields as $field) {
            if ($order->{$field} < 0) {
                $order->{$field} = 0;
            }
        }

        /* Prevent from floating precision issues */
        foreach ($fields as $field) {
            $order->{$field} = number_format(
                $order->{$field},
                Context::getContext()->getComputingPrecision(),
                '.',
                ''
            );
        }
    }

    /**
     * @param Order $order
     * @param OrderDetail $orderDetail
     * @param int $quantity
     * @param float $productPriceTaxIncl
     * @param float $productPriceTaxExcl
     * @param float $shippingDiffTaxIncl
     * @param float $shippingDiffTaxExcl
     *
     * @throws DeleteProductFromOrderException
     * @throws \PrestaShopDatabaseException
     * @throws \PrestaShopException
     */
    private function updateOrderDetail(
        Order $order,
        OrderDetail $orderDetail,
        int $quantity,
        float $productPriceTaxIncl,
        float $productPriceTaxExcl,
        float $shippingDiffTaxIncl,
        float $shippingDiffTaxExcl
    ) {
        $orderDetail->product_quantity -= (int) $quantity;
        if ($orderDetail->product_quantity == 0) {
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
        } else {
            $orderDetail->total_price_tax_incl -= $productPriceTaxIncl;
            $orderDetail->total_price_tax_excl -= $productPriceTaxExcl;
            $orderDetail->total_shipping_price_tax_incl -= $shippingDiffTaxIncl;
            $orderDetail->total_shipping_price_tax_excl -= $shippingDiffTaxExcl;
        }
    }

    /**
     * @param Order $order
     * @param OrderDetail $orderDetail
     * @param int $quantity
     */
    private function deleteCustomization(Order $order, OrderDetail $orderDetail, int $quantity)
    {
        if (!(int) $order->getCurrentState()) {
            throw new DeleteCustomizedProductFromOrderException('Could not get a valid Order state before deletion');
        }

        if ($order->hasBeenDelivered()) {
            return Db::getInstance()->execute('UPDATE `' . _DB_PREFIX_ . 'customization` SET `quantity_returned` = `quantity_returned` + ' . (int) $quantity . ' WHERE `id_customization` = ' . (int) $orderDetail->id_customization . ' AND `id_cart` = ' . (int) $order->id_cart . ' AND `id_product` = ' . (int) $orderDetail->product_id);
        } elseif ($order->hasBeenPaid()) {
            return Db::getInstance()->execute('UPDATE `' . _DB_PREFIX_ . 'customization` SET `quantity_refunded` = `quantity_refunded` + ' . (int) $quantity . ' WHERE `id_customization` = ' . (int) $orderDetail->id_customization . ' AND `id_cart` = ' . (int) $order->id_cart . ' AND `id_product` = ' . (int) $orderDetail->product_id);
        }
        if (!Db::getInstance()->execute('UPDATE `' . _DB_PREFIX_ . 'customization` SET `quantity` = `quantity` - ' . (int) $quantity . ' WHERE `id_customization` = ' . (int) $orderDetail->id_customization . ' AND `id_cart` = ' . (int) $order->id_cart . ' AND `id_product` = ' . (int) $orderDetail->product_id)) {
            throw new DeleteCustomizedProductFromOrderException('Could not update customization quantity in database.');
        }
        if (!Db::getInstance()->execute('DELETE FROM `' . _DB_PREFIX_ . 'customization` WHERE `quantity` = 0')) {
            throw new DeleteCustomizedProductFromOrderException('Could not delete customization from database.');
        }
    }
}
