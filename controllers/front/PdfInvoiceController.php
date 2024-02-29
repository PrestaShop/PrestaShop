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

    public function postProcess()
    {
        // If the customer is not logged in AND no secure key was passed
        if (!$this->context->customer->isLogged() && !Tools::getValue('secure_key')) {
            Tools::redirect('index.php?controller=authentication&back=pdf-invoice');
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
        if ((isset($this->context->customer->id) && $order->id_customer != $this->context->customer->id) && (Tools::isSubmit('secure_key') && $order->secure_key != Tools::getValue('secure_key'))) {
            die($this->trans('The invoice was not found.', [], 'Shop.Notifications.Error'));
        }

        if (!OrderState::invoiceAvailable($order->getCurrentState()) && !$order->invoice_number) {
            die($this->trans('No invoice is available.', [], 'Shop.Notifications.Error'));
        }

        $this->order = $order;
    }

    /**
     * @return bool|void
     *
     * @throws PrestaShopException
     */
    public function display()
    {
        $order_invoice_list = $this->order->getInvoicesCollection();
        Hook::exec('actionPDFInvoiceRender', ['order_invoice_list' => $order_invoice_list]);

        $pdf = new PDF($order_invoice_list, PDF::TEMPLATE_INVOICE, $this->context->smarty);
        $pdf->render();
    }
}
