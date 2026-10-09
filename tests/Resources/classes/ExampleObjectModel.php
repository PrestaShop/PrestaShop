<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Tests\Resources\classes;

use ObjectModel;

class ExampleObjectModel extends ObjectModel
{
    /**
     * @var int
     */
    public $id_example_object_model;

    /**
     * @var string
     */
    public $string_field;

    /**
     * @var string|string[]
     */
    public $string_multilang_field;

    /**
     * @var int
     */
    public $int_field;

    /**
     * @var bool
     */
    public $bool_field;

    /**
     * @var string
     */
    public $date_field;

    /**
     * @var float
     */
    public $float_field;

    public static $definition = [
        'table' => 'example_object_model',
        'primary' => 'id_example_object_model',
        'multilang' => true,
        'multilang_shop' => true,
        'fields' => [
            'string_field' => ['type' => self::TYPE_STRING],
            'string_multilang_field' => ['type' => self::TYPE_STRING, 'lang' => true],
            'int_field' => ['type' => self::TYPE_INT],
            'bool_field' => ['type' => self::TYPE_BOOL],
            'date_field' => ['type' => self::TYPE_DATE],
            'float_field' => ['type' => self::TYPE_FLOAT],
        ],
    ];
}
