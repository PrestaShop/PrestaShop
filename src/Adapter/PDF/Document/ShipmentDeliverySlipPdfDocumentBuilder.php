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
use Hook;
use Image;
use ImageManager;
use Order;
use OrderDetail;
use OrderInvoice;
use PDF;
use PrestaShop\PrestaShop\Adapter\PDF\PdfDocumentCommonDataBuilder;
use PrestaShop\PrestaShop\Adapter\PDF\PdfTemplateResolver;
use PrestaShop\PrestaShop\Core\Domain\Shipment\ValueObject\DeliverySlipNumber;
use PrestaShop\PrestaShop\Core\PDF\PdfDocumentBuilderInterface;
use PrestaShop\PrestaShop\Core\PDF\PdfDocumentInterface;
use PrestaShop\PrestaShop\Core\Util\Sorter;
use PrestaShopBundle\Entity\Shipment;
use PrestaShopCollection;
use Product;
use Shop;
use Symfony\Contracts\Translation\TranslatorInterface;
use Tools;
use Validate;

/**
 * Builds a shipment delivery slip PDF document (see legacy classes/pdf/HTMLTemplateShipmentDeliverySlip.php).
 *
 * @internal
 */
final class ShipmentDeliverySlipPdfDocumentBuilder implements PdfDocumentBuilderInterface
{
    public function __construct(
        private readonly PdfTemplateResolver $templateResolver,
        private readonly PdfDocumentCommonDataBuilder $commonDataBuilder,
        private readonly TranslatorInterface $translator
    ) {
    }

    public function supports(string $type): bool
    {
        return PDF::TEMPLATE_SHIPMENT_DELIVERY_SLIP === $type;
    }

    public function getBulkFilename(): string
    {
        return 'shipment-deliveries.pdf';
    }

    /**
     * @param array{shipment: Shipment, order: Order, order_invoice_collection: PrestaShopCollection} $object
     */
    public function build($object, bool $bulkMode): PdfDocumentInterface
    {
        /** @var Shipment $shipment */
        $shipment = $object['shipment'];
        /** @var Order $order */
        $order = $object['order'];
        $orderInvoiceCollection = $object['order_invoice_collection'];

        $firstInvoice = $orderInvoiceCollection->getFirst();
        $orderInvoice = $firstInvoice instanceof OrderInvoice ? $firstInvoice : null;

        if (null !== $orderInvoice && empty($orderInvoice->shop_address)) {
            $orderInvoice->shop_address = OrderInvoice::getCurrentFormattedShopAddress((int) $order->id_shop);
            if (!$bulkMode) {
                OrderInvoice::fixAllShopAddresses();
            }
        }
        $shopAddress = null !== $orderInvoice
            ? $orderInvoice->shop_address
            : OrderInvoice::getCurrentFormattedShopAddress((int) $order->id_shop);

        $shop = new Shop((int) $order->id_shop);

        // Use shipment's shipped_at date, fallback to packed_at, then order date
        $shipmentDate = $shipment->getShippedAt() ?? $shipment->getPackedAt();
        $date = null !== $shipmentDate
            ? Tools::displayDate($shipmentDate->format('Y-m-d'))
            : Tools::displayDate($order->date_add);

        $languageId = (int) Context::getContext()->language->id;

        // Use delivery slip prefix and shipment ID as the number. Like DeliverySlipPdfDocumentBuilder,
        // this MUST be the default-shop-scoped configuration value (no id_shop passed), exactly like
        // classes/pdf/HTMLTemplateShipmentDeliverySlip.php does.
        $prefix = Configuration::get('PS_DELIVERY_PREFIX', $languageId);
        $title = DeliverySlipNumber::format($prefix, (int) $order->id, $shipment->getId());

        $header = $this->templateResolver->render(
            'header',
            $this->commonDataBuilder->buildHeaderData($shop, $title, $date, $this->translator->trans('Delivery', [], 'Shop.Pdf'))
        );
        $footer = $this->templateResolver->render(
            'footer',
            // Like DeliverySlip, ShipmentDeliverySlip never overrode HTMLTemplate::$available_in_your_account,
            // so it kept the base class default (true).
            $this->commonDataBuilder->buildFooterData($shop, true)
        );
        $pagination = $this->templateResolver->render('pagination', []);
        $content = $this->getContent($order, $shipment, $shopAddress, $object);

        // Legacy getFilename() uses the shop-scoped configuration value, while the title above uses the
        // default-shop one (no id_shop passed), exactly like classes/pdf/HTMLTemplateShipmentDeliverySlip.php
        // does: this asymmetry is a pre-existing legacy quirk, kept here for behavioral parity.
        $filenamePrefix = Configuration::get('PS_DELIVERY_PREFIX', $languageId, null, (int) $order->id_shop);
        $filename = sprintf('%s.pdf', $filenamePrefix . DeliverySlipNumber::format('', (int) $order->id, $shipment->getId()));

        return new GenericPdfDocument($header, $footer, $pagination, $content, $filename);
    }

