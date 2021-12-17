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

/**
 * @since 1.5
 */
class HTMLTemplateOrderSlipCore extends HTMLTemplateInvoice
{
    /**
     * @var Order
     */
    public $order;

    /**
     * @var OrderSlip
     */
    public $order_slip;

    /**
     * @var int Cart id
     */
    public $id_cart;

    /**
     * @param OrderSlip $order_slip
     * @param Smarty $smarty
     *
     * @throws PrestaShopException
     */
    public function __construct(OrderSlip $order_slip, Smarty $smarty)
    {
        $this->order_slip = $order_slip;
        $this->order = new Order((int) $order_slip->id_order);
        $this->id_cart = $this->order->id_cart;

        $products = OrderSlip::getOrdersSlipProducts($this->order_slip->id, $this->order);

        foreach ($products as $product) {
            $customized_datas = Product::getAllCustomizedDatas($this->id_cart, null, true, null, (int) $product['id_customization']);
            Product::addProductCustomizationPrice($product, $customized_datas);
        }

        $this->order->products = $products;
        $this->smarty = $smarty;
        $this->smarty->assign('isTaxEnabled', (bool) Configuration::get('PS_TAX'));

        // header informations
        $this->date = Tools::displayDate($this->order_slip->date_add);
        $prefix = Configuration::get('PS_CREDIT_SLIP_PREFIX', Context::getContext()->language->id);
        $this->title = sprintf(HTMLTemplateOrderSlip::l('%1$s%2$06d'), $prefix, (int) $this->order_slip->id);

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
        $this->smarty->assign(['header' => Context::getContext()->getTranslator()->trans('Credit slip', [], 'Shop.Pdf')]);

        return $this->smarty->fetch($this->getTemplate('header'));
    }

