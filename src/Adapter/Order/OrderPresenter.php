<?php

namespace PrestaShop\PrestaShop\Adapter\Order;

use PrestaShop\PrestaShop\Adapter\Cart\CartPresenter;
use PrestaShop\PrestaShop\Adapter\ObjectPresenter;
use PrestaShop\PrestaShop\Adapter\Product\PriceFormatter;
use PrestaShop\PrestaShop\Adapter\Translator;
use PrestaShop\PrestaShop\Adapter\LegacyContext;
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
use Product;
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

    public function __construct()
    {
        $this->cartPresenter   = new CartPresenter();
        $this->objectPresenter = new ObjectPresenter();
        $this->priceFormatter  = new PriceFormatter();
        $this->translator      = new Translator(new LegacyContext());
    }

    /**
     * @param Order $order
     *
     * @return array
     */
    public function present($order)
    {
        if (!is_a($order, 'Order')) {
            throw new \Exception("OrderPresenter can only present instance of Order");
        }

        return [
            'products' => $this->getProducts($order),
            'products_count' => count($this->getProducts($order)),
            'totals' => $this->getAmounts($order)['totals'],
            'subtotals' =>$this->getAmounts($order)['subtotals'],
            'details' => $this->getDetails($order),
            'history' => $this->getHistory($order),
            'messages' => $this->getMessages($order),
            'carrier' => $this->getCarrier($order),
            'addresses' => $this->getAddresses($order),
            'follow_up' => $this->getFollowUp($order),
            'shipping' => $this->getShipping($order),
            'id_address_delivery' => $order->id_address_delivery,
            'id_address_invoice' => $order->id_address_invoice,
            'tax_label' => $this->getTaxLabel(),
        ];
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
        $tax_label = $this->getTaxLabel();
        $amounts = [];
        $subtotals = [];

        $tax = $order->total_paid_tax_incl - $order->total_paid_tax_excl;
        if ((float)$tax && Configuration::get('PS_TAX_DISPLAY')) {
            $subtotals['tax'] = [
                'type' => 'tax',
                'label' => $this->translator->trans('Tax', [], 'Cart'),
                'amount' => $tax,
                'value' => $this->priceFormatter->format($tax),
            ];
        }

        $total_products = ($this->includeTaxes()) ? $order->total_products_wt : $order->total_products;
        $subtotals['products'] = [
            'type' => 'products',
            'label' => $this->translator->trans('Products', [], 'Cart').' '.$tax_label,
            'amount' => $total_products,
            'value' => $this->priceFormatter->format($total_products),
        ];

        $shipping_cost = ($this->includeTaxes()) ? $order->total_shipping_tax_incl : $order->total_shipping_tax_excl;
        $subtotals['shipping'] = [
            'type' => 'shipping',
            'label' => $this->translator->trans('Shipping and handling', [], 'Cart').' '.$tax_label,
            'amount' => $shipping_cost,
            'value' => $shipping_cost != 0 ? $this->priceFormatter->format($shipping_cost) : $this->translator->trans('Free', [], 'Cart'),
        ];

        $discount_amount = ($this->includeTaxes()) ? $order->total_discounts_tax_incl : $order->total_discounts_tax_excl;
        if ((float)$discount_amount) {
            $subtotals['discounts'] = [
                'type' => 'discount',
                'label' => $this->translator->trans('Discount', [], 'Cart').' '.$tax_label,
                'amount' => $discount_amount,
                'value' => $this->priceFormatter->format($discount_amount),
            ];
        }

        $amounts['subtotals'] = $subtotals;

        $amounts['totals'] = array();
        $amounts['totals']['total'] = [
            'type' => 'total',
            'label' => $this->translator->trans('Total', [], 'Cart'),
            'amount' => $order->total_paid,
            'value' => $this->priceFormatter->format($order->total_paid),
        ];

        $amounts['totals']['total_paid'] = [
            'type' => 'total_paid',
            'label' => $this->translator->trans('Total paid', [], 'Order'),
            'amount' => $order->total_paid_real,
            'value' => $this->priceFormatter->format($order->total_paid_real),
        ];

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

        return [
            'id' => $order->id,
            'reference' => $order->reference,
            'order_date' => Tools::displayDate($order->date_add, null, false),
            'url_to_reorder' => HistoryController::getUrlToReorder((int) $order->id, $context),
            'url_to_invoice' => HistoryController::getUrlToInvoice($order, $context),
            'gift_message' => nl2br($order->gift_message),
            'return_allowed' => (int) $order->isReturnable(),
            'payment' => $order->payment,
            'recyclable' => (bool) $order->recyclable,
            'shipping' => $this->getShipping($order),
            'is_valid' => $order->valid
        ];
    }

    /**
     * @param Order $order
     *
     * @return array
     */
    private function getHistory(Order $order)
    {
        $orderHistory = [];
        $context = Context::getContext();
        $historyList = $order->getHistory($context->language->id, false, true);

        foreach ($historyList as $historyId => $history) {
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
        $orderShipping = [];

        foreach ($shippingList as $shippingId => $shipping) {
            if (isset($shipping['carrier_name']) && $shipping['carrier_name']) {
                $orderShipping[$shippingId] = $shipping;
                $orderShipping[$shippingId]['shipping_date'] = Tools::displayDate($shipping['date_add'], null, false);
                $orderShipping[$shippingId]['shipping_weight'] = ($shipping['weight'] > 0) ? sprintf('%.3f', $shipping['weight']).' '.Configuration::get('PS_WEIGHT_UNIT') : '-';
                $shippingCost = (!$order->getTaxCalculationMethod()) ? $shipping['shipping_cost_tax_excl'] : $shipping['shipping_cost_tax_incl'];
                $orderShipping[$shippingId]['shipping_cost'] = ($shippingCost > 0) ? Tools::displayPrice($shippingCost, (int) $order->id_currency) : $this->translator->trans('Free', [], 'Cart');

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
        $messages = [];
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
        $orderAddresses = [
            'delivery' => [],
            'invoice' => [],
        ];

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
        return !Product::getTaxCalculationMethod(Context::getContext()->cookie->id_customer);
    }

    private function getTaxLabel()
    {
        return ($this->includeTaxes())
                ? $this->translator->trans('(tax incl.)', [], 'Cart')
                : $this->translator->trans('(tax excl.)', [], 'Cart');
    }
}
