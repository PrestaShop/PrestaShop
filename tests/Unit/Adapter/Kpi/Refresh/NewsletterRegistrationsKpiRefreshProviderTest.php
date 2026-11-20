<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Tests\Unit\Adapter\Kpi\Refresh;

use PrestaShop\PrestaShop\Adapter\Kpi\Refresh\NewsletterRegistrationsKpiRefreshProvider;
use PrestaShop\PrestaShop\Adapter\Module\ModuleDataProvider;
use PrestaShop\PrestaShop\Adapter\Shop\Context;

class NewsletterRegistrationsKpiRefreshProviderTest extends AbstractKpiRefreshProviderTestCase
{
    public function testGetValueAddsEmailSubscriptionsWhenTheModuleIsInstalled(): void
    {
        // Shops 1 and 2 share their orders
        $this->statsRepository->method('countActiveNewsletterCustomers')->with([1, 2])->willReturn(5);
        $this->statsRepository->method('countActiveEmailSubscriptions')->with([1, 2])->willReturn(3);

        $this->assertSame('8', $this->createProvider(true)->getValue()->getValue());
        $this->assertCachedValue('NEWSLETTER_REGISTRATIONS', 8, '+6 hour');
    }

    public function testGetValueCountsCustomersOnlyWhenTheModuleIsNotInstalled(): void
    {
        $this->statsRepository->method('countActiveNewsletterCustomers')->willReturn(5);
        $this->statsRepository->expects($this->never())->method('countActiveEmailSubscriptions');

        $this->assertSame('5', $this->createProvider(false)->getValue()->getValue());
    }

    private function createProvider(bool $isModuleInstalled): NewsletterRegistrationsKpiRefreshProvider
    {
        $moduleDataProvider = $this->createMock(ModuleDataProvider::class);
        $moduleDataProvider->method('isInstalled')->with('ps_emailsubscription')->willReturn($isModuleInstalled);

        $shopContext = $this->createMock(Context::class);
        $shopContext->method('getContextListShopID')->with('share_order')->willReturn(['1', '2']);

        return new NewsletterRegistrationsKpiRefreshProvider(
            $this->statsRepository,
            $shopContext,
            $this->kpiConfiguration,
            $moduleDataProvider
        );
    }
}
