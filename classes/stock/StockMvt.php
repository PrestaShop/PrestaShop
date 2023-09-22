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

/**
 * @since 1.5.0 Defines stock movements
 * @deprecated since 9.0 and will be removed in 10.0, this object model is no longer needed
 */
class StockMvtCore extends ObjectModel
{
    public $id;

    /**
     * @var string The creation date of the movement
     */
    public $date_add;

    /**
     * @var int The employee id, responsible of the movement
     */
    public $id_employee;

    /**
     * @since 1.5.0
     *
     * @var string The first name of the employee responsible of the movement
     */
    public $employee_firstname;

    /**
     * @since 1.5.0
     *
     * @var string The last name of the employee responsible of the movement
     */
    public $employee_lastname;

    /**
     * @since 1.5.0
     *
     * @var int The stock id on wtich the movement is applied
     */
    public $id_stock;

    /**
     * @since 1.5.0
     *
     * @var int the quantity of product with is moved
     */
    public $physical_quantity;

    /**
     * @var int id of the movement reason assoiated to the movement
     */
    public $id_stock_mvt_reason;

    /**
     * @var int Used when the movement is due to a customer order
     */
    public $id_order = null;

    /**
     * @since 1.5.0
     *
     * @var int detrmine if the movement is a positive or negative operation
     */
    public $sign;

    /**
     * @since 1.5.0
     *
     * @var int Used when the movement is due to a supplier order
     */
    public $id_supply_order = null;

    /**
     * @since 1.5.0
     *
     * @var float Last value of the weighted-average method
     */
    public $last_wa = null;

    /**
     * @since 1.5.0
     *
     * @var float Current value of the weighted-average method
     */
    public $current_wa = null;

    /**
     * @since 1.5.0
     *
     * @var float The unit price without tax of the product associated to the movement
     */
    public $price_te;

    /**
     * @since 1.5.0
     *
     * @var int Refers to an other id_stock_mvt : used for LIFO/FIFO implementation in StockManager
     */
    public $referer;

    /**
     * @see ObjectModel::$definition
     */
    public static $definition = [
        'table' => 'stock_mvt',
        'primary' => 'id_stock_mvt',
        'fields' => [
            'id_employee' => ['type' => self::TYPE_INT, 'validate' => 'isUnsignedId', 'required' => true],
            'employee_firstname' => ['type' => self::TYPE_STRING, 'validate' => 'isName'],
            'employee_lastname' => ['type' => self::TYPE_STRING, 'validate' => 'isName'],
            'id_stock' => ['type' => self::TYPE_INT, 'validate' => 'isUnsignedId', 'required' => true],
            'physical_quantity' => ['type' => self::TYPE_INT, 'validate' => 'isUnsignedInt', 'required' => true],
            'id_stock_mvt_reason' => ['type' => self::TYPE_INT, 'validate' => 'isUnsignedId', 'required' => true],
            'id_order' => ['type' => self::TYPE_INT, 'validate' => 'isUnsignedId'],
            'id_supply_order' => ['type' => self::TYPE_INT, 'validate' => 'isUnsignedId'],
            'sign' => ['type' => self::TYPE_INT, 'validate' => 'isInt', 'required' => true],
            'last_wa' => ['type' => self::TYPE_FLOAT, 'validate' => 'isPrice'],
            'current_wa' => ['type' => self::TYPE_FLOAT, 'validate' => 'isPrice'],
            'price_te' => ['type' => self::TYPE_FLOAT, 'validate' => 'isPrice', 'required' => true],
            'referer' => ['type' => self::TYPE_INT, 'validate' => 'isUnsignedId'],
            'date_add' => ['type' => self::TYPE_DATE, 'validate' => 'isDate', 'required' => true],
        ],
    ];

    protected $webserviceParameters = [
        'objectsNodeName' => 'stock_movements',
        'objectNodeName' => 'stock_movement',
        'fields' => [
            'id_employee' => ['xlink_resource' => 'employees'],
            'id_stock' => ['xlink_resource' => 'stock'],
            'id_stock_mvt_reason' => ['xlink_resource' => 'stock_movement_reasons'],
            'id_order' => ['xlink_resource' => 'orders'],
            'id_supply_order' => ['xlink_resource' => 'supply_order'],
        ],
    ];

    /**
     * Gets the negative (decrements the stock) stock mvts that correspond to the given order, for :
     * the given product, in the given quantity.
     *
     * @since 1.5.0
     *
     * @param int $id_order
     * @param int $id_product
     * @param int $id_product_attribute Use 0 if the product does not have attributes
     * @param int $quantity
     * @param int $id_warehouse Optional
     *
     * @return array mvts
     */
    public static function getNegativeStockMvts($id_order, $id_product, $id_product_attribute, $quantity, $id_warehouse = null)
    {
        $movements = [];
        $quantity_total = 0;

        // preps query
        $query = new DbQuery();
        $query->select('sm.*, s.id_warehouse');
        $query->from('stock_mvt', 'sm');
        $query->innerJoin('stock', 's', 's.id_stock = sm.id_stock');
        $query->where('sm.sign = -1');
        $query->where('sm.id_order = ' . (int) $id_order);
        $query->where('s.id_product = ' . (int) $id_product . ' AND s.id_product_attribute = ' . (int) $id_product_attribute);

        // if filer by warehouse
        if (null !== $id_warehouse) {
            $query->where('s.id_warehouse = ' . (int) $id_warehouse);
        }

        // orders the movements by date
        $query->orderBy('date_add DESC');

        // gets the result
        $res = Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS($query, false);

        // fills the movements array
        while ($row = Db::getInstance(_PS_USE_SQL_SLAVE_)->nextRow($res)) {
            if ($quantity_total >= $quantity) {
                break;
            }
            $quantity_total += (int) $row['physical_quantity'];
            $movements[] = $row;
        }

        return $movements;
    }

    /**
     * For a given product, gets the last positive stock mvt.
     *
     * @since 1.5.0
     *
     * @param int $id_product
     * @param int $id_product_attribute Use 0 if the product does not have attributes
     *
     * @return bool|array
     */
    public static function getLastPositiveStockMvt($id_product, $id_product_attribute)
    {
        $query = new DbQuery();
        $query->select('sm.*, w.id_currency, (s.usable_quantity = sm.physical_quantity) as is_usable');
        $query->from('stock_mvt', 'sm');
        $query->innerJoin('stock', 's', 's.id_stock = sm.id_stock');
        $query->innerJoin('warehouse', 'w', 'w.id_warehouse = s.id_warehouse');
        $query->where('sm.sign = 1');
        if ($id_product_attribute) {
            $query->where('s.id_product = ' . (int) $id_product . ' AND s.id_product_attribute = ' . (int) $id_product_attribute);
        } else {
            $query->where('s.id_product = ' . (int) $id_product);
        }
        $query->orderBy('date_add DESC');

        $res = Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS($query);

        if ($res != false) {
            return $res['0'];
        }

        return false;
    }
}
