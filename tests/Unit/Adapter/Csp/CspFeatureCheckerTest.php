<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Tests\Unit\Adapter\Csp;

use PHPUnit\Framework\Attributes\PreserveGlobalState;
use PHPUnit\Framework\Attributes\RunInSeparateProcess;
use PHPUnit\Framework\TestCase;
use PrestaShop\PrestaShop\Adapter\Csp\CspFeatureChecker;
use PrestaShop\PrestaShop\Core\Domain\Configuration\ShopConfigurationInterface;
use PrestaShop\PrestaShop\Core\Domain\Csp\ValueObject\CspContext;
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

    public function testTheBackOfficeReadsItsOwnGlobalEnabledKeyIndependentOfTheStorefront(): void
    {
        $checker = $this->checker(flagEnabled: true, config: ['PS_CSP_ENABLED' => '0', 'PS_CSP_ADMIN_ENABLED' => '1']);

        $this->assertTrue($checker->isEnabledForContext(CspContext::ADMIN, 0));
        $this->assertFalse($checker->isEnabledForShop(1), 'The admin toggle must not enable the storefront');
    }

    public function testTheBackOfficeIsReportOnlyByDefault(): void
    {
        $this->assertTrue($this->checker(flagEnabled: true, config: [])->isReportOnlyForContext(CspContext::ADMIN, 0));
    }

    /**
     * The lockout kill-switch runs in a separate process: defining the constant cannot be undone, so it
     * must not leak into the other tests in this class.
     */
    #[RunInSeparateProcess]
    #[PreserveGlobalState(false)]
    public function testTheKillSwitchForcesTheBackOfficeOffWithoutAffectingTheStorefront(): void
    {
        define('_PS_CSP_ADMIN_DISABLE_', true);

        $checker = $this->checker(flagEnabled: true, config: ['PS_CSP_ADMIN_ENABLED' => '1', 'PS_CSP_ENABLED' => '1']);

        $this->assertFalse($checker->isEnabledForContext(CspContext::ADMIN, 0), 'The kill-switch must disable the back office even with the admin toggle on');
        $this->assertTrue($checker->isEnabledForShop(1), 'The admin kill-switch must not affect the storefront');
    }

    public function testItReturnsAConfiguredExternalReportTarget(): void
    {
        $url = 'https://o1.ingest.example.com/api/9/security/?key=abc';

        $this->assertSame($url, $this->checker(flagEnabled: true, config: ['PS_CSP_REPORT_URI' => $url])->reportTargetForContext(CspContext::FRONT, 1));
    }

    public function testItHasNoExternalReportTargetByDefault(): void
    {
        $this->assertSame('', $this->checker(flagEnabled: true, config: [])->reportTargetForContext(CspContext::FRONT, 1));
    }

    public function testItRejectsAReportTargetThatIsNotAnAbsoluteHttpUrl(): void
    {
        foreach (['not a url', 'ftp://example.com/r', 'javascript:alert(1)', '/relative/path'] as $bad) {
            $this->assertSame('', $this->checker(flagEnabled: true, config: ['PS_CSP_REPORT_URI' => $bad])->reportTargetForContext(CspContext::FRONT, 1));
        }
    }

    public function testTheBackOfficeReadsItsOwnReportTargetKey(): void
    {
        $admin = 'https://admin-monitor.example.com/csp';
        $checker = $this->checker(flagEnabled: true, config: ['PS_CSP_REPORT_URI' => 'https://front.example.com/csp', 'PS_CSP_ADMIN_REPORT_URI' => $admin]);

        $this->assertSame($admin, $checker->reportTargetForContext(CspContext::ADMIN, 0));
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
