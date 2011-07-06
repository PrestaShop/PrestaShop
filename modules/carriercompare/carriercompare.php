<?php
/*
* 2007-2011 PrestaShop
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
*  @copyright  2007-2011 PrestaShop SA
*  @version  Release: $Revision: 7040 $
*  @license    http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
*  International Registered Trademark & Property of PrestaShop SA
*/

if (!defined('_CAN_LOAD_FILES_'))
	exit;

class CarrierCompare extends Module
{
	public function __construct()
	{
		$this->name = 'carriercompare';
		$this->tab = 'shipping_logistics';
		$this->version = '1.0';
		$this->author = 'PrestaShop';
		$this->need_instance = 0;

		parent::__construct();

		$this->displayName = $this->l('Shipping Estimation');
		$this->description = $this->l('Module to compare carrier possibilities before using  the checkout process');
	}

	public function install()
	{
		if (!parent::install() OR !$this->registerHook('shoppingCart') OR !$this->registerHook('header'))
			return false;
		return true;
	}

	public function hookHeader($params)
	{
		$fileName = explode(DIRECTORY_SEPARATOR, $_SERVER['PHP_SELF']);
		if ($fileName[(sizeof($fileName)-1)] != 'order.php')
			return;
		Tools::addCSS(($this->_path).'style.css', 'all');
		Tools::addJS(($this->_path).'carriercompare.js');
	}

	/*
	 ** Hook Shopping Cart Process
	 */
	public function hookShoppingCart($params)
	{
		global $cookie, $smarty, $currency;

		if ($cookie->id_customer)
			return;

		$smarty->assign(array(
			'countries' => Country::getCountries((int)$cookie->id_lang),
			'id_carrier' => ($params['cart']->id_carrier ? $params['cart']->id_carrier : Configuration::get('PS_CARRIER_DEFAULT')),
			'id_country' => (isset($cookie->id_country) ? $cookie->id_country : Configuration::get('PS_COUNTRY_DEFAULT')),
			'id_state' => (isset($cookie->id_state) ? $cookie->id_state : 0),
			'zipcode' => (isset($cookie->postcode) ? $cookie->postcode : ''),
			'currencySign' => $currency->sign,
			'currencyRate' => $currency->conversion_rate,
			'currencyFormat' => $currency->format,
			'currencyBlank' => $currency->blank
		));

		return $this->display(__FILE__, 'carriercompare.tpl');
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
	public function getCarriersListByIdZone($id_country, $id_state = 0)
	{
		global $cart, $smarty;

		$id_zone = 0;
		if ($id_state != 0)
			$id_zone = State::getIdZone($id_state);
		if (!$id_zone)
			$id_zone = Country::getIdZone($id_country);

		$carriers = Carrier::getCarriersForOrder((int)$id_zone);

		return (sizeof($carriers) ? $carriers : array());
	}

	public function saveSelection($id_country, $id_state, $zipcode, $id_carrier)
	{
		global $cart, $cookie;

		$errors = array();

		if (!Validate::isInt($id_state))
			$errors[] = $this->l('Invalid state ID');
		if ($id_state != 0 && !Validate::isLoadedObject(new State($id_state)))
			$errors[] = $this->l('Invalid state ID');
		if (!Validate::isInt($id_country) || !Validate::isLoadedObject(new Country($id_country)))
			$errors[] = $this->l('Invalid country ID');
		if (!$this->checkZipcode($zipcode, $id_country))
			$errors[] = $this->l('Please use a valid zip/postal code depending on your country selection');
		if (!Validate::isInt($id_carrier) || !Validate::isLoadedObject(new Carrier($id_carrier)))
			$errors[] = $this->l('Invalid carrier ID');

		if (sizeof($errors))
			return $errors;

		$ids_carrier = array();
		foreach (self::getCarriersListByIdZone($id_country, $id_state) as $carrier)
			$ids_carrier[] = $carrier['id_carrier'];
		if (!in_array($id_carrier, $ids_carrier))
			$errors[] = $this->l('This carrier ID isn\'t available for your selection');

		if (sizeof($errors))
			return $errors;

		$cookie->id_country = $id_country;
		$cookie->id_state = $id_state;
		$cookie->postcode = $zipcode;
		$cart->id_carrier = $id_carrier;
		if (!$cart->update())
			return array($this->l('Can\'t update the cart'));
		return array();
	}

	/*
	** Check the validity of the zipcode format depending of the country
	*/
	private function checkZipcode($zipcode, $id_country)
	{
		$zipcodeFormat = Db::getInstance(_PS_USE_SQL_SLAVE_)->getValue('
				SELECT `zip_code_format`
				FROM `'._DB_PREFIX_.'country`
				WHERE `id_country` = '.(int)$id_country);

		if (!$zipcodeFormat)
			return false;

		$regxMask = str_replace(
				array('N', 'C', 'L'),
				array(
					'[0-9]',
					Country::getIsoById((int)$id_country),
					'[a-zA-Z]'),
				$zipcodeFormat);
		if (preg_match('/'.$regxMask.'/', $zipcode))
			return true;
		return false;
	}
}

