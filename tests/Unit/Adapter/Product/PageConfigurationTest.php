<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Tests\Unit\Adapter\Product;

use PrestaShop\PrestaShop\Adapter\Product\PageConfiguration;
use Tests\TestCase\AbstractConfigurationTestCase;

class PageConfigurationTest extends AbstractConfigurationTestCase
{
    /**
     * When stock management is disabled, the "Display available quantities" switch is forced off and
     * disabled in the UI, so the browser submits no value for it. The saved configuration must treat
     * the missing value as its forced state (false) rather than failing with a 500.
     *
     * @see https://github.com/PrestaShop/PrestaShop/issues/43312
     */
    public function testUpdateConfigurationAcceptsDisabledDisplayQuantities(): void
    {
        $pageConfiguration = new PageConfiguration($this->mockConfiguration, $this->mockShopConfiguration, $this->mockMultistoreFeature);

        $stored = [];
        $this->mockConfiguration
            ->method('set')
            ->willReturnCallback(static function ($key, $value) use (&$stored): void {
                $stored[$key] = $value;
            });

        $errors = $pageConfiguration->updateConfiguration([
            'display_quantities' => null,
            'allow_add_variant_to_cart_from_listing' => false,
            'use_combination_image_in_listing' => false,
            'attribute_anchor_separator' => '-',
            'display_discount_price' => false,
            'display_amount_in_cart' => false,
            'feature_values_order' => 'name',
        ]);

        $this->assertSame([], $errors);
        $this->assertArrayHasKey('PS_DISPLAY_QTIES', $stored);
        $this->assertFalse($stored['PS_DISPLAY_QTIES']);
    }
}
