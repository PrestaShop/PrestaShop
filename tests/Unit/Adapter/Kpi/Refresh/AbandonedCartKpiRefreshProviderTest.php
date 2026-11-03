<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Tests\Unit\Adapter\Kpi\Refresh;

use PrestaShop\PrestaShop\Adapter\Kpi\Refresh\AbandonedCartKpiRefreshProvider;

class AbandonedCartKpiRefreshProviderTest extends AbstractKpiRefreshProviderTestCase
{
    public function testGetValueReturnsAndCachesTheRepositoryCount(): void
    {
        $this->statsRepository->expects($this->once())
            ->method('countAbandonedCarts')
            ->with(
                $this->callback(static fn (string $date) => abs(strtotime($date) - strtotime('-2 day')) < 5),
                $this->callback(static fn (string $date) => abs(strtotime($date) - strtotime('-1 day')) < 5),
                self::SHOP_IDS
            )
            ->willReturn(7);

        $provider = new AbandonedCartKpiRefreshProvider($this->statsRepository, $this->shopContext, $this->kpiConfiguration);
        $value = $provider->getValue();

        $this->assertSame('7', $value->getValue());
        $this->assertNull($value->getTooltip());
        $this->assertCachedValue('ABANDONED_CARTS', 7, '+1 hour');
    }
}
