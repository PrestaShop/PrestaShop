<?php
/*
* 2007-2015 PrestaShop
*
* NOTICE OF LICENSE
*
* This source file is subject to the Academic Free License (AFL 3.0)
* that is bundled with this package in the file LICENSE.txt.
* It is also available through the world-wide-web at this URL:
* http://opensource.org/licenses/afl-3.0.php
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
*  @author PrestaShop SA <contact@prestashop.com>
*  @copyright  2007-2015 PrestaShop SA
*  @license    http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
*  International Registered Trademark & Property of PrestaShop SA
*/

use PrestaShop\PrestaShop\Core\Payment\PaymentOption;

if (!defined('_PS_VERSION_')) {
    exit;
}

class Ps_Checkpayment extends PaymentModule
{
    private $_html = '';
    private $_postErrors = array();

    public $checkName;
    public $address;
    public $extra_mail_vars;

    public function __construct()
    {
        $this->name = 'ps_checkpayment';
        $this->tab = 'payments_gateways';
        $this->version = '2.0.4';
        $this->author = 'PrestaShop';
        $this->controllers = array('payment', 'validation');

        $this->currencies = true;
        $this->currencies_mode = 'checkbox';

        $config = Configuration::getMultiple(array('CHEQUE_NAME', 'CHEQUE_ADDRESS'));
        if (isset($config['CHEQUE_NAME'])) {
            $this->checkName = $config['CHEQUE_NAME'];
        }
        if (isset($config['CHEQUE_ADDRESS'])) {
            $this->address = $config['CHEQUE_ADDRESS'];
        }

        $this->bootstrap = true;
        parent::__construct();

        $this->displayName = $this->trans('Payments by check', array(), 'Modules.Checkpayment.Admin');
        $this->description = $this->trans('This module allows you to accept payments by check.', array(), 'Modules.Checkpayment.Admin');
        $this->confirmUninstall = $this->trans('Are you sure you want to delete these details?', array(), 'Modules.Checkpayment.Admin');
        $this->ps_versions_compliancy = array('min' => '1.7.1.0', 'max' => _PS_VERSION_);

        if ((!isset($this->checkName) || !isset($this->address) || empty($this->checkName) || empty($this->address))) {
            $this->warning = $this->trans('The "Payee" and "Address" fields must be configured before using this module.', array(), 'Modules.Checkpayment.Admin');
        }
        if (!count(Currency::checkPaymentCurrencies($this->id))) {
            $this->warning = $this->trans('No currency has been set for this module.', array(), 'Modules.Checkpayment.Admin');
        }

        $this->extra_mail_vars = array(
                                    '{check_name}' => Configuration::get('CHEQUE_NAME'),
                                    '{check_address}' => Configuration::get('CHEQUE_ADDRESS'),
                                    '{check_address_html}' => Tools::nl2br(Configuration::get('CHEQUE_ADDRESS'))
                                );
    }

    public function install()
    {
        return parent::install()
            && $this->registerHook('paymentOptions')
            && $this->registerHook('paymentReturn')
        ;
    }

    public function uninstall()
    {
        return Configuration::deleteByName('CHEQUE_NAME')
            && Configuration::deleteByName('CHEQUE_ADDRESS')
            && parent::uninstall()
        ;
    }

    private function _postValidation()
    {
        if (Tools::isSubmit('btnSubmit')) {
            if (!Tools::getValue('CHEQUE_NAME')) {
                $this->_postErrors[] = $this->trans('The "Payee" field is required.', array(),'Modules.Checkpayment.Admin');
            } elseif (!Tools::getValue('CHEQUE_ADDRESS')) {
                $this->_postErrors[] = $this->trans('The "Address" field is required.', array(), 'Modules.Checkpayment.Admin');
            }
        }
    }

    private function _postProcess()
    {
        if (Tools::isSubmit('btnSubmit')) {
            Configuration::updateValue('CHEQUE_NAME', Tools::getValue('CHEQUE_NAME'));
            Configuration::updateValue('CHEQUE_ADDRESS', Tools::getValue('CHEQUE_ADDRESS'));
        }
        $this->_html .= $this->displayConfirmation($this->trans('Settings updated', array(), 'Admin.Notifications.Success'));
    }

    private function _displayCheck()
    {
        return $this->display(__FILE__, './views/templates/hook/infos.tpl');
    }

    public function getContent()
    {
        $this->_html = '';

        if (Tools::isSubmit('btnSubmit')) {
            $this->_postValidation();
            if (!count($this->_postErrors)) {
                $this->_postProcess();
            } else {
                foreach ($this->_postErrors as $err) {
                    $this->_html .= $this->displayError($err);
                }
            }
        }

        $this->_html .= $this->_displayCheck();
        $this->_html .= $this->renderForm();

        return $this->_html;
    }

