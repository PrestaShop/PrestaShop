<?php
/**
 * Copyright since 2007 PrestaShop SA and Contributors
 * PrestaShop is an International Registered Trademark & Property of PrestaShop SA
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.md.
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
 * needs please refer to https://devdocs.prestashop.com/ for more information.
 *
 * @author    PrestaShop SA and Contributors <contact@prestashop.com>
 * @copyright Since 2007 PrestaShop SA and Contributors
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
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
