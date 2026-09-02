<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

use PrestaShop\PrestaShop\Core\Domain\Product\Customization\CustomizationFieldSettings;

/**
 * Class CustomizationFieldCore.
 */
class CustomizationFieldCore extends ObjectModel
{
    /** @var int */
    public $id_product;
    /** @var int Customization type (0 File, 1 Textfield) (See Product class) */
    public $type;
    /** @var bool Field is required */
    public $required;
    /** @var bool Field was added by a module */
    public $is_module;
    /** @var string[] Label for customized field */
    public $name;
    /** @var bool Soft delete */
    public $is_deleted;

    /**
     * @see ObjectModel::$definition
     */
    public static $definition = [
        'table' => 'customization_field',
        'primary' => 'id_customization_field',
        'multilang' => true,
        'multilang_shop' => true,
        'fields' => [
            /* Classic fields */
            'id_product' => ['type' => self::TYPE_INT, 'validate' => 'isUnsignedId', 'required' => true],
            'type' => ['type' => self::TYPE_INT, 'validate' => 'isUnsignedId', 'required' => true],
            'required' => ['type' => self::TYPE_BOOL, 'validate' => 'isBool', 'required' => true],
            'is_module' => ['type' => self::TYPE_BOOL, 'validate' => 'isBool', 'required' => false],
            'is_deleted' => ['type' => self::TYPE_BOOL, 'validate' => 'isBool', 'required' => false],

            /* Lang fields */
            'name' => ['type' => self::TYPE_STRING, 'lang' => true, 'required' => true, 'size' => CustomizationFieldSettings::MAX_NAME_LENGTH],
        ],
    ];

    /** @var array */
    protected $webserviceParameters = [
        'fields' => [
            'id_product' => [
                'xlink_resource' => [
                    'resourceName' => 'products',
                ],
            ],
        ],
    ];
}
