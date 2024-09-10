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
 * needs please refer to https://devdocs.prestashop.com/ for more information.
 *
 * @author    PrestaShop SA and Contributors <contact@prestashop.com>
 * @copyright Since 2007 PrestaShop SA and Contributors
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 */

/* Copied from Drupal search module, except for \x{0}-\x{2f} that has been replaced by \x{0}-\x{2c}\x{2e}-\x{2f} in order to keep the char '-' */
define(
    'PREG_CLASS_SEARCH_EXCLUDE',
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
'\x{ff5b}-\x{ff65}\x{ff70}\x{ff9e}\x{ff9f}\x{ffe0}-\x{fffd}'
);

define(
    'PREG_CLASS_NUMBERS',
'\x{30}-\x{39}\x{b2}\x{b3}\x{b9}\x{bc}-\x{be}\x{660}-\x{669}\x{6f0}-\x{6f9}' .
'\x{966}-\x{96f}\x{9e6}-\x{9ef}\x{9f4}-\x{9f9}\x{a66}-\x{a6f}\x{ae6}-\x{aef}' .
'\x{b66}-\x{b6f}\x{be7}-\x{bf2}\x{c66}-\x{c6f}\x{ce6}-\x{cef}\x{d66}-\x{d6f}' .
'\x{e50}-\x{e59}\x{ed0}-\x{ed9}\x{f20}-\x{f33}\x{1040}-\x{1049}\x{1369}-' .
'\x{137c}\x{16ee}-\x{16f0}\x{17e0}-\x{17e9}\x{17f0}-\x{17f9}\x{1810}-\x{1819}' .
'\x{1946}-\x{194f}\x{2070}\x{2074}-\x{2079}\x{2080}-\x{2089}\x{2153}-\x{2183}' .
'\x{2460}-\x{249b}\x{24ea}-\x{24ff}\x{2776}-\x{2793}\x{3007}\x{3021}-\x{3029}' .
'\x{3038}-\x{303a}\x{3192}-\x{3195}\x{3220}-\x{3229}\x{3251}-\x{325f}\x{3280}-' .
'\x{3289}\x{32b1}-\x{32bf}\x{ff10}-\x{ff19}'
);

define(
    'PREG_CLASS_PUNCTUATION',
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
'\x{ff65}'
);

/*
 * Matches all CJK characters that are candidates for auto-splitting
 * (Chinese, Japanese, Korean).
 * Contains kana and BMP ideographs.
 */
define('PREG_CLASS_CJK', '\x{3041}-\x{30ff}\x{31f0}-\x{31ff}\x{3400}-\x{4db5}\x{4e00}-\x{9fbb}\x{f900}-\x{fad9}');

class SearchCore
{
    /**
     * @var int
     */
    private static $totalWordInSearchWordTable;

    /**
     * @var int
     */
    public static $coefMin;

    /**
     * @var int
     */
    public static $coefMax;

    /**
     * @var int
     */
    public static $targetLengthMin;

    /**
     * @var int
     */
    public static $targetLengthMax;

    public const PS_SEARCH_MAX_WORDS_IN_TABLE = 100000; /* Max numer of words in ps_search_word, above which $coefs for target length will be everytime equal to 1 */
    public const PS_DEFAULT_SEARCH_MAX_WORD_LENGTH = 30; /* default max word length, for when we are not in fuzzy search mode */
    public const PS_SEARCH_ORDINATE_MIN = 0.5;
    public const PS_SEARCH_ORDINATE_MAX = -1;
    public const PS_SEARCH_ABSCISSA_MIN = 0.5;
    public const PS_SEARCH_ABSCISSA_MAX = 2;
    public const PS_DISTANCE_MAX = 8;

    /**
     * Method that takes a raw string (sentence) and extract all keywords it can find.
     *
     * @param string $string Search expression
     * @param int $id_lang Language ID
     * @param bool $indexation Are we in indexation mode or not
     * @param bool|string $iso_code Iso code to use in sanitization function, to perform some tasks
     */
    public static function extractKeyWords($string, $id_lang, $indexation = false, $iso_code = false)
    {
        // If nothing was passed, nothing to do here
        if (empty($string)) {
            return [];
        }

        // First, we take the string and clean it as a whole.
        // This removes special characters, tags, blacklisted words, hyphens etc.
        // So, "Prestashop Tést A-1000" becomes "prestashop test a 1000";
        $sanitizedString = Search::sanitize($string, $id_lang, $indexation, $iso_code, false);

        // And we separate it by words to get array
        // So we get an array ["prestashop", "test", "a", "1000"]
        $words = explode(' ', $sanitizedString);

        /*
         * Now, because we want to maximize the number of keywords we can get from the expression,
         * we will also try to handle words with hyphens in them. People can search A 1000, A-1000, A1000, we don't know.
         *
         * For this reason, if the original expression contained a dash, we will do the process once again,
         * but keeping the dashes.
         */
        if (strpos($string, '-') !== false) {
            // So, one more sanitization with different parameter, one more separation to get array.
            // We get an array ["prestashop", "test", "a-1000"]
            $sanitizedStringWithHyphens = Search::sanitize($string, $id_lang, $indexation, $iso_code, true);
            $wordsWithHyphens = explode(' ', $sanitizedStringWithHyphens);

            // And we add all words to our final list, in both dashed and non dashed version.
            foreach ($wordsWithHyphens as $word) {
                if (strpos($word, '-') === false) {
                    continue;
                }

                $words[] = $word;
                $word = str_replace('-', '', $word);
                if (!empty($word)) {
                    $words[] = $word;
                }
            }
        }

        return array_unique($words);
    }

