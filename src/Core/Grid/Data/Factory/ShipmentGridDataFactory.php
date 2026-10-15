<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Core\Grid\Data\Factory;

use Doctrine\DBAL\Exception as DBALException;
use PrestaShop\PrestaShop\Core\ConfigurationInterface;
use PrestaShop\PrestaShop\Core\Context\CurrencyContext;
use PrestaShop\PrestaShop\Core\Domain\Shipment\ValueObject\ShipmentStatus;
use PrestaShop\PrestaShop\Core\Grid\Data\GridData;
use PrestaShop\PrestaShop\Core\Grid\Record\RecordCollection;
use PrestaShop\PrestaShop\Core\Grid\Record\RecordCollectionInterface;
use PrestaShop\PrestaShop\Core\Grid\Search\SearchCriteriaInterface;
use PrestaShop\PrestaShop\Core\Localization\LocaleInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

/**
 * Presents the raw rows of the shop-wide shipments grid: prices, weights and the derived status.
 *
 * @internal The grid id 'shipment' was the per-order shipments grid up to 9.2, and it now names this
 *           shop-wide one. The per-order grid moved to 'order_shipment'. That reassignment also moves
 *           the hooks derived from the id, actionShipmentGrid*Modifier, onto this grid. It is a
 *           deliberate break, accepted because the improved_shipment feature flag is beta and off by
 *           default; these classes carry no backward compatibility promise while it stays that way.
 */
final class ShipmentGridDataFactory implements GridDataFactoryInterface
{
    public function __construct(
        private readonly GridDataFactoryInterface $shipmentDataFactory,
        private readonly LocaleInterface $locale,
        private readonly CurrencyContext $currencyContext,
        private readonly ConfigurationInterface $configuration,
        private readonly TranslatorInterface $translator,
    ) {
    }

    /**
     * @throws DBALException if the decorated factory cannot read the shipments from the database
     */
    public function getData(SearchCriteriaInterface $searchCriteria): GridData
    {
        $data = $this->shipmentDataFactory->getData($searchCriteria);

        return new GridData(
            $this->applyModifications($data->getRecords()),
            $data->getRecordsTotal(),
            $data->getQuery()
        );
    }

    private function applyModifications(RecordCollectionInterface $records): RecordCollectionInterface
    {
        $weightUnit = (string) $this->configuration->get('PS_WEIGHT_UNIT');
        $currencyIsoCode = $this->currencyContext->getIsoCode();
        $modified = [];

        foreach ($records as $record) {
            $record['shipping_cost'] = $this->locale->formatPrice((float) $record['shipping_cost'], $currencyIsoCode);
            $record['weight'] = sprintf('%.3f %s', (float) $record['weight'], $weightUnit);
            $record['items'] = (int) $record['items'];

            $status = ShipmentStatus::tryFrom((string) $record['status']);
            $record['status'] = $status?->trans($this->translator) ?? '';
            $record['status_badge_type'] = $status?->getBadgeType() ?? '';

            $modified[] = $record;
        }

        return new RecordCollection($modified);
    }
}
