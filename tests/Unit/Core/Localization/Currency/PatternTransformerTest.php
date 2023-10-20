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

namespace Tests\Unit\Core\Localization\Currency;

use PHPUnit\Framework\TestCase;
use PrestaShop\PrestaShop\Core\Localization\Currency\PatternTransformer;

class PatternTransformerTest extends TestCase
{
    /**
     * @dataProvider getDataForTestTransform
     *
     * @param string $basePattern
     * @param array $transformations
     */
    public function testTransform(string $basePattern, array $transformations)
    {
        $transformer = new PatternTransformer();

        foreach ($transformations as $transformationType => $expectedPattern) {
            $this->assertEquals($expectedPattern, $transformer->transform($basePattern, $transformationType), 'Invalid transformation ' . $transformationType);
        }
    }

    /**
     * @return array[]
     */
    public function getDataForTestTransform(): array
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

    /**
     * @param string $expectedTransformationType
     * @param array $patterns
     *
     * @dataProvider getDataForTestGetTransformationType
     */
    public function testGetTransformationType(string $expectedTransformationType, array $patterns)
    {
        $transformer = new PatternTransformer();

        foreach ($patterns as $pattern) {
            $transformationType = $transformer->getTransformationType($pattern);
            $this->assertEquals($expectedTransformationType, $transformationType, 'Invalid pattern match ' . $pattern);
        }
    }

    /**
     * @return array[]
     */
    public function getDataForTestGetTransformationType()
    {
        return [
            PatternTransformer::TYPE_LEFT_SYMBOL_WITH_SPACE => [
                PatternTransformer::TYPE_LEFT_SYMBOL_WITH_SPACE,
                [
                    "¤\u{00A0}#,##0.00",
                    "¤\u{00A0}#,##,##0.00",
                    "¤\u{00A0}#,##0.00;¤\u{00A0}-#,##0.00",
                    "\u{200F}¤\u{00A0}#,##0.00;\u{200F}¤\u{00A0}-#,##0.00",
                ],
            ],
            PatternTransformer::TYPE_LEFT_SYMBOL_WITHOUT_SPACE => [
                PatternTransformer::TYPE_LEFT_SYMBOL_WITHOUT_SPACE,
                [
                    '¤#,##0.00',
                    '¤#,##,##0.00',
                    '¤#,##0.00;¤-#,##0.00',
                    "\u{200F}¤#,##0.00;\u{200F}¤-#,##0.00",
                ],
            ],
            PatternTransformer::TYPE_RIGHT_SYMBOL_WITH_SPACE => [
                PatternTransformer::TYPE_RIGHT_SYMBOL_WITH_SPACE,
                [
                    "#,##0.00\u{a0}¤",
                    "#,##,##0.00\u{00A0}¤",
                    "#,##0.00\u{00A0}¤;-#,##0.00\u{00A0}¤",
                    "\u{200F}#,##0.00\u{00A0}¤;\u{200F}-#,##0.00\u{00A0}¤",
                ],
            ],
            PatternTransformer::TYPE_RIGHT_SYMBOL_WITHOUT_SPACE => [
                PatternTransformer::TYPE_RIGHT_SYMBOL_WITHOUT_SPACE,
                [
                    '#,##0.00¤',
                    '#,##,##0.00¤',
                    '#,##0.00¤;-#,##0.00¤',
                    "\u{200F}#,##0.00¤;\u{200F}-#,##0.00¤",
                ],
            ],
        ];
    }
}
