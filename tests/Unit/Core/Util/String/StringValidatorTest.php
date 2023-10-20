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

namespace Tests\Unit\Core\Util\String;

use PHPUnit\Framework\TestCase;
use PrestaShop\PrestaShop\Core\Util\String\StringValidator;

class StringValidatorTest extends TestCase
{
    public function testItDetectsThatStringStartsWith()
    {
        $data = [
            [
                'string' => 'startString',
                'starts_with' => 'start',
                'expects' => true,
            ],
            [
                'string' => 'start another start string',
                'starts_with' => 'start',
                'expects' => true,
            ],
            [
                'string' => ' start',
                'starts_with' => 'start',
                'expects' => false,
            ],
        ];

        $stringValidator = new StringValidator();

        foreach ($data as $item) {
            $result = $stringValidator->startsWith($item['string'], $item['starts_with']);

            $this->assertEquals($item['expects'], $result);
        }
    }

    public function testItDetectsThatStringEndsWith()
    {
        $data = [
            [
                'string' => 'startString',
                'ends_with' => 'String',
                'expects' => true,
            ],
            [
                'string' => 'start . another . string.',
                'ends_with' => '.',
                'expects' => true,
            ],
            [
                'string' => ' start.',
                'ends_with' => 'start',
                'expects' => false,
            ],
        ];

        $stringValidator = new StringValidator();

        foreach ($data as $item) {
            $result = $stringValidator->endsWith($item['string'], $item['ends_with']);

            $this->assertEquals($item['expects'], $result);
        }
    }

    public function testStringStartsWithAndEndsWith()
    {
        $data = [
            [
                'string' => 'actionSomethingModifier',
                'prefix' => 'action',
                'suffix' => 'Modifier',
                'expects' => true,
            ],
            [
                'string' => 'start . another . string.',
                'prefix' => 'start',
                'suffix' => '. string.',
                'expects' => true,
            ],
            [
                'string' => ' start.',
                'prefix' => ' ',
                'suffix' => 't',
                'expects' => false,
            ],
        ];

        $stringValidator = new StringValidator();

        foreach ($data as $item) {
            $result = $stringValidator->startsWithAndEndsWith(
                $item['string'],
                $item['prefix'],
                $item['suffix']
            );

            $this->assertEquals($item['expects'], $result);
        }
    }

    public function testStringContainsWhiteSpaces()
    {
        $data = [
            [
                'string' => 'action Something Modifier',
                'expects' => true,
            ],
            [
                'string' => 'tab    ',
                'expects' => true,
            ],
            [
                'string' => 'noWhiteSpace',
                'expects' => false,
            ],
        ];

        $stringValidator = new StringValidator();

        foreach ($data as $item) {
            $result = $stringValidator->doesContainsWhiteSpaces($item['string']);
            $this->assertEquals($item['expects'], $result);
        }
    }
}
