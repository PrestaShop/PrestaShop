<?php
/**
 * 2007-2017 PrestaShop
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@prestashop.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade PrestaShop to newer
 * versions in the future. If you wish to customize PrestaShop for your
 * needs please refer to http://www.prestashop.com for more information.
 *
 * @author    PrestaShop SA <contact@prestashop.com>
 * @copyright 2007-2017 PrestaShop SA
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * International Registered Trademark & Property of PrestaShop SA
 */

class AdminInvoicesControllerCore extends AdminController
{
    public function __construct()
    {
        $this->bootstrap = true;
        $this->table = 'invoice';

        parent::__construct();

        $this->fields_options = array(
            'general' => array(
                'title' =>    $this->trans('Invoice options', array(), 'Admin.Orderscustomers.Feature'),
                'fields' =>    array(
                    'PS_INVOICE' => array(
                        'title' => $this->trans('Enable invoices', array(), 'Admin.Orderscustomers.Feature'),
                        'desc' => $this->trans('If enabled, your customers will receive an invoice for the purchase.', array(), 'Admin.Orderscustomers.Help'),
                        'cast' => 'intval',
                        'type' => 'bool'
                    ),
                    'PS_INVOICE_TAXES_BREAKDOWN' => array(
                        'title' => $this->trans('Enable tax breakdown', array(), 'Admin.Orderscustomers.Feature'),
                        'desc' => $this->trans('If required, show the total amount per rate of the corresponding tax.', array(), 'Admin.Orderscustomers.Help'),
                        'cast' => 'intval',
                        'type' => 'bool'
                    ),
                    'PS_PDF_IMG_INVOICE' => array(
                        'title' => $this->trans('Enable product image', array(), 'Admin.Orderscustomers.Feature'),
                        'hint' => $this->trans('Adds an image in front of the product name on the invoice', array(), 'Admin.Orderscustomers.Help'),
                        'validation' => 'isBool',
                        'cast' => 'intval',
                        'type' => 'bool'
                    ),
                    'PS_INVOICE_PREFIX' => array(
                        'title' => $this->trans('Invoice prefix', array(), 'Admin.Orderscustomers.Feature'),
                        'desc' => $this->trans('Freely definable prefix for invoice number (e.g. #IN00001).', array(), 'Admin.Orderscustomers.Help'),
                        'size' => 6,
                        'type' => 'textLang'
                    ),
                    'PS_INVOICE_USE_YEAR' => array(
                        'title' => $this->trans('Add current year to invoice number', array(), 'Admin.Orderscustomers.Feature'),
                        'cast' => 'intval',
                        'type' => 'bool'
                    ),
                    'PS_INVOICE_RESET' => array(
                        'title' => $this->trans('Reset sequential invoice number at the beginning of the year', array(), 'Admin.Orderscustomers.Feature'),
                        'cast' => 'intval',
                        'type' => 'bool'
                    ),
                    'PS_INVOICE_YEAR_POS' => array(
                        'title' => $this->trans('Position of the year date', array(), 'Admin.Orderscustomers.Feature'),
                        'cast' => 'intval',
                        'show' => true,
                        'required' => false,
                        'type' => 'radio',
                        'validation' => 'isBool',
                        'choices' => array(
                            0 => $this->trans('After the sequential number', array(), 'Admin.Orderscustomers.Feature'),
                            1 => $this->trans('Before the sequential number', array(), 'Admin.Orderscustomers.Feature')
                        )
                    ),
                    'PS_INVOICE_START_NUMBER' => array(
                        'title' => $this->trans('Invoice number', array(), 'Admin.Orderscustomers.Feature'),
                        'desc' => $this->trans(
                            'The next invoice will begin with this number, and then increase with each additional invoice. Set to 0 if you want to keep the current number (which is #%number%).',
                            array(
                                '%number%' => Order::getLastInvoiceNumber() + 1
                            ),
                            'Admin.Orderscustomers.Help'
                        ),
                        'size' => 6,
                        'type' => 'text',
                        'cast' => 'intval'
                    ),
                    'PS_INVOICE_LEGAL_FREE_TEXT' => array(
                        'title' => $this->trans('Legal free text', array(), 'Admin.Orderscustomers.Feature'),
                        'desc' => $this->trans('Use this field to show additional information on the invoice, below the payment methods summary (like specific legal information).', array(), 'Admin.Orderscustomers.Help'),
                        'size' => 50,
                        'type' => 'textareaLang',
                    ),
                    'PS_INVOICE_FREE_TEXT' => array(
                        'title' => $this->trans('Footer text', array(), 'Admin.Orderscustomers.Feature'),
                        'desc' => $this->trans('This text will appear at the bottom of the invoice, below your company details.', array(), 'Admin.Orderscustomers.Help'),
                        'size' => 50,
                        'type' => 'textLang',
                    ),
                    'PS_INVOICE_MODEL' => array(
                        'title' => $this->trans('Invoice model', array(), 'Admin.Orderscustomers.Feature'),
                        'desc' => $this->trans('Choose an invoice model.', array(), 'Admin.Orderscustomers.Help'),
                        'type' => 'select',
                        'identifier' => 'value',
                        'list' => $this->getInvoicesModels()
                    ),
                    'PS_PDF_USE_CACHE' => array(
                        'title' => $this->trans('Use the disk as cache for PDF invoices', array(), 'Admin.Orderscustomers.Feature'),
                        'desc' => $this->trans('Saves memory but slows down the PDF generation.', array(), 'Admin.Orderscustomers.Help'),
                        'validation' => 'isBool',
                        'cast' => 'intval',
                        'type' => 'bool'
                    )
                ),
                'submit' => array('title' => $this->trans('Save', array(), 'Admin.Actions'))
            )
        );
    }

