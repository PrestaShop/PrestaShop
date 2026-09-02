<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace Tests\Unit\Core\Domain\Country;

use PHPUnit\Framework\TestCase;
use PrestaShop\PrestaShop\Core\Domain\Country\ZipCodePatternResolver;

class ZipCodePatternResolverTest extends TestCase
{
    /**
     * @var ZipCodePatternResolver
     */
    protected $resolver;

    protected function setUp(): void
    {
        $this->resolver = new ZipCodePatternResolver();
    }

    /**
     * @dataProvider regexpPatternDataProvider
     *
     * @param string $format
     * @param string $isoCode
     * @param string $expectedResult
     */
    public function testGetRegexpPattern(string $format, string $isoCode, string $expectedResult)
    {
        $result = $this->resolver->getRegexPattern($format, $isoCode);

        $this->assertEquals($expectedResult, $result);
    }

    /**
     * @dataProvider humanReadablePatternDataProvider
     *
     * @param string $format
     * @param string $isoCode
     * @param string $expectedResult
     */
    public function testGetHumanReadablePattern(string $format, string $isoCode, string $expectedResult)
    {
        $result = $this->resolver->getHumanReadablePattern($format, $isoCode);

        $this->assertEquals($expectedResult, $result);
    }

    /**
     * @return array
     */
    public function regexpPatternDataProvider(): array
    {
        $expectedResults = [
            '/^[0-9][0-9][0-9][0-9][0-9]$/ui',
            '/^[0-9][0-9][0-9][0-9] [a-zA-Z][a-zA-Z]$/ui',
            '/^[0-9][0-9][0-9][0-9]-[0-9][0-9][0-9]$/ui',
            '/^[a-zA-Z][0-9][a-zA-Z] [0-9][a-zA-Z][0-9]$/ui',
            '/^ISO[0-9][0-9][0-9]$/ui',
            '/^[a-zA-Z][a-zA-Z][a-zA-Z][a-zA-Z] [0-9][a-zA-Z][a-zA-Z]$/ui',
            '/^[0-9][0-9][0-9][0-9][0-9]-[0-9][0-9][0-9][0-9][0-9]$/ui',
            '/^980[0-9][0-9]$/ui',
            '/^NNNN$/ui',
            '/^98NNNN89$/ui',
            '/^7384687583671238947$/ui',
            '/^adfsjskdjf$/ui',
            '/^]]]]]]]]]$/ui',
            '/^zZzZzZzZ$/ui',
            '/^1234567890poiuytrewq[0-9]n1234567890$/ui',
        ];

        return array_map(function ($a, $b) {
            array_push($a, $b);

            return $a;
        }, $this->getPatterns(), $expectedResults);
    }

    /**
     * @return array
     */
    public function humanReadablePatternDataProvider(): array
    {
        $expectedResults = [
            '00000',
            '0000 AA',
            '0000-000',
            'A0A 0A0',
            'ISO000',
            'AAAA 0AA',
            '00000-00000',
            '98000',
            'NNNN',
            '98NNNN89',
            '7384687583671238947',
            'adfsjskdjf',
            ']]]]]]]]]',
            'zZzZzZzZ',
            '1234567890poiuytrewq0n1234567890',
        ];

        return array_map(function ($a, $b) {
            array_push($a, $b);

            return $a;
        }, $this->getPatterns(), $expectedResults);
    }

    /**
     * @return array
     */
    public function getPatterns(): array
    {
        return [
            ['NNNNN', ''],
            ['NNNN LL', ''],
            ['NNNN-NNN', ''],
            ['LNL NLN', ''],
            ['CNNN', 'ISO'],
            ['LLLL NLL', ''],
            ['NNNNN-NNNNN', ''],
            ['980NN', 'CC'],
            ['CC', 'NN'],
            ['98CC89', 'NN'],
            ['7384687583671238947', ''],
            ['adfsjskdjf', ''],
            [']]]]]]]]]', ''],
            ['zZzZzZzZ', ''],
            ['1234567890poiuytrewqNn1234567890', ''],
        ];
    }
}
