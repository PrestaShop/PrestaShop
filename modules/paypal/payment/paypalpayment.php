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

class PaypalPayment extends Paypal
{
	protected $_logs = array();

	public function PayPalRound($value)
	{
		return (floor($value * 100) / 100);
	}

	public function getAuthorisation()
	{
		global $cookie, $cart;

		// Getting cart informations
		$currency = new Currency((int)($cart->id_currency));
		if (!Validate::isLoadedObject($currency))
			$this->_logs[] = $this->l('Not a valid currency');
		if (sizeof($this->_logs))
			return false;

		// Making request
		$vars = '?fromPayPal=1';
		$returnURL = Tools::getShopDomainSsl(true, true).__PS_BASE_URI__.'modules/paypal/payment/submit.php'.$vars;
		$cancelURL = Tools::getShopDomainSsl(true, true).__PS_BASE_URI__.'order.php';
		$paymentAmount = (float)($cart->getOrderTotal());
		$currencyCodeType = strval($currency->iso_code);
		$paymentType = Configuration::get('PAYPAL_CAPTURE') == 1 ? 'Authorization' : 'Sale';
		$request = '&Amt='.urlencode($paymentAmount).'&PAYMENTACTION='.urlencode($paymentType).'&ReturnUrl='.urlencode($returnURL).'&CANCELURL='.urlencode($cancelURL).'&CURRENCYCODE='.urlencode($currencyCodeType).'&NOSHIPPING=1';
		if (Configuration::get('PAYPAL_PAYMENT_METHOD') == 0)
			$request .= '&SOLUTIONTYPE=Sole&LANDINGPAGE=Billing';
		else
			$request .= '&SOLUTIONTYPE=Mark&LANDINGPAGE=Login';
		$request .= '&LOCALECODE='.strtoupper(Language::getIsoById($cart->id_lang));
		if (Configuration::get('PAYPAL_HEADER'))
			$request .= '&HDRIMG='.urlencode(Configuration::get('PAYPAL_HEADER'));
		// Customer informations
		$customer = new Customer((int)$cart->id_customer);
		$request .= '&EMAIL='.urlencode($customer->email);//customer
		// address of delivery
		$id_address = $cart->id_address_delivery;
		$address = new Address((int)$id_address);
		$country = new Country((int)$address->id_country);
		if ($address->id_state)
			$state = new State((int)$address->id_state);
		$discounts = (float)($cart->getOrderTotal(true, Cart::ONLY_DISCOUNTS));
		if ($discounts == 0)
		{
			$products = $cart->getProducts();
			$amt = 0;
			for ($i = 0; $i < sizeof($products); $i++)
			{
				$request .= '&L_NAME'.$i.'='.substr(urlencode($products[$i]['name'].(isset($products[$i]['attributes'])?' - '.$products[$i]['attributes']:'').(isset($products[$i]['instructions'])?' - '.$products[$i]['instructions']:'') ), 0, 127);
				$request .= '&L_AMT'.$i.'='.urlencode($this->PayPalRound($products[$i]['price_wt']));
				$request .= '&L_QTY'.$i.'='.urlencode($products[$i]['cart_quantity']);
				$amt += $this->PayPalRound($products[$i]['price_wt']*$products[$i]['cart_quantity']);
			}
			$shipping = $this->PayPalRound($cart->getOrderShippingCost($cart->id_carrier, false));
			$request .= '&ITEMAMT='.urlencode($amt);
			$request .= '&SHIPPINGAMT='.urlencode($shipping);
			$request .= '&TAXAMT='.urlencode((float)max($this->PayPalRound($paymentAmount - $amt - $shipping), 0));
		}
		else
		{
			$products = $cart->getProducts();
			$description = 0;
			for ($i = 0; $i < sizeof($products); $i++)
				$description .= ($description == ''?'':', ').$products[$i]['cart_quantity']." x ".$products[$i]['name'].(isset($products[$i]['attributes'])?' - '.$products[$i]['attributes']:'').(isset($products[$i]['instructions'])?' - '.$products[$i]['instructions']:'') ; 
			$request .= '&ORDERDESCRIPTION='.urlencode(substr($description, 0, 120));
		}
		$request .= '&SHIPTONAME='.urlencode($address->firstname.' '.$address->lastname);
		$request .= '&SHIPTOSTREET='.urlencode($address->address1);
		$request .= '&SHIPTOSTREET2='.urlencode($address->address2);
		$request .= '&SHIPTOCITY='.urlencode($address->city);
		$request .= '&SHIPTOSTATE='.($address->id_state ? $state->iso_code : $country->iso_code);
		$request .= '&SHIPTOZIP='.urlencode($address->postcode);
		$request .= '&SHIPTOCOUNTRY='.urlencode($country->iso_code);
		$request .= '&SHIPTOPHONENUM='.urlencode($address->phone);
		$request .= '&ADDROVERRIDE=1';

		// Calling PayPal API
		include(_PS_MODULE_DIR_.'paypal/api/paypallib.php');
		$ppAPI = new PaypalLib();
		$result = $ppAPI->makeCall($this->getAPIURL(), $this->getAPIScript(), 'SetExpressCheckout', $request);
		$this->_logs = array_merge($this->_logs, $ppAPI->getLogs());
		return $result;
	}

	public function getLogs()
	{
		return $this->_logs;
	}
}
