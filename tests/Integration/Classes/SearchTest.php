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

declare(strict_types=1);

namespace Tests\Integration\Classes;

use Configuration;
use PHPUnit\Framework\TestCase;
use Search;

class SearchTest extends Testcase
{
    /**
     * @dataProvider providerSearchString()
     */
    public function testSearchSanitizer(string $input, int $langId, array $expected): void
    {
        $result = Search::extractKeyWords($input, $langId);
        // array_values used to prevent issues with indexes keeped from array_unique
        $this->assertEquals($expected, array_values($result));
    }

    public function providerSearchString(): array
    {
        return [
            'simple' => [
                'input' => 'test',
                'langId' => 1,
                'expected' => ['test'],
            ],
            'with hyphen' => [
                'input' => 'test1-test2',
                'langId' => 1,
                'expected' => ['test1', 'test2', 'test1test2', 'test1-test2'],
            ],
            'with hyphen with double' => [
                'input' => 'test1-test-test',
                'langId' => 1,
                'expected' => ['test1', 'test', 'test1testtest', 'test1-test-test'],
            ],
            'with space' => [
                'input' => 'test1 test2',
                'langId' => 1,
                'expected' => ['test1', 'test2'],
            ],
            'with double space' => [
                'input' => 'test1  test2',
                'langId' => 1,
                'expected' => ['test1', 'test2'],
            ],
            'with space with double' => [
                'input' => 'test test',
                'langId' => 1,
                'expected' => ['test'],
            ],
            'with space before hyphen' => [
                'input' => 'test1 -test2',
                'langId' => 1,
                'expected' => ['test1', '-test2', 'test2'],
            ],
            'with double space before hyphen' => [
                'input' => 'test1  -test2',
                'langId' => 1,
                'expected' => ['test1', '-test2', 'test2'],
            ],
            'with multiple hyphens' => [
                'input' => 'test1--test2',
                'langId' => 1,
                'expected' => ['test1', '-test2', 'test1test2', 'test1--test2'],
            ],
            'with space separated hyphen' => [
                'input' => 'test1 - test2',
                'langId' => 1,
                'expected' => ['test1', '-', 'test2'],
            ],
            'with strange double hyphens' => [
                'input' => 'test1 -- test2',
                'langId' => 1,
                'expected' => ['test1', '-', 'test2', '--'],
            ],
            'with space after hyphen' => [
                'input' => 'test1- test2',
                'langId' => 1,
                'expected' => ['test1', 'test2', 'test1-'],
            ],
            'with multiple space separated hyphens' => [
                'input' => 'test1 - - test2',
                'langId' => 1,
                'expected' => ['test1', '-', 'test2'],
            ],
        ];
    }

    /**
     * @dataProvider providerGetSearchParamFromWord
     */
    public function testGetSearchParamFromWord(string $word, string $expectedKeyWord, bool $withStart, bool $withEnd): void
    {
        Configuration::set('PS_SEARCH_START', $withStart);
        Configuration::set('PS_SEARCH_END', !$withEnd); // Opposite of the meaning of start equivalent :)
        $actual = Search::getSearchParamFromWord($word);
        $this->assertEquals(
            $expectedKeyWord,
            $actual,
            'Search::getSearchParamFromWord() failed for data input : ' . $word . '; Expected : ' . $expectedKeyWord . '; Returns : ' . $actual
        );
    }

    public function providerGetSearchParamFromWord(): iterable
    {
        yield ['dress', 'dress%', false, true];
        yield ['dres', 'dres%', false, true];
        yield ['dress', '%dress%', true, true];
        yield ['dress', 'dress', false, false];
        yield ['dre%ss', 'dre\\\\%ss', false, false];
        yield ['dre%ss', '%dre\\\\%ss', true, false];
        yield ['-dress', 'dress%', false, true];
    }
}
