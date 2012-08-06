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
*  @version  Release: $Revision: 9202 $
*  @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
*  International Registered Trademark & Property of PrestaShop SA
*/

/**
 * StockManager : implementation of StockManagerInterface
 * @since 1.5.0
 */
class StockManagerCore implements StockManagerInterface
{

	/**
	 * @see StockManagerInterface::isAvailable()
	 */
	public static function isAvailable()
	{
		// Default Manager : always available
		return true;
	}

	/**
	 * @see StockManagerInterface::addProduct()
	 */
	public function addProduct($id_product,
							   $id_product_attribute = 0,
							   Warehouse $warehouse,
							   $quantity,
							   $id_stock_mvt_reason,
							   $price_te,
							   $is_usable = true,
							   $id_supply_order = null)
	{
		if (!Validate::isLoadedObject($warehouse) || !$price_te || !$quantity || !$id_product)
			return false;

		$price_te = (float)round($price_te, 6);

		if (!StockMvtReason::exists($id_stock_mvt_reason))
			$id_stock_mvt_reason = Configuration::get('PS_STOCK_MVT_INC_REASON_DEFAULT');

		$context = Context::getContext();

		$mvt_params = array(
			'id_stock' => null,
			'physical_quantity' => $quantity,
			'id_stock_mvt_reason' => $id_stock_mvt_reason,
			'id_supply_order' => $id_supply_order,
			'price_te' => $price_te,
			'last_wa' => null,
			'current_wa' => null,
			'id_employee' => $context->employee->id,
			'employee_firstname' => $context->employee->firstname,
			'employee_lastname' => $context->employee->lastname,
			'sign' => 1
		);

		$stock_exists = false;

		// switch on MANAGEMENT_TYPE
		switch ($warehouse->management_type)
		{
			// case CUMP mode
			case 'WA':

				$stock_collection = $this->getStockCollection($id_product, $id_product_attribute, $warehouse->id);

				// if this product is already in stock
				if (count($stock_collection) > 0)
				{
					$stock_exists = true;

					// for a warehouse using WA, there is one and only one stock for a given product
					$stock = $stock_collection->current();

					// calculates WA price
					$last_wa = $stock->price_te;
					$current_wa = $this->calculateWA($stock, $quantity, $price_te);

					$mvt_params['id_stock'] = $stock->id;
					$mvt_params['last_wa'] = $last_wa;
					$mvt_params['current_wa'] = $current_wa;

					$stock_params = array(
						'physical_quantity' => ($stock->physical_quantity + $quantity),
						'price_te' => $current_wa,
						'usable_quantity' => ($is_usable ? ($stock->usable_quantity + $quantity) : $stock->usable_quantity),
						'id_warehouse' => $warehouse->id,
					);

					// saves stock in warehouse
					$stock->hydrate($stock_params);
					$stock->update();
				}
				else // else, the product is not in sock
				{
					$mvt_params['last_wa'] = 0;
					$mvt_params['current_wa'] = $price_te;
				}
			break;

			// case FIFO / LIFO mode
			case 'FIFO':
			case 'LIFO':

				$stock_collection = $this->getStockCollection($id_product, $id_product_attribute, $warehouse->id, $price_te);

				// if this product is already in stock
				if (count($stock_collection) > 0)
				{
					$stock_exists = true;

					// there is one and only one stock for a given product in a warehouse and at the current unit price
					$stock = $stock_collection->current();

					$stock_params = array(
						'physical_quantity' => ($stock->physical_quantity + $quantity),
						'usable_quantity' => ($is_usable ? ($stock->usable_quantity + $quantity) : $stock->usable_quantity),
					);

					// updates stock in warehouse
					$stock->hydrate($stock_params);
					$stock->update();

					// sets mvt_params
					$mvt_params['id_stock'] = $stock->id;

				}

			break;

			default:
				return false;
			break;
		}

		if (!$stock_exists)
		{
			$stock = new Stock();

			$stock_params = array(
				'id_product_attribute' => $id_product_attribute,
				'id_product' => $id_product,
				'physical_quantity' => $quantity,
				'price_te' => $price_te,
				'usable_quantity' => ($is_usable ? $quantity : 0),
				'id_warehouse' => $warehouse->id
			);

			// saves stock in warehouse
			$stock->hydrate($stock_params);
			$stock->add();
			$mvt_params['id_stock'] = $stock->id;
		}

		// saves stock mvt
		$stock_mvt = new StockMvt();
		$stock_mvt->hydrate($mvt_params);
		$stock_mvt->add();

		return true;
	}

