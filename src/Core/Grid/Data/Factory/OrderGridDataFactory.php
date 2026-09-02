<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Core\Grid\Data\Factory;

use PrestaShop\PrestaShop\Core\ConfigurationInterface;
use PrestaShop\PrestaShop\Core\Grid\Data\GridData;
use PrestaShop\PrestaShop\Core\Grid\Record\RecordCollection;
use PrestaShop\PrestaShop\Core\Grid\Search\SearchCriteriaInterface;
use PrestaShop\PrestaShop\Core\Localization\Locale\RepositoryInterface;

/**
 * Decorates DoctrineGridDataFactory configured for orders to modify order records.
 */
final class OrderGridDataFactory implements GridDataFactoryInterface
{
    /**
     * @var GridDataFactoryInterface
     */
    private $dataFactory;

    /**
     * @var RepositoryInterface
     */
    private $localeRepository;

    /**
     * @var string
     */
    private $contextLocale;

    /**
     * @var ConfigurationInterface
     */
    private $configuration;

    /**
     * @param GridDataFactoryInterface $dataFactory
     * @param RepositoryInterface $localeRepository
     * @param ConfigurationInterface $configuration
     * @param string $contextLocale
     */
    public function __construct(
        GridDataFactoryInterface $dataFactory,
        RepositoryInterface $localeRepository,
        ConfigurationInterface $configuration,
        $contextLocale
    ) {
        $this->dataFactory = $dataFactory;
        $this->localeRepository = $localeRepository;
        $this->contextLocale = $contextLocale;
        $this->configuration = $configuration;
    }

    /**
     * {@inheritdoc}
     */
    public function getData(SearchCriteriaInterface $searchCriteria)
    {
        $data = $this->dataFactory->getData($searchCriteria);
        $records = $data->getRecords()->all();

        $locale = $this->localeRepository->getLocale($this->contextLocale);
        $isInvoicesEnabled = $this->configuration->get('PS_INVOICE');

        foreach ($records as &$record) {
            if (!$record['company']) {
                $record['company'] = '--';
            }

            $record['total_paid_tax_incl'] = $locale->formatPrice(
                $record['total_paid_tax_incl'],
                $record['iso_code']
            );

            $record['is_invoice_available'] = $isInvoicesEnabled && $record['invoice_number'];
        }

        return new GridData(
            new RecordCollection($records),
            $data->getRecordsTotal(),
            $data->getQuery()
        );
    }
}
