<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Core\Product\Search;

class ProductSearchResult
{
    /**
     * @var array
     */
    private $products = [];
    /**
     * @var int
     */
    private $totalProductsCount;
    /**
     * @var FacetCollection|null
     */
    private $facetCollection;
    /**
     * @var string
     */
    private $encodedFacets;
    /**
     * @var SortOrder[]
     */
    private $availableSortOrders = [];
    /**
     * @var SortOrder
     */
    private $currentSortOrder;

    /**
     * @param array $products
     *
     * @return $this
     */
    public function setProducts(array $products)
    {
        $this->products = $products;

        return $this;
    }

    /**
     * @return array
     */
    public function getProducts()
    {
        return $this->products;
    }

    /**
     * @param int $totalProductsCount
     *
     * @return $this
     */
    public function setTotalProductsCount($totalProductsCount)
    {
        $this->totalProductsCount = $totalProductsCount;

        return $this;
    }

    /**
     * @return int
     */
    public function getTotalProductsCount()
    {
        return $this->totalProductsCount;
    }

    /**
     * @param FacetCollection $facetCollection
     *
     * @return $this
     */
    public function setFacetCollection(FacetCollection $facetCollection)
    {
        $this->facetCollection = $facetCollection;

        return $this;
    }

    /**
     * @return FacetCollection|null
     */
    public function getFacetCollection()
    {
        return $this->facetCollection;
    }

    /**
     * @param string $encodedFacets
     *
     * @return $this
     */
    public function setEncodedFacets($encodedFacets)
    {
        $this->encodedFacets = $encodedFacets;

        return $this;
    }

    /**
     * @return string
     */
    public function getEncodedFacets()
    {
        return $this->encodedFacets;
    }

    /**
     * @param SortOrder $sortOrder
     *
     * @return $this
     */
    public function addAvailableSortOrder(SortOrder $sortOrder)
    {
        $this->availableSortOrders[] = $sortOrder;

        return $this;
    }

    /**
     * @return array
     */
    public function getAvailableSortOrders()
    {
        return $this->availableSortOrders;
    }

    /**
     * @param array $sortOrders
     *
     * @return $this
     */
    public function setAvailableSortOrders(array $sortOrders)
    {
        $this->availableSortOrders = [];

        foreach ($sortOrders as $sortOrder) {
            $this->addAvailableSortOrder($sortOrder);
        }

        return $this;
    }

    /**
     * @param SortOrder $currentSortOrder
     *
     * @return $this
     */
    public function setCurrentSortOrder(SortOrder $currentSortOrder)
    {
        $this->currentSortOrder = $currentSortOrder;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getCurrentSortOrder()
    {
        return $this->currentSortOrder;
    }
}
