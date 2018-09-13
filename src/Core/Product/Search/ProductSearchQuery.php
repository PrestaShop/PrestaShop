<?php
/**
 * 2007-2018 PrestaShop.
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
 * needs please refer to http://www.prestashop.com for more information.
 *
 * @author    PrestaShop SA <contact@prestashop.com>
 * @copyright 2007-2018 PrestaShop SA
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 * International Registered Trademark & Property of PrestaShop SA
 */

namespace PrestaShop\PrestaShop\Core\Product\Search;

/**
 * Define the Product Query to execute according the the encoded facets.
 */
class ProductSearchQuery
{
    /**
     * @var string The Product Search query type.
     */
    private $queryType;

    /**
     * @var int The Product Search Category id.
     */
    private $idCategory;

    /**
     * @var int The Product Search Manufacturer id.
     */
    private $idManufacturer;

    /**
     * @var int The Product Search Supplier id.
     */
    private $idSupplier;

    /**
     * @var string The Product Search search string.
     */
    private $searchString;

    /**
     * @var string The Product Search search tag.
     */
    private $searchTag;

    /**
     * At this time, this concept is not used in Core.
     * @deprecated since 1.7.5, to be removed in 1.8.
     * @var array The Product Search encoded facets.
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
     * @var int The Product Search page index.
     */
    private $page = 1;

    /**
     * @var SortOrder The Product Search Sort order.
     */
    private $sortOrder;

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
     * @return string Returns the Query type
     */
    public function getQueryType()
    {
        return $this->queryType;
    }

    /**
     * @param int $idCategory the Category id
     *
     * @return $this
     */
    public function setIdCategory($idCategory)
    {
        $this->idCategory = $idCategory;

        return $this;
    }

    /**
     * @return int Returns the Category id
     */
    public function getIdCategory()
    {
        return $this->idCategory;
    }

    /**
     * @param int $idManufacturer the Manufacturer id
     *
     * @return $this
     */
    public function setIdManufacturer($idManufacturer)
    {
        $this->idManufacturer = $idManufacturer;

        return $this;
    }

    /**
     * @return int Returns the Manufacturer id
     */
    public function getIdManufacturer()
    {
        return $this->idManufacturer;
    }

    /**
     * @param int $idSupplier the Supplier id
     *
     * @return $this
     */
    public function setIdSupplier($idSupplier)
    {
        $this->idSupplier = $idSupplier;

        return $this;
    }

    /**
     * @return int Returns the Supplier id
     */
    public function getIdSupplier()
    {
        return $this->idSupplier;
    }

    /**
     * @param int $resultsPerPage the number of results per page
     *
     * @return $this
     */
    public function setResultsPerPage($resultsPerPage)
    {
        $this->resultsPerPage = $resultsPerPage;

        return $this;
    }

    /**
     * @return int Returns the number of results per page
     */
    public function getResultsPerPage()
    {
        return $this->resultsPerPage;
    }

    /**
     * @param int $page the page index
     *
     * @return $this
     */
    public function setPage($page)
    {
        $this->page = $page;

        return $this;
    }

    /**
     * @return int Returns the page index
     */
    public function getPage()
    {
        return $this->page;
    }

    /**
     * @param SortOrder $sortOrder the Sort order
     *
     * @return $this
     */
    public function setSortOrder(SortOrder $sortOrder)
    {
        $this->sortOrder = $sortOrder;

        return $this;
    }

    /**
     * @return SortOrder Returns the Sort Order
     */
    public function getSortOrder()
    {
        return $this->sortOrder;
    }

    /**
     * @param string $searchString the search string
     *
     * @return $this
     */
    public function setSearchString($searchString)
    {
        $this->searchString = $searchString;

        return $this;
    }

    /**
     * @return string Returns the search string
     */
    public function getSearchString()
    {
        return $this->searchString;
    }

    /**
     * @param string $searchTag the search tag
     *
     * @return $this
     */
    public function setSearchTag($searchTag)
    {
        $this->searchTag = $searchTag;

        return $this;
    }

    /**
     * @return string Returns the search tag
     */
    public function getSearchTag()
    {
        return $this->searchTag;
    }

    /**
     * @param array $encodedFacets
     *
     * @return $this
     */
    public function setEncodedFacets($encodedFacets)
    {
        $this->encodedFacets = $encodedFacets;

        return $this;
    }

    /**
     * @return array
     */
    public function getEncodedFacets()
    {
        return $this->encodedFacets;
    }
}
