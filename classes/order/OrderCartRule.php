<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */
class OrderCartRuleCore extends ObjectModel
{
    /** @var int */
    public $id_order_cart_rule;

    /** @var int */
    public $id_order;

    /** @var int */
    public $id_cart_rule;

    /** @var int */
    public $id_order_invoice;

    /** @var string */
    public $name;

    /** @var float value (tax incl.) of voucher */
    public $value;

    /** @var float value (tax excl.) of voucher */
    public $value_tax_excl;

    /** @var bool value : voucher gives free shipping or not */
    public $free_shipping;

    /** @var bool value : deleted from order */
    public $deleted = false;

    /**
     * @see ObjectModel::$definition
     */
    public static $definition = [
        'table' => 'order_cart_rule',
        'primary' => 'id_order_cart_rule',
        'fields' => [
            'id_order' => ['type' => self::TYPE_INT, 'validate' => 'isUnsignedId', 'required' => true],
            'id_cart_rule' => ['type' => self::TYPE_INT, 'validate' => 'isUnsignedId', 'required' => true],
            'id_order_invoice' => ['type' => self::TYPE_INT, 'validate' => 'isUnsignedId'],
            'name' => ['type' => self::TYPE_HTML, 'required' => true, 'size' => 254],
            'value' => ['type' => self::TYPE_FLOAT, 'validate' => 'isFloat', 'required' => true],
            'value_tax_excl' => ['type' => self::TYPE_FLOAT, 'validate' => 'isFloat', 'required' => true],
            'free_shipping' => ['type' => self::TYPE_BOOL, 'validate' => 'isBool'],
            'deleted' => ['type' => self::TYPE_BOOL, 'validate' => 'isBool'],
        ],
    ];

    protected $webserviceParameters = [
        'fields' => [
            'id_order' => ['xlink_resource' => 'orders'],
        ],
    ];
}
