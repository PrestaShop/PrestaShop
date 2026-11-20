<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Adapter\Search;

use PrestaShop\PrestaShop\Core\Util\String\StringModifier;

/**
 * Extracts keywords for product indexation using the supplied language and search settings.
 */
class KeywordExtractor
{
    /*
     * List of punctuation and other characters to exclude from search keywords.
     * They can be a part of words, but cannot appear as standalone keywords.
     */
    public const SEARCH_EXCLUDE =
        '\x{0}-\x{2c}\x{2e}-\x{2f}\x{3a}-\x{40}\x{5b}-\x{60}\x{7b}-\x{bf}\x{d7}\x{f7}\x{2b0}-' .
        '\x{385}\x{387}\x{3f6}\x{482}-\x{489}\x{559}-\x{55f}\x{589}-\x{5c7}\x{5f3}-' .
        '\x{61f}\x{640}\x{64b}-\x{65e}\x{66a}-\x{66d}\x{670}\x{6d4}\x{6d6}-\x{6ed}' .
        '\x{6fd}\x{6fe}\x{700}-\x{70f}\x{711}\x{730}-\x{74a}\x{7a6}-\x{7b0}\x{901}-' .
        '\x{903}\x{93c}\x{93e}-\x{94d}\x{951}-\x{954}\x{962}-\x{965}\x{970}\x{981}-' .
        '\x{983}\x{9bc}\x{9be}-\x{9cd}\x{9d7}\x{9e2}\x{9e3}\x{9f2}-\x{a03}\x{a3c}-' .
        '\x{a4d}\x{a70}\x{a71}\x{a81}-\x{a83}\x{abc}\x{abe}-\x{acd}\x{ae2}\x{ae3}' .
        '\x{af1}-\x{b03}\x{b3c}\x{b3e}-\x{b57}\x{b70}\x{b82}\x{bbe}-\x{bd7}\x{bf0}-' .
        '\x{c03}\x{c3e}-\x{c56}\x{c82}\x{c83}\x{cbc}\x{cbe}-\x{cd6}\x{d02}\x{d03}' .
        '\x{d3e}-\x{d57}\x{d82}\x{d83}\x{dca}-\x{df4}\x{e31}\x{e34}-\x{e3f}\x{e46}-' .
        '\x{e4f}\x{e5a}\x{e5b}\x{eb1}\x{eb4}-\x{ebc}\x{ec6}-\x{ecd}\x{f01}-\x{f1f}' .
        '\x{f2a}-\x{f3f}\x{f71}-\x{f87}\x{f90}-\x{fd1}\x{102c}-\x{1039}\x{104a}-' .
        '\x{104f}\x{1056}-\x{1059}\x{10fb}\x{10fc}\x{135f}-\x{137c}\x{1390}-\x{1399}' .
        '\x{166d}\x{166e}\x{1680}\x{169b}\x{169c}\x{16eb}-\x{16f0}\x{1712}-\x{1714}' .
        '\x{1732}-\x{1736}\x{1752}\x{1753}\x{1772}\x{1773}\x{17b4}-\x{17db}\x{17dd}' .
        '\x{17f0}-\x{180e}\x{1843}\x{18a9}\x{1920}-\x{1945}\x{19b0}-\x{19c0}\x{19c8}' .
        '\x{19c9}\x{19de}-\x{19ff}\x{1a17}-\x{1a1f}\x{1d2c}-\x{1d61}\x{1d78}\x{1d9b}-' .
        '\x{1dc3}\x{1fbd}\x{1fbf}-\x{1fc1}\x{1fcd}-\x{1fcf}\x{1fdd}-\x{1fdf}\x{1fed}-' .
        '\x{1fef}\x{1ffd}-\x{2070}\x{2074}-\x{207e}\x{2080}-\x{2101}\x{2103}-\x{2106}' .
        '\x{2108}\x{2109}\x{2114}\x{2116}-\x{2118}\x{211e}-\x{2123}\x{2125}\x{2127}' .
        '\x{2129}\x{212e}\x{2132}\x{213a}\x{213b}\x{2140}-\x{2144}\x{214a}-\x{2b13}' .
        '\x{2ce5}-\x{2cff}\x{2d6f}\x{2e00}-\x{3005}\x{3007}-\x{303b}\x{303d}-\x{303f}' .
        '\x{3099}-\x{309e}\x{30a0}\x{30fb}\x{30fd}\x{30fe}\x{3190}-\x{319f}\x{31c0}-' .
        '\x{31cf}\x{3200}-\x{33ff}\x{4dc0}-\x{4dff}\x{a015}\x{a490}-\x{a716}\x{a802}' .
        '\x{e000}-\x{f8ff}\x{fb29}\x{fd3e}-\x{fd3f}\x{fdfc}-\x{fdfd}' .
        '\x{fd3f}\x{fdfc}-\x{fe6b}\x{feff}-\x{ff0f}\x{ff1a}-\x{ff20}\x{ff3b}-\x{ff40}' .
        '\x{ff5b}-\x{ff65}\x{ff70}\x{ff9e}\x{ff9f}\x{ffe0}-\x{fffd}';

