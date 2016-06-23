<?php

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
use TaxConfiguration;
use Tools;

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

    public function __construct()
    {
        $this->cartPresenter = new CartPresenter();
        $this->objectPresenter = new ObjectPresenter();
        $this->priceFormatter = new PriceFormatter();
        $this->translator = Context::getContext()->getTranslator();
        $this->taxConfiguration = new TaxConfiguration();
    }

    /**
     * @param Order $order
     *
     * @return array
     */
    public function present($order)
    {
        if (!is_a($order, 'Order')) {
            throw new \Exception('OrderPresenter can only present instance of Order');
        }

        return array(
            'products' => $this->getProducts($order),
            'products_count' => count($this->getProducts($order)),
            'totals' => $this->getAmounts($order)['totals'],
            'subtotals' => $this->getAmounts($order)['subtotals'],
            'details' => $this->getDetails($order),
            'history' => $this->getHistory($order),
            'messages' => $this->getMessages($order),
            'carrier' => $this->getCarrier($order),
            'addresses' => $this->getAddresses($order),
            'follow_up' => $this->getFollowUp($order),
            'shipping' => $this->getShipping($order),
            'id_address_delivery' => $order->id_address_delivery,
            'id_address_invoice' => $order->id_address_invoice,
            'labels' => $this->getLabels(),
        );
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

        foreach ($orderProducts as &$orderProduct) {
            $orderProduct['name'] = $orderProduct['product_name'];
            $orderProduct['price'] = $this->priceFormatter->format($orderProduct['product_price']);
            $orderProduct['quantity'] = $orderProduct['product_quantity'];
            $orderProduct['total'] = $this->priceFormatter->format($orderProduct['total_price']);

            foreach ($cartProducts['products'] as $cartProduct) {
                if ($cartProduct['id_product'] === $orderProduct['product_id']) {
                    if (isset($cartProduct['attributes'])) {
                        $orderProduct['attributes'] = $cartProduct['attributes'];
                    } else {
                        $orderProduct['attributes'] = array();
                    }
                    $orderProduct['cover'] = $cartProduct['cover'];
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
    private function getAmounts(Order $order)
    {
        $amounts = array();
        $subtotals = array();

        $total_products = ($this->includeTaxes()) ? $order->total_products_wt : $order->total_products;
        $subtotals['products'] = array(
            'type' => 'products',
            'label' => $this->translator->trans('Subtotal', array(), 'Shop.Theme.Checkout'),
            'amount' => $total_products,
            'value' => $this->priceFormatter->format($total_products),
        );

        $discount_amount = ($this->includeTaxes())
            ? $order->total_discounts_tax_incl
            : $order->total_discounts_tax_excl;
        if ((float) $discount_amount) {
            $subtotals['discounts'] = array(
                'type' => 'discount',
                'label' => $this->translator->trans('Discount', array(), 'Shop.Theme.Checkout'),
                'amount' => $discount_amount,
                'value' => $this->priceFormatter->format($discount_amount),
            );
        }

        $cart = new Cart($order->id_cart);
        if (!$cart->isVirtualCart()) {
            $shippingCost = ($this->includeTaxes()) ? $order->total_shipping_tax_incl : $order->total_shipping_tax_excl;
            $subtotals['shipping'] = array(
                'type' => 'shipping',
                'label' => $this->translator->trans('Shipping and handling', array(), 'Shop.Theme.Checkout'),
                'amount' => $shippingCost,
                'value' => $shippingCost != 0 ? $this->priceFormatter->format($shippingCost) : $this->translator->trans('Free', array(), 'Shop.Theme.Checkout'),
            );
        }

        $tax = $order->total_paid_tax_incl - $order->total_paid_tax_excl;
        $subtotals['tax'] = null;
        if ((float) $tax && Configuration::get('PS_TAX_DISPLAY')) {
            $subtotals['tax'] = array(
                'type' => 'tax',
                'label' => $this->translator->trans('Tax', array(), 'Shop.Theme.Checkout'),
                'amount' => $tax,
                'value' => $this->priceFormatter->format($tax),
            );
        }

        $amounts['subtotals'] = $subtotals;

        $amounts['totals'] = array();
        $amounts['totals']['total'] = array(
            'type' => 'total',
            'label' => $this->translator->trans('Total', array(), 'Shop.Theme.Checkout'),
            'amount' => $order->total_paid,
            'value' => $this->priceFormatter->format($order->total_paid),
        );

        $amounts['totals']['total_paid'] = array(
            'type' => 'total_paid',
            'label' => $this->translator->trans('Total paid', array(), 'Shop.Theme.Checkout'),
            'amount' => $order->total_paid_real,
            'value' => $this->priceFormatter->format($order->total_paid_real),
        );

        return $amounts;
    }

    /**
     * @param Order $order
     *
     * @return array
     */
    private function getDetails(Order $order)
    {
        $context = Context::getContext();
        $cart = new Cart($order->id_cart);

        return array(
            'id' => $order->id,
            'reference' => $order->reference,
            'order_date' => Tools::displayDate($order->date_add, null, false),
            'details_url' => $context->link->getPageLink('order-detail', true, null, 'id_order='.$order->id),
            'reorder_url' => HistoryController::getUrlToReorder((int) $order->id, $context),
            'invoice_url' => HistoryController::getUrlToInvoice($order, $context),
            'gift_message' => nl2br($order->gift_message),
            'is_returnable' => (int) $order->isReturnable(),
            'is_virtual' => $cart->isVirtualCart(),
            'payment' => $order->payment,
            'recyclable' => (bool) $order->recyclable,
            'shipping' => $this->getShipping($order),
            'is_valid' => $order->valid,
        );
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

        return $orderHistory;
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
                        $tracking_line = '<a href="'.str_replace('@', $shipping['tracking_number'], $shipping['url']).'">'.$shipping['tracking_number'].'</a>';
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
                ? $this->translator->trans('(tax incl.)', array(), 'Shop.Theme')
                : $this->translator->trans('(tax excl.)', array(), 'Shop.Theme'),
            'tax_long' => ($this->includeTaxes())
                ? $this->translator->trans('(tax included)', array(), 'Shop.Theme')
                : $this->translator->trans('(tax excluded)', array(), 'Shop.Theme'),
        );
    }
}
