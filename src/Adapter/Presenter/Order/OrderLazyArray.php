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

namespace PrestaShop\PrestaShop\Adapter\Presenter\Order;

use Address;
use AddressFormat;
use Carrier;
use Cart;
use Configuration;
use Context;
use Currency;
use CustomerMessage;
use Doctrine\Common\Annotations\AnnotationException;
use Order;
use OrderReturn;
use PrestaShop\PrestaShop\Adapter\Presenter\AbstractLazyArray;
use PrestaShop\PrestaShop\Adapter\Presenter\Cart\CartPresenter;
use PrestaShop\PrestaShop\Adapter\Presenter\Object\ObjectPresenter;
use PrestaShop\PrestaShop\Adapter\Product\PriceFormatter;
use PrestaShopBundle\Translation\TranslatorComponent;
use PrestaShopException;
use ProductDownload;
use ReflectionException;
use TaxConfiguration;
use Tools;

class OrderLazyArray extends AbstractLazyArray
{
    /** @var CartPresenter */
    private $cartPresenter;

    /** @var ObjectPresenter */
    private $objectPresenter;

    /** @var PriceFormatter */
    private $priceFormatter;

    /** @var TranslatorComponent */
    private $translator;

    /** @var TaxConfiguration */
    private $taxConfiguration;

    /** @var Order */
    private $order;

    /** @var OrderSubtotalLazyArray */
    private $subTotals;

    /**
     * OrderArray constructor.
     *
     * @throws AnnotationException
     * @throws ReflectionException
     */
    public function __construct(Order $order)
    {
        $this->order = $order;
        $this->cartPresenter = new CartPresenter();
        $this->objectPresenter = new ObjectPresenter();
        $this->priceFormatter = new PriceFormatter();
        $this->translator = Context::getContext()->getTranslator();
        $this->taxConfiguration = new TaxConfiguration();
        $this->subTotals = new OrderSubtotalLazyArray($this->order);
        parent::__construct();
    }

    /**
     * @arrayAccess
     *
     * @return mixed
     */
    public function getTotals()
    {
        $amounts = $this->getAmounts();

        return $amounts['totals'];
    }

    /**
     * @arrayAccess
     *
     * @return int
     */
    public function getIdAddressInvoice()
    {
        return $this->order->id_address_invoice;
    }

    /**
     * @arrayAccess
     *
     * @return int
     */
    public function getIdAddressDelivery()
    {
        return $this->order->id_address_delivery;
    }

    /**
     * @arrayAccess
     *
     * @return mixed
     */
    public function getSubtotals()
    {
        return $this->subTotals;
    }

    /**
     * @arrayAccess
     *
     * @return int
     */
    public function getProductsCount()
    {
        return count($this->getProducts());
    }

    /**
     * @arrayAccess
     *
     * @return mixed
     *
     * @throws PrestaShopException
     */
    public function getShipping()
    {
        $details = $this->getDetails();

        return $details['shipping'];
    }

