<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */
class HTMLTemplateOrderReturnCore extends HTMLTemplate
{
    /**
     * @var OrderReturn
     */
    public $order_return;

    /**
     * @var Order
     */
    public $order;

    /**
     * @param OrderReturn $order_return
     * @param Smarty $smarty
     *
     * @throws PrestaShopException
     */
    public function __construct(OrderReturn $order_return, Smarty $smarty)
    {
        $this->order_return = $order_return;
        $this->smarty = $smarty;
        $this->order = new Order($order_return->id_order);

        // header informations
        $this->date = Tools::displayDate($this->order->invoice_date);
        $prefix = Configuration::get('PS_RETURN_PREFIX', Context::getContext()->language->id);
        $this->title = sprintf(HTMLTemplateOrderReturn::l('%1$s%2$06d'), $prefix, $this->order_return->id);

        $this->shop = new Shop((int) $this->order->id_shop);
    }

    /**
     * Returns the template's HTML content.
     *
     * @return string HTML content
     */
    public function getContent()
    {
        $delivery_address = new Address((int) $this->order->id_address_delivery);
        $formatted_delivery_address = AddressFormat::generateAddress($delivery_address, [], '<br />', ' ');
        $formatted_invoice_address = '';

        if ($this->order->id_address_delivery != $this->order->id_address_invoice) {
            $invoice_address = new Address((int) $this->order->id_address_invoice);
            $formatted_invoice_address = AddressFormat::generateAddress($invoice_address, [], '<br />', ' ');
        }

        $this->smarty->assign([
            'order_return' => $this->order_return,
            'return_nb_days' => (int) Configuration::get('PS_ORDER_RETURN_NB_DAYS'),
            'products' => OrderReturn::getOrdersReturnProducts((int) $this->order_return->id, $this->order),
            'delivery_address' => $formatted_delivery_address,
            'invoice_address' => $formatted_invoice_address,
            'shop_address' => AddressFormat::generateAddress($this->shop->getAddress(), [], '<br />', ' '),
        ]);

        $tpls = [
            'style_tab' => $this->smarty->fetch($this->getTemplate('invoice.style-tab')),
            'addresses_tab' => $this->smarty->fetch($this->getTemplate('order-return.addresses-tab')),
            'summary_tab' => $this->smarty->fetch($this->getTemplate('order-return.summary-tab')),
            'product_tab' => $this->smarty->fetch($this->getTemplate('order-return.product-tab')),
            'conditions_tab' => $this->smarty->fetch($this->getTemplate('order-return.conditions-tab')),
        ];
        $this->smarty->assign($tpls);

        return $this->smarty->fetch($this->getTemplate('order-return'));
    }

    /**
     * Returns the template filename.
     *
     * @return string filename
     */
    public function getFilename()
    {
        return Configuration::get('PS_RETURN_PREFIX', Context::getContext()->language->id, null, $this->order->id_shop) . sprintf('%06d', $this->order_return->id) . '.pdf';
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
     * Returns the template's HTML header.
     *
     * @return string HTML header
     */
    public function getHeader()
    {
        $this->assignCommonHeaderData();
        $this->smarty->assign(['header' => Context::getContext()->getTranslator()->trans('Order return', [], 'Shop.Pdf')]);

        return $this->smarty->fetch($this->getTemplate('header'));
    }
}
