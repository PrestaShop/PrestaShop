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
 * needs please refer to https://devdocs.prestashop-project.org/ for more information.
 *
 * @author    PrestaShop SA and Contributors <contact@prestashop.com>
 * @copyright Since 2007 PrestaShop SA and Contributors
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 */

use PrestaShop\PrestaShop\Core\Util\Sorter;

/**
 * @since 1.5
 */
class HTMLTemplateInvoiceCore extends HTMLTemplate
{
    /**
     * @var Order
     */
    public $order;

    /**
     * @var OrderInvoice
     */
    public $order_invoice;

    /**
     * @var bool
     */
    public $available_in_your_account = false;

    /**
     * @param OrderInvoice $order_invoice
     * @param Smarty $smarty
     * @param bool $bulk_mode
     *
     * @throws PrestaShopException
     */
    public function __construct(OrderInvoice $order_invoice, Smarty $smarty, $bulk_mode = false)
    {
        $this->order_invoice = $order_invoice;
        $this->order = new Order((int) $this->order_invoice->id_order);
        $this->smarty = $smarty;
        $this->smarty->assign('isTaxEnabled', (bool) Configuration::get('PS_TAX'));

        // If shop_address is null, then update it with current one.
        // But no DB save required here to avoid massive updates for bulk PDF generation case.
        // (DB: bug fixed in 1.6.1.1 with upgrade SQL script to avoid null shop_address in old orderInvoices)
        if (empty($this->order_invoice->shop_address)) {
            $this->order_invoice->shop_address = OrderInvoice::getCurrentFormattedShopAddress((int) $this->order->id_shop);
            if (!$bulk_mode) {
                OrderInvoice::fixAllShopAddresses();
            }
        }

        // header informations
        $this->date = Tools::displayDate($order_invoice->date_add);

        $id_lang = Context::getContext()->language->id;
        $id_shop = Context::getContext()->shop->id;
        $this->title = $order_invoice->getInvoiceNumberFormatted($id_lang, $id_shop);

        $this->shop = new Shop((int) $this->order->id_shop);
    }

    /**
     * Returns the template's HTML header.
     *
     * @return string HTML header
     */
    public function getHeader()
    {
        $this->assignCommonHeaderData();
        $this->smarty->assign(['header' => Context::getContext()->getTranslator()->trans('Invoice', [], 'Shop.Pdf')]);

        return $this->smarty->fetch($this->getTemplate('header'));
    }

    /**
     * Compute layout elements size.
     *
     * @param array $params Layout elements
     *
     * @return array Layout elements columns size
     */
    protected function computeLayout(array $params)
    {
        $layout = [
            'reference' => [
                'width' => 15,
            ],
            'product' => [
                'width' => 40,
            ],
            'quantity' => [
                'width' => 12,
            ],
            'tax_code' => [
                'width' => 8,
            ],
            'unit_price_tax_excl' => [
                'width' => 0,
            ],
            'total_tax_excl' => [
                'width' => 0,
            ],
        ];

        if (isset($params['has_discount']) && $params['has_discount']) {
            $layout['before_discount'] = ['width' => 0];
            $layout['product']['width'] -= 7;
            $layout['reference']['width'] -= 3;
        }

        $total_width = 0;
        $free_columns_count = 0;
        foreach ($layout as $data) {
            if ($data['width'] === 0) {
                ++$free_columns_count;
            }

            $total_width += $data['width'];
        }

        $delta = 100 - $total_width;

        foreach ($layout as $row => $data) {
            if ($data['width'] === 0) {
                $layout[$row]['width'] = $delta / $free_columns_count;
            }
        }

        $layout['_colCount'] = count($layout);

        return $layout;
    }

