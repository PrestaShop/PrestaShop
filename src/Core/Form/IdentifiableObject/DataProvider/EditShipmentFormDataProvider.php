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
use Symfony\Component\HttpFoundation\RequestStack;

class EditShipmentFormDataProvider implements FormDataProviderInterface
{
    public function __construct(
        private CommandBusInterface $queryBus,
        private RequestStack $requestStack,
    ) {
    }

    public function getData($orderId)
    {
        $shipmentId = (int) $this->requestStack->getCurrentRequest()->attributes->get('shipmentId');

        /** @var ShipmentForEditing $shipment */
        $shipment = $this->queryBus->handle(new GetShipmentForEditing($orderId, $shipmentId));

        $data = $shipment->toArray();
        $data['shipment_id'] = $shipmentId;

        return $data;
    }

    public function getDefaultData()
    {
        return [];
    }
}
