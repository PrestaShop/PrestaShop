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

	/**
	 * @see ObjectModel::$definition
	 */
	public static $definition = array(
		'table' => 'order_history',
		'primary' => 'id_order_history',
		'fields' => array(
			'id_order' => 		array('type' => self::TYPE_INT, 'validate' => 'isUnsignedId', 'required' => true),
			'id_order_state' => array('type' => self::TYPE_INT, 'validate' => 'isUnsignedId', 'required' => true),
			'id_employee' => 	array('type' => self::TYPE_INT, 'validate' => 'isUnsignedId'),
			'date_add' => 				array('type' => self::TYPE_DATE, 'validate' => 'isDate'),
		),
	);


	protected	$webserviceParameters = array(
		'objectsNodeName' => 'order_histories',
		'fields' => array(
			'id_order_state' => array('required' => true, 'xlink_resource'=> 'order_states'),
			'id_order' => array('xlink_resource' => 'orders'),
		),
	);

	public function changeIdOrderState($new_order_state, $id_order)
	{
		if ($new_order_state != NULL)
		{
			Hook::updateOrderStatus((int)($new_order_state), (int)$id_order);
			$order = new Order((int)($id_order));

			/* Best sellers */
			$newOS = new OrderState((int)($new_order_state), $order->id_lang);
			$oldOrderStatus = OrderHistory::getLastOrderState((int)$id_order);
			$isValidated = $this->isValidated();
			if (Validate::isLoadedObject($order))
				foreach ($order->getProductsDetail() as $product)
				{
					/* If becoming logable => adding sale */
					if ($newOS->logable
						&& !($oldOrderStatus instanceof OrderState
						&& $oldOrderStatus->logable))
					{
						ProductSale::addProductSale($product['product_id'], $product['product_quantity']);
					}
					/* If becoming unlogable => removing sale */
					else if (!$newOS->logable
						&& $oldOrderStatus instanceof OrderState
						&& $oldOrderStatus->logable)
					{
						ProductSale::removeProductSale($product['product_id'], $product['product_quantity']);
						// @since 1.5.0
						if ($newOS->id == Configuration::get('PS_OS_ERROR') || $newOS->id == Configuration::get('PS_OS_CANCELED'))
							StockAvailable::updateQuantity($product['product_id'], $product['product_attribute_id'], (int)$product['product_quantity'], $order->id_shop);
					}

					if ((!Configuration::get('PS_ADVANCED_STOCK_MANAGEMENT') || (int)$product['advanced_stock_management'] != 1)
						&& !$isValidated
						&& $newOS->logable
						&& $oldOrderStatus instanceof OrderState
						&& $oldOrderStatus->id == Configuration::get('PS_OS_ERROR')
					)
						StockAvailable::updateQuantity($product['product_id'], $product['product_attribute_id'], (int)$product['product_quantity'], $order->id_shop);
					// If order is shipped for the first time and
					// if we use advanced stock management system, decrement stock preperly.
					// The product is removed from the physical stock. $id_warehouse is needed
					// @TODO Checks $id_warehouse
					else if ($newOS->shipped == 1
						&& $oldOrderStatus instanceof OrderState
						&& $oldOrderStatus->shipped == 0
						&& Configuration::get('PS_ADVANCED_STOCK_MANAGEMENT')
						&& (int)$product['advanced_stock_management'] == 1)
					{
						$manager = StockManagerFactory::getManager();
						$warehouse = new Warehouse($product['id_warehouse']);

						$manager->removeProduct(
							$product['product_id'],
							$product['product_attribute_id'],
							$warehouse,
							$product['product_quantity'],
							Configuration::get('PS_STOCK_CUSTOMER_ORDER_REASON'),
							true,
							(int)$id_order
						);

						if (StockAvailable::dependsOnStock($product['product_id'], $order->id_shop))
							StockAvailable::synchronize($product['product_id']);
						else
							StockAvailable::updateQuantity($product['product_id'], $product['product_attribute_id'], -(int)$product['product_quantity'], $order->id_shop);
					}
					else if ($newOS->shipped == 0
						&& $oldOrderStatus instanceof OrderState
						&& $oldOrderStatus->shipped == 1
						&& Configuration::get('PS_ADVANCED_STOCK_MANAGEMENT')
						&& (int)$product['advanced_stock_management'] == 1
					)
					{
						$manager = StockManagerFactory::getManager();
						$mvts = StockMvt::getNegativeStockMvts($order->id, $product['product_id'], $product['product_attribute_id'], $product['product_quantity']);
						foreach ($mvts as $mvt)
						{
							$manager->addProduct(
								$product['product_id'],
								$product['product_attribute_id'],
								new Warehouse($mvt['id_warehouse']),
								$mvt['physical_quantity'],
								null,
								$mvt['price_te'],
								true
							);
						}

						if (StockAvailable::dependsOnStock($product['product_id'], $order->id_shop))
							StockAvailable::synchronize($product['product_id']);
						else
							StockAvailable::updateQuantity($product['product_id'], $product['product_attribute_id'], (int)$product['physical_quantity'], $order->id_shop);
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

			// Set order as paid
			if ($newOS->paid == 1)
			{
				$invoices = $order->getInvoicesCollection();
				$payment_method = Module::getInstanceByName($order->module);
				foreach ($invoices as $invoice)
				{
					$rest_paid = $invoice->getRestPaid();
					if ($rest_paid)
					{
						$payment = new OrderPayment();
						$payment->id_order = $order->id;
						$payment->id_order_invoice = $invoice->id;
						$payment->id_currency = $order->id_currency;
						$payment->amount = $rest_paid;
						$payment->payment_method = $payment_method->displayName;
						$payment->conversion_rate = 1;
						$payment->save();
					}
				}
			}

			// Update delivery date even if it was already set by another state change
			if ($newOS->delivery)
				$order->setDelivery();
			Hook::postUpdateOrderStatus((int)($new_order_state), (int)($id_order));
		}
	}

	public static function getLastOrderState($id_order)
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

	public function addWithemail($autodate = true, $templateVars = false, Context $context = null)
	{
		if (!$context)
			$context = Context::getContext();
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
		WHERE oh.`id_order_history` = '.(int)($this->id).' AND os.`send_email` = 1');

		if (isset($result['template']) AND Validate::isEmail($result['email']))
		{
			$topic = $result['osname'];
			$data = array('{lastname}' => $result['lastname'], '{firstname}' => $result['firstname'], '{id_order}' => (int)$this->id_order);
			if ($templateVars)
				$data = array_merge($data, $templateVars);
			$order = new Order((int)$this->id_order);
			$data['{total_paid}'] = Tools::displayPrice((float)$order->total_paid, new Currency((int)$order->id_currency), false);
			$data['{order_name}'] = sprintf("#%06d", (int)$order->id);

			// An additional email is sent the first time a virtual item is validated
			if ($virtualProducts = $order->getVirtualProducts() AND (!$lastOrderState OR !$lastOrderState->logable) AND $newOrderState = new OrderState($this->id_order_state, Configuration::get('PS_LANG_DEFAULT')) AND $newOrderState->logable)
			{
				$assign = array();
				foreach ($virtualProducts AS $key => $virtualProduct)
				{
					$id_product_download = ProductDownload::getIdFromIdAttribute($virtualProduct['product_id'], $virtualProduct['product_attribute_id']);
					$product_download = new ProductDownload($id_product_download);
					// If this virtual item has an associated file, we'll provide the link to download the file in the email
					if ($product_download->display_filename != '')
					{
						$assign[$key]['name'] = $product_download->display_filename;
						$dl_link = $product_download->getTextLink(false, $virtualProduct['download_hash'])
							.'&id_order='.$order->id
							.'&secure_key='.$order->secure_key;
						$assign[$key]['link'] = $dl_link;
						if ($virtualProduct['download_deadline'] != '0000-00-00 00:00:00')
							$assign[$key]['deadline'] = Tools::displayDate($virtualProduct['download_deadline'], $order->id_lang);
						if ($product_download->nb_downloadable != 0)
							$assign[$key]['downloadable'] = $product_download->nb_downloadable;
					}
				}
				$context->smarty->assign('virtualProducts', $assign);
				$context->smarty->assign('id_order', $order->id);
				$iso = Language::getIsoById((int)($order->id_lang));
				$links = $context->smarty->fetch(_PS_MAIL_DIR_.$iso.'/download-product.tpl');
				$tmpArray = array('{nbProducts}' => count($virtualProducts), '{virtualProducts}' => $links);
				$data = array_merge ($data, $tmpArray);
				// If there's at least one downloadable file
				if (!empty($assign))
					Mail::Send((int)$order->id_lang, 'download_product', Mail::l('Virtual product to download', $order->id_lang), $data, $result['email'], $result['firstname'].' '.$result['lastname']);
			}

			if (Validate::isLoadedObject($order))
				Mail::Send((int)$order->id_lang, $result['template'], $topic, $data, $result['email'], $result['firstname'].' '.$result['lastname']);
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
