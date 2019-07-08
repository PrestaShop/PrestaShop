<?php

namespace PrestaShop\PrestaShop\Core\Domain\Product\QueryHandler;

use Generator;
use PrestaShop\PrestaShop\Core\Domain\Product\Query\GetProductExportableData;
use PrestaShop\PrestaShop\Core\Domain\Product\QueryResult\ProductExportableData;
use PrestaShop\PrestaShop\Core\Grid\Column\ColumnInterface;

/**
 * Gets products exportable data.
 */
final class GetProductExportableDataHandler implements GetProductExportableDataHandlerInterface
{
    /**
     * todo: quite universal export logic - maybe we should create a service for csv export data retrieval?
     * {@inheritdoc}
     */
    public function handle(GetProductExportableData $query)
    {
        list($headers, $headerRowPosition) = $this->getHeaders($query->getColumns());

        $data = $this->getData($query->getRecords(), $headerRowPosition);

        return new ProductExportableData(
            $headers,
            $data
        );
    }

    /**
     * Collects actual headers with translatable names as they will be used as csv column.
     * Collects header key positions so the data can be assigned for the right column in later on processing
     *
     * @param array $columns
     *
     * @return array
     */
    private function getHeaders(array $columns)
    {
        $headers = [];
        $headerRowPosition = [];
        $excludedColumns = ['bulk', 'actions'];
        $headerIteration = 0;

        /**
         * @var string $columnId
         * @var ColumnInterface $column
         */
        foreach ($columns as $column) {
            if (in_array($column['id'], $excludedColumns, true)) {
                continue;
            }

            $headers[$column['id']] = $column['name'];
            $headerRowPosition[$column['id']] = $headerIteration;

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
            $data[$dataIteration] = $this->getRecord($columnRecord, $headerRowPosition);

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
        $data = [];
        foreach ($columnRecord as $columnId => $columnValue) {
            if (isset($headerRowPosition[$columnId])) {
                $position = $headerRowPosition[$columnId];
                $data[$position] = $columnValue;
            }
        }

        return $data;
    }
}