    /**
     * @arrayAccess
     *
     * @return array
     */
    public function getProducts()
    {
        $order = $this->order;
        $cart = new Cart($order->id_cart);

        $orderProducts = $order->getProducts();
        $cartProducts = $this->cartPresenter->present($cart);
        $orderPaid = $order->getCurrentOrderState() && $order->getCurrentOrderState()->paid;

        $includeTaxes = $this->includeTaxes();
        foreach ($orderProducts as &$orderProduct) {
            // Use data from OrderDetail in case that the Product has been deleted
            $orderProduct['name'] = $orderProduct['product_name'];
            $orderProduct['quantity'] = $orderProduct['product_quantity'];
            $orderProduct['id_product'] = $orderProduct['product_id'];
            $orderProduct['id_product_attribute'] = $orderProduct['product_attribute_id'];

            $productPrice = $includeTaxes ? 'product_price_wt' : 'product_price';
            $totalPrice = $includeTaxes ? 'total_wt' : 'total_price';

            $orderProduct['price'] = $this->priceFormatter->format(
                $orderProduct[$productPrice],
                Currency::getCurrencyInstance((int) $order->id_currency)
            );
            $orderProduct['total'] = $this->priceFormatter->format(
                $orderProduct[$totalPrice],
                Currency::getCurrencyInstance((int) $order->id_currency)
            );

            if ($orderPaid && $orderProduct['is_virtual']) {
                $id_product_download = ProductDownload::getIdFromIdProduct($orderProduct['product_id']);
                $product_download = new ProductDownload($id_product_download);
                if ($product_download->display_filename != '') {
                    $orderProduct['download_link'] =
                        $product_download->getTextLink(false, $orderProduct['download_hash'])
                        . '&id_order=' . (int) $order->id
                        . '&secure_key=' . $order->secure_key;
                }
            }

            foreach ($cartProducts['products'] as $cartProduct) {
                if (($cartProduct['id_product'] === $orderProduct['id_product'])
                    && ($cartProduct['id_product_attribute'] === $orderProduct['id_product_attribute'])
                ) {
                    if (isset($cartProduct['attributes'])) {
                        $orderProduct['attributes'] = $cartProduct['attributes'];
                    } else {
                        $orderProduct['attributes'] = [];
                    }
                    $orderProduct['cover'] = $cartProduct['cover'];
                    $orderProduct['default_image'] = $cartProduct['default_image'];
                    $orderProduct['unit_price_full'] = $cartProduct['unit_price_full'];
                    break;
                }
            }

            OrderReturn::addReturnedQuantity($orderProducts, $order->id);
        }

        $orderProducts = $this->cartPresenter->addCustomizedData($orderProducts, $cart);

        return $orderProducts;
    }

    /**
     * @arrayAccess
     *
     * @return array
     */
    public function getAmounts()
    {
        $order = $this->order;

        $amounts['subtotals'] = $this->subTotals;

        $amounts['totals'] = [];
        $amount = $this->includeTaxes() ? $order->total_paid : $order->total_paid_tax_excl;
        $amounts['totals']['total'] = [
            'type' => 'total',
            'label' => $this->translator->trans('Total', [], 'Shop.Theme.Checkout'),
            'amount' => $amount,
            'value' => $this->priceFormatter->format($amount, Currency::getCurrencyInstance((int) $order->id_currency)),
        ];

        $amounts['totals']['total_paid'] = [
            'type' => 'total_paid',
            'label' => $this->translator->trans('Total paid', [], 'Shop.Theme.Checkout'),
            'amount' => $order->total_paid_real,
            'value' => $this->priceFormatter->format(
                $order->total_paid_real,
                Currency::getCurrencyInstance((int) $order->id_currency)
            ),
        ];

        $amounts['totals']['total_including_tax'] = [
            'type' => 'total_including_tax',
            'label' => $this->translator->trans('Total (tax incl.)', [], 'Shop.Theme.Checkout'),
            'amount' => $order->total_paid_tax_incl,
            'value' => $this->priceFormatter->format(
                $order->total_paid_tax_incl,
                Currency::getCurrencyInstance((int) $order->id_currency)
            ),
        ];

        $amounts['totals']['total_excluding_tax'] = [
            'type' => 'total_excluding_tax',
            'label' => $this->translator->trans('Total (tax excl.)', [], 'Shop.Theme.Checkout'),
            'amount' => $order->total_paid_tax_excl,
            'value' => $this->priceFormatter->format(
                $order->total_paid_tax_excl,
                Currency::getCurrencyInstance((int) $order->id_currency)
            ),
        ];

        return $amounts;
    }

    /**
     * @arrayAccess
     *
     * @return OrderDetailLazyArray
     */
    public function getDetails()
    {
        return new OrderDetailLazyArray($this->order);
    }

