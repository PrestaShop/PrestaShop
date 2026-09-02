<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Core\Grid\Data\Factory;

use PrestaShop\PrestaShop\Core\Grid\Data\GridData;
use PrestaShop\PrestaShop\Core\Grid\Record\RecordCollection;
use PrestaShop\PrestaShop\Core\Grid\Search\SearchCriteriaInterface;

final class ZoneGridDataFactory implements GridDataFactoryInterface
{
    /**
     * @var GridDataFactoryInterface
     */
    private $zoneDataFactory;

    /**
     * @param GridDataFactoryInterface $zoneDataFactory
     */
    public function __construct(GridDataFactoryInterface $zoneDataFactory)
    {
        $this->zoneDataFactory = $zoneDataFactory;
    }

    /**
     * {@inheritdoc}
     */
    public function getData(SearchCriteriaInterface $searchCriteria)
    {
        $zoneData = $this->zoneDataFactory->getData($searchCriteria);

        return new GridData(
            new RecordCollection($zoneData->getRecords()->all()),
            $zoneData->getRecordsTotal(),
            $zoneData->getQuery()
        );
    }
}