	/**
	 * @see StockManagerInterface::removeProduct()
	 */
	public function removeProduct($id_product,
								  $id_product_attribute = null,
								  Warehouse $warehouse,
								  $quantity,
								  $id_stock_mvt_reason,
								  $is_usable = true,
								  $id_order = null)
	{
		$return = array();

		if (!Validate::isLoadedObject($warehouse) || !$quantity || !$id_product)
			return $return;

		if (!StockMvtReason::exists($id_stock_mvt_reason))
			$id_stock_mvt_reason = Configuration::get('PS_STOCK_MVT_DEC_REASON_DEFAULT');

		$context = Context::getContext();

		// Special case of a pack
		if (Pack::isPack((int)$id_product))
		{
			// Gets items
			$products_pack = Pack::getItems((int)$id_product, (int)Configuration::get('PS_LANG_DEFAULT'));
			// Foreach item
			foreach ($products_pack as $product_pack)
			{
				$pack_id_product_attribute = Product::getDefaultAttribute($product_pack->id, 1);
				if ($product_pack->advanced_stock_management == 1)
					$this->removeProduct($product_pack->id, $pack_id_product_attribute, $warehouse, $product_pack->pack_quantity * $quantity, $id_stock_mvt_reason, $is_usable, $id_order);
			}
		}
		else
		{
			// gets total quantities in stock for the current product
			$physical_quantity_in_stock = (int)$this->getProductPhysicalQuantities($id_product, $id_product_attribute, array($warehouse->id), false);
			$usable_quantity_in_stock = (int)$this->getProductPhysicalQuantities($id_product, $id_product_attribute, array($warehouse->id), true);

			// check quantity if we want to decrement unusable quantity
			if (!$is_usable)
				$quantity_in_stock = $physical_quantity_in_stock - $usable_quantity_in_stock;
			else
				$quantity_in_stock = $usable_quantity_in_stock;

			// checks if it's possible to remove the given quantity
			if ($quantity_in_stock < $quantity)
				return $return;

			$stock_collection = $this->getStockCollection($id_product, $id_product_attribute, $warehouse->id);
			$stock_collection->getAll();

			// check if the collection is loaded
			if (count($stock_collection) <= 0)
				return $return;

			$stock_history_qty_available = array();
			$mvt_params = array();
			$stock_params = array();
			$quantity_to_decrement_by_stock = array();
			$global_quantity_to_decrement = $quantity;

			// switch on MANAGEMENT_TYPE
			switch ($warehouse->management_type)
			{
				// case CUMP mode
				case 'WA':
					// There is one and only one stock for a given product in a warehouse in this mode
					$stock = $stock_collection->current();

					$mvt_params = array(
						'id_stock' => $stock->id,
						'physical_quantity' => $quantity,
						'id_stock_mvt_reason' => $id_stock_mvt_reason,
						'id_order' => $id_order,
						'price_te' => $stock->price_te,
						'last_wa' => $stock->price_te,
						'current_wa' => $stock->price_te,
						'id_employee' => $context->employee->id,
						'employee_firstname' => $context->employee->firstname,
						'employee_lastname' => $context->employee->lastname,
						'sign' => -1
					);
					$stock_params = array(
						'physical_quantity' => ($stock->physical_quantity - $quantity),
						'usable_quantity' => ($is_usable ? ($stock->usable_quantity - $quantity) : $stock->usable_quantity)
					);

					// saves stock in warehouse
					$stock->hydrate($stock_params);
					$stock->update();

					// saves stock mvt
					$stock_mvt = new StockMvt();
					$stock_mvt->hydrate($mvt_params);
					$stock_mvt->save();

					$return[$stock->id]['quantity'] = $quantity;
					$return[$stock->id]['price_te'] = $stock->price_te;

				break;

				case 'LIFO':
				case 'FIFO':

					// for each stock, parse its mvts history to calculate the quantities left for each positive mvt,
					// according to the instant available quantities for this stock
					foreach ($stock_collection as $stock)
					{
						$left_quantity_to_check = $stock->physical_quantity;
						if ($left_quantity_to_check <= 0)
							continue;

						$resource = Db::getInstance(_PS_USE_SQL_SLAVE_)->query('
							SELECT sm.`id_stock_mvt`, sm.`date_add`, sm.`physical_quantity`,
								IF ((sm2.`physical_quantity` is null), sm.`physical_quantity`, (sm.`physical_quantity` - SUM(sm2.`physical_quantity`))) as qty
							FROM `'._DB_PREFIX_.'stock_mvt` sm
							LEFT JOIN `'._DB_PREFIX_.'stock_mvt` sm2 ON sm2.`referer` = sm.`id_stock_mvt`
							WHERE sm.`sign` = 1
							AND sm.`id_stock` = '.(int)$stock->id.'
							GROUP BY sm.`id_stock_mvt`
							ORDER BY sm.`date_add` DESC'
						);

						while ($row = Db::getInstance()->nextRow($resource))
						{
							// break - in FIFO mode, we have to retreive the oldest positive mvts for which there are left quantities
							if ($warehouse->management_type == 'FIFO')
								if ($row['qty'] == 0)
									break;

							// converts date to timestamp
							$date = new DateTime($row['date_add']);
							$timestamp = $date->format('U');

							// history of the mvt
							$stock_history_qty_available[$timestamp] = array(
								'id_stock' => $stock->id,
								'id_stock_mvt' => (int)$row['id_stock_mvt'],
								'qty' => (int)$row['qty']
							);

							// break - in LIFO mode, checks only the necessary history to handle the global quantity for the current stock
							if ($warehouse->management_type == 'LIFO')
							{
								$left_quantity_to_check -= (int)$row['physical_quantity'];
								if ($left_quantity_to_check <= 0)
									break;
							}
						}
					}

					if ($warehouse->management_type == 'LIFO')
						// orders stock history by timestamp to get newest history first
						krsort($stock_history_qty_available);
					else
						// orders stock history by timestamp to get oldest history first
						ksort($stock_history_qty_available);

					// checks each stock to manage the real quantity to decrement for each of them
					foreach ($stock_history_qty_available as $entry)
					{
						if ($entry['qty'] >= $global_quantity_to_decrement)
						{
							$quantity_to_decrement_by_stock[$entry['id_stock']][$entry['id_stock_mvt']] = $global_quantity_to_decrement;
							$global_quantity_to_decrement = 0;
						}
						else
						{
							$quantity_to_decrement_by_stock[$entry['id_stock']][$entry['id_stock_mvt']] = $entry['qty'];
							$global_quantity_to_decrement -= $entry['qty'];
						}

						if ($global_quantity_to_decrement <= 0)
							break;
					}

					// for each stock, decrements it and logs the mvts
					foreach ($stock_collection as $stock)
					{
						if (array_key_exists($stock->id, $quantity_to_decrement_by_stock) && is_array($quantity_to_decrement_by_stock[$stock->id]))
						{
							$total_quantity_for_current_stock = 0;

							foreach ($quantity_to_decrement_by_stock[$stock->id] as $id_mvt_referrer => $qte)
							{
								$mvt_params = array(
									'id_stock' => $stock->id,
									'physical_quantity' => $qte,
									'id_stock_mvt_reason' => $id_stock_mvt_reason,
									'id_order' => $id_order,
									'price_te' => $stock->price_te,
									'sign' => -1,
									'referer' => $id_mvt_referrer,
									'id_employee' => $context->employee->id
								);

								// saves stock mvt
								$stock_mvt = new StockMvt();
								$stock_mvt->hydrate($mvt_params);
								$stock_mvt->save();

								$total_quantity_for_current_stock += $qte;
							}

							$stock_params = array(
								'physical_quantity' => ($stock->physical_quantity - $total_quantity_for_current_stock),
								'usable_quantity' => ($is_usable ? ($stock->usable_quantity - $total_quantity_for_current_stock) : $stock->usable_quantity)
							);

							$return[$stock->id]['quantity'] = $total_quantity_for_current_stock;
							$return[$stock->id]['price_te'] = $stock->price_te;

							// saves stock in warehouse
							$stock->hydrate($stock_params);
							$stock->update();
						}
					}
				break;
			}
		}

		// if we remove a usable quantity, exec hook
		if ($is_usable)
			Hook::exec('actionProductCoverage',
					   	array(
		   					'id_product' => $id_product,
		   					'id_product_attribute' => $id_product_attribute,
		   					'warehouse' => $warehouse
					   	)
			);

		return $return;
	}