    public static function sanitize($string, $id_lang, $indexation = false, $iso_code = false, $keepHyphens = false)
    {
        // If we get some nonsense or space, just return empty string
        if (null === $string || empty($string = trim($string))) {
            return '';
        }

        // The string gets into this method in a raw form of, like "Prestashop Tést A-1000".
        // This get rid of all tags, special characters and convert everything to lowercase.
        $string = Tools::strtolower(strip_tags($string));
        $string = html_entity_decode($string, ENT_NOQUOTES, 'utf-8');
        $string = preg_replace('/([' . PREG_CLASS_NUMBERS . ']+)[' . PREG_CLASS_PUNCTUATION . ']+(?=[' . PREG_CLASS_NUMBERS . '])/u', '\1', $string);
        $string = preg_replace('/[' . PREG_CLASS_SEARCH_EXCLUDE . ']+/u', ' ', $string);
        // Now, our string looks something like "prestashop test a-1000".

        if ($indexation) {
            if (!$keepHyphens) {
                $string = str_replace(['.', '_', '-'], ' ', $string);
            } else {
                $string = str_replace(['.', '_'], ' ', $string);
            }
        } else {
            /*
             * Now, we will search for all aliases, that are contained in our query.
             * Our string looks something like "prestashop test a-1000".
             * Aliases must be searched for in a raw form, with no special characters.
             */
            $query = '
				SELECT a.alias, a.search
				FROM `' . _DB_PREFIX_ . 'alias` a
				WHERE \'' . pSQL($string) . '\' %s AND `active` = 1
            ';

            // Check if we can we use '\b' (faster)
            $useICU = (bool) Db::getInstance((bool) _PS_USE_SQL_SLAVE_)->getValue(
                'SELECT 1 FROM DUAL WHERE \'icu regex\' REGEXP \'\\\\bregex\''
            );
            $aliases = Db::getInstance((bool) _PS_USE_SQL_SLAVE_)->executeS(
                sprintf(
                    $query,
                    $useICU
                        ? 'REGEXP CONCAT(\'\\\\b\', alias, \'\\\\b\')'
                        : 'REGEXP CONCAT(\'(^|[[:space:]]|[[:<:]])\', alias, \'([[:space:]]|[[:>:]]|$)\')'
                )
            );

            $words = explode(' ', $string);
            $processed_words = [];
            foreach ($aliases as $alias) {
                $processed_words = array_merge($processed_words, explode(' ', $alias['search']));
                // delete words that are being replaced with aliases
                $words = array_diff($words, explode(' ', $alias['alias']));
            }
            $string = implode(' ', array_unique(array_merge($processed_words, $words)));
            $string = str_replace(['.', '_'], '', $string);
            if (!$keepHyphens) {
                $string = ltrim(preg_replace('/([^ ])-/', '$1 ', ' ' . $string));
            }
        }

        // Remove all blacklisted words from the search string
        $blacklist = Tools::strtolower(Configuration::get('PS_SEARCH_BLACKLIST', $id_lang));
        if (!empty($blacklist)) {
            $string = preg_replace('/(?<=\s)(' . $blacklist . ')(?=\s)/Su', '', $string);
            $string = preg_replace('/^(' . $blacklist . ')(?=\s)/Su', '', $string);
            $string = preg_replace('/(?<=\s)(' . $blacklist . ')$/Su', '', $string);
            $string = preg_replace('/^(' . $blacklist . ')$/Su', '', $string);
        }

        // If the language is constituted with symbol and there is no "words", then split every chars.
        // This concerns asian languages.
        if (in_array($iso_code, ['zh', 'tw', 'ja'])) {
            // Cut symbols from letters
            $symbols = '';
            $letters = '';
            foreach (explode(' ', $string) as $mb_word) {
                if (strlen(Tools::replaceAccentedChars($mb_word)) == mb_strlen(Tools::replaceAccentedChars($mb_word))) {
                    $letters .= $mb_word . ' ';
                } else {
                    $symbols .= $mb_word . ' ';
                }
            }

            if (preg_match_all('/./u', $symbols, $matches)) {
                $symbols = implode(' ', $matches[0]);
            }

            $string = $letters . $symbols;
        } elseif ($indexation) {
            $minWordLen = (int) Configuration::get('PS_SEARCH_MINWORDLEN');
            if ($minWordLen > 1) {
                --$minWordLen;
                $string = preg_replace('/(?<=\s)[^\s]{1,' . $minWordLen . '}(?=\s)/Su', ' ', $string);
                $string = preg_replace('/^[^\s]{1,' . $minWordLen . '}(?=\s)/Su', '', $string);
                $string = preg_replace('/(?<=\s)[^\s]{1,' . $minWordLen . '}$/Su', '', $string);
                $string = preg_replace('/^[^\s]{1,' . $minWordLen . '}$/Su', '', $string);
            }
        }

        // Do some more cleaning to the string and return it
        $string = Tools::replaceAccentedChars(trim(preg_replace('/\s+/', ' ', $string)));

        return $string;
    }

