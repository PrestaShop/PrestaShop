<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace Tests\Unit\Adapter\Order\Checkout;

use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use PrestaShop\PrestaShop\Adapter\Order\Checkout\OnePageCheckoutAvailabilityChecker;
use PrestaShop\PrestaShop\Core\Checkout\OnePageCheckoutSettings;
use PrestaShop\PrestaShop\Core\Domain\Configuration\ShopConfigurationInterface;
use PrestaShop\PrestaShop\Core\Domain\Shop\ValueObject\ShopConstraint;
use PrestaShop\PrestaShop\Core\FeatureFlag\FeatureFlagSettings;
use PrestaShop\PrestaShop\Core\FeatureFlag\FeatureFlagStateCheckerInterface;
use RuntimeException;

class OnePageCheckoutAvailabilityCheckerTest extends TestCase
{
    private const SHOP_ID = 42;

    private ShopConfigurationInterface|MockObject $configuration;
    private FeatureFlagStateCheckerInterface|MockObject $featureFlagStateChecker;

    private OnePageCheckoutAvailabilityChecker $onePageCheckoutAvailabilityChecker;

    protected function setUp(): void
    {
        $this->configuration = $this->createMock(ShopConfigurationInterface::class);
        $this->featureFlagStateChecker = $this->createMock(FeatureFlagStateCheckerInterface::class);

        $this->onePageCheckoutAvailabilityChecker = new OnePageCheckoutAvailabilityChecker(
            $this->configuration,
            $this->featureFlagStateChecker
        );
    }

    /**
     * @dataProvider provideAvailabilityCases
     */
    public function testItReturnsExpectedAvailability(
        bool $isConfigurationEnabled,
        bool $isFeatureFlagEnabled,
        bool $expectedResult
    ): void {
        $this->configuration
            ->expects($this->once())
            ->method('get')
            ->with(
                OnePageCheckoutSettings::ONE_PAGE_CHECKOUT_ENABLED,
                false,
                $this->callback(fn (ShopConstraint $shopConstraint): bool => $shopConstraint->isEqual(ShopConstraint::shop(self::SHOP_ID)))
            )
            ->willReturn($isConfigurationEnabled)
        ;

        if ($isConfigurationEnabled) {
            $this->featureFlagStateChecker
                ->expects($this->once())
                ->method('isEnabled')
                ->with(FeatureFlagSettings::FEATURE_FLAG_ONE_PAGE_CHECKOUT)
                ->willReturn($isFeatureFlagEnabled)
            ;
        } else {
            $this->featureFlagStateChecker
                ->expects($this->never())
                ->method('isEnabled')
            ;
        }

        $this->assertSame($expectedResult, $this->onePageCheckoutAvailabilityChecker->isEnabledForShop(self::SHOP_ID));
    }

    /**
     * @return iterable<array{bool, bool, bool}>
     */
    public function provideAvailabilityCases(): iterable
    {
        yield 'configuration disabled' => [false, false, false];
        yield 'configuration enabled and feature flag disabled' => [true, false, false];
        yield 'configuration enabled and feature flag enabled' => [true, true, true];
    }

    public function testItReturnsFalseWhenConfigurationThrowsException(): void
    {
        $this->configuration
            ->expects($this->once())
            ->method('get')
            ->willThrowException(new RuntimeException('Configuration read failed'))
        ;

        $this->featureFlagStateChecker
            ->expects($this->never())
            ->method('isEnabled')
        ;

        $this->assertFalse($this->onePageCheckoutAvailabilityChecker->isEnabledForShop(self::SHOP_ID));
    }

    public function testItReturnsFalseWhenFeatureFlagCheckerThrowsException(): void
    {
        $this->configuration
            ->expects($this->once())
            ->method('get')
            ->willReturn(true)
        ;

        $this->featureFlagStateChecker
            ->expects($this->once())
            ->method('isEnabled')
            ->willThrowException(new RuntimeException('Feature flag check failed'))
        ;

        $this->assertFalse($this->onePageCheckoutAvailabilityChecker->isEnabledForShop(self::SHOP_ID));
    }
}
