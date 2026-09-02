<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Core\Grid\Data\Factory;

use Customer;
use PrestaShop\PrestaShop\Core\Grid\Data\GridData;
use PrestaShop\PrestaShop\Core\Grid\Record\RecordCollection;
use PrestaShop\PrestaShop\Core\Grid\Search\SearchCriteriaInterface;
use PrestaShop\PrestaShop\Core\Localization\Locale\RepositoryInterface;
use Validate;

final class OutstandingGridDataFactory implements GridDataFactoryInterface
{
    /**
     * @var GridDataFactoryInterface
     */
    private $outstandingDataFactory;

    /**
     * @var RepositoryInterface
     */
    private $repositoryLocale;

    /**
     * @var string
     */
    private $contextLocale;

    /**
     * @param GridDataFactoryInterface $outstandingDataFactory
     * @param RepositoryInterface $repositoryLocale
     * @param string $contextLocale
     */
    public function __construct(
        GridDataFactoryInterface $outstandingDataFactory,
        RepositoryInterface $repositoryLocale,
        string $contextLocale
    ) {
        $this->outstandingDataFactory = $outstandingDataFactory;
        $this->repositoryLocale = $repositoryLocale;
        $this->contextLocale = $contextLocale;
    }

    /**
     * {@inheritdoc}
     */
    public function getData(SearchCriteriaInterface $searchCriteria)
    {
        $data = $this->outstandingDataFactory->getData($searchCriteria);
        $records = $data->getRecords()->all();

        $locale = $this->repositoryLocale->getLocale($this->contextLocale);

        foreach ($records as &$record) {
            $customer = new Customer((int) $record['id_customer']);
            $record['outstanding'] = Validate::isLoadedObject($customer)
                ? $locale->formatPrice($customer->getOutstanding(), $record['iso_code'])
                : null
            ;

            if ($record['outstanding_allow_amount'] !== null) {
                $record['outstanding_allow_amount'] = $locale->formatPrice($record['outstanding_allow_amount'], $record['iso_code']);
            }

            if (!$record['company']) {
                $record['company'] = '--';
            }
        }

        return new GridData(
            new RecordCollection($records),
            $data->getRecordsTotal(),
            $data->getQuery()
        );
    }
}