    /**
     * @param array{shipment: Shipment, order: Order, order_invoice_collection: PrestaShopCollection} $rawObject
     */
    private function getContent(Order $order, Shipment $shipment, string $shopAddress, array $rawObject): string
    {
        $deliveryAddress = new Address($shipment->getAddressId());
        $formattedDeliveryAddress = AddressFormat::generateAddress($deliveryAddress, [], '<br />', ' ');

        $formattedInvoiceAddress = '';
        if ($order->id_address_delivery != $order->id_address_invoice) {
            $invoiceAddress = new Address((int) $order->id_address_invoice);
            $formattedInvoiceAddress = AddressFormat::generateAddress($invoiceAddress, [], '<br />', ' ');
        }

        $carrier = new Carrier($shipment->getCarrierId());
        $carrierName = empty($carrier->name) ? Configuration::get('PS_SHOP_NAME') : $carrier->name;

        $displayProductImages = (bool) Configuration::get('PS_PDF_IMG_DELIVERY');
        $orderDetails = $this->getShipmentProducts($shipment, $order, $displayProductImages);

        $sorter = new Sorter();
        $orderDetails = $sorter->natural($orderDetails, Sorter::ORDER_DESC, 'product_reference', 'product_supplier_reference');

        $variables = [
            'order' => $order,
            'order_reference' => $order->getUniqReference(),
            'order_date_formatted' => Tools::displayDate($order->date_add),
            'order_details' => $orderDetails,
            'shop_address' => $shopAddress,
            'delivery_address' => $formattedDeliveryAddress,
            'invoice_address' => $formattedInvoiceAddress,
            'carrier_name' => $carrierName,
            'tracking_number' => $shipment->getTrackingNumber(),
            'display_product_images' => $displayProductImages,
            'hook_display_pdf' => Hook::exec('displayPDFShipmentDeliverySlip', ['object' => $rawObject]),
        ];

        $tpls = [
            'style_tab' => $this->templateResolver->render('delivery-slip/style-tab', $variables),
            'addresses_tab' => $this->templateResolver->render('delivery-slip/addresses-tab', $variables),
            'summary_tab' => $this->templateResolver->render('shipment-delivery-slip/summary-tab', $variables),
            'product_tab' => $this->templateResolver->render('shipment-delivery-slip/product-tab', $variables),
        ];

        return $this->templateResolver->render('delivery-slip/delivery-slip', array_merge($variables, $tpls));
    }

    /**
     * @return array<int, array<string, mixed>>
     */
    private function getShipmentProducts(Shipment $shipment, Order $order, bool $displayProductImages): array
    {
        $products = [];

        foreach ($shipment->getProducts() as $shipmentProduct) {
            $orderDetail = new OrderDetail($shipmentProduct->getOrderDetailId());

            if (!Validate::isLoadedObject($orderDetail)) {
                continue;
            }

            $product = $orderDetail->getFields();
            $product['quantity'] = $shipmentProduct->getQuantity();
            $product['product_quantity'] = $shipmentProduct->getQuantity();
            $product['id_order_detail'] = $orderDetail->id;
            $product['product_id'] = $orderDetail->product_id;
            $product['product_attribute_id'] = $orderDetail->product_attribute_id;
            $product['product_name'] = $orderDetail->product_name;
            $product['product_reference'] = $orderDetail->product_reference;
            $product['product_supplier_reference'] = $orderDetail->product_supplier_reference;
            $product['product_weight'] = $orderDetail->product_weight;
            $product['product_price'] = $orderDetail->product_price;
            $product['unit_price_tax_incl'] = $orderDetail->unit_price_tax_incl;
            $product['unit_price_tax_excl'] = $orderDetail->unit_price_tax_excl;

            $product['image'] = null;
            if ($orderDetail->product_id) {
                $image = Image::getCover((int) $orderDetail->product_id);
                if ($image) {
                    $product['image'] = new Image((int) $image['id_image']);
                }
            }

            $product['image_tag'] = null;
            if ($displayProductImages && null !== $product['image']) {
                $name = 'product_mini_' . (int) $product['product_id'] . (isset($product['product_attribute_id']) ? '_' . (int) $product['product_attribute_id'] : '') . '.jpg';
                $path = _PS_PRODUCT_IMG_DIR_ . $product['image']->getExistingImgPath() . '.jpg';
                $product['image_tag'] = preg_replace(
                    '/\.*' . preg_quote(__PS_BASE_URI__, '/') . '/',
                    _PS_ROOT_DIR_ . DIRECTORY_SEPARATOR,
                    ImageManager::thumbnail($path, $name, 45, 'jpg', false),
                    1
                );
            }

            $customizedDatas = Product::getAllCustomizedDatas((int) $order->id_cart, null, true, null, (int) $product['id_customization']);
            $product['customizedDatas'] = $customizedDatas[$product['product_id']][$product['product_attribute_id']] ?? null;

            $products[] = $product;
        }

        return $products;
    }
}
