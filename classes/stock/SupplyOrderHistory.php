<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

/**
 * @deprecated since 9.0 and will be removed in 10.0
 */
class SupplyOrderHistoryCore extends ObjectModel
{
    /**
     * @var int Supply order Id
     */
    public $id_supply_order;

    /**
     * @var int Employee Id
     */
    public $id_employee;

    /**
     * @var string The first name of the employee responsible of the movement
     */
    public $employee_firstname;

    /**
     * @var string The last name of the employee responsible of the movement
     */
    public $employee_lastname;

    /**
     * @var int State of the supply order
     */
    public $id_state;

    /**
     * @var string Date
     */
    public $date_add;

    /**
     * @see ObjectModel::$definition
     */
    public static $definition = [
        'table' => 'supply_order_history',
        'primary' => 'id_supply_order_history',
        'fields' => [
            'id_supply_order' => ['type' => self::TYPE_INT, 'validate' => 'isUnsignedId', 'required' => true],
            'id_employee' => ['type' => self::TYPE_INT, 'validate' => 'isUnsignedId', 'required' => true],
            'employee_firstname' => ['type' => self::TYPE_STRING, 'validate' => 'isName'],
            'employee_lastname' => ['type' => self::TYPE_STRING, 'validate' => 'isName'],
            'id_state' => ['type' => self::TYPE_INT, 'validate' => 'isUnsignedId', 'required' => true],
            'date_add' => ['type' => self::TYPE_DATE, 'validate' => 'isDate', 'required' => true],
        ],
    ];

    /**
     * @see ObjectModel::$webserviceParameters
     */
    protected $webserviceParameters = [
        'objectsNodeName' => 'supply_order_histories',
        'objectNodeName' => 'supply_order_history',
        'fields' => [
            'id_supply_order' => ['xlink_resource' => 'supply_orders'],
            'id_employee' => ['xlink_resource' => 'employees'],
            'id_state' => ['xlink_resource' => 'supply_order_states'],
        ],
    ];
}
