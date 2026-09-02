<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Core\Grid\Data\Factory;

use PrestaShop\PrestaShop\Core\Domain\CustomerService\Status\CustomerThreadStatusColor;
use PrestaShop\PrestaShop\Core\Grid\Data\GridData;
use PrestaShop\PrestaShop\Core\Grid\Record\RecordCollection;
use PrestaShop\PrestaShop\Core\Grid\Record\RecordCollectionInterface;
use PrestaShop\PrestaShop\Core\Grid\Search\SearchCriteriaInterface;

/**
 * Class decorates data from customer thread doctrine data factory by adding colors for status inputs.
 */
final class CustomerThreadGridDataFactoryDecorator implements GridDataFactoryInterface
{
    /**
     * @var GridDataFactoryInterface
     */
    private $customerDoctrineGridDataFactory;

    /**
     * @param GridDataFactoryInterface $customerDoctrineGridDataFactory
     */
    public function __construct(
        GridDataFactoryInterface $customerDoctrineGridDataFactory
    ) {
        $this->customerDoctrineGridDataFactory = $customerDoctrineGridDataFactory;
    }

    /**
     * {@inheritdoc}
     */
    public function getData(SearchCriteriaInterface $searchCriteria): GridData
    {
        $customerThreadData = $this->customerDoctrineGridDataFactory->getData($searchCriteria);

        $customerThreadRecords = $this->applyModifications($customerThreadData->getRecords());

        return new GridData(
            $customerThreadRecords,
            $customerThreadData->getRecordsTotal(),
            $customerThreadData->getQuery()
        );
    }

    /**
     * @param RecordCollectionInterface $customerThreads
     *
     * @return RecordCollection
     */
    private function applyModifications(RecordCollectionInterface $customerThreads): RecordCollection
    {
        $modifiedCustomerThreads = [];

        foreach ($customerThreads as $customerThread) {
            $customerThread['status_color'] = CustomerThreadStatusColor::CUSTOMER_THREAD_STATUSES[$customerThread['status']];
            $modifiedCustomerThreads[] = $customerThread;
        }

        return new RecordCollection($modifiedCustomerThreads);
    }
}