    public function initFormByDate()
    {
        $this->fields_form = array(
            'legend' => array(
                'title' => $this->trans('By date', array(), 'Admin.Orderscustomers.Feature'),
                'icon' => 'icon-calendar'
            ),
            'input' => array(
                array(
                    'type' => 'date',
                    'label' => $this->trans('From', array(), 'Admin.Global'),
                    'name' => 'date_from',
                    'maxlength' => 10,
                    'required' => true,
                    'hint' => $this->trans('Format: 2011-12-31 (inclusive).', array(), 'Admin.Orderscustomers.Help')
                ),
                array(
                    'type' => 'date',
                    'label' => $this->trans('To', array(), 'Admin.Global'),
                    'name' => 'date_to',
                    'maxlength' => 10,
                    'required' => true,
                    'hint' => $this->trans('Format: 2012-12-31 (inclusive).', array(), 'Admin.Orderscustomers.Help')
                )
            ),
            'submit' => array(
                'title' => $this->trans('Generate PDF file by date', array(), 'Admin.Orderscustomers.Feature'),
                'id' => 'submitPrint',
                'icon' => 'process-icon-download-alt'
            )
        );

        $this->fields_value = array(
            'date_from' => date('Y-m-d'),
            'date_to' => date('Y-m-d')
        );

        $this->table = 'invoice_date';
        $this->show_toolbar = false;
        $this->show_form_cancel_button = false;
        $this->toolbar_title = $this->trans('Print PDF invoices', array(), 'Admin.Orderscustomers.Feature');
        return parent::renderForm();
    }

