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
 * needs please refer to https://devdocs.prestashop-project.org/ for more information.
 *
 * @author    PrestaShop SA and Contributors <contact@prestashop.com>
 * @copyright Since 2007 PrestaShop SA and Contributors
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 */

declare(strict_types=1);

namespace Tests\Resources\classes;

use ObjectModel;
use Shop;

class TestableObjectModel extends ObjectModel
{
    /**
     * @var int
     */
    public $id_testable_object;

    /**
     * This field is multilang and multi shop
     *
     * @var string|string[]
     */
    public $name;

    /**
     * This field is global to all shops
     *
     * @var int
     */
    public $quantity;

    /**
     * This field is multishop
     *
     * @var bool
     */
    public $enabled;

    public static $definition = [
        'table' => 'testable_object',
        'primary' => 'id_testable_object',
        'multilang' => true,
        'multilang_shop' => true,
        'fields' => [
            // Classic fields
            'quantity' => ['type' => self::TYPE_INT, 'validate' => 'isUnsignedFloat'],
            // Multi lang fields
            'name' => ['type' => self::TYPE_STRING, 'lang' => true, 'validate' => 'isCatalogName', 'required' => false, 'size' => 128],
            // Shop fields
            'enabled' => ['type' => self::TYPE_BOOL, 'shop' => true, 'validate' => 'isBool'],
        ],
    ];

    public function __construct($id = null, $id_lang = null, $id_shop = null)
    {
        Shop::addTableAssociation('testable_object', ['type' => 'shop']);
        Shop::addTableAssociation('testable_object_lang', ['type' => 'fk_shop']);
        parent::__construct($id, $id_lang, $id_shop);
    }
}
