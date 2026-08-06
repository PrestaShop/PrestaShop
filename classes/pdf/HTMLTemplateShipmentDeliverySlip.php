<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

use PrestaShop\PrestaShop\Core\Domain\Shipment\ValueObject\DeliverySlipNumber;
use PrestaShop\PrestaShop\Core\Util\Sorter;
use PrestaShopBundle\Entity\Shipment;

class HTMLTemplateShipmentDeliverySlipCore extends HTMLTemplate
{
    /**
     * @var Order
     */
    public $order;

    /**
     * @var Shipment
     */
    public $shipment;

    /**
     * @var OrderInvoice|null Order invoice for address information
     */
    public $order_invoice;

    /**
     * @param array{
     *     shipment: Shipment,
     *     order: Order,
     *     order_invoice_collection: PrestaShopCollection,
     * } $shipmentData
     *
     * @throws PrestaShopException
     */
    public function __construct(array $shipmentData, Smarty $smarty)
    {
        if (!isset($shipmentData['shipment']) || !is_array($shipmentData)) {
            $errorMessage = Context::getContext()->getTranslator()->trans('Invalid shipment data provided to HTMLTemplateShipmentDeliverySlip');
            throw new PrestaShopException($errorMessage);
        }

        $this->shipment = $shipmentData['shipment'];
        $this->order = $shipmentData['order'];
        $orderInvoiceCollection = $shipmentData['order_invoice_collection'];

        // Get the first invoice for address information (fallback to order addresses if no invoice)
        $first = $orderInvoiceCollection->getFirst();

        if ($first && $first instanceof OrderInvoice) {
            $this->order_invoice = $first;
        } else {
            $this->order_invoice = null;
        }

        $this->smarty = $smarty;

        // Use shipment's shipped_at date, fallback to packed_at, then order date
        $shipmentDate = $this->shipment->getShippedAt() ?? $this->shipment->getPackedAt();
        if ($shipmentDate) {
            $this->date = Tools::displayDate($shipmentDate->format('Y-m-d'));
        } else {
            $this->date = Tools::displayDate($this->order->date_add);
        }

        // Use delivery slip prefix and shipment ID as the number
        $prefix = Configuration::get('PS_DELIVERY_PREFIX', Context::getContext()->language->id);

        $this->title = DeliverySlipNumber::format(
            $prefix,
            $this->order->id,
            $this->shipment->getId()
        );

        // footer informations
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
        $this->smarty->assign(['header' => Context::getContext()->getTranslator()->trans('Delivery', [], 'Shop.Pdf')]);

        return $this->smarty->fetch($this->getTemplate('header'));
    }

