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
*  @version  Release: $Revision: 1.4 $
*  @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
*  International Registered Trademark & Property of PrestaShop SA
*/

class OrderHistoryCore extends ObjectModel
{
	/** @var integer Order id */
	public 		$id_order;
	
	/** @var integer Order state id */
	public 		$id_order_state;
	
	/** @var integer Employee id for this history entry */
	public 		$id_employee;
	
	/** @var string Object creation date */
	public 		$date_add;

	/** @var string Object last modification date */
	public 		$date_upd;

	protected $tables = array ('order_history');
	
	protected	$fieldsRequired = array('id_order', 'id_order_state');
	protected	$fieldsValidate = array('id_order' => 'isUnsignedId', 'id_order_state' => 'isUnsignedId', 'id_employee' => 'isUnsignedId');

	protected 	$table = 'order_history';
	protected 	$identifier = 'id_order_history';
	
	protected	$webserviceParameters = array(
		'objectsNodeName' => 'order_histories',
		'fields' => array(
			'id_order_state' => array('required' => true, 'xlink_resource'=> 'order_states'),
			'id_order' => array('xlink_resource' => 'orders'),
		),
	);

	public function getFields()
	{
		parent::validateFields();
		
		$fields['id_order'] = (int)($this->id_order);
		$fields['id_order_state'] = (int)($this->id_order_state);
		$fields['id_employee'] = (int)($this->id_employee);
		$fields['date_add'] = pSQL($this->date_add);
				
		return $fields;
	}

	public function changeIdOrderState($new_order_state = NULL, $id_order)
	{
		if ($new_order_state != NULL)
		{
			Hook::updateOrderStatus((int)($new_order_state), (int)($id_order));
			$order = new Order((int)($id_order));
			
			/* Best sellers */
			$newOS = new OrderState((int)($new_order_state), $order->id_lang);
			$oldOrderStatus = OrderHistory::getLastOrderState((int)($id_order));
			$cart = Cart::getCartByOrderId($id_order);
			$isValidated = $this->isValidated();
			if (Validate::isLoadedObject($cart))
				foreach ($cart->getProducts() as $product)
				{
					/* If becoming logable => adding sale */
					if ($newOS->logable AND (!$oldOrderStatus OR !$oldOrderStatus->logable))
						ProductSale::addProductSale($product['id_product'], $product['cart_quantity']);
					/* If becoming unlogable => removing sale */
					elseif (!$newOS->logable AND ($oldOrderStatus AND $oldOrderStatus->logable))
						ProductSale::removeProductSale($product['id_product'], $product['cart_quantity']);
					if (!$isValidated AND $newOS->logable AND isset($oldOrderStatus) AND $oldOrderStatus AND $oldOrderStatus->id == _PS_OS_ERROR_)
					{
						Product::updateQuantity($product);
						Hook::updateQuantity($product, $order);
					}
				}
			
			$this->id_order_state = (int)($new_order_state);
			
			/* Change invoice number of order ? */
			if (!Validate::isLoadedObject($newOS) OR !Validate::isLoadedObject($order))
				die(Tools::displayError('Invalid new order state'));
			
			/* The order is valid only if the invoice is available and the order is not cancelled */
			$order->valid = $newOS->logable;
			$order->update();

			if ($newOS->invoice AND !$order->invoice_number)
				$order->setInvoice();
			if ($newOS->delivery AND !$order->delivery_number)
				$order->setDelivery();
			Hook::postUpdateOrderStatus((int)($new_order_state), (int)($id_order));
		}
	}

