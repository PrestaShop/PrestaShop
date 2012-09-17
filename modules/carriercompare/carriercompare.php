<?php
/*
* 2007-2012 PrestaShop
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
*  @copyright  2007-2012 PrestaShop SA
*  @version  Release: $Revision: 7040 $
*  @license    http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
*  International Registered Trademark & Property of PrestaShop SA
*/

if (!defined('_PS_VERSION_'))
	exit;

class CarrierCompare extends Module
{
	public $template_directory = '';
	public $smarty;
	
	public function __construct()
	{
		$this->name = 'carriercompare';
		$this->tab = 'shipping_logistics';
		$this->version = '1.2';
		$this->author = 'PrestaShop';
		$this->need_instance = 0;

		parent::__construct();

		$this->displayName = $this->l('Shipping Estimation');
		$this->description = $this->l('Module to compare carrier possibilities before using  the checkout process');
		$this->template_directory = dirname(__FILE__).'/template/';
		$this->initRetroCompatibilityVar();
	}
	
	// Retro-compatibiliy 1.4/1.5
	private function initRetroCompatibilityVar()
	{			
		if (class_exists('Context'))
			$smarty = Context::getContext()->smarty;
		else
			global $smarty;
		
		$this->smarty = $smarty;
	}

	public function install()
	{
		if (!parent::install() OR !$this->registerHook('shoppingCart') OR !$this->registerHook('header'))
			return false;
		return true;
	}
	
	public function getContent()
	{
		if (!empty($_POST))
			$this->postProcess();
		
		$this->smarty->assign('refresh_method', Configuration::get('SE_RERESH_METHOD'));
		return $this->smarty->fetch($this->template_directory .'configuration.tpl');
	}
	
	public function postProcess()
	{
		$errors = array();
		
		if (Tools::isSubmit('setGlobalConfiguration'))
		{
			$method = (int)Tools::getValue('refresh_method');
			Configuration::updateValue('SE_RERESH_METHOD', $method);
		}
		
		$this->smarty->assign(array(
			'display_error' => count($errors) ? $errors : false));
	}

	public function hookHeader($params)
	{
		if (!$this->isModuleAvailable())
			return;
		$this->context->controller->addCSS(($this->_path).'style.css', 'all');
		$this->context->controller->addJS(($this->_path).'carriercompare.js');
	}

	/*
	 ** Hook Shopping Cart Process
	 */
	public function hookShoppingCart($params)
	{
		if (!$this->isModuleAvailable())
			return;
		
		$protocol = (Configuration::get('PS_SSL_ENABLED') || (!empty($_SERVER['HTTPS']) 
			&& strtolower($_SERVER['HTTPS']) != 'off')) ? 'https://' : 'http://';
		
		$endURL = __PS_BASE_URI__.'modules/carriercompare/';
	
		if (method_exists('Tools', 'getShopDomainSsl'))
			$moduleURL = $protocol.Tools::getShopDomainSsl().$endURL;
		else
			$moduleURL = $protocol.$_SERVER['HTTP_HOST'].$endURL;
		
		$refresh_method = Configuration::get('SE_RERESH_METHOD');
		
		$this->smarty->assign(array(
			'countries' => Country::getCountries((int)$this->context->cookie->id_lang, true),
			'id_carrier' => ($params['cart']->id_carrier ? $params['cart']->id_carrier : Configuration::get('PS_CARRIER_DEFAULT')),
			'id_country' => (isset($this->context->customer->geoloc_id_country) ? $this->context->customer->geoloc_id_country : Configuration::get('PS_COUNTRY_DEFAULT')),
			'id_state' => (isset($this->context->customer->geoloc_id_state) ? $this->context->customer->geoloc_id_state : 0),
			'zipcode' => (isset($this->context->customer->geoloc_postcode) ? $this->context->customer->geoloc_postcode : ''),
			'currencySign' => $this->context->currency->sign,
			'currencyRate' => $this->context->currency->conversion_rate,
			'currencyFormat' => $this->context->currency->format,
			'currencyBlank' => $this->context->currency->blank,
			'new_base_dir' => $moduleURL,
			'refresh_method' => ($refresh_method === false) ? 0 : $refresh_method
		));

		return $this->smarty->fetch($this->template_directory.'carriercompare.tpl');
	}

