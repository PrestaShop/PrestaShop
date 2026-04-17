<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

use PrestaShop\PrestaShop\Core\Domain\OrderReturnState\OrderReturnStateSettings;

class OrderReturnStateCore extends ObjectModel
{
    /** @var string|array<int, string> Name */
    public $name;

    /** @var string Display state in the specified color */
    public $color;

    /**
     * @see ObjectModel::$definition
     */
    public static $definition = [
        'table' => 'order_return_state',
        'primary' => 'id_order_return_state',
        'multilang' => true,
        'fields' => [
            'color' => ['type' => self::TYPE_STRING, 'validate' => 'isColor', 'size' => 32],

            /* Lang fields */
            'name' => ['type' => self::TYPE_STRING, 'lang' => true, 'validate' => 'isGenericName', 'required' => true, 'size' => OrderReturnStateSettings::NAME_MAX_LENGTH],
        ],
    ];

    /**
     * Get all available order statuses.
     *
     * @param int $id_lang Language id for status name
     *
     * @return array Order statuses
     */
    public static function getOrderReturnStates($id_lang)
    {
        return Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS('
        SELECT *
        FROM `' . _DB_PREFIX_ . 'order_return_state` ors
        LEFT JOIN `' . _DB_PREFIX_ . 'order_return_state_lang` orsl ON (ors.`id_order_return_state` = orsl.`id_order_return_state` AND orsl.`id_lang` = ' . (int) $id_lang . ')
        ORDER BY ors.`id_order_return_state` ASC');
    }
}
