<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Adapter\PDF\Document;

use Address;
use AddressFormat;
use Configuration;
use Context;
use Currency;
use Customer;
use Group;
use Hook;
use Order;
use OrderSlip;
use PDF;
use PrestaShop\PrestaShop\Adapter\PDF\PdfDocumentCommonDataBuilder;
use PrestaShop\PrestaShop\Adapter\PDF\PdfTemplateResolver;
use PrestaShop\PrestaShop\Core\PDF\PdfDocumentBuilderInterface;
use PrestaShop\PrestaShop\Core\PDF\PdfDocumentInterface;
use PrestaShop\PrestaShop\Core\Util\Sorter;
use Product;
use Shop;
use Symfony\Contracts\Translation\TranslatorInterface;
use Tax;
use TaxCalculator;
use Tools;

/**
 * Builds a credit slip (order slip) PDF document (see legacy classes/pdf/HTMLTemplateOrderSlip.php).
 *
 * @internal
 */
final class OrderSlipPdfDocumentBuilder implements PdfDocumentBuilderInterface
{
    public function __construct(
        private readonly PdfTemplateResolver $templateResolver,
        private readonly PdfDocumentCommonDataBuilder $commonDataBuilder,
        private readonly TranslatorInterface $translator
    ) {
    }

    public function supports(string $type): bool
    {
        return PDF::TEMPLATE_ORDER_SLIP === $type;
    }

    public function getBulkFilename(): string
    {
        return 'order-slips.pdf';
    }

    public function build($object, bool $bulkMode): PdfDocumentInterface
    {
        /** @var OrderSlip $orderSlip */
        $orderSlip = $object;
        $order = new Order((int) $orderSlip->id_order);
        $order->products = OrderSlip::getOrdersSlipProducts((int) $orderSlip->id, $order);
        $shop = new Shop((int) $order->id_shop);

        $languageId = (int) Context::getContext()->language->id;
        $prefix = Configuration::get('PS_CREDIT_SLIP_PREFIX', $languageId);
        $title = sprintf('%1$s%2$06d', $prefix, (int) $orderSlip->id);
        $date = Tools::displayDate($orderSlip->date_add);

        $header = $this->templateResolver->render(
            'header',
            $this->commonDataBuilder->buildHeaderData($shop, $title, $date, $this->translator->trans('Credit slip', [], 'Shop.Pdf'))
        );
        $footer = $this->templateResolver->render(
            'footer',
            $this->commonDataBuilder->buildFooterData($shop, false)
        );
        $pagination = $this->templateResolver->render('pagination', []);
        $content = $this->getContent($order, $orderSlip);

        $filename = sprintf(
            '%s%06d.pdf',
            Configuration::get('PS_CREDIT_SLIP_PREFIX', $languageId, null, (int) $order->id_shop),
            (int) $orderSlip->id
        );

        return new GenericPdfDocument($header, $footer, $pagination, $content, $filename);
    }

