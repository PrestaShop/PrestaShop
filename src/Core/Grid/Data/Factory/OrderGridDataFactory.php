<?php
/**
 * 2007-2019 PrestaShop SA and Contributors
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * https://opensource.org/licenses/OSL-3.0
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@prestashop.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade PrestaShop to newer
 * versions in the future. If you wish to customize PrestaShop for your
 * needs please refer to https://www.prestashop.com for more information.
 *
 * @author    PrestaShop SA <contact@prestashop.com>
 * @copyright 2007-2019 PrestaShop SA and Contributors
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 * International Registered Trademark & Property of PrestaShop SA
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
     * @var string
     */
    private $contextCurrencyIsoCode;

    /**
     * @var ConfigurationInterface
     */
    private $configuration;

    /**
     * @param GridDataFactoryInterface $dataFactory
     * @param RepositoryInterface $localeRepository
     * @param ConfigurationInterface $configuration
     * @param string $contextLocale
     * @param string $contextCurrencyIsoCode
     */
    public function __construct(
        GridDataFactoryInterface $dataFactory,
        RepositoryInterface $localeRepository,
        ConfigurationInterface $configuration,
        $contextLocale,
        $contextCurrencyIsoCode
    ) {
        $this->dataFactory = $dataFactory;
        $this->localeRepository = $localeRepository;
        $this->contextLocale = $contextLocale;
        $this->contextCurrencyIsoCode = $contextCurrencyIsoCode;
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
                $this->contextCurrencyIsoCode
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
