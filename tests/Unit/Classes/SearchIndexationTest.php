<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Tests\Unit\Classes;

use Configuration;
use PHPUnit\Framework\Attributes\BackupStaticProperties;
use PHPUnit\Framework\TestCase;
use ReflectionMethod;
use ReflectionProperty;
use Search;

#[BackupStaticProperties(true)]
class SearchIndexationTest extends TestCase
{
    protected function setUp(): void
    {
        // Seed the configuration cache directly so these tests cannot load shop data.
        Configuration::resetStaticCache();
        $initialized = new ReflectionProperty(Configuration::class, '_initialized');
        $initialized->setAccessible(true);
        $initialized->setValue(null, true);
        $types = new ReflectionProperty(Configuration::class, 'types');
        $types->setAccessible(true);
        $types->setValue(null, ['PS_SEARCH_BLACKLIST' => true]);
    }

    public function testIndexationUsesCurrentConfiguration(): void
    {
        // Configure index limits and a language-specific blacklist without a database.
        Configuration::set('PS_SEARCH_FUZZY', true, 0, 0);
        Configuration::set('PS_SEARCH_MINWORDLEN', 3, 0, 0);
        Configuration::set('PS_SEARCH_MAX_WORD_LENGTH', 30, 0, 0);
        Configuration::set('PS_SEARCH_BLACKLIST', [1 => '', 2 => 'talia[.]120'], 0, 0);

        // Both index entry points use the extractor and the selected language's blacklist.
        self::assertSame(['talia', '120', 'talia120', 'talia.120'], Search::getKeywordsToIndex('Talia.120', 1, 'en'));
        self::assertSame([], Search::extractKeyWords('Talia.120', 2, true, 'en'));

        // Cached extractors must not ignore configuration changes in the same process.
        Configuration::set('PS_SEARCH_MAX_WORD_LENGTH', 5, 0, 0);
        self::assertSame(['talia', '120'], Search::getKeywordsToIndex('Talia.120', 1, 'en'));
        Configuration::set('PS_SEARCH_MINWORDLEN', 4, 0, 0);
        self::assertSame(['talia'], Search::getKeywordsToIndex('Talia.120', 1, 'en'));
    }

    public function testFieldWeightIsAddedOncePerTruncatedKeyword(): void
    {
        // Words that truncate to the same index key contribute only one field weight.
        Configuration::set('PS_SEARCH_FUZZY', true, 0, 0);
        Configuration::set('PS_SEARCH_MINWORDLEN', 3, 0, 0);
        Configuration::set('PS_SEARCH_MAX_WORD_LENGTH', 5, 0, 0);
        Configuration::set('PS_SEARCH_BLACKLIST', [1 => ''], 0, 0);
        $products = [];
        $weights = ['pname' => 6, 'description' => 1];
        $fillProductArray = new ReflectionMethod(Search::class, 'fillProductArray');
        $fillProductArray->setAccessible(true);
        $fillProductArray->invokeArgs(null, [&$products, &$weights, 'pname', 'abcdef abcdeg abcde', 1, 'en']);
        self::assertSame(['abcde' => 6], $products);

        // A separate field contributes its own configured weight to the same keyword.
        $fillProductArray->invokeArgs(null, [&$products, &$weights, 'description', 'abcdef', 1, 'en']);
        self::assertSame(['abcde' => 7], $products);
    }

    public function testDisabledFuzzySearchUsesDefaultMaximumLength(): void
    {
        // The configured fuzzy word-length limit does not limit a non-fuzzy search index.
        Configuration::set('PS_SEARCH_FUZZY', false, 0, 0);
        Configuration::set('PS_SEARCH_MINWORDLEN', 3, 0, 0);
        Configuration::set('PS_SEARCH_MAX_WORD_LENGTH', 5, 0, 0);
        Configuration::set('PS_SEARCH_BLACKLIST', [1 => ''], 0, 0);
        self::assertSame(['talia', '120', 'talia120', 'talia-120'], Search::extractKeyWords('Talia-120', 1, true, 'en'));
        self::assertSame([str_repeat('a', 30)], Search::getKeywordsToIndex(str_repeat('a', 35), 1, 'en'));
    }
}
