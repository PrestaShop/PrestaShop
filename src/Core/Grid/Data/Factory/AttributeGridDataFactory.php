<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Core\Grid\Data\Factory;

use PrestaShop\PrestaShop\Adapter\Shop\Url\ImageFolderProvider;
use PrestaShop\PrestaShop\Core\Grid\Data\GridData;
use PrestaShop\PrestaShop\Core\Grid\Record\RecordCollection;
use PrestaShop\PrestaShop\Core\Grid\Search\SearchCriteriaInterface;

/**
 * Decorates database records for grid presentation
 */
final class AttributeGridDataFactory implements GridDataFactoryInterface
{
    /**
     * @param GridDataFactoryInterface $attributeDataFactory
     */
    public function __construct(
        private readonly GridDataFactoryInterface $attributeDataFactory,
        private readonly ImageFolderProvider $imageFolderProvider
    ) {
    }

    /**
     * {@inheritdoc}
     */
    public function getData(SearchCriteriaInterface $searchCriteria)
    {
        $records = $this->attributeDataFactory->getData($searchCriteria);
        $modifiedRecords = $this->modifyRecords($records->getRecords()->all());

        return new GridData(
            new RecordCollection($modifiedRecords),
            $records->getRecordsTotal(),
            $records->getQuery()
        );
    }

    /**
     * @param array $records
     *
     * @return array
     */
    private function modifyRecords(array $records): array
    {
        foreach ($records as &$record) {
            if (file_exists(_PS_IMG_DIR_ . 'co/' . (int) $record['id_attribute'] . '.jpg')) {
                $record['texture'] = $this->imageFolderProvider->getUrl() . '/' . (int) $record['id_attribute'] . '.jpg';
            }
        }

        return $records;
    }
}
