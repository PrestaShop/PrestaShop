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
use Currency;
use Hook;
use ImageManager;
use Order;
use OrderInvoice;
use PDF;
use PrestaShop\PrestaShop\Adapter\PDF\PdfDocumentCommonDataBuilder;
use PrestaShop\PrestaShop\Adapter\PDF\PdfTemplateResolver;
use PrestaShop\PrestaShop\Core\PDF\PdfDocumentBuilderInterface;
use PrestaShop\PrestaShop\Core\PDF\PdfDocumentInterface;
use PrestaShop\PrestaShop\Core\Util\Sorter;
use Shop;
use Symfony\Contracts\Translation\TranslatorInterface;
use Tools;

/**
 * Builds a delivery slip PDF document (see legacy classes/pdf/HTMLTemplateDeliverySlip.php).
 *
 * @internal
 */
final class DeliverySlipPdfDocumentBuilder implements PdfDocumentBuilderInterface
{
    public function __construct(
        private readonly PdfTemplateResolver $templateResolver,
        private readonly PdfDocumentCommonDataBuilder $commonDataBuilder,
        private readonly TranslatorInterface $translator
    ) {
    }

    public function supports(string $type): bool
    {
        return PDF::TEMPLATE_DELIVERY_SLIP === $type;
    }

    public function getBulkFilename(): string
    {
        return 'deliveries.pdf';
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

        // The date MUST be the delivery slip date and not the invoice date. In case of empty date, use the old one.
        $date = Tools::displayDate($orderInvoice->delivery_date) ?: Tools::displayDate($orderInvoice->date_add);

        $prefix = Configuration::get('PS_DELIVERY_PREFIX', $languageId);
        $title = sprintf($this->translator->trans('%1$s%2$06d', [], 'Shop.Pdf'), $prefix, $orderInvoice->delivery_number);

        $header = $this->templateResolver->render(
            'header',
            $this->commonDataBuilder->buildHeaderData($shop, $title, $date, $this->translator->trans('Delivery', [], 'Shop.Pdf'))
        );
        $footer = $this->templateResolver->render(
            'footer',
            // Unlike Invoice, DeliverySlip never overrode HTMLTemplate::$available_in_your_account, so it kept the base class default (true).
            $this->commonDataBuilder->buildFooterData($shop, true)
        );
        $pagination = $this->templateResolver->render('pagination', []);
        $content = $this->getContent($order, $orderInvoice);

        // Legacy getFilename() uses the shop-scoped configuration value, while the title above uses the
        // default-shop one (no id_shop passed) exactly like classes/pdf/HTMLTemplateDeliverySlip.php does:
        // this asymmetry is a pre-existing legacy quirk, kept here for behavioral parity.
        $filenamePrefix = Configuration::get('PS_DELIVERY_PREFIX', $languageId, null, (int) $order->id_shop);
        $filename = sprintf('%s%06d.pdf', $filenamePrefix, $orderInvoice->delivery_number);

        return new GenericPdfDocument($header, $footer, $pagination, $content, $filename);
    }

    private function getContent(Order $order, OrderInvoice $orderInvoice): string
    {
        $deliveryAddress = new Address((int) $order->id_address_delivery);
        $formattedDeliveryAddress = AddressFormat::generateAddress($deliveryAddress, [], '<br />', ' ');

        $formattedInvoiceAddress = '';
        if ($order->id_address_delivery != $order->id_address_invoice) {
            $invoiceAddress = new Address((int) $order->id_address_invoice);
            $formattedInvoiceAddress = AddressFormat::generateAddress($invoiceAddress, [], '<br />', ' ');
        }

        $carrier = new Carrier((int) $order->id_carrier);
        $carrierName = '0' === $carrier->name ? Configuration::get('PS_SHOP_NAME') : $carrier->name;

        $displayProductImages = (bool) Configuration::get('PS_PDF_IMG_DELIVERY');

        $orderDetails = $orderInvoice->getProducts();
        foreach ($orderDetails as &$orderDetail) {
            $orderDetail['image_tag'] = null;
            if ($displayProductImages && null !== $orderDetail['image']) {
                $name = 'product_mini_' . (int) $orderDetail['product_id'] . (isset($orderDetail['product_attribute_id']) ? '_' . (int) $orderDetail['product_attribute_id'] : '') . '.jpg';
                $path = _PS_PRODUCT_IMG_DIR_ . $orderDetail['image']->getExistingImgPath() . '.jpg';
                $orderDetail['image_tag'] = preg_replace(
                    '/\.*' . preg_quote(__PS_BASE_URI__, '/') . '/',
                    _PS_ROOT_DIR_ . DIRECTORY_SEPARATOR,
                    ImageManager::thumbnail($path, $name, 45, 'jpg', false),
                    1
                );
            }
        }
        unset($orderDetail);

        $sorter = new Sorter();
        $orderDetails = $sorter->natural($orderDetails, Sorter::ORDER_DESC, 'product_reference', 'product_supplier_reference');

        $payments = [];
        foreach ($orderInvoice->getOrderPaymentCollection() as $payment) {
            $payments[] = [
                'payment_method' => $payment->payment_method,
                'amount' => $payment->amount,
                'currency_iso_code' => Currency::getCurrencyInstance((int) $payment->id_currency)->iso_code,
            ];
        }

        $variables = [
            'order' => $order,
            'order_reference' => $order->getUniqReference(),
            'order_date_formatted' => Tools::displayDate($order->date_add),
            'order_details' => $orderDetails,
            'shop_address' => $orderInvoice->shop_address,
            'delivery_address' => $formattedDeliveryAddress,
            'invoice_address' => $formattedInvoiceAddress,
            'carrier_name' => $carrierName,
            'display_product_images' => $displayProductImages,
            'payments' => $payments,
            'hook_display_pdf' => Hook::exec('displayPDFDeliverySlip', ['object' => $orderInvoice]),
        ];

        $tpls = [
            'style_tab' => $this->templateResolver->render('delivery-slip/style-tab', $variables),
            'addresses_tab' => $this->templateResolver->render('delivery-slip/addresses-tab', $variables),
            'summary_tab' => $this->templateResolver->render('delivery-slip/summary-tab', $variables),
            'product_tab' => $this->templateResolver->render('delivery-slip/product-tab', $variables),
            'payment_tab' => $this->templateResolver->render('delivery-slip/payment-tab', $variables),
        ];

        return $this->templateResolver->render('delivery-slip/delivery-slip', array_merge($variables, $tpls));
    }
}
