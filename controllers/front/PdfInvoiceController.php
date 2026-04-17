<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */
class PdfInvoiceControllerCore extends FrontController
{
    /** @var string */
    public $php_self = 'pdf-invoice';
    /** @var bool */
    protected $display_header = false;
    /** @var bool */
    protected $display_footer = false;
    /** @var bool */
    public $content_only = true;
    /** @var string */
    protected $template = '';
    public $filename;
    /** @var Order */
    public $order;

    public function postProcess(): void
    {
        // If the customer is not logged in AND no secure key was passed
        if (!$this->context->customer->isLogged() && !Tools::getValue('secure_key')) {
            Tools::redirect($this->context->link->getPageLink(
                'authentication',
                null,
                null,
                ['back' => 'pdf-invoice']
            ));
        }

        // If built-in invoicing is disabled
        if (!(int) Configuration::get('PS_INVOICE')) {
            die($this->trans('Invoices are disabled in this shop.', [], 'Shop.Notifications.Error'));
        }

        $id_order = (int) Tools::getValue('id_order');
        if (Validate::isUnsignedId($id_order)) {
            $order = new Order((int) $id_order);
        }

        // If the order doesn't exist
        if (!isset($order) || !Validate::isLoadedObject($order)) {
            die($this->trans('The invoice was not found.', [], 'Shop.Notifications.Error'));
        }

        // Check if the user is not trying to download an invoice of an order of different customer
        // Either the ID of the customer in context must match the customer in order OR a secure_key matching the one on the order must be provided
        if (Tools::isSubmit('secure_key') && $order->secure_key != Tools::getValue('secure_key')) {
            die($this->trans('The invoice was not found.', [], 'Shop.Notifications.Error'));
        }

        if (!Tools::isSubmit('secure_key') && (!isset($this->context->customer->id) || $order->id_customer != $this->context->customer->id)) {
            die($this->trans('The invoice was not found.', [], 'Shop.Notifications.Error'));
        }

        if (!OrderState::invoiceAvailable($order->getCurrentState()) && !$order->invoice_number) {
            die($this->trans('No invoice is available.', [], 'Shop.Notifications.Error'));
        }

        $this->order = $order;
    }

    /**
     * @return void
     *
     * @throws PrestaShopException
     */
    public function display(): void
    {
        $order_invoice_list = $this->order->getInvoicesCollection();
        Hook::exec('actionPDFInvoiceRender', ['order_invoice_list' => $order_invoice_list]);

        $pdf = new PDF($order_invoice_list, PDF::TEMPLATE_INVOICE, $this->context->smarty);
        $pdf->render();
    }
}
