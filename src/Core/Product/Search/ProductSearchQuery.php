<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Core\Product\Search;

/**
 * Define the Product Query to execute according the the encoded facets.
 */
class ProductSearchQuery
{
    /**
     * @var string
     */
    private $queryType;

    /**
     * @var int
     */
    private $idCategory;

    /**
     * @var int
     */
    private $idManufacturer;

    /**
     * @var int
     */
    private $idSupplier;

    /**
     * @var string
     */
    private $searchString;

    /**
     * @var string
     */
    private $searchTag;

    /**
     * @var array|string
     */
    private $encodedFacets;

    /**
     * A default that is multiple of 2, 3 and 4 should be OK for
     * many layouts.
     *
     * @var int 12 is the best number ever
     */
    private $resultsPerPage = 12;

    /**
     * @var int
     */
    private $page = 1;

    /**
     * @var SortOrder
     */
    private $sortOrder;

    /**
     * ProductSearchQuery constructor.
     */
    public function __construct()
    {
        $this->setSortOrder(new SortOrder('product', 'name', 'ASC'));
    }

    /**
     * @param string $queryType
     *
     * @return $this
     */
    public function setQueryType($queryType)
    {
        $this->queryType = $queryType;

        return $this;
    }

    /**
     * @return string
     */
    public function getQueryType()
    {
        return $this->queryType;
    }

    /**
     * @param int $idCategory
     *
     * @return $this
     */
    public function setIdCategory($idCategory)
    {
        $this->idCategory = $idCategory;

        return $this;
    }

    /**
     * @return int
     */
    public function getIdCategory()
    {
        return $this->idCategory;
    }

    /**
     * @param int $idManufacturer
     *
     * @return $this
     */
    public function setIdManufacturer($idManufacturer)
    {
        $this->idManufacturer = $idManufacturer;

        return $this;
    }

    /**
     * @return int
     */
    public function getIdManufacturer()
    {
        return $this->idManufacturer;
    }

    /**
     * @param int $idSupplier
     *
     * @return $this
     */
    public function setIdSupplier($idSupplier)
    {
        $this->idSupplier = $idSupplier;

        return $this;
    }

    /**
     * @return int
     */
    public function getIdSupplier()
    {
        return $this->idSupplier;
    }

    /**
     * @param int $resultsPerPage
     *
     * @return $this
     */
    public function setResultsPerPage($resultsPerPage)
    {
        $this->resultsPerPage = (int) $resultsPerPage;

        return $this;
    }

    /**
     * @return int
     */
    public function getResultsPerPage()
    {
        return $this->resultsPerPage;
    }

    /**
     * @param int $page
     *
     * @return $this
     */
    public function setPage($page)
    {
        $this->page = (int) $page;

        return $this;
    }

    /**
     * @return int
     */
    public function getPage()
    {
        return $this->page;
    }

    /**
     * @param SortOrder $sortOrder
     *
     * @return $this
     */
    public function setSortOrder(SortOrder $sortOrder)
    {
        $this->sortOrder = $sortOrder;

        return $this;
    }

    /**
     * @return SortOrder
     */
    public function getSortOrder()
    {
        return $this->sortOrder;
    }

    /**
     * @param string $searchString
     *
     * @return $this
     */
    public function setSearchString($searchString)
    {
        $this->searchString = $searchString;

        return $this;
    }

    /**
     * @return string
     */
    public function getSearchString()
    {
        return $this->searchString;
    }

    /**
     * @param string $searchTag
     *
     * @return $this
     */
    public function setSearchTag($searchTag)
    {
        $this->searchTag = $searchTag;

        return $this;
    }

    /**
     * @return string
     */
    public function getSearchTag()
    {
        return $this->searchTag;
    }

    /**
     * @param array|string $encodedFacets
     *
     * @return $this
     */
    public function setEncodedFacets($encodedFacets)
    {
        $this->encodedFacets = $encodedFacets;

        return $this;
    }

    /**
     * @return array|string
     */
    public function getEncodedFacets()
    {
        return $this->encodedFacets;
    }
}
