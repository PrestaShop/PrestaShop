<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Tests\Unit\Adapter\Kpi\Refresh;

use PrestaShop\PrestaShop\Adapter\Kpi\Refresh\ProductsPerCategoryKpiRefreshProvider;

class ProductsPerCategoryKpiRefreshProviderTest extends AbstractKpiRefreshProviderTestCase
{
    /**
     * @dataProvider provideCounters
     */
    public function testGetValueDividesProductsByCategories(int $products, int $categories, string $expectedValue): void
    {
        $this->statsRepository->method('getProductActivationCounters')->willReturn(['disabled' => 0, 'total' => $products]);
        $this->statsRepository->method('countTotalCategories')->willReturn($categories);

        $provider = new ProductsPerCategoryKpiRefreshProvider($this->statsRepository, $this->shopContext, $this->kpiConfiguration);

        $this->assertSame($expectedValue, $provider->getValue()->getValue());
        $this->assertCachedValue('PRODUCTS_PER_CATEGORY', $expectedValue, '+1 hour');
    }

    public static function provideCounters(): iterable
    {
        yield 'rounded half up' => [10, 4, '3'];
        yield 'rounded down' => [10, 3, '3'];
        // Legacy divided by zero here
        yield 'no category' => [10, 0, '0'];
    }
}