    private function getContent(Order $order, OrderSlip $orderSlip): string
    {
        $invoiceAddress = new Address((int) $order->id_address_invoice);
        $formattedInvoiceAddress = AddressFormat::generateAddress($invoiceAddress, [], '<br />', ' ');

        $formattedDeliveryAddress = '';
        if ($order->id_address_delivery != $order->id_address_invoice) {
            $deliveryAddress = new Address((int) $order->id_address_delivery);
            $formattedDeliveryAddress = AddressFormat::generateAddress($deliveryAddress, [], '<br />', ' ');
        }

        $customer = new Customer((int) $order->id_customer);

        $order->total_paid_tax_excl = 0;
        $order->total_paid_tax_incl = 0;
        $order->total_products = 0;
        $order->total_products_wt = 0;

        if ($orderSlip->amount > 0) {
            // Fetched once and indexed by order detail id, rather than one query per
            // product like the legacy Db::getInstance()->getRow() call in a loop.
            $slipDetailsByOrderDetail = [];
            if (1 == $orderSlip->partial) {
                foreach (OrderSlip::getOrdersSlipDetail((int) $orderSlip->id) as $slipDetail) {
                    $slipDetailsByOrderDetail[(int) $slipDetail['id_order_detail']] = $slipDetail;
                }
            }

            foreach ($order->products as &$product) {
                [$product['text_customizations'], $product['file_customizations_count']] = $this->splitCustomizations($product['customizedDatas'] ?? []);

                $product['total_price_tax_excl'] = $product['unit_price_tax_excl'] * $product['product_quantity'];
                $product['total_price_tax_incl'] = $product['unit_price_tax_incl'] * $product['product_quantity'];

                if (1 == $orderSlip->partial) {
                    $slipDetail = $slipDetailsByOrderDetail[(int) $product['id_order_detail']] ?? null;
                    if (null !== $slipDetail) {
                        $product['total_price_tax_excl'] = (float) $slipDetail['amount_tax_excl'];
                        $product['total_price_tax_incl'] = (float) $slipDetail['amount_tax_incl'];
                    }
                }

                $order->total_products += $product['total_price_tax_excl'];
                $order->total_products_wt += $product['total_price_tax_incl'];
                $order->total_paid_tax_excl = $order->total_products;
                $order->total_paid_tax_incl = $order->total_products_wt;
            }
            unset($product);
        } else {
            $order->products = [];
        }

        if (0 == $orderSlip->shipping_cost) {
            $order->total_shipping_tax_incl = 0;
            $order->total_shipping_tax_excl = 0;
        }

        $taxExcludedDisplay = (bool) Group::getPriceDisplayMethod((int) $customer->id_default_group);

        $order->total_shipping_tax_incl = $orderSlip->total_shipping_tax_incl;
        $order->total_shipping_tax_excl = $orderSlip->total_shipping_tax_excl;
        $orderSlip->shipping_cost_amount = $taxExcludedDisplay ? $orderSlip->total_shipping_tax_excl : $orderSlip->total_shipping_tax_incl;

        $order->total_paid_tax_incl += $order->total_shipping_tax_incl;
        $order->total_paid_tax_excl += $order->total_shipping_tax_excl;

        $cartRules = false;
        $totalCartRule = 0;
        if (1 == $orderSlip->order_slip_type) {
            $cartRules = $order->getCartRules();
            if (is_array($cartRules)) {
                foreach ($cartRules as $cartRule) {
                    $totalCartRule += $taxExcludedDisplay ? $cartRule['value_tax_excl'] : $cartRule['value'];
                }
            }
        }

        $orderDetails = $order->products;
        if (!empty($orderDetails)) {
            $sorter = new Sorter();
            $orderDetails = $sorter->natural($orderDetails, Sorter::ORDER_DESC, 'product_reference', 'product_supplier_reference');
        }

        $variables = [
            'is_tax_enabled' => (bool) Configuration::get('PS_TAX'),
            'currency_iso_code' => Currency::getCurrencyInstance((int) $order->id_currency)->iso_code,
            'order' => $order,
            'order_slip' => $orderSlip,
            'order_reference' => $order->getUniqReference(),
            'order_date_formatted' => Tools::displayDate($order->date_add),
            'order_details' => $orderDetails,
            'cart_rules' => $cartRules,
            'amount_choosen' => 2 == $orderSlip->order_slip_type,
            'delivery_address' => $formattedDeliveryAddress,
            'invoice_address' => $formattedInvoiceAddress,
            'invoice_address_vat_number' => $invoiceAddress->vat_number,
            'tax_excluded_display' => $taxExcludedDisplay,
            'total_cart_rule' => $totalCartRule,
            'hook_display_pdf' => Hook::exec('displayPDFOrderSlip', ['object' => $orderSlip]),
        ];

        $variables['tax_tab'] = $this->getTaxTabContent($order, $orderSlip, $variables);

        $tpls = [
            'style_tab' => $this->templateResolver->render('style-tab', $variables),
            'addresses_tab' => $this->templateResolver->render('addresses-tab', $variables),
            'summary_tab' => $this->templateResolver->render('order-slip/summary-tab', $variables),
            'product_tab' => $this->templateResolver->render('order-slip/product-tab', $variables),
            'tax_tab' => $variables['tax_tab'],
            'total_tab' => $this->templateResolver->render('order-slip/total-tab', $variables),
            'payment_tab' => $this->templateResolver->render('order-slip/payment-tab', $variables),
        ];

        return $this->templateResolver->render('order-slip/order-slip', array_merge($variables, $tpls));
    }

    /**
     * @param array<string, mixed> $context
     */
    private function getTaxTabContent(Order $order, OrderSlip $orderSlip, array $context): string
    {
        $address = new Address((int) $order->{Configuration::get('PS_TAX_ADDRESS_TYPE')});
        $taxExempt = Configuration::get('VATNUMBER_MANAGEMENT')
            && !empty($address->vat_number)
            && $address->id_country != Configuration::get('VATNUMBER_COUNTRY');

        $variables = array_merge($context, [
            'tax_exempt' => $taxExempt,
            'display_tax_bases_in_breakdowns' => false,
            'tax_breakdowns' => $this->getTaxBreakdown($order, $orderSlip),
            'is_order_slip' => true,
        ]);

        return $this->templateResolver->render('tax-tab', $variables);
    }