    /**
     * Returns the template's HTML content.
     *
     * @return string HTML content
     */
    public function getContent()
    {
        $invoiceAddressPatternRules = json_decode(Configuration::get('PS_INVCE_INVOICE_ADDR_RULES'), true);
        $deliveryAddressPatternRules = json_decode(Configuration::get('PS_INVCE_DELIVERY_ADDR_RULES'), true);

        $invoice_address = new Address((int) $this->order->id_address_invoice);
        $country = new Country((int) $invoice_address->id_country);
        $formatted_invoice_address = AddressFormat::generateAddress($invoice_address, $invoiceAddressPatternRules, '<br />', ' ');

        $delivery_address = null;
        $formatted_delivery_address = '';
        if (!empty($this->order->id_address_delivery)) {
            $delivery_address = new Address((int) $this->order->id_address_delivery);
            $formatted_delivery_address = AddressFormat::generateAddress($delivery_address, $deliveryAddressPatternRules, '<br />', ' ');
        }

        $customer = new Customer((int) $this->order->id_customer);
        $carrier = new Carrier((int) $this->order->id_carrier);

        $order_details = $this->order_invoice->getProducts();

        $has_discount = false;
        foreach ($order_details as $id => &$order_detail) {
            // Find out if column 'price before discount' is required
            if ($order_detail['reduction_amount_tax_excl'] > 0) {
                $has_discount = true;
                $order_detail['unit_price_tax_excl_before_specific_price'] = $order_detail['unit_price_tax_excl_including_ecotax'] + $order_detail['reduction_amount_tax_excl'];
            } elseif ($order_detail['reduction_percent'] > 0) {
                $has_discount = true;
                if ($order_detail['reduction_percent'] == 100) {
                    $order_detail['unit_price_tax_excl_before_specific_price'] = 0;
                } else {
                    $order_detail['unit_price_tax_excl_before_specific_price'] = (100 * $order_detail['unit_price_tax_excl_including_ecotax']) / (100 - $order_detail['reduction_percent']);
                }
            }

            // Set tax_code
            $taxes = OrderDetail::getTaxListStatic($id);
            $tax_temp = [];
            foreach ($taxes as $tax) {
                $obj = new Tax($tax['id_tax']);
                $translator = Context::getContext()->getTranslator();
                $tax_temp[] = $translator->trans(
                    '%taxrate%%space%%',
                    [
                        '%taxrate%' => ($obj->rate + 0),
                        '%space%' => '&nbsp;',
                    ],
                    'Shop.Pdf'
                );
            }

            $order_detail['order_detail_tax'] = $taxes;
            $order_detail['order_detail_tax_label'] = implode(', ', $tax_temp);
        }
        unset(
            $tax_temp,
            $order_detail
        );

        if (Configuration::get('PS_PDF_IMG_INVOICE')) {
            foreach ($order_details as &$order_detail) {
                if ($order_detail['image'] != null) {
                    $name = 'product_mini_' . (int) $order_detail['product_id'] . (isset($order_detail['product_attribute_id']) ? '_' . (int) $order_detail['product_attribute_id'] : '') . '.jpg';
                    $path = _PS_PRODUCT_IMG_DIR_ . $order_detail['image']->getExistingImgPath() . '.jpg';

                    $order_detail['image_tag'] = preg_replace(
                        '/\.*' . preg_quote(__PS_BASE_URI__, '/') . '/',
                        _PS_ROOT_DIR_ . DIRECTORY_SEPARATOR,
                        ImageManager::thumbnail($path, $name, 45, 'jpg', false),
                        1
                    );

                    if (file_exists(_PS_TMP_IMG_DIR_ . $name)) {
                        $order_detail['image_size'] = getimagesize(_PS_TMP_IMG_DIR_ . $name);
                    } else {
                        $order_detail['image_size'] = false;
                    }
                }
            }
            unset($order_detail); // don't overwrite the last order_detail later
        }

        // Sort products by Reference ID (and if equals (like combination) by Supplier Reference)
        $sorter = new Sorter();
        $order_details = $sorter->natural($order_details, Sorter::ORDER_DESC, 'product_reference', 'product_supplier_reference');

        $cart_rules = $this->order->getCartRules();
        $free_shipping = false;
        foreach ($cart_rules as $key => $cart_rule) {
            if ($cart_rule['free_shipping']) {
                $free_shipping = true;
                /*
                 * Adjust cart rule value to remove the amount of the shipping.
                 * We're not interested in displaying the shipping discount as it is already shown as "Free Shipping".
                 */
                $cart_rules[$key]['value_tax_excl'] -= $this->order_invoice->total_shipping_tax_excl;
                $cart_rules[$key]['value'] -= $this->order_invoice->total_shipping_tax_incl;

                /*
                 * Don't display cart rules that are only about free shipping and don't create
                 * a discount on products.
                 */
                if ($cart_rules[$key]['value'] == 0) {
                    unset($cart_rules[$key]);
                }
            }
        }

        $product_taxes = 0;
        foreach ($this->order_invoice->getProductTaxesBreakdown($this->order) as $details) {
            $product_taxes += $details['total_amount'];
        }

        $product_discounts_tax_excl = $this->order_invoice->total_discount_tax_excl;
        $product_discounts_tax_incl = $this->order_invoice->total_discount_tax_incl;
        if ($free_shipping) {
            $product_discounts_tax_excl -= $this->order_invoice->total_shipping_tax_excl;
            $product_discounts_tax_incl -= $this->order_invoice->total_shipping_tax_incl;
        }

        $products_after_discounts_tax_excl = $this->order_invoice->total_products - $product_discounts_tax_excl;
        $products_after_discounts_tax_incl = $this->order_invoice->total_products_wt - $product_discounts_tax_incl;

        $shipping_tax_excl = $free_shipping ? 0 : $this->order_invoice->total_shipping_tax_excl;
        $shipping_tax_incl = $free_shipping ? 0 : $this->order_invoice->total_shipping_tax_incl;
        $shipping_taxes = $shipping_tax_incl - $shipping_tax_excl;

        $wrapping_taxes = $this->order_invoice->total_wrapping_tax_incl - $this->order_invoice->total_wrapping_tax_excl;

        $total_taxes = $this->order_invoice->total_paid_tax_incl - $this->order_invoice->total_paid_tax_excl;

        $footer = [
            'products_before_discounts_tax_excl' => $this->order_invoice->total_products,
            'product_discounts_tax_excl' => $product_discounts_tax_excl,
            'products_after_discounts_tax_excl' => $products_after_discounts_tax_excl,
            'products_before_discounts_tax_incl' => $this->order_invoice->total_products_wt,
            'product_discounts_tax_incl' => $product_discounts_tax_incl,
            'products_after_discounts_tax_incl' => $products_after_discounts_tax_incl,
            'product_taxes' => $product_taxes,
            'shipping_tax_excl' => $shipping_tax_excl,
            'shipping_taxes' => $shipping_taxes,
            'shipping_tax_incl' => $shipping_tax_incl,
            'wrapping_tax_excl' => $this->order_invoice->total_wrapping_tax_excl,
            'wrapping_taxes' => $wrapping_taxes,
            'wrapping_tax_incl' => $this->order_invoice->total_wrapping_tax_incl,
            'ecotax_taxes' => $total_taxes - $product_taxes - $wrapping_taxes - $shipping_taxes,
            'total_taxes' => $total_taxes,
            'total_paid_tax_excl' => $this->order_invoice->total_paid_tax_excl,
            'total_paid_tax_incl' => $this->order_invoice->total_paid_tax_incl,
        ];

        foreach ($footer as $key => $value) {
            $footer[$key] = Tools::ps_round($value, Context::getContext()->getComputingPrecision(), $this->order->round_mode);
        }

        /*
         * Need the $round_mode for the tests.
         */
        switch ($this->order->round_type) {
            case Order::ROUND_TOTAL:
                $round_type = 'total';

                break;
            case Order::ROUND_LINE:
                $round_type = 'line';

                break;
            case Order::ROUND_ITEM:
                $round_type = 'item';

                break;
            default:
                $round_type = 'line';

                break;
        }

        $display_product_images = Configuration::get('PS_PDF_IMG_INVOICE');
        $tax_excluded_display = Group::getPriceDisplayMethod($customer->id_default_group);

        $layout = $this->computeLayout(['has_discount' => $has_discount]);

        $legal_free_text = Hook::exec('displayInvoiceLegalFreeText', ['order' => $this->order]);
        if (!$legal_free_text) {
            $legal_free_text = Configuration::get('PS_INVOICE_LEGAL_FREE_TEXT', (int) Context::getContext()->language->id, null, (int) $this->order->id_shop);
        }

        $data = [
            'order' => $this->order,
            'order_invoice' => $this->order_invoice,
            'order_details' => $order_details,
            'carrier' => $carrier,
            'cart_rules' => $cart_rules,
            'delivery_address' => $formatted_delivery_address,
            'invoice_address' => $formatted_invoice_address,
            'addresses' => ['invoice' => $invoice_address, 'delivery' => $delivery_address],
            'tax_excluded_display' => $tax_excluded_display,
            'display_product_images' => $display_product_images,
            'layout' => $layout,
            'tax_tab' => $this->getTaxTabContent(),
            'customer' => $customer,
            'footer' => $footer,
            'ps_price_compute_precision' => Context::getContext()->getComputingPrecision(),
            'round_type' => $round_type,
            'legal_free_text' => $legal_free_text,
        ];

        if (Tools::getValue('debug')) {
            die(json_encode($data));
        }

        $this->smarty->assign($data);

        $tpls = [
            'style_tab' => $this->smarty->fetch($this->getTemplate('invoice.style-tab')),
            'addresses_tab' => $this->smarty->fetch($this->getTemplate('invoice.addresses-tab')),
            'summary_tab' => $this->smarty->fetch($this->getTemplate('invoice.summary-tab')),
            'product_tab' => $this->smarty->fetch($this->getTemplate('invoice.product-tab')),
            'tax_tab' => $this->getTaxTabContent(),
            'payment_tab' => $this->smarty->fetch($this->getTemplate('invoice.payment-tab')),
            'note_tab' => $this->smarty->fetch($this->getTemplate('invoice.note-tab')),
            'total_tab' => $this->smarty->fetch($this->getTemplate('invoice.total-tab')),
            'shipping_tab' => $this->smarty->fetch($this->getTemplate('invoice.shipping-tab')),
        ];
        $this->smarty->assign($tpls);

        return $this->smarty->fetch($this->getTemplateByCountry($country->iso_code));
    }

