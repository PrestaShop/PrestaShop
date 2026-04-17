<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Adapter\BestSales;

use PrestaShop\PrestaShop\Core\Product\Search\ProductSearchContext;
use PrestaShop\PrestaShop\Core\Product\Search\ProductSearchProviderInterface;
use PrestaShop\PrestaShop\Core\Product\Search\ProductSearchQuery;
use PrestaShop\PrestaShop\Core\Product\Search\ProductSearchResult;
use PrestaShop\PrestaShop\Core\Product\Search\SortOrder;
use PrestaShop\PrestaShop\Core\Product\Search\SortOrdersCollection;
use ProductSale;
use Symfony\Contracts\Translation\TranslatorInterface;

class BestSalesProductSearchProvider implements ProductSearchProviderInterface
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
     * @param ProductSearchContext $context
     * @param ProductSearchQuery $query
     *
     * @return ProductSearchResult
     */
    public function runQuery(
        ProductSearchContext $context,
        ProductSearchQuery $query
    ) {
        // If provided sort order is unsupported random, we set a fallback
        if ($query->getSortOrder()->isRandom()) {
            $query->setSortOrder((new SortOrder('product', 'sales', 'desc'))->setLabel(
                $this->translator->trans('Sales, highest to lowest', [], 'Shop.Theme.Catalog')
            ));
        }

        if (!$products = ProductSale::getBestSales(
            $context->getIdLang(),
            $query->getPage(),
            $query->getResultsPerPage(),
            $query->getSortOrder()->toLegacyOrderBy(),
            $query->getSortOrder()->toLegacyOrderWay()
        )) {
            $products = [];
        }

        $count = (int) ProductSale::getNbSales();

        $result = new ProductSearchResult();

        if (!empty($products)) {
            $result
                ->setProducts($products)
                ->setTotalProductsCount($count);

            // We use default set of sort orders + option to sort by sales
            $result->setAvailableSortOrders(
                array_merge(
                    [
                        (new SortOrder('product', 'sales', 'desc'))->setLabel(
                            $this->translator->trans('Sales, highest to lowest', [], 'Shop.Theme.Catalog')
                        ),
                    ],
                    $this->sortOrdersCollection->getDefaults())
            );
        }

        return $result;
    }
}
