<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Adapter\Carrier\ShippingCost\Provider;

use PrestaShop\Decimal\DecimalNumber;
use PrestaShop\PrestaShop\Adapter\Configuration as AdapterConfiguration;
use PrestaShop\PrestaShop\Core\Domain\Carrier\ShippingCost\Provider\FreeShippingCriteria;
use PrestaShop\PrestaShop\Core\Domain\Carrier\ShippingCost\Provider\FreeShippingCriteriaProviderInterface;

class ConfigFreeShippingCriteriaProvider implements FreeShippingCriteriaProviderInterface
{
    public function __construct(
        private readonly AdapterConfiguration $configuration,
    ) {
    }

    public function getCriteria(): FreeShippingCriteria
    {
        $freePrice = $this->configuration->get('PS_SHIPPING_FREE_PRICE');
        $freeWeight = $this->configuration->get('PS_SHIPPING_FREE_WEIGHT');

        return new FreeShippingCriteria(
            $freePrice !== false && $freePrice > 0 ? new DecimalNumber((string) $freePrice) : null,
            $freeWeight !== false && $freeWeight > 0 ? new DecimalNumber((string) $freeWeight) : null,
        );
    }
}