	static public function getLastOrderState($id_order)
	{
		$id_order_state = Db::getInstance()->getValue('
		SELECT `id_order_state`
		FROM `'._DB_PREFIX_.'order_history`
		WHERE `id_order` = '.(int)($id_order).'
		ORDER BY `date_add` DESC, `id_order_history` DESC');
		if (!$id_order_state)
			return false;
		return new OrderState($id_order_state, Configuration::get('PS_LANG_DEFAULT'));
	}

	public function addWithemail($autodate = true, $templateVars = false)
	{
		$lastOrderState = $this->getLastOrderState($this->id_order);
		
		if (!parent::add($autodate))
			return false;

		$result = Db::getInstance()->getRow('
			SELECT osl.`template`, c.`lastname`, c.`firstname`, osl.`name` AS osname, c.`email`
			FROM `'._DB_PREFIX_.'order_history` oh
				LEFT JOIN `'._DB_PREFIX_.'orders` o ON oh.`id_order` = o.`id_order`
				LEFT JOIN `'._DB_PREFIX_.'customer` c ON o.`id_customer` = c.`id_customer`
				LEFT JOIN `'._DB_PREFIX_.'order_state` os ON oh.`id_order_state` = os.`id_order_state`
				LEFT JOIN `'._DB_PREFIX_.'order_state_lang` osl ON (os.`id_order_state` = osl.`id_order_state` AND osl.`id_lang` = o.`id_lang`)
			WHERE oh.`id_order_history` = '.(int)($this->id).'
				AND os.`send_email` = 1');

		if (isset($result['template']) AND Validate::isEmail($result['email']))
		{
			$topic = $result['osname'];
			$data = array('{lastname}' => $result['lastname'], '{firstname}' => $result['firstname'], '{id_order}' => (int)($this->id_order));
			if ($templateVars) $data = array_merge($data, $templateVars);
			$order = new Order((int)($this->id_order));
			$data['{total_paid}'] = Tools::displayPrice((float)($order->total_paid), new Currency((int)($order->id_currency)), false, false);
			$data['{order_name}'] = sprintf("#%06d", (int)($order->id));
			
			// An additional email is sent the first time a virtual item is validated
			if ($virtualProducts = $order->getVirtualProducts() AND (!$lastOrderState OR !$lastOrderState->logable) AND $newOrderState = new OrderState($this->id_order_state, Configuration::get('PS_LANG_DEFAULT')) AND $newOrderState->logable)
			{
				global $smarty;
				$assign = array();
				foreach ($virtualProducts AS $key => $virtualProduct)
				{
					$id_product_download = ProductDownload::getIdFromIdProduct($virtualProduct['product_id']);
					$product_download = new ProductDownload($id_product_download);
					$assign[$key]['name'] = $product_download->display_filename;
					$assign[$key]['link'] = $product_download->getTextLink(false, $virtualProduct['download_hash']);
					if ($virtualProduct['download_deadline'] != '0000-00-00 00:00:00')
						$assign[$key]['deadline'] = Tools::displayDate($virtualProduct['download_deadline'], $order->id_lang);
					if ($product_download->nb_downloadable != 0)
						$assign[$key]['downloadable'] = $product_download->nb_downloadable;
				}
				$smarty->assign('virtualProducts', $assign);
				$iso = Language::getIsoById((int)($order->id_lang));
				$links = $smarty->fetch(_PS_MAIL_DIR_.$iso.'/download-product.tpl');
				$tmpArray = array('{nbProducts}' => count($virtualProducts), '{virtualProducts}' => $links);
				$data = array_merge ($data, $tmpArray);
				global $_LANGMAIL;
				Mail::Send((int)($order->id_lang), 'download_product', Mail::l('Virtual product to download'), $data, $result['email'], $result['firstname'].' '.$result['lastname']);
			}

			if (Validate::isLoadedObject($order))
				Mail::Send((int)($order->id_lang), $result['template'], $topic, $data, $result['email'], $result['firstname'].' '.$result['lastname']);
		}

		return true;
	}
	
	public function isValidated()
	{
		return Db::getInstance()->getValue('
		SELECT COUNT(oh.`id_order_history`) AS nb
		FROM `'._DB_PREFIX_.'order_state` os 
		LEFT JOIN `'._DB_PREFIX_.'order_history` oh ON (os.`id_order_state` = oh.`id_order_state`) 
		WHERE oh.`id_order` = '.(int)$this->id_order.'
		AND os.`logable` = 1');
	}

}
