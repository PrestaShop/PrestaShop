<?php
/**
 * 2007-2018 PrestaShop
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
 * needs please refer to http://www.prestashop.com for more information.
 *
 * @author    PrestaShop SA <contact@prestashop.com>
 * @copyright 2007-2018 PrestaShop SA
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 * International Registered Trademark & Property of PrestaShop SA
 */


namespace PrestaShop\PrestaShop\Adapter\Order;

use PrestaShop\PrestaShop\Adapter\Cart\CartPresenter;
use PrestaShop\PrestaShop\Adapter\ObjectPresenter;
use PrestaShop\PrestaShop\Adapter\Product\PriceFormatter;
use PrestaShop\PrestaShop\Core\Foundation\Templating\PresenterInterface;
use Address;
use AddressFormat;
use Carrier;
use Cart;
use Configuration;
use Context;
use CustomerMessage;
use HistoryController;
use Order;
use OrderReturn;
use ProductDownload;
use TaxConfiguration;
use Tools;
use Currency;

class OrderPresenter implements PresenterInterface
{
    /* @var CartPresenter */
    private $cartPresenter;

    /* @var ObjectPresenter */
    private $objectPresenter;

    /* @var PriceFormatter */
    private $priceFormatter;

    /* @var Translator */
    private $translator;

    /* @var TaxConfiguration */
    private $taxConfiguration;

    /* @var bool */
    private $skipProductDetails = false;

    public function __construct()
    {
        $this->cartPresenter = new CartPresenter();
        $this->objectPresenter = new ObjectPresenter();
        $this->priceFormatter = new PriceFormatter();
        $this->translator = Context::getContext()->getTranslator();
        $this->taxConfiguration = new TaxConfiguration();
    }

    /**
     * Set if we need to skip product details from the results
     *
     * @param $value
     */
    public function setSkipProductDetails($value)
    {
        $this->skipProductDetails = (bool) $value;
    }

    /**
     * @return bool
     */
    public function getSkipProductDetails()
    {
        return $this->skipProductDetails;
    }

    /**
     * @param Order $order
     *
     * @return array
     * @throws \Exception
     */
    public function present($order)
    {
        if (!is_a($order, 'Order')) {
            throw new \Exception('OrderPresenter can only present instance of Order');
        }

        $amounts = $this->getAmounts($order, !$this->skipProductDetails);

        $result = array(
            'totals' => $amounts['totals'],
            'history' => $this->getHistory($order),
            'id_address_delivery' => $order->id_address_delivery,
            'id_address_invoice' => $order->id_address_invoice,
        );

        if (!$this->skipProductDetails) {
            $productsOrder = $this->getProducts($order);

            $result = array(
                'messages' => $this->getMessages($order),
                'follow_up' => $this->getFollowUp($order),
                'details' => $this->getDetails($order, true),
                'subtotals' => $amounts['subtotals'],
                'products' => $productsOrder,
                'products_count' => count($productsOrder),
                'addresses' => $this->getAddresses($order),
                'carrier' => $this->getCarrier($order),
                'labels' => $this->getLabels(),
            );

            // backward compat
            $result['shipping'] = $result['details']['shipping'];
        } else {
            $result['details'] = $this->getDetails($order, false);
        }

        return $result;
    }

