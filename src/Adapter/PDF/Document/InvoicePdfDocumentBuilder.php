<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Adapter\PDF\Document;

use Address;
use AddressFormat;
use Carrier;
use Configuration;
use Context;
use Country;
use Currency;
use Customer;
use Group;
use Hook;
use ImageManager;
use Order;
use OrderDetail;
use OrderInvoice;
use PDF;
use PrestaShop\PrestaShop\Adapter\PDF\PdfDocumentCommonDataBuilder;
use PrestaShop\PrestaShop\Adapter\PDF\PdfTemplateResolver;
use PrestaShop\PrestaShop\Core\PDF\PdfDocumentBuilderInterface;
use PrestaShop\PrestaShop\Core\PDF\PdfDocumentInterface;
use PrestaShop\PrestaShop\Core\Util\Sorter;
use PrestaShopBundle\Entity\Repository\ShipmentRepository;
use Product;
use Shop;
use Symfony\Contracts\Translation\TranslatorInterface;
use Tax;
use Throwable;
use Tools;

/**
 * Builds an invoice PDF document (see legacy classes/pdf/HTMLTemplateInvoice.php).
 *
 * @internal
 */
final class InvoicePdfDocumentBuilder implements PdfDocumentBuilderInterface
{
    public function __construct(
        private readonly PdfTemplateResolver $templateResolver,
        private readonly PdfDocumentCommonDataBuilder $commonDataBuilder,
        private readonly ShipmentRepository $shipmentRepository,
        private readonly TranslatorInterface $translator
    ) {
    }

    public function supports(string $type): bool
    {
        return PDF::TEMPLATE_INVOICE === $type;
    }

    public function getBulkFilename(): string
    {
        return 'invoices.pdf';
    }

    public function build($object, bool $bulkMode): PdfDocumentInterface
    {
        /** @var OrderInvoice $orderInvoice */
        $orderInvoice = $object;
        $order = new Order((int) $orderInvoice->id_order);
        $shop = new Shop((int) $order->id_shop);

        if (empty($orderInvoice->shop_address)) {
            $orderInvoice->shop_address = OrderInvoice::getCurrentFormattedShopAddress((int) $order->id_shop);
            if (!$bulkMode) {
                OrderInvoice::fixAllShopAddresses();
            }
        }

        $languageId = (int) Context::getContext()->language->id;
        $shopId = (int) Context::getContext()->shop->id;
        $title = $orderInvoice->getInvoiceNumberFormatted($languageId, $shopId);
        $date = Tools::displayDate($orderInvoice->date_add);

        $header = $this->templateResolver->render(
            'header',
            $this->commonDataBuilder->buildHeaderData($shop, $title, $date, $this->translator->trans('Invoice', [], 'Shop.Pdf'))
        );
        $footer = $this->templateResolver->render(
            'footer',
            $this->commonDataBuilder->buildFooterData($shop, false)
        );
        $pagination = $this->templateResolver->render('pagination', []);
        $content = $this->getContent($order, $orderInvoice);

        $invoiceNumber = $orderInvoice->getInvoiceNumberFormatted($languageId, $shopId);
        $filename = sprintf('%s.pdf', str_replace(['/', '\\'], '-', $invoiceNumber));

        return new GenericPdfDocument($header, $footer, $pagination, $content, $filename);
    }

