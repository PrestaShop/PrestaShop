<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Tests\Unit\Adapter\Kpi\Refresh;

use PrestaShop\PrestaShop\Adapter\Kpi\Refresh\TopCategoryKpiRefreshProvider;
use PrestaShop\PrestaShop\Core\Context\ShopContext;

class TopCategoryKpiRefreshProviderTest extends AbstractKpiRefreshProviderTestCase
{
    public function testGetValueReturnsTheCategoryNameAndCachesItPerLanguage(): void
    {
        // The name is read for the employee language and the current shop
        $this->statsRepository->expects($this->once())
            ->method('getBestSellingCategory')
            ->with($this->anything(), $this->anything(), self::LANGUAGE_ID, 3, self::SHOP_IDS)
            ->willReturn(['id_category' => 3, 'name' => 'Vêtements', 'sales' => 120.0]);

        $value = $this->createProvider()->getValue();

        $this->assertSame('Vêtements', $value->getValue());
        // TopCategoryKpi reads the entry of the employee language
        $this->assertCachedValuePerLanguage('TOP_CATEGORY', 'Vêtements', '+1 day');
    }

    public function testGetValueWithoutSalesReturnsNoCategory(): void
    {
        $this->statsRepository->method('getBestSellingCategory')->willReturn(null);

        $this->assertSame('No category', $this->createProvider()->getValue()->getValue());
        $this->assertCachedValuePerLanguage('TOP_CATEGORY', 'No category', '+1 day');
    }

    private function createProvider(): TopCategoryKpiRefreshProvider
    {
        $currentShop = $this->createMock(ShopContext::class);
        $currentShop->method('getId')->willReturn(3);

        return new TopCategoryKpiRefreshProvider(
            $this->statsRepository,
            $this->shopContext,
            $this->kpiConfiguration,
            $this->createTranslator(),
            $this->createLanguageContext(),
            $currentShop
        );
    }
}
