<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Tests\Unit\Adapter\Kpi\Refresh;

use PrestaShop\PrestaShop\Adapter\Kpi\Refresh\EmptyCategoriesKpiRefreshProvider;

class EmptyCategoriesKpiRefreshProviderTest extends AbstractKpiRefreshProviderTestCase
{
    public function testGetValueExcludesTheRootCategoryReadFromShopConfiguration(): void
    {
        $this->statsRepository->expects($this->once())
            ->method('countEmptyCategories')
            ->with(1, self::SHOP_IDS)
            ->willReturn(4);
        // Shop settings live in the main configuration table, not in the KPI one
        $this->kpiConfiguration->expects($this->never())->method('get');

        $provider = new EmptyCategoriesKpiRefreshProvider(
            $this->statsRepository,
            $this->shopContext,
            $this->kpiConfiguration,
            $this->createShopConfiguration(['PS_ROOT_CATEGORY' => '1'])
        );

        $this->assertSame('4', $provider->getValue()->getValue());
        $this->assertCachedValue('EMPTY_CATEGORIES', 4, '+2 hour');
    }
}
