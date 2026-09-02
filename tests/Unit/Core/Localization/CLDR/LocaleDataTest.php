<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace Tests\Unit\Core\Localization\CLDR;

use PHPUnit\Framework\TestCase;
use PrestaShop\PrestaShop\Core\Localization\CLDR\LocaleData;

class LocaleDataTest extends TestCase
{
    /**
     * @var LocaleData
     */
    private $localeData;

    /**
     * Setup tested dependency
     */
    public function setUp(): void
    {
        $this->localeData = new LocaleData();
    }

    /**
     * @param array|null $decimalPatternsInitial
     * @param array|null $decimalPatternsOverride
     * @param array|null $decimalPatternsExpected
     *
     * @dataProvider providerDecimalPatterns
     */
    public function testOverrideWithDecimalPatterns(
        ?array $decimalPatternsInitial,
        ?array $decimalPatternsOverride,
        ?array $decimalPatternsExpected
    ) {
        // Initial
        $this->localeData->setDecimalPatterns($decimalPatternsInitial);

        // Override
        $localeData = new LocaleData();
        if (null !== $decimalPatternsOverride) {
            $localeData->setDecimalPatterns($decimalPatternsOverride);
        }

        // Result
        $result = $this->localeData->overrideWith($localeData);
        $this->assertInstanceOf(LocaleData::class, $result);
        $this->assertEquals($decimalPatternsExpected, $result->getDecimalPatterns());
    }

    public function providerDecimalPatterns(): array
    {
        return [
            [
                null,
                null,
                null,
            ],
            [
                null,
                ['latn' => 'A'],
                ['latn' => 'A'],
            ],
            [
                ['latn' => 'A'],
                ['latn' => 'B'],
                ['latn' => 'B'],
            ],
            [
                ['arab' => 'A'],
                ['latn' => 'B'],
                ['arab' => 'A', 'latn' => 'B'],
            ],
            [
                ['arab' => 'A', 'latn' => 'B'],
                ['latn' => 'C'],
                ['arab' => 'A', 'latn' => 'C'],
            ],
        ];
    }

    /**
     * @param array|null $decimalPatternsInitial
     * @param array|null $decimalPatternsOverride
     * @param array|null $decimalPatternsExpected
     *
     * @dataProvider providerPercentPatterns
     */
    public function testOverrideWithPercentPatterns(
        ?array $decimalPatternsInitial,
        ?array $decimalPatternsOverride,
        ?array $decimalPatternsExpected
    ) {
        // Initial
        $this->localeData->setDecimalPatterns($decimalPatternsInitial);

        // Override
        $localeData = new LocaleData();
        if (null !== $decimalPatternsOverride) {
            $localeData->setDecimalPatterns($decimalPatternsOverride);
        }

        // Result
        $result = $this->localeData->overrideWith($localeData);
        $this->assertInstanceOf(LocaleData::class, $result);
        $this->assertEquals($decimalPatternsExpected, $result->getDecimalPatterns());
    }

    public function providerPercentPatterns(): array
    {
        return [
            [
                null,
                null,
                null,
            ],
            [
                null,
                ['latn' => 'A %'],
                ['latn' => 'A %'],
            ],
            [
                ['latn' => 'A %'],
                ['latn' => 'B %'],
                ['latn' => 'B %'],
            ],
            [
                ['arab' => 'A %'],
                ['latn' => 'B %'],
                ['arab' => 'A %', 'latn' => 'B %'],
            ],
            [
                ['arab' => 'A %', 'latn' => 'B %'],
                ['latn' => 'C %'],
                ['arab' => 'A %', 'latn' => 'C %'],
            ],
        ];
    }
}