    /**
     * Returns the tax tab content.
     *
     * @return string|array Tax tab html content (Returns an array if debug params used in request)
     */
    public function getTaxTabContent()
    {
        $debug = Tools::getValue('debug');

        $address = new Address((int) $this->order->{Configuration::get('PS_TAX_ADDRESS_TYPE')});
        $tax_exempt = Configuration::get('VATNUMBER_MANAGEMENT')
                            && !empty($address->vat_number)
                            && $address->id_country != Configuration::get('VATNUMBER_COUNTRY');
        $carrier = new Carrier($this->order->id_carrier);

        $data = [
            'tax_exempt' => $tax_exempt,
            'use_one_after_another_method' => $this->order_invoice->useOneAfterAnotherTaxComputationMethod(),
            'display_tax_bases_in_breakdowns' => $this->order_invoice->displayTaxBasesInProductTaxesBreakdown(),
            'product_tax_breakdown' => $this->order_invoice->getProductTaxesBreakdown($this->order),
            'shipping_tax_breakdown' => $this->order_invoice->getShippingTaxesBreakdown($this->order),
            'ecotax_tax_breakdown' => $this->order_invoice->getEcoTaxTaxesBreakdown(),
            'wrapping_tax_breakdown' => $this->order_invoice->getWrappingTaxesBreakdown(),
            'tax_breakdowns' => $this->getTaxBreakdown(),
            'order' => $debug ? null : $this->order,
            'order_invoice' => $debug ? null : $this->order_invoice,
            'carrier' => $debug ? null : $carrier,
        ];

        if ($debug) {
            return $data;
        }

        $this->smarty->assign($data);

        return $this->smarty->fetch($this->getTemplate('invoice.tax-tab'));
    }