    // Unicode digits and numeric symbols from different writing systems.
    // Used to recognize numbers on both sides of punctuation.
    public const NUMBERS =
        '\x{30}-\x{39}\x{b2}\x{b3}\x{b9}\x{bc}-\x{be}\x{660}-\x{669}\x{6f0}-\x{6f9}' .
        '\x{966}-\x{96f}\x{9e6}-\x{9ef}\x{9f4}-\x{9f9}\x{a66}-\x{a6f}\x{ae6}-\x{aef}' .
        '\x{b66}-\x{b6f}\x{be7}-\x{bf2}\x{c66}-\x{c6f}\x{ce6}-\x{cef}\x{d66}-\x{d6f}' .
        '\x{e50}-\x{e59}\x{ed0}-\x{ed9}\x{f20}-\x{f33}\x{1040}-\x{1049}\x{1369}-' .
        '\x{137c}\x{16ee}-\x{16f0}\x{17e0}-\x{17e9}\x{17f0}-\x{17f9}\x{1810}-\x{1819}' .
        '\x{1946}-\x{194f}\x{2070}\x{2074}-\x{2079}\x{2080}-\x{2089}\x{2153}-\x{2183}' .
        '\x{2460}-\x{249b}\x{24ea}-\x{24ff}\x{2776}-\x{2793}\x{3007}\x{3021}-\x{3029}' .
        '\x{3038}-\x{303a}\x{3192}-\x{3195}\x{3220}-\x{3229}\x{3251}-\x{325f}\x{3280}-' .
        '\x{3289}\x{32b1}-\x{32bf}\x{ff10}-\x{ff19}';

    // Unicode punctuation characters that can appear between digits, for example in 123/456 or 4,4.
    // We remove them to create joined variants, while also indexing the original expression.
    // Hyphens, en dashes, dots, and underscores are handled separately to generate individual parts as well.
    public const PUNCTUATION =
        '\x{21}-\x{23}\x{25}-\x{2a}\x{2c}-\x{2f}\x{3a}\x{3b}\x{3f}\x{40}\x{5b}-\x{5d}' .
        '\x{5f}\x{7b}\x{7d}\x{a1}\x{ab}\x{b7}\x{bb}\x{bf}\x{37e}\x{387}\x{55a}-\x{55f}' .
        '\x{589}\x{58a}\x{5be}\x{5c0}\x{5c3}\x{5f3}\x{5f4}\x{60c}\x{60d}\x{61b}\x{61f}' .
        '\x{66a}-\x{66d}\x{6d4}\x{700}-\x{70d}\x{964}\x{965}\x{970}\x{df4}\x{e4f}' .
        '\x{e5a}\x{e5b}\x{f04}-\x{f12}\x{f3a}-\x{f3d}\x{f85}\x{104a}-\x{104f}\x{10fb}' .
        '\x{1361}-\x{1368}\x{166d}\x{166e}\x{169b}\x{169c}\x{16eb}-\x{16ed}\x{1735}' .
        '\x{1736}\x{17d4}-\x{17d6}\x{17d8}-\x{17da}\x{1800}-\x{180a}\x{1944}\x{1945}' .
        '\x{2010}-\x{2027}\x{2030}-\x{2043}\x{2045}-\x{2051}\x{2053}\x{2054}\x{2057}' .
        '\x{207d}\x{207e}\x{208d}\x{208e}\x{2329}\x{232a}\x{23b4}-\x{23b6}\x{2768}-' .
        '\x{2775}\x{27e6}-\x{27eb}\x{2983}-\x{2998}\x{29d8}-\x{29db}\x{29fc}\x{29fd}' .
        '\x{3001}-\x{3003}\x{3008}-\x{3011}\x{3014}-\x{301f}\x{3030}\x{303d}\x{30a0}' .
        '\x{30fb}\x{fd3e}\x{fd3f}\x{fe30}-\x{fe52}\x{fe54}-\x{fe61}\x{fe63}\x{fe68}' .
        '\x{fe6a}\x{fe6b}\x{ff01}-\x{ff03}\x{ff05}-\x{ff0a}\x{ff0c}-\x{ff0f}\x{ff1a}' .
        '\x{ff1b}\x{ff1f}\x{ff20}\x{ff3b}-\x{ff3d}\x{ff3f}\x{ff5b}\x{ff5d}\x{ff5f}-' .
        '\x{ff65}';