    /**
     * The holy method to search for products.
     *
     * @param int $id_lang Language identifier
     * @param string $expr Search expression
     * @param int $page_number Start from page
     * @param int $page_size Number of products to return
     * @param $order_by
     * @param $order_way
     * @param bool $ajax Specifies the return structure of data
     * @param bool $use_cookie unused
     * @param Context $context Context to use when searching data. Current context will be used if missing.
     *
     * @return array|bool search results returned in certain structure, depending on $ajax parameter
     */
    public static function find(
        $id_lang,
        $expr,
        $page_number = 1,
        $page_size = 1,
        $order_by = 'position',
        $order_way = 'desc',
        $ajax = false,
        $use_cookie = true,
        Context $context = null
    ) {
        if (!$context) {
            $context = Context::getContext();
        }

        // Get database instance to use
        $db = Db::getInstance(_PS_USE_SQL_SLAVE_);

        // Initialize pagination if nonsense was passed
        if (empty($page_number)) {
            $page_number = 1;
        }
        if (empty($page_size)) {
            $page_size = 1;
        }

        // Initialize and validate sorting
        if (!Validate::isOrderBy($order_by) || !Validate::isOrderWay($order_way)) {
            return false;
        }

        /*
         * Variables related to fuzzy search.
         *
         * $psFuzzySearch to see if fuzzy search is enabled.
         * $fuzzyMaxLoop configuration to limit how many times we try to fuzzy search for each word.
         * $fuzzyLoop to track how many times we tried to fuzzy search, so we can break the loop.
         */
        $fuzzyLoop = 0;
        $fuzzyMaxLoop = (int) Configuration::get('PS_SEARCH_FUZZY_MAX_LOOP');
        $psFuzzySearch = (int) Configuration::get('PS_SEARCH_FUZZY');

        // Score array to keep track of words we will get weights for (for relevance)
        $scoreArray = [];

        // Word count to track how many words we got for given expression
        $wordCnt = 0;

        // Final resulting array with product IDs found
        $foundProductIds = [];

        // Expressions to search for. If user passes search expressions separated with semicolon, they will be treated separately
        $expressions = explode(';', $expr);

        // Minimal word length configuration, so we don't search for extremely short words
        $psSearchMinWordLength = (int) Configuration::get('PS_SEARCH_MINWORDLEN');

        // Ok, now let's go through each expression. It's usually only one.
        foreach ($expressions as $expression) {
            $productIdsFoundForCurrentExpression = null;

            // Get all words from current expression
            $words = Search::extractKeyWords($expression, $id_lang, false, $context->language->iso_code);
            foreach ($words as $key => $word) {
                // Skip all empty words or shorter than our limit
                if (empty($word) || strlen($word) < $psSearchMinWordLength) {
                    unset($words[$key]);
                    continue;
                }

                // We prepare a basic part of SQL query that we will be searching
                $sql = 'SELECT DISTINCT si.id_product ' .
                    'FROM ' . _DB_PREFIX_ . 'search_word sw ' .
                    'LEFT JOIN ' . _DB_PREFIX_ . 'search_index si ON sw.id_word = si.id_word ' .
                    'LEFT JOIN ' . _DB_PREFIX_ . 'product_shop product_shop ON (product_shop.`id_product` = si.`id_product`) ' .
                    'WHERE sw.id_lang = ' . (int) $id_lang . ' ' .
                    'AND sw.id_shop = ' . $context->shop->id . ' ' .
                    'AND product_shop.`active` = 1 ' .
                    'AND product_shop.`visibility` IN ("both", "search") ' .
                    'AND product_shop.indexed = 1 ' .
                    'AND sw.word LIKE ';

                /*
                 * Now, find all products from the index, that have this keyword.
                 * We start with the word itself wrapped in %%, coming from getSearchParamFromWord.
                 *
                 * If we don't find anything, we will leverage levenshtein algorithm to find a closest keyword
                 * via findClosestWeightestWord method.
                 *
                 * We will keep searching with different expressions, until we find something
                 * or we exceed our fuzzy search limit.
                 */
                $sql_param_search = self::getSearchParamFromWord($word);
                while (!($result = $db->executeS($sql . "'" . $sql_param_search . "';", true, false))) {
                    if (!$psFuzzySearch
                        || $fuzzyLoop++ > $fuzzyMaxLoop
                        || !($sql_param_search = static::findClosestWeightestWord($context, $word))
                    ) {
                        break;
                    }
                }

                // If nothing was found after X retries, skip this keyword
                if (!$result) {
                    unset($words[$key]);
                    continue;
                }

                /*
                 * Extremely important step that someone broke in the past.
                 * Now if we found something, we need to intersect it with the the previously found products.
                 * If we search for "Red car", we want to get products that contain "red" AND contain "car".
                 * Somebody broke it before and it found all things "car" and all things "red".
                 */
                $productIdsFoundForCurrentWord = array_column($result, 'id_product');
                if ($productIdsFoundForCurrentExpression === null) {
                    $productIdsFoundForCurrentExpression = $productIdsFoundForCurrentWord;
                } else {
                    $productIdsFoundForCurrentExpression = array_intersect($productIdsFoundForCurrentExpression, $productIdsFoundForCurrentWord);
                }

                // Add the expresion to our score array, so we can later calculate the relevance
                $scoreArray[] = 'sw.word LIKE \'' . $sql_param_search . '\'';
            }
            $wordCnt += count($words);
            if ($productIdsFoundForCurrentExpression) {
                $foundProductIds = array_merge($foundProductIds, $productIdsFoundForCurrentExpression);
            }
        }

        // Remove all duplicates from product IDs
        $foundProductIds = array_unique($foundProductIds);

        // If we didn't end up anything now, we can immediately return empty response.
        // No sense in calculating weights of nothing.
        if (!$wordCnt || !count($foundProductIds)) {
            return $ajax ? [] : ['total' => 0, 'result' => []];
        }

        /*
         * Now, we have a list of randomly ordered product IDs for our search,
         * but we don't know if they are active, should be displayed, nothing.
         */

        /*
         * This is a subquery that selects weight for each keyword.
         * This is used as "relevance" sort order.
         */
        $sqlScore = '';
        if (!empty($scoreArray) && is_array($scoreArray)) {
            $sqlScore = ',( ' .
                'SELECT SUM(weight) ' .
                'FROM ' . _DB_PREFIX_ . 'search_word sw ' .
                'LEFT JOIN ' . _DB_PREFIX_ . 'search_index si ON sw.id_word = si.id_word ' .
                'WHERE sw.id_lang = ' . (int) $id_lang . ' ' .
                'AND sw.id_shop = ' . $context->shop->id . ' ' .
                'AND si.id_product = p.id_product ' .
                'AND (' . implode(' OR ', $scoreArray) . ') ' .
                ') position';
        }

        $sqlGroups = '';
        if (Group::isFeatureActive()) {
            $groups = FrontController::getCurrentCustomerGroups();
            $sqlGroups = 'AND cg.`id_group` ' . (count($groups) ? 'IN (' . implode(',', $groups) . ')' : '=' . (int) Group::getCurrent()->id);
        }

        // Select products from the list of IDs that should be displayed and can be returned.
        $results = $db->executeS(
            'SELECT DISTINCT cp.`id_product` ' .
            'FROM `' . _DB_PREFIX_ . 'category_product` cp ' .
            (Group::isFeatureActive() ? 'INNER JOIN `' . _DB_PREFIX_ . 'category_group` cg ON cp.`id_category` = cg.`id_category`' : '') . ' ' .
            'INNER JOIN `' . _DB_PREFIX_ . 'category` c ON cp.`id_category` = c.`id_category` ' .
            'INNER JOIN `' . _DB_PREFIX_ . 'product` p ON cp.`id_product` = p.`id_product` ' .
            Shop::addSqlAssociation('product', 'p', false) . ' ' .
            'WHERE c.`active` = 1 ' .
            'AND product_shop.`active` = 1 ' .
            'AND product_shop.`visibility` IN ("both", "search") ' .
            'AND product_shop.indexed = 1 ' .
            'AND cp.id_product IN (' . implode(',', $foundProductIds) . ')' . $sqlGroups,
            true,
            false
        );

        // And again, extract their IDs
        $eligibleProducts = [];
        foreach ($results as $row) {
            $eligibleProducts[] = $row['id_product'];
        }

        // If we didn't end up anything now, we can immediately return empty response.
        // No sense in getting more data for nothing.
        if (!count($eligibleProducts)) {
            return $ajax ? [] : ['total' => 0, 'result' => []];
        }

        /*
         * Now, we have a list of (also) randomly ordered product IDs for our search,
         * but we know that they are real, active products that should be returned.
         */
        $product_pool = ' IN (' . implode(',', $eligibleProducts) . ') ';

        if ($ajax) {
            $sql = 'SELECT DISTINCT p.id_product, pl.name pname, cl.name cname,
						cl.link_rewrite crewrite, pl.link_rewrite prewrite ' . $sqlScore . '
					FROM ' . _DB_PREFIX_ . 'product p
					INNER JOIN `' . _DB_PREFIX_ . 'product_lang` pl ON (
						p.`id_product` = pl.`id_product`
						AND pl.`id_lang` = ' . (int) $id_lang . Shop::addSqlRestrictionOnLang('pl') . '
					)
					' . Shop::addSqlAssociation('product', 'p') . '
					INNER JOIN `' . _DB_PREFIX_ . 'category_lang` cl ON (
						product_shop.`id_category_default` = cl.`id_category`
						AND cl.`id_lang` = ' . (int) $id_lang . Shop::addSqlRestrictionOnLang('cl') . '
					)
					WHERE p.`id_product` ' . $product_pool . '
					ORDER BY position DESC LIMIT 10';

            return $db->executeS($sql, true, false);
        }

        if (strpos($order_by, '.') > 0) {
            $order_by = explode('.', $order_by);
            $order_by = pSQL($order_by[0]) . '.`' . pSQL($order_by[1]) . '`';
        }
        $alias = '';
        if ($order_by == 'price') {
            $alias = 'product_shop.';
        } elseif (in_array($order_by, ['date_upd', 'date_add'])) {
            $alias = 'p.';
        }
        $sql = 'SELECT p.*, product_shop.*, stock.out_of_stock, IFNULL(stock.quantity, 0) as quantity,
				pl.`description_short`, pl.`available_now`, pl.`available_later`, pl.`link_rewrite`, pl.`name`,
			 image_shop.`id_image` id_image, il.`legend`, m.`name` manufacturer_name ' . $sqlScore . ',
				DATEDIFF(
					p.`date_add`,
					DATE_SUB(
						"' . date('Y-m-d') . ' 00:00:00",
						INTERVAL ' . (Validate::isUnsignedInt(Configuration::get('PS_NB_DAYS_NEW_PRODUCT')) ? Configuration::get('PS_NB_DAYS_NEW_PRODUCT') : 20) . ' DAY
					)
				) > 0 new' . (Combination::isFeatureActive() ? ', product_attribute_shop.minimal_quantity AS product_attribute_minimal_quantity, IFNULL(product_attribute_shop.`id_product_attribute`,0) id_product_attribute' : '') . '
				FROM ' . _DB_PREFIX_ . 'product p
				' . Shop::addSqlAssociation('product', 'p') . '
				INNER JOIN `' . _DB_PREFIX_ . 'product_lang` pl ON (
					p.`id_product` = pl.`id_product`
					AND pl.`id_lang` = ' . (int) $id_lang . Shop::addSqlRestrictionOnLang('pl') . '
				)
				' . (Combination::isFeatureActive() ? 'LEFT JOIN `' . _DB_PREFIX_ . 'product_attribute_shop` product_attribute_shop FORCE INDEX (id_product)
				    ON (p.`id_product` = product_attribute_shop.`id_product` AND product_attribute_shop.`default_on` = 1 AND product_attribute_shop.id_shop=' . (int) $context->shop->id . ')' : '') . '
				' . Product::sqlStock('p', 0) . '
				LEFT JOIN `' . _DB_PREFIX_ . 'manufacturer` m FORCE INDEX (PRIMARY)
				    ON m.`id_manufacturer` = p.`id_manufacturer`
				LEFT JOIN `' . _DB_PREFIX_ . 'image_shop` image_shop FORCE INDEX (id_product)
					ON (image_shop.`id_product` = p.`id_product` AND image_shop.cover=1 AND image_shop.id_shop=' . (int) $context->shop->id . ')
				LEFT JOIN `' . _DB_PREFIX_ . 'image_lang` il ON (image_shop.`id_image` = il.`id_image` AND il.`id_lang` = ' . (int) $id_lang . ')
				WHERE p.`id_product` ' . $product_pool . '
				GROUP BY product_shop.id_product';

        if ($order_by !== 'price') {
            $sql .= ($order_by ? ' ORDER BY  ' . $alias . $order_by : '') . ($order_way ? ' ' . $order_way : '') . '
				LIMIT ' . (int) (($page_number - 1) * $page_size) . ',' . (int) $page_size;
        }

        $result = $db->executeS($sql, true, false);

        if ($order_by === 'price') {
            Tools::orderbyPrice($result, $order_way);
            $result = array_slice($result, (int) (($page_number - 1) * $page_size), (int) $page_size);
        }

        $sql = 'SELECT COUNT(*)
				FROM ' . _DB_PREFIX_ . 'product p
				' . Shop::addSqlAssociation('product', 'p') . '
				INNER JOIN `' . _DB_PREFIX_ . 'product_lang` pl ON (
					p.`id_product` = pl.`id_product`
					AND pl.`id_lang` = ' . (int) $id_lang . Shop::addSqlRestrictionOnLang('pl') . '
				)
				LEFT JOIN `' . _DB_PREFIX_ . 'manufacturer` m ON m.`id_manufacturer` = p.`id_manufacturer`
				WHERE p.`id_product` ' . $product_pool;
        $total = $db->getValue($sql, false);

        if (!$result) {
            $result_properties = false;
        } else {
            $result_properties = Product::getProductsProperties((int) $id_lang, $result);
        }

        return ['total' => $total, 'result' => $result_properties];
    }

