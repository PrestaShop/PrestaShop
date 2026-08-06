<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Adapter\Carrier\ShippingCost\Calculator;

use PrestaShop\Decimal\DecimalNumber;
use PrestaShop\PrestaShop\Adapter\Currency\Repository\CurrencyRepository;
use PrestaShop\PrestaShop\Adapter\Tools;
use PrestaShop\PrestaShop\Core\Domain\Carrier\ShippingCost\Calculator\ShippingCostCalculatorInterface;
use PrestaShop\PrestaShop\Core\Domain\Carrier\ShippingCost\Provider\FreeShippingCriteriaProviderInterface;
use PrestaShop\PrestaShop\Core\Domain\Carrier\ShippingCost\ShippingCostPriceInterface;
use PrestaShop\PrestaShop\Core\Domain\Currency\ValueObject\CurrencyId;

class FreeShippingCalculator implements ShippingCostCalculatorInterface
{
    public function __construct(
        private readonly FreeShippingCriteriaProviderInterface $criteriaProvider,
        private readonly Tools $tools,
        private readonly CurrencyRepository $currencyRepository,
    ) {
    }

    public function compute(ShippingCostPriceInterface $context): void
    {
        if (!$context->isAvailable() || $context->isFreeShipping()) {
            return;
        }

        $thresholds = $this->criteriaProvider->getCriteria();

        if ($thresholds->hasFreePrice()) {
            $convertedPrice = new DecimalNumber((string) $this->tools->convertPrice(
                (float) (string) $thresholds->getFreePrice(),
                $this->currencyRepository->get(new CurrencyId($context->getCurrencyId()))
            ));

            if ($context->getOrderTotal()->isGreaterOrEqualThan($convertedPrice)) {
                $context->setFreeShipping(true);

                return;
            }
        }

        if ($thresholds->hasFreeWeight() && $context->getTotalWeight()->isGreaterOrEqualThan($thresholds->getFreeWeight())) {
            $context->setFreeShipping(true);
        }
    }
}
