<?php

/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Tests\Unit\Adapter\Product\Presentation;

use PHPUnit\Framework\TestCase;
use PrestaShop\PrestaShop\Adapter\Product\Presentation\ProductQuantityDiscountProvider;
use PrestaShop\PrestaShop\Core\FeatureFlag\FeatureFlagSettings;
use PrestaShop\PrestaShop\Core\FeatureFlag\FeatureFlagStateCheckerInterface;
use PrestaShop\PrestaShop\Core\Pricing\Product\Calculator\ProductCalculatorInterface;
use RuntimeException;

final class ProductQuantityDiscountProviderTest extends TestCase
{
    public function testIsNewPricingEnabledUsesFeatureFlagStateChecker(): void
    {
        $featureFlagStateChecker = $this->createMock(FeatureFlagStateCheckerInterface::class);
        $featureFlagStateChecker
            ->expects(self::once())
            ->method('isEnabled')
            ->with(FeatureFlagSettings::FEATURE_FLAG_NEW_PRICING)
            ->willReturn(true);

        $provider = new ProductQuantityDiscountProvider(
            $featureFlagStateChecker,
            $this->createMock(ProductCalculatorInterface::class),
        );

        self::assertTrue($provider->isNewPricingEnabled());
    }

    public function testIsNewPricingEnabledFallsBackToFalseWhenCheckerFails(): void
    {
        $featureFlagStateChecker = $this->createMock(FeatureFlagStateCheckerInterface::class);
        $featureFlagStateChecker
            ->expects(self::once())
            ->method('isEnabled')
            ->with(FeatureFlagSettings::FEATURE_FLAG_NEW_PRICING)
            ->willThrowException(new RuntimeException('Feature flag unavailable'));

        $provider = new ProductQuantityDiscountProvider(
            $featureFlagStateChecker,
            $this->createMock(ProductCalculatorInterface::class),
        );

        self::assertFalse($provider->isNewPricingEnabled());
    }
}
