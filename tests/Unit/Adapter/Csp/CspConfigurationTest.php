<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Tests\Unit\Adapter\Csp;

use PHPUnit\Framework\TestCase;
use PrestaShop\PrestaShop\Adapter\Configuration;
use PrestaShop\PrestaShop\Adapter\Csp\CspConfiguration;
use PrestaShop\PrestaShop\Adapter\Shop\Context;
use PrestaShop\PrestaShop\Core\Domain\Shop\ValueObject\ShopConstraint;
use PrestaShop\PrestaShop\Core\Feature\FeatureInterface;
use PrestaShopBundle\Entity\Repository\CspRuleRepository;
use Symfony\Contracts\Translation\TranslatorInterface;

class CspConfigurationTest extends TestCase
{
    private const SHOP_ID = 1;

    public function testItRefusesToEnforceOnAShopWithNoBaseline(): void
    {
        $configuration = $this->createMock(Configuration::class);
        // The dangerous save must never reach the configuration store.
        $configuration->expects($this->never())->method('set');

        $errors = $this->cspConfiguration($configuration)
            ->updateConfiguration(['enabled' => true, 'report_only' => false, 'retention_days' => 0]);

        $this->assertNotEmpty($errors, 'Enforcing with no baseline (no allowed sources) must be rejected with an error');
    }

    public function testItRefusesToEnforceWithCollectedReportsButNoAllowedSources(): void
    {
        $configuration = $this->createMock(Configuration::class);
        // Collected violations are not a baseline on their own: enforcing would block every reported
        // source that was never allowed. The dangerous save must never reach the store.
        $configuration->expects($this->never())->method('set');

        $errors = $this->cspConfiguration($configuration)
            ->updateConfiguration(['enabled' => true, 'report_only' => false, 'retention_days' => 0]);

        $this->assertNotEmpty($errors, 'Reports alone must not let a shop enforce without an allow-list');
    }

    public function testItAllowsEnablingReportOnlyModeWithNoReports(): void
    {
        $configuration = $this->createMock(Configuration::class);
        $configuration->expects($this->exactly(3))->method('set');

        // The safe path (report-only on) must not be blocked even with an empty log.
        $errors = $this->cspConfiguration($configuration)
            ->updateConfiguration(['enabled' => true, 'report_only' => true, 'retention_days' => 0]);

        $this->assertSame([], $errors);
    }

    public function testItLetsAnAlreadyEnforcingShopSaveEvenWithAnEmptyLog(): void
    {
        $configuration = $this->createMock(Configuration::class);
        // The shop is already enforcing (enabled on, report-only off) — e.g. it enforced, then the
        // merchant cleared the log. Re-saving the same enforcing state must not be rejected.
        $configuration->method('get')->willReturnCallback(
            fn (string $key, $default = null) => match ($key) {
                'PS_CSP_ENABLED' => '1',
                'PS_CSP_REPORT_ONLY' => '0',
                default => $default,
            }
        );
        $configuration->expects($this->exactly(3))->method('set');

        $errors = $this->cspConfiguration($configuration)
            ->updateConfiguration(['enabled' => true, 'report_only' => false, 'retention_days' => 0]);

        $this->assertSame([], $errors);
    }

    public function testItLetsAShopEnforceWhenItHasCuratedRulesButAnEmptyLog(): void
    {
        $configuration = $this->createMock(Configuration::class);
        $configuration->expects($this->exactly(3))->method('set');

        // Log is empty (e.g. cleared after curating, or sources added manually), but an allow-list
        // exists — enforcement must not be blocked.
        $errors = $this->cspConfiguration($configuration, ruleCount: 3)
            ->updateConfiguration(['enabled' => true, 'report_only' => false, 'retention_days' => 0]);

        $this->assertSame([], $errors);
    }

    public function testItRefusesToTurnOnEnforcementInAllShopContext(): void
    {
        $configuration = $this->createMock(Configuration::class);
        // The dangerous all-shop enforce transition must never reach the store.
        $configuration->expects($this->never())->method('set');

        $shopContext = $this->createMock(Context::class);
        $shopContext->method('getShopConstraint')->willReturn(ShopConstraint::allShops());

        $ruleRepository = $this->createMock(CspRuleRepository::class);
        $ruleRepository->method('getRulesByShop')->willReturn([]);
        $translator = $this->createMock(TranslatorInterface::class);
        $translator->method('trans')->willReturn('error message');

        $config = new CspConfiguration(
            $configuration,
            $shopContext,
            $this->createMock(FeatureInterface::class),
            $ruleRepository,
            $translator
        );

        $errors = $config->updateConfiguration(['enabled' => true, 'report_only' => false, 'retention_days' => 0]);

        $this->assertNotEmpty($errors, 'Enforcing across all shops at once must be refused');
    }