    // Tags considered block-level, so words separated by them do not join.
    // Inline tags allow styled parts of the same word to join.
    public const HTML_BLOCK_TAGS_REGEX = '#</?(?:address|article|aside|blockquote|br|dd|div|dl|dt|fieldset|figcaption|figure|footer|form|h[1-6]|header|hr|li|main|nav|ol|p|pre|section|table|tbody|td|tfoot|th|thead|tr|ul)\b[^>]*>#i';

    private StringModifier $stringModifier;

    /**
     * @param int $minWordLength Minimum word length before transliteration, ignored for Chinese and Japanese
     * @param int $maxWordLength Maximum keyword length after transliteration; longer keywords are truncated
     * @param string $blacklist Pipe-separated blacklist expressions for the indexed language
     */
    public function __construct(private readonly int $minWordLength, private readonly int $maxWordLength, private readonly string $blacklist)
    {
        $this->stringModifier = new StringModifier();
    }

    /**
     * Take a string (HTML or plain text) and extract as many indexable keywords as possible.
     * The ISO code is needed to apply special rules to Asian languages.
     *
     * @return string[] Unique keywords, in extraction order
     */
    public function extract(string $text, string $isoCode): array
    {
        // First, we normalize the text by converting HTML to plain text and removing unwanted characters
        $text = $this->normalizeToPlainText($text);
        if ($text === '') {
            return [];
        }

        // Next, we remove blacklisted words and phrases from the source text before creating any variants.
        if ($this->blacklist !== '') {
            $text = preg_replace('/(?<!\S)(' . mb_strtolower($this->blacklist, 'UTF-8') . ')(?!\S)/u', '', $text);
        }

        // Next, we split words containing -, –, _, or . and index the separate, joined, and original variants.
        $words = $this->expandWordsWithSeparators(preg_split('/\s+/u', trim($text), -1, PREG_SPLIT_NO_EMPTY));

        // Next, we apply the language rules and convert the words to Latin characters.
        $words = $this->normalizeWordsAndApplyLanguageRules($words, $isoCode);

        // Last step is to shorten long keywords and remove duplicates.
        return $this->finalizeKeywords($words);
    }

    /**
     * Converts any string containing HTML or plain text into normalized text suitable for keyword extraction.
     */
    private function normalizeToPlainText(string $text): string
    {
        // First, we separate block-level HTML elements with spaces to preserve word boundaries.
        // Without this, words at the boundaries of block-level elements could be incorrectly joined together.
        if (str_contains($text, '<')) {
            $text = preg_replace(self::HTML_BLOCK_TAGS_REGEX, ' ', $text);
        }

        // Next, we strip HTML tags and decode entities, leaving only the plain text content.
        // We convert it to lowercase afterwards because entities can contain uppercase letters.
        $text = html_entity_decode(strip_tags($text), ENT_NOQUOTES, 'UTF-8');
        $text = mb_strtolower($text, 'UTF-8');

        // Next, we remove tokens made entirely of excluded characters
        $words = preg_split('/\s+/u', trim($text), -1, PREG_SPLIT_NO_EMPTY);
        foreach ($words as $key => $word) {
            if (preg_match('/^[' . self::SEARCH_EXCLUDE . ']+$/u', $word)) {
                unset($words[$key]);
            }
        }

        return implode(' ', $words);
    }

