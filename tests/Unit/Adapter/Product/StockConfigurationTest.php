<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Tests\Unit\Adapter\Product;

use PrestaShop\PrestaShop\Adapter\Product\StockConfiguration;
use Tests\TestCase\AbstractConfigurationTestCase;

class StockConfigurationTest extends AbstractConfigurationTestCase
{
    /**
     * When stock management is disabled, the "Allow ordering of out-of-stock products" switch is
     * forced on and disabled in the UI, so the browser submits no value for it. The saved
     * configuration must treat the missing value as its forced state (true) rather than failing
     * with a 500.
     *
     * @see https://github.com/PrestaShop/PrestaShop/issues/43312
     */
    public function testUpdateConfigurationAcceptsDisabledAllowOrderingOos(): void
    {
        $stockConfiguration = new StockConfiguration($this->mockConfiguration, $this->mockShopConfiguration, $this->mockMultistoreFeature);

        $stored = [];
        $this->mockConfiguration
            ->method('set')
            ->willReturnCallback(static function ($key, $value) use (&$stored): void {
                $stored[$key] = $value;
            });

        $errors = $stockConfiguration->updateConfiguration([
            'allow_ordering_oos' => null,
            'stock_management' => false,
            'in_stock_label' => ['1' => 'In stock'],
            'delivery_time' => ['1' => 'Delivered within 3-4 days'],
            'oos_allowed_backorders' => ['1' => 'Available on backorder'],
            'oos_delivery_time' => ['1' => 'Delivered within 5-6 days'],
            'oos_denied_backorders' => ['1' => 'Out of stock'],
            'pack_stock_management' => 0,
            'oos_show_label_listing_pages' => false,
            'display_last_quantities' => 3,
            'display_unavailable_attributes' => false,
        ]);

        $this->assertSame([], $errors);
        $this->assertArrayHasKey('PS_ORDER_OUT_OF_STOCK', $stored);
        $this->assertTrue($stored['PS_ORDER_OUT_OF_STOCK']);
    }
}
