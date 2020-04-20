<?php
/**
 * 2007-2020 PrestaShop SA and Contributors
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
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
 * needs please refer to https://www.prestashop.com for more information.
 *
 * @author    PrestaShop SA <contact@prestashop.com>
 * @copyright 2007-2020 PrestaShop SA and Contributors
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 * International Registered Trademark & Property of PrestaShop SA
 */

namespace LegacyTests\Unit\Classes;

use LegacyTests\TestCase\UnitTestCase;
use Alias;

class ObjectModelTest extends UnitTestCase
{
    /**
     * {@inheritdoc}
     */
    protected function setUp()
    {
        # Old retro compatible way
        Alias::$definition['fields']['fullname'] = [
            'type' => Alias::TYPE_STRING,
            'validate' => 'isString'
        ];

        # What should be documented post 1.7.8
        Alias::$definition['fields']['whatever'] = [
            'type' => Alias::TYPE_INT,
            'validate' => 'Validate::isInt'
        ];

        # What is also possible post 1.7.8
        Alias::$definition['fields']['gender'] = [
            'type' => Alias::TYPE_STRING,
            'validate' => 'LegacyTests\Unit\Classes\CustomValidator::isValidGender',
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function teardown()
    {
        unset(
            Alias::$definition['fields']['fullname'],
            Alias::$definition['fields']['whatever'],
            Alias::$definition['fields']['gender']
        );
    }

    public function testValidateField()
    {
        $alias = new Alias();

        self::assertTrue($alias->validateField('fullname', 'Mickaël Andrieu'));
        self::assertNotTrue($alias->validateField('whatever', 'not a number'));
        self::assertTrue($alias->validateField('gender', 'MALE'));
    }
}

/**
 * This class must be only used for this test !
 */
class CustomValidator {
    public static function isValidGender($value)
    {
        return in_array($value, [
            'MALE',
            'FEMALE',
            'NOT_BINARY',
        ]);
    }
}