    /**
     * Expands words containing separators into separate parts, joined variants, and original words.
     * We want to maximize the chances of matching keywords by considering all possible word variants.
     *
     * @param string[] $words
     *
     * @return string[] Separate parts, joined variants, and original words with separators
     */
    private function expandWordsWithSeparators(array $words): array
    {
        // First, collect separate parts in source order, followed by joined and original variants.
        $parts = [];
        $joinedWords = [];
        $originalWords = [];
        foreach ($words as $word) {
            // First, remove trailing dots and commas so sentence punctuation does not create extra keywords.
            // Separators inside the word remain available for constructing its variants.
            $word = rtrim($word, '.,');
            if ($word === '') {
                continue;
            }

            // Next, we join numbers separated by punctuation, for example 123/456 becomes 123456.
            // We also keep the original expression, so both versions can be indexed, including fractions and product codes.
            // Hyphens, en dashes, dots, and underscores are handled in the next steps.
            $numericWord = preg_replace('/([' . self::NUMBERS . ']+)(?:(?![-–._])[' . self::PUNCTUATION . '])+(?=[' . self::NUMBERS . '])/u', '$1', $word);
            if ($numericWord !== $word) {
                $originalWords[] = $word;
                $word = $numericWord;
            }

            // Commas separate list items even without spaces; also index the complete comma-containing word.
            if (str_contains($word, ',')) {
                $originalWords[] = $word;
            }
            foreach (preg_split('/,+/', $word, -1, PREG_SPLIT_NO_EMPTY) as $commaPart) {
                // A list item can also end with a dot before the comma.
                $commaPart = rtrim($commaPart, '.');
                if ($commaPart === '') {
                    continue;
                }

                // Words without separators need no separate parts or joined variant.
                if (!preg_match('/[-–._]/u', $commaPart)) {
                    $parts[] = $commaPart;
                    continue;
                }

                // Index separate and joined variants within each list item, without joining across commas.
                array_push($parts, ...preg_split('/[-–._]+/u', $commaPart, -1, PREG_SPLIT_NO_EMPTY));
                $joinedWord = str_replace(['-', '–', '.', '_'], '', $commaPart);
                if ($joinedWord !== '') {
                    $joinedWords[] = $joinedWord;
                }

                $originalWords[] = $commaPart;
            }
        }

        return array_merge($parts, $joinedWords, $originalWords);
    }

    /**
     * Filters short words and converts them to Latin characters using the language rules.
     * Chinese and Japanese allow individual symbols as keywords, including text mixed with Latin words.
     *
     * @param string[] $words
     *
     * @return string[]
     */
    private function normalizeWordsAndApplyLanguageRules(array $words, string $isoCode): array
    {
        // First, determine whether the language allows individual symbols without a minimum word length.
        $splitSymbols = in_array($isoCode, ['zh', 'tw', 'ja'], true);
        $letters = [];
        $symbols = [];
        foreach ($words as $word) {
            // Next, remove standalone excluded characters that can also appear after splitting a word.
            if (preg_match('/^[' . self::SEARCH_EXCLUDE . ']+$/u', $word)) {
                continue;
            }

            // Next, check the minimum length before transliteration, which can expand or split a word.
            if (!$splitSymbols && mb_strlen($word, 'UTF-8') < $this->minWordLength) {
                continue;
            }

            // Next, convert the word to Latin characters and use the result directly when no symbol splitting is needed.
            $transliteratedWord = $this->stringModifier->replaceAccentedChars($word);
            if (!$splitSymbols || strlen($transliteratedWord) === mb_strlen($transliteratedWord, 'UTF-8')) {
                $letters[] = $transliteratedWord;
                continue;
            }

            // Words still containing multibyte symbols are split into their source characters.
            // Each character is converted separately so mixed Latin and Asian text can form individual keywords.
            foreach (preg_split('//u', $word, -1, PREG_SPLIT_NO_EMPTY) as $symbol) {
                if (!preg_match('/^[' . self::SEARCH_EXCLUDE . ']+$/u', $symbol)) {
                    $symbols[] = $this->stringModifier->replaceAccentedChars($symbol);
                }
            }
        }

        // Finally, split spaces produced by transliteration, with whole words before individual symbols.
        return preg_split('/\s+/u', trim(implode(' ', array_merge($letters, $symbols))), -1, PREG_SPLIT_NO_EMPTY);
    }

    /**
     * Shortens keywords to the maximum length and removes duplicates.
     *
     * @param string[] $words
     *
     * @return string[]
     */
    private function finalizeKeywords(array $words): array
    {
        // First, shorten each keyword to the maximum length allowed in the index.
        $keywords = [];
        foreach ($words as $word) {
            $keyword = mb_substr($word, 0, $this->maxWordLength, 'UTF-8');
            if ($keyword !== '') {
                $keywords[] = $keyword;
            }
        }

        // Finally, remove duplicates because truncation can turn different words into the same keyword.
        return array_values(array_unique($keywords));
    }
}
