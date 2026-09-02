<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Tests\Integration\Classes;

use Language;
use PHPUnit\Framework\TestCase;
use Validate;

/**
 * Validate::isSearchableName() delegates to Search::extractKeyWords(), which reads the
 * search blacklist from Configuration and therefore needs a booted shop. This is why the
 * coverage lives under tests/Integration/ rather than tests/Unit/.
 *
 * The truth table below pins the two premise-breaking examples raised in the review of
 * PR #41664 (`----` is indexable, `a+` is not) alongside the obvious cases.
 */
class ValidateSearchableNameTest extends TestCase
{
    private const DEFAULT_LANG_ID = 1;

    /**
     * @dataProvider provideSearchableNames
     */
    public function testItAcceptsNamesTheIndexerKeeps(string $name): void
    {
        $this->assertTrue(
            Validate::isSearchableName($name, self::DEFAULT_LANG_ID, $this->isoCode()),
            sprintf('Expected "%s" to be searchable', $name)
        );
    }

    /**
     * @dataProvider provideNonSearchableNames
     */
    public function testItRejectsNamesTheIndexerStripsToNothing(string $name): void
    {
        $this->assertFalse(
            Validate::isSearchableName($name, self::DEFAULT_LANG_ID, $this->isoCode()),
            sprintf('Expected "%s" to be rejected as non-searchable', $name)
        );
    }

    public function provideSearchableNames(): array
    {
        return [
            'plain word'                          => ['summer'],
            'digits only'                         => ['2024'],
            'letters with leading special chars'  => ['+++promo'],
            'accented letters'                    => ['été'],
            // Hyphen-only terms are kept: PREG_CLASS_SEARCH_EXCLUDE only strips a term made
            // entirely of excluded characters when the whole-term check matches, and the
            // hyphen-preserving pass in extractKeyWords() adds "----" back. Verified on 9.x.
            'dashes only are indexable'           => ['----'],
            'hyphenated word'                     => ['t-shirt'],
            // '/' is not in PREG_CLASS_SEARCH_EXCLUDE, so this term survives sanitization -
            // another case a "contains letter/digit" rule would have over-rejected.
            'slash keeps the term alive'          => ['+-*/'],
        ];
    }

    public function provideNonSearchableNames(): array
    {
        return [
            'plus signs'                => ['++++'],
            'asterisks'                 => ['***'],
            'hashes'                    => ['###'],
            'single plus'               => ['+'],
            'whitespace only'           => ['   '],
            'empty string'              => [''],
            // Contains a letter but indexation yields no keyword (verified on 9.x). This
            // is why a character-class rule like "contains any letter or digit" was not
            // enough - it would under-reject "a+".
            'letter followed by excluded char' => ['a+'],
            // Single-char tokens are filtered by the indexer under the default English lang.
            'single letter'             => ['a'],
            // CJK is only tokenised for iso zh/tw/ja; with iso "en" it drops.
            'cjk letters under non-cjk lang' => ['中文'],
        ];
    }

    private function isoCode(): string
    {
        return (string) Language::getIsoById(self::DEFAULT_LANG_ID);
    }
}
