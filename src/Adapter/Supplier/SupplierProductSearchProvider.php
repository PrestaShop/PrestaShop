<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Adapter\Supplier;

use PrestaShop\PrestaShop\Core\Product\Search\ProductSearchContext;
use PrestaShop\PrestaShop\Core\Product\Search\ProductSearchProviderInterface;
use PrestaShop\PrestaShop\Core\Product\Search\ProductSearchQuery;
use PrestaShop\PrestaShop\Core\Product\Search\ProductSearchResult;
use PrestaShop\PrestaShop\Core\Product\Search\SortOrdersCollection;
use Supplier;
use Symfony\Contracts\Translation\TranslatorInterface;

/**
 * Class responsible of retrieving products in Suppliers page of Front Office.
 *
 * @see SupplierController
 */
class SupplierProductSearchProvider implements ProductSearchProviderInterface
{
    /**
     * @var TranslatorInterface
     */
    private $translator;

    /**
     * @var Supplier
     */
    private $supplier;

    /**
     * @var SortOrdersCollection
     */
    private $sortOrdersCollection;

    public function __construct(
        TranslatorInterface $translator,
        Supplier $supplier
    ) {
        $this->translator = $translator;
        $this->supplier = $supplier;
        $this->sortOrdersCollection = new SortOrdersCollection($this->translator);
    }

    /**
     * @param ProductSearchContext $context
     * @param ProductSearchQuery $query
     * @param string $type
     *
     * @return int|array
     */
    private function getProductsOrCount(
        ProductSearchContext $context,
        ProductSearchQuery $query,
        $type = 'products'
    ) {
        $isTypeProducts = $type === 'products';

        $result = $this->supplier->getProducts(
            $this->supplier->id,
            $context->getIdLang(),
            $query->getPage(),
            $query->getResultsPerPage(),
            $query->getSortOrder()->toLegacyOrderBy(),
            $query->getSortOrder()->toLegacyOrderWay(),
            !$isTypeProducts
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
        $products = $this->getProductsOrCount($context, $query, 'products');
        $count = $this->getProductsOrCount($context, $query, 'count');

        $result = new ProductSearchResult();

        if (!empty($products)) {
            $result
                ->setProducts($products)
                ->setTotalProductsCount($count);

            // We use only default set of sort orders
            $result->setAvailableSortOrders(
                $this->sortOrdersCollection->getDefaults()
            );
        }

        return $result;
    }
}
