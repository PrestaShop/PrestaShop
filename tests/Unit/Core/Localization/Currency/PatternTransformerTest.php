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

    /**
     * CLDR marks the direction of a currency pattern with U+200F for locales such as Hebrew and with
     * U+200E for Persian. Only the first used to be recognised, so the Persian pattern was reported
     * as having no transformation type at all and the back office could not tell where its symbol sits.
     *
     * @dataProvider getDirectionalPatterns
     */
    public function testADirectionalMarkDoesNotHideTheTransformationType(string $expectedType, string $pattern): void
    {
        $transformer = new PatternTransformer();

        $this->assertSame($expectedType, $transformer->getTransformationType($pattern));
    }

    public function getDirectionalPatterns(): array
    {
        return [
            'persian, left to right mark' => [
                PatternTransformer::TYPE_LEFT_SYMBOL_WITHOUT_SPACE,
                "\u{200E}\u{00A4}#,##0.00",
            ],
            'hebrew, right to left mark' => [
                PatternTransformer::TYPE_RIGHT_SYMBOL_WITH_SPACE,
                "\u{200F}#,##0.00\u{00A0}\u{00A4}",
            ],
            'no mark at all' => [
                PatternTransformer::TYPE_LEFT_SYMBOL_WITHOUT_SPACE,
                "\u{00A4}#,##0.00",
            ],
        ];
    }

    /**
     * A transformed pattern has to keep the directional mark it came with.
     */
    public function testTheLeftToRightMarkSurvivesATransformation(): void
    {
        $transformer = new PatternTransformer();

        $this->assertSame(
            "\u{200E}#,##0.00\u{00A0}\u{00A4}",
            $transformer->transform("\u{200E}\u{00A4}#,##0.00", PatternTransformer::TYPE_RIGHT_SYMBOL_WITH_SPACE)
        );
    }
}
