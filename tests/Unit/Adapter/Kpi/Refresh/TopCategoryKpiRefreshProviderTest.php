<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Tests\Unit\Adapter\Kpi\Refresh;

use PrestaShop\PrestaShop\Adapter\Category\Repository\CategoryRepository;
use PrestaShop\PrestaShop\Adapter\Kpi\Refresh\TopCategoryKpiRefreshProvider;
use PrestaShop\PrestaShop\Core\Domain\Category\ValueObject\CategoryId;

class TopCategoryKpiRefreshProviderTest extends AbstractKpiRefreshProviderTestCase
{
    public function testGetValueReturnsTheCategoryNameAndCachesItPerLanguage(): void
    {
        $this->statsRepository->method('getBestSellingCategory')->willReturn(['id_category' => 3]);
        $categoryRepository = $this->createMock(CategoryRepository::class);
        $categoryRepository->method('getLocalizedNames')
            ->with([new CategoryId(3)])
            ->willReturn([3 => [1 => 'Clothes', self::LANGUAGE_ID => 'Vêtements']]);

        $value = $this->createProvider($categoryRepository)->getValue();

        $this->assertSame('Vêtements', $value->getValue());
        // TopCategoryKpi reads the entry of the employee language
        $this->assertCachedValuePerLanguage('TOP_CATEGORY', 'Vêtements', '+1 day');
    }

    public function testGetValueWithoutSalesReturnsNoCategory(): void
    {
        $this->statsRepository->method('getBestSellingCategory')->willReturn(null);
        $categoryRepository = $this->createMock(CategoryRepository::class);
        $categoryRepository->expects($this->never())->method('getLocalizedNames');

        $this->assertSame('No category', $this->createProvider($categoryRepository)->getValue()->getValue());
        $this->assertCachedValuePerLanguage('TOP_CATEGORY', 'No category', '+1 day');
    }

    private function createProvider(CategoryRepository $categoryRepository): TopCategoryKpiRefreshProvider
    {
        return new TopCategoryKpiRefreshProvider(
            $this->statsRepository,
            $this->shopContext,
            $this->kpiConfiguration,
            $this->createTranslator(),
            $this->createLanguageContext(),
            $categoryRepository
        );
    }
}
