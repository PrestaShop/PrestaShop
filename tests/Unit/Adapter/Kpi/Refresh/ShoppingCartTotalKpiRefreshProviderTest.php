<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Tests\Unit\Adapter\Kpi\Refresh;

use PHPUnit\Framework\TestCase;
use PrestaShop\PrestaShop\Adapter\Kpi\Refresh\ShoppingCartTotalKpiRefreshProvider;
use PrestaShop\PrestaShop\Core\Kpi\Refresh\Exception\KpiValueUnavailableException;
use PrestaShop\PrestaShop\Core\Localization\LocaleInterface;

class ShoppingCartTotalKpiRefreshProviderTest extends TestCase
{
    /**
     * @dataProvider provideInvalidParameters
     */
    public function testGetValueWithoutCartIsUnavailable(array $requestParameters): void
    {
        $provider = new ShoppingCartTotalKpiRefreshProvider($this->createMock(LocaleInterface::class));

        // The endpoint then answers has_errors, so the KPI box keeps its value
        $this->expectException(KpiValueUnavailableException::class);

        $provider->getValue($requestParameters);
    }

    public static function provideInvalidParameters(): iterable
    {
        yield 'missing' => [[]];
        yield 'zero' => [['cartId' => '0']];
        yield 'not numeric' => [['cartId' => 'abc']];
    }
}
