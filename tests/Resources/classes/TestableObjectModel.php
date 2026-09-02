<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
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