	/**
	 * @see StockManagerInterface::getProductPhysicalQuantities()
	 */
	public function getProductPhysicalQuantities($id_product, $id_product_attribute, $ids_warehouse = null, $usable = false)
	{
		if (!is_null($ids_warehouse))
		{
			// in case $ids_warehouse is not an array
			if (!is_array($ids_warehouse))
				$ids_warehouse = array($ids_warehouse);

			// casts for security reason
			$ids_warehouse = array_map('intval', $ids_warehouse);
			if (!count($ids_warehouse))
				return 0;
		}
		else
			$ids_warehouse = array();

		$query = new DbQuery();
		$query->select('SUM('.($usable ? 's.usable_quantity' : 's.physical_quantity').')');
		$query->from('stock', 's');
		$query->where('s.id_product = '.(int)$id_product);
		if (0 != $id_product_attribute)
			$query->where('s.id_product_attribute = '.(int)$id_product_attribute);

		if (count($ids_warehouse))
			$query->where('s.id_warehouse IN('.implode(', ', $ids_warehouse).')');

		return (int)Db::getInstance(_PS_USE_SQL_SLAVE_)->getValue($query);
	}

	/**
	 * @see StockManagerInterface::getProductRealQuantities()
	 */
	public function getProductRealQuantities($id_product, $id_product_attribute, $ids_warehouse = null, $usable = false)
	{
		if (!is_null($ids_warehouse))
		{
			// in case $ids_warehouse is not an array
			if (!is_array($ids_warehouse))
				$ids_warehouse = array($ids_warehouse);

			// casts for security reason
			$ids_warehouse = array_map('intval', $ids_warehouse);
		}

		// Gets client_orders_qty
		$query = new DbQuery();
		$query->select('SUM(od.product_quantity) + SUM(od.product_quantity_refunded)');
		$query->from('order_detail', 'od');
		$query->leftjoin('orders', 'o', 'o.id_order = od.id_order');
		$query->where('od.product_id = '.(int)$id_product);
		if (0 != $id_product_attribute)
			$query->where('od.product_attribute_id = '.(int)$id_product_attribute);
		$query->leftJoin('order_history', 'oh', 'oh.id_order = o.id_order AND oh.date_add = o.date_upd');
		$query->leftJoin('order_state', 'os', 'os.id_order_state = oh.id_order_state');
		$query->where('os.shipped != 1');
		$query->where('o.valid = 1 OR (os.id_order_state != '.(int)Configuration::get('PS_OS_ERROR').'
					   AND os.id_order_state != '.(int)Configuration::get('PS_OS_CANCELED').')');
		//if (count($ids_warehouse))
			//$query->where('od.id_warehouse IN('.implode(', ', $ids_warehouse).')');
		$client_orders_qty = (int)Db::getInstance(_PS_USE_SQL_SLAVE_)->getValue($query);

		// Gets supply_orders_qty
		$query = new DbQuery();

		$query->select('sod.quantity_expected, sod.quantity_received');
		$query->from('supply_order', 'so');
		$query->leftjoin('supply_order_detail', 'sod', 'sod.id_supply_order = so.id_supply_order');
		$query->leftjoin('supply_order_state', 'sos', 'sos.id_supply_order_state = so.id_supply_order_state');
		$query->where('sos.pending_receipt = 1');
		$query->where('sod.id_product = '.(int)$id_product.' AND sod.id_product_attribute = '.(int)$id_product_attribute);
		if (!is_null($ids_warehouse) && count($ids_warehouse))
			$query->where('so.id_warehouse IN('.implode(', ', $ids_warehouse).')');

		$supply_orders_qties = Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS($query);

		$supply_orders_qty = 0;
		foreach ($supply_orders_qties as $qty)
			if ($qty['quantity_expected'] > $qty['quantity_received'])
				$supply_orders_qty += ($qty['quantity_expected'] - $qty['quantity_received']);

		// Gets {physical OR usable}_qty
		$qty = $this->getProductPhysicalQuantities($id_product, $id_product_attribute, $ids_warehouse, $usable);

		//real qty = actual qty in stock - current client orders + current supply orders
		return ($qty - $client_orders_qty + $supply_orders_qty);
	}

