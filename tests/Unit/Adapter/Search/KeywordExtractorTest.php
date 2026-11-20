<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Tests\Unit\Adapter\Search;

use PHPUnit\Framework\TestCase;
use PrestaShop\PrestaShop\Adapter\Search\KeywordExtractor;

class KeywordExtractorTest extends TestCase
{
    /**
     * @dataProvider provideKeywords
     *
     * @param string[] $expected
     */
    public function testExtract(string $text, string $isoCode, int $minLength, int $maxLength, string $blacklist, array $expected): void
    {
        // Compare the complete ordered keyword list, including filtering and deduplication.
        $extractor = new KeywordExtractor($minLength, $maxLength, $blacklist);
        self::assertSame($expected, $extractor->extract($text, $isoCode));
    }

    /**
     * @return array<string, array>
     */
    public static function provideKeywords(): array
    {
        return [
            'empty text' => ['', 'en', 3, 30, '', []],
            'whitespace only' => [" \t\n ", 'en', 3, 30, '', []],
            'lowercase and accents' => ['ŽLUŤOUČKÝ KŮŇ', 'cs', 3, 30, '', ['zlutoucky', 'kun']],
            'duplicate words' => ['Test test TEST', 'en', 3, 30, '', ['test']],
            'whitespace boundaries' => ["talia\t120\nother", 'en', 3, 30, '', ['talia', '120', 'other']],
            'ordinary spaces never join' => ['Talia 120', 'en', 3, 30, '', ['talia', '120']],
            'hyphen' => ['Talia-120', 'en', 3, 30, '', ['talia', '120', 'talia120', 'talia-120']],
            'dot' => ['Talia.120', 'en', 3, 30, '', ['talia', '120', 'talia120', 'talia.120']],
            'underscore' => ['Talia_120', 'en', 3, 30, '', ['talia', '120', 'talia120', 'talia_120']],
            'all separators share parts and joined words' => ['Talia-120 Talia.120 Talia_120', 'en', 3, 30, '', ['talia', '120', 'talia120', 'talia-120', 'talia.120', 'talia_120']],
            'repeated original word' => ['Talia.120 Talia.120', 'en', 3, 30, '', ['talia', '120', 'talia120', 'talia.120']],
            'mixed separators' => ['Talia-Pro_120', 'en', 3, 30, '', ['talia', 'pro', '120', 'taliapro120', 'talia-pro_120']],
            'multiple dots' => ['Talia..120', 'en', 3, 30, '', ['talia', '120', 'talia120', 'talia..120']],
            'multiple hyphens' => ['Talia--120', 'en', 3, 30, '', ['talia', '120', 'talia120', 'talia--120']],
            'separate hyphen' => ['Talia - 120', 'en', 3, 30, '', ['talia', '120']],
            'leading hyphen' => ['Talia -120', 'en', 3, 30, '', ['talia', '120', '-120']],
            'trailing hyphen' => ['Talia- 120', 'en', 3, 30, '', ['talia', '120', 'talia-']],
            'hyphen between dotted parts' => ['Talia.120-Pro', 'en', 3, 30, '', ['talia', '120', 'pro', 'talia120pro', 'talia.120-pro']],
            'numeric hyphen' => ['123-456', 'en', 3, 30, '', ['123', '456', '123456', '123-456']],
            'numeric dot' => ['12.34', 'en', 1, 30, '', ['12', '34', '1234', '12.34']],
            'numeric underscore' => ['123_456', 'en', 3, 30, '', ['123', '456', '123456', '123_456']],
            'numeric comma also joins' => ['123,456', 'en', 3, 30, '', ['123456', '123,456']],
            'comma without space separates words' => ['černá,bílá', 'cs', 1, 30, '', ['cerna', 'bila', 'cerna,bila']],
            'dot without space separates words' => ['černá.bílá', 'cs', 1, 30, '', ['cerna', 'bila', 'cernabila', 'cerna.bila']],
            'trailing comma is removed' => ['Onyx, pravý', 'cs', 1, 30, '', ['onyx', 'pravy']],
            'commas prevent joining adjacent dotted words' => [
                'Talia.120,Centro.150', 'cs', 1, 30, '',
                ['talia', '120', 'centro', '150', 'talia120', 'centro150', 'talia.120,centro.150', 'talia.120', 'centro.150'],
            ],
            'numeric slash also joins' => ['123/456', 'en', 3, 30, '', ['123456', '123/456']],
            'slash in product code stays intact' => ['TL/7', 'cs', 1, 30, '', ['tl/7']],
            'leading slash stays intact' => ['/2', 'cs', 1, 30, '', ['/2']],
            'slash between color names' => ['černá/šedá', 'cs', 1, 30, '', ['cerna/seda']],
            'slash between dimensions with units' => ['40mm/50mm', 'cs', 1, 30, '', ['40mm/50mm']],
            'slash in airflow unit' => ['m3/h', 'cs', 1, 30, '', ['m3/h']],
            'fraction with inch mark' => ['6/4"', 'cs', 1, 30, '', ['64"', '6/4"']],
            'numeric slash before hyphen' => ['210/110-45', 'cs', 1, 30, '', ['210110', '45', '21011045', '210/110-45', '210110-45']],
            'numeric slash after hyphen' => ['611-78/39', 'cs', 1, 30, '', ['611', '7839', '6117839', '611-78/39', '611-7839']],
            'manufacturer code with leading zeroes' => ['134.0039.553', 'cs', 1, 30, '', ['134', '0039', '553', '1340039553', '134.0039.553']],
            'decimal comma in dimension' => ['4,4', 'cs', 1, 30, '', ['44', '4,4']],
            'numeric range with en dash' => ['403–494', 'cs', 1, 30, '', ['403', '494', '403494', '403-494']],
            'en dash in product name' => ['Talia–120', 'cs', 1, 30, '', ['talia', '120', 'talia120', 'talia-120']],
            'hyphen and en dash variants deduplicate' => ['Talia–120 Talia-120', 'cs', 1, 30, '', ['talia', '120', 'talia120', 'talia-120']],
            'hyphenated word ending a sentence' => ['K-LINK.', 'en', 1, 30, '', ['k', 'link', 'klink', 'k-link']],
            'hyphenated adjective ending a sentence' => ['High-end.', 'en', 1, 30, '', ['high', 'end', 'highend', 'high-end']],
            'hyphenated code ending a sentence' => ['D-150.', 'en', 1, 30, '', ['d', '150', 'd150', 'd-150']],
            'rotation angle with degree symbol' => ['110°', 'cs', 1, 30, '', ['110°']],
            'registered trademark transliteration' => ['Ceran®', 'cs', 1, 30, '', ['ceran(R)']],
            'encoded registered trademark transliteration' => ['Ceran&reg;', 'cs', 1, 30, '', ['ceran(R)']],
            'html specifications with slash and short units' => [
                '<li><b>Barva:</b> černá/šedá</li><li><b>Koše:</b> 2x 15 l</li>',
                'cs', 1, 30, '',
                ['barva:', 'cerna/seda', 'kose:', '2x', '15', 'l'],
            ],
            'product name with slash and hyphen' => [
                'Kuchyňský dřez Franke Centro CNG 611-78 TL/7 Onyx, pravý',
                'cs', 1, 30, '',
                ['kuchynsky', 'drez', 'franke', 'centro', 'cng', '611', '78', 'tl/7', 'onyx', 'pravy', '61178', '611-78'],
            ],
            'non-numeric punctuation inside words' => ['test t&t', 'en', 1, 30, '', ['test', 't&t']],
            'excluded symbols only' => ['&& ... ___', 'en', 1, 30, '', []],
            'zero word' => ['0', 'en', 1, 30, '', ['0']],
            'short parts do not suppress joined word' => ['AB_CD', 'en', 3, 30, '', ['abcd', 'ab_cd']],
            'short numeric parts' => ['12.34', 'en', 3, 30, '', ['1234', '12.34']],
            'minimum length' => ['a ab abc abcd', 'en', 3, 30, '', ['abc', 'abcd']],
            'maximum length' => ['abcdefgh', 'en', 1, 5, '', ['abcde']],
            'deduplicate after truncation' => ['abcdef abcdeg abcde', 'en', 1, 5, '', ['abcde']],
            'joined variant already present' => ['Talia.120 talia120', 'en', 3, 30, '', ['talia', '120', 'talia120', 'talia.120']],
            'blacklist exact words' => ['the theme other the', 'en', 1, 30, 'the|other', ['theme']],
            'blacklist is case insensitive' => ['THE product', 'en', 1, 30, 'THE', ['product']],
            'blacklist does not match generated joined words' => ['Talia.120', 'en', 3, 30, 'talia120', ['talia', '120', 'talia120', 'talia.120']],
            'blacklisted source word creates no variants' => ['Talia.120', 'en', 3, 30, 'talia[.]120', []],
            'blacklist does not match generated parts' => ['Talia.120', 'en', 3, 30, 'talia', ['talia', '120', 'talia120', 'talia.120']],
            'blacklist phrases' => ['red shirt blue', 'en', 1, 30, 'red shirt', ['blue']],
            'blacklist phrase cannot cross into generated variants' => ['foo-bar baz', 'en', 1, 30, 'baz foobar', ['foo', 'bar', 'baz', 'foobar', 'foo-bar']],
            'blacklist phrase cannot span joined variants' => ['foo-bar baz-qux', 'en', 1, 30, 'foobar bazqux', ['foo', 'bar', 'baz', 'qux', 'foobar', 'bazqux', 'foo-bar', 'baz-qux']],
            'blacklist phrase does not match generated parts' => ['red-shirt blue', 'en', 1, 30, 'shirt blue', ['red', 'shirt', 'blue', 'redshirt', 'red-shirt']],
            'blacklist does not filter variants a second time' => ['foo-bar baz', 'en', 1, 30, 'foobar', ['foo', 'bar', 'baz', 'foobar', 'foo-bar']],
            'excluded symbol produced by splitting' => ['A-®', 'en', 1, 30, '', ['a', 'a(R)', 'a-(R)']],
            'sentence punctuation is removed before expansion' => ['High-end.,', 'en', 1, 30, '', ['high', 'end', 'highend', 'high-end']],
            'numeric entity is lowercased after decoding' => ['&#201;vier', 'en', 1, 30, '', ['evier']],
            'uppercase named entity is decoded before lowercasing' => ['&Eacute;vier', 'en', 1, 30, '', ['evier']],
            'blacklist regular expressions' => ['shirt shirts shirtless', 'en', 1, 30, 'shirts?', ['shirtless']],
            'blacklist before transliteration' => ['červený kabát', 'cs', 1, 30, 'červený', ['kabat']],
            'list items stay separate' => ['<ul><li>red</li><li>shirt</li></ul>', 'en', 1, 30, '', ['red', 'shirt']],
            'paragraphs stay separate' => ['<p>hello</p><p>world</p>', 'en', 1, 30, '', ['hello', 'world']],
            'headings and sections stay separate' => ['<h2>red</h2><section>shirt</section>', 'en', 1, 30, '', ['red', 'shirt']],
            'table cells stay separate' => ['<table><tr><td>Talia</td><td>120</td></tr></table>', 'en', 1, 30, '', ['talia', '120']],
            'line breaks stay separate' => ['Talia<br />120<BR>other', 'en', 1, 30, '', ['talia', '120', 'other']],
            'inline fragments join' => ['<b>bio</b><span>logic</span>', 'en', 1, 30, '', ['biologic']],
            'inline separator' => ['<b>Talia</b>.<span>120</span>', 'en', 3, 30, '', ['talia', '120', 'talia120', 'talia.120']],
            'block boundaries prevent joined variants' => ['<p>Talia.</p><p>120</p>', 'en', 3, 30, '', ['talia', '120']],
            'html attributes do not become keywords' => ['<span title="hidden-word">visible</span>', 'en', 3, 30, '', ['visible']],
            'entities' => ['caf&eacute;&nbsp;table', 'en', 1, 30, '', ['cafe', 'table']],
            'encoded separator' => ['Talia&#46;120', 'en', 3, 30, '', ['talia', '120', 'talia120', 'talia.120']],
            'encoded markup remains text' => ['&lt;b&gt;word&lt;/b&gt;', 'en', 1, 30, '', ['<b>word</b>']],
            'japanese transliteration' => ['日本語', 'ja', 3, 30, '', ['ri', 'ben', 'yu']],
            'japanese kana' => ['カメラ', 'ja', 3, 30, '', ['kamera']],
            'chinese transliteration' => ['中文', 'zh', 3, 30, '', ['zhong', 'wen']],
            'taiwanese language code' => ['中文', 'tw', 3, 30, '', ['zhong', 'wen']],
            'japanese mixed token' => ['ABC日本', 'ja', 3, 30, '', ['abc', 'ri', 'ben']],
            'untransliterated symbols split into characters' => ['abc😀😃', 'ja', 3, 30, '', ['a', 'b', 'c', '😀', '😃']],
            'single-byte tokens precede symbols' => ['😀 camera 😃', 'ja', 3, 30, '', ['camera', '😀', '😃']],
            'asian minimum length exception' => ['a ab 日本語', 'ja', 5, 30, '', ['a', 'ab', 'ri', 'ben', 'yu']],
            'asian blacklist' => ['日本語 カメラ', 'ja', 3, 30, '日本語', ['kamera']],
            'asian maximum length' => ['カメラ', 'ja', 3, 3, '', ['kam']],
            'minimum length precedes transliteration' => ['中文', 'en', 3, 30, '', []],
            'unspecified language uses minimum length' => ['a ab camera', '', 3, 30, '', ['camera']],
        ];
    }

    public function testExtractorCanBeReusedAcrossLanguages(): void
    {
        // Language-specific symbol handling belongs to each extraction, not to cached state.
        $extractor = new KeywordExtractor(3, 30, '');
        self::assertSame(['a', 'ab'], $extractor->extract('a ab', 'ja'));
        self::assertSame([], $extractor->extract('a ab', 'en'));
        self::assertSame(['talia', '120', 'talia120', 'talia.120'], $extractor->extract('Talia.120', 'en'));
    }
}
