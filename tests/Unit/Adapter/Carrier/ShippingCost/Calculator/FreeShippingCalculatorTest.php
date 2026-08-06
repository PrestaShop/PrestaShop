<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Tests\Unit\Adapter\Carrier\ShippingCost\Calculator;

use Currency;
use PHPUnit\Framework\TestCase;
use PrestaShop\Decimal\DecimalNumber;
use PrestaShop\PrestaShop\Adapter\Carrier\ShippingCost\Calculator\FreeShippingCalculator;
use PrestaShop\PrestaShop\Adapter\Currency\Repository\CurrencyRepository;
use PrestaShop\PrestaShop\Adapter\Tools;
use PrestaShop\PrestaShop\Core\Domain\Carrier\ShippingCost\Provider\FreeShippingCriteria;
use PrestaShop\PrestaShop\Core\Domain\Carrier\ShippingCost\Provider\FreeShippingCriteriaProviderInterface;
use PrestaShop\PrestaShop\Core\Domain\Carrier\ShippingCost\ShippingCostPrice;
use PrestaShop\PrestaShop\Core\Domain\Carrier\ShippingCost\ShippingCostPriceInterface;
use PrestaShop\PrestaShop\Core\Domain\Carrier\ValueObject\ShippingCalculationRequest;
use PrestaShop\PrestaShop\Core\Domain\Currency\ValueObject\CurrencyId;

class FreeShippingCalculatorTest extends TestCase
{
    /** @var FreeShippingCriteriaProviderInterface|\PHPUnit\Framework\MockObject\MockObject */
    private $criteriaProvider;

    /** @var Tools|\PHPUnit\Framework\MockObject\MockObject */
    private $tools;

    /** @var CurrencyRepository|\PHPUnit\Framework\MockObject\MockObject */
    private $currencyRepository;

    /** @var FreeShippingCalculator */
    private $calculator;

    protected function setUp(): void
    {
        $this->criteriaProvider = $this->createMock(FreeShippingCriteriaProviderInterface::class);
        $this->tools = $this->createMock(Tools::class);
        $this->currencyRepository = $this->createMock(CurrencyRepository::class);
        $this->calculator = new FreeShippingCalculator(
            $this->criteriaProvider,
            $this->tools,
            $this->currencyRepository
        );
    }

    public function testItReturnsEarlyIfAlreadyUnavailable(): void
    {
        $context = $this->createContext(1, new DecimalNumber('10'));
        $context->setAvailable(false);

        $this->criteriaProvider->expects($this->never())->method('getCriteria');

        $this->calculator->compute($context);
    }

    public function testItDoesNotComputeIfFreeShippingIsAlreadySet(): void
    {
        $context = $this->createContext(1, new DecimalNumber('10'));
        $context->setFreeShipping(true);

        $this->criteriaProvider->expects($this->never())->method('getCriteria');

        $this->calculator->compute($context);
    }

    public function testItSetsFreeShippingIfPriceThresholdIsMet(): void
    {
        $context = $this->createContext(1, new DecimalNumber('50'));
        $criteria = new FreeShippingCriteria(new DecimalNumber('40'), null);
        $this->criteriaProvider->method('getCriteria')->willReturn($criteria);

        $currency = $this->createMock(Currency::class);
        $this->currencyRepository->method('get')->with(new CurrencyId(1))->willReturn($currency);
        $this->tools->method('convertPrice')->willReturn(40.0);

        $this->calculator->compute($context);

        $this->assertTrue($context->isFreeShipping());
    }

    public function testItSetsFreeShippingIfWeightThresholdIsMet(): void
    {
        $context = $this->createContext(1, new DecimalNumber('10'));
        $context->setTotalWeight(new DecimalNumber('10'));
        $criteria = new FreeShippingCriteria(null, new DecimalNumber('5'));
        $this->criteriaProvider->method('getCriteria')->willReturn($criteria);

        $this->calculator->compute($context);

        $this->assertTrue($context->isFreeShipping());
    }

    private function createContext(int $currencyId, DecimalNumber $orderTotal): ShippingCostPriceInterface
    {
        $request = new ShippingCalculationRequest(
            [], // products
            1, // carrierId
            1, // zoneId
            null, // addressId
            1, // countryZoneId
            $currencyId,
            null, // customerId
            (float) $orderTotal->__toString()
        );

        return ShippingCostPrice::createFromRequest($request);
    }
}
