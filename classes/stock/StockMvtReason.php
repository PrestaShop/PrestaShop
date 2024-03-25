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
 * Object model used for installation and webservice purposes.
 */
class StockMvtReasonCore extends ObjectModel
{
    /** @var int identifier of the movement reason */
    public $id;

    /** @var string the name of the movement reason */
    public $name;

    /** @var int detrmine if the movement reason correspond to a positive or negative operation */
    public $sign;

    /** @var string the creation date of the movement reason */
    public $date_add;

    /** @var string the last update date of the movement reason */
    public $date_upd;

    /** @var bool True if the movement reason has been deleted (staying in database as deleted) */
    public $deleted = false;

    /**
     * @since 1.5.0
     * @see ObjectModel::$definition
     */
    public static $definition = [
        'table' => 'stock_mvt_reason',
        'primary' => 'id_stock_mvt_reason',
        'multilang' => true,
        'fields' => [
            'sign' => ['type' => self::TYPE_INT],
            'deleted' => ['type' => self::TYPE_BOOL],
            'date_add' => ['type' => self::TYPE_DATE, 'validate' => 'isDate'],
            'date_upd' => ['type' => self::TYPE_DATE, 'validate' => 'isDate'],
            'name' => ['type' => self::TYPE_STRING, 'lang' => true, 'validate' => 'isGenericName', 'required' => true, 'size' => 255],
        ],
    ];

    /**
     * @see ObjectModel::$webserviceParameters
     */
    protected $webserviceParameters = [
        'objectsNodeName' => 'stock_movement_reasons',
        'objectNodeName' => 'stock_movement_reason',
        'fields' => [
            'sign' => [],
        ],
    ];
}
