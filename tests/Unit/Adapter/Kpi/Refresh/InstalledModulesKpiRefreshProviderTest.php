<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Tests\Unit\Adapter\Kpi\Refresh;

use PrestaShop\PrestaShop\Adapter\Kpi\Refresh\InstalledModulesKpiRefreshProvider;

class InstalledModulesKpiRefreshProviderTest extends AbstractKpiRefreshProviderTestCase
{
    public function testGetValueReturnsAndCachesTheRepositoryCount(): void
    {
        $this->statsRepository->expects($this->once())
            ->method('countInstalledModules')
            ->with(self::SHOP_IDS)
            ->willReturn(7);

        $provider = new InstalledModulesKpiRefreshProvider($this->statsRepository, $this->shopContext, $this->kpiConfiguration);
        $value = $provider->getValue();

        $this->assertSame('7', $value->getValue());
        $this->assertNull($value->getTooltip());
        $this->assertCachedValue('INSTALLED_MODULES', 7, '+2 min');
    }
}