	/*
	** Get states by Country id, called by the ajax process
	** id_state allow to preselect the selection option
	*/
	public function getStatesByIdCountry($id_country, $id_state = '')
	{
		$states = State::getStatesByIdCountry($id_country);

		return (sizeof($states) ? $states : array());
	}

	/*
	** Get carriers by country id, called by the ajax process
	*/
	public function getCarriersListByIdZone($id_country, $id_state = 0, $zipcode = 0)
	{
		// cookie saving/updating
		$this->context->cookie->id_country = $id_country;
		if ($id_state != 0)
			$this->context->cookie->id_state = $id_state;
		if ($zipcode != 0)
			$this->context->cookie->postcode = $zipcode;

		$id_zone = 0;
		if ($id_state != 0)
			$id_zone = State::getIdZone($id_state);
		if (!$id_zone)
			$id_zone = Country::getIdZone($id_country);
		
		// Need to set the infos for carrier module !
		$this->context->cookie->id_country = $id_country;
		$this->context->cookie->id_state = $id_state;
		$this->context->cookie->postcode = $zipcode;

		$carriers = Carrier::getCarriersForOrder((int)$id_zone);
		
		return (sizeof($carriers) ? $carriers : array());
	}

	public function saveSelection($id_country, $id_state, $zipcode, $id_carrier)
	{
		$errors = array();

		if (!Validate::isInt($id_state))
			$errors[] = $this->l('Invalid state ID');
		if ($id_state != 0 && !Validate::isLoadedObject(new State($id_state)))
			$errors[] = $this->l('Please select a state');
		if (!Validate::isInt($id_country) || !Validate::isLoadedObject(new Country($id_country)))
			$errors[] = $this->l('Please select a country');
		if (!$this->checkZipcode($zipcode, $id_country))
			$errors[] = $this->l('Please use a valid zip/postal code depending on your country selection');
		if (!Validate::isInt($id_carrier) || !Validate::isLoadedObject(new Carrier($id_carrier)))
			$errors[] = $this->l('Please select a carrier');

		if (sizeof($errors))
			return $errors;

		$ids_carrier = array();
		foreach (self::getCarriersListByIdZone($id_country, $id_state, $zipcode) as $carrier)
			$ids_carrier[] = $carrier['id_carrier'];
		if (!in_array($id_carrier, $ids_carrier))
			$errors[] = $this->l('This carrier ID isn\'t available for your selection');

		if (sizeof($errors))
			return $errors;

		$this->context->cookie->id_country = $id_country;
		$this->context->cookie->id_state = $id_state;
		$this->context->cookie->postcode = $zipcode;
		$this->context->cart->id_carrier = $id_carrier;
		if (!$this->context->cart->update())
			return array($this->l('Cannot update the cart'));
		return array();
	}

	/*
	** Check the validity of the zipcode format depending of the country
	*/
	private function checkZipcode($zipcode, $id_country)
	{
		$country = new Country((int)$id_country);
		if (!Validate::isLoadedObject($country))
			return true;
		$zipcodeFormat = Db::getInstance(_PS_USE_SQL_SLAVE_)->getValue('
				SELECT `zip_code_format`
				FROM `'._DB_PREFIX_.'country`
				WHERE `id_country` = '.(int)$id_country);

		if (!$country->need_zip_code || !$country->zip_code_format)
			return true;

		$regxMask = str_replace(
				array('N', 'C', 'L'),
				array(
					'[0-9]',
					$country->iso_code,
					'[a-zA-Z]'),
				$country->zip_code_format);
		if (preg_match('/'.$regxMask.'/', $zipcode))
			return true;
		return false;
	}

	/**
	 * This module is shown on front office, in only some conditions
	 * @return bool
	 */
	private function isModuleAvailable()
	{
		$fileName = basename($_SERVER['SCRIPT_FILENAME']);
		/**
		 * This module is only available on standard order process because
		 * on One Page Checkout the carrier list is already available.
		 */
		if (Configuration::get('PS_ORDER_PROCESS_TYPE') == 1)
			return false;
		/**
		 * If visitor is logged, the module isn't available on Front office,
		 * we use the account informations for carrier selection and taxes.
		 */
		if (Context::getContext()->customer->id)
			return false;
		return true;
}
}

