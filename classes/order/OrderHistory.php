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
*  @version  Release: $Revision: 6844 $
*  @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
*  International Registered Trademark & Property of PrestaShop SA
*/

class OrderHistoryCore extends ObjectModel
{
	/** @var integer Order id */
	public $id_order;

	/** @var integer Order state id */
	public $id_order_state;

	/** @var integer Employee id for this history entry */
	public $id_employee;

	/** @var string Object creation date */
	public $date_add;

	/** @var string Object last modification date */
	public $date_upd;

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
			'date_add' => 		array('type' => self::TYPE_DATE, 'validate' => 'isDate'),
		),
	);

	/**
	 * @see  ObjectModel::$webserviceParameters
	 */
	protected $webserviceParameters = array(
		'objectsNodeName' => 'order_histories',
		'fields' => array(
			'id_order_state' => array('required' => true, 'xlink_resource'=> 'order_states'),
			'id_order' => array('xlink_resource' => 'orders'),
		),
	);

	/**
	 * Sets the new state of the given order
	 *
	 * @param int $new_order_state
	 * @param int $id_order
	 * @param bool $use_existing_payment
	 */
	public function changeIdOrderState($new_order_state, &$id_order, $use_existing_payment = false)
	{
		if (!$new_order_state || !$id_order)
			return;

		if (!is_object($id_order) && is_numeric($id_order))
			$order = new Order((int)$id_order);
		elseif (is_object($id_order))
			$order = $id_order;
		else
			return;

		$new_os = new OrderState((int)$new_order_state, $order->id_lang);
		$old_os = $order->getCurrentOrderState();
		$is_validated = $this->isValidated();

		// executes hook
		if ($new_os->id == Configuration::get('PS_OS_PAYMENT'))
			Hook::exec('actionPaymentConfirmation', array('id_order' => (int)$order->id));

		// executes hook
		Hook::exec('actionOrderStatusUpdate', array(
			'newOrderStatus' => $new_os,
			'id_order' => (int)$order->id
		));

		if (Validate::isLoadedObject($order) && ($old_os instanceof OrderState) && ($new_os instanceof OrderState))
		{
			// @since 1.5.0 : gets the stock manager
			$manager = null;
			if (Configuration::get('PS_ADVANCED_STOCK_MANAGEMENT'))
				$manager = StockManagerFactory::getManager();
			// foreach products of the order
			foreach ($order->getProductsDetail() as $product)
			{
				// if becoming logable => adds sale
				if ($new_os->logable && !$old_os->logable)
				{
					ProductSale::addProductSale($product['product_id'], $product['product_quantity']);
					// @since 1.5.0 - Stock Management
					if (!Pack::isPack($product['product_id']) &&
						($old_os->id == Configuration::get('PS_OS_ERROR') || $old_os->id == Configuration::get('PS_OS_CANCELED')) &&
						!StockAvailable::dependsOnStock($product['id_product'], (int)$order->id_shop))
						StockAvailable::updateQuantity($product['product_id'], $product['product_attribute_id'], -(int)$product['product_quantity'], $order->id_shop);
				}
				// if becoming unlogable => removes sale
				elseif (!$new_os->logable && $old_os->logable)
				{
					ProductSale::removeProductSale($product['product_id'], $product['product_quantity']);

					// @since 1.5.0 - Stock Management
					if (!Pack::isPack($product['product_id']) &&
						($new_os->id == Configuration::get('PS_OS_ERROR') || $new_os->id == Configuration::get('PS_OS_CANCELED')) &&
						!StockAvailable::dependsOnStock($product['id_product']))
						StockAvailable::updateQuantity($product['product_id'], $product['product_attribute_id'], (int)$product['product_quantity'], $order->id_shop);
				}
				// if waiting for payment => payment error/canceled
				elseif (!$new_os->logable && !$old_os->logable &&
						 ($new_os->id == Configuration::get('PS_OS_ERROR') || $new_os->id == Configuration::get('PS_OS_CANCELED')) &&
						 !StockAvailable::dependsOnStock($product['id_product']))
						 StockAvailable::updateQuantity($product['product_id'], $product['product_attribute_id'], (int)$product['product_quantity'], $order->id_shop);
				// @since 1.5.0 : if the order is being shipped and this products uses the advanced stock management :
				// decrements the physical stock using $id_warehouse
				if ($new_os->shipped == 1 && $old_os->shipped == 0 &&
					Configuration::get('PS_ADVANCED_STOCK_MANAGEMENT') &&
					Warehouse::exists($product['id_warehouse']) &&
					$manager != null &&
					((int)$product['advanced_stock_management'] == 1 || Pack::usesAdvancedStockManagement($product['product_id'])))
				{
					// gets the warehouse
					$warehouse = new Warehouse($product['id_warehouse']);

					// decrements the stock (if it's a pack, the StockManager does what is needed)
					$manager->removeProduct(
						$product['product_id'],
						$product['product_attribute_id'],
						$warehouse,
						$product['product_quantity'],
						Configuration::get('PS_STOCK_CUSTOMER_ORDER_REASON'),
						true,
						(int)$order->id
					);
				}
				// @since.1.5.0 : if the order was shipped, and is not anymore, we need to restock products
				elseif ($new_os->shipped == 0 && $old_os->shipped == 1 &&
						 Configuration::get('PS_ADVANCED_STOCK_MANAGEMENT') &&
						 Warehouse::exists($product['id_warehouse']) &&
						 $manager != null &&
						 ((int)$product['advanced_stock_management'] == 1 || Pack::usesAdvancedStockManagement($product['product_id'])))
				{
					// if the product is a pack, we restock every products in the pack using the last negative stock mvts
					if (Pack::isPack($product['product_id']))
					{
						$pack_products = Pack::getItems($product['product_id'], Configuration::get('PS_LANG_DEFAULT'));
						foreach ($pack_products as $pack_product)
						{
							if ($pack_product->advanced_stock_management == 1)
							{
								$mvts = StockMvt::getNegativeStockMvts($order->id, $pack_product->id, 0, $pack_product->pack_quantity * $product['product_quantity']);
								foreach ($mvts as $mvt)
								{
									$manager->addProduct(
										$pack_product->id,
										0,
										new Warehouse($mvt['id_warehouse']),
										$mvt['physical_quantity'],
										null,
										$mvt['price_te'],
										true
									);
								}
								if (!StockAvailable::dependsOnStock($product['id_product']))
									StockAvailable::updateQuantity($pack_product->id, 0, (int)$pack_product->pack_quantity * $product['product_quantity'], $order->id_shop);
							}
						}
					}
					// else, it's not a pack, re-stock using the last negative stock mvts
					else
					{
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
					}
				}
			}
		}

		$this->id_order_state = (int)$new_order_state;

		// changes invoice number of order ?
		if (!Validate::isLoadedObject($new_os) || !Validate::isLoadedObject($order))
			die(Tools::displayError('Invalid new order state'));

		// the order is valid if and only if the invoice is available and the order is not cancelled
		$order->valid = $new_os->logable;
		$order->update();

		if ($new_os->invoice && !$order->invoice_number)
			$order->setInvoice($use_existing_payment);

		// set orders as paid
		if ($new_os->paid == 1)
		{
			$invoices = $order->getInvoicesCollection();
			if ($order->total_paid != 0)
				$payment_method = Module::getInstanceByName($order->module);

			foreach ($invoices as $invoice)
			{
				$rest_paid = $invoice->getRestPaid();
				if ($rest_paid > 0)
				{
					$payment = new OrderPayment();
					$payment->order_reference = $order->reference;
					$payment->id_currency = $order->id_currency;
					$payment->amount = $rest_paid;

					if ($order->total_paid != 0)
						$payment->payment_method = $payment_method->displayName;
					else 
						$payment->payment_method = null;
					
					// Update total_paid_real value for backward compatibility reasons
					if ($payment->id_currency == $order->id_currency)
						$order->total_paid_real += $payment->amount;
					else
						$order->total_paid_real += Tools::ps_round(Tools::convertPrice($payment->amount, $payment->id_currency, false), 2);
					$order->save();
						
					$payment->conversion_rate = 1;
					$payment->save();
					Db::getInstance()->execute('
					INSERT INTO `'._DB_PREFIX_.'order_invoice_payment`
					VALUES('.(int)$invoice->id.', '.(int)$payment->id.', '.(int)$order->id.')');
				}
			}
		}

		// updates delivery date even if it was already set by another state change
		if ($new_os->delivery)
			$order->setDelivery();

		// executes hook
		Hook::exec('actionOrderStatusPostUpdate', array(
			'newOrderStatus' => $new_os,
			'id_order' => (int)$order->id,
		));
	}

	/**
	 * Returns the last order state
	 * @param int $id_order
	 * @return OrderState|bool
	 * @deprecated 1.5.0.4
	 * @see Order->current_state
	 */
	public static function getLastOrderState($id_order)
	{
		Tools::displayAsDeprecated();
		$id_order_state = Db::getInstance()->getValue('
		SELECT `id_order_state`
		FROM `'._DB_PREFIX_.'order_history`
		WHERE `id_order` = '.(int)$id_order.'
		ORDER BY `date_add` DESC, `id_order_history` DESC');

		// returns false if there is no state
		if (!$id_order_state)
			return false;

		// else, returns an OrderState object
		return new OrderState($id_order_state, Configuration::get('PS_LANG_DEFAULT'));
	}

	/**
	 * @param bool $autodate Optional
	 * @param array $template_vars Optional
	 * @param Context $context Optional
	 * @return bool
	 */
	public function addWithemail($autodate = true, $template_vars = false, Context $context = null)
	{
		if (!$context)
			$context = Context::getContext();
		$order = new Order($this->id_order);
		$last_order_state = $order->getCurrentOrderState();
		$new_order_state = new OrderState($this->id_order_state, Configuration::get('PS_LANG_DEFAULT'));

		if (!$this->add($autodate))
			return false;

		$result = Db::getInstance()->getRow('
			SELECT osl.`template`, c.`lastname`, c.`firstname`, osl.`name` AS osname, c.`email`, os.`module_name`
			FROM `'._DB_PREFIX_.'order_history` oh
				LEFT JOIN `'._DB_PREFIX_.'orders` o ON oh.`id_order` = o.`id_order`
				LEFT JOIN `'._DB_PREFIX_.'customer` c ON o.`id_customer` = c.`id_customer`
				LEFT JOIN `'._DB_PREFIX_.'order_state` os ON oh.`id_order_state` = os.`id_order_state`
				LEFT JOIN `'._DB_PREFIX_.'order_state_lang` osl ON (os.`id_order_state` = osl.`id_order_state` AND osl.`id_lang` = o.`id_lang`)
			WHERE oh.`id_order_history` = '.(int)$this->id.' AND os.`send_email` = 1');
		if (isset($result['template']) && Validate::isEmail($result['email']))
		{
			$topic = $result['osname'];
			$data = array(
				'{lastname}' => $result['lastname'],
				'{firstname}' => $result['firstname'],
				'{id_order}' => (int)$this->id_order,
				'{order_name}' => $order->getUniqReference()
			);
			if ($template_vars)
				$data = array_merge($data, $template_vars);

			if ($result['module_name'])
			{
				$module = Module::getInstanceByName($result['module_name']);
				if (Validate::isLoadedObject($module) && isset($module->extra_mail_vars) && is_array($module->extra_mail_vars))
					$data = array_merge($data, $module->extra_mail_vars);
			}
			
			$data['{total_paid}'] = Tools::displayPrice((float)$order->total_paid, new Currency((int)$order->id_currency), false);
			$data['{order_name}'] = $order->getUniqReference();

			// An additional email is sent the first time a virtual item is validated
			$virtual_products = $order->getVirtualProducts();

			if ($virtual_products && (!$last_order_state || !$last_order_state->logable) && $new_order_state && $new_order_state->logable)
			{
				$assign = array();
				foreach ($virtual_products as $key => $virtual_product)
				{
					$id_product_download = ProductDownload::getIdFromIdProduct($virtual_product['product_id']);
					$product_download = new ProductDownload($id_product_download);
					// If this virtual item has an associated file, we'll provide the link to download the file in the email
					if ($product_download->display_filename != '')
					{
						$assign[$key]['name'] = $product_download->display_filename;
						$dl_link = $product_download->getTextLink(false, $virtual_product['download_hash'])
							.'&id_order='.$order->id
							.'&secure_key='.$order->secure_key;
						$assign[$key]['link'] = $dl_link;
						if ($virtual_product['download_deadline'] != '0000-00-00 00:00:00')
							$assign[$key]['deadline'] = Tools::displayDate($virtual_product['download_deadline'], $order->id_lang);
						if ($product_download->nb_downloadable != 0)
							$assign[$key]['downloadable'] = $product_download->nb_downloadable;
					}
				}
				$context->smarty->assign('virtualProducts', $assign);
				$context->smarty->assign('id_order', $order->id);
				$iso = Language::getIsoById((int)($order->id_lang));
				$links = $context->smarty->fetch(_PS_MAIL_DIR_.$iso.'/download-product.tpl');
				$tmp_array = array('{nbProducts}' => count($virtual_products), '{virtualProducts}' => $links);
				$data = array_merge ($data, $tmp_array);
				// If there's at least one downloadable file
				if (!empty($assign))
					Mail::Send((int)$order->id_lang, 'download_product', Mail::l('Virtual product to download', $order->id_lang), $data, $result['email'], $result['firstname'].' '.$result['lastname'],
						null, null, null, null, _PS_MAIL_DIR_, false, (int)$order->id_shop);
			}

			if (Validate::isLoadedObject($order))
				Mail::Send((int)$order->id_lang, $result['template'], $topic, $data, $result['email'], $result['firstname'].' '.$result['lastname'],
					null, null, null, null, _PS_MAIL_DIR_, false, (int)$order->id_shop);
		}

		return true;
	}

	public function add($autodate = true, $null_values = false)
	{
		if (!parent::add($autodate))
			return false;

		$order = new Order((int)$this->id_order);
		// Update id_order_state attribute in Order
		$order->current_state = $this->id_order_state;
		$order->update();

		Hook::exec('actionOrderHistoryAddAfter', array('order_history' => $this));

		return true;
	}

	/**
	 * @return int
	 */
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
