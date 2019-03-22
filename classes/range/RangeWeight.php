<?php
/**
 * 2007-2019 PrestaShop and Contributors
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
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
 * needs please refer to https://www.prestashop.com for more information.
 *
 * @author    PrestaShop SA <contact@prestashop.com>
 * @copyright 2007-2019 PrestaShop SA and Contributors
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 * International Registered Trademark & Property of PrestaShop SA
 */
class RangeWeightCore extends ObjectModel
{
    /**
     * @var int
     */
    public $id_carrier;

    /**
     * @var float
     */
    public $delimiter1;

    /**
     * @var float
     */
    public $delimiter2;

    /**
     * @see ObjectModel::$definition
     */
    public static $definition = array(
        'table' => 'range_weight',
        'primary' => 'id_range_weight',
        'fields' => array(
            'id_carrier' => array('type' => self::TYPE_INT, 'validate' => 'isInt', 'required' => true),
            'delimiter1' => array('type' => self::TYPE_FLOAT, 'validate' => 'isUnsignedFloat', 'required' => true),
            'delimiter2' => array('type' => self::TYPE_FLOAT, 'validate' => 'isUnsignedFloat', 'required' => true),
        ),
    );

    protected $webserviceParameters = array(
        'objectNodeName' => 'weight_range',
        'objectsNodeName' => 'weight_ranges',
        'fields' => array(
            'id_carrier' => array('xlink_resource' => 'carriers'),
        ),
    );

    /**
     * Override add to create delivery value for all zones.
     *
     * @see classes/ObjectModelCore::add()
     *
     * @param bool $null_values
     * @param bool $autodate
     *
     * @return bool Insertion result
     */
    public function add($autodate = true, $null_values = false)
    {
        if (!parent::add($autodate, $null_values) || !Validate::isLoadedObject($this)) {
            return false;
        }
        if (defined('PS_INSTALLATION_IN_PROGRESS')) {
            return true;
        }
        $carrier = new Carrier((int) $this->id_carrier);
        $price_list = array();
        foreach ($carrier->getZones() as $zone) {
            $price_list[] = array(
                'id_range_price' => null,
                'id_range_weight' => (int) $this->id,
                'id_carrier' => (int) $this->id_carrier,
                'id_zone' => (int) $zone['id_zone'],
                'price' => 0,
            );
        }
        $carrier->addDeliveryPrice($price_list);

        return true;
    }

    /**
     * Get all available price weight.
     *
     * @param int $id_carrier Carrier identifier
     *
     * @return array|false All range for this carrier
     */
    public static function getRanges($id_carrier)
    {
        $query = new DbQuery();
        $query->select('*');
        $query->from(static::$definition['table']);
        $query->where('id_carrier = ' . (int) $id_carrier);
        $query->orderBy('delimiter1 ASC');

        return Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS($query);
    }

    /**
     * Check if a range exist for delimiter1 and delimiter2 by id_carrier or id_reference
     *
     * @param int|null $id_carrier Carrier identifier
     * @param float $delimiter1
     * @param float $delimiter2
     * @param int|null $id_reference Carrier reference is the initial Carrier identifier (optional)
     *
     * @return int|bool Number of existing range
     */
    public static function rangeExist($id_carrier, $delimiter1, $delimiter2, $id_reference = null)
    {
        $query = new DbQuery();
        $query->select('COUNT(r.' . static::$definition['primary'] . ')');
        $query->from(static::$definition['table'], 'r');
        $query->where('r.delimiter1 = ' . (float) $delimiter1);
        $query->where('r.delimiter2 = ' . (float) $delimiter2);

        if ($id_carrier) {
            $query->where('r.id_carrier = ' . (int) $id_carrier);
        }

        if ($id_reference) {
            $query->innerJoin('carrier', 'c', 'r.id_carrier = c.id_carrier');
            $query->where('c.id_reference = ' . (int) $id_reference);
        }

        return Db::getInstance(_PS_USE_SQL_SLAVE_)->getValue($query);
    }

    /**
     * Check if a range overlap another range for this carrier
     *
     * @param int $id_carrier Carrier identifier
     * @param float $delimiter1
     * @param float $delimiter2
     * @param int|null $id_range RangeWeight identifier (optional)
     *
     * @return int|bool Number of range overlap
     */
    public static function isOverlapping($id_carrier, $delimiter1, $delimiter2, $id_range = null)
    {
        $query = new DbQuery();
        $query->select('COUNT(' . static::$definition['primary'] . ')');
        $query->from(static::$definition['table']);
        $query->where('id_carrier = ' . (int) $id_carrier);
        $overlapCondition = '(delimiter1 >= ' . (float) $delimiter1 . ' AND delimiter1 < ' . (float) $delimiter2 . ')';
        $overlapCondition .= ' OR (delimiter2 > ' . (float) $delimiter1 . ' AND delimiter2 < ' . (float) $delimiter2 . ')';
        $overlapCondition .= ' OR (' . (float) $delimiter1 . ' > delimiter1 AND ' . (float) $delimiter1 . ' < delimiter2)';
        $overlapCondition .= ' OR (' . (float) $delimiter2 . ' < delimiter1 AND ' . (float) $delimiter2 . ' > delimiter2)';
        $query->where($overlapCondition);
        if ($id_range) {
            $query->where(static::$definition['primary'] . ' != ' . (int) $id_range);
        }

        return Db::getInstance(_PS_USE_SQL_SLAVE_)->getValue($query);
    }
}
