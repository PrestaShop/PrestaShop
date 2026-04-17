<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
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
