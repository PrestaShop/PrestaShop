<?php
/*
* 2007-2012 PrestaShop
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
*  @author PrestaShop SA <contact@prestashop.com>
*  @copyright  2007-2012 PrestaShop SA
*  @version  Release: $Revision: 9841 $
*  @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
*  International Registered Trademark & Property of PrestaShop SA
*/

class AdminAccountingManagementControllerCore extends AdminController
{
	public function __construct()
	{
	 	$this->className = 'Accounting';
	 		 	
		parent::__construct();
	}
	
	public function initContent()
	{
		$shop = array();
		$error = '';
		
		if (count(Shop::getContextListShopID()) > 1)
			$error = $this->l('Please select the shop you want to configure');
		else
		{
			$this->initToolbar();

			$zones = Zone::getZones();
			$id_shop = $this->context->shop->id;

			// Set default zone value to the shop	and sort it
			foreach ($zones as $zone)
			{
					$shop['zones'][$zone['id_zone']]['name'] = $zone['name'];
					$shop['zones'][$zone['id_zone']]['account_number'] = '';
					$shop['name'] = $this->context->shop->name;
			}
			
			$shop['default_account_number'] = Configuration::get('default_account_number', null, null, $id_shop);
			ksort($shop['zones']);
			
			$zone_shop_list = Accounting::getAccountNumberZoneShop($id_shop);
	
			// Set Account number to the id_zone for the id_shop if exist
			foreach ($zone_shop_list as $zone_shop)
				$shop['zones'][$zone_shop['id_zone']]['account_number'] = $zone_shop['account_number'];
		}
		
		$this->context->smarty->assign(array(
			'shop_details' => $shop,
			'error' => $error,
			'toolbar_btn' => $this->toolbar_btn,
			'title' => $this->l('Accounting Management'),
			'table' => 'accounting'
		));
		parent::initContent();	
	}

	/**
	 * AdminController::init() override
	 * @see AdminController::postProcess()
	 */
	public function postProcess()
	{
		if (Tools::isSubmit('UpdateNumbers'))
			$this->updateAccountNumber();
	}
	
	/**
	 * assign default action in toolbar_btn smarty var, if they are not set.
	 * uses override to specifically add, modify or remove items
	 *
	 */
	public function initToolbar()
	{
		$this->initToolbarTitle();
		$this->toolbar_btn['save'] = array(
			'href' => '#',
			'desc' => $this->l('Save')
		);
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