    /**
     * @return array<string, array<int, array<string, mixed>>>|false
     */
    private function getTaxBreakdown(Order $order, OrderSlip $orderSlip)
    {
        $breakdowns = [
            'product_tax' => $this->getProductTaxesBreakdown($order),
            'shipping_tax' => $this->getShippingTaxesBreakdown($order, $orderSlip),
            'ecotax_tax' => Configuration::get('PS_USE_ECOTAX') ? $orderSlip->getEcoTaxTaxesBreakdown() : [],
        ];

        foreach ($breakdowns as $type => $breakdown) {
            if (empty($breakdown)) {
                unset($breakdowns[$type]);
            }
        }

        if (empty($breakdowns)) {
            return false;
        }

        if (isset($breakdowns['product_tax'])) {
            foreach ($breakdowns['product_tax'] as &$row) {
                $row['total_tax_excl'] = $row['total_price_tax_excl'];
            }
            unset($row);
        }

        if (isset($breakdowns['ecotax_tax'])) {
            foreach ($breakdowns['ecotax_tax'] as &$row) {
                $row['total_tax_excl'] = $row['ecotax_tax_excl'];
                $row['total_amount'] = $row['ecotax_tax_incl'] - $row['ecotax_tax_excl'];
            }
            unset($row);
        }

        return $breakdowns;
    }

    /**
     * @return array<int|string, array<string, mixed>>
     */
    private function getProductTaxesBreakdown(Order $order): array
    {
        $breakdown = [];
        $details = $order->getProductTaxesDetails($order->products);

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
            $breakdown[$rate]['total_price_tax_excl'] = Tools::ps_round($data['total_price_tax_excl'], Context::getContext()->getComputingPrecision(), $order->round_mode);
            $breakdown[$rate]['total_amount'] = Tools::ps_round($data['total_amount'], Context::getContext()->getComputingPrecision(), $order->round_mode);
        }

        ksort($breakdown);

        return $breakdown;
    }

    /**
     * @return array<int, array<string, mixed>>
     */
    private function getShippingTaxesBreakdown(Order $order, OrderSlip $orderSlip): array
    {
        $taxesBreakdown = [];
        $tax = new Tax();
        $tax->rate = $order->carrier_tax_rate;
        $taxCalculator = new TaxCalculator([$tax]);
        $customer = new Customer((int) $order->id_customer);
        $taxExcludedDisplay = Group::getPriceDisplayMethod((int) $customer->id_default_group);

        if ($taxExcludedDisplay) {
            $totalTaxExcl = $orderSlip->shipping_cost_amount;
            $shippingTaxAmount = $taxCalculator->addTaxes($orderSlip->shipping_cost_amount) - $totalTaxExcl;
        } else {
            $totalTaxExcl = $taxCalculator->removeTaxes($orderSlip->shipping_cost_amount);
            $shippingTaxAmount = $orderSlip->shipping_cost_amount - $totalTaxExcl;
        }

        if ($shippingTaxAmount > 0) {
            $taxesBreakdown[] = [
                'rate' => $order->carrier_tax_rate,
                'total_amount' => $shippingTaxAmount,
                'total_tax_excl' => $totalTaxExcl,
            ];
        }

        return $taxesBreakdown;
    }

    /**
     * @return array{0: array<int, array{name: string, value: string, is_module: bool}>, 1: int}
     */
    private function splitCustomizations(array $customizedDatas): array
    {
        $textCustomizations = [];
        $fileCustomizationsCount = 0;

        foreach ($customizedDatas as $customizationPerAddress) {
            foreach ($customizationPerAddress as $customization) {
                foreach ($customization['datas'][Product::CUSTOMIZE_TEXTFIELD] ?? [] as $customizationInfo) {
                    $textCustomizations[] = [
                        'name' => $customizationInfo['name'],
                        'value' => $customizationInfo['value'],
                        'is_module' => (bool) ($customizationInfo['id_module'] ?? 0),
                    ];
                }
                $fileCustomizationsCount += count($customization['datas'][Product::CUSTOMIZE_FILE] ?? []);
            }
        }

        return [$textCustomizations, $fileCustomizationsCount];
    }
}
