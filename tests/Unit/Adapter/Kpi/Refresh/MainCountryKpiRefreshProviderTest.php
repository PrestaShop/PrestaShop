<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Tests\Unit\Adapter\Kpi\Refresh;

use PrestaShop\PrestaShop\Adapter\Kpi\Refresh\MainCountryKpiRefreshProvider;

class MainCountryKpiRefreshProviderTest extends AbstractKpiRefreshProviderTestCase
{
    public function testGetValueWithoutOrdersDoesNotLookForTheMainCountry(): void
    {
        $this->statsRepository->method('countOrders')->willReturn(0);
        // Would divide by zero: invoiced orders exist, none of them is valid
        $this->statsRepository->expects($this->never())->method('getMainCountry');

        $provider = new MainCountryKpiRefreshProvider(
            $this->statsRepository,
            $this->shopContext,
            $this->kpiConfiguration,
            $this->createTranslator(),
            $this->createLanguageContext()
        );

        $this->assertSame('No orders', $provider->getValue()->getValue());
        $this->assertCachedValuePerLanguage('MAIN_COUNTRY', 'No orders', '+1 day');
    }

    public function testGetValueWithoutInvoicedOrdersReturnsNoOrders(): void
    {
        $this->statsRepository->method('countOrders')->willReturn(3);
        $this->statsRepository->method('getMainCountry')->willReturn(null);

        $provider = new MainCountryKpiRefreshProvider(
            $this->statsRepository,
            $this->shopContext,
            $this->kpiConfiguration,
            $this->createTranslator(),
            $this->createLanguageContext()
        );

        $this->assertSame('No orders', $provider->getValue()->getValue());
    }

    public function testGetValueReturnsTheShareOfTheMainCountry(): void
    {
        $this->statsRepository->method('countOrders')->willReturn(3);
        $this->statsRepository->expects($this->once())
            ->method('getMainCountry')
            ->with($this->anything(), $this->anything(), self::LANGUAGE_ID, self::SHOP_IDS)
            ->willReturn(['id_country' => 8, 'name' => 'France', 'orders' => 2]);

        $provider = new MainCountryKpiRefreshProvider(
            $this->statsRepository,
            $this->shopContext,
            $this->kpiConfiguration,
            $this->createTranslator(),
            $this->createLanguageContext()
        );

        // '%d%%' is replaced as a whole, as in legacy
        $this->assertSame('66.7 France', $provider->getValue()->getValue());
        $this->assertCachedValuePerLanguage('MAIN_COUNTRY', '66.7 France', '+1 day');
    }
}