	/**
	 * @see StockManagerInterface::transferBetweenWarehouses()
	 */
	public function transferBetweenWarehouses($id_product,
											  $id_product_attribute,
											  $quantity,
											  $id_warehouse_from,
											  $id_warehouse_to,
											  $usable_from = true,
											  $usable_to = true)
	{
		// Checks if this transfer is possible
		if ($this->getProductPhysicalQuantities($id_product, $id_product_attribute, array($id_warehouse_from), $usable_from) < $quantity)
			return false;

		if ($id_warehouse_from == $id_warehouse_to && $usable_from == $usable_to)
			return false;

		// Checks if the given warehouses are available
		$warehouse_from = new Warehouse($id_warehouse_from);
		$warehouse_to = new Warehouse($id_warehouse_to);
		if (!Validate::isLoadedObject($warehouse_from) ||
			!Validate::isLoadedObject($warehouse_to))
			return false;

		// Removes from warehouse_from
		$stocks = $this->removeProduct($id_product,
									   $id_product_attribute,
									   $warehouse_from,
									   $quantity,
									   Configuration::get('PS_STOCK_MVT_TRANSFER_FROM'),
									   $usable_from);
		if (!count($stocks))
			return false;

		// Adds in warehouse_to
		foreach ($stocks as $stock)
		{
			$price = $stock['price_te'];

			// convert product price to destination warehouse currency if needed
			if ($warehouse_from->id_currency != $warehouse_to->id_currency)
			{
				// First convert price to the default currency
				$price_converted_to_default_currency = Tools::convertPrice($price, $warehouse_from->id_currency, false);

				// Convert the new price from default currency to needed currency
				$price = Tools::convertPrice($price_converted_to_default_currency, $warehouse_to->id_currency, true);
			}

			if (!$this->addProduct($id_product,
								   $id_product_attribute,
								   $warehouse_to,
								   $stock['quantity'],
								   Configuration::get('PS_STOCK_MVT_TRANSFER_TO'),
								   $price,
								   $usable_to))
				return false;
		}
		return true;
	}

