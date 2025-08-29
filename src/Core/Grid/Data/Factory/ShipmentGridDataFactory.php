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

use PrestaShop\PrestaShop\Adapter\Configuration;
use PrestaShop\PrestaShop\Core\Context\CurrencyContext;
use PrestaShop\PrestaShop\Core\Grid\Data\GridData;
use PrestaShop\PrestaShop\Core\Grid\Record\RecordCollection;
use PrestaShop\PrestaShop\Core\Grid\Record\RecordCollectionInterface;
use PrestaShop\PrestaShop\Core\Grid\Search\SearchCriteriaInterface;
use PrestaShop\PrestaShop\Core\Localization\LocaleInterface;

class ShipmentGridDataFactory implements GridDataFactoryInterface
{
    public function __construct(
        private readonly GridDataFactoryInterface $shipmentDataFactory,
        private readonly LocaleInterface $locale,
        private readonly CurrencyContext $currencyContext,
        private readonly Configuration $configuration,
    ) {
    }

    public function getData(SearchCriteriaInterface $searchCriteria): GridData
    {
        $data = $this->shipmentDataFactory->getData($searchCriteria);
        $modifiedRecords = $this->applyModifications($data->getRecords(), $data->getRecordsTotal());

        return new GridData(
            $modifiedRecords,
            $data->getRecordsTotal(),
            $data->getQuery()
        );
    }

    private function applyModifications(RecordCollectionInterface $records, int $totalRecord): RecordCollectionInterface
    {
        $updated = [];

        foreach ($records as $record) {
            $record['shipping_cost'] = $this->locale->formatPrice((float) $record['shipping_cost'], $this->currencyContext->getIsoCode());
            $record['weight'] = sprintf('%.3f %s', $record['weight'], $this->configuration->get('PS_WEIGHT_UNIT'));
            $record['total_shipments'] = $totalRecord;
            $updated[] = $record;
        }

        return new RecordCollection($updated);
    }
}
