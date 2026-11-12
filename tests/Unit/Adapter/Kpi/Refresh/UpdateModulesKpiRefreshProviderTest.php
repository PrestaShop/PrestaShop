<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Tests\Unit\Adapter\Kpi\Refresh;

use Context;
use Employee;
use PrestaShop\PrestaShop\Adapter\Kpi\Refresh\UpdateModulesKpiRefreshProvider;
use PrestaShop\PrestaShop\Adapter\LegacyContext;
use stdClass;

class UpdateModulesKpiRefreshProviderTest extends AbstractKpiRefreshProviderTestCase
{
    public function testGetValueCountsInstalledModulesWithAnAvailableUpdate(): void
    {
        $employee = $this->createMock(Employee::class);
        $employee->id = 7;
        $context = $this->createMock(Context::class);
        $context->employee = $employee;
        $legacyContext = $this->createMock(LegacyContext::class);
        $legacyContext->method('getContext')->willReturn($context);

        $provider = $this->getMockBuilder(UpdateModulesKpiRefreshProvider::class)
            ->setConstructorArgs([$legacyContext, $this->kpiConfiguration])
            ->onlyMethods(['getModulesOnDisk'])
            ->getMock();
        $provider->expects($this->once())
            ->method('getModulesOnDisk')
            ->with(7)
            ->willReturn([
                $this->createModule(true, '2.0.0'),
                $this->createModule(true, '1.1.0'),
                $this->createModule(true, null),
                $this->createModule(false, '3.0.0'),
            ]);

        $this->assertSame('2', $provider->getValue()->getValue());
        $this->assertCachedValue('UPDATE_MODULES', 2, '+2 min');
    }

    private function createModule(bool $installed, ?string $addonsVersion): stdClass
    {
        $module = new stdClass();
        $module->installed = $installed;
        if (null !== $addonsVersion) {
            $module->version_addons = $addonsVersion;
        }

        return $module;
    }
}
