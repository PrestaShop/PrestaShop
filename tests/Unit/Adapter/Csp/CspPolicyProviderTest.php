<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Tests\Unit\Adapter\Csp;

use PHPUnit\Framework\TestCase;
use PrestaShop\PrestaShop\Adapter\Csp\CspPolicyProvider;
use PrestaShop\PrestaShop\Core\Csp\CspPolicy;
use PrestaShop\PrestaShop\Core\Csp\CspPolicyHookDispatcherInterface;
use PrestaShop\PrestaShop\Core\Domain\Csp\ValueObject\CspContext;
use PrestaShopBundle\Entity\Repository\CspRuleRepository;
use Psr\Log\LoggerInterface;

class CspPolicyProviderTest extends TestCase
{
    private const SHOP_ID = 1;

    public function testItReturnsTheBaseCollectionPolicyWithNoRulesOrTheme(): void
    {
        $directives = $this->provider()->getPolicy(CspContext::FRONT, self::SHOP_ID)->getDirectives();

        $this->assertSame(
            [
                'default-src' => ["'self'"],
                'base-uri' => ["'self'"],
                'frame-ancestors' => ["'self'"],
                'form-action' => ["'self'"],
                'object-src' => ["'none'"],
                'img-src' => ["'self'", 'data:'],
                'font-src' => ["'self'", 'data:'],
                'script-src' => ["'self'"],
                'style-src' => ["'self'"],
                'child-src' => ["'self'"],
                'connect-src' => ["'self'"],
                'frame-src' => ["'self'"],
                'manifest-src' => ["'self'"],
                'media-src' => ["'self'"],
                'worker-src' => ["'self'"],
            ],
            $directives
        );
    }

    public function testTheBasePolicyRestrictsTheDirectivesThatDoNotFallBackToDefaultSrc(): void
    {
        $directives = $this->provider()->getPolicy(CspContext::FRONT, self::SHOP_ID)->getDirectives();

        // base-uri, frame-ancestors and form-action have no default-src fallback, so they must be
        // set explicitly or they stay unrestricted under enforcement; object-src is locked to 'none'.
        $this->assertSame(["'self'"], $directives['base-uri']);
        $this->assertSame(["'self'"], $directives['frame-ancestors']);
        $this->assertSame(["'self'"], $directives['form-action']);
        $this->assertSame(["'none'"], $directives['object-src']);
    }

    public function testItMergesCuratedRulesOnTopOfTheBasePolicy(): void
    {
        $rules = [
            ['directive' => 'script-src', 'source' => 'https://cdn.example.com'],
            ['directive' => 'connect-src', 'source' => 'https://api.example.com'],
        ];

        $directives = $this->provider($rules)->getPolicy(CspContext::FRONT, self::SHOP_ID)->getDirectives();

        // Both augment the base 'self' rather than replacing it.
        $this->assertSame(["'self'", 'https://cdn.example.com'], $directives['script-src']);
        $this->assertSame(["'self'", 'https://api.example.com'], $directives['connect-src']);
    }

    public function testItMergesValidThemeContributions(): void
    {
        $theme = ['script-src' => ["'unsafe-eval'"]];

        $directives = $this->provider()->getPolicy(CspContext::FRONT, self::SHOP_ID, $theme)->getDirectives();

        $this->assertSame(["'self'", "'unsafe-eval'"], $directives['script-src']);
    }

    public function testAllowingAReportedInlineSourceKeepsSelfSoSameOriginAssetsStillLoad(): void
    {
        // Regression: a merchant allowing the reported 'unsafe-inline' must not drop 'self' from
        // script-src/style-src, or the theme's own same-origin scripts and styles would be blocked
        // under enforcement (an explicit directive cancels the default-src fallback).
        $rules = [
            ['directive' => 'script-src', 'source' => "'unsafe-inline'"],
            ['directive' => 'style-src', 'source' => "'unsafe-inline'"],
        ];

        $directives = $this->provider($rules)->getPolicy(CspContext::FRONT, self::SHOP_ID)->getDirectives();

        $this->assertSame(["'self'", "'unsafe-inline'"], $directives['script-src']);
        $this->assertSame(["'self'", "'unsafe-inline'"], $directives['style-src']);
    }

    public function testItSkipsAndLogsAnUnknownThemeDirective(): void
    {
        $logger = $this->createMock(LoggerInterface::class);
        $logger->expects($this->once())->method('warning');

        $theme = ['not-a-directive' => ["'self'"]];
        $directives = $this->provider([], $logger)->getPolicy(CspContext::FRONT, self::SHOP_ID, $theme)->getDirectives();

        $this->assertArrayNotHasKey('not-a-directive', $directives);
    }

