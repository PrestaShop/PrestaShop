<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Tests\Unit\Adapter\Csp;

use PHPUnit\Framework\TestCase;
use PrestaShop\PrestaShop\Adapter\Csp\AdminCspConfiguration;
use PrestaShop\PrestaShop\Core\Domain\Configuration\ShopConfigurationInterface;
use PrestaShop\PrestaShop\Core\Domain\Csp\ValueObject\CspContext;
use PrestaShop\PrestaShop\Core\Domain\Shop\ValueObject\ShopConstraint;
use PrestaShopBundle\Entity\Repository\CspRuleRepository;
use Symfony\Contracts\Translation\TranslatorInterface;

/**
 * The back-office settings are global (one admin for the whole installation): every value is read and
 * written at the all-shops scope, never the shop selected in the back office, so the form and the header
 * agree in multistore. Enforcement is fenced by the same rules-only baseline guard as the storefront,
 * scoped to the admin allow-list.
 */
class AdminCspConfigurationTest extends TestCase
{
    public function testItRefusesToEnforceWithoutAnAdminAllowList(): void
    {
        $configuration = $this->createMock(ShopConfigurationInterface::class);
        $configuration->expects($this->never())->method('set');

        $errors = $this->adminConfiguration($configuration, hasAdminRule: false)
            ->updateConfiguration(['enabled' => true, 'report_only' => false, 'retention_days' => 0]);

        $this->assertNotEmpty($errors, 'Enforcing the back office without a curated allow-list must be refused');
    }

    public function testItAllowsEnforcementOnceTheAdminAllowListHasARule(): void
    {
        $configuration = $this->createMock(ShopConfigurationInterface::class);
        $configuration->expects($this->exactly(3))->method('set');

        $errors = $this->adminConfiguration($configuration, hasAdminRule: true)
            ->updateConfiguration(['enabled' => true, 'report_only' => false, 'retention_days' => 0]);

        $this->assertSame([], $errors);
    }

    public function testItAllowsReportOnlyWithNoAllowList(): void
    {
        $configuration = $this->createMock(ShopConfigurationInterface::class);
        $configuration->expects($this->exactly(3))->method('set');

        $errors = $this->adminConfiguration($configuration, hasAdminRule: false)
            ->updateConfiguration(['enabled' => true, 'report_only' => true, 'retention_days' => 0]);

        $this->assertSame([], $errors);
    }

    public function testItChecksTheAllowListUnderTheAdminSurface(): void
    {
        $ruleRepository = $this->createMock(CspRuleRepository::class);
        // The baseline must be read from the admin surface (shop id 0), never the storefront.
        $ruleRepository->expects($this->once())->method('existsByShop')->with(CspContext::ADMIN, 0)->willReturn(false);

        $configuration = $this->createMock(ShopConfigurationInterface::class);
        $translator = $this->createMock(TranslatorInterface::class);
        $translator->method('trans')->willReturn('error message');

        $config = new AdminCspConfiguration($configuration, $ruleRepository, $translator);
        $config->updateConfiguration(['enabled' => true, 'report_only' => false, 'retention_days' => 0]);
    }

    public function testItWritesEverySettingAtTheAllShopsScope(): void
    {
        // The back office is global: in multistore the form must not write to the selected shop, or the
        // header (which reads all-shops) would never see the value. Every set() must carry allShops().
        $scopes = [];
        $configuration = $this->createMock(ShopConfigurationInterface::class);
        $configuration->method('set')->willReturnCallback(
            function (string $key, $value, ?ShopConstraint $shopConstraint = null) use (&$scopes): void {
                $scopes[$key] = $shopConstraint;
            }
        );

        $this->adminConfiguration($configuration, hasAdminRule: true)
            ->updateConfiguration(['enabled' => true, 'report_only' => true, 'retention_days' => 30]);

        $this->assertEquals(ShopConstraint::allShops(), $scopes['PS_CSP_ADMIN_ENABLED']);
        $this->assertEquals(ShopConstraint::allShops(), $scopes['PS_CSP_ADMIN_REPORT_ONLY']);
        $this->assertEquals(ShopConstraint::allShops(), $scopes['PS_CSP_ADMIN_RETENTION_DAYS']);
    }

    public function testItReadsEverySettingAtTheAllShopsScope(): void
    {
        $scopes = [];
        $configuration = $this->createMock(ShopConfigurationInterface::class);
        $configuration->method('get')->willReturnCallback(
            function (string $key, $default = null, ?ShopConstraint $shopConstraint = null) use (&$scopes) {
                $scopes[$key] = $shopConstraint;

                return $default;
            }
        );

        $this->adminConfiguration($configuration, hasAdminRule: false)->getConfiguration();

        $this->assertEquals(ShopConstraint::allShops(), $scopes['PS_CSP_ADMIN_ENABLED']);
        $this->assertEquals(ShopConstraint::allShops(), $scopes['PS_CSP_ADMIN_REPORT_ONLY']);
        $this->assertEquals(ShopConstraint::allShops(), $scopes['PS_CSP_ADMIN_RETENTION_DAYS']);
    }

    private function adminConfiguration(ShopConfigurationInterface $configuration, bool $hasAdminRule): AdminCspConfiguration
    {
        $ruleRepository = $this->createMock(CspRuleRepository::class);
        $ruleRepository->method('existsByShop')->with(CspContext::ADMIN, 0)->willReturn($hasAdminRule);

        $translator = $this->createMock(TranslatorInterface::class);
        $translator->method('trans')->willReturn('error message');

        return new AdminCspConfiguration($configuration, $ruleRepository, $translator);
    }
}
