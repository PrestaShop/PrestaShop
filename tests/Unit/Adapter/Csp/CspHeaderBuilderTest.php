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
use PrestaShop\PrestaShop\Core\FeatureFlag\FeatureFlagStateCheckerInterface;
use PrestaShopBundle\Entity\Repository\CspRuleRepository;
use Psr\Log\LoggerInterface;

class CspHeaderBuilderTest extends TestCase
{
    private const SHOP_ID = 1;
    private const REPORT_URI = 'https://shop.example.com/?controller=cspreport';

    public function testItBuildsReportOnlyHeadersWhenEnabled(): void
    {
        $headers = $this->builder(flagEnabled: true, cspEnabled: true)->build(self::SHOP_ID, self::REPORT_URI);

        $this->assertArrayHasKey('Content-Security-Policy-Report-Only', $headers);
        $this->assertArrayNotHasKey('Content-Security-Policy', $headers, 'This phase never enforces');

        $policy = $headers['Content-Security-Policy-Report-Only'];
        $this->assertStringContainsString("default-src 'self'", $policy);
        $this->assertStringContainsString('report-uri ' . self::REPORT_URI, $policy);
        $this->assertStringContainsString('report-to csp-endpoint', $policy);

        $this->assertSame('csp-endpoint="' . self::REPORT_URI . '"', $headers['Reporting-Endpoints']);
    }

    public function testItBuildsNothingWhenTheSettingIsOff(): void
    {
        $this->assertSame([], $this->builder(flagEnabled: true, cspEnabled: false)->build(self::SHOP_ID, self::REPORT_URI));
    }

    public function testItBuildsNothingWhenTheFeatureFlagIsOff(): void
    {
        $this->assertSame([], $this->builder(flagEnabled: false, cspEnabled: true)->build(self::SHOP_ID, self::REPORT_URI));
    }

    private function builder(bool $flagEnabled, bool $cspEnabled): CspHeaderBuilder
    {
        $featureFlagChecker = $this->createMock(FeatureFlagStateCheckerInterface::class);
        $featureFlagChecker->method('isEnabled')->willReturn($flagEnabled);

        $configuration = $this->createMock(ShopConfigurationInterface::class);
        $configuration->method('get')->willReturn($cspEnabled);

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
