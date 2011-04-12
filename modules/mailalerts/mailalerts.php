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

if (!defined('_CAN_LOAD_FILES_'))
	exit;

class MailAlerts extends Module
{
	private $_html = '';
	private $_postErrors = array();

	private $_merchant_mails;
	private $_merchant_order;
	private $_merchant_oos;
	private $_customer_qty;

	const __MA_MAIL_DELIMITOR__ = ',';

	public function __construct()
	{
		$this->name = 'mailalerts';
		$this->tab = 'administration';
		$this->version = '2.2';
		$this->author = 'PrestaShop';

		parent::__construct();
		
		if ($this->id)
			$this->_refreshProperties();

		$this->displayName = $this->l('Mail alerts');
		$this->description = $this->l('Sends e-mail notifications to customers and merchants.');
		$this->confirmUninstall = $this->l('Are you sure you want to delete all customer notifications?');
	}

	public function install()
	{
		if (!parent::install() OR
			!$this->registerHook('newOrder') OR
			!$this->registerHook('updateQuantity') OR
			!$this->registerHook('productOutOfStock') OR
			!$this->registerHook('customerAccount') OR
			!$this->registerHook('updateProduct') OR
			!$this->registerHook('deleteProduct') OR
			!$this->registerHook('deleteProductAttribute') OR
			!$this->registerHook('updateProductAttribute')
		)
			return false;

		Configuration::updateValue('MA_MERCHANT_ORDER', 1);
		Configuration::updateValue('MA_MERCHANT_OOS', 1);
		Configuration::updateValue('MA_CUSTOMER_QTY', 1);
		Configuration::updateValue('MA_MERCHANT_MAILS', Configuration::get('PS_SHOP_EMAIL'));
		Configuration::updateValue('MA_LAST_QTIES', Configuration::get('PS_LAST_QTIES'));

		if (!Db::getInstance()->Execute('
			CREATE TABLE IF NOT EXISTS `'._DB_PREFIX_.'mailalert_customer_oos` (
				`id_customer` int(10) unsigned NOT NULL,
				`customer_email` varchar(128) NOT NULL,
				`id_product` int(10) unsigned NOT NULL,
				`id_product_attribute` int(10) unsigned NOT NULL,
				PRIMARY KEY  (`id_customer`,`customer_email`,`id_product`,`id_product_attribute`)
			) ENGINE='._MYSQL_ENGINE_.' DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci')
		)
	 		return false;

		/* This hook is optional */
		$this->registerHook('myAccountBlock');
		return true;
	}

	public function uninstall()
	{
		Configuration::deleteByName('MA_MERCHANT_ORDER');
		Configuration::deleteByName('MA_MERCHANT_OOS');
		Configuration::deleteByName('MA_CUSTOMER_QTY');
		Configuration::deleteByName('MA_MERCHANT_MAILS');
		Configuration::deleteByName('MA_LAST_QTIES');
	 	if (!Db::getInstance()->Execute('DROP TABLE '._DB_PREFIX_.'mailalert_customer_oos'))
	 		return false;
		return parent::uninstall();
	}
	
	private function _refreshProperties()
	{
		$this->_merchant_mails = Configuration::get('MA_MERCHANT_MAILS');
		$this->_merchant_order = (int)Configuration::get('MA_MERCHANT_ORDER');
		$this->_merchant_oos = (int)Configuration::get('MA_MERCHANT_OOS');
		$this->_customer_qty = (int)Configuration::get('MA_CUSTOMER_QTY');
	}

	public function hookNewOrder($params)
	{
		if (!$this->_merchant_order OR empty($this->_merchant_mails))
			return;

		// Getting differents vars
		$id_lang = (int)(Configuration::get('PS_LANG_DEFAULT'));
	 	$currency = $params['currency'];
		$configuration = Configuration::getMultiple(array('PS_SHOP_EMAIL', 'PS_MAIL_METHOD', 'PS_MAIL_SERVER', 'PS_MAIL_USER', 'PS_MAIL_PASSWD', 'PS_SHOP_NAME'));
		$order = $params['order'];
		$customer = $params['customer'];
		$delivery = new Address((int)($order->id_address_delivery));
		$invoice = new Address((int)($order->id_address_invoice));
		$order_date_text = Tools::displayDate($order->date_add, (int)($id_lang));
		$carrier = new Carrier((int)($order->id_carrier));
		$message = $order->getFirstMessage();
		if (!$message OR empty($message))
			$message = $this->l('No message');

		$itemsTable = '';
		foreach ($params['order']->getProducts() AS $key => $product)
		{
			$unit_price = $product['product_price_wt'];
			$price = $product['total_price'];
			$itemsTable .=
				'<tr style="background-color:'.($key % 2 ? '#DDE2E6' : '#EBECEE').';">
					<td style="padding:0.6em 0.4em;">'.$product['product_reference'].'</td>
					<td style="padding:0.6em 0.4em;"><strong>'.$product['product_name'].(isset($product['attributes_small']) ? ' '.$product['attributes_small'] : '').'</strong></td>
					<td style="padding:0.6em 0.4em; text-align:right;">'.Tools::displayPrice($unit_price, $currency, false, false).'</td>
					<td style="padding:0.6em 0.4em; text-align:center;">'.(int)($product['product_quantity']).'</td>
					<td style="padding:0.6em 0.4em; text-align:right;">'.Tools::displayPrice(($unit_price * $product['product_quantity']), $currency, false, false).'</td>
				</tr>';
		}
		foreach ($params['order']->getDiscounts() AS $discount)
		{
			$itemsTable .=
			'<tr style="background-color:#EBECEE;">
					<td colspan="4" style="padding:0.6em 0.4em; text-align:right;">'.$this->l('Voucher code:').' '.$discount['name'].'</td>
					<td style="padding:0.6em 0.4em; text-align:right;">-'.Tools::displayPrice($discount['value'], $currency, false, false).'</td>
			</tr>';
		}
		if ($delivery->id_state)
			$delivery_state = new State((int)($delivery->id_state));
		if ($invoice->id_state)
			$invoice_state = new State((int)($invoice->id_state));

		// Filling-in vars for email
		$template = 'new_order';
		$subject = $this->l('New order');
		$templateVars = array(
			'{firstname}' => $customer->firstname,
			'{lastname}' => $customer->lastname,
			'{email}' => $customer->email,
			'{delivery_company}' => $delivery->company,
			'{delivery_firstname}' => $delivery->firstname,
			'{delivery_lastname}' => $delivery->lastname,
			'{delivery_address1}' => $delivery->address1,
			'{delivery_address2}' => $delivery->address2,
			'{delivery_city}' => $delivery->city,
			'{delivery_postal_code}' => $delivery->postcode,
			'{delivery_country}' => $delivery->country,
			'{delivery_state}' => $delivery->id_state ? $delivery_state->name : '',
			'{delivery_phone}' => $delivery->phone,
			'{delivery_other}' => $delivery->other,
			'{invoice_company}' => $invoice->company,
			'{invoice_firstname}' => $invoice->firstname,
			'{invoice_lastname}' => $invoice->lastname,
			'{invoice_address2}' => $invoice->address2,
			'{invoice_address1}' => $invoice->address1,
			'{invoice_city}' => $invoice->city,
			'{invoice_postal_code}' => $invoice->postcode,
			'{invoice_country}' => $invoice->country,
			'{invoice_state}' => $invoice->id_state ? $invoice_state->name : '',
			'{invoice_phone}' => $invoice->phone,
			'{invoice_other}' => $invoice->other,
			'{order_name}' => sprintf("%06d", $order->id),
			'{shop_name}' => Configuration::get('PS_SHOP_NAME'),
			'{date}' => $order_date_text,
			'{carrier}' => (($carrier->name == '0') ? Configuration::get('PS_SHOP_NAME') : $carrier->name),
			'{payment}' => $order->payment,
			'{items}' => $itemsTable,
			'{total_paid}' => Tools::displayPrice($order->total_paid, $currency),
			'{total_products}' => Tools::displayPrice($order->getTotalProductsWithTaxes(), $currency),
			'{total_discounts}' => Tools::displayPrice($order->total_discounts, $currency),
			'{total_shipping}' => Tools::displayPrice($order->total_shipping, $currency),
			'{total_wrapping}' => Tools::displayPrice($order->total_wrapping, $currency),
			'{currency}' => $currency->sign,
			'{message}' => $message
		);
		$iso = Language::getIsoById((int)($id_lang));
		if (file_exists(dirname(__FILE__).'/mails/'.$iso.'/'.$template.'.txt') AND file_exists(dirname(__FILE__).'/mails/'.$iso.'/'.$template.'.html'))
			Mail::Send($id_lang, $template, $subject, $templateVars, explode(self::__MA_MAIL_DELIMITOR__, $this->_merchant_mails), NULL, $configuration['PS_SHOP_EMAIL'], $configuration['PS_SHOP_NAME'], NULL, NULL, dirname(__FILE__).'/mails/');
	}

	public function hookProductOutOfStock($params)
	{
		global $smarty, $cookie;

		if (!$this->_customer_qty)
			return ;

		$id_product = (int)($params['product']->id);
		$id_product_attribute = 0;

		if (!$cookie->isLogged())
			$smarty->assign('email', 1);
		else
		{
			$id_customer = (int)($params['cookie']->id_customer);
			if ($this->customerHasNotification($id_customer, $id_product, $id_product_attribute))
				return ;
		}

		$smarty->assign(array(
			'id_product' => $id_product,
			'id_product_attribute' => $id_product_attribute));

		return $this->display(__FILE__, 'product.tpl');
	}

	public function customerHasNotification($id_customer, $id_product, $id_product_attribute)
	{
		$result = Db::getInstance(_PS_USE_SQL_SLAVE_)->ExecuteS('
			SELECT * 
			FROM `'._DB_PREFIX_.'mailalert_customer_oos` 
			WHERE `id_customer` = '.(int)($id_customer).' 
			AND `id_product` = '.(int)($id_product).' 
			AND `id_product_attribute` = '.(int)($id_product_attribute));
		return sizeof($result);
	}

	public function hookUpdateQuantity($params)
	{
		global $cookie;
		
		if (is_object($params['product']))
			$params['product'] = get_object_vars($params['product']);
			
		if (isset($params['product']['id_product']))
			$params['product']['id'] = (int)$params['product']['id_product'];
		
		$qty = (int)$params['product']['stock_quantity'];
		if ($qty <= (int)(Configuration::get('MA_LAST_QTIES')) AND !(!$this->_merchant_oos OR empty($this->_merchant_mails)) AND Configuration::get('PS_STOCK_MANAGEMENT'))
		{
			$templateVars = array(
				'{qty}' => $qty,
				'{last_qty}' => (int)(Configuration::get('MA_LAST_QTIES')),
				'{product}' => strval($params['product']['name']).(isset($params['product']['attributes_small']) ? ' '.$params['product']['attributes_small'] : ''));
			$id_lang = (is_object($cookie) AND isset($cookie->id_lang)) ? (int)$cookie->id_lang : (int)Configuration::get('PS_LANG_DEFAULT');
			$iso = Language::getIsoById((int)$id_lang);
			if (file_exists(dirname(__FILE__).'/mails/'.$iso.'/productoutofstock.txt') AND file_exists(dirname(__FILE__).'/mails/'.$iso.'/productoutofstock.html'))
				Mail::Send((int)Configuration::get('PS_LANG_DEFAULT'), 'productoutofstock', Mail::l('Product out of stock'), $templateVars, explode(self::__MA_MAIL_DELIMITOR__, $this->_merchant_mails), NULL, strval(Configuration::get('PS_SHOP_EMAIL')), strval(Configuration::get('PS_SHOP_NAME')), NULL, NULL, dirname(__FILE__).'/mails/');
		}
		
		if ($this->_customer_qty AND $params['product']['quantity'] > 0)
			$this->sendCustomerAlert((int)$params['product']['id_product'], 0);
	}

	public function hookUpdateProduct($params)
	{
		/* We specify 0 as an id_product_attribute because this hook is called when the main product is updated */
		if ($this->_customer_qty AND $params['product']->quantity > 0)
			$this->sendCustomerAlert((int)$params['product']->id, 0);
	}

	public function hookUpdateProductAttribute($params)
	{
		$result = Db::getInstance()->getRow('
		SELECT `id_product`, `quantity` 
		FROM `'._DB_PREFIX_.'product_attribute` 
		WHERE `id_product_attribute` = '.(int)$params['id_product_attribute']);

		if ($this->_customer_qty AND $result['quantity'] > 0)
			$this->sendCustomerAlert((int)$result['id_product'], (int)$params['id_product_attribute']);
	}
	
	public function sendCustomerAlert($id_product, $id_product_attribute)
	{
		global $cookie, $link;

		$link = new Link();
		
		$customers = Db::getInstance(_PS_USE_SQL_SLAVE_)->ExecuteS('
		SELECT id_customer, customer_email
		FROM `'._DB_PREFIX_.'mailalert_customer_oos`
		WHERE `id_product` = '.(int)$id_product.' AND `id_product_attribute` = '.(int)$id_product_attribute);
		
		$product = new Product((int)$id_product, false, (int)$cookie->id_lang);
		$templateVars = array(
			'{product}' => (is_array($product->name) ? $product->name[(int)Configuration::get('PS_LANG_DEFAULT')] : $product->name),
			'{product_link}' => $link->getProductLink($product)
		);
		foreach ($customers AS $cust)
		{
			if ($cust['id_customer'])
			{
				$customer = new Customer((int)$cust['id_customer']);
				$customer_email = $customer->email;
				$customer_id = (int)$customer->id;
			}
			else
			{
				$customer_email = $cust['customer_email'];
				$customer_id = 0;
			}
			$id_lang = (is_object($cookie) AND isset($cookie->id_lang)) ? (int)$cookie->id_lang : (int)Configuration::get('PS_LANG_DEFAULT');
			$iso = Language::getIsoById((int)$id_lang);
			
			if (file_exists(dirname(__FILE__).'/mails/'.$iso.'/customer_qty.txt') AND file_exists(dirname(__FILE__).'/mails/'.$iso.'/customer_qty.html'))
				Mail::Send((int)(Configuration::get('PS_LANG_DEFAULT')), 'customer_qty', Mail::l('Product available'), $templateVars, strval($customer_email), NULL, strval(Configuration::get('PS_SHOP_EMAIL')), strval(Configuration::get('PS_SHOP_NAME')), NULL, NULL, dirname(__FILE__).'/mails/');
			if ($customer_id)
				$customer_email = 0;
			self::deleteAlert((int)$customer_id, strval($customer_email), (int)$id_product, (int)$id_product_attribute);
		}
	}
	
	public function hookCustomerAccount($params)
	{
		return $this->_customer_qty ? $this->display(__FILE__, 'my-account.tpl') : NULL;
	}
	
	public function hookMyAccountBlock($params)
	{
		return $this->hookCustomerAccount($params);
	}

	public function getContent()
	{
		$this->_html = '<h2>'.$this->displayName.'</h2>';
		$this->_postProcess();
		$this->_displayForm();
		return $this->_html;
	}

	private function _displayForm()
	{
		global $currentIndex;

		$tab = Tools::getValue('tab');
		$token = Tools::getValue('token');

		$this->_html .= '
		<form action="'.$currentIndex.'&token='.$token.'&configure=mailalerts" method="post">
			<fieldset class="width3"><legend><img src="'.$this->_path.'logo.gif" />'.$this->l('Customer notification').'</legend>
				<label>'.$this->l('Product availability:').' </label>
				<div class="margin-form">
					<input type="checkbox" value="1" id="mA_customer_qty" name="mA_customer_qty" '.(Tools::getValue('mA_customer_qty', $this->_customer_qty) == 1 ? 'checked' : '').'>
					&nbsp;<label for="mA_customer_qty" class="t">'.$this->l('Gives the customer the option of receiving a notification for an available product if this one is out of stock.').'</label>
				</div>
				<div class="margin-form">
					<input type="submit" value="'.$this->l('   Save   ').'" name="submitMACustomer" class="button" />
				</div>
			</fieldset>
		</form>
		<br />
		<form action="'.$currentIndex.'&token='.$token.'&configure=mailalerts" method="post">
			<fieldset class="width3"><legend><img src="'.$this->_path.'logo.gif" />'.$this->l('Merchant notification').'</legend>
				<label>'.$this->l('New order:').' </label>
				<div class="margin-form">
					<input type="checkbox" value="1" id="mA_merchand_order" name="mA_merchand_order" '.(Tools::getValue('mA_merchand_order', $this->_merchant_order) == 1 ? 'checked' : '').'>
					&nbsp;<label for="mA_merchand_order" class="t">'.$this->l('Receive a notification if a new order is made').'</label>
				</div>
				<label>'.$this->l('Out of stock:').' </label>
				<div class="margin-form">
					<input type="checkbox" value="1" id="mA_merchand_oos" name="mA_merchand_oos" '.(Tools::getValue('mA_merchand_oos', $this->_merchant_oos) == 1 ? 'checked' : '').'>
					&nbsp;<label for="mA_merchand_oos" class="t">'.$this->l('Receive a notification if the quantity of a product is below the alert threshold').'</label>
				</div>
				<label>'.$this->l('Alert threshold:').'</label>
				<div class="margin-form">
					<input type="text" name="MA_LAST_QTIES" value="'.(Tools::getValue('MA_LAST_QTIES') != NULL ? (int)(Tools::getValue('MA_LAST_QTIES')) : Configuration::get('MA_LAST_QTIES')).'" size="3" />
					<p>'.$this->l('Quantity for which a product is regarded as out of stock').'</p>
				</div>
				<label>'.$this->l('Send to these e-mail addresses:').' </label>
				<div class="margin-form">
					<div style="float:left; margin-right:10px;">
						<textarea name="ma_merchant_mails" rows="10" cols="30">'.Tools::getValue('ma_merchant_mails', str_replace(self::__MA_MAIL_DELIMITOR__, "\n", $this->_merchant_mails)).'</textarea>
					</div>
					<div style="float:left;">
						'.$this->l('One e-mail address per line').'<br />
						'.$this->l('e.g.,').' bob@example.com
					</div>
				</div>
				<div style="clear:both;">&nbsp;</div>
				<div class="margin-form">
					<input type="submit" value="'.$this->l('   Save   ').'" name="submitMAMerchant" class="button" />
				</div>
			</fieldset>
		</form>';
	}

	private function _postProcess()
	{
		if (Tools::isSubmit('submitMACustomer'))
		{
			if (!Configuration::updateValue('MA_CUSTOMER_QTY', (int)(Tools::getValue('mA_customer_qty'))))
				$this->_html .= '<div class="alert error">'.$this->l('Cannot update settings').'</div>';
			else
				$this->_html .= '<div class="conf confirm"><img src="../img/admin/ok.gif" alt="'.$this->l('Confirmation').'" />'.$this->l('Settings updated').'</div>';
		}
		elseif (Tools::isSubmit('submitMAMerchant'))
		{
			$emails = strval(Tools::getValue('ma_merchant_mails'));
			if (!$emails OR empty($emails))
				$this->_html .= '<div class="alert error">'.$this->l('Please type one (or more) e-mail address').'</div>';
			else
			{
				$emails = explode("\n", $emails);
				foreach ($emails AS $k => $email)
				{
					$email = trim($email);
					if (!empty($email) AND !Validate::isEmail($email))
						return ($this->_html .= '<div class="alert error">'.$this->l('Invalid e-mail:').' '.$email.'</div>');
					if (!empty($email) AND sizeof($email))
						$emails[$k] = $email;
					else
						unset($emails[$k]);
				}
				$emails = implode(self::__MA_MAIL_DELIMITOR__, $emails);
				if (!Configuration::updateValue('MA_MERCHANT_MAILS', strval($emails)))
					$this->_html .= '<div class="alert error">'.$this->l('Cannot update settings').'</div>';
				elseif (!Configuration::updateValue('MA_MERCHANT_ORDER', (int)(Tools::getValue('mA_merchand_order'))))
					$this->_html .= '<div class="alert error">'.$this->l('Cannot update settings').'</div>';
				elseif (!Configuration::updateValue('MA_MERCHANT_OOS', (int)(Tools::getValue('mA_merchand_oos'))))
					$this->_html .= '<div class="alert error">'.$this->l('Cannot update settings').'</div>';
				elseif (!Configuration::updateValue('MA_LAST_QTIES', (int)(Tools::getValue('MA_LAST_QTIES'))))
					$this->_html .= '<div class="alert error">'.$this->l('Cannot update settings').'</div>';
				else
					$this->_html .= '<div class="conf confirm"><img src="../img/admin/ok.gif" alt="'.$this->l('Confirmation').'" />'.$this->l('Settings updated').'</div>';
			}
		}
		$this->_refreshProperties();
	}

	static public function getProductsAlerts($id_customer, $id_lang)
	{
		if (!Validate::isUnsignedId($id_customer) OR
			!Validate::isUnsignedId($id_lang)
		)
			die (Tools::displayError());

		$products = Db::getInstance(_PS_USE_SQL_SLAVE_)->ExecuteS('
			SELECT ma.`id_product`, p.`quantity` AS product_quantity, pl.`name`, ma.`id_product_attribute`
			FROM `'._DB_PREFIX_.'mailalert_customer_oos` ma
			JOIN `'._DB_PREFIX_.'product` p ON p.`id_product` = ma.`id_product`
			JOIN `'._DB_PREFIX_.'product_lang` pl ON pl.`id_product` = ma.`id_product`
			WHERE ma.`id_customer` = '.(int)($id_customer).'
			AND pl.`id_lang` = '.(int)($id_lang));
		if (empty($products) === true OR !sizeof($products))
			return array();
		for ($i = 0; $i < sizeof($products); ++$i)
		{
			$obj = new Product((int)($products[$i]['id_product']), false, (int)($id_lang));
			if (!Validate::isLoadedObject($obj))
				continue;

			if (isset($products[$i]['id_product_attribute']) AND
				Validate::isUnsignedInt($products[$i]['id_product_attribute']))
			{
				$result = Db::getInstance(_PS_USE_SQL_SLAVE_)->ExecuteS('
					SELECT al.`name` AS attribute_name
					FROM `'._DB_PREFIX_.'product_attribute_combination` pac
					LEFT JOIN `'._DB_PREFIX_.'attribute` a ON (a.`id_attribute` = pac.`id_attribute`)
					LEFT JOIN `'._DB_PREFIX_.'attribute_group` ag ON (ag.`id_attribute_group` = a.`id_attribute_group`)
					LEFT JOIN `'._DB_PREFIX_.'attribute_lang` al ON (a.`id_attribute` = al.`id_attribute` AND al.`id_lang` = '.(int)($id_lang).')
					LEFT JOIN `'._DB_PREFIX_.'attribute_group_lang` agl ON (ag.`id_attribute_group` = agl.`id_attribute_group` AND agl.`id_lang` = '.(int)($id_lang).')
					LEFT JOIN `'._DB_PREFIX_.'product_attribute` pa ON (pac.`id_product_attribute` = pa.`id_product_attribute`)
					WHERE pac.`id_product_attribute` = '.(int)($products[$i]['id_product_attribute']));
				$products[$i]['attributes_small'] = '';
				if ($result)
					foreach ($result AS $k => $row)
						$products[$i]['attributes_small'] .= $row['attribute_name'].', ';
				$products[$i]['attributes_small'] = rtrim($products[$i]['attributes_small'], ', ');
				
				// cover
				$attrgrps = $obj->getAttributesGroups((int)($id_lang));
				foreach ($attrgrps AS $attrgrp)
					if ($attrgrp['id_product_attribute'] == (int)($products[$i]['id_product_attribute']) AND $images = Product::_getAttributeImageAssociations((int)($attrgrp['id_product_attribute'])))
					{
						$products[$i]['cover'] = $obj->id.'-'.array_pop($images);
						break;
					}
			}
			if (!isset($products[$i]['cover']) OR !$products[$i]['cover'])
			{
				$images = $obj->getImages((int)($id_lang));
				foreach ($images AS $k => $image)
					if ($image['cover'])
					{
						$products[$i]['cover'] = $obj->id.'-'.$image['id_image'];
						break;
					}
			}
			if (!isset($products[$i]['cover']))
				$products[$i]['cover'] = Language::getIsoById($id_lang).'-default';
			$products[$i]['link'] = $obj->getLink();
		}
		return ($products);
	}

	static public function deleteAlert($id_customer, $customer_email, $id_product, $id_product_attribute)
	{
		return Db::getInstance()->Execute('
			DELETE FROM `'._DB_PREFIX_.'mailalert_customer_oos` 
			WHERE `id_customer` = '.(int)($id_customer).'
			AND `customer_email` = \''.pSQL($customer_email).'\'
			AND `id_product` = '.(int)($id_product).'
			AND `id_product_attribute` = '.(int)($id_product_attribute));
	}
	
	public function hookDeleteProduct($params)
	{
		Db::getInstance()->Execute('DELETE FROM `'._DB_PREFIX_.'mailalert_customer_oos` WHERE `id_product` = '.(int)$params['product']->id);
	}
	
	public function hookDeleteProductAttribute($params)
	{
		if ($params['deleteAllAttributes'])
			Db::getInstance()->Execute('DELETE FROM `'._DB_PREFIX_.'mailalert_customer_oos`
			WHERE `id_product` = '.(int)$params['id_product']);
		else
			Db::getInstance()->Execute('DELETE FROM `'._DB_PREFIX_.'mailalert_customer_oos`
			WHERE `id_product_attribute` = '.(int)$params['id_product_attribute'].' 
			AND `id_product` = '.(int)$params['id_product']);
	}
}