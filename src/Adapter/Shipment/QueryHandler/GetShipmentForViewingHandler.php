<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Adapter\Shipment\QueryHandler;

use PrestaShop\PrestaShop\Adapter\Address\Repository\AddressRepository;
use PrestaShop\PrestaShop\Adapter\Carrier\Repository\CarrierRepository;
use PrestaShop\PrestaShop\Adapter\Country\Repository\CountryRepository;
use PrestaShop\PrestaShop\Adapter\Order\Repository\OrderRepository;
use PrestaShop\PrestaShop\Adapter\State\Repository\StateRepository;
use PrestaShop\PrestaShop\Core\CommandBus\Attributes\AsQueryHandler;
use PrestaShop\PrestaShop\Core\Domain\Address\QueryResult\ShippingAdressSummary;
use PrestaShop\PrestaShop\Core\Domain\Address\ValueObject\AddressId;
use PrestaShop\PrestaShop\Core\Domain\Carrier\QueryResult\CarrierSummary;
use PrestaShop\PrestaShop\Core\Domain\Carrier\ValueObject\CarrierId;
use PrestaShop\PrestaShop\Core\Domain\Country\ValueObject\CountryId;
use PrestaShop\PrestaShop\Core\Domain\Order\ValueObject\OrderId;
use PrestaShop\PrestaShop\Core\Domain\Shipment\Exception\ShipmentNotFoundException;
use PrestaShop\PrestaShop\Core\Domain\Shipment\Query\GetShipmentForViewing;
use PrestaShop\PrestaShop\Core\Domain\Shipment\QueryHandler\GetShipmentForViewingHandlerInterface;
use PrestaShop\PrestaShop\Core\Domain\Shipment\QueryResult\ShipmentForViewing;
use PrestaShop\PrestaShop\Core\Domain\State\ValueObject\StateId;
use PrestaShopBundle\Entity\Repository\ShipmentRepository;
use PrestaShopBundle\Entity\Shipment;
use Symfony\Contracts\Translation\TranslatorInterface;

#[AsQueryHandler]
class GetShipmentForViewingHandler implements GetShipmentForViewingHandlerInterface
{
    public function __construct(
        private readonly ShipmentRepository $shipmentRepository,
        private readonly OrderRepository $orderRepository,
        private readonly CarrierRepository $carrierRepository,
        private readonly CountryRepository $countryRepository,
        private readonly AddressRepository $addressRepository,
        private readonly StateRepository $stateRepository,
        private TranslatorInterface $translator,
    ) {
    }

    /**
     * @param GetShipmentForViewing $query
     *
     * @return ShipmentForViewing
     */
    public function handle(GetShipmentForViewing $query)
    {
        $id = $query->getShipmentId()->getValue();
        /** @var Shipment $shipment */
        $shipment = $this->shipmentRepository->find($id);

        if ($shipment === null) {
            throw new ShipmentNotFoundException(
                $this->translator->trans(
                    'Could not find shipment with id "%id%".',
                    ['%id%' => $id],
                    'Admin.Shipment.Error'
                )
            );
        }

        $order = $this->orderRepository->get(new OrderId($shipment->getOrderId()));
        $carrier = $this->carrierRepository->get(new CarrierId($shipment->getCarrierId()));
        $address = $this->addressRepository->get(new AddressId($shipment->getAddressId()));
        $state = $this->stateRepository->get(new StateId($address->id_state));
        $country = $this->countryRepository->get(new CountryId($address->id_country));

        $carrierSummary = new CarrierSummary($carrier->id, $carrier->name);
        $shippingAdressSummary = new ShippingAdressSummary(
            $address->firstname,
            $address->lastname,
            $address->company,
            $address->vat_number,
            $address->address1,
            $address->address2,
            $address->city,
            $address->postcode,
            $state->name,
            $country->name[(int) $order->getAssociatedLanguage()->getId()],
            $address->phone,
        );

        return new ShipmentForViewing(
            $id,
            $shipment->getTrackingNumber(),
            $carrierSummary,
            $shippingAdressSummary,
            $shipment->isDeleted()
        );
    }
}
