<?php

namespace PrestaShop\PrestaShop\Core\Domain\Product\Query;

use PrestaShop\PrestaShop\Core\Grid\Search\SearchCriteriaInterface;

/**
 * Gets products exportable data.
 */
class GetProductExportableData
{
    /**
     * @var SearchCriteriaInterface
     */
    private $searchCriteria;

    /**
     * @param SearchCriteriaInterface $searchCriteria
     */
    public function __construct(SearchCriteriaInterface $searchCriteria)
    {
        $this->searchCriteria = $searchCriteria;
    }

    /**
     * @return SearchCriteriaInterface
     */
    public function getSearchCriteria()
    {
        return $this->searchCriteria;
    }
}
