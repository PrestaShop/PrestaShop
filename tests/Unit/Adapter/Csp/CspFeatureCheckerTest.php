<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Tests\Unit\Adapter\Csp;

use PHPUnit\Framework\TestCase;
use PrestaShop\PrestaShop\Adapter\Csp\CspFeatureChecker;
use PrestaShop\PrestaShop\Core\Domain\Configuration\ShopConfigurationInterface;
use PrestaShop\PrestaShop\Core\FeatureFlag\FeatureFlagStateCheckerInterface;

/**
 * The gate shared by the header builder and the collector: CSP is active only when the feature flag
 * and the per-shop PS_CSP_ENABLED are both on, and a shop is report-only unless it opted in.
 */
class CspFeatureCheckerTest extends TestCase
{
    public function testItIsEnabledForAShopOnlyWhenTheFlagAndThePerShopToggleAreBothOn(): void
    {
        $this->assertTrue($this->checker(flagEnabled: true, config: ['PS_CSP_ENABLED' => '1'])->isEnabledForShop(1));
    }

    public function testItIsDisabledWhenTheFeatureFlagIsOffRegardlessOfTheShopToggle(): void
    {
        $this->assertFalse($this->checker(flagEnabled: false, config: ['PS_CSP_ENABLED' => '1'])->isEnabledForShop(1));
    }

    public function testItIsDisabledWhenTheShopToggleIsOffEvenWithTheFlagOn(): void
    {
        $this->assertFalse($this->checker(flagEnabled: true, config: ['PS_CSP_ENABLED' => '0'])->isEnabledForShop(1));
    }

    public function testItReportsTheFeatureFlagState(): void
    {
        $this->assertTrue($this->checker(flagEnabled: true, config: [])->isFeatureFlagEnabled());
        $this->assertFalse($this->checker(flagEnabled: false, config: [])->isFeatureFlagEnabled());
    }

    public function testAShopIsReportOnlyByDefault(): void
    {
        // No stored PS_CSP_REPORT_ONLY: the default (true) must win so a shop never blocks unasked.
        $this->assertTrue($this->checker(flagEnabled: true, config: [])->isReportOnlyForShop(1));
    }

    public function testAShopCanTurnReportOnlyOff(): void
    {
        $this->assertFalse($this->checker(flagEnabled: true, config: ['PS_CSP_REPORT_ONLY' => '0'])->isReportOnlyForShop(1));
    }

    /**
     * @param array<string, string> $config
     */
    private function checker(bool $flagEnabled, array $config): CspFeatureChecker
    {
        $flagChecker = $this->createMock(FeatureFlagStateCheckerInterface::class);
        $flagChecker->method('isEnabled')->willReturn($flagEnabled);

        $configuration = $this->createMock(ShopConfigurationInterface::class);
        $configuration->method('get')->willReturnCallback(
            fn (string $key, $default = null) => $config[$key] ?? $default
        );

        return new CspFeatureChecker($flagChecker, $configuration);
    }
}
