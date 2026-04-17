<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Tests\Integration\Classes;

use Configuration;
use PHPUnit\Framework\TestCase;
use Search;

class SearchTest extends TestCase
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
            'with special characters' => [
                'input' => '&&',
                'langId' => 1,
                'expected' => [''],
            ],
            'with special characters (allowed)' => [
                'input' => 'test t&t',
                'langId' => 1,
                'expected' => ['test', 't&t'],
            ],
            'with hyphen' => [
                'input' => 'test1-test2',
                'langId' => 1,
                'expected' => ['test1', 'test2', 'test1-test2', 'test1test2'],
            ],
            'with hyphen with double' => [
                'input' => 'test1-test-test',
                'langId' => 1,
                'expected' => ['test1', 'test', 'test1-test-test', 'test1testtest'],
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
                'expected' => ['test1', '-test2', 'test1--test2', 'test1test2'],
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