    public function testItSkipsAndLogsAnInvalidThemeSource(): void
    {
        $logger = $this->createMock(LoggerInterface::class);
        $logger->expects($this->once())->method('warning');

        $theme = ['script-src' => ['has space']];
        $directives = $this->provider([], $logger)->getPolicy(CspContext::FRONT, self::SHOP_ID, $theme)->getDirectives();

        // The invalid source is skipped; the base 'self' remains on script-src.
        $this->assertSame(["'self'"], $directives['script-src']);
    }

    public function testItDispatchesThePolicyHook(): void
    {
        $hookDispatcher = $this->createMock(CspPolicyHookDispatcherInterface::class);
        $hookDispatcher->expects($this->once())->method('dispatch')->with($this->isInstanceOf(CspPolicy::class));

        $this->provider([], null, $hookDispatcher)->getPolicy(CspContext::FRONT, self::SHOP_ID);
    }

    public function testTheBackOfficePolicySkipsThemeContributionsAndTheStorefrontHook(): void
    {
        // The back office is a core-owned surface: no theme CSP, and the storefront policy hook (which
        // lets modules add storefront sources) must not run for it.
        $hookDispatcher = $this->createMock(CspPolicyHookDispatcherInterface::class);
        $hookDispatcher->expects($this->never())->method('dispatch');

        $theme = ['script-src' => ['https://theme.example.com']];
        $directives = $this->provider([], null, $hookDispatcher)->getPolicy(CspContext::ADMIN, 0, $theme)->getDirectives();

        // The theme source is absent; the predefined first-party hosts remain (see the dedicated test).
        $this->assertNotContains('https://theme.example.com', $directives['script-src'], 'A theme contribution must not reach the admin policy');
        $this->assertContains("'self'", $directives['script-src']);
    }

    public function testTheBackOfficePolicyPreAllowsPrestaShopFirstPartyResources(): void
    {
        // The back office loads a handful of PrestaShop-owned resources; the admin base pre-allows them
        // so a merchant does not have to curate core's own first-party domains.
        $admin = $this->provider()->getPolicy(CspContext::ADMIN, 0)->getDirectives();

        $this->assertSame(["'self'", 'https://storage.googleapis.com', 'https://assets.prestashop3.com'], $admin['script-src']);
        $this->assertSame(["'self'", 'https://fonts.googleapis.com'], $admin['style-src']);
        $this->assertSame(["'self'", 'data:', 'https://fonts.gstatic.com'], $admin['font-src']);
        // The distribution client fetches the project APIs via XHR, so the host belongs on connect-src, not img-src.
        $this->assertSame(["'self'", 'https://*.prestashop-project.org'], $admin['connect-src']);
        $this->assertSame(["'self'", 'data:', 'https://*.prestashop.com', 'https://*.gravatar.com'], $admin['img-src']);
        $this->assertSame(["'self'", 'https://*.prestashop.com'], $admin['frame-src']);

        // No weakening keyword is pre-baked: admin script-src stays host-only, inline/eval are curated.
        $this->assertNotContains("'unsafe-inline'", $admin['script-src']);
        $this->assertNotContains("'unsafe-eval'", $admin['script-src']);

        // The storefront keeps the tight base: none of these first-party hosts leak onto it.
        $front = $this->provider()->getPolicy(CspContext::FRONT, self::SHOP_ID)->getDirectives();
        $this->assertSame(["'self'"], $front['script-src']);
        $this->assertSame(["'self'", 'data:'], $front['img-src']);
        $this->assertSame(["'self'"], $front['frame-src']);
        $this->assertSame(["'self'"], $front['connect-src']);
    }

    public function testItExposesTheBuiltInAdminSourcesFlattenedForTheUi(): void
    {
        $rows = CspPolicyProvider::getAdminFirstPartySources();

        // Every flattened {directive, source} pair must match what the admin policy actually pre-allows,
        // so the read-only "Built-in sources" panel can never drift from the emitted header.
        $this->assertContains(['directive' => 'script-src', 'source' => 'https://storage.googleapis.com'], $rows);
        $this->assertContains(['directive' => 'img-src', 'source' => 'https://*.gravatar.com'], $rows);
        $this->assertCount(8, $rows);

        $admin = $this->provider()->getPolicy(CspContext::ADMIN, 0)->getDirectives();
        foreach ($rows as $row) {
            $this->assertContains($row['source'], $admin[$row['directive']], sprintf('Built-in %s %s must be in the emitted admin policy', $row['directive'], $row['source']));
        }
    }

    /**
     * @param list<array{directive: string, source: string}> $rules
     */
    private function provider(array $rules = [], ?LoggerInterface $logger = null, ?CspPolicyHookDispatcherInterface $hookDispatcher = null): CspPolicyProvider
    {
        $repository = $this->createMock(CspRuleRepository::class);
        $repository->method('getRulesByShop')->willReturn($rules);

        return new CspPolicyProvider(
            $repository,
            $hookDispatcher ?? $this->createMock(CspPolicyHookDispatcherInterface::class),
            $logger ?? $this->createMock(LoggerInterface::class)
        );
    }
}