    private function getContent(Order $order, OrderInvoice $orderInvoice): string
    {
        $isTaxEnabled = (bool) Configuration::get('PS_TAX');
        $currencyIsoCode = Currency::getCurrencyInstance((int) $order->id_currency)->iso_code;

        $invoiceAddressPatternRules = json_decode(Configuration::get('PS_INVCE_INVOICE_ADDR_RULES'), true);
        $deliveryAddressPatternRules = json_decode(Configuration::get('PS_INVCE_DELIVERY_ADDR_RULES'), true);

        $invoiceAddress = new Address((int) $order->id_address_invoice);
        $country = new Country((int) $invoiceAddress->id_country);
        $formattedInvoiceAddress = AddressFormat::generateAddress($invoiceAddress, $invoiceAddressPatternRules ?: [], '<br />', ' ');

        $formattedDeliveryAddress = '';
        if (!empty($order->id_address_delivery)) {
            $deliveryAddress = new Address((int) $order->id_address_delivery);
            $formattedDeliveryAddress = AddressFormat::generateAddress($deliveryAddress, $deliveryAddressPatternRules ?: [], '<br />', ' ');
        }

        $customer = new Customer((int) $order->id_customer);
        $carrier = new Carrier((int) $order->id_carrier);
        $carrierName = $this->getCarrierNamesFromShipments($order->id) ?: $carrier->name;

        $orderDetails = $orderInvoice->getProducts();
        $hasDiscount = false;
        foreach ($orderDetails as $id => &$orderDetail) {
            if ($orderDetail['reduction_amount_tax_excl'] > 0) {
                $hasDiscount = true;
                $orderDetail['unit_price_tax_excl_before_specific_price'] = $orderDetail['unit_price_tax_excl_including_ecotax'] + $orderDetail['reduction_amount_tax_excl'];
            } elseif ($orderDetail['reduction_percent'] > 0) {
                $hasDiscount = true;
                $orderDetail['unit_price_tax_excl_before_specific_price'] = 100 == $orderDetail['reduction_percent']
                    ? 0
                    : (100 * $orderDetail['unit_price_tax_excl_including_ecotax']) / (100 - $orderDetail['reduction_percent']);
            }

            $taxes = OrderDetail::getTaxListStatic($id);
            $taxLabels = [];
            foreach ($taxes as $tax) {
                $taxObject = new Tax($tax['id_tax']);
                $taxLabels[] = $this->translator->trans('%taxrate%%space%%', ['%taxrate%' => $taxObject->rate + 0, '%space%' => '&nbsp;'], 'Shop.Pdf');
            }
            $orderDetail['order_detail_tax_label'] = implode(', ', $taxLabels);

            $orderDetail['image_tag'] = null;
            if (Configuration::get('PS_PDF_IMG_INVOICE') && null !== $orderDetail['image']) {
                $name = 'product_mini_' . (int) $orderDetail['product_id'] . (isset($orderDetail['product_attribute_id']) ? '_' . (int) $orderDetail['product_attribute_id'] : '') . '.jpg';
                $path = _PS_PRODUCT_IMG_DIR_ . $orderDetail['image']->getExistingImgPath() . '.jpg';
                $orderDetail['image_tag'] = preg_replace(
                    '/\.*' . preg_quote(__PS_BASE_URI__, '/') . '/',
                    _PS_ROOT_DIR_ . DIRECTORY_SEPARATOR,
                    ImageManager::thumbnail($path, $name, 45, 'jpg', false),
                    1
                );
            }

            [$orderDetail['text_customizations'], $orderDetail['file_customizations_count']] = $this->splitCustomizations($orderDetail['customizedDatas'] ?? []);
        }
        unset($orderDetail);

        $sorter = new Sorter();
        $orderDetails = $sorter->natural($orderDetails, Sorter::ORDER_DESC, 'product_reference', 'product_supplier_reference');

        $cartRules = $order->getCartRules();
        $freeShipping = false;
        foreach ($cartRules as $key => $cartRule) {
            if ($cartRule['free_shipping']) {
                $freeShipping = true;
                $cartRules[$key]['value_tax_excl'] -= $orderInvoice->total_shipping_tax_excl;
                $cartRules[$key]['value'] -= $orderInvoice->total_shipping_tax_incl;
                if (0 == $cartRules[$key]['value']) {
                    unset($cartRules[$key]);
                }
            }
        }

        $productTaxes = 0;
        foreach ($orderInvoice->getProductTaxesBreakdown($order) as $details) {
            $productTaxes += $details['total_amount'];
        }

        $productDiscountsTaxExcl = $orderInvoice->total_discount_tax_excl;
        $productDiscountsTaxIncl = $orderInvoice->total_discount_tax_incl;
        if ($freeShipping) {
            $productDiscountsTaxExcl -= $orderInvoice->total_shipping_tax_excl;
            $productDiscountsTaxIncl -= $orderInvoice->total_shipping_tax_incl;
        }

        $shippingTaxExcl = $freeShipping ? 0 : $orderInvoice->total_shipping_tax_excl;
        $shippingTaxIncl = $freeShipping ? 0 : $orderInvoice->total_shipping_tax_incl;
        $shippingTaxes = $shippingTaxIncl - $shippingTaxExcl;
        $wrappingTaxes = $orderInvoice->total_wrapping_tax_incl - $orderInvoice->total_wrapping_tax_excl;
        $totalTaxes = $orderInvoice->total_paid_tax_incl - $orderInvoice->total_paid_tax_excl;

        $footer = [
            'products_before_discounts_tax_excl' => $orderInvoice->total_products,
            'product_discounts_tax_excl' => $productDiscountsTaxExcl,
            'products_after_discounts_tax_excl' => $orderInvoice->total_products - $productDiscountsTaxExcl,
            'products_before_discounts_tax_incl' => $orderInvoice->total_products_wt,
            'product_discounts_tax_incl' => $productDiscountsTaxIncl,
            'products_after_discounts_tax_incl' => $orderInvoice->total_products_wt - $productDiscountsTaxIncl,
            'product_taxes' => $productTaxes,
            'shipping_tax_excl' => $shippingTaxExcl,
            'shipping_taxes' => $shippingTaxes,
            'shipping_tax_incl' => $shippingTaxIncl,
            'wrapping_tax_excl' => $orderInvoice->total_wrapping_tax_excl,
            'wrapping_taxes' => $wrappingTaxes,
            'wrapping_tax_incl' => $orderInvoice->total_wrapping_tax_incl,
            'ecotax_taxes' => $totalTaxes - $productTaxes - $wrappingTaxes - $shippingTaxes,
            'total_taxes' => $totalTaxes,
            'total_paid_tax_excl' => $orderInvoice->total_paid_tax_excl,
            'total_paid_tax_incl' => $orderInvoice->total_paid_tax_incl,
        ];
        foreach ($footer as $key => $value) {
            $footer[$key] = Tools::ps_round($value, Context::getContext()->getComputingPrecision(), $order->round_mode);
        }

        $legalFreeText = Hook::exec('displayInvoiceLegalFreeText', ['order' => $order]);
        if (!$legalFreeText) {
            $legalFreeText = Configuration::get('PS_INVOICE_LEGAL_FREE_TEXT', (int) Context::getContext()->language->id, null, (int) $order->id_shop);
        }

        $payments = [];
        foreach ($orderInvoice->getOrderPaymentCollection() as $payment) {
            $payments[] = [
                'payment_method' => $payment->payment_method,
                'amount' => $payment->amount,
                'currency_iso_code' => Currency::getCurrencyInstance((int) $payment->id_currency)->iso_code,
            ];
        }

        $variables = [
            'is_tax_enabled' => $isTaxEnabled,
            'currency_iso_code' => $currencyIsoCode,
            'order' => $order,
            'order_reference' => $order->getUniqReference(),
            'order_date_formatted' => Tools::displayDate($order->date_add),
            'invoice_date_formatted' => Tools::displayDate($orderInvoice->invoice_date),
            'title' => $orderInvoice->getInvoiceNumberFormatted((int) Context::getContext()->language->id, (int) Context::getContext()->shop->id),
            'order_details' => $orderDetails,
            'display_product_images' => (bool) Configuration::get('PS_PDF_IMG_INVOICE'),
            'carrier_name' => $carrierName,
            'cart_rules' => $cartRules,
            'delivery_address' => $formattedDeliveryAddress,
            'invoice_address' => $formattedInvoiceAddress,
            'invoice_address_vat_number' => $invoiceAddress->vat_number,
            'is_virtual_order' => $order->isVirtual(),
            'tax_excluded_display' => Group::getPriceDisplayMethod($customer->id_default_group),
            'layout' => $this->computeLayout($hasDiscount),
            'footer' => $footer,
            'order_invoice_note' => $orderInvoice->note,
            'payments' => $payments,
            'order_payment' => $order->payment,
            'legal_free_text_html' => nl2br(htmlspecialchars((string) $legalFreeText, ENT_QUOTES, 'UTF-8')),
            'hook_display_pdf' => Hook::exec('displayPDFInvoice', ['object' => $orderInvoice]),
        ];

        $variables['tax_tab'] = $this->getTaxTabContent($order, $orderInvoice, $variables);

        $tpls = [
            'style_tab' => $this->templateResolver->render('style-tab', $variables),
            'addresses_tab' => $this->templateResolver->render('addresses-tab', $variables),
            'summary_tab' => $this->templateResolver->render('invoice/summary-tab', $variables),
            'product_tab' => $this->templateResolver->render('invoice/product-tab', $variables),
            'tax_tab' => $variables['tax_tab'],
            'payment_tab' => $this->templateResolver->render('invoice/payment-tab', $variables),
            'note_tab' => $this->templateResolver->render('invoice/note-tab', $variables),
            'total_tab' => $this->templateResolver->render('invoice/total-tab', $variables),
            'shipping_tab' => $this->templateResolver->render('invoice/shipping-tab', $variables),
        ];

        return $this->templateResolver->renderFirstExisting(
            $this->getInvoiceModelCandidates($country->iso_code),
            array_merge($variables, $tpls)
        );
    }

