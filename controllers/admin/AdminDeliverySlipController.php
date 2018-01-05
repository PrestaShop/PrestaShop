<?php
/**
 * 2007-2017 PrestaShop
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
 * needs please refer to http://www.prestashop.com for more information.
 *
 * @author    PrestaShop SA <contact@prestashop.com>
 * @copyright 2007-2017 PrestaShop SA
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 * International Registered Trademark & Property of PrestaShop SA
 */

class AdminDeliverySlipControllerCore extends AdminController
{
    public function __construct()
    {
        $this->bootstrap = true;
        $this->table = 'delivery';

        parent::__construct();

        $this->fields_options = array(
            'general' => array(
                'title' =>    $this->trans('Delivery slip options', array(), 'Admin.Orderscustomers.Feature'),
                'fields' =>    array(
                    'PS_DELIVERY_PREFIX' => array(
                        'title' => $this->trans('Delivery prefix', array(), 'Admin.Orderscustomers.Feature'),
                        'desc' => $this->trans('Prefix used for delivery slips.', array(), 'Admin.Orderscustomers.Help'),
                        'type' => 'textLang'
                    ),
                    'PS_DELIVERY_NUMBER' => array(
                        'title' => $this->trans('Delivery number', array(), 'Admin.Orderscustomers.Feature'),
                        'desc' => $this->trans('The next delivery slip will begin with this number and then increase with each additional slip.', array(), 'Admin.Orderscustomers.Help'),
                        'cast' => 'intval',
                        'type' => 'text'
                    ),
                    'PS_PDF_IMG_DELIVERY' => array(
                        'title' => $this->trans('Enable product image', array(), 'Admin.Orderscustomers.Feature'),
                        'hint' => $this->trans('Adds an image before product name on Delivery-slip', array(), 'Admin.Orderscustomers.Help'),
                        'validation' => 'isBool',
                        'cast' => 'intval',
                        'type' => 'bool'
                    ),
                ),
                'submit' => array('title' => $this->trans('Save', array(), 'Admin.Actions'))
            )
        );
    }

    public function renderForm()
    {
        $this->fields_form = array(
            'legend' => array(
                'title' => $this->trans('Print PDF', array(), 'Admin.Orderscustomers.Feature'),
                'icon' => 'icon-print'
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
                'title' => $this->trans('Generate PDF', array(), 'Admin.Orderscustomers.Feature'),
                'icon' => 'process-icon-download-alt'
            )
        );

        $this->fields_value = array(
            'date_from' => date('Y-m-d'),
            'date_to' => date('Y-m-d')
        );

        return parent::renderForm();
    }

    public function postProcess()
    {
        if (Tools::isSubmit('submitAdddelivery')) {
            if (!Validate::isDate(Tools::getValue('date_from'))) {
                $this->errors[] = $this->trans('Invalid \'from\' date', array(), 'Admin.Catalog.Notification');
            }
            if (!Validate::isDate(Tools::getValue('date_to'))) {
                $this->errors[] = $this->trans('Invalid \'to\' date', array(), 'Admin.Catalog.Notification');
            }
            if (!count($this->errors)) {
                if (count(OrderInvoice::getByDeliveryDateInterval(Tools::getValue('date_from'), Tools::getValue('date_to')))) {
                    Tools::redirectAdmin($this->context->link->getAdminLink('AdminPdf').'&submitAction=generateDeliverySlipsPDF&date_from='.urlencode(Tools::getValue('date_from')).'&date_to='.urlencode(Tools::getValue('date_to')));
                } else {
                    $this->errors[] = $this->trans('No delivery slip was found for this period.', array(), 'Admin.Orderscustomers.Notification');
                }
            }
        } else {
            parent::postProcess();
        }
    }

    public function initContent()
    {
        $this->show_toolbar = false;

        $this->content .= $this->renderForm();
        $this->content .= $this->renderOptions();

        $this->context->smarty->assign(array(
            'content' => $this->content,
        ));
    }
}