    /**
     * @param Order $order
     *
     * @return array
     */
    private function getProducts(Order $order)
    {
        $cart = new Cart($order->id_cart);

        $orderProducts = $order->getCartProducts();
        $cartProducts = $this->cartPresenter->present($cart);
        $orderPaid = $order->getCurrentOrderState() && $order->getCurrentOrderState()->paid;

        foreach ($orderProducts as &$orderProduct) {
            $orderProduct['name'] = $orderProduct['product_name'];
            $orderProduct['price'] = $this->priceFormatter->format($orderProduct['product_price'], Currency::getCurrencyInstance((int)$order->id_currency));
            $orderProduct['quantity'] = $orderProduct['product_quantity'];
            $orderProduct['total'] = $this->priceFormatter->format($orderProduct['total_price'], Currency::getCurrencyInstance((int)$order->id_currency));

            if ($orderPaid && $orderProduct['is_virtual']) {
                $id_product_download = ProductDownload::getIdFromIdProduct($orderProduct['product_id']);
                $product_download = new ProductDownload($id_product_download);
                if ($product_download->display_filename != '') {
                    $orderProduct['download_link'] = $product_download->getTextLink(false, $orderProduct['download_hash'])
                        . '&id_order=' . (int)$order->id
                        . '&secure_key=' . $order->secure_key;
                }
            }

            foreach ($cartProducts['products'] as $cartProduct) {
                if (($cartProduct['id_product'] === $orderProduct['id_product']) && ($cartProduct['id_product_attribute'] === $orderProduct['id_product_attribute'])) {
                    if (isset($cartProduct['attributes'])) {
                        $orderProduct['attributes'] = $cartProduct['attributes'];
                    } else {
                        $orderProduct['attributes'] = array();
                    }
                    $orderProduct['cover'] = $cartProduct['cover'];
                    $orderProduct['unit_price_full'] = $cartProduct['unit_price_full'];
                }
            }

            OrderReturn::addReturnedQuantity($orderProducts, $order->id);
        }

        $orderProducts = $this->cartPresenter->addCustomizedData($orderProducts, $cart);

        return $orderProducts;
    }

    /**
     * @param Order $order
     *
     * @return array
     */
    private function getSubTotal(Order $order)
    {
        $subtotals = array();

        $total_products = ($this->includeTaxes()) ? $order->total_products_wt : $order->total_products;
        $subtotals['products'] = array(
            'type' => 'products',
            'label' => $this->translator->trans('Subtotal', array(), 'Shop.Theme.Checkout'),
            'amount' => $total_products,
            'value' => $this->priceFormatter->format($total_products, Currency::getCurrencyInstance((int)$order->id_currency)),
        );

        $discount_amount = ($this->includeTaxes())
            ? $order->total_discounts_tax_incl
            : $order->total_discounts_tax_excl;
        if ((float) $discount_amount) {
            $subtotals['discounts'] = array(
                'type' => 'discount',
                'label' => $this->translator->trans('Discount', array(), 'Shop.Theme.Checkout'),
                'amount' => $discount_amount,
                'value' => $this->priceFormatter->format($discount_amount, Currency::getCurrencyInstance((int)$order->id_currency)),
            );
        }

        $cart = new Cart($order->id_cart);
        if (!$cart->isVirtualCart()) {
            $shippingCost = ($this->includeTaxes()) ? $order->total_shipping_tax_incl : $order->total_shipping_tax_excl;
            $subtotals['shipping'] = array(
                'type' => 'shipping',
                'label' => $this->translator->trans('Shipping and handling', array(), 'Shop.Theme.Checkout'),
                'amount' => $shippingCost,
                'value' => $shippingCost != 0 ? $this->priceFormatter->format($shippingCost, Currency::getCurrencyInstance((int)$order->id_currency)) : $this->translator->trans('Free', array(), 'Shop.Theme.Checkout'),
            );
        }

        $tax = $order->total_paid_tax_incl - $order->total_paid_tax_excl;
        $subtotals['tax'] = array(
            'type' => 'tax',
            'label' => null,
            'amount' => null,
            'value' => '',
        );
        if ((float) $tax && Configuration::get('PS_TAX_DISPLAY')) {
            $subtotals['tax'] = array(
                'type' => 'tax',
                'label' => $this->translator->trans('Tax', array(), 'Shop.Theme.Checkout'),
                'amount' => $tax,
                'value' => $this->priceFormatter->format($tax, Currency::getCurrencyInstance((int)$order->id_currency)),
            );
        }

        if ($order->gift) {
            $giftWrapping = ($this->includeTaxes())
                ? $order->total_wrapping_tax_incl
                : $order->total_wrapping_tax_excl;
            $subtotals['gift_wrapping'] = array(
                'type' => 'gift_wrapping',
                'label' => $this->translator->trans('Gift wrapping', array(), 'Shop.Theme.Checkout'),
                'amount' => $giftWrapping,
                'value' => $this->priceFormatter->format($giftWrapping, Currency::getCurrencyInstance((int)$order->id_currency)),
            );
        }

        return $subtotals;
    }

