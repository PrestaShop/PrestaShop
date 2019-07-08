<?php

namespace PrestaShop\PrestaShop\Core\Domain\Product\Query;

/**
 * Gets products exportable data.
 */
class GetProductExportableData
{
    /**
     * @var array
     */
    private $columns;

    /**
     * @var array
     */
    private $records;

    /**
     * @param array $columns
     * @param array $records
     */
    public function __construct(
        array $columns,
        array $records
    ) {
        $this->columns = $columns;
        $this->records = $records;
    }

    /**
     * @return array
     */
    public function getColumns()
    {
        return $this->columns;
    }

    /**
     * @return array
     */
    public function getRecords()
    {
        return $this->records;
    }
}