    public function testItRefusesToTurnOnEnforcementInAGroupShopContext(): void
    {
        $configuration = $this->createMock(Configuration::class);
        // A group scope has no single shop to check a baseline for, so the enforce transition is
        // refused just like all-shops; it must never reach the store.
        $configuration->expects($this->never())->method('set');

        $shopContext = $this->createMock(Context::class);
        $shopContext->method('getShopConstraint')->willReturn(ShopConstraint::shopGroup(1));

        $config = new CspConfiguration(
            $configuration,
            $shopContext,
            $this->createMock(FeatureInterface::class),
            $this->createMock(CspRuleRepository::class),
            $this->createMock(TranslatorInterface::class)
        );

        $errors = $config->updateConfiguration(['enabled' => true, 'report_only' => false, 'retention_days' => 0]);

        $this->assertNotEmpty($errors, 'Enforcing across a group of shops must be refused');
    }

    public function testItDoesNotTreatAnInheritedEnforcingParentAsTheChildShopsOwnBaseline(): void
    {
        $configuration = $this->createMock(Configuration::class);
        // Non-strict reads fall back to the enforcing parent; the child shop has no own (strict) override,
        // so a strict read returns the default. isAlreadyEnforcing must use the strict read, otherwise a
        // child with no baseline would inherit "already enforcing" and skip the guard.
        $configuration->method('get')->willReturnCallback(
            function (string $key, $default = null, ?ShopConstraint $shopConstraint = null) {
                if (null !== $shopConstraint && $shopConstraint->isStrict()) {
                    return $default;
                }

                return match ($key) {
                    'PS_CSP_ENABLED' => '1',
                    'PS_CSP_REPORT_ONLY' => '0',
                    default => $default,
                };
            }
        );
        $configuration->expects($this->never())->method('set');

        $errors = $this->cspConfiguration($configuration)
            ->updateConfiguration(['enabled' => true, 'report_only' => false, 'retention_days' => 0]);

        $this->assertNotEmpty($errors, 'Inherited enforcement must not bypass the per-shop baseline guard');
    }

    public function testItAllowsSavingReportOnlyModeInAllShopContext(): void
    {
        $configuration = $this->createMock(Configuration::class);
        // The baseline guard only fences the enforce transition; the safe report-only save across all
        // shops must still go through to the store.
        $configuration->expects($this->exactly(3))->method('set');

        $shopContext = $this->createMock(Context::class);
        $shopContext->method('getShopConstraint')->willReturn(ShopConstraint::allShops());

        $config = new CspConfiguration(
            $configuration,
            $shopContext,
            $this->createMock(FeatureInterface::class),
            $this->createMock(CspRuleRepository::class),
            $this->createMock(TranslatorInterface::class)
        );

        $errors = $config->updateConfiguration(['enabled' => true, 'report_only' => true, 'retention_days' => 0]);

        $this->assertSame([], $errors);
    }

    private function cspConfiguration(Configuration $configuration, int $ruleCount = 0): CspConfiguration
    {
        $shopContext = $this->createMock(Context::class);
        $shopContext->method('isAllShopContext')->willReturn(false);
        $shopContext->method('getShopConstraint')->willReturn(ShopConstraint::shop(self::SHOP_ID));

        $multistoreFeature = $this->createMock(FeatureInterface::class);
        $multistoreFeature->method('isUsed')->willReturn(false);

        // The baseline is the curated allow-list only: a shop may enforce once it has at least one rule.
        $ruleRepository = $this->createMock(CspRuleRepository::class);
        $ruleRepository->method('existsByShop')->with(self::SHOP_ID)->willReturn($ruleCount > 0);

        $translator = $this->createMock(TranslatorInterface::class);
        $translator->method('trans')->willReturn('error message');

        return new CspConfiguration($configuration, $shopContext, $multistoreFeature, $ruleRepository, $translator);
    }
}
