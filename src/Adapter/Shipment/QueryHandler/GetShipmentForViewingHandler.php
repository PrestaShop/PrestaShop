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

#[AsQueryHandler]
class GetShipmentForViewingHandler implements GetShipmentForViewingHandlerInterface
{
    public function __construct(
        private readonly ShipmentRepository $shipmentRepository,
        private readonly OrderRepository $orderRepository,
        private readonly CarrierRepository $carrierRepository,
        private readonly CountryRepository $countryRepository,
        private readonly AddressRepository $addressRepository,
        private readonly StateRepository $stateRepository
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
            throw new ShipmentNotFoundException(sprintf('Could not find shipment with id "%s"', $id));
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
        );
    }
}
