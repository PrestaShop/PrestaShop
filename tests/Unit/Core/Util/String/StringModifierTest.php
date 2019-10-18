<?php
/**
 * 2007-2019 PrestaShop SA and Contributors
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
 * @copyright 2007-2019 PrestaShop SA and Contributors
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 * International Registered Trademark & Property of PrestaShop SA
 */

namespace Tests\Unit\Core\Util\String;

use PHPUnit\Framework\TestCase;
use PrestaShop\PrestaShop\Core\Util\String\StringModifier;

class StringModifierTest extends TestCase
{
    public function testItTransformsCamelCaseToSplitWords()
    {
        $data = [
            [
                'string' => 'oneTwoThreeFour',
                'expects' => 'one Two Three Four',
            ],
            [
                'string' => 'StartsWithCap',
                'expects' => 'Starts With Cap',
            ],
            [
                'string' => 'hasConsecutiveCAPS',
                'expects' => 'has Consecutive CAPS',
            ],
            [
                'string' => 'NewMODULEDevelopment',
                'expects' => 'New MODULE Development',
            ],
            [
                'string' => 'snake_case_word',
                'expects' => 'snake_case_word',
            ],
        ];

        $stringModifier = new StringModifier();

        foreach ($data as $item) {
            $result = $stringModifier->splitByCamelCase($item['string']);

            $this->assertEquals($item['expects'], $result);
        }
    }
}