    public function initFormByStatus()
    {
        $this->fields_form = array(
            'legend' => array(
                'title' => $this->trans('By order status', array(), 'Admin.Orderscustomers.Feature'),
                'icon' => 'icon-time'
            ),
            'input' => array(
                array(
                    'type' => 'checkboxStatuses',
                    'label' => $this->trans('Order statuses', array(), 'Admin.Orderscustomers.Feature'),
                    'name' => 'id_order_state',
                    'values' => array(
                        'query' => OrderState::getOrderStates($this->context->language->id),
                        'id' => 'id_order_state',
                        'name' => 'name'
                    ),
                    'hint' => $this->trans('You can also export orders which have not been charged yet.', array(), 'Admin.Orderscustomers.Help')
                )
            ),
            'submit' => array(
                'title' => $this->trans('Generate PDF file by status', array(), 'Admin.Orderscustomers.Feature'),
                'id' => 'submitPrint2',
                'icon' => 'process-icon-download-alt'
            )
        );

        $result = Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS('
			SELECT COUNT( o.id_order ) AS nbOrders, o.current_state as id_order_state
			FROM `'._DB_PREFIX_.'order_invoice` oi
			LEFT JOIN `'._DB_PREFIX_.'orders` o ON oi.id_order = o.id_order
			WHERE o.id_shop IN('.implode(', ', Shop::getContextListShopID()).')
			AND oi.number > 0
			GROUP BY o.current_state
		 ');

        $status_stats = array();
        foreach ($result as $row) {
            $status_stats[$row['id_order_state']] = $row['nbOrders'];
        }

        $this->tpl_form_vars = array(
            'statusStats' => $status_stats,
            'style' => ''
        );

        $this->table = 'invoice_status';
        $this->show_toolbar = false;
        return parent::renderForm();
    }

    public function initContent()
    {
        $this->display = 'edit';

        $this->content .= $this->initFormByDate();
        $this->content .= $this->initFormByStatus();

        $this->table = 'invoice';

        $this->content .= $this->renderOptions();

        $this->context->smarty->assign(array(
            'content' => $this->content,
        ));
    }

    public function initToolbarTitle()
    {
        $this->toolbar_title = array_unique($this->breadcrumbs);
    }

    public function initPageHeaderToolbar()
    {
        parent::initPageHeaderToolbar();
        unset($this->page_header_toolbar_btn['cancel']);
    }

    public function postProcess()
    {
        if (Tools::isSubmit('submitAddinvoice_date')) {
            if (!Validate::isDate(Tools::getValue('date_from'))) {
                $this->errors[] = $this->trans('Invalid "From" date', array(), 'Admin.Orderscustomers.Notification');
            }

            if (!Validate::isDate(Tools::getValue('date_to'))) {
                $this->errors[] = $this->trans('Invalid "To" date', array(), 'Admin.Orderscustomers.Notification');
            }

            if (!count($this->errors)) {
                if (count(OrderInvoice::getByDateInterval(Tools::getValue('date_from'), Tools::getValue('date_to')))) {
                    Tools::redirectAdmin($this->context->link->getAdminLink('AdminPdf').'&submitAction=generateInvoicesPDF&date_from='.urlencode(Tools::getValue('date_from')).'&date_to='.urlencode(Tools::getValue('date_to')));
                }

                $this->errors[] = $this->trans('No invoice has been found for this period.', array(), 'Admin.Orderscustomers.Notification');
            }
        } elseif (Tools::isSubmit('submitAddinvoice_status')) {
            if (!is_array($status_array = Tools::getValue('id_order_state')) || !count($status_array)) {
                $this->errors[] = $this->trans('You must select at least one order status.', array(), 'Admin.Orderscustomers.Notification');
            } else {
                foreach ($status_array as $id_order_state) {
                    if (count(OrderInvoice::getByStatus((int)$id_order_state))) {
                        Tools::redirectAdmin($this->context->link->getAdminLink('AdminPdf').'&submitAction=generateInvoicesPDF2&id_order_state='.implode('-', $status_array));
                    }
                }

                $this->errors[] = $this->trans('No invoice has been found for this status.', array(), 'Admin.Orderscustomers.Notification');
            }
        } else {
            parent::postProcess();
        }
    }

    public function beforeUpdateOptions()
    {
        if ((int)Tools::getValue('PS_INVOICE_START_NUMBER') != 0 && (int)Tools::getValue('PS_INVOICE_START_NUMBER') <= Order::getLastInvoiceNumber()) {
            $this->errors[] = $this->trans('Invalid invoice number.', array(), 'Admin.Orderscustomers.Notification').Order::getLastInvoiceNumber().')';
        }
    }

    protected function getInvoicesModels()
    {
        $models = array(
            array(
                'value' => 'invoice',
                'name' => 'invoice'
            )
        );

        $templates_override = $this->getInvoicesModelsFromDir(_PS_THEME_DIR_.'pdf/');
        $templates_default = $this->getInvoicesModelsFromDir(_PS_PDF_DIR_);

        foreach (array_merge($templates_default, $templates_override) as $template) {
            $template_name = basename($template, '.tpl');
            $models[] = array('value' => $template_name, 'name' => $template_name);
        }
        return $models;
    }

    protected function getInvoicesModelsFromDir($directory)
    {
        $templates = false;

        if (is_dir($directory)) {
            $templates = glob($directory.'invoice-*.tpl');
        }

        if (!$templates) {
            $templates = array();
        }

        return $templates;
    }
}