    /**
     * @param Order $order
     * @param bool  $withSubTotal
     *
     * @return array
     */
    private function getAmounts(Order $order, $withSubTotal = true)
    {
        $amounts = array();
        if ($withSubTotal) {
            $amounts['subtotals'] = $this->getSubTotal($order);
        }

        $amounts['totals'] = array();
        $amount = $this->includeTaxes() ? $order->total_paid : $order->total_paid_tax_excl;
        $amounts['totals']['total'] = array(
            'type' => 'total',
            'label' => $this->translator->trans('Total', array(), 'Shop.Theme.Checkout'),
            'amount' => $amount,
            'value' => $this->priceFormatter->format($amount, Currency::getCurrencyInstance((int)$order->id_currency)),
        );

        $amounts['totals']['total_paid'] = array(
            'type' => 'total_paid',
            'label' => $this->translator->trans('Total paid', array(), 'Shop.Theme.Checkout'),
            'amount' => $order->total_paid_real,
            'value' => $this->priceFormatter->format($order->total_paid_real, Currency::getCurrencyInstance((int)$order->id_currency)),
        );

        return $amounts;
    }

    /**
     * @param Order $order
     * @param bool  $getExtraInfos
     *
     * @return array
     */
    private function getDetails(Order $order, $getExtraInfos = true)
    {
        $context = Context::getContext();

        $result = array(
            'id' => $order->id,
            'reference' => $order->reference,
            'order_date' => Tools::displayDate($order->date_add, null, false),
            'details_url' => $context->link->getPageLink('order-detail', true, null, 'id_order=' . $order->id),
            'reorder_url' => HistoryController::getUrlToReorder((int)$order->id, $context),
            'invoice_url' => HistoryController::getUrlToInvoice($order, $context),
            'gift_message' => nl2br($order->gift_message),
            'is_returnable' => (int)$order->isReturnable(),
            'payment' => $order->payment,
            'module' => $order->module,
            'recyclable' => (bool)$order->recyclable,
            'is_valid' => $order->valid,
        );

        if ($getExtraInfos) {
            $cart = new Cart($order->id_cart);
            $result['shipping'] = $this->getShipping($order);
            $result['is_virtual'] = $cart->isVirtualCart();
        }

        return $result;
    }

    /**
     * @param Order $order
     *
     * @return array
     */
    private function getHistory(Order $order)
    {
        $orderHistory = array();
        $context = Context::getContext();
        $historyList = $order->getHistory($context->language->id, false, true);

        foreach ($historyList as $historyId => $history) {
            if ($history['id_order_state'] == $order->current_state) {
                $historyId = 'current';
            }
            $orderHistory[$historyId] = $history;
            $orderHistory[$historyId]['history_date'] = Tools::displayDate($history['date_add'], null, false);
            $orderHistory[$historyId]['contrast'] = (Tools::getBrightness($history['color']) > 128) ? 'dark' : 'bright';
        }

        if (!isset($orderHistory['current'])) {
            $orderHistory['current'] = $this->getDefaultHistory();
        }

        return $orderHistory;
    }

    private function getDefaultHistory()
    {
        return array(
            'id_order_state' => '',
            'invoice' => '',
            'send_email' => '',
            'module_name' => '',
            'color' => '',
            'unremovable' => '',
            'hidden' => '',
            'logable' => '',
            'delivery' => '',
            'shipped' => '',
            'paid' => '',
            'pdf_invoice' => '',
            'pdf_delivery' => '',
            'deleted' => '',
            'id_order_history' => '',
            'id_employee' => '',
            'id_order' => '',
            'date_add' => '',
            'employee_firstname' => '',
            'employee_lastname' => '',
            'ostate_name' => '',
            'history_date' => '',
            'contrast' => '',
        );
    }

