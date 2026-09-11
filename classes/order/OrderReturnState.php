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

    /** Cancel return products */
    public bool $is_cancelling_return = false;

    /**
     * @see ObjectModel::$definition
     */
    public static $definition = [
        'table' => 'order_return_state',
        'primary' => 'id_order_return_state',
        'multilang' => true,
        'fields' => [
            'color' => ['type' => self::TYPE_STRING, 'validate' => 'isColor', 'size' => 32],
            'is_cancelling_return' => ['type' => self::TYPE_BOOL, 'validate' => 'isBool'],

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

    /**
     * Check if a localized name is already used by another order return state.
     *
     * WHY: the name identifies the state everywhere it is displayed, so two states sharing one are
     * indistinguishable in the back office lists and in the customer's return history.
     *
     * @param string $name
     * @param int $idLang
     * @param int|null $excludeIdOrderReturnState ID of the order return state excluded from the search
     *
     * @return bool
     */
    public static function existsLocalizedNameInDatabase(string $name, int $idLang, ?int $excludeIdOrderReturnState): bool
    {
        return (bool) Db::getInstance(_PS_USE_SQL_SLAVE_)->getValue(
            'SELECT COUNT(*) AS count' .
            ' FROM ' . _DB_PREFIX_ . 'order_return_state_lang orsl' .
            ' WHERE orsl.id_lang = ' . $idLang .
            ' AND orsl.name = \'' . pSQL($name) . '\'' .
            ($excludeIdOrderReturnState ? ' AND orsl.id_order_return_state != ' . $excludeIdOrderReturnState : '')
        );
    }
}
