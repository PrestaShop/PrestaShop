<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Tests\Unit\Adapter\Csp;

use PHPUnit\Framework\TestCase;
use PrestaShop\PrestaShop\Adapter\Csp\AdminCspConfiguration;
use PrestaShop\PrestaShop\Core\ConfigurationInterface;
use PrestaShop\PrestaShop\Core\Domain\Csp\ValueObject\CspContext;
use PrestaShopBundle\Entity\Repository\CspRuleRepository;
use Symfony\Contracts\Translation\TranslatorInterface;

/**
 * The back-office settings are global (one admin for the whole installation), and enforcement is
 * fenced by the same rules-only baseline guard as the storefront, scoped to the admin allow-list.
 */
class AdminCspConfigurationTest extends TestCase
{
    public function testItRefusesToEnforceWithoutAnAdminAllowList(): void
    {
        $configuration = $this->createMock(ConfigurationInterface::class);
        $configuration->expects($this->never())->method('set');

        $errors = $this->adminConfiguration($configuration, hasAdminRule: false)
            ->updateConfiguration(['enabled' => true, 'report_only' => false, 'retention_days' => 0, 'report_uri' => '']);

        $this->assertNotEmpty($errors, 'Enforcing the back office without a curated allow-list must be refused');
    }

    public function testItAllowsEnforcementOnceTheAdminAllowListHasARule(): void
    {
        $configuration = $this->createMock(ConfigurationInterface::class);
        $configuration->expects($this->exactly(4))->method('set');

        $errors = $this->adminConfiguration($configuration, hasAdminRule: true)
            ->updateConfiguration(['enabled' => true, 'report_only' => false, 'retention_days' => 0, 'report_uri' => '']);

        $this->assertSame([], $errors);
    }

    public function testItAllowsReportOnlyWithNoAllowList(): void
    {
        $configuration = $this->createMock(ConfigurationInterface::class);
        $configuration->expects($this->exactly(4))->method('set');

        $errors = $this->adminConfiguration($configuration, hasAdminRule: false)
            ->updateConfiguration(['enabled' => true, 'report_only' => true, 'retention_days' => 0, 'report_uri' => '']);

        $this->assertSame([], $errors);
    }

    public function testItChecksTheAllowListUnderTheAdminSurface(): void
    {
        $ruleRepository = $this->createMock(CspRuleRepository::class);
        // The baseline must be read from the admin surface (shop id 0), never the storefront.
        $ruleRepository->expects($this->once())->method('existsByShop')->with(CspContext::ADMIN, 0)->willReturn(false);

        $configuration = $this->createMock(ConfigurationInterface::class);
        $translator = $this->createMock(TranslatorInterface::class);
        $translator->method('trans')->willReturn('error message');

        $config = new AdminCspConfiguration($configuration, $ruleRepository, $translator);
        $config->updateConfiguration(['enabled' => true, 'report_only' => false, 'retention_days' => 0, 'report_uri' => '']);
    }

    public function testItStoresAndTrimsTheExternalReportTarget(): void
    {
        $written = [];
        $configuration = $this->createMock(ConfigurationInterface::class);
        $configuration->method('set')->willReturnCallback(function (string $key, $value) use (&$written): void {
            $written[$key] = $value;
        });

        $this->adminConfiguration($configuration, hasAdminRule: false)
            ->updateConfiguration(['enabled' => true, 'report_only' => true, 'retention_days' => 0, 'report_uri' => '  https://monitor.example.com/csp  ']);

        $this->assertSame('https://monitor.example.com/csp', $written['PS_CSP_ADMIN_REPORT_URI']);
    }

    private function adminConfiguration(ConfigurationInterface $configuration, bool $hasAdminRule): AdminCspConfiguration
    {
        $ruleRepository = $this->createMock(CspRuleRepository::class);
        $ruleRepository->method('existsByShop')->with(CspContext::ADMIN, 0)->willReturn($hasAdminRule);

        $translator = $this->createMock(TranslatorInterface::class);
        $translator->method('trans')->willReturn('error message');

        return new AdminCspConfiguration($configuration, $ruleRepository, $translator);
    }
}
