<?php
/*
* 2007-2012 PrestaShop
*
* NOTICE OF LICENSE
ing*
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
*  @author PrestaShop SA <contact@prestashop.com>
*  @copyright  2007-2012 PrestaShop SA
*  @version  Release: $Revision: 9841 $
*  @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
*  International Registered Trademark & Property of PrestaShop SA
*/

class AdminAccountingConfigurationControllerCore extends AdminController
{
	public $acc_conf = array();

	public function __construct()
	{
		parent::__construct();
		
		$this->acc_conf = Accounting::getConfiguration();
		$this->className = 'Accounting';

		$this->options = array(
			'general' => array(
				'title' =>	$this->l('Export'),
				'fields' =>	array(
					'customer_prefix' => array(
						'title' => $this->l('Customer prefix:'),
						'desc' => $this->l('Set your default customer prefix'),
						'type' => 'text',
						'value' => $this->acc_conf['customer_prefix'],
						'size' => '15',
                        'auto_value' => false
					),
					'journal' => array(
						'title' => $this->l('Journal:'),
						'desc' => '',
						'type' => 'text',
						'value' => $this->acc_conf['journal'],
						'size' => '15',
                        'auto_value' => false
					),
					'account_length' => array(
						'title' => $this->l('Customer account length:'),
						'desc' => $this->l('Set the length of the customer account number (the prefix will always be displayed with the customer id)'),
						'type' => 'text',
						'value' => $this->acc_conf['account_length'],
						'size' => '15',
                        'auto_value' => false
					)
                )
            ),

            'account_number_list' => array(
                'title' =>	$this->l('Default account number Management'),
                'fields' =>	array(
					'account_submit_shipping_charge' => array(
						'title' => $this->l('Submited shipping charge account:'),
						'desc' => $this->l('Set the account for submited shipping charged'),
						'type' => 'text',
						'value' => $this->acc_conf['account_submit_shipping_charge'],
						'size' => '15',
                        'auto_value' => false
					),
					'account_unsubmit_shipping_charge' => array(
						'title' => $this->l('Unsubmited shipping charge account:'),
						'desc' => $this->l('Set the account for unsubmited shipping charged'),
						'type' => 'text',
						'value' => $this->acc_conf['account_unsubmit_shipping_charge'],
						'size' => '15',
                        'auto_value' => false
					),
					'account_gift_wripping' => array(
						'title' => $this->l('Gift-wrapping account number:'),
						'desc' => $this->l('Set the account number for the gift-wrapping'),
						'type' => 'text',
						'value' => $this->acc_conf['account_gift_wripping'],
						'size' => '15',
                        'auto_value' => false
					),
                    'account_handling' => array(
						'title' => $this->l('Handling account number:'),
						'desc' => $this->l('Set the account number for handling'),
						'type' => 'text',
						'value' => $this->acc_conf['account_handling'],
						'size' => '15',
                        'auto_value' => false
                    )
				),
				'submit' => array('name' => 'update_cfg')
			),
		);
	}

	public function initToolbar()
	{
		$this->initToolbarTitle();
		$this->toolbar_btn['save'] = array(
			'href' => '#',
			'desc' => $this->l('Save')
		);

	}

	public function initContent()
	{
		$this->display = 'options';
		parent::initContent();

		$this->initToolbar();
		//$this->initInputList();

		$this->context->smarty->assign(array(
			'title' => $this->l('Accounting Configuration'),
			'acc_conf' => $this->acc_conf,
			'table' => 'accounting',
			'toolbar_btn' => $this->toolbar_btn
		));
	}

	public function postProcess()
	{
		if (Tools::isSubmit('update_cfg'))
		{
			foreach ($this->acc_conf as $name => $val)
				$this->acc_conf[$name] = Tools::getValue($name);

			Accounting::updateConfiguration($this->acc_conf);
			Tools::redirectAdmin(self::$currentIndex.'&token='.$this->token.'&update=true');
		}
		else if (Tools::getValue('update'))
			$this->confirmations[] = $this->l('Configuration updated');
	}
}
