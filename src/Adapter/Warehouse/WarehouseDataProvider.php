<?php
/**
 * Copyright since 2007 PrestaShop SA and Contributors
 * PrestaShop is an International Registered Trademark & Property of PrestaShop SA
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.md.
 * It is also available through the world-wide-web at this URL:
 * https://opensource.org/licenses/OSL-3.0
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@prestashop.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade PrestaShop to newer
 * versions in the future. If you wish to customize PrestaShop for your
 * needs please refer to https://devdocs.prestashop.com/ for more information.
 *
 * @author    PrestaShop SA and Contributors <contact@prestashop.com>
 * @copyright Since 2007 PrestaShop SA and Contributors
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 */

namespace PrestaShop\PrestaShop\Adapter\Warehouse;

use Warehouse;
use WarehouseProductLocation;

/**
 * @deprecated since 8.1 and will be removed in next major.
 *
 * This class will provide data from DB / ORM about Warehouse.
 */
class WarehouseDataProvider
{
    /**
     * Get product warehouses.
     *
     * @param int $id_product
     *
     * @return array Warehouses
     */
    public function getWarehouseProductLocations($id_product)
    {
        $collection = WarehouseProductLocation::getCollection($id_product);

        return $collection->getResults();
    }

    /**
     * Get all warehouses.
     *
     * @param bool $ignore_shop Optional, false by default - Allows to get only the warehouses that are associated to one/some shops (@see $id_shop)
     * @param int $id_shop optional, Context::shop::Id by default - Allows to define a specific shop to filter
     *
     * @return array Warehouses (ID, reference/name concatenated)
     */
    public function getWarehouses($ignore_shop = false, $id_shop = null)
    {
        return Warehouse::getWarehouses($ignore_shop, $id_shop);
    }

    /**
     * For a given product and warehouse, gets the product warehouse data.
     *
     * @param int $id_product
     * @param int $id_product_attribute
     * @param int $id_warehouse
     *
     * @return array
     */
    public function getWarehouseProductLocationData($id_product, $id_product_attribute, $id_warehouse)
    {
        $location = WarehouseProductLocation::getProductLocation($id_product, $id_product_attribute, $id_warehouse);
        // for 'activated', we test if $location is ===false or ==="", that's the only difference to know it...
        return ['location' => $location, 'activated' => ($location !== false), 'product_id' => $id_product];
    }
}
