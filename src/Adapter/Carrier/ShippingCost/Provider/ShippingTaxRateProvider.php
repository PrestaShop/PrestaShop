<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Adapter\Carrier\ShippingCost\Provider;

use Exception;
use PrestaShop\Decimal\DecimalNumber;
use PrestaShop\PrestaShop\Adapter\Address\Repository\AddressRepository;
use PrestaShop\PrestaShop\Adapter\Carrier\Repository\CarrierRepository;
use PrestaShop\PrestaShop\Core\Domain\Address\ValueObject\AddressId;
use PrestaShop\PrestaShop\Core\Domain\Carrier\ShippingCost\Provider\ShippingTaxRateProviderInterface;
use PrestaShop\PrestaShop\Core\Domain\Carrier\ValueObject\CarrierId;
use PrestaShop\PrestaShop\Core\Pricing\ValueObject\TaxRate;
use Psr\Log\LoggerInterface;

class ShippingTaxRateProvider implements ShippingTaxRateProviderInterface
{
    public function __construct(
        private readonly CarrierRepository $carrierRepository,
        private readonly AddressRepository $addressRepository,
        private readonly ?LoggerInterface $logger = null,
    ) {
    }

    public function getTaxRate(int $carrierId, int $addressId): TaxRate
    {
        try {
            $carrier = $this->carrierRepository->get(new CarrierId($carrierId));
            $address = $this->addressRepository->get(new AddressId($addressId));

            return new TaxRate(new DecimalNumber((string) $carrier->getTaxesRate($address)));
        } catch (Exception $e) {
            if ($this->logger) {
                $this->logger->error(
                    sprintf('Failed to retrieve tax rate for carrier %d and address %d: %s', $carrierId, $addressId, $e->getMessage()),
                    ['exception' => $e]
                );
            }

            return TaxRate::zero();
        }
    }
}
