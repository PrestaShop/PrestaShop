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
