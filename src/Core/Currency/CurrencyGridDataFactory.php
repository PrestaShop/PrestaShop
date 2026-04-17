<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Core\Currency;

use PrestaShop\PrestaShop\Core\Grid\Data\Factory\GridDataFactoryInterface;
use PrestaShop\PrestaShop\Core\Grid\Data\GridData;
use PrestaShop\PrestaShop\Core\Grid\Record\RecordCollection;
use PrestaShop\PrestaShop\Core\Grid\Record\RecordCollectionInterface;
use PrestaShop\PrestaShop\Core\Grid\Search\SearchCriteriaInterface;

/**
 * Class CurrencyGridDataFactory is responsible for providing modified currency list grid data.
 */
final class CurrencyGridDataFactory implements GridDataFactoryInterface
{
    /**
     * @var GridDataFactoryInterface
     */
    private $gridDataFactory;

    /**
     * CurrencyGridDataFactory constructor.
     *
     * @param GridDataFactoryInterface $gridDataFactory
     */
    public function __construct(GridDataFactoryInterface $gridDataFactory)
    {
        $this->gridDataFactory = $gridDataFactory;
    }

    /**
     * {@inheritdoc}
     */
    public function getData(SearchCriteriaInterface $searchCriteria)
    {
        $gridData = $this->gridDataFactory->getData($searchCriteria);

        $records = $gridData->getRecords();

        return new GridData(
            $this->getModifiedRecords($records),
            $gridData->getRecordsTotal(),
            $gridData->getQuery()
        );
    }

    /**
     * Gets record collection with extra and modified rows.
     *
     * @param RecordCollectionInterface $records
     *
     * @return RecordCollection
     */
    private function getModifiedRecords(RecordCollectionInterface $records)
    {
        $result = [];
        foreach ($records as $key => $record) {
            $result[$key] = $record;
            $result[$key]['name'] = $this->buildCurrencyName($result[$key]);
        }

        return new RecordCollection($result);
    }

    /**
     * @param array $currency
     *
     * @return string
     */
    private function buildCurrencyName(array $currency)
    {
        return mb_ucfirst($currency['name']);
    }
}
