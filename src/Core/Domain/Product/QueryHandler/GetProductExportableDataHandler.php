<?php

namespace PrestaShop\PrestaShop\Core\Domain\Product\QueryHandler;

use PrestaShop\PrestaShop\Core\Domain\Product\Query\GetProductExportableData;
use PrestaShop\PrestaShop\Core\Domain\Product\QueryResult\ProductExportableData;
use PrestaShop\PrestaShop\Core\Grid\Column\ColumnInterface;
use PrestaShop\PrestaShop\Core\Grid\GridFactoryInterface;

/**
 * Gets products exportable data.
 */
final class GetProductExportableDataHandler implements GetProductExportableDataHandlerInterface
{
    /**
     * @var GridFactoryInterface
     */
    private $productGridFactory;

    /**
     * @param GridFactoryInterface $productGridFactory
     */
    public function __construct(GridFactoryInterface $productGridFactory)
    {
        $this->productGridFactory = $productGridFactory;
    }

    /**
     * todo: test with position
     * todo: quite universal export logic - maybe we should create a service for csv export data retrieval?
     *
     * {@inheritdoc}
     */
    public function handle(GetProductExportableData $query)
    {
        $productGrid = $this->productGridFactory->getGrid($query->getSearchCriteria());

        $columns = $productGrid->getDefinition()->getColumns();

        $headers = [];
        $headerRowPosition = [];

        $excludedColumns = ['bulk', 'actions'];

        $headerIteration = 0;
        /**
         * @var string $columnId
         * @var ColumnInterface $column
         */
        foreach ($columns as $columnId => $column) {
            if (in_array($columnId, $excludedColumns, true)) {
                continue;
            }

            $headers[$columnId] = $column->getName();
            $headerRowPosition[$columnId] = $headerIteration;

            $headerIteration++;
        }

        $data = [];
        $dataIteration = 0;

        /** @var array $record */
        foreach ($productGrid->getData()->getRecords()->all() as $record) {
            foreach ($record as $columnId => $columnValue) {
                if (isset($headerRowPosition[$columnId])) {
                    $position = $headerRowPosition[$columnId];
                    $data[$dataIteration][$position] = $columnValue;
                }
            }

            ksort($data[$dataIteration]);

            $dataIteration++;
        }

        return new ProductExportableData(
            $headers,
            $data
        );
    }
}
