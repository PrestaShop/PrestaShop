<?php

namespace PrestaShop\PrestaShop\Core\Domain\Product\QueryHandler;

use Generator;
use PrestaShop\PrestaShop\Core\Domain\Product\Query\GetProductExportableData;
use PrestaShop\PrestaShop\Core\Domain\Product\QueryResult\ProductExportableData;
use PrestaShop\PrestaShop\Core\Grid\Column\ColumnCollectionInterface;
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

        list($headers, $headerRowPosition) = $this->getHeaders($columns);

        $data = $this->getData($productGrid->getData()->getRecords()->all(), $headerRowPosition);

        return new ProductExportableData(
            $headers,
            $data
        );
    }

    /**
     * Collects actual headers with translatable names as they will be used as csv column.
     * Collects header key positions so the data can be assigned for the right column in later on processing
     *
     * @param ColumnCollectionInterface $columns
     *
     * @return array
     */
    private function getHeaders(ColumnCollectionInterface $columns)
    {
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

        return [$headers, $headerRowPosition];
    }

    /**
     * Gets actual data that will be represented - using header row positions it determines the place where
     * the data should be inserted.
     *
     * @param array $records
     * @param array $headerRowPosition
     *
     * @return array
     */
    private function getData(array $records, array $headerRowPosition)
    {
        $data = [];
        $dataIteration = 0;

        foreach ($records as $columnRecord) {
            foreach ($this->getRecord($columnRecord, $headerRowPosition) as list($position, $columnValue)) {
                $data[$dataIteration][$position] = $columnValue;
            }

            ksort($data[$dataIteration]);
            $dataIteration++;
        }

        return $data;
    }

    /**
     * Gets modified record.
     *
     * @param array $columnRecord
     * @param array $headerRowPosition
     *
     * @return Generator
     */
    private function getRecord(array $columnRecord, array $headerRowPosition)
    {
        foreach ($columnRecord as $columnId => $columnValue) {
            if (isset($headerRowPosition[$columnId])) {
                $position = $headerRowPosition[$columnId];
                yield [$position, $columnValue];
            }
        }
    }
}
