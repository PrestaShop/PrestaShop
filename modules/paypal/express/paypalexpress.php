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
*  @version  Release: $Revision: 1.4 $
*  @license    http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
*  International Registered Trademark & Property of PrestaShop SA
*/

class PaypalExpress extends Paypal
{
	protected $_logs = array();

	public function getAuthorisation()
	{
		global $cookie;

		// Getting cart informations
		$cart = new Cart((int)($cookie->id_cart));
		if (!Validate::isLoadedObject($cart))
			$this->_logs[] = $this->l('Not a valid cart');
		$currency = new Currency((int)($cart->id_currency));
		if (!Validate::isLoadedObject($currency))
			$this->_logs[] = $this->l('Not a valid currency');

		if (sizeof($this->_logs))
			return false;

		// Making request
		$returnURL = Tools::getShopDomainSsl(true, true).__PS_BASE_URI__.'modules/paypal/express/submit.php';
		$cancelURL = Tools::getShopDomainSsl(true, true).__PS_BASE_URI__.'order.php';
		$paymentAmount = (float)($cart->getOrderTotal());
		$currencyCodeType = strval($currency->iso_code);
		$paymentType = Configuration::get('PAYPAL_CAPTURE') == 1 ? 'Authorization' : 'Sale';
		$request = '&Amt='.urlencode($paymentAmount).'&PAYMENTACTION='.urlencode($paymentType).'&ReturnUrl='.urlencode($returnURL).'&CANCELURL='.urlencode($cancelURL).'&CURRENCYCODE='.urlencode($currencyCodeType);
		if ($this->_pp_integral)
			$request .= '&SOLUTIONTYPE=Sole&LANDINGPAGE=Billing';
		else
			$request .= '&SOLUTIONTYPE=Mark&LANDINGPAGE=Login';
		$request .= '&LOCALECODE='.strtoupper($this->getCountryCode());
		if ($this->_header) $request .= '&HDRIMG='.urlencode($this->_header);
		// Customer informations
		$customer = new Customer((int)$cart->id_customer);
		$request .= '&EMAIL='.urlencode($customer->email);//customer
		// address of delivery
		$address = new Address((int)$cart->id_address_delivery);
		$country = new Country((int)$address->id_country);
		if ($address->id_state)
			$state = new State((int)$address->id_state);
		$request .= '&SHIPTONAME='.urlencode($address->firstname.' '.$address->lastname);
		$request .= '&SHIPTOSTREET='.urlencode($address->address1);
		$request .= '&SHIPTOSTREET2='.urlencode($address->address2);
		$request .= '&SHIPTOCITY='.urlencode($address->city);
		$request .= '&SHIPTOSTATE='.($address->id_state ? $state->iso_code : $country->iso_code);
		$request .= '&SHIPTOZIP='.urlencode($address->postcode);
		$request .= '&SHIPTOCOUNTRY='.urlencode($country->iso_code);
		$request .= '&SHIPTOPHONENUM='.urlencode($address->phone);

		// Calling PayPal API
		include(_PS_MODULE_DIR_.'paypal/api/paypallib.php');
		$ppAPI = new PaypalLib();
		$result = $ppAPI->makeCall($this->getAPIURL(), $this->getAPIScript(), 'SetExpressCheckout', $request);
		$this->_logs = array_merge($this->_logs, $ppAPI->getLogs());
		return $result;
	}

	public function getCustomerInfos()
	{
		global $cookie;

		// Making request
		$request = '&TOKEN='.urlencode(strval($cookie->paypal_token));

		// Calling PayPal API
		include(_PS_MODULE_DIR_.'paypal/api/paypallib.php');
		$ppAPI = new PaypalLib();
		$result = $ppAPI->makeCall($this->getAPIURL(), $this->getAPIScript(), 'GetExpressCheckoutDetails', $request);
		$this->_logs = array_merge($this->_logs, $ppAPI->getLogs());
		return $result;
	}

	public function getLogs()
	{
		return $this->_logs;
	}
}