    /**
     * @arrayAccess
     *
     * @return array
     */
    public function getHistory()
    {
        $order = $this->order;

        $orderHistory = [];
        $context = Context::getContext();
        $historyList = $order->getHistory($context->language->id, false, true);

        foreach ($historyList as $historyId => $history) {
            // HistoryList only contains order states that are not hidden to customers, the last visible order state,
            // that is to say the one we get in the first iteration
            if ($historyId === array_key_first($historyList)) {
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

    /**
     * @arrayAccess
     *
     * @return array
     */
    public function getMessages()
    {
        $order = $this->order;

        $messages = [];
        $customerMessages = CustomerMessage::getMessagesByOrderId((int) $order->id, false);

        foreach ($customerMessages as $cmId => $customerMessage) {
            $messages[$cmId] = $customerMessage;
            $messages[$cmId]['message'] = nl2br($customerMessage['message']);
            $messages[$cmId]['message_date'] = Tools::displayDate($customerMessage['date_add'], null, true);
            if (isset($customerMessage['elastname']) && $customerMessage['elastname']) {
                $messages[$cmId]['name'] = $customerMessage['efirstname'] . ' ' . $customerMessage['elastname'];
            } elseif ($customerMessage['clastname']) {
                $messages[$cmId]['name'] = $customerMessage['cfirstname'] . ' ' . $customerMessage['clastname'];
            } else {
                $messages[$cmId]['name'] = Configuration::get('PS_SHOP_NAME');
            }
        }

        return $messages;
    }

    /**
     * @arrayAccess
     *
     * @return array
     */
    public function getCarrier()
    {
        $order = $this->order;

        $carrier = new Carrier((int) $order->id_carrier, (int) $order->getAssociatedLanguage()->getId());
        $orderCarrier = $this->objectPresenter->present($carrier);
        $orderCarrier['name'] = ($carrier->name == '0') ? Configuration::get('PS_SHOP_NAME') : $carrier->name;
        $orderCarrier['delay'] = $carrier->delay;

        return $orderCarrier;
    }

    /**
     * @arrayAccess
     *
     * @return array
     */
    public function getAddresses()
    {
        $order = $this->order;

        $orderAddresses = [
            'delivery' => [],
            'invoice' => [],
        ];

        $addressDelivery = new Address((int) $order->id_address_delivery);
        $addressInvoice = new Address((int) $order->id_address_invoice);

        if (!$order->isVirtual()) {
            $orderAddresses['delivery'] = $this->objectPresenter->present($addressDelivery);
            $orderAddresses['delivery']['formatted'] =
                AddressFormat::generateAddress($addressDelivery, [], '<br />');
        }

        $orderAddresses['invoice'] = $this->objectPresenter->present($addressInvoice);
        $orderAddresses['invoice']['formatted'] = AddressFormat::generateAddress($addressInvoice, [], '<br />');

        return $orderAddresses;
    }

    /**
     * @arrayAccess
     *
     * @return string
     */
    public function getFollowUp()
    {
        $order = $this->order;

        $carrier = $this->getCarrier();
        if (!empty($carrier['url']) && !empty($order->shipping_number)) {
            return str_replace('@', $order->shipping_number, $carrier['url']);
        }

        return '';
    }

    /**
     * @arrayAccess
     *
     * @return array
     */
    public function getLabels()
    {
        return [
            'tax_short' => ($this->includeTaxes())
                ? $this->translator->trans('(tax incl.)', [], 'Shop.Theme.Global')
                : $this->translator->trans('(tax excl.)', [], 'Shop.Theme.Global'),
            'tax_long' => ($this->includeTaxes())
                ? $this->translator->trans('(tax included)', [], 'Shop.Theme.Global')
                : $this->translator->trans('(tax excluded)', [], 'Shop.Theme.Global'),
        ];
    }

    /**
     * @return bool|mixed
     */
    private function includeTaxes()
    {
        return $this->taxConfiguration->includeTaxes();
    }

    /**
     * @return array
     */
    private function getDefaultHistory()
    {
        return [
            'id_order_state' => '',
            'invoice' => '',
            'send_email' => '',
            'module_name' => '',
            'color' => '',
            'unremovable' => '',
            'hidden' => '',
            'loggable' => '',
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
        ];
    }
}
