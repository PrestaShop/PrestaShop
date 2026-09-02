<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Core\Grid\Data\Factory;

use PrestaShop\PrestaShop\Core\Grid\Data\GridData;
use PrestaShop\PrestaShop\Core\Grid\Record\RecordCollection;
use PrestaShop\PrestaShop\Core\Grid\Search\SearchCriteriaInterface;

/**
 * Decorates database records for grid presentation
 */
final class AttributeGroupGridDataFactory implements GridDataFactoryInterface
{
    /**
     * @var GridDataFactoryInterface
     */
    private $attributeDataFactory;

    /**
     * @param GridDataFactoryInterface $attributeDataFactory
     */
    public function __construct(GridDataFactoryInterface $attributeDataFactory)
    {
        $this->attributeDataFactory = $attributeDataFactory;
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
            if (null === $record['values']) {
                $record['values'] = 0;
            }
        }

        return $records;
    }
}
