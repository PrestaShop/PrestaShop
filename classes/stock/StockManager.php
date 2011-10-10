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
 *  @version  Release: $Revision: 8105 $
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
							   $id_product_attribute = null,
							   $warehouse,
							   $quantity,
							   $id_stock_mvt_reason,
							   $price_te,
							   $is_usable = true,
							   $id_supplier_order = null)
	{
		if (!is_object($warehouse) || !$price_te || !$quantity || (!$id_product || !$id_product_attribute))
			return false;

		if (!StockmvtReason::exists($id_stock_mvt_reason))
			$id_stock_mvt_reason = StockMvtReason::STOCK_MVT_DEFAULT_REASON;

		// Get context to have employee informations
		$context = Context::getContext();

		// sets mvt params to save stock mvt (only one mvt possible when adding product quantities)
		$mvt_params = array(
			'id_stock' => null,
			'physical_quantity' => $quantity,
			'id_stock_mvt_reason' => $id_stock_mvt_reason,
			'id_supplier_order' => $id_supplier_order,
			'price_te' => $price_te,
			'last_wa' => null,
			'current_wa' => null,
			'id_employee' => $context->employee->id_employee
		);

		// Flag if an existing stock is available
		$stock_exists = false;

		// switch on STOCK_MANAGEMENT_MODE
		switch ($warehouse->stock_management)
		{
			// case CUMP mode
			case 'WA':

				// gets stock collection for the given product
				$stock_collection = $this->getStockCollection($id_product, $id_product_attribute, $warehouse->id_warehouse);

				// if the product is already in stock
				if (count($stock_collection) > 0)
				{
					$stock_exists = true;

					// There is one and only one stock for a given product in a warehouse in this mode
					$stock = $stock_collection[0];

					// determine weigthed average price
					$last_wa = $stock->price_te;
					$current_wa = $this->calculateWA($stock, $quantity, $price_te);

					// sets mvt_params
					$mvt_params['id_stock'] = $stock->id_stock;
					$mvt_params['last_wa'] = $last_wa;
					$mvt_params['current_wa'] = $current_wa;

					// set new stock params
					$stock_params = array(
						'physical_quantity' => ($stock->physical_quantity + $quantity),
						'price_te' => $current_cump,
						'usable_quantity' => ($is_usable ? ($stock->usable_quantity + $quantity) : $stock->usable_quantity),
						'id_warehouse' => $warehouse->id_warehouse
					);

					// save stock in warehouse
					$stock->hydrate($stock_params);
					$stock->update();
				}
				else // else, the product is not in sock
				{
					// sets mvt_params
					$mvt_params['last_wa'] = 0;
					$mvt_params['current_wa'] = $price_te;
				}
			break;

			// case FIFO mode
			case 'FIFO':
			case 'LIFO':
				// gets stock collection for the given product and the same unit price
				$stock_collection = $this->getStockCollection($id_product, $id_product_attribute, $warehouse->id_warehouse, $price_te);

				// if the product is already in stock
				if (count($stock_collection) > 0)
				{
					$stock_exists = true;

					// There is one and only one stock for a given product in a warehouse and at the curent unit price
					$stock = $stock_collection[0];

					// set new stock params
					$stock_params = array(
						'physical_quantity' => ($stock->physical_quantity + $quantity),
						'usable_quantity' => ($is_usable ? ($stock->usable_quantity + $quantity) : $stock->usable_quantity),
					);

					// update stock in warehouse
					$stock->hydrate($stock_params);
					$stock->update();

					// sets mvt_params
					$mvt_params['id_stock'] = $stock->id_stock;

				}

			break;

			default:
				return false;
			break;
		}

		if (!$stock_exists)
		{
			// creates a new stock for this product
			$stock = new Stock();

			// set stock params
			$stock_params = array(
				'id_product_attribute' => $id_product_attribute,
				'id_product' => $id_product,
				'physical_quantity' => $quantity,
				'price_te' => $price_te,
				'usable_quantity' => ($is_usable ? $quantity : 0),
				'id_warehouse' => $warehouse->id_warehouse
			);

			// save stock in warehouse
			$stock->hydrate($stock_params);
			$stock->add();

			// sets mvt_params
			$mvt_params['id_stock'] = $stock->id_stock;
		}

		// saves stock mvt
		$stock_mvt = new StockMvt();
		$stock_mvt->hydrate($mvt_params);
		$stock_mvt->save();

		return true;
	}

	/**
	 * @see StockManagerInterface::removeProduct()
	 */
	public function removeProduct($id_product,
								  $id_product_attribute = null,
								  $warehouse,
								  $quantity,
								  $id_stock_mvt_reason,
								  $is_usable = true,
								  $id_order = null)
	{
		$return = array();

		if (!is_object($warehouse) || !$price_te || !$quantity || (!$id_product || !$id_product_attribute))
			return $return;

		if (!StockmvtReason::exists($id_stock_mvt_reason))
			$id_stock_mvt_reason = StockMvtReason::STOCK_MVT_DEFAULT_REASON;

		// gets context to have the employee informations
		$context = Context::getContext();

		// gets total quantitiy in stock for the current product
		$quantity_in_stock = $this->getProductPhysicalQuantities($id_product, $id_product_attribute, array($warehouse->id_warehouse), $usable);

		// checks if it's possible to remove the given quantity
		if ($quantity_in_stock < $quantity)
			return $return;

		// gets stock collection for the given product
		$stock_collection = $this->getStockCollection($id_product, $id_product_attribute, $warehouse->id_warehouse);

		// check if the collection is well loaded
		if (count($stock_collection) <= 0)
			return $return;

		// prepare utils variables
		$stock_history_qty_available = array();
		$mvt_params = array();
		$stock_params = array();
		$quantity_to_decrement_by_stock = array();
		$global_quantity_to_decrement = $quantity;

		// switch on STOCK_MANAGEMENT_MODE
		switch ($warehouse->management_type)
		{
			// case CUMP mode
			case 'WA':
				// There is one and only one stock for a given product in a warehouse in this mode
				$stock = $stock_collection[0];

				// sets mvt params to save stock mvt (only one mvt is possible when removing product quantities in this mode)
				$mvt_params = array(
					'id_stock' => $stock->id_stock,
					'physical_quantity' => $quantity,
					'id_stock_mvt_reason' => $id_stock_mvt_reason,
					'id_order' => $id_order,
					'price_te' => $stock->price_te,
					'id_employee' => $context->employee->id_employee
				);

				// sets new stock params
				$stock_params = array(
					'physical_quantity' => ($stock->physical_quantity - $quantity),
					'usable_quantity' => ($is_usable ? ($stock->usable_quantity - $quantity) : $stock->usable_quantity),
				);

				// saves stock in warehouse
				$stock->hydrate($stock_params);
				$stock->update();

				// saves stock mvt
				$stock_mvt = new StockMvt();
				$stock_mvt->hydrate($mvt_params);
				$stock_mvt->save();
			break;

			case 'LIFO':
			case 'FIFO':

				// for each stock, parse its mvts history to determine left quantities for each positive mvt,
				// according to instant available quantities for this stock
				foreach ($stock_collection as $stock)
				{
					$left_quantity_to_check = $stock->physical_quantity;
					if ($left_quantity_to_check <= 0)
						continue;

					$resource = Db::getInstance(_PS_USE_SQL_SLAVE_)->execute('
						SELECT sm.`id_stock_mvt`, sm.`date_add`, sm.`physical_quantity`, (sm.`physical_quantity` - SUM(sm2.`physical_quantity`)) as qty
						FROM `'._DB_PREFIX_.'stock_mvt` sm
						JOIN `'._DB_PREFIX_.'stock_mvt` sm2 ON sm2.`stock_mvt_referer` = sm.`id_stock_mvt`
						WHERE sm.`sign` = 1
						AND sm.`id_stock` = '.(int)$stock->id_stock.'
						AND sm2.`sign` = -1
						ORDER BY sm.`date_add` DESC'
					);

					while ($row = Db::getInstance()->nextRow($resource))
					{
						// stop while - in FIFO mode, we have to retreive the oldest positive mvts for which there are left quantities
						if ($warehouse->management_type == 'FIFO')
							if ($row['qty'] >= $row['physical_quantity'])
								break;

						// converts date to timestamp
						$date = new DateTime($row['date_add']);
						$timestamp = $date->format('U');

						// history of the mvt
						$stock_history_qty_available[$timestamp] = array(
							'id_stock' => $stock->id_stock,
							'id_stock_mvt' => (int)$row['id_stock_mvt'],
							'qty' => (int)$row['qty']
						);

						// stop while - in LIFO mode, checks only the necessary history to handle the global quantity for the current stock
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
					krsort($stock_history);
				else
					// orders stock history by timestamp to get oldest history first
					ksort($stock_history);

				// checks each stock to manage the real quantity to decrement for each of them
				foreach ($stock_history as $entry)
				{
					if ($entry['qty'] >= $global_quantity_to_decrement)
					{
						$quantity_to_decrement_by_stock[$entry['id_stock']][$entry['id_stock_mvt']] = $global_quantity_to_decrement;
						$global_quantity_to_decrement = 0;
					}
					else {
						$quantity_to_decrement_by_stock[$entry['id_stock']][$entry['id_stock_mvt']] = $entry['qty'];
						$global_quantity_to_decrement -= $entry['qty'];
					}

					if ($global_quantity_to_decrement <= 0)
						break;
				}

				// for each stock, decrements it and logs the mvts
				foreach ($stock_collection as $stock)
				{
					if (array_key_exists($stock->id_stock, $quantity_to_decrement_by_stock) && is_array($quantity_to_decrement_by_stock[$stock->id_stock]))
					{
						$total_quantity_for_current_stock = 0;

						foreach ($quantity_to_decrement_by_stock[$stock->id_stock] as $id_mvt_referrer => $qte)
						{
							// sets mvt params to save stock mvt
							$mvt_params = array(
								'id_stock' => $stock->id_stock,
								'physical_quantity' => $qte,
								'id_stock_mvt_reason' => $id_stock_mvt_reason,
								'id_order' => $id_order,
								'price_te' => $stock->price_te,
								'sign' => -1,
								'stock_mvt_referer' => $id_mvt_referrer,
								'id_employee' => $context->employee->id_employee
							);

							// saves stock mvt
							$stock_mvt = new StockMvt();
							$stock_mvt->hydrate($mvt_params);
							$stock_mvt->save();

							$total_quantity_for_current_stock += $qte;
						}

						// sets new stock params
						$stock_params = array(
							'physical_quantity' => ($stock->physical_quantity - $total_quantity_for_current_stock),
							'usable_quantity' => ($is_usable ? ($stock->usable_quantity - $total_quantity_for_current_stock) : $stock->usable_quantity),
						);

						$return[$stock->id_stock]['quantity'] = $total_quantity_for_current_stock;
						$return[$stock->id_stock]['price_te'] = $stock->price_te;

						// saves stock in warehouse
						$stock->hydrate($stock_params);
						$stock->update();
					}
				}
			break;
		}

		return $return;
	}

	/**
	 * @see StockManagerInterface::getProductPhysicalQuantities()
	 */
	public function getProductPhysicalQuantities($id_product, $id_product_attribute, $warehouses_id = null, $usable = false)
	{

	}

	/**
	 * @see StockManagerInterface::getProductRealQuantities()
	 */
	public function getProductRealQuantities($id_product, $id_product_attribute, $warehouses_id = null, $usable = false)
	{

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
		return ((($stock->physical_quantity * $stock->price_te) + ($quantity * $price_te)) / ($stock->$quantity + $quantity));
	}

	/**
	 * For a given product, retrieves the stock collection
	 *
	 * @param int $id_product
	 * @param int $id_product_attribute
	 * @return array
	 */
	protected function getStockCollection($id_product, $id_product_attribute, $id_warehouse = null, $price_te = null)
	{
		// build query
		$query = new DbQuery();
		$query->select('s.id_stock, s.physical_quantity, s.usable_quantity, s.price_te');
		$query->from('stock s');
		$query->where('id_product = '.(int)$id_product.' AND id_product_attribute = '.(int)$id_product_attribute);
		if ($id_warehouse != null)
			$query->where('id_warehouse = '.(int)$id_warehouse);
		if ($price_te != null)
			$query->where('price_te = '.(float)$price_te);

		$results = Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS($query);

		return ObjectModel::hydrateCollection('Stock', $results);
	}

}