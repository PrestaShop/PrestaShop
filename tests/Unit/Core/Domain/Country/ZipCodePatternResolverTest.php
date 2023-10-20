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
