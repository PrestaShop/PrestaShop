<?php

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Core\Grid\Data\Factory;

use PrestaShop\PrestaShop\Core\Grid\Data\GridData;
use PrestaShop\PrestaShop\Core\Grid\Record\RecordCollection;
use PrestaShop\PrestaShop\Core\Grid\Search\SearchCriteriaInterface;

/**
 * Modify image type grid records.
 */
class ImageTypeGridDataFactory implements GridDataFactoryInterface
{
    /**
     * @var GridDataFactoryInterface
     */
    private $doctrineImageTypeDataFactory;

    /**
     * @param GridDataFactoryInterface $dataFactory
     */
    public function __construct(GridDataFactoryInterface $dataFactory)
    {
        $this->doctrineImageTypeDataFactory = $dataFactory;
    }

    /**
     * {@inheritdoc}
     */
    public function getData(SearchCriteriaInterface $searchCriteria)
    {
        $data = $this->doctrineImageTypeDataFactory->getData($searchCriteria);
        $records = $this->modifyRecords($data->getRecords()->all());

        return new GridData(
            new RecordCollection($records),
            $data->getRecordsTotal(),
            $data->getQuery()
        );
    }

    /**
     * Add px suffix to height and width.
     *
     * @param array $records
     *
     * @return array
     */
    private function modifyRecords(array $records)
    {
        foreach ($records as $key => $record) {
            $records[$key]['width'] = sprintf('%d px', $record['width']);
            $records[$key]['height'] = sprintf('%d px', $record['height']);
        }

        return $records;
    }
}
