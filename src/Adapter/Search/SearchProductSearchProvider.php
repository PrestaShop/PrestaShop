<?php
/**
 * 2007-2019 PrestaShop and Contributors
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
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
 * needs please refer to https://www.prestashop.com for more information.
 *
 * @author    PrestaShop SA <contact@prestashop.com>
 * @copyright 2007-2019 PrestaShop SA and Contributors
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 * International Registered Trademark & Property of PrestaShop SA
 */

namespace PrestaShop\PrestaShop\Adapter\Search;

use Hook;
use PrestaShop\PrestaShop\Core\Product\Search\ProductSearchContext;
use PrestaShop\PrestaShop\Core\Product\Search\ProductSearchProviderInterface;
use PrestaShop\PrestaShop\Core\Product\Search\ProductSearchQuery;
use PrestaShop\PrestaShop\Core\Product\Search\ProductSearchResult;
use PrestaShop\PrestaShop\Core\Product\Search\SortOrderFactory;
use Search;
use Symfony\Component\Translation\TranslatorInterface;
use Tools;
use Db;
use Configuration;

/**
 * Class responsible of retrieving products in Search page of Front Office.
 *
 * @see SearchController
 */
class SearchProductSearchProvider implements ProductSearchProviderInterface
{
    /**
     * @var TranslatorInterface
     */
    private $translator;

    /**
     * @var SortOrderFactory
     */
    private $sortOrderFactory;

    /**
     * @const Limit of length word we want to compare in findClosestWeightestWords()
     */
    const LENGHTWORDCOEFMIN = 0.7;
    const LENGHTWORDCOEFMAX = 1.5;

    public function __construct(
        TranslatorInterface $translator
    ) {
        $this->translator = $translator;
        $this->sortOrderFactory = new SortOrderFactory($this->translator);
    }

    /**
     * {@inheritdoc}
     */
    public function runQuery(
        ProductSearchContext $context,
        ProductSearchQuery $query
    ) {
        $products = [];
        $count = 0;

        if (($string = $query->getSearchString())) {
            $queryString = Tools::replaceAccentedChars(urldecode($string));

            $result = Search::find(
                $context->getIdLang(),
                $queryString,
                $query->getPage(),
                $query->getResultsPerPage(),
                $query->getSortOrder()->toLegacyOrderBy(),
                $query->getSortOrder()->toLegacyOrderWay(),
                false, // ajax, what's the link?
                false, // $use_cookie, ignored anyway
                null
            );

            $count = $result['total'];

            if (!$count) {
                $result = Search::find(
                    $context->getIdLang(),
                    self::findClosestWeightestWords($context, $queryString),
                    $query->getPage(),
                    $query->getResultsPerPage(),
                    $query->getSortOrder()->toLegacyOrderBy(),
                    $query->getSortOrder()->toLegacyOrderWay(),
                    false, // ajax, what's the link?
                    false, // $use_cookie, ignored anyway
                    null
                );
            }

            $products = $result['result'];
            $count = $result['total'];

            Hook::exec('actionSearch', array(
                'searched_query' => $queryString,
                'total' => $count,

                // deprecated since 1.7.x
                'expr' => $queryString,
            ));
        } elseif (($tag = $query->getSearchTag())) {
            $queryString = urldecode($tag);

            $products = Search::searchTag(
                $context->getIdLang(),
                $queryString,
                false,
                $query->getPage(),
                $query->getResultsPerPage(),
                $query->getSortOrder()->toLegacyOrderBy(true),
                $query->getSortOrder()->toLegacyOrderWay(),
                false,
                null
            );

            $count = Search::searchTag(
                $context->getIdLang(),
                $queryString,
                true,
                $query->getPage(),
                $query->getResultsPerPage(),
                $query->getSortOrder()->toLegacyOrderBy(true),
                $query->getSortOrder()->toLegacyOrderWay(),
                false,
                null
            );

            Hook::exec('actionSearch', array(
                'searched_query' => $queryString,
                'total' => $count,

                // deprecated since 1.7.x
                'expr' => $queryString,
            ));
        }

        $result = new ProductSearchResult();

        if (!empty($products)) {
            $result
                ->setProducts($products)
                ->setTotalProductsCount($count);

            $result->setAvailableSortOrders(
                $this->sortOrderFactory->getDefaultSortOrders()
            );
        }

        return $result;
    }

    /**
     * @param ProductSearchContext $context
     * @param $queryString
     *
     * @return string
     */
    public static function findClosestWeightestWords(ProductSearchContext $context, $queryString)
    {
        $distance = array(); // cache levenshtein distance
        $closestWords = [];
        $searchMinWordLength = (int) Configuration::get('PS_SEARCH_MINWORDLEN');
        $queries = explode(' ', Search::sanitize($queryString, (int) $context->getIdLang(), false));

        foreach ($queries as $query) {
            if (strlen($query) < $searchMinWordLength) {
                continue;
            }

            $targetLenghtMin = (int) (strlen($query) * self::LENGHTWORDCOEFMIN);
            $targetLenghtMax = (int) (strlen($query) * self::LENGHTWORDCOEFMAX);

            if ($targetLenghtMin < $searchMinWordLength) {
                $targetLenghtMin = $searchMinWordLength;
            }

            $sql = 'SELECT sw.`word`, SUM(weight) as weight
                    FROM `' . _DB_PREFIX_ . 'search_word` sw
                    LEFT JOIN `' . _DB_PREFIX_ . 'search_index` si ON (sw.`id_word` = si.`id_word`)
                    WHERE sw.`id_lang` = ' . (int) $context->getIdLang() . '
                    AND sw.`id_shop` = ' . (int) $context->getIdShop() . '
                    AND LENGTH(sw.`word`) > ' . $targetLenghtMin . '
                    AND LENGTH(sw.`word`) < ' . $targetLenghtMax . '
                    GROUP BY sw.`word`;';

            $selectedWords = Db::getInstance()->executeS($sql);
            $closestWords []= array_reduce($selectedWords, function ($a, $b) use ($query, &$distance /* Cache */) {
                if (!isset($distance[$a['word']])) {
                    $distance[$a['word']] = levenshtein($a['word'], $query);
                }

                if (!isset($distance[$b['word']])) {
                    $distance[$b['word']] = levenshtein($b['word'], $query);
                }

                if ($distance[$a['word']] != $distance[$b['word']]) {
                    return $distance[$a['word']] < $distance[$b['word']] ? $a : $b;
                }
                return $a['weight'] > $b['weight'] ? $a : $b;

            }, array('word' => 'initial', 'weight' => '0'))['word'];

            unset($distance);
        }

        return implode(' ', $closestWords);
    }
}
