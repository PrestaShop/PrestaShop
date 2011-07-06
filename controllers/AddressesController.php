<?php
/*
* 2007-2011 PrestaShop 
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
*  @copyright  2007-2011 PrestaShop SA
*  @version  Release: $Revision: 6844 $
*  @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
*  International Registered Trademark & Property of PrestaShop SA
*/

class AddressesControllerCore extends FrontController
{
	public function __construct()
	{
		$this->auth = true;
		$this->php_self = 'addresses.php';
		$this->authRedirection = 'addresses.php';
		$this->ssl = true;
	
		parent::__construct();
	}
	
	public function setMedia()
	{
		parent::setMedia();
		Tools::addCSS(_THEME_CSS_DIR_.'addresses.css');
		Tools::addJS(_THEME_JS_DIR_.'tools.js');
	}
	
	public function process()
	{
		parent::process();
		
		$multipleAddressesFormated = array();
		$ordered_fields = array();
		$customer = new Customer((int)(self::$cookie->id_customer));
		
		if (!Validate::isLoadedObject($customer))
			die(Tools::displayError('Customer not found'));
			
		// Retro Compatibility Theme < 1.4.1
		self::$smarty->assign('addresses', $customer->getAddresses((int)(self::$cookie->id_lang)));
		
		$customerAddressesDetailed = $customer->getAddresses((int)(self::$cookie->id_lang));
		
		$total = 0;
		foreach($customerAddressesDetailed as $addressDetailed)
		{
			$address = new Address($addressDetailed['id_address']);
			
			$multipleAddressesFormated[$total]['ordered'] = AddressFormat::getOrderedAddressFields($addressDetailed['id_country']);
			$multipleAddressesFormated[$total]['formated'] =  AddressFormat::getFormattedAddressFieldsValues(
				$address, 
				$multipleAddressesFormated[$total]['ordered']);
			$multipleAddressesFormated[$total]['object'] = $addressDetailed;
			unset($address);
			++$total;
			
			// Retro theme < 1.4.2
      $ordered_fields = AddressFormat::getOrderedAddressFields($addressDetailed['id_country']);
		}
		
		// Retro theme 1.4.2
    if (($key = array_search('Country:name', $ordered_fields)))
       $ordered_fields[$key] = 'country';

		self::$smarty->assign('addresses_style', array(
								'company' => 'address_company'
								,'vat_number' => 'address_company'
								,'firstname' => 'address_name'
								,'lastname' => 'address_name'
								,'address1' => 'address_address1'
								,'address2' => 'address_address2'
								,'city' => 'address_city'
								,'country' => 'address_country'
								,'phone' => 'address_phone'
								,'phone_mobile' => 'address_phone_mobile'
								,'alias' => 'address_title'
							));
							
		self::$smarty->assign(array(
			'multipleAddresses' => $multipleAddressesFormated,
			'ordered_fields' => $ordered_fields));
		unset($customer);
	}
	
	public function displayContent()
	{
		parent::displayContent();
		self::$smarty->display(_PS_THEME_DIR_.'addresses.tpl');
	}
}