	/**
	 * @see StockManagerInterface::getProductCoverage()
	 * Here, $coverage is a number of days
	 * @return int number of days left (-1 if infinite)
	 */
	public function getProductCoverage($id_product, $id_product_attribute, $coverage, $id_warehouse = null)
	{
		if (!$id_product_attribute)
			$id_product_attribute = 0;

		if ($coverage == 0 || !$coverage)
			$coverage = 7; // Week by default

		// gets all stock_mvt for the given coverage period
		$query = '
			SELECT SUM(view.quantity) as quantity_out
			FROM
			(	SELECT sm.`physical_quantity` as quantity
				FROM `'._DB_PREFIX_.'stock_mvt` sm
				LEFT JOIN `'._DB_PREFIX_.'stock` s ON (sm.`id_stock` = s.`id_stock`)
				LEFT JOIN `'._DB_PREFIX_.'product` p ON (p.`id_product` = s.`id_product`)
				'.Shop::addSqlAssociation('product', 'p').'
				LEFT JOIN `'._DB_PREFIX_.'product_attribute` pa ON (p.`id_product` = pa.`id_product`)
				'.Shop::addSqlAssociation('product_attribute', 'pa', false).'
				WHERE sm.`sign` = -1
				AND sm.`id_stock_mvt_reason` != '.Configuration::get('PS_STOCK_MVT_TRANSFER_FROM').'
				AND TO_DAYS(NOW()) - TO_DAYS(sm.`date_add`) <= '.(int)$coverage.'
				AND s.`id_product` = '.(int)$id_product.'
				AND s.`id_product_attribute` = '.(int)$id_product_attribute.
				($id_warehouse ? ' AND s.`id_warehouse` = '.(int)$id_warehouse : '').'
				GROUP BY sm.`id_stock_mvt`
			) as view';

		$quantity_out = Db::getInstance(_PS_USE_SQL_SLAVE_)->getValue($query);
		if (!$quantity_out)
			return -1;

		$quantity_per_day = Tools::ps_round($quantity_out / $coverage);
		$physical_quantity = $this->getProductPhysicalQuantities($id_product,
															     $id_product_attribute,
															     ($id_warehouse ? array($id_warehouse) : null),
															     true);
		$time_left = ($quantity_per_day == 0) ? (-1) : Tools::ps_round($physical_quantity / $quantity_per_day);

		return $time_left;
	}

	/**
	 * For a given stock, calculates its new WA(Weighted Average) price based on the new quantities and price
	 * Formula : (physicalStock * lastCump + quantityToAdd * unitPrice) / (physicalStock + quantityToAdd)
	 *
	 * @param Stock $stock
	 * @param int $quantity
	 * @param float $price_te
	 * @return int WA
	 */
	protected function calculateWA(Stock $stock, $quantity, $price_te)
	{
		return (float)Tools::ps_round(((($stock->physical_quantity * $stock->price_te) + ($quantity * $price_te)) / ($stock->physical_quantity + $quantity)), 6);
	}

	/**
	 * For a given product, retrieves the stock collection
	 *
	 * @param int $id_product
	 * @param int $id_product_attribute
	 * @param int $id_warehouse Optional
	 * @param int $price_te Optional
	 * @return Collection of Stock
	 */
	protected function getStockCollection($id_product, $id_product_attribute, $id_warehouse = null, $price_te = null)
	{
		$stocks = new Collection('Stock');
		$stocks->where('id_product', '=', $id_product);
		$stocks->where('id_product_attribute', '=', $id_product_attribute);
		if ($id_warehouse)
			$stocks->where('id_warehouse', '=', $id_warehouse);
		if ($price_te)
			$stocks->where('price_te', '=', $price_te);

		return $stocks;
	}

}