    public function hookPaymentOptions($params)
    {
        if (!$this->active) {
            return;
        }
        if (!$this->checkCurrency($params['cart'])) {
            return;
        }

        $this->smarty->assign(
            $this->getTemplateVars()
        );

        $newOption = new PaymentOption();
        $newOption->setModuleName($this->name)
                ->setCallToActionText($this->trans('Pay by Check', array(), 'Modules.Checkpayment.Admin'))
                ->setAction($this->context->link->getModuleLink($this->name, 'validation', array(), true))
                ->setAdditionalInformation($this->fetch('module:ps_checkpayment/views/templates/front/payment_infos.tpl'));

        return [$newOption];
    }

    public function hookPaymentReturn($params)
    {
        if (!$this->active) {
            return;
        }

        $state = $params['order']->getCurrentState();
        if (in_array($state, array(Configuration::get('PS_OS_CHEQUE'), Configuration::get('PS_OS_OUTOFSTOCK'), Configuration::get('PS_OS_OUTOFSTOCK_UNPAID')))) {
            $this->smarty->assign(array(
                'total_to_pay' => Tools::displayPrice(
                    $params['order']->getOrdersTotalPaid(),
                    new Currency($params['order']->id_currency),
                    false
                ),
                'shop_name' => $this->context->shop->name,
                'checkName' => $this->checkName,
                'checkAddress' => Tools::nl2br($this->address),
                'status' => 'ok',
                'id_order' => $params['order']->id
            ));
            if (isset($params['order']->reference) && !empty($params['order']->reference)) {
                $this->smarty->assign('reference', $params['order']->reference);
            }
        } else {
            $this->smarty->assign('status', 'failed');
        }
        return $this->fetch('module:ps_checkpayment/views/templates/hook/payment_return.tpl');
    }

    public function checkCurrency($cart)
    {
        $currency_order = new Currency((int)($cart->id_currency));
        $currencies_module = $this->getCurrency((int)$cart->id_currency);

        if (is_array($currencies_module)) {
            foreach ($currencies_module as $currency_module) {
                if ($currency_order->id == $currency_module['id_currency']) {
                    return true;
                }
            }
        }
        return false;
    }

    public function renderForm()
    {
        $fields_form = array(
            'form' => array(
                'legend' => array(
                    'title' => $this->trans('Contact details', array(), 'Modules.Checkpayment.Admin'),
                    'icon' => 'icon-envelope'
                ),
                'input' => array(
                    array(
                        'type' => 'text',
                        'label' => $this->trans('Payee (name)', array(), 'Modules.Checkpayment.Admin'),
                        'name' => 'CHEQUE_NAME',
                        'required' => true
                    ),
                    array(
                        'type' => 'textarea',
                        'label' => $this->trans('Address', array(), 'Modules.Checkpayment.Admin'),
                        'desc' => $this->trans('Address where the check should be sent to.', array(), 'Modules.Checkpayment.Admin'),
                        'name' => 'CHEQUE_ADDRESS',
                        'required' => true
                    ),
                ),
                'submit' => array(
                    'title' => $this->trans('Save', array(), 'Admin.Actions'),
                )
            ),
        );

        $helper = new HelperForm();
        $helper->show_toolbar = false;
        $helper->id = (int)Tools::getValue('id_carrier');
        $helper->identifier = $this->identifier;
        $helper->submit_action = 'btnSubmit';
        $helper->currentIndex = $this->context->link->getAdminLink('AdminModules', false).'&configure='.$this->name.'&tab_module='.$this->tab.'&module_name='.$this->name;
        $helper->token = Tools::getAdminTokenLite('AdminModules');
        $helper->tpl_vars = array(
            'fields_value' => $this->getConfigFieldsValues(),
        );

        $this->fields_form = array();

        return $helper->generateForm(array($fields_form));
    }

    public function getConfigFieldsValues()
    {
        return array(
            'CHEQUE_NAME' => Tools::getValue('CHEQUE_NAME', Configuration::get('CHEQUE_NAME')),
            'CHEQUE_ADDRESS' => Tools::getValue('CHEQUE_ADDRESS', Configuration::get('CHEQUE_ADDRESS')),
        );
    }

    public function getTemplateVars()
    {
        $cart = $this->context->cart;
        $total = $this->trans(
            '%amount% (tax incl.)',
            array(
                '%amount%' => Tools::displayPrice($cart->getOrderTotal(true, Cart::BOTH)),
            ),
            'Modules.Checkpayment.Admin'
        );

        $checkOrder = Configuration::get('CHEQUE_NAME');
        if (!$checkOrder) {
            $checkOrder = '___________';
        }

        $checkAddress = Tools::nl2br(Configuration::get('CHEQUE_ADDRESS'));
        if (!$checkAddress) {
            $checkAddress = '___________';
        }

        return [
            'checkTotal' => $total,
            'checkOrder' => $checkOrder,
            'checkAddress' => $checkAddress,
        ];
    }
}
