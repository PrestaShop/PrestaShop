<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Tests\Unit\Adapter\Csp;

use PHPUnit\Framework\TestCase;
use PrestaShop\PrestaShop\Adapter\Csp\CspFeatureChecker;
use PrestaShop\PrestaShop\Adapter\Csp\CspHeaderBuilder;
use PrestaShop\PrestaShop\Adapter\Csp\CspPolicyProvider;
use PrestaShop\PrestaShop\Core\Csp\CspPolicyHookDispatcherInterface;
use PrestaShop\PrestaShop\Core\Domain\Configuration\ShopConfigurationInterface;
use PrestaShop\PrestaShop\Core\Domain\Csp\ValueObject\CspContext;
use PrestaShop\PrestaShop\Core\FeatureFlag\FeatureFlagStateCheckerInterface;
use PrestaShopBundle\Entity\Repository\CspRuleRepository;
use Psr\Log\LoggerInterface;

class CspHeaderBuilderTest extends TestCase
{
    private const SHOP_ID = 1;
    private const REPORT_URI = 'https://shop.example.com/?controller=cspreport';

    public function testItBuildsReportOnlyHeadersWhenReportOnlyIsOn(): void
    {
        $headers = $this->builder(flagEnabled: true, cspEnabled: true, reportOnly: true)->build(CspContext::FRONT, self::SHOP_ID, self::REPORT_URI);

        $this->assertArrayHasKey('Content-Security-Policy-Report-Only', $headers);
        $this->assertArrayNotHasKey('Content-Security-Policy', $headers, 'Report-only mode must not enforce');

        $policy = $headers['Content-Security-Policy-Report-Only'];
        $this->assertStringContainsString("default-src 'self'", $policy);
        $this->assertStringContainsString('report-uri ' . self::REPORT_URI, $policy);
        $this->assertStringContainsString('report-to csp-endpoint', $policy);

        // script-src/style-src carry 'report-sample' so browsers include a snippet of the offending inline
        // code in the report; it is reporting-only and must not appear on any other directive.
        $this->assertStringContainsString("script-src 'self' 'report-sample'", $policy);
        $this->assertStringContainsString("style-src 'self' 'report-sample'", $policy);
        $this->assertStringNotContainsString("default-src 'self' 'report-sample'", $policy);

        $this->assertSame('csp-endpoint="' . self::REPORT_URI . '"', $headers['Reporting-Endpoints']);
    }

    public function testItBuildsTheEnforcedHeaderWhenReportOnlyIsOff(): void
    {
        $headers = $this->builder(flagEnabled: true, cspEnabled: true, reportOnly: false)->build(CspContext::FRONT, self::SHOP_ID, self::REPORT_URI);

        $this->assertArrayHasKey('Content-Security-Policy', $headers);
        $this->assertArrayNotHasKey('Content-Security-Policy-Report-Only', $headers, 'Enforcement must not use the report-only header');

        // Reports are still collected under enforcement.
        $policy = $headers['Content-Security-Policy'];
        $this->assertStringContainsString('report-uri ' . self::REPORT_URI, $policy);
        $this->assertStringContainsString('report-to csp-endpoint', $policy);
        $this->assertSame('csp-endpoint="' . self::REPORT_URI . '"', $headers['Reporting-Endpoints']);
    }

    public function testItBuildsNothingWhenTheSettingIsOff(): void
    {
        $this->assertSame([], $this->builder(flagEnabled: true, cspEnabled: false)->build(CspContext::FRONT, self::SHOP_ID, self::REPORT_URI));
    }

    public function testItBuildsNothingWhenTheFeatureFlagIsOff(): void
    {
        $this->assertSame([], $this->builder(flagEnabled: false, cspEnabled: true)->build(CspContext::FRONT, self::SHOP_ID, self::REPORT_URI));
    }

    public function testAnExternalReportingEndpointReplacesTheBuiltInCollector(): void
    {
        $external = 'https://o1.ingest.example.com/api/9/security/?key=abc';
        $headers = $this->builder(flagEnabled: true, cspEnabled: true, reportOnly: true, externalReportUri: $external)
            ->build(CspContext::FRONT, self::SHOP_ID, self::REPORT_URI);

        $this->assertSame('csp-endpoint="' . $external . '"', $headers['Reporting-Endpoints']);
        $policy = $headers['Content-Security-Policy-Report-Only'];
        $this->assertStringContainsString('report-uri ' . $external, $policy);
        $this->assertStringNotContainsString(self::REPORT_URI, $policy, 'The internal collector must not be emitted when an endpoint is configured');
    }

    public function testAnInvalidExternalEndpointFallsBackToTheCollector(): void
    {
        $headers = $this->builder(flagEnabled: true, cspEnabled: true, reportOnly: true, externalReportUri: 'not a url')
            ->build(CspContext::FRONT, self::SHOP_ID, self::REPORT_URI);

        $this->assertSame('csp-endpoint="' . self::REPORT_URI . '"', $headers['Reporting-Endpoints']);
        $this->assertStringContainsString('report-uri ' . self::REPORT_URI, $headers['Content-Security-Policy-Report-Only']);
    }

    public function testItStripsHeaderBreakingCharactersFromTheReportUri(): void
    {
        // A URI carrying quotes, semicolons, commas or whitespace must never corrupt a header line
        // or inject an extra directive.
        $headers = $this->builder(flagEnabled: true, cspEnabled: true, reportOnly: true)
            ->build(CspContext::FRONT, self::SHOP_ID, 'https://shop.example.com/r";script-src *, evil');

        $cleanUri = 'https://shop.example.com/rscript-src*evil';
        $this->assertSame('csp-endpoint="' . $cleanUri . '"', $headers['Reporting-Endpoints']);
        $this->assertStringContainsString('report-uri ' . $cleanUri, $headers['Content-Security-Policy-Report-Only']);
        $this->assertStringNotContainsString('";', $headers['Content-Security-Policy-Report-Only']);
    }

    private function builder(bool $flagEnabled, bool $cspEnabled, bool $reportOnly = true, string $externalReportUri = ''): CspHeaderBuilder
    {
        $featureFlagChecker = $this->createMock(FeatureFlagStateCheckerInterface::class);
        $featureFlagChecker->method('isEnabled')->willReturn($flagEnabled);

        $configuration = $this->createMock(ShopConfigurationInterface::class);
        $configuration->method('get')->willReturnCallback(
            static fn (string $key, $default = null) => match ($key) {
                'PS_CSP_ENABLED' => $cspEnabled,
                'PS_CSP_REPORT_ONLY' => $reportOnly,
                'PS_CSP_REPORT_URI' => $externalReportUri,
                default => $default,
            }
        );

        $ruleRepository = $this->createMock(CspRuleRepository::class);
        $ruleRepository->method('getRulesByShop')->willReturn([]);

        return new CspHeaderBuilder(
            new CspFeatureChecker($featureFlagChecker, $configuration),
            new CspPolicyProvider(
                $ruleRepository,
                $this->createMock(CspPolicyHookDispatcherInterface::class),
                $this->createMock(LoggerInterface::class)
            )
        );
    }
}
