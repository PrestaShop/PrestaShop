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
 * Class DeliveryCore.
 */
class DeliveryCore extends ObjectModel
{
    /** @var int */
    public $id_delivery;

    /** @var int * */
    public $id_shop;

    /** @var int * */
    public $id_shop_group;

    /** @var int */
    public $id_carrier;

    /** @var int */
    public $id_range_price;

    /** @var int */
    public $id_range_weight;

    /** @var int */
    public $id_zone;

    /** @var float */
    public $price;

    /**
     * @see ObjectModel::$definition
     */
    public static $definition = [
        'table' => 'delivery',
        'primary' => 'id_delivery',
        'fields' => [
            'id_carrier' => ['type' => self::TYPE_INT, 'validate' => 'isUnsignedId', 'required' => true],
            'id_range_price' => ['type' => self::TYPE_INT, 'validate' => 'isUnsignedId', 'required' => true],
            'id_range_weight' => ['type' => self::TYPE_INT, 'validate' => 'isUnsignedId', 'required' => true],
            'id_zone' => ['type' => self::TYPE_INT, 'validate' => 'isUnsignedId', 'required' => true],
            'id_shop' => ['type' => self::TYPE_INT],
            'id_shop_group' => ['type' => self::TYPE_INT],
            'price' => ['type' => self::TYPE_FLOAT, 'validate' => 'isPrice', 'required' => true],
        ],
    ];

    protected $webserviceParameters = [
        'objectsNodeName' => 'deliveries',
        'fields' => [
            'id_carrier' => ['xlink_resource' => 'carriers'],
            'id_range_price' => ['xlink_resource' => 'price_ranges'],
            'id_range_weight' => ['xlink_resource' => 'weight_ranges'],
            'id_zone' => ['xlink_resource' => 'zones'],
        ],
    ];

    /**
     * Get Object fields and values in array.
     *
     * @return array
     */
    public function getFields()
    {
        $fields = parent::getFields();

        // @todo add null management in definitions
        if ($this->id_shop) {
            $fields['id_shop'] = (int) $this->id_shop;
        } else {
            $fields['id_shop'] = null;
        }

        if ($this->id_shop_group) {
            $fields['id_shop_group'] = (int) $this->id_shop_group;
        } else {
            $fields['id_shop_group'] = null;
        }

        return $fields;
    }
}
