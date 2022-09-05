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
 * @since 1.5.0
 */
class SupplyOrderStateCore extends ObjectModel
{
    /**
     * @var string Name of the state
     */
    public $name;

    /**
     * @var bool Tells if a delivery note can be issued (i.e. the order has been validated)
     */
    public $delivery_note;

    /**
     * @var bool Tells if the order is still editable by an employee (i.e. you can add products)
     */
    public $editable;

    /**
     * @var bool Tells if the the order has been delivered
     */
    public $receipt_state;

    /**
     * @var bool Tells if the the order is in a state corresponding to a product pending receipt
     */
    public $pending_receipt;

    /**
     * @var bool Tells if the the order is in an enclosed state (i.e. terminated, canceled)
     */
    public $enclosed;

    /**
     * @var string Color used to display the state in the specified color (Ex. #FFFF00)
     */
    public $color;

    /**
     * @see ObjectModel::$definition
     */
    public static $definition = [
        'table' => 'supply_order_state',
        'primary' => 'id_supply_order_state',
        'multilang' => true,
        'fields' => [
            'delivery_note' => ['type' => self::TYPE_BOOL, 'validate' => 'isBool'],
            'editable' => ['type' => self::TYPE_BOOL, 'validate' => 'isBool'],
            'receipt_state' => ['type' => self::TYPE_BOOL, 'validate' => 'isBool'],
            'pending_receipt' => ['type' => self::TYPE_BOOL, 'validate' => 'isBool'],
            'enclosed' => ['type' => self::TYPE_BOOL, 'validate' => 'isBool'],
            'color' => ['type' => self::TYPE_STRING, 'validate' => 'isColor'],
            'name' => ['type' => self::TYPE_STRING, 'lang' => true, 'validate' => 'isGenericName', 'required' => true, 'size' => 128],
        ],
    ];

    /**
     * @see ObjectModel::$webserviceParameters
     */
    protected $webserviceParameters = [
        'objectsNodeName' => 'supply_order_states',
        'objectNodeName' => 'supply_order_state',
        'fields' => [
        ],
    ];

    /**
     * Gets the list of supply order statuses.
     *
     * @param int $id_state_referrer Optional, used to know what state is available after this one
     * @param int $id_lang Optional Id Language
     *
     * @return array States
     */
    public static function getSupplyOrderStates($id_state_referrer = null, $id_lang = null)
    {
        if ($id_lang == null) {
            $id_lang = Context::getContext()->language->id;
        }

        $query = new DbQuery();
        $query->select('sl.name, s.id_supply_order_state');
        $query->from('supply_order_state', 's');
        $query->leftjoin('supply_order_state_lang', 'sl', 's.id_supply_order_state = sl.id_supply_order_state AND sl.id_lang=' . (int) $id_lang);

        if (null !== $id_state_referrer) {
            $is_receipt_state = false;
            $is_editable = false;
            $is_delivery_note = false;
            $is_pending_receipt = false;

            //check current state to see what state is available
            $state = new SupplyOrderState((int) $id_state_referrer);
            if (Validate::isLoadedObject($state)) {
                $is_receipt_state = $state->receipt_state;
                $is_editable = $state->editable;
                $is_delivery_note = $state->delivery_note;
                $is_pending_receipt = $state->pending_receipt;
            }

            $query->where('s.id_supply_order_state <> ' . (int) $id_state_referrer);

            //check first if the order is editable
            if ($is_editable) {
                $query->where('s.editable = 1 OR s.delivery_note = 1 OR s.enclosed = 1');
            } elseif ($is_delivery_note || $is_pending_receipt) {
                //check if the delivery note is available or if the state correspond to a pending receipt state
                $query->where('(s.delivery_note = 0 AND s.editable = 0) OR s.enclosed = 1');
            } elseif ($is_receipt_state) {
                //check if the state correspond to a receipt state
                $query->where('s.receipt_state = 1');
            }
        }

        return Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS($query);
    }

    /**
     * Gets the list of supply order statuses.
     *
     * @param array|null $ids Optional Do not include these ids in the result
     * @param int $id_lang Optional
     *
     * @return array
     */
    public static function getStates($ids = null, $id_lang = null)
    {
        if ($id_lang == null) {
            $id_lang = Context::getContext()->language->id;
        }

        if (!is_array($ids)) {
            $ids = [];
        }

        $query = new DbQuery();
        $query->select('sl.name, s.id_supply_order_state');
        $query->from('supply_order_state', 's');
        $query->leftjoin('supply_order_state_lang', 'sl', 's.id_supply_order_state = sl.id_supply_order_state AND sl.id_lang=' . (int) $id_lang);
        if ($ids) {
            $query->where('s.id_supply_order_state NOT IN(' . implode(',', array_map('intval', $ids)) . ')');
        }

        $query->orderBy('sl.name ASC');

        return Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS($query);
    }
}
