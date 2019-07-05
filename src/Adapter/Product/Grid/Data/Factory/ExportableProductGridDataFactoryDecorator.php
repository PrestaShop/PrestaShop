<?php

namespace PrestaShop\PrestaShop\Adapter\Product\Grid\Data\Factory;

use PrestaShop\PrestaShop\Core\Grid\Data\Factory\GridDataFactoryInterface;
use PrestaShop\PrestaShop\Core\Grid\Search\SearchCriteriaInterface;

/**
 * Returns grid data for export content.
 */
final class ExportableProductGridDataFactoryDecorator implements GridDataFactoryInterface
{
    /**
     * {@inheritdoc}
     */
    public function getData(SearchCriteriaInterface $searchCriteria)
    {
        // TODO: Implement getData() method.
    }
}