    /**
     * Returns the template's HTML content.
     *
     * @return string HTML content
     */
    public function getContent()
    {
        // Get delivery address from shipment's addressId
        $delivery_address = new Address($this->shipment->getAddressId());
        $formatted_delivery_address = AddressFormat::generateAddress($delivery_address, [], '<br />', ' ');
        $formatted_invoice_address = '';

        // Get invoice address from order
        if ($this->order->id_address_delivery != $this->order->id_address_invoice) {
            $invoice_address = new Address((int) $this->order->id_address_invoice);
            $formatted_invoice_address = AddressFormat::generateAddress($invoice_address, [], '<br />', ' ');
        }

        // Get carrier from shipment
        $carrier = new Carrier($this->shipment->getCarrierId());
        $carrier->name = (empty($carrier->name) ? Configuration::get('PS_SHOP_NAME') : $carrier->name);

        // Get products from shipment
        $products = $this->getShipmentProducts();

        if (Configuration::get('PS_PDF_IMG_DELIVERY')) {
            foreach ($products as &$product) {
                if ($product['image'] != null) {
                    $name = 'product_mini_' . (int) $product['product_id'] . (isset($product['product_attribute_id']) ? '_' . (int) $product['product_attribute_id'] : '') . '.jpg';
                    $path = _PS_PRODUCT_IMG_DIR_ . $product['image']->getExistingImgPath() . '.jpg';

                    $product['image_tag'] = preg_replace(
                        '/\.*' . preg_quote(__PS_BASE_URI__, '/') . '/',
                        _PS_ROOT_DIR_ . DIRECTORY_SEPARATOR,
                        ImageManager::thumbnail($path, $name, 45, 'jpg', false),
                        1
                    );

                    if (file_exists(_PS_TMP_IMG_DIR_ . $name)) {
                        $product['image_size'] = getimagesize(_PS_TMP_IMG_DIR_ . $name);
                    } else {
                        $product['image_size'] = false;
                    }
                }
            }
        }

        // Sort products by Reference ID (and if equals (like combination) by Supplier Reference)
        $sorter = new Sorter();
        $products = $sorter->natural($products, Sorter::ORDER_DESC, 'product_reference', 'product_supplier_reference');

        foreach ($products as &$product) {
            $customized_datas = Product::getAllCustomizedDatas($this->order->id_cart, null, true, null, (int) $product['id_customization']);
            $product['customizedDatas'] = null;

            if (isset($customized_datas[$product['product_id']][$product['product_attribute_id']])) {
                $product['customizedDatas'] = $customized_datas[$product['product_id']][$product['product_attribute_id']];
            }
        }
        unset($product);

        $this->smarty->assign([
            'order' => $this->order,
            'products' => $products,
            'delivery_address' => $formatted_delivery_address,
            'invoice_address' => $formatted_invoice_address,
            'order_invoice' => $this->order_invoice,
            'shipment' => $this->shipment,
            'carrier' => $carrier,
            'tracking_number' => $this->shipment->getTrackingNumber(),
            'display_product_images' => Configuration::get('PS_PDF_IMG_DELIVERY'),
        ]);

        $tpls = [
            'style_tab' => $this->smarty->fetch($this->getTemplate('delivery-slip.style-tab')),
            'addresses_tab' => $this->smarty->fetch($this->getTemplate('delivery-slip.addresses-tab')),
            'summary_tab' => $this->smarty->fetch($this->getTemplate('shipment-delivery-slip.summary-tab')),
            'product_tab' => $this->smarty->fetch($this->getTemplate('shipment-delivery-slip.product-tab')),
        ];
        $this->smarty->assign($tpls);

        return $this->smarty->fetch($this->getTemplate('delivery-slip'));
    }

    /**
     * Get products from shipment entity
     *
     * @return array{
     *     quantity: int,
     *     product_quantity: int,
     *     id_order_detail: int,
     *     product_id: int,
     *     product_attribute_id: int,
     *     product_name: string,
     *     product_reference: string,
     *     product_supplier_reference: string,
     *     product_weight: float,
     *     product_price: float,
     *     unit_price_tax_incl: float,
     *     unit_price_tax_excl: float,
     *     image: Image|null,
     * }[]
     */
    protected function getShipmentProducts()
    {
        $products = [];
        $shipmentProducts = $this->shipment->getProducts();

        foreach ($shipmentProducts as $shipmentProduct) {
            $orderDetail = new OrderDetail($shipmentProduct->getOrderDetailId());

            if (Validate::isLoadedObject($orderDetail)) {
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

                // Get product image
                $product['image'] = null;
                if ($orderDetail->product_id) {
                    $image = Image::getCover($orderDetail->product_id);
                    if ($image) {
                        $product['image'] = new Image($image['id_image']);
                    }
                }

                $products[] = $product;
            }
        }

        return $products;
    }

    /**
     * Returns the template filename when using bulk rendering.
     *
     * @return string filename
     */
    public function getBulkFilename()
    {
        return 'shipment-deliveries.pdf';
    }

    /**
     * Returns the template filename.
     *
     * @return string filename
     */
    public function getFilename()
    {
        return Configuration::get('PS_DELIVERY_PREFIX', Context::getContext()->language->id, null, $this->order->id_shop)
            . DeliverySlipNumber::format(
                '',
                $this->order->id,
                $this->shipment->getId()
            ) . '.pdf';
    }
}