    /**
     * @param Db $db
     * @param int $id_product
     * @param int $id_lang
     *
     * @return string
     */
    public static function getTags($db, $id_product, $id_lang)
    {
        $tags = '';
        $tagsArray = $db->executeS('
		SELECT t.name FROM ' . _DB_PREFIX_ . 'product_tag pt
		LEFT JOIN ' . _DB_PREFIX_ . 'tag t ON (pt.id_tag = t.id_tag AND t.id_lang = ' . (int) $id_lang . ')
		WHERE pt.id_product = ' . (int) $id_product, true, false);
        foreach ($tagsArray as $tag) {
            $tags .= $tag['name'] . ' ';
        }

        return $tags;
    }

    /**
     * @param Db $db
     * @param int $id_product
     * @param int $id_lang
     *
     * @return string
     */
    public static function getAttributes($db, $id_product, $id_lang)
    {
        if (!Combination::isFeatureActive()) {
            return '';
        }

        $attributes = '';
        $attributesArray = $db->executeS('
		SELECT al.name FROM ' . _DB_PREFIX_ . 'product_attribute pa
		INNER JOIN ' . _DB_PREFIX_ . 'product_attribute_combination pac ON pa.id_product_attribute = pac.id_product_attribute
		INNER JOIN ' . _DB_PREFIX_ . 'attribute_lang al ON (pac.id_attribute = al.id_attribute AND al.id_lang = ' . (int) $id_lang . ')
		' . Shop::addSqlAssociation('product_attribute', 'pa') . '
		WHERE pa.id_product = ' . (int) $id_product, true, false);
        foreach ($attributesArray as $attribute) {
            $attributes .= $attribute['name'] . ' ';
        }

        return $attributes;
    }

    /**
     * @param Db $db
     * @param int $id_product
     * @param int $id_lang
     *
     * @return string
     */
    public static function getFeatures($db, $id_product, $id_lang)
    {
        if (!Feature::isFeatureActive()) {
            return '';
        }

        $features = '';
        $featuresArray = $db->executeS('
		SELECT fvl.value FROM ' . _DB_PREFIX_ . 'feature_product fp
		LEFT JOIN ' . _DB_PREFIX_ . 'feature_value_lang fvl ON (fp.id_feature_value = fvl.id_feature_value AND fvl.id_lang = ' . (int) $id_lang . ')
		WHERE fp.id_product = ' . (int) $id_product, true, false);
        foreach ($featuresArray as $feature) {
            $features .= $feature['value'] . ' ';
        }

        return $features;
    }

    /**
     * @param array $weight_array
     *
     * @return string
     */
    protected static function getSQLProductAttributeFields(&$weight_array)
    {
        $sql = '';
        if (is_array($weight_array)) {
            foreach ($weight_array as $key => $weight) {
                if ((int) $weight) {
                    switch ($key) {
                        case 'pa_reference':
                            $sql .= ', pa.reference AS pa_reference';

                            break;
                        case 'pa_supplier_reference':
                            $sql .= ', pa.supplier_reference AS pa_supplier_reference';

                            break;
                        case 'pa_ean13':
                            $sql .= ', pa.ean13 AS pa_ean13';

                            break;
                        case 'pa_isbn':
                            $sql .= ', pa.isbn AS pa_isbn';

                            break;
                        case 'pa_upc':
                            $sql .= ', pa.upc AS pa_upc';

                            break;
                        case 'pa_mpn':
                            $sql .= ', pa.mpn AS pa_mpn';

                            break;
                    }
                }
            }
        }

        return $sql;
    }

    protected static function getProductsToIndex($total_languages, $id_product = false, $limit = 50, $weight_array = [])
    {
        $ids = null;
        if (!$id_product) {
            // Limit products for each step but be sure that each attribute is taken into account
            $sql = 'SELECT p.id_product FROM ' . _DB_PREFIX_ . 'product p
				' . Shop::addSqlAssociation('product', 'p', true, null, true) . '
				WHERE product_shop.`indexed` = 0
				AND product_shop.`visibility` IN ("both", "search")
				AND product_shop.`active` = 1
				ORDER BY product_shop.`id_product` ASC
				LIMIT ' . (int) $limit;

            $res = Db::getInstance()->executeS($sql, false);
            while ($row = Db::getInstance()->nextRow($res)) {
                $ids[] = $row['id_product'];
            }
        }

        // Now get every attribute in every language
        $sql = 'SELECT p.id_product, pl.id_lang, pl.id_shop, l.iso_code';

        if (is_array($weight_array)) {
            foreach ($weight_array as $key => $weight) {
                if ((int) $weight) {
                    switch ($key) {
                        case 'pname':
                            $sql .= ', pl.name pname';

                            break;
                        case 'reference':
                            $sql .= ', p.reference';

                            break;
                        case 'supplier_reference':
                            $sql .= ', p.supplier_reference';

                            break;
                        case 'ean13':
                            $sql .= ', p.ean13';

                            break;
                        case 'isbn':
                            $sql .= ', p.isbn';

                            break;
                        case 'upc':
                            $sql .= ', p.upc';

                            break;
                        case 'mpn':
                            $sql .= ', p.mpn';

                            break;
                        case 'description_short':
                            $sql .= ', pl.description_short';

                            break;
                        case 'description':
                            $sql .= ', pl.description';

                            break;
                        case 'cname':
                            $sql .= ', cl.name cname';

                            break;
                        case 'mname':
                            $sql .= ', m.name mname';

                            break;
                    }
                }
            }
        }

        $sql .= ' FROM ' . _DB_PREFIX_ . 'product p
			LEFT JOIN ' . _DB_PREFIX_ . 'product_lang pl
				ON p.id_product = pl.id_product
			' . Shop::addSqlAssociation('product', 'p', true, null, true) . '
			LEFT JOIN ' . _DB_PREFIX_ . 'category_lang cl
				ON (cl.id_category = product_shop.id_category_default AND pl.id_lang = cl.id_lang AND cl.id_shop = product_shop.id_shop)
			LEFT JOIN ' . _DB_PREFIX_ . 'manufacturer m
				ON m.id_manufacturer = p.id_manufacturer
			LEFT JOIN ' . _DB_PREFIX_ . 'lang l
				ON l.id_lang = pl.id_lang
			WHERE product_shop.indexed = 0
			AND product_shop.visibility IN ("both", "search")
			' . ($id_product ? 'AND p.id_product = ' . (int) $id_product : '') . '
			' . ($ids ? 'AND p.id_product IN (' . implode(',', array_map('intval', $ids)) . ')' : '') . '
			AND product_shop.`active` = 1
			AND pl.`id_shop` = product_shop.`id_shop`';

        return Db::getInstance()->executeS($sql, true, false);
    }

    /**
     * @param Db $db
     * @param int $id_product
     * @param string $sql_attribute
     *
     * @return array|null
     */
    protected static function getAttributesFields($db, $id_product, $sql_attribute)
    {
        return $db->executeS('SELECT id_product ' . $sql_attribute . ' FROM ' .
            _DB_PREFIX_ . 'product_attribute pa WHERE pa.id_product = ' . (int) $id_product, true, false);
    }

    /**
     * @param array $product_array
     * @param array $weight_array
     * @param string $key
     * @param string $value
     * @param int $id_lang
     * @param string|bool $iso_code
     */
    protected static function fillProductArray(&$product_array, $weight_array, $key, $value, $id_lang, $iso_code)
    {
        if (strncmp($key, 'id_', 3) && isset($weight_array[$key])) {
            $words = Search::extractKeyWords($value, (int) $id_lang, true, $iso_code);
            foreach ($words as $word) {
                if (!empty($word)) {
                    $word = Tools::substr($word, 0, self::getMaximumWordLength());

                    if (!isset($product_array[$word])) {
                        $product_array[$word] = 0;
                    }
                    $product_array[$word] += $weight_array[$key];
                }
            }
        }
    }

    public static function indexation($full = false, $id_product = false)
    {
        $db = Db::getInstance();

        if ($id_product) {
            $full = false;
        }

        if ($full && Context::getContext()->shop->getContext() == Shop::CONTEXT_SHOP) {
            $db->execute('DELETE si, sw FROM `' . _DB_PREFIX_ . 'search_index` si
				INNER JOIN `' . _DB_PREFIX_ . 'product` p ON (p.id_product = si.id_product)
				' . Shop::addSqlAssociation('product', 'p') . '
				INNER JOIN `' . _DB_PREFIX_ . 'search_word` sw ON (sw.id_word = si.id_word AND product_shop.id_shop = sw.id_shop)
				WHERE product_shop.`visibility` IN ("both", "search")
				AND product_shop.`active` = 1');
            $db->execute('UPDATE `' . _DB_PREFIX_ . 'product` p
				' . Shop::addSqlAssociation('product', 'p') . '
				SET p.`indexed` = 0, product_shop.`indexed` = 0
				WHERE product_shop.`visibility` IN ("both", "search")
				AND product_shop.`active` = 1
				');
        } elseif ($full) {
            $db->execute('TRUNCATE ' . _DB_PREFIX_ . 'search_index');
            $db->execute('TRUNCATE ' . _DB_PREFIX_ . 'search_word');
            ObjectModel::updateMultishopTable('Product', ['indexed' => 0]);
        } else {
            $db->execute('DELETE si FROM `' . _DB_PREFIX_ . 'search_index` si
				INNER JOIN `' . _DB_PREFIX_ . 'product` p ON (p.id_product = si.id_product)
				' . Shop::addSqlAssociation('product', 'p') . '
				WHERE product_shop.`visibility` IN ("both", "search")
				AND product_shop.`active` = 1
				AND ' . ($id_product ? 'p.`id_product` = ' . (int) $id_product : 'product_shop.`indexed` = 0'));

            $db->execute('UPDATE `' . _DB_PREFIX_ . 'product` p
				' . Shop::addSqlAssociation('product', 'p') . '
				SET p.`indexed` = 0, product_shop.`indexed` = 0
				WHERE product_shop.`visibility` IN ("both", "search")
				AND product_shop.`active` = 1
				AND ' . ($id_product ? 'p.`id_product` = ' . (int) $id_product : 'product_shop.`indexed` = 0'));
        }

        // Every fields are weighted according to the configuration in the backend
        $weight_array = [
            'pname' => Configuration::get('PS_SEARCH_WEIGHT_PNAME'),
            'reference' => Configuration::get('PS_SEARCH_WEIGHT_REF'),
            'pa_reference' => Configuration::get('PS_SEARCH_WEIGHT_REF'),
            'supplier_reference' => Configuration::get('PS_SEARCH_WEIGHT_REF'),
            'pa_supplier_reference' => Configuration::get('PS_SEARCH_WEIGHT_REF'),
            'ean13' => Configuration::get('PS_SEARCH_WEIGHT_REF'),
            'pa_ean13' => Configuration::get('PS_SEARCH_WEIGHT_REF'),
            'isbn' => Configuration::get('PS_SEARCH_WEIGHT_REF'),
            'pa_isbn' => Configuration::get('PS_SEARCH_WEIGHT_REF'),
            'upc' => Configuration::get('PS_SEARCH_WEIGHT_REF'),
            'pa_upc' => Configuration::get('PS_SEARCH_WEIGHT_REF'),
            'mpn' => Configuration::get('PS_SEARCH_WEIGHT_REF'),
            'pa_mpn' => Configuration::get('PS_SEARCH_WEIGHT_REF'),
            'description_short' => Configuration::get('PS_SEARCH_WEIGHT_SHORTDESC'),
            'description' => Configuration::get('PS_SEARCH_WEIGHT_DESC'),
            'cname' => Configuration::get('PS_SEARCH_WEIGHT_CNAME'),
            'mname' => Configuration::get('PS_SEARCH_WEIGHT_MNAME'),
            'tags' => Configuration::get('PS_SEARCH_WEIGHT_TAG'),
            'attributes' => Configuration::get('PS_SEARCH_WEIGHT_ATTRIBUTE'),
            'features' => Configuration::get('PS_SEARCH_WEIGHT_FEATURE'),
        ];

        // Those are kind of global variables required to save the processed data in the database every X occurrences, in order to avoid overloading MySQL
        $count_words = 0;
        $query_array3 = [];

        // Retrieve the number of languages
        $total_languages = count(Language::getIDs(false));

        $sql_attribute = Search::getSQLProductAttributeFields($weight_array);
        // Products are processed 50 by 50 in order to avoid overloading MySQL
        while (($products = Search::getProductsToIndex($total_languages, $id_product, 50, $weight_array)) && (count($products) > 0)) {
            $products_array = [];
            // Now each non-indexed product is processed one by one, language by language
            foreach ($products as $product) {
                if ((int) $weight_array['tags']) {
                    $product['tags'] = Search::getTags($db, (int) $product['id_product'], (int) $product['id_lang']);
                }
                if ((int) $weight_array['attributes']) {
                    $product['attributes'] = Search::getAttributes($db, (int) $product['id_product'], (int) $product['id_lang']);
                }
                if ((int) $weight_array['features']) {
                    $product['features'] = Search::getFeatures($db, (int) $product['id_product'], (int) $product['id_lang']);
                }
                if ($sql_attribute) {
                    $attribute_fields = Search::getAttributesFields($db, (int) $product['id_product'], $sql_attribute);
                    if ($attribute_fields) {
                        $product['attributes_fields'] = $attribute_fields;
                    }
                }

                // Data must be cleaned of html, bad characters, spaces and anything, then if the resulting words are long enough, they're added to the array
                $product_array = [];
                foreach ($product as $key => $value) {
                    if ($key == 'attributes_fields') {
                        foreach ($value as $pa_array) {
                            foreach ($pa_array as $pa_key => $pa_value) {
                                Search::fillProductArray($product_array, $weight_array, $pa_key, $pa_value, $product['id_lang'], $product['iso_code']);
                            }
                        }
                    } else {
                        Search::fillProductArray($product_array, $weight_array, $key, $value, $product['id_lang'], $product['iso_code']);
                    }
                }

                // If we find words that need to be indexed, they're added to the word table in the database
                if (is_array($product_array) && !empty($product_array)) {
                    $query_array = $query_array2 = [];
                    foreach ($product_array as $word => $weight) {
                        if ($weight) {
                            $query_array[$word] = '(' . (int) $product['id_lang'] . ', ' . (int) $product['id_shop'] . ', \'' . pSQL($word) . '\')';
                            $query_array2[] = '\'' . pSQL($word) . '\'';
                        }
                    }

                    if (is_array($query_array) && !empty($query_array)) {
                        // The words are inserted...
                        $db->execute('
						INSERT IGNORE INTO ' . _DB_PREFIX_ . 'search_word (id_lang, id_shop, word)
						VALUES ' . implode(',', $query_array), false);
                    }
                    $word_ids_by_word = [];
                    if (is_array($query_array2) && !empty($query_array2)) {
                        // ...then their IDs are retrieved
                        $added_words = $db->executeS('
						SELECT sw.id_word, sw.word
						FROM ' . _DB_PREFIX_ . 'search_word sw
						WHERE sw.word IN (' . implode(',', $query_array2) . ')
						AND sw.id_lang = ' . (int) $product['id_lang'] . '
						AND sw.id_shop = ' . (int) $product['id_shop'], true, false);
                        foreach ($added_words as $word_id) {
                            $word_ids_by_word['_' . $word_id['word']] = (int) $word_id['id_word'];
                        }
                    }
                }

                foreach ($product_array as $word => $weight) {
                    if (!$weight) {
                        continue;
                    }
                    if (!isset($word_ids_by_word['_' . $word])) {
                        continue;
                    }
                    $id_word = $word_ids_by_word['_' . $word];
                    if (!$id_word) {
                        continue;
                    }
                    $query_array3[] = '(' . (int) $product['id_product'] . ',' .
                        (int) $id_word . ',' . (int) $weight . ')';
                    // Force save every 200 words in order to avoid overloading MySQL
                    if (++$count_words % 200 == 0) {
                        Search::saveIndex($query_array3);
                    }
                }

                $products_array[] = (int) $product['id_product'];
            }
            $products_array = array_unique($products_array);
            Search::setProductsAsIndexed($products_array);

            // One last save is done at the end in order to save what's left
            Search::saveIndex($query_array3);
        }

        return true;
    }

    public static function removeProductsSearchIndex($products)
    {
        if (is_array($products) && !empty($products)) {
            Db::getInstance()->execute('DELETE FROM ' . _DB_PREFIX_ . 'search_index WHERE id_product IN (' . implode(',', array_unique(array_map('intval', $products))) . ')');
            ObjectModel::updateMultishopTable('Product', ['indexed' => 0], 'a.id_product IN (' . implode(',', array_map('intval', $products)) . ')');
        }
    }

    protected static function setProductsAsIndexed(&$products)
    {
        if (is_array($products) && !empty($products)) {
            ObjectModel::updateMultishopTable('Product', ['indexed' => 1], 'a.id_product IN (' . implode(',', array_map('intval', $products)) . ')');
        }
    }

    /** $queryArray3 is automatically emptied in order to be reused immediately */
    protected static function saveIndex(&$queryArray3)
    {
        if (is_array($queryArray3) && !empty($queryArray3)) {
            $query = 'INSERT INTO ' . _DB_PREFIX_ . 'search_index (id_product, id_word, weight)
				VALUES ' . implode(',', $queryArray3) . '
				ON DUPLICATE KEY UPDATE weight = weight + VALUES(weight)';

            Db::getInstance()->execute($query, false);
        }
        $queryArray3 = [];
    }

    public static function searchTag(
        $id_lang,
        $tag,
        $count = false,
        $pageNumber = 0,
        $pageSize = 10,
        $orderBy = false,
        $orderWay = false,
        $useCookie = true,
        Context $context = null
    ) {
        if (!$context) {
            $context = Context::getContext();
        }

        // Only use cookie if id_customer is not present
        if ($useCookie) {
            $id_customer = (int) $context->customer->id;
        } else {
            $id_customer = 0;
        }

        if (!is_numeric($pageNumber) || !is_numeric($pageSize) || !Validate::isBool($count) || !Validate::isValidSearch($tag)
            || $orderBy && !$orderWay || ($orderBy && !Validate::isOrderBy($orderBy)) || ($orderWay && !Validate::isOrderBy($orderWay))
        ) {
            return false;
        }

        if ($pageNumber < 1) {
            $pageNumber = 1;
        }
        if ($pageSize < 1) {
            $pageSize = 10;
        }

        $id = Context::getContext()->shop->id;
        $id_shop = $id ? $id : Configuration::get('PS_SHOP_DEFAULT');

        $sqlGroups = '';
        if (Group::isFeatureActive()) {
            $groups = FrontController::getCurrentCustomerGroups();
            $sqlGroups = 'AND cg.`id_group` ' . (count($groups) ? 'IN (' . implode(',', $groups) . ')' : '=' . (int) Group::getCurrent()->id);
        }

        if ($count) {
            return (int) Db::getInstance(_PS_USE_SQL_SLAVE_)->getValue(
                'SELECT COUNT(DISTINCT pt.`id_product`) nb ' .
                'FROM ' .
                '`' . _DB_PREFIX_ . 'tag` t ' .
                'STRAIGHT_JOIN `' . _DB_PREFIX_ . 'product_tag` pt ON (pt.`id_tag` = t.`id_tag` AND t.`id_lang` = ' . (int) $id_lang . ') ' .
                'STRAIGHT_JOIN `' . _DB_PREFIX_ . 'product` p ON (p.`id_product` = pt.`id_product`) ' .
                Shop::addSqlAssociation('product', 'p') . ' ' .
                'LEFT JOIN `' . _DB_PREFIX_ . 'category_product` cp ON (cp.`id_product` = p.`id_product`) ' .
                'LEFT JOIN `' . _DB_PREFIX_ . 'category_shop` cs ON (cp.`id_category` = cs.`id_category` AND cs.`id_shop` = ' . (int) $id_shop . ') ' .
                (Group::isFeatureActive() ? 'LEFT JOIN `' . _DB_PREFIX_ . 'category_group` cg ON (cg.`id_category` = cp.`id_category`)' : '') . ' ' .
                'WHERE product_shop.`active` = 1 ' .
                'AND product_shop.`visibility` IN (\'both\', \'search\') ' .
                'AND cs.`id_shop` = ' . (int) Context::getContext()->shop->id . ' ' .
                $sqlGroups . ' ' .
                'AND t.`name` LIKE \'%' . pSQL($tag) . '%\''
            );
        }

        $sql = 'SELECT DISTINCT p.*, product_shop.*, stock.out_of_stock, IFNULL(stock.quantity, 0) as quantity, pl.`description_short`, pl.`link_rewrite`, pl.`name`, pl.`available_now`, pl.`available_later`,
					MAX(image_shop.`id_image`) id_image, il.`legend`, m.`name` manufacturer_name, 1 position,
					DATEDIFF(
						p.`date_add`,
						DATE_SUB(
							"' . date('Y-m-d') . ' 00:00:00",
							INTERVAL ' . (Validate::isUnsignedInt(Configuration::get('PS_NB_DAYS_NEW_PRODUCT')) ? Configuration::get('PS_NB_DAYS_NEW_PRODUCT') : 20) . ' DAY
						)
					) > 0 new
				FROM
				`' . _DB_PREFIX_ . 'tag` t
				STRAIGHT_JOIN `' . _DB_PREFIX_ . 'product_tag` pt ON (pt.`id_tag` = t.`id_tag` AND t.`id_lang` = ' . (int) $id_lang . ')
				STRAIGHT_JOIN `' . _DB_PREFIX_ . 'product` p ON (p.`id_product` = pt.`id_product`)
				INNER JOIN `' . _DB_PREFIX_ . 'product_lang` pl ON (
					p.`id_product` = pl.`id_product`
					AND pl.`id_lang` = ' . (int) $id_lang . Shop::addSqlRestrictionOnLang('pl') . '
				)
				' . Shop::addSqlAssociation('product', 'p', false) . '
				LEFT JOIN `' . _DB_PREFIX_ . 'product_attribute_shop` product_attribute_shop
				ON (p.`id_product` = product_attribute_shop.`id_product` AND product_attribute_shop.`default_on` = 1 AND product_attribute_shop.id_shop=' . (int) $context->shop->id . ')
				LEFT JOIN `' . _DB_PREFIX_ . 'image_shop` image_shop
					ON (image_shop.`id_product` = p.`id_product` AND image_shop.cover=1 AND image_shop.id_shop=' . (int) $context->shop->id . ')
				LEFT JOIN `' . _DB_PREFIX_ . 'image_lang` il ON (image_shop.`id_image` = il.`id_image` AND il.`id_lang` = ' . (int) $id_lang . ')
				LEFT JOIN `' . _DB_PREFIX_ . 'manufacturer` m ON (m.`id_manufacturer` = p.`id_manufacturer`)
				LEFT JOIN `' . _DB_PREFIX_ . 'category_product` cp ON (cp.`id_product` = p.`id_product`)
				' . (Group::isFeatureActive() ? 'LEFT JOIN `' . _DB_PREFIX_ . 'category_group` cg ON (cg.`id_category` = cp.`id_category`)' : '') . '
				LEFT JOIN `' . _DB_PREFIX_ . 'category_shop` cs ON (cp.`id_category` = cs.`id_category` AND cs.`id_shop` = ' . (int) $id_shop . ')
				' . Product::sqlStock('p', 0) . '
				WHERE product_shop.`active` = 1
                    AND product_shop.`visibility` IN (\'both\', \'search\')
					AND cs.`id_shop` = ' . (int) Context::getContext()->shop->id . '
					' . $sqlGroups . '
					AND t.`name` LIKE \'%' . pSQL($tag) . '%\'
					GROUP BY product_shop.id_product
				ORDER BY position DESC' . ($orderBy ? ', ' . $orderBy : '') . ($orderWay ? ' ' . $orderWay : '') . '
				LIMIT ' . (int) (($pageNumber - 1) * $pageSize) . ',' . (int) $pageSize;
        if (!$result = Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS($sql, true, false)) {
            return false;
        }

        return Product::getProductsProperties((int) $id_lang, $result);
    }

    /**
     * Prepare a word for the SQL requests (Remove hyphen if present, add percentage signs).
     *
     * @internal Public for tests
     *
     * @param string $word
     *
     * @return string
     */
    public static function getSearchParamFromWord($word)
    {
        $word = str_replace(['%', '_'], ['\\%', '\\_'], $word);
        $start_search = Configuration::get('PS_SEARCH_START') ? '%' : '';
        $end_search = Configuration::get('PS_SEARCH_END') ? '' : '%';
        $psSearchMawWordLenth = self::getMaximumWordLength();
        $start_pos = (int) ($word[0] == '-');

        return $start_search . pSQL(Tools::substr($word, $start_pos, $psSearchMawWordLenth)) . $end_search;
    }

    /**
     * @param Context $context
     * @param string $queryString
     *
     * @return string
     *
     * @throws PrestaShopDatabaseException
     */
    public static function findClosestWeightestWord($context, $queryString)
    {
        $distance = []; // cache levenshtein distance
        $searchMinWordLength = (int) Configuration::get('PS_SEARCH_MINWORDLEN');
        $psSearchMaxWordLength = (int) Configuration::get('PS_SEARCH_MAX_WORD_LENGTH');

        if (!self::$totalWordInSearchWordTable) {
            $sql = 'SELECT count(*) FROM `' . _DB_PREFIX_ . 'search_word`;';
            self::$totalWordInSearchWordTable = (int) Db::getInstance()->getValue($sql);
        }

        /* If the ps_search_word table size is superior to PS_SEARCH_MAX_WORDS_IN_TABLE, that mean that the DB is really huge.
         * To reduce the server load, we are looking only for words with same length that the query word.
         * If we use the auto-acale && self::$totalWordInSearchWordTable > PS_SEARCH_MAX_WORDS_IN_TABLE,
         * we will get $coefMax < 1 following by $coefMax < $coefMin, this is a non-sense.
         * So, we test it before and assign a right value for both target lengths */
        if (self::$totalWordInSearchWordTable > static::PS_SEARCH_MAX_WORDS_IN_TABLE) {
            self::$targetLengthMin = self::$targetLengthMax = (int) (strlen($queryString));
        } else {
            /* This part of code can be considered like an auto-scale mechanism.
            *  The table ps_search_word can grow huge, and exceed server resources.
            *  So, we need a mechanism to reduce the server load depending the DB size.
            *  Here will be calculated ranges of target length depending the ps_search_word table size.
            *  If ps_search_word table size tends to PS_SEARCH_MAX_WORDS_IN_TABLE, $coefMax and $coefMin will tend to 1.
            *  If ps_search_word table size tends to 0, $coefMax will tends to 2, and $coefMin will tends to 0.5.
            *  Computations are made with the linear function y = ax + b.
            *  With actual constant values, we have :
            *  Linear function for $coefMin : a = 0.5 / 100000, b = 0.5
            *  Linear function for $coefMax : a = -1 / 100000, b = 2
            *  Results :
            *  500 words id DB give coefMin : 0.5025, coefMax : 1.995
            *  20,000 words id DB give $coefMin : 0.6, $coefMax : 1.8
            *  40,000 words id DB give $coefMin : 0.7, $coefMax : 1.6
            *  60,000 words id DB give $coefMin : 0.8, $coefMax : 1.4
            *  80,000 words id DB give $coefMin : 0.9, $coefMax : 1.2
            *  100,000 words id DB give $coefMin : 1, $coefMax : 1*/
            if (!self::$coefMin) {
                //self::$coefMin && self::$coefMax depend on the number of total words in ps_search_word table, need to calculate only for every search
                self::$coefMin = (
                    (static::PS_SEARCH_ORDINATE_MIN / static::PS_SEARCH_MAX_WORDS_IN_TABLE)
                    * self::$totalWordInSearchWordTable
                ) + static::PS_SEARCH_ABSCISSA_MIN; //y = ax + b

                self::$coefMax = (
                    (static::PS_SEARCH_ORDINATE_MAX / static::PS_SEARCH_MAX_WORDS_IN_TABLE)
                    * self::$totalWordInSearchWordTable
                ) + static::PS_SEARCH_ABSCISSA_MAX; //y = ax + b
            }
            // self::$targetLengthMin depends of the length of the $queryString, need to calculate for every word
            self::$targetLengthMin = (int) (strlen($queryString) * self::$coefMin);
            self::$targetLengthMax = (int) (strlen($queryString) * self::$coefMax);

            if (self::$targetLengthMin < $searchMinWordLength) {
                self::$targetLengthMin = $searchMinWordLength;
            }
            if (self::$targetLengthMax > $psSearchMaxWordLength) {
                self::$targetLengthMax = $psSearchMaxWordLength;
            }
            // Could happen when $queryString length * $coefMin > $psSearchMaxWordLength
            if (self::$targetLengthMax < self::$targetLengthMin) {
                return '';
            }
        }

        $sql = 'SELECT null as levenshtein, -SUM(weight) as weight, sw.`word` ' .
             'FROM `' . _DB_PREFIX_ . 'search_word` sw ' .
             'LEFT JOIN `' . _DB_PREFIX_ . 'search_index` si ON (sw.`id_word` = si.`id_word`) ' .
             'LEFT JOIN `' . _DB_PREFIX_ . 'product_shop` product_shop ON (product_shop.`id_product` = si.`id_product`) ' .
             'WHERE sw.`id_lang` = ' . (int) $context->language->id . ' ' .
             'AND sw.`id_shop` = ' . (int) $context->shop->id . ' ' .
             'AND LENGTH(sw.`word`) >= ' . self::$targetLengthMin . ' ' .
             'AND LENGTH(sw.`word`) <= ' . self::$targetLengthMax . ' ' .
             'AND product_shop.`active` = 1 ' .
             'AND product_shop.`visibility` IN ("both", "search") ' .
             'AND product_shop.indexed = 1 ' .
             'GROUP BY sw.`word`;';

        $selectedWords = Db::getInstance()->executeS($sql);

        $closestWord = array_reduce(
            $selectedWords,
            static function ($a, $b) use ($queryString) {
                /* The 'null as levenshtein' column is used as cache
                 *  if $b win, next loop, it will be $a. So, no need to assign $a['levenshtein']*/
                $b['levenshtein'] = levenshtein($b['word'], $queryString);

                /* The array comparison will follow the order keys as follow: levenshtein, weight, word
                 * So, were looking for the smaller levenshtein distance, then the smallest weight (-SUM(weight))*/
                return $a < $b ? $a : $b;
            },
            ['word' => 'initial', 'weight' => 0, 'levenshtein' => 100]
        );

        return $closestWord['levenshtein'] < static::PS_DISTANCE_MAX ? $closestWord['word'] : '';
    }

    /**
     * Get the maximum word length value from configuration or default value
     * depending on the activation of the fuzzy search mechanism
     *
     * @return int|string
     */
    public static function getMaximumWordLength()
    {
        if (Configuration::get('PS_SEARCH_FUZZY')) {
            return Configuration::get('PS_SEARCH_MAX_WORD_LENGTH');
        }

        return self::PS_DEFAULT_SEARCH_MAX_WORD_LENGTH;
    }
}
