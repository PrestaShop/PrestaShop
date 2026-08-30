<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Adapter\Carrier\ShippingCost\Calculator;

use PrestaShop\PrestaShop\Adapter\Address\Repository\AddressRepository;
use PrestaShop\PrestaShop\Core\Domain\Address\Exception\AddressNotFoundException;
use PrestaShop\PrestaShop\Core\Domain\Address\ValueObject\AddressId;
use PrestaShop\PrestaShop\Core\Domain\Carrier\ShippingCost\Calculator\ShippingCostCalculatorInterface;
use PrestaShop\PrestaShop\Core\Domain\Carrier\ShippingCost\ShippingCostPriceInterface;

class ZoneResolutionCalculator implements ShippingCostCalculatorInterface
{
    public function __construct(
        private readonly AddressRepository $addressRepository,
    ) {
    }

    public function compute(ShippingCostPriceInterface $context): void
    {
        if (!$context->isAvailable() || $context->getResolvedZoneId() !== null) {
            return;
        }

        $addressId = $context->getAddressId();

        if ($addressId !== null) {
            try {
                $zoneId = $this->addressRepository->getZoneId(new AddressId($addressId));
            } catch (AddressNotFoundException) {
                $context->setAvailable(false);

                return;
            }
        } else {
            $zoneId = $context->getCountryZoneId();
        }

        $context->setResolvedZoneId($zoneId);
    }
}
