<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

use PrestaShopBundle\Form\Admin\Type\FormattedTextareaType;

class OrderMessageCore extends ObjectModel
{
    /** @var string|array<int, string> Name */
    public $name;

    /** @var string|array<int, string> Message content */
    public $message;

    /** @var string Object creation date */
    public $date_add;

    /**
     * @see ObjectModel::$definition
     */
    public static $definition = [
        'table' => 'order_message',
        'primary' => 'id_order_message',
        'multilang' => true,
        'fields' => [
            'date_add' => ['type' => self::TYPE_DATE, 'validate' => 'isDate'],

            /* Lang fields */
            'name' => ['type' => self::TYPE_STRING, 'lang' => true, 'validate' => 'isGenericName', 'required' => true, 'size' => 128],
            'message' => ['type' => self::TYPE_STRING, 'lang' => true, 'validate' => 'isMessage', 'required' => true, 'size' => FormattedTextareaType::LIMIT_MEDIUMTEXT_UTF8_MB4],
        ],
    ];

    protected $webserviceParameters = [
        'fields' => [
            'id' => ['sqlId' => 'id_discount_type', 'xlink_resource' => 'order_message_lang'],
            'date_add' => ['sqlId' => 'date_add'],
        ],
    ];

    public static function getOrderMessages($id_lang)
    {
        return Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS('
        SELECT om.id_order_message, oml.name, oml.message
        FROM ' . _DB_PREFIX_ . 'order_message om
        LEFT JOIN ' . _DB_PREFIX_ . 'order_message_lang oml ON (oml.id_order_message = om.id_order_message)
        WHERE oml.id_lang = ' . (int) $id_lang . '
        ORDER BY name ASC');
    }
}
