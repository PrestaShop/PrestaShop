<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Adapter\Carrier\ShippingCost\Provider;

use PrestaShop\Decimal\DecimalNumber;
use PrestaShop\PrestaShop\Adapter\Configuration as AdapterConfiguration;
use PrestaShop\PrestaShop\Adapter\Currency\Repository\CurrencyRepository;
use PrestaShop\PrestaShop\Adapter\HookManager;
use PrestaShop\PrestaShop\Adapter\Tools;
use PrestaShop\PrestaShop\Core\Domain\Carrier\ShippingCost\Provider\FreeShippingCriteria;
use PrestaShop\PrestaShop\Core\Domain\Carrier\ShippingCost\Provider\FreeShippingCriteriaProviderInterface;
use PrestaShop\PrestaShop\Core\Domain\Carrier\ShippingCost\ShippingCostPriceInterface;
use PrestaShop\PrestaShop\Core\Domain\Currency\ValueObject\CurrencyId;

class ConfigFreeShippingCriteriaProvider implements FreeShippingCriteriaProviderInterface
{
    public function __construct(
        private readonly HookManager $hookManager,
        private readonly AdapterConfiguration $configuration,
        private readonly Tools $tools,
        private readonly CurrencyRepository $currencyRepository,
    ) {
    }

    public function getCriteria(ShippingCostPriceInterface $context): FreeShippingCriteria
    {
        $freePrice = $this->configuration->get('PS_SHIPPING_FREE_PRICE');
        if ($freePrice !== false && (float) $freePrice > 0) {
            $freePrice = $this->tools->convertPrice(
                (float) $freePrice,
                $this->currencyRepository->get(new CurrencyId($context->getCurrencyId()))
            );
        }

        $zoneId = $context->getResolvedZoneId() ?? $context->getCountryZoneId();

        $this->hookManager->exec('actionOverrideShippingFreePrice', [
            'shippingFreePrice' => &$freePrice,
            'id_zone' => $zoneId,
            'id_currency' => $context->getCurrencyId(),
        ]);

        $freeWeight = $this->configuration->get('PS_SHIPPING_FREE_WEIGHT');
        $this->hookManager->exec('actionOverrideShippingFreeWeight', [
            'shippingFreeWeight' => &$freeWeight,
            'id_zone' => $zoneId,
            'id_currency' => $context->getCurrencyId(),
        ]);

        return new FreeShippingCriteria(
            $freePrice !== false && (float) $freePrice > 0 ? new DecimalNumber((string) $freePrice) : null,
            $freeWeight !== false && (float) $freeWeight > 0 ? new DecimalNumber((string) $freeWeight) : null,
        );
    }
}
