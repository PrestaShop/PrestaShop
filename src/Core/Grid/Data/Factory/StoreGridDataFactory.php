<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */
declare(strict_types=1);

namespace PrestaShop\PrestaShop\Core\Grid\Data\Factory;

use PrestaShop\PrestaShop\Core\Grid\Data\GridData;
use PrestaShop\PrestaShop\Core\Grid\Data\GridDataInterface;
use PrestaShop\PrestaShop\Core\Grid\Record\RecordCollection;
use PrestaShop\PrestaShop\Core\Grid\Search\SearchCriteriaInterface;

class StoreGridDataFactory implements GridDataFactoryInterface
{
    /**
     * @var GridDataFactoryInterface
     */
    private $storeDataFactory;

    public function __construct(
        GridDataFactoryInterface $storeDataFactory
    ) {
        $this->storeDataFactory = $storeDataFactory;
    }

    public function getData(SearchCriteriaInterface $searchCriteria): GridDataInterface
    {
        $storeData = $this->storeDataFactory->getData($searchCriteria);

        $modifiedRecords = $this->applyModification(
            $storeData->getRecords()->all()
        );

        return new GridData(
            new RecordCollection($modifiedRecords),
            $storeData->getRecordsTotal(),
            $storeData->getQuery()
        );
    }

    /**
     * @param array<int, array<string, mixed>> $stores
     *
     * @return array<int, array<string, mixed>>
     */
    protected function applyModification(array $stores): array
    {
        $optionalFields = ['state', 'fax', 'phone'];

        foreach ($stores as $i => $store) {
            foreach ($store as $key => $value) {
                if (!in_array($key, $optionalFields, true)) {
                    continue;
                }

                // adds placeholder "--" to every field which contains empty value
                if (!$value) {
                    $stores[$i][$key] = '--';
                }
            }
        }

        return $stores;
    }
}
