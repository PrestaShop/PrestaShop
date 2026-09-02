<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Adapter\Search;

use Hook;
use PrestaShop\PrestaShop\Core\Product\Search\ProductSearchContext;
use PrestaShop\PrestaShop\Core\Product\Search\ProductSearchProviderInterface;
use PrestaShop\PrestaShop\Core\Product\Search\ProductSearchQuery;
use PrestaShop\PrestaShop\Core\Product\Search\ProductSearchResult;
use PrestaShop\PrestaShop\Core\Product\Search\SortOrder;
use PrestaShop\PrestaShop\Core\Product\Search\SortOrdersCollection;
use Search;
use Symfony\Contracts\Translation\TranslatorInterface;
use Tools;

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
     * @var SortOrdersCollection
     */
    private $sortOrdersCollection;

    public function __construct(
        TranslatorInterface $translator
    ) {
        $this->translator = $translator;
        $this->sortOrdersCollection = new SortOrdersCollection($this->translator);
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

        if ($string = $query->getSearchString()) {
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
            $products = $result['result'];
            $count = $result['total'];

            Hook::exec('actionSearch', [
                'searched_query' => $queryString,
                'total' => $count,

                // deprecated since 1.7.x
                'expr' => $queryString,
            ]);
        } elseif ($tag = $query->getSearchTag()) {
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

            Hook::exec('actionSearch', [
                'searched_query' => $queryString,
                'total' => $count,

                // deprecated since 1.7.x
                'expr' => $queryString,
            ]);
        }

        $result = new ProductSearchResult();

        if (!empty($products)) {
            $result
                ->setProducts($products)
                ->setTotalProductsCount($count);

            // We use default set of sort orders + option to sort by position (relevance), which makes sense only here and on category page
            $result->setAvailableSortOrders(
                array_merge(
                    [
                        (new SortOrder('product', 'position', 'desc'))->setLabel(
                            $this->translator->trans('Relevance', [], 'Shop.Theme.Catalog')
                        ),
                    ],
                    $this->sortOrdersCollection->getDefaults())
            );
        }

        return $result;
    }
}
