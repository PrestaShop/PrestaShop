<?php

namespace PrestaShop\PrestaShop\Core\Domain\Product\QueryResult;

/**
 * Holds product exportable data - used for csv.
 */
class ProductExportableData
{
    /**
     * @var array
     */
    private $headers;

    /**
     * @var array
     */
    private $data;

    /**
     * @param array $headers
     * @param array $data
     */
    public function __construct(array $headers, array $data)
    {
        $this->headers = $headers;
        $this->data = $data;
    }

    /**
     * @return array
     */
    public function getHeaders()
    {
        return $this->headers;
    }

    /**
     * @return array
     */
    public function getData()
    {
        return $this->data;
    }
}