    /**
     * Returns the template's HTML content.
     *
     * @return string HTML content
     */
    public function getContent()
    {
        $delivery_address = $invoice_address = new Address((int) $this->order->id_address_invoice);
        $formatted_invoice_address = AddressFormat::generateAddress($invoice_address, [], '<br />', ' ');
        $formatted_delivery_address = '';

        if ($this->order->id_address_delivery != $this->order->id_address_invoice) {
            $delivery_address = new Address((int) $this->order->id_address_delivery);
            $formatted_delivery_address = AddressFormat::generateAddress($delivery_address, [], '<br />', ' ');
        }

        $customer = new Customer((int) $this->order->id_customer);
        $this->order->total_paid_tax_excl = $this->order->total_paid_tax_incl = $this->order->total_products = $this->order->total_products_wt = 0;

        if ($this->order_slip->amount > 0) {
            foreach ($this->order->products as &$product) {
                $product['total_price_tax_excl'] = $product['unit_price_tax_excl'] * $product['product_quantity'];
                $product['total_price_tax_incl'] = $product['unit_price_tax_incl'] * $product['product_quantity'];

                if ($this->order_slip->partial == 1) {
                    $order_slip_detail = Db::getInstance()->getRow('
                        SELECT * FROM `' . _DB_PREFIX_ . 'order_slip_detail`
                        WHERE `id_order_slip` = ' . (int) $this->order_slip->id . '
                        AND `id_order_detail` = ' . (int) $product['id_order_detail']);

                    $product['total_price_tax_excl'] = $order_slip_detail['amount_tax_excl'];
                    $product['total_price_tax_incl'] = $order_slip_detail['amount_tax_incl'];
                }

                $this->order->total_products += $product['total_price_tax_excl'];
                $this->order->total_products_wt += $product['total_price_tax_incl'];
                $this->order->total_paid_tax_excl = $this->order->total_products;
                $this->order->total_paid_tax_incl = $this->order->total_products_wt;
            }
            unset($product);
        } else {
            $this->order->products = null;
        }

        if ($this->order_slip->shipping_cost == 0) {
            $this->order->total_shipping_tax_incl = $this->order->total_shipping_tax_excl = 0;
        }

        $tax = new Tax();
        $tax->rate = $this->order->carrier_tax_rate;
        $tax_calculator = new TaxCalculator([$tax]);
        $tax_excluded_display = Group::getPriceDisplayMethod((int) $customer->id_default_group);

        $this->order->total_shipping_tax_incl = $this->order_slip->total_shipping_tax_incl;
        $this->order->total_shipping_tax_excl = $this->order_slip->total_shipping_tax_excl;
        $this->order_slip->shipping_cost_amount = $tax_excluded_display ? $this->order_slip->total_shipping_tax_excl : $this->order_slip->total_shipping_tax_incl;

        $this->order->total_paid_tax_incl += $this->order->total_shipping_tax_incl;
        $this->order->total_paid_tax_excl += $this->order->total_shipping_tax_excl;

        $total_cart_rule = 0;
        if ($this->order_slip->order_slip_type == 1 && is_array($cart_rules = $this->order->getCartRules())) {
            foreach ($cart_rules as $cart_rule) {
                if ($tax_excluded_display) {
                    $total_cart_rule += $cart_rule['value_tax_excl'];
                } else {
                    $total_cart_rule += $cart_rule['value'];
                }
            }
        }

        $this->smarty->assign([
            'order' => $this->order,
            'order_slip' => $this->order_slip,
            'order_details' => $this->order->products,
            'cart_rules' => $this->order_slip->order_slip_type == 1 ? $this->order->getCartRules() : false,
            'amount_choosen' => $this->order_slip->order_slip_type == 2 ? true : false,
            'delivery_address' => $formatted_delivery_address,
            'invoice_address' => $formatted_invoice_address,
            'addresses' => ['invoice' => $invoice_address, 'delivery' => $delivery_address],
            'tax_excluded_display' => $tax_excluded_display,
            'total_cart_rule' => $total_cart_rule,
        ]);

        $tpls = [
            'style_tab' => $this->smarty->fetch($this->getTemplate('invoice.style-tab')),
            'addresses_tab' => $this->smarty->fetch($this->getTemplate('invoice.addresses-tab')),
            'summary_tab' => $this->smarty->fetch($this->getTemplate('order-slip.summary-tab')),
            'product_tab' => $this->smarty->fetch($this->getTemplate('order-slip.product-tab')),
            'total_tab' => $this->smarty->fetch($this->getTemplate('order-slip.total-tab')),
            'payment_tab' => $this->smarty->fetch($this->getTemplate('order-slip.payment-tab')),
            'tax_tab' => $this->getTaxTabContent(),
        ];
        $this->smarty->assign($tpls);

        return $this->smarty->fetch($this->getTemplate('order-slip'));
    }

    /**
     * Returns the template filename when using bulk rendering.
     *
     * @return string filename
     */
    public function getBulkFilename()
    {
        return 'order-slips.pdf';
    }

    /**
     * Returns the template filename.
     *
     * @return string filename
     */
    public function getFilename()
    {
        return Configuration::get('PS_CREDIT_SLIP_PREFIX', Context::getContext()->language->id, null, $this->order->id_shop) . sprintf('%06d', $this->order_slip->id) . '.pdf';
    }

    /**
     * Returns the tax tab content.
     *
     * @return string Tax tab html content
     */
    public function getTaxTabContent()
    {
        $address = new Address((int) $this->order->{Configuration::get('PS_TAX_ADDRESS_TYPE')});
        $tax_exempt = Configuration::get('VATNUMBER_MANAGEMENT')
                            && !empty($address->vat_number)
                            && $address->id_country != Configuration::get('VATNUMBER_COUNTRY');

        $this->smarty->assign([
            'tax_exempt' => $tax_exempt,
            'product_tax_breakdown' => $this->getProductTaxesBreakdown(),
            'shipping_tax_breakdown' => $this->getShippingTaxesBreakdown(),
            'order' => $this->order,
            'ecotax_tax_breakdown' => $this->order_slip->getEcoTaxTaxesBreakdown(),
            'is_order_slip' => true,
            'tax_breakdowns' => $this->getTaxBreakdown(),
            'display_tax_bases_in_breakdowns' => false,
        ]);

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
            'product_tax' => $this->getProductTaxesBreakdown(),
            'shipping_tax' => $this->getShippingTaxesBreakdown(),
            'ecotax_tax' => Configuration::get('PS_USE_ECOTAX') ? $this->order_slip->getEcoTaxTaxesBreakdown() : [],
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
     * @return array
     */
    public function getProductTaxesBreakdown()
    {
        // $breakdown will be an array with tax rates as keys and at least the columns:
        // 	- 'total_price_tax_excl'
        // 	- 'total_amount'
        $breakdown = [];

        $details = $this->order->getProductTaxesDetails($this->order->products);

        foreach ($details as $row) {
            $rate = sprintf('%.3f', $row['tax_rate']);
            if (!isset($breakdown[$rate])) {
                $breakdown[$rate] = [
                    'total_price_tax_excl' => 0,
                    'total_amount' => 0,
                    'id_tax' => $row['id_tax'],
                    'rate' => $rate,
                ];
            }

            $breakdown[$rate]['total_price_tax_excl'] += $row['total_tax_base'];
            $breakdown[$rate]['total_amount'] += $row['total_amount'];
        }

        foreach ($breakdown as $rate => $data) {
            $breakdown[$rate]['total_price_tax_excl'] = Tools::ps_round($data['total_price_tax_excl'], Context::getContext()->getComputingPrecision(), $this->order->round_mode);
            $breakdown[$rate]['total_amount'] = Tools::ps_round($data['total_amount'], Context::getContext()->getComputingPrecision(), $this->order->round_mode);
        }

        ksort($breakdown);

        return $breakdown;
    }

    /**
     * Returns Shipping tax breakdown elements.
     *
     * @return array Shipping tax breakdown elements
     */
    public function getShippingTaxesBreakdown()
    {
        $taxes_breakdown = [];
        $tax = new Tax();
        $tax->rate = $this->order->carrier_tax_rate;
        $tax_calculator = new TaxCalculator([$tax]);
        $customer = new Customer((int) $this->order->id_customer);
        $tax_excluded_display = Group::getPriceDisplayMethod((int) $customer->id_default_group);

        if ($tax_excluded_display) {
            $total_tax_excl = $this->order_slip->shipping_cost_amount;
            $shipping_tax_amount = $tax_calculator->addTaxes($this->order_slip->shipping_cost_amount) - $total_tax_excl;
        } else {
            $total_tax_excl = $tax_calculator->removeTaxes($this->order_slip->shipping_cost_amount);
            $shipping_tax_amount = $this->order_slip->shipping_cost_amount - $total_tax_excl;
        }

        if ($shipping_tax_amount > 0) {
            $taxes_breakdown[] = [
                'rate' => $this->order->carrier_tax_rate,
                'total_amount' => $shipping_tax_amount,
                'total_tax_excl' => $total_tax_excl,
            ];
        }

        return $taxes_breakdown;
    }
}