    /**
     * @param array<string, mixed> $context
     */
    private function getTaxTabContent(Order $order, OrderInvoice $orderInvoice, array $context): string
    {
        $address = new Address((int) $order->{Configuration::get('PS_TAX_ADDRESS_TYPE')});
        $taxExempt = Configuration::get('VATNUMBER_MANAGEMENT')
            && !empty($address->vat_number)
            && $address->id_country != Configuration::get('VATNUMBER_COUNTRY');

        $breakdowns = $this->getTaxBreakdown($order, $orderInvoice);

        $variables = array_merge($context, [
            'tax_exempt' => $taxExempt,
            'display_tax_bases_in_breakdowns' => $orderInvoice->displayTaxBasesInProductTaxesBreakdown(),
            'tax_breakdowns' => $breakdowns,
            'is_order_slip' => false,
        ]);

        return $this->templateResolver->render('tax-tab', $variables);
    }

    /**
     * @return array<string, array<int, array<string, mixed>>>|false
     */
    private function getTaxBreakdown(Order $order, OrderInvoice $orderInvoice)
    {
        $breakdowns = [
            'product_tax' => $orderInvoice->getProductTaxesBreakdown($order),
            'shipping_tax' => $orderInvoice->getShippingTaxesBreakdown($order),
            'ecotax_tax' => Configuration::get('PS_USE_ECOTAX') ? $orderInvoice->getEcoTaxTaxesBreakdown() : [],
            'wrapping_tax' => $orderInvoice->getWrappingTaxesBreakdown(),
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

    private function computeLayout(bool $hasDiscount): array
    {
        $layout = [
            'reference' => ['width' => 15],
            'product' => ['width' => 40],
            'quantity' => ['width' => 12],
            'tax_code' => ['width' => 8],
            'unit_price_tax_excl' => ['width' => 0],
            'total_tax_excl' => ['width' => 0],
        ];

        if ($hasDiscount) {
            $layout['before_discount'] = ['width' => 0];
            $layout['product']['width'] -= 7;
            $layout['reference']['width'] -= 3;
        }

        $totalWidth = 0;
        $freeColumnsCount = 0;
        foreach ($layout as $data) {
            if (0 === $data['width']) {
                ++$freeColumnsCount;
            }
            $totalWidth += $data['width'];
        }

        $delta = 100 - $totalWidth;
        foreach ($layout as $row => $data) {
            if (0 === $data['width']) {
                $layout[$row]['width'] = $delta / $freeColumnsCount;
            }
        }

        $layout['_colCount'] = count($layout);

        return $layout;
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

    /**
     * @return string[]
     */
    private function getInvoiceModelCandidates(string $isoCountry): array
    {
        $file = Configuration::get('PS_INVOICE_MODEL');

        return ['invoice/' . $file . '.' . $isoCountry, 'invoice/' . $file];
    }

    private function getCarrierNamesFromShipments(int $orderId): string
    {
        try {
            $shipments = $this->shipmentRepository->findByOrderId($orderId);
        } catch (Throwable) {
            return '';
        }

        $carrierNames = [];
        foreach ($shipments as $shipment) {
            $carrier = new Carrier($shipment->getCarrierId());
            if ($carrier->name && !in_array($carrier->name, $carrierNames)) {
                $carrierNames[] = $carrier->name;
            }
        }

        return implode(', ', $carrierNames);
    }
}
