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

namespace Tests\Unit\Core\Localization\Currency;

use PHPUnit\Framework\TestCase;
use PrestaShop\PrestaShop\Core\Localization\Currency\PatternTransformer;

class PatternTransformerTest extends TestCase
{
    /**
     * @dataProvider getPatterns
     *
     * @param string $pattern
     * @param array $transformations
     */
    public function testTransform(string $pattern, array $transformations)
    {
        $transformer = new PatternTransformer($pattern);
        foreach ($transformations as $transformationType => $expectedPattern) {
            $this->assertEquals($expectedPattern, $transformer->transform($transformationType), 'Invalid transformation ' . $transformationType);
        }
    }

    public function getPatterns()
    {
        return [
            'fr' => [
                "#,##0.00\u{00A0}¤",
                [
                    PatternTransformer::TYPE_LEFT_SYMBOL_WITH_SPACE => "¤\u{00A0}#,##0.00",
                    PatternTransformer::TYPE_LEFT_SYMBOL_WITHOUT_SPACE => '¤#,##0.00',
                    PatternTransformer::TYPE_RIGHT_SYMBOL_WITH_SPACE => "#,##0.00\u{00A0}¤",
                    PatternTransformer::TYPE_RIGHT_SYMBOL_WITHOUT_SPACE => '#,##0.00¤',
                ],
            ],
            'rn' => [
                '#,##0.00¤',
                [
                    PatternTransformer::TYPE_LEFT_SYMBOL_WITH_SPACE => "¤\u{00A0}#,##0.00",
                    PatternTransformer::TYPE_LEFT_SYMBOL_WITHOUT_SPACE => '¤#,##0.00',
                    PatternTransformer::TYPE_RIGHT_SYMBOL_WITH_SPACE => "#,##0.00\u{00A0}¤",
                    PatternTransformer::TYPE_RIGHT_SYMBOL_WITHOUT_SPACE => '#,##0.00¤',
                ],
            ],
            'en' => [
                '¤#,##0.00',
                [
                    PatternTransformer::TYPE_LEFT_SYMBOL_WITH_SPACE => "¤\u{00A0}#,##0.00",
                    PatternTransformer::TYPE_LEFT_SYMBOL_WITHOUT_SPACE => '¤#,##0.00',
                    PatternTransformer::TYPE_RIGHT_SYMBOL_WITH_SPACE => "#,##0.00\u{00A0}¤",
                    PatternTransformer::TYPE_RIGHT_SYMBOL_WITHOUT_SPACE => '#,##0.00¤',
                ],
            ],
            'pt' => [
                "¤\u{00A0}#,##0.00",
                [
                    PatternTransformer::TYPE_LEFT_SYMBOL_WITH_SPACE => "¤\u{00A0}#,##0.00",
                    PatternTransformer::TYPE_LEFT_SYMBOL_WITHOUT_SPACE => '¤#,##0.00',
                    PatternTransformer::TYPE_RIGHT_SYMBOL_WITH_SPACE => "#,##0.00\u{00A0}¤",
                    PatternTransformer::TYPE_RIGHT_SYMBOL_WITHOUT_SPACE => '#,##0.00¤',
                ],
            ],
            'hi' => [
                '¤#,##,##0.00',
                [
                    PatternTransformer::TYPE_LEFT_SYMBOL_WITH_SPACE => "¤\u{00A0}#,##,##0.00",
                    PatternTransformer::TYPE_LEFT_SYMBOL_WITHOUT_SPACE => '¤#,##,##0.00',
                    PatternTransformer::TYPE_RIGHT_SYMBOL_WITH_SPACE => "#,##,##0.00\u{00A0}¤",
                    PatternTransformer::TYPE_RIGHT_SYMBOL_WITHOUT_SPACE => '#,##,##0.00¤',
                ],
            ],
            'sg' => [
                '¤#,##0.00;¤-#,##0.00',
                [
                    PatternTransformer::TYPE_LEFT_SYMBOL_WITH_SPACE => "¤\u{00A0}#,##0.00;¤\u{00A0}-#,##0.00",
                    PatternTransformer::TYPE_LEFT_SYMBOL_WITHOUT_SPACE => '¤#,##0.00;¤-#,##0.00',
                    PatternTransformer::TYPE_RIGHT_SYMBOL_WITH_SPACE => "#,##0.00\u{00A0}¤;-#,##0.00\u{00A0}¤",
                    PatternTransformer::TYPE_RIGHT_SYMBOL_WITHOUT_SPACE => '#,##0.00¤;-#,##0.00¤',
                ],
            ],
            'nl' => [
                "¤\u{00A0}#,##0.00;¤\u{00A0}-#,##0.00",
                [
                    PatternTransformer::TYPE_LEFT_SYMBOL_WITH_SPACE => "¤\u{00A0}#,##0.00;¤\u{00A0}-#,##0.00",
                    PatternTransformer::TYPE_LEFT_SYMBOL_WITHOUT_SPACE => '¤#,##0.00;¤-#,##0.00',
                    PatternTransformer::TYPE_RIGHT_SYMBOL_WITH_SPACE => "#,##0.00\u{00A0}¤;-#,##0.00\u{00A0}¤",
                    PatternTransformer::TYPE_RIGHT_SYMBOL_WITHOUT_SPACE => '#,##0.00¤;-#,##0.00¤',
                ],
            ],
            'he' => [
                // RTL pattern
                "\u{200F}¤\u{00A0}#,##0.00;\u{200F}¤\u{00A0}-#,##0.00",
                [
                    PatternTransformer::TYPE_LEFT_SYMBOL_WITH_SPACE => "\u{200F}¤\u{00A0}#,##0.00;\u{200F}¤\u{00A0}-#,##0.00",
                    PatternTransformer::TYPE_LEFT_SYMBOL_WITHOUT_SPACE => "\u{200F}¤#,##0.00;\u{200F}¤-#,##0.00",
                    PatternTransformer::TYPE_RIGHT_SYMBOL_WITH_SPACE => "\u{200F}#,##0.00\u{00A0}¤;\u{200F}-#,##0.00\u{00A0}¤",
                    PatternTransformer::TYPE_RIGHT_SYMBOL_WITHOUT_SPACE => "\u{200F}#,##0.00¤;\u{200F}-#,##0.00¤",
                ],
            ],
        ];
    }
}
