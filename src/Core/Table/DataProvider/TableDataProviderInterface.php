<?php

namespace PrestaShop\PrestaShop\Core\Table\DataProvider;

/**
 * Interface TableDataProviderInterface defines contract for table data providers
 */
interface TableDataProviderInterface
{
    /**
     * Get filtered & paginated rows from any data source (database, API or any other)
     *
     * @param array $filters
     *
     * @return array
     */
    public function getRows(array $filters);

    /**
     * Get total rows count in data source
     *
     * @return int
     */
    public function getRowsTotal();
}
