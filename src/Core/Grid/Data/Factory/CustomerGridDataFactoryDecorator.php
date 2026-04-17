<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Core\Grid\Data\Factory;

use PrestaShop\PrestaShop\Core\Grid\Data\GridData;
use PrestaShop\PrestaShop\Core\Grid\Record\RecordCollection;
use PrestaShop\PrestaShop\Core\Grid\Record\RecordCollectionInterface;
use PrestaShop\PrestaShop\Core\Grid\Search\SearchCriteriaInterface;
use PrestaShop\PrestaShop\Core\Localization\LocaleInterface;
use PrestaShop\PrestaShop\Core\Util\DateTime\DateTime as DateTimeUtil;

/**
 * Class CustomerGridDataFactoryDecorator decorates data from customer doctrine data factory.
 */
final class CustomerGridDataFactoryDecorator implements GridDataFactoryInterface
{
    /**
     * @var GridDataFactoryInterface
     */
    private $customerDoctrineGridDataFactory;

    /**
     * @var LocaleInterface
     */
    private $locale;

    /**
     * @var string
     */
    private $contextCurrencyIsoCode;

    /**
     * @param GridDataFactoryInterface $customerDoctrineGridDataFactory
     * @param LocaleInterface $locale
     * @param string $contextCurrencyIsoCode
     */
    public function __construct(
        GridDataFactoryInterface $customerDoctrineGridDataFactory,
        LocaleInterface $locale,
        $contextCurrencyIsoCode
    ) {
        $this->customerDoctrineGridDataFactory = $customerDoctrineGridDataFactory;
        $this->locale = $locale;
        $this->contextCurrencyIsoCode = $contextCurrencyIsoCode;
    }

    /**
     * {@inheritdoc}
     */
    public function getData(SearchCriteriaInterface $searchCriteria)
    {
        $customerData = $this->customerDoctrineGridDataFactory->getData($searchCriteria);

        $customerRecords = $this->applyModifications($customerData->getRecords());

        return new GridData(
            $customerRecords,
            $customerData->getRecordsTotal(),
            $customerData->getQuery()
        );
    }

    /**
     * @param RecordCollectionInterface $customers
     *
     * @return RecordCollection
     */
    private function applyModifications(RecordCollectionInterface $customers)
    {
        $modifiedCustomers = [];

        foreach ($customers as $customer) {
            if (empty($customer['social_title'])) {
                $customer['social_title'] = '--';
            }

            if (null === $customer['company']) {
                $customer['company'] = '--';
            }

            if (!empty($customer['total_spent'])) {
                $customer['total_spent'] = $this->locale->formatPrice(
                    $customer['total_spent'],
                    $this->contextCurrencyIsoCode
                );
            }

            if (null === $customer['connect']) {
                $customer['connect'] = DateTimeUtil::NULL_DATETIME;
            }

            $modifiedCustomers[] = $customer;
        }

        return new RecordCollection($modifiedCustomers);
    }
}
