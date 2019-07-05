<?php

namespace PrestaShop\PrestaShop\Core\Domain\Product\QueryHandler;

use PrestaShop\PrestaShop\Core\Domain\Product\Query\GetProductExportableData;
use PrestaShop\PrestaShop\Core\Domain\Product\QueryResult\ProductExportableData;

/**
 * Defines contract for GetProductExportableDataHandler.
 */
interface GetProductExportableDataHandlerInterface
{
    /**
     * @param GetProductExportableData $query
     *
     * @return ProductExportableData
     */
    public function handle(GetProductExportableData $query);
}
