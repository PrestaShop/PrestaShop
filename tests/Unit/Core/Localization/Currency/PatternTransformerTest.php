<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
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
