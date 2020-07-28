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
            $record['outstanding'] = $locale->formatPrice(
                Validate::isLoadedObject($customer) ? $customer->getOutstanding() : 0.00,
                $record['iso_code']
            );

            $record['outstanding_allow_amount'] = $locale->formatPrice($record['outstanding_allow_amount'], $record['iso_code']);

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
