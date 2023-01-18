<?php

namespace PrestaShop\PrestaShop\Core\Grid\Data\Factory;

use PrestaShop\PrestaShop\Core\Grid\Data\GridData;
use PrestaShop\PrestaShop\Core\Grid\Record\RecordCollection;
use PrestaShop\PrestaShop\Core\Grid\Search\SearchCriteriaInterface;

class CustomerGroupsDataFactory implements GridDataFactoryInterface
{
    /**
     * @var GridDataFactoryInterface
     */
    private $doctrineCustomerGroupDataFactory;

    /**
     * @param GridDataFactoryInterface $doctrineCustomerGroupDataFactory
     */
    public function __construct(
        GridDataFactoryInterface $doctrineCustomerGroupDataFactory
    ) {
        $this->doctrineCustomerGroupDataFactory = $doctrineCustomerGroupDataFactory;
    }

    public function getData(SearchCriteriaInterface $searchCriteria)
    {
        $customerGroupsData = $this->doctrineCustomerGroupDataFactory->getData($searchCriteria);

        return new GridData(
            new RecordCollection($customerGroupsData->getRecords()->all()),
            $customerGroupsData->getRecordsTotal(),
            $customerGroupsData->getQuery()
        );
    }
}
