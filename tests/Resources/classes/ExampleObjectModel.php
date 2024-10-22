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
     * @var date
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
            'string_field' => ['type' => self::TYPE_INT],
            'string_multilang_field' => ['type' => self::TYPE_STRING, 'lang' => true],
            'int_field' => ['type' => self::TYPE_INT],
            'bool_field' => ['type' => self::TYPE_BOOL],
            'date_field' => ['type' => self::TYPE_DATE],
            'float_field' => ['type' => self::TYPE_FLOAT],
        ],
    ];
}
