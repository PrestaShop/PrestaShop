<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Core\Grid\Data\Factory;

use PrestaShop\PrestaShop\Core\Grid\Data\GridData;
use PrestaShop\PrestaShop\Core\Grid\Record\RecordCollection;
use PrestaShop\PrestaShop\Core\Grid\Record\RecordCollectionInterface;
use PrestaShop\PrestaShop\Core\Grid\Search\SearchCriteriaInterface;
use PrestaShop\PrestaShop\Core\Localization\LocaleInterface;

/**
 * Class CustomerOrderGridDataFactoryDecorator decorates data from customer ordesrs doctrine data factory.
 */
final class CustomerOrderGridDataFactoryDecorator implements GridDataFactoryInterface
{
    /**
     * @var GridDataFactoryInterface
     */
    private $customerOrderDoctrineGridDataFactory;

    /**
     * @var LocaleInterface
     */
    private $locale;

    /**
     * @var string
     */
    private $contextCurrencyIsoCode;

    /**
     * @param GridDataFactoryInterface $customerOrderDoctrineGridDataFactory
     * @param LocaleInterface $locale
     * @param string $contextCurrencyIsoCode
     */
    public function __construct(
        GridDataFactoryInterface $customerOrderDoctrineGridDataFactory,
        LocaleInterface $locale,
        $contextCurrencyIsoCode
    ) {
        $this->customerOrderDoctrineGridDataFactory = $customerOrderDoctrineGridDataFactory;
        $this->locale = $locale;
        $this->contextCurrencyIsoCode = $contextCurrencyIsoCode;
    }

    /**
     * {@inheritdoc}
     */
    public function getData(SearchCriteriaInterface $searchCriteria)
    {
        $customerData = $this->customerOrderDoctrineGridDataFactory->getData($searchCriteria);

        $records = $this->applyModifications($customerData->getRecords());

        return new GridData(
            $records,
            $customerData->getRecordsTotal(),
            $customerData->getQuery()
        );
    }

    /**
     * @param RecordCollectionInterface $records
     *
     * @return RecordCollection
     */
    private function applyModifications(RecordCollectionInterface $records)
    {
        $modifiedRecords = [];
        foreach ($records as $r) {
            if (!empty($r['total_paid_tax_incl'])) {
                $r['total_paid_tax_incl'] = $this->locale->formatPrice(
                    $r['total_paid_tax_incl'],
                    $this->contextCurrencyIsoCode
                );
            }

            if (!empty($r['status'])) {
                $r['status'] = '<span class="badge badge-' .
                ($r['valid'] == 1 ? 'success' : 'danger') .
                ' rounded">' . $r['status'] . '</span>';
            }

            if (empty($r['nb_products'])) {
                $r['nb_products'] = '0';
            }

            $modifiedRecords[] = $r;
        }

        return new RecordCollection($modifiedRecords);
    }
}
