<?php

namespace PrestaShop\PrestaShop\Adapter\Order;

use PrestaShop\PrestaShop\Adapter\Cart\CartPresenter;
use PrestaShop\PrestaShop\Adapter\ObjectSerializer;
use PrestaShop\PrestaShop\Adapter\Product\PricePresenter;
use PrestaShop\PrestaShop\Adapter\Translator;
use PrestaShop\PrestaShop\Adapter\LegacyContext;
use Address;
use AddressFormat;
use Carrier;
use Cart;
use Context;
use CustomerMessage;
use HistoryController;
use Order;
use OrderReturn;
use Tools;

class OrderPresenter
{
    /* @var ObjectSerializer */
    private $objectSerializer;
    /* @var PricePresenter */
    private $pricePresenter;
    /* @var Translator */
    private $translator;

    public function __construct()
    {
        $this->objectSerializer = new ObjectSerializer();
        $this->pricePresenter = new PricePresenter();
        $this->translator = new Translator(new LegacyContext());
    }

    /**
     * @param Order $order
     *
     * @return array
     */
    public function present(Order $order)
    {
        return [
            'products' => $this->getProducts($order),
            'products_count' => count($this->getProducts($order)),
            'total' => $this->getAmounts($order)['total'],
            'subtotals' =>$this->getAmounts($order)['subtotals'],
            'details' => $this->getDetails($order),
            'history' => $this->getHistory($order),
            'messages' => $this->getMessages($order),
            'carrier' => $this->getCarrier($order),
            'addresses' => $this->getAddresses($order),
            'follow_up' => $this->getFollowUp($order),
            'shipping' => $this->getShipping($order),
            'id_address_delivery' => $order->id_address_delivery,
            'id_address_invoice' => $order->id_address_invoice
        ];
    }

    /**
     * @param Order $order
     *
     * @return array
     */
    public function getCart(Order $order)
    {
        return (new CartPresenter())->present(new Cart($order->id_cart));
    }

    /**
     * @param Order $order
     *
     * @return array
     */
    public function getProducts(Order $order)
    {
        $cartPresenter = $this->getCart($order);

        $products = $cartPresenter['products'];

        $orderProductsDetails = $order->getProductsDetail();

        foreach($products as &$product) {
            foreach($orderProductsDetails as $orderProduct) {
                if ($product['id_product'] == $orderProduct['product_id']) {
                    $product['id_order_detail'] = $orderProduct['id_order_detail'];
                }
            }
        }
        OrderReturn::addReturnedQuantity($products, $order->id);

        return $products;
    }

    /**
     * @param Order $order
     *
     * @return array
     */
    public function getAmounts(Order $order)
    {
        $amounts = [];
        $cartPresenter = $this->getCart($order);
        $amounts['total'] = $cartPresenter['total'];
        $amounts['subtotals'] = $cartPresenter['subtotals'];
        $amounts['total']['total_paid'] = [
            'type' => 'total_paid',
            'label' => $this->translator->trans('Total paid', [], 'Order'),
            'amount' => $this->pricePresenter->convertAndFormat($order->total_paid),
        ];

        return $amounts;
    }

    /**
     * @param Order $order
     *
     * @return array
     */
    public function getDetails(Order $order)
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
    public function getHistory(Order $order)
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
    public function getShipping(Order $order)
    {
        $shippingList = $order->getShipping();
        $orderShipping = [];

        foreach ($shippingList as $shippingId => $shipping) {
            if (isset($shipping['carrier_name']) && $shipping['carrier_name']) {
                $orderShipping[$shippingId] = $shipping;
                $orderShipping[$shippingId]['shipping_date'] = Tools::displayDate($shipping['date_add'], null, false);
                $orderShipping[$shippingId]['shipping_weight'] = ($shipping['weight'] > 0) ? sprintf('%.3f', $shipping['weight']).' '.Configuration::get('PS_WEIGHT_UNIT') : '-';
                $shippingCost = (!$order->getTaxCalculationMethod()) ? $shipping['shipping_cost_tax_excl'] : $shipping['shipping_cost_tax_incl'];
                $orderShipping[$shippingId]['shipping_cost'] = ($shippingCost > 0) ? Tools::displayPrice($shippingCost, (int) $order->id_currency) : $this->l('Free !');

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
    public function getMessages(Order $order)
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
    public function getCarrier(Order $order)
    {
        $carrier = new Carrier((int) $order->id_carrier, (int) $order->id_lang);
        $orderCarrier = $this->objectSerializer->toArray($carrier);
        $orderCarrier['name'] = ($carrier->name == '0') ? Configuration::get('PS_SHOP_NAME') : $carrier->name;

        return $orderCarrier;
    }

    /**
     * @param Order $order
     *
     * @return array
     */
    public function getAddresses(Order $order)
    {
        $orderAddresses = [
            'delivery' => [],
            'invoice' => [],
        ];

        $addressDelivery = new Address((int) $order->id_address_delivery);
        $addressInvoice = new Address((int) $order->id_address_invoice);

        if (!$order->isVirtual()) {
            $orderAddresses['delivery'] = $this->objectSerializer->toArray($addressDelivery);
            $orderAddresses['delivery']['formatted'] = AddressFormat::generateAddress($addressDelivery, array(), '<br />');
        }

        $orderAddresses['invoice'] = $this->objectSerializer->toArray($addressInvoice);
        $orderAddresses['invoice']['formatted'] = AddressFormat::generateAddress($addressInvoice, array(), '<br />');

        return $orderAddresses;
    }

    /**
     * @param Order $order
     *
     * @return string
     */
    public function getFollowUp(Order $order)
    {
        $carrier = $this->getCarrier($order);
        if (!empty($carrier['url']) && !empty($order->shipping_number)) {
            return str_replace('@', $order->shipping_number, $carrier['url']);
        }

        return '';
    }
}
