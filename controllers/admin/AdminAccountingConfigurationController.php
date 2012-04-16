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

	public $fields_list_detail = array();

	public function __construct()
	{
		parent::__construct();
		
		$this->acc_conf = Accounting::getConfiguration();
		$this->className = 'Accounting';
	}

	public function initToolbar()
	{
		$this->initToolbarTitle();
		$this->toolbar_btn['save'] = array(
			'href' => '#',
			'desc' => $this->l('Save')
		);
	}

	public function initAccountZoneShop()
	{
		$zones = Zone::getZones();
		$id_shop = $this->context->shop->id;
		$fields_option = array();

		// Set default zone value to the shop	and sort it
		foreach ($zones as $zone)
			$fields_option['zone_'.$zone['id_zone']] = array(
				'title' => $zone['name'],
				'type' => 'text',
				'value' => '',
				'size' => '15',
				'id' => 'zone_'.$zone['id_zone'],
				'name' => 'zone_'.$zone['id_zone'],
				'auto_value' => false
			);

		ksort($fields_option);
		$zone_shop_list = Accounting::getAccountNumberZoneShop($id_shop);

		$this->fields_list_detail['zone'] = array(
			'title' =>	$this->l('Account number by zone'),
			'fields' =>	array(
				'default_account_number' => array(
					'title' => $this->l('Default number for this shop'),
					'desc' => $this->l('If a zone field is empty it will use this default number'),
					'type' => 'text',
					'value' => Configuration::get('default_account_number', null, null, $id_shop),
					'size' => '15',
					'auto_value' => false
				))
		);

		// Set Account number to the id_zone for the id_shop if exist
		foreach ($zone_shop_list as $zone_shop)
			$fields_option['zone_'.$zone_shop['id_zone']]['value'] = $zone_shop['account_number'];

		$this->fields_list_detail['zone']['fields'] = array_merge($this->fields_list_detail['zone']['fields'], $fields_option);
	}

	public function initAccountingForm()
	{
		// Only text type available for this configuration, handle new missing type in the tpl file (as the options.tpl helper file)
		$this->fields_list_detail = array(
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

		$this->initAccountZoneShop();
	}

	public function initContent()
	{
		$this->display = 'options';

		$this->initToolbar();

		$this->initAccountingForm();

		$this->context->smarty->assign(array(
			'title' => $this->l('Accounting Configuration'),
			'acc_conf' => $this->acc_conf,
			'input_category_list' => $this->fields_list_detail,
			'table' => 'accounting',
			'has_shop_selected' => (count(Shop::getContextListShopID()) == 1),
			'toolbar_btn' => $this->toolbar_btn,
			'show_toolbar' => $this->show_toolbar,
			'toolbar_scroll' => $this->toolbar_scroll
		));
		parent::initContent();
	}

	public function postProcess()
	{
		if (Tools::isSubmit('update_cfg'))
		{
			foreach ($this->acc_conf as $name => $val)
				$this->acc_conf[$name] = Tools::getValue($name);

			Accounting::updateConfiguration($this->acc_conf);

			$this->updateAccountNumber();
			Tools::redirectAdmin(self::$currentIndex.'&token='.$this->token.'&update=true');
		}
		else if (Tools::getValue('update'))
			$this->confirmations[] = $this->l('Configuration updated');
	}

	/**
	 * Update the account number for each shop liable to their zones
	 */
	protected function updateAccountNumber()
	{
		$id_shop = $this->context->shop->id;

		// Update the current default shop account number
		Configuration::updateValue(
			'default_account_number',
			Tools::getValue('default_account_number'),
			false, null,
			$id_shop);

		// If zone still exist, then update the database with the new value
		if (count($zones = Zone::getZones()))
		{
			$tab = array();
			foreach ($zones as $zone)
				if (($num = Tools::getValue('zone_'.$zone['id_zone'])) !== null)
					$tab[] = array(
						'id_zone' => $zone['id_zone'],
						'id_shop' => $id_shop,
						'num' => $num);

			// Save to the database the account
			if (count($tab) && Accounting::setAccountNumberByZoneShop($tab))
				$this->confirmations[] = $this->l('Account numbers have been updated');
			else
				$this->errors[] = $this->l('Account Numbers could not be updated or added in the database');
		}
	}
}
