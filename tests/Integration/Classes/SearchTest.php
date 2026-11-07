<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Tests\Integration\Classes;

use Configuration;
use Context;
use Db;
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
            // Block-level tags separate words so list items do not merge (#40244 search index pollution).
            'with html list items' => [
                'input' => '<ul><li>red</li><li>shirt</li></ul>',
                'langId' => 1,
                'expected' => ['red', 'shirt'],
            ],
            'with adjacent block paragraphs' => [
                'input' => '<p>hello</p><p>world</p>',
                'langId' => 1,
                'expected' => ['hello', 'world'],
            ],
            // Inline tags are stripped without a separator so a word styled mid-way stays whole.
            'with mixed inline styling inside a word' => [
                'input' => '<b>bio</b><span>logic</span>',
                'langId' => 1,
                'expected' => ['biologic'],
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

    /**
     * @dataProvider providerFindRelevance
     */
    public function testFindRelevanceIsTheSumOfMatchingWordWeights(string $expression, array $likePatterns): void
    {
        Configuration::set('PS_SEARCH_START', true);
        Configuration::set('PS_SEARCH_END', false);
        $context = Context::getContext();

        $found = Search::find(1, $expression, 1, 1000, 'position', 'desc', false, false, $context);
        $this->assertNotEmpty($found['result']);

        $expectedPositions = $this->getExpectedPositions($likePatterns, (int) $context->shop->id);
        $previous = PHP_INT_MAX;
        foreach ($found['result'] as $product) {
            $this->assertSame($expectedPositions[(int) $product['id_product']], (int) $product['position']);
            $this->assertLessThanOrEqual($previous, (int) $product['position']);
            $previous = (int) $product['position'];
        }

        $ajaxResult = Search::find(1, $expression, 1, 10, 'position', 'desc', true, false, $context);
        $this->assertSame(
            array_slice(array_map('intval', array_column($found['result'], 'position')), 0, 10),
            array_map('intval', array_column($ajaxResult, 'position'))
        );
    }

    public function providerFindRelevance(): iterable
    {
        yield 'one word' => ['mug', ['%mug%']];
        yield 'two words' => ['mug ceramic', ['%mug%', '%ceramic%']];
        yield 'two words matching the same indexed word' => ['dishwasher proof', ['%dishwasher%', '%proof%']];
        yield 'two expressions' => ['mug;poster', ['%mug%', '%poster%']];
    }

    private function getExpectedPositions(array $likePatterns, int $shopId): array
    {
        $conditions = array_map(fn (string $pattern) => "sw.word LIKE '" . pSQL($pattern) . "'", $likePatterns);
        $rows = Db::getInstance()->executeS(
            'SELECT si.id_product, SUM(si.weight) position FROM ' . _DB_PREFIX_ . 'search_word sw '
            . 'INNER JOIN ' . _DB_PREFIX_ . 'search_index si ON si.id_word = sw.id_word '
            . 'WHERE sw.id_lang = 1 AND sw.id_shop = ' . $shopId . ' AND (' . implode(' OR ', $conditions) . ') '
            . 'GROUP BY si.id_product'
        );

        return array_combine(array_map('intval', array_column($rows, 'id_product')), array_map('intval', array_column($rows, 'position')));
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
