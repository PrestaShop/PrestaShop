<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Tests\Unit\Adapter\Kpi\Refresh;

use PrestaShop\PrestaShop\Adapter\Kpi\Refresh\EnabledLanguagesKpiRefreshProvider;
use PrestaShop\PrestaShop\Core\Context\ShopContext;

class EnabledLanguagesKpiRefreshProviderTest extends AbstractKpiRefreshProviderTestCase
{
    public function testGetValueCountsTheActiveLanguagesOfTheCurrentShop(): void
    {
        $currentShop = $this->createMock(ShopContext::class);
        $currentShop->method('getId')->willReturn(3);
        $this->statsRepository->expects($this->once())
            ->method('countActiveLanguages')
            ->with(3)
            ->willReturn(2);

        $provider = new EnabledLanguagesKpiRefreshProvider($this->statsRepository, $currentShop, $this->kpiConfiguration);

        $this->assertSame('2', $provider->getValue()->getValue());
        $this->assertCachedValue('ENABLED_LANGUAGES', 2, '+1 min');
    }
}
