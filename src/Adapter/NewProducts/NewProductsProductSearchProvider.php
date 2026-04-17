<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Adapter\NewProducts;

use PrestaShop\PrestaShop\Core\Product\Search\ProductSearchContext;
use PrestaShop\PrestaShop\Core\Product\Search\ProductSearchProviderInterface;
use PrestaShop\PrestaShop\Core\Product\Search\ProductSearchQuery;
use PrestaShop\PrestaShop\Core\Product\Search\ProductSearchResult;
use PrestaShop\PrestaShop\Core\Product\Search\SortOrder;
use PrestaShop\PrestaShop\Core\Product\Search\SortOrdersCollection;
use Product;
use Symfony\Contracts\Translation\TranslatorInterface;

/**
 * Used to query the latest products, see NewProductsController in Front Office.
 */
class NewProductsProductSearchProvider implements ProductSearchProviderInterface
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
     * @param string $type
     *
     * @return array|int
     */
    private function getProductsOrCount(
        ProductSearchContext $context,
        ProductSearchQuery $query,
        $type = 'products'
    ) {
        $isTypeProducts = $type === 'products';
        $result = Product::getNewProducts(
            $context->getIdLang(),
            $query->getPage(),
            $query->getResultsPerPage(),
            !$isTypeProducts,
            $query->getSortOrder()->toLegacyOrderBy(),
            $query->getSortOrder()->toLegacyOrderWay()
        );

        return $isTypeProducts ? $result : (int) $result;
    }

    /**
     * {@inheritdoc}
     */
    public function runQuery(
        ProductSearchContext $context,
        ProductSearchQuery $query
    ) {
        if (!$products = $this->getProductsOrCount($context, $query, 'products')) {
            $products = [];
        }
        $count = $this->getProductsOrCount($context, $query, 'count');

        $result = new ProductSearchResult();

        if (!empty($products)) {
            $result
                ->setProducts($products)
                ->setTotalProductsCount($count);

            // We use default set of sort orders + option to sort by date
            $result->setAvailableSortOrders(
                array_merge(
                    [
                        (new SortOrder('product', 'date_add', 'desc'))->setLabel(
                            $this->translator->trans('Date added, newest to oldest', [], 'Shop.Theme.Catalog')
                        ),
                        (new SortOrder('product', 'date_add', 'asc'))->setLabel(
                            $this->translator->trans('Date added, oldest to newest', [], 'Shop.Theme.Catalog')
                        ),
                    ],
                    $this->sortOrdersCollection->getDefaults())
            );
        }

        return $result;
    }
}