    /**
     * @param Order $order
     *
     * @return array
     */
    private function getShipping(Order $order)
    {
        $shippingList = $order->getShipping();
        $orderShipping = array();

        foreach ($shippingList as $shippingId => $shipping) {
            if (isset($shipping['carrier_name']) && $shipping['carrier_name']) {
                $orderShipping[$shippingId] = $shipping;
                $orderShipping[$shippingId]['shipping_date'] = Tools::displayDate($shipping['date_add'], null, false);
                $orderShipping[$shippingId]['shipping_weight'] = ($shipping['weight'] > 0) ? sprintf('%.3f', $shipping['weight']).' '.Configuration::get('PS_WEIGHT_UNIT') : '-';
                $shippingCost = (!$order->getTaxCalculationMethod()) ? $shipping['shipping_cost_tax_excl'] : $shipping['shipping_cost_tax_incl'];
                $orderShipping[$shippingId]['shipping_cost'] = ($shippingCost > 0) ? Tools::displayPrice($shippingCost, (int) $order->id_currency) : $this->translator->trans('Free', array(), 'Shop.Theme.Checkout');

                $tracking_line = '-';
                if ($shipping['tracking_number']) {
                    if ($shipping['url'] && $shipping['tracking_number']) {
                        $tracking_line = '<a href="'.str_replace('@', $shipping['tracking_number'], $shipping['url']).'" target="_blank">'.$shipping['tracking_number'].'</a>';
                    } else {
                        $tracking_line = $shipping['tracking_number'];
                    }
                }

                $orderShipping[$shippingId]['tracking'] = $tracking_line;
            }
        }

        return $orderShipping;
    }

    /**
     * @param Order $order
     *
     * @return array
     */
    private function getMessages(Order $order)
    {
        $messages = array();
        $customerMessages = CustomerMessage::getMessagesByOrderId((int) $order->id, false);

        foreach ($customerMessages as $cmId => $customerMessage) {
            $messages[$cmId] = $customerMessage;
            $messages[$cmId]['message'] = nl2br($customerMessage['message']);
            $messages[$cmId]['message_date'] = Tools::displayDate($customerMessage['date_add'], null, true);
            if (isset($customerMessage['elastname']) && $customerMessage['elastname']) {
                $messages[$cmId]['name'] = $customerMessage['efirstname'].' '.$customerMessage['elastname'];
            } elseif ($customerMessage['clastname']) {
                $messages[$cmId]['name'] = $customerMessage['cfirstname'].' '.$customerMessage['clastname'];
            } else {
                $messages[$cmId]['name'] = Configuration::get('PS_SHOP_NAME');
            }
        }

        return $messages;
    }

    /**
     * @param Order $order
     *
     * @return array
     */
    private function getCarrier(Order $order)
    {
        $carrier = new Carrier((int) $order->id_carrier, (int) $order->id_lang);
        $orderCarrier = $this->objectPresenter->present($carrier);
        $orderCarrier['name'] = ($carrier->name == '0') ? Configuration::get('PS_SHOP_NAME') : $carrier->name;
        $orderCarrier['delay'] = $carrier->delay;

        return $orderCarrier;
    }

    /**
     * @param Order $order
     *
     * @return array
     */
    private function getAddresses(Order $order)
    {
        $orderAddresses = array(
            'delivery' => array(),
            'invoice' => array(),
        );

        $addressDelivery = new Address((int) $order->id_address_delivery);
        $addressInvoice = new Address((int) $order->id_address_invoice);

        if (!$order->isVirtual()) {
            $orderAddresses['delivery'] = $this->objectPresenter->present($addressDelivery);
            $orderAddresses['delivery']['formatted'] = AddressFormat::generateAddress($addressDelivery, array(), '<br />');
        }

        $orderAddresses['invoice'] = $this->objectPresenter->present($addressInvoice);
        $orderAddresses['invoice']['formatted'] = AddressFormat::generateAddress($addressInvoice, array(), '<br />');

        return $orderAddresses;
    }

    /**
     * @param Order $order
     *
     * @return string
     */
    private function getFollowUp(Order $order)
    {
        $carrier = $this->getCarrier($order);
        if (!empty($carrier['url']) && !empty($order->shipping_number)) {
            return str_replace('@', $order->shipping_number, $carrier['url']);
        }

        return '';
    }

    private function includeTaxes()
    {
        return $this->taxConfiguration->includeTaxes();
    }

    private function getLabels()
    {
        return array(
            'tax_short' => ($this->includeTaxes())
                ? $this->translator->trans('(tax incl.)', array(), 'Shop.Theme.Global')
                : $this->translator->trans('(tax excl.)', array(), 'Shop.Theme.Global'),
            'tax_long' => ($this->includeTaxes())
                ? $this->translator->trans('(tax included)', array(), 'Shop.Theme.Global')
                : $this->translator->trans('(tax excluded)', array(), 'Shop.Theme.Global'),
        );
    }
}
