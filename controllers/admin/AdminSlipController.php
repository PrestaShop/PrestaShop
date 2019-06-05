<?php
/**
 * 2007-2019 PrestaShop and Contributors
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
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
 * needs please refer to https://www.prestashop.com for more information.
 *
 * @author    PrestaShop SA <contact@prestashop.com>
 * @copyright 2007-2019 PrestaShop SA and Contributors
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 * International Registered Trademark & Property of PrestaShop SA
 */

/**
 * @property OrderSlip $object
 */
class AdminSlipControllerCore extends AdminController
{
    public function __construct()
    {
        $this->bootstrap = true;
        $this->table = 'order_slip';
        $this->className = 'OrderSlip';

        $this->_select = ' o.`id_shop`';
        $this->_join .= ' LEFT JOIN ' . _DB_PREFIX_ . 'orders o ON (o.`id_order` = a.`id_order`)';
        $this->_group = ' GROUP BY a.`id_order_slip`';

        parent::__construct();

        $this->fields_list = array(
            'id_order_slip' => array(
                'title' => $this->trans('ID', array(), 'Admin.Global'),
                'align' => 'center',
                'class' => 'fixed-width-xs',
            ),
            'id_order' => array(
                'title' => $this->trans('Order ID', array(), 'Admin.Orderscustomers.Feature'),
                'align' => 'left',
                'class' => 'fixed-width-md',
                'filter_key' => 'a!id_order',
            ),
            'date_add' => array(
                'title' => $this->trans('Date issued', array(), 'Admin.Orderscustomers.Feature'),
                'type' => 'date',
                'align' => 'right',
                'filter_key' => 'a!date_add',
                'havingFilter' => true,
            ),
            'id_pdf' => array(
                'title' => $this->trans('PDF', array(), 'Admin.Global'),
                'align' => 'center',
                'callback' => 'printPDFIcons',
                'orderby' => false,
                'search' => false,
                'remove_onclick' => true, ),
        );

        $this->_select = 'a.id_order_slip AS id_pdf';
        $this->optionTitle = $this->trans('Slip', array(), 'Admin.Orderscustomers.Feature');

        $this->fields_options = array(
            'general' => array(
                'title' => $this->trans('Credit slip options', array(), 'Admin.Orderscustomers.Feature'),
                'fields' => array(
                    'PS_CREDIT_SLIP_PREFIX' => array(
                        'title' => $this->trans('Credit slip prefix', array(), 'Admin.Orderscustomers.Feature'),
                        'desc' => $this->trans('Prefix used for credit slips.', array(), 'Admin.Orderscustomers.Help'),
                        'size' => 6,
                        'type' => 'textLang',
                    ),
                ),
                'submit' => array('title' => $this->trans('Save', array(), 'Admin.Actions')),
            ),
        );

        $this->_where = Shop::addSqlRestriction(false, 'o');
    }

    public function initPageHeaderToolbar()
    {
        $this->page_header_toolbar_btn['generate_pdf'] = array(
            'href' => self::$currentIndex . '&token=' . $this->token,
            'desc' => $this->trans('Generate PDF', array(), 'Admin.Orderscustomers.Feature'),
            'icon' => 'process-icon-save-date',
        );

        parent::initPageHeaderToolbar();
    }

    public function renderForm()
    {
        $this->fields_form = array(
            'legend' => array(
                'title' => $this->trans('Print a PDF', array(), 'Admin.Orderscustomers.Feature'),
                'icon' => 'icon-print',
            ),
            'input' => array(
                array(
                    'type' => 'date',
                    'label' => $this->trans('From', array(), 'Admin.Global'),
                    'name' => 'date_from',
                    'maxlength' => 10,
                    'required' => true,
                    'hint' => $this->trans('Format: 2011-12-31 (inclusive).', array(), 'Admin.Orderscustomers.Help'),
                ),
                array(
                    'type' => 'date',
                    'label' => $this->trans('To', array(), 'Admin.Global'),
                    'name' => 'date_to',
                    'maxlength' => 10,
                    'required' => true,
                    'hint' => $this->trans('Format: 2012-12-31 (inclusive).', array(), 'Admin.Orderscustomers.Help'),
                ),
            ),
            'submit' => array(
                'title' => $this->trans('Generate PDF', array(), 'Admin.Orderscustomers.Feature'),
                'id' => 'submitPrint',
                'icon' => 'process-icon-download-alt',
            ),
        );

        $this->fields_value = array(
            'date_from' => date('Y-m-d'),
            'date_to' => date('Y-m-d'),
        );

        $this->show_toolbar = false;

        return parent::renderForm();
    }

    public function postProcess()
    {
        if (Tools::getValue('submitAddorder_slip')) {
            if (!Validate::isDate(Tools::getValue('date_from'))) {
                $this->errors[] = $this->trans('Invalid "From" date', array(), 'Admin.Orderscustomers.Notification');
            }
            if (!Validate::isDate(Tools::getValue('date_to'))) {
                $this->errors[] = $this->trans('Invalid "To" date', array(), 'Admin.Orderscustomers.Notification');
            }
            if (!count($this->errors)) {
                $order_slips = OrderSlip::getSlipsIdByDate(Tools::getValue('date_from'), Tools::getValue('date_to'));
                if (count($order_slips)) {
                    Tools::redirectAdmin($this->context->link->getAdminLink('AdminPdf') . '&submitAction=generateOrderSlipsPDF&date_from=' . urlencode(Tools::getValue('date_from')) . '&date_to=' . urlencode(Tools::getValue('date_to')));
                }
                $this->errors[] = $this->trans('No order slips were found for this period.', array(), 'Admin.Orderscustomers.Notification');
            }
        } else {
            return parent::postProcess();
        }
    }

    public function initContent()
    {
        $this->content .= $this->renderList();
        $this->content .= $this->renderForm();
        $this->content .= $this->renderOptions();

        $this->context->smarty->assign(array(
            'content' => $this->content,
        ));
    }

    public function initToolbar()
    {
        parent::initToolbar();

        $this->toolbar_btn['save-date'] = array(
            'href' => '#',
            'desc' => $this->trans('Generate PDF', array(), 'Admin.Orderscustomers.Feature'),
        );
    }

    public function printPDFIcons($id_order_slip, $tr)
    {
        $order_slip = new OrderSlip((int) $id_order_slip);
        if (!Validate::isLoadedObject($order_slip)) {
            return '';
        }

        $this->context->smarty->assign(array(
            'order_slip' => $order_slip,
            'tr' => $tr,
        ));

        return $this->createTemplate('_print_pdf_icon.tpl')->fetch();
    }
}
