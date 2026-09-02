<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Core\Form\IdentifiableObject\DataProvider;

use PrestaShop\PrestaShop\Core\CommandBus\CommandBusInterface;
use PrestaShop\PrestaShop\Core\Domain\Shipment\Query\GetShipmentForEditing;
use PrestaShop\PrestaShop\Core\Domain\Shipment\QueryResult\ShipmentForEditing;

abstract class ShipmentFormDataProvider implements FormDataProviderInterface
{
    public function __construct(
        private CommandBusInterface $queryBus,
    ) {
    }

    protected function isShipmentShipped(int $orderId, int $shipmentId): bool
    {
        /** @var ShipmentForEditing $shipment */
        $shipment = $this->queryBus->handle(new GetShipmentForEditing($orderId, $shipmentId));

        return !empty($shipment->getTrackingNumber());
    }
}