    /**
     * Returns different tax breakdown elements.
     *
     * @return array|bool Different tax breakdown elements
     */
    protected function getTaxBreakdown()
    {
        $breakdowns = [
            'product_tax' => $this->order_invoice->getProductTaxesBreakdown($this->order),
            'shipping_tax' => $this->order_invoice->getShippingTaxesBreakdown($this->order),
            'ecotax_tax' => Configuration::get('PS_USE_ECOTAX') ? $this->order_invoice->getEcoTaxTaxesBreakdown() : [],
            'wrapping_tax' => $this->order_invoice->getWrappingTaxesBreakdown(),
        ];

        foreach ($breakdowns as $type => $bd) {
            if (empty($bd)) {
                unset($breakdowns[$type]);
            }
        }

        if (empty($breakdowns)) {
            return false;
        }

        if (isset($breakdowns['product_tax'])) {
            foreach ($breakdowns['product_tax'] as &$bd) {
                $bd['total_tax_excl'] = $bd['total_price_tax_excl'];
            }
        }

        if (isset($breakdowns['ecotax_tax'])) {
            foreach ($breakdowns['ecotax_tax'] as &$bd) {
                $bd['total_tax_excl'] = $bd['ecotax_tax_excl'];
                $bd['total_amount'] = $bd['ecotax_tax_incl'] - $bd['ecotax_tax_excl'];
            }
        }

        return $breakdowns;
    }

    /**
     * Returns the invoice template associated to the country iso_code.
     *
     * @param string $iso_country
     *
     * @return string
     */
    protected function getTemplateByCountry($iso_country)
    {
        $file = Configuration::get('PS_INVOICE_MODEL');

        // try to fetch the iso template
        $template = $this->getTemplate($file . '.' . $iso_country);

        // else use the default one
        if (!$template) {
            $template = $this->getTemplate($file);
        }

        return $template;
    }

    /**
     * Returns the template filename when using bulk rendering.
     *
     * @return string filename
     */
    public function getBulkFilename()
    {
        return 'invoices.pdf';
    }

    /**
     * Returns the template filename.
     *
     * @return string filename
     */
    public function getFilename()
    {
        $id_lang = Context::getContext()->language->id;
        $id_shop = (int) $this->order->id_shop;

        return sprintf(
            '%s.pdf',
            $this->order_invoice->getInvoiceNumberFormatted($id_lang, $id_shop)
        );
    }
}